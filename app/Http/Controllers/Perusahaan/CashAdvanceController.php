<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Rekening;
use App\Models\TransaksiRekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = CashAdvance::with([
            'project:id,nama', 
            'karyawan:id,nama_lengkap', 
            'rekening:id,nama_rekening,nomor_rekening,saldo_saat_ini',
            'transactions' => function($q) {
                $q->where('tipe', 'pengeluaran');
            }
        ])->orderBy('created_at', 'desc');

        // Filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $cashAdvances = $query->paginate(20);

        // Data untuk filter
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();

        return view('perusahaan.cash-advance.index', compact(
            'cashAdvances',
            'projects'
        ));
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $karyawans = Karyawan::select('id', 'nama_lengkap', 'project_id')
            ->with('project:id,nama')
            ->orderBy('nama_lengkap')
            ->get();
        $rekenings = Rekening::select('id', 'nama_rekening', 'nomor_rekening', 'saldo_saat_ini')
            ->where('is_active', true)
            ->orderBy('nama_rekening')
            ->get();

        return view('perusahaan.cash-advance.create', compact('projects', 'karyawans', 'rekenings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'rekening_id' => 'required|exists:rekenings,id',
            'jumlah_pengajuan' => 'required|numeric|min:1|max:999999999',
            'keperluan' => 'required|string|max:1000',
            'tanggal_pengajuan' => 'required|date',
            'batas_pertanggungjawaban' => 'required|date|after_or_equal:tanggal_pengajuan',
        ]);

        // Cek apakah karyawan sudah punya Cash Advance aktif
        $existingCA = CashAdvance::where('karyawan_id', $validated['karyawan_id'])
            ->whereIn('status', ['pending', 'approved', 'active', 'need_report'])
            ->first();

        if ($existingCA) {
            return back()->withErrors([
                'karyawan_id' => 'Karyawan ini masih memiliki Cash Advance yang belum selesai.'
            ])->withInput();
        }

        // Cek saldo rekening
        $rekening = Rekening::find($validated['rekening_id']);
        if ($rekening->saldo_saat_ini < $validated['jumlah_pengajuan']) {
            return back()->withErrors([
                'jumlah_pengajuan' => 'Saldo rekening tidak mencukupi. Saldo tersedia: Rp ' . number_format($rekening->saldo_saat_ini, 0, ',', '.')
            ])->withInput();
        }

        try {
            DB::transaction(function () use ($validated) {
                $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
                $validated['status'] = 'pending';

                CashAdvance::create($validated);
            });

            return redirect()->route('perusahaan.keuangan.cash-advance.index')
                ->with('success', 'Pengajuan Cash Advance berhasil dibuat dan menunggu approval.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat pengajuan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(CashAdvance $cashAdvance)
    {
        $cashAdvance->load([
            'project:id,nama',
            'karyawan:id,nama_lengkap,nik_karyawan',
            'approvedBy:id,name',
            'rekening:id,nama_rekening,nomor_rekening',
            'transactions' => function ($query) {
                $query->with('createdBy:id,name')->orderBy('created_at', 'asc');
            },
            'reports' => function ($query) {
                $query->with('approvedBy:id,name')->orderBy('created_at', 'desc');
            }
        ]);

        return view('perusahaan.cash-advance.show', compact('cashAdvance'));
    }

    public function approve(Request $request, CashAdvance $cashAdvance)
    {
        if ($cashAdvance->status !== 'pending') {
            return back()->withErrors(['error' => 'Cash Advance ini tidak dapat diapprove.']);
        }

        $validated = $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
        ]);

        // Cek saldo rekening
        $rekening = $cashAdvance->rekening;
        if (!$rekening) {
            return back()->withErrors(['error' => 'Rekening tidak ditemukan.']);
        }

        if ($rekening->saldo_saat_ini < $cashAdvance->jumlah_pengajuan) {
            return back()->withErrors(['error' => 'Saldo rekening tidak mencukupi. Saldo tersedia: Rp ' . number_format($rekening->saldo_saat_ini, 0, ',', '.')]);
        }

        try {
            DB::transaction(function () use ($cashAdvance, $validated, $rekening) {
                // Update Cash Advance status dan langsung aktifkan
                $cashAdvance->update([
                    'status' => 'active',
                    'approved_by' => auth()->id(),
                    'tanggal_approved' => now(),
                    'catatan_approval' => $validated['catatan_approval'],
                    'saldo_tersedia' => $cashAdvance->jumlah_pengajuan,
                    'sisa_saldo' => $cashAdvance->jumlah_pengajuan,
                ]);

                // Kurangi saldo rekening
                $rekening->decrement('saldo_saat_ini', $cashAdvance->jumlah_pengajuan);

                // Buat transaksi rekening (DEBIT - Keluar)
                \App\Models\TransaksiRekening::create([
                    'perusahaan_id' => auth()->user()->perusahaan_id,
                    'rekening_id' => $rekening->id,
                    'nomor_transaksi' => \App\Models\TransaksiRekening::generateNomorTransaksi(),
                    'tanggal_transaksi' => now(),
                    'jenis_transaksi' => 'debit',
                    'kategori_transaksi' => 'cash_advance',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'keterangan' => "Cash Advance untuk {$cashAdvance->karyawan->nama_lengkap} - {$cashAdvance->nomor_ca}",
                    'referensi' => $cashAdvance->nomor_ca,
                    'saldo_sebelum' => $rekening->saldo_saat_ini + $cashAdvance->jumlah_pengajuan,
                    'saldo_sesudah' => $rekening->saldo_saat_ini,
                    'user_id' => auth()->id(),
                    'is_verified' => true,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Buat transaksi Cash Advance (pencairan)
                $pencairanTime = now()->subSeconds(1); // Pastikan pencairan dibuat lebih awal
                $cashAdvance->transactions()->create([
                    'tipe' => 'pencairan',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'tanggal_transaksi' => $pencairanTime,
                    'keterangan' => 'Pencairan Cash Advance dari rekening ' . $rekening->nama_rekening . ' - ' . $cashAdvance->keperluan,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => $cashAdvance->jumlah_pengajuan,
                    'created_by' => auth()->id(),
                    'created_at' => $pencairanTime,
                    'updated_at' => $pencairanTime,
                ]);
            });

            return back()->with('success', 'Cash Advance berhasil diapprove dan saldo telah dipindahkan dari rekening.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal approve: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, CashAdvance $cashAdvance)
    {
        if ($cashAdvance->status !== 'pending') {
            return back()->withErrors(['error' => 'Cash Advance ini tidak dapat ditolak.']);
        }

        $validated = $request->validate([
            'catatan_reject' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($cashAdvance, $validated) {
                $cashAdvance->update([
                    'status' => 'rejected',
                    'catatan_reject' => $validated['catatan_reject'],
                ]);
            });

            return back()->with('success', 'Cash Advance berhasil ditolak.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menolak: ' . $e->getMessage()]);
        }
    }

    public function activate(CashAdvance $cashAdvance)
    {
        if ($cashAdvance->status !== 'approved') {
            return back()->withErrors(['error' => 'Cash Advance harus diapprove terlebih dahulu.']);
        }

        // Cek saldo rekening
        $rekening = $cashAdvance->rekening;
        if (!$rekening) {
            return back()->withErrors(['error' => 'Rekening tidak ditemukan.']);
        }

        if ($rekening->saldo_saat_ini < $cashAdvance->jumlah_pengajuan) {
            return back()->withErrors(['error' => 'Saldo rekening tidak mencukupi. Saldo tersedia: Rp ' . number_format($rekening->saldo_saat_ini, 0, ',', '.')]);
        }

        try {
            DB::transaction(function () use ($cashAdvance, $rekening) {
                // Update Cash Advance status
                $cashAdvance->update([
                    'status' => 'active',
                    'saldo_tersedia' => $cashAdvance->jumlah_pengajuan,
                    'sisa_saldo' => $cashAdvance->jumlah_pengajuan,
                ]);

                // Kurangi saldo rekening
                $rekening->decrement('saldo_saat_ini', $cashAdvance->jumlah_pengajuan);

                // Buat transaksi rekening (DEBIT - Keluar)
                \App\Models\TransaksiRekening::create([
                    'perusahaan_id' => auth()->user()->perusahaan_id,
                    'rekening_id' => $rekening->id,
                    'nomor_transaksi' => \App\Models\TransaksiRekening::generateNomorTransaksi(),
                    'tanggal_transaksi' => now(),
                    'jenis_transaksi' => 'debit',
                    'kategori_transaksi' => 'cash_advance',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'keterangan' => "Cash Advance untuk {$cashAdvance->karyawan->nama_lengkap} - {$cashAdvance->nomor_ca}",
                    'referensi' => $cashAdvance->nomor_ca,
                    'saldo_sebelum' => $rekening->saldo_saat_ini + $cashAdvance->jumlah_pengajuan,
                    'saldo_sesudah' => $rekening->saldo_saat_ini,
                    'user_id' => auth()->id(),
                    'is_verified' => true,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Buat transaksi Cash Advance (pencairan)
                $pencairanTime = now()->subSeconds(1); // Pastikan pencairan dibuat lebih awal
                $cashAdvance->transactions()->create([
                    'tipe' => 'pencairan',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'tanggal_transaksi' => $pencairanTime,
                    'keterangan' => 'Pencairan Cash Advance dari rekening ' . $rekening->nama_rekening . ' - ' . $cashAdvance->keperluan,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => $cashAdvance->jumlah_pengajuan,
                    'created_by' => auth()->id(),
                    'created_at' => $pencairanTime,
                    'updated_at' => $pencairanTime,
                ]);
            });

            return back()->with('success', 'Cash Advance berhasil diaktifkan dan saldo telah dipindahkan dari rekening.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengaktifkan: ' . $e->getMessage()]);
        }
    }

    public function returnBalance(CashAdvance $cashAdvance)
    {
        if (!in_array($cashAdvance->status, ['active', 'need_report'])) {
            return back()->withErrors(['error' => 'Cash Advance tidak dalam status yang dapat dikembalikan.']);
        }

        if ($cashAdvance->sisa_saldo <= 0) {
            return back()->withErrors(['error' => 'Tidak ada sisa saldo yang dapat dikembalikan.']);
        }

        try {
            DB::transaction(function () use ($cashAdvance) {
                $sisaSaldo = $cashAdvance->sisa_saldo;
                $rekening = $cashAdvance->rekening;

                // Kembalikan saldo ke rekening
                $rekening->increment('saldo_saat_ini', $sisaSaldo);

                // Update Cash Advance
                $cashAdvance->update([
                    'sisa_saldo' => 0,
                    'status' => 'completed',
                ]);

                // Buat transaksi rekening (KREDIT - Masuk)
                \App\Models\TransaksiRekening::create([
                    'perusahaan_id' => auth()->user()->perusahaan_id,
                    'rekening_id' => $rekening->id,
                    'nomor_transaksi' => \App\Models\TransaksiRekening::generateNomorTransaksi(),
                    'tanggal_transaksi' => now(),
                    'jenis_transaksi' => 'kredit',
                    'kategori_transaksi' => 'cash_advance_return',
                    'jumlah' => $sisaSaldo,
                    'keterangan' => "Pengembalian sisa Cash Advance dari {$cashAdvance->karyawan->nama_lengkap} - {$cashAdvance->nomor_ca}",
                    'referensi' => $cashAdvance->nomor_ca,
                    'saldo_sebelum' => $rekening->saldo_saat_ini - $sisaSaldo,
                    'saldo_sesudah' => $rekening->saldo_saat_ini,
                    'user_id' => auth()->id(),
                    'is_verified' => true,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Buat transaksi Cash Advance (pengembalian)
                $cashAdvance->transactions()->create([
                    'tipe' => 'pengembalian',
                    'jumlah' => $sisaSaldo,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Pengembalian sisa saldo ke rekening ' . $rekening->nama_rekening,
                    'saldo_sebelum' => $sisaSaldo,
                    'saldo_sesudah' => 0,
                    'created_by' => auth()->id(),
                ]);
            });

            return back()->with('success', 'Sisa saldo berhasil dikembalikan ke rekening.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengembalikan saldo: ' . $e->getMessage()]);
        }
    }

    public function getKaryawanByProject(Request $request)
    {
        $projectId = $request->get('project_id');
        
        $karyawans = Karyawan::select('id', 'nama_lengkap')
            ->where('project_id', $projectId)
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json($karyawans);
    }

    public function searchKaryawan(Request $request)
    {
        $projectId = $request->get('project_id');
        $search = $request->get('search', '');
        
        $query = Karyawan::select('id', 'nama_lengkap', 'nik_karyawan')
            ->where('is_active', true);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nik_karyawan', 'LIKE', "%{$search}%");
            });
        }
        
        // Exclude karyawan yang sudah punya Cash Advance aktif
        $query->whereDoesntHave('cashAdvances', function($q) {
            $q->whereIn('status', ['pending', 'approved', 'active', 'need_report']);
        });
        
        $karyawans = $query->orderBy('nama_lengkap')
            ->limit(20)
            ->get()
            ->map(function($karyawan) {
                return [
                    'id' => $karyawan->id,
                    'text' => $karyawan->nama_lengkap . ' (' . $karyawan->nik_karyawan . ')',
                    'nama_lengkap' => $karyawan->nama_lengkap,
                    'nik_karyawan' => $karyawan->nik_karyawan
                ];
            });

        return response()->json($karyawans);
    }

    public function addExpense(Request $request, CashAdvance $cashAdvance)
    {
        if (!in_array($cashAdvance->status, ['approved', 'active', 'need_report'])) {
            return back()->withErrors(['error' => 'Cash Advance tidak dalam status yang dapat digunakan.']);
        }

        // Jika status masih approved, aktifkan dulu
        if ($cashAdvance->status === 'approved') {
            $rekening = $cashAdvance->rekening;
            if (!$rekening || $rekening->saldo_saat_ini < $cashAdvance->jumlah_pengajuan) {
                return back()->withErrors(['error' => 'Saldo rekening tidak mencukupi untuk mengaktifkan Cash Advance.']);
            }

            // Aktifkan Cash Advance
            DB::transaction(function () use ($cashAdvance, $rekening) {
                $cashAdvance->update([
                    'status' => 'active',
                    'saldo_tersedia' => $cashAdvance->jumlah_pengajuan,
                    'sisa_saldo' => $cashAdvance->jumlah_pengajuan,
                ]);

                // Kurangi saldo rekening
                $rekening->decrement('saldo_saat_ini', $cashAdvance->jumlah_pengajuan);

                // Buat transaksi rekening
                \App\Models\TransaksiRekening::create([
                    'perusahaan_id' => auth()->user()->perusahaan_id,
                    'rekening_id' => $rekening->id,
                    'nomor_transaksi' => \App\Models\TransaksiRekening::generateNomorTransaksi(),
                    'tanggal_transaksi' => now(),
                    'jenis_transaksi' => 'debit',
                    'kategori_transaksi' => 'cash_advance',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'keterangan' => "Cash Advance untuk {$cashAdvance->karyawan->nama_lengkap} - {$cashAdvance->nomor_ca}",
                    'referensi' => $cashAdvance->nomor_ca,
                    'saldo_sebelum' => $rekening->saldo_saat_ini + $cashAdvance->jumlah_pengajuan,
                    'saldo_sesudah' => $rekening->saldo_saat_ini,
                    'user_id' => auth()->id(),
                    'is_verified' => true,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Buat transaksi Cash Advance (pencairan)
                $cashAdvance->transactions()->create([
                    'tipe' => 'pencairan',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Pencairan Cash Advance dari rekening ' . $rekening->nama_rekening . ' - ' . $cashAdvance->keperluan,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => $cashAdvance->jumlah_pengajuan,
                    'created_by' => auth()->id(),
                ]);
            });

            // Refresh model
            $cashAdvance->refresh();
        }

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:500',
            'bukti_transaksi' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek apakah masih ada sisa saldo
        if ($cashAdvance->sisa_saldo <= 0) {
            return back()->withErrors(['error' => 'Saldo Cash Advance sudah habis. Tidak dapat menambah pengeluaran lagi.']);
        }

        // Cek apakah jumlah tidak melebihi sisa saldo
        if ($validated['jumlah'] > $cashAdvance->sisa_saldo) {
            return back()->withErrors(['jumlah' => 'Jumlah pengeluaran melebihi sisa saldo yang tersedia.']);
        }

        try {
            DB::transaction(function () use ($cashAdvance, $validated, $request) {
                // Upload bukti transaksi
                $buktiPath = null;
                if ($request->hasFile('bukti_transaksi')) {
                    $buktiPath = $request->file('bukti_transaksi')->store('cash-advance/bukti', 'public');
                }

                // Buat transaksi pengeluaran
                $cashAdvance->transactions()->create([
                    'tipe' => 'pengeluaran',
                    'jumlah' => $validated['jumlah'],
                    'tanggal_transaksi' => $validated['tanggal_transaksi'],
                    'keterangan' => $validated['keterangan'],
                    'bukti_transaksi' => $buktiPath,
                    'saldo_sebelum' => $cashAdvance->sisa_saldo,
                    'saldo_sesudah' => $cashAdvance->sisa_saldo - $validated['jumlah'],
                    'created_by' => auth()->id(),
                ]);

                // Update sisa saldo dan total terpakai
                $cashAdvance->decrement('sisa_saldo', $validated['jumlah']);
                $cashAdvance->increment('total_terpakai', $validated['jumlah']);
            });

            return back()->with('success', 'Bukti pengeluaran berhasil ditambahkan.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan pengeluaran: ' . $e->getMessage()]);
        }
    }

    public function createReport(Request $request, CashAdvance $cashAdvance)
    {
        if (!in_array($cashAdvance->status, ['approved', 'active', 'need_report'])) {
            return back()->withErrors(['error' => 'Cash Advance tidak dalam status yang dapat dilaporkan.']);
        }

        // Jika status masih approved, aktifkan dulu
        if ($cashAdvance->status === 'approved') {
            $rekening = $cashAdvance->rekening;
            if (!$rekening || $rekening->saldo_saat_ini < $cashAdvance->jumlah_pengajuan) {
                return back()->withErrors(['error' => 'Saldo rekening tidak mencukupi untuk mengaktifkan Cash Advance.']);
            }

            // Aktifkan Cash Advance
            DB::transaction(function () use ($cashAdvance, $rekening) {
                $cashAdvance->update([
                    'status' => 'active',
                    'saldo_tersedia' => $cashAdvance->jumlah_pengajuan,
                    'sisa_saldo' => $cashAdvance->jumlah_pengajuan,
                ]);

                // Kurangi saldo rekening
                $rekening->decrement('saldo_saat_ini', $cashAdvance->jumlah_pengajuan);

                // Buat transaksi rekening
                \App\Models\TransaksiRekening::create([
                    'perusahaan_id' => auth()->user()->perusahaan_id,
                    'rekening_id' => $rekening->id,
                    'nomor_transaksi' => \App\Models\TransaksiRekening::generateNomorTransaksi(),
                    'tanggal_transaksi' => now(),
                    'jenis_transaksi' => 'debit',
                    'kategori_transaksi' => 'cash_advance',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'keterangan' => "Cash Advance untuk {$cashAdvance->karyawan->nama_lengkap} - {$cashAdvance->nomor_ca}",
                    'referensi' => $cashAdvance->nomor_ca,
                    'saldo_sebelum' => $rekening->saldo_saat_ini + $cashAdvance->jumlah_pengajuan,
                    'saldo_sesudah' => $rekening->saldo_saat_ini,
                    'user_id' => auth()->id(),
                    'is_verified' => true,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                // Buat transaksi Cash Advance (pencairan)
                $cashAdvance->transactions()->create([
                    'tipe' => 'pencairan',
                    'jumlah' => $cashAdvance->jumlah_pengajuan,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Pencairan Cash Advance dari rekening ' . $rekening->nama_rekening . ' - ' . $cashAdvance->keperluan,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => $cashAdvance->jumlah_pengajuan,
                    'created_by' => auth()->id(),
                ]);
            });

            // Refresh model
            $cashAdvance->refresh();
        }

        $validated = $request->validate([
            'tanggal_laporan' => 'required|date',
            'ringkasan_penggunaan' => 'required|string|max:1000',
            'file_laporan' => 'required|file|mimes:pdf|max:5120', // 5MB
            'tindakan_sisa_saldo' => 'nullable|in:kembalikan,lanjutkan',
        ]);

        try {
            DB::transaction(function () use ($cashAdvance, $validated, $request) {
                // Upload file laporan
                $filePath = null;
                if ($request->hasFile('file_laporan')) {
                    $filePath = $request->file('file_laporan')->store('cash-advance/laporan', 'public');
                }

                // Buat laporan
                $report = $cashAdvance->reports()->create([
                    'tanggal_laporan' => $validated['tanggal_laporan'],
                    'total_pengeluaran' => $cashAdvance->total_terpakai,
                    'sisa_saldo' => $cashAdvance->sisa_saldo,
                    'ringkasan_penggunaan' => $validated['ringkasan_penggunaan'],
                    'file_laporan' => $filePath,
                    'status' => 'submitted',
                ]);

                // Jika pilih kembalikan sisa saldo
                if ($validated['tindakan_sisa_saldo'] === 'kembalikan' && $cashAdvance->sisa_saldo > 0) {
                    $rekening = $cashAdvance->rekening;
                    $sisaSaldo = $cashAdvance->sisa_saldo;

                    // Kembalikan saldo ke rekening
                    $rekening->increment('saldo_saat_ini', $sisaSaldo);

                    // Update Cash Advance
                    $cashAdvance->update([
                        'sisa_saldo' => 0,
                        'status' => 'completed',
                    ]);

                    // Update laporan
                    $report->update([
                        'jumlah_dikembalikan' => $sisaSaldo,
                        'sisa_saldo' => 0,
                    ]);

                    // Buat transaksi rekening (KREDIT - Masuk)
                    \App\Models\TransaksiRekening::create([
                        'perusahaan_id' => auth()->user()->perusahaan_id,
                        'rekening_id' => $rekening->id,
                        'nomor_transaksi' => \App\Models\TransaksiRekening::generateNomorTransaksi(),
                        'tanggal_transaksi' => now(),
                        'jenis_transaksi' => 'kredit',
                        'kategori_transaksi' => 'cash_advance_return',
                        'jumlah' => $sisaSaldo,
                        'keterangan' => "Pengembalian sisa Cash Advance dari {$cashAdvance->karyawan->nama_lengkap} - {$cashAdvance->nomor_ca}",
                        'referensi' => $cashAdvance->nomor_ca,
                        'saldo_sebelum' => $rekening->saldo_saat_ini - $sisaSaldo,
                        'saldo_sesudah' => $rekening->saldo_saat_ini,
                        'user_id' => auth()->id(),
                        'is_verified' => true,
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ]);

                    // Buat transaksi Cash Advance (pengembalian)
                    $cashAdvance->transactions()->create([
                        'tipe' => 'pengembalian',
                        'jumlah' => $sisaSaldo,
                        'tanggal_transaksi' => now(),
                        'keterangan' => 'Pengembalian sisa saldo ke rekening ' . $rekening->nama_rekening,
                        'saldo_sebelum' => $sisaSaldo,
                        'saldo_sesudah' => 0,
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    // Jika lanjutkan penggunaan, ubah status ke need_report
                    $cashAdvance->update(['status' => 'need_report']);
                }
            });

            $message = $validated['tindakan_sisa_saldo'] === 'kembalikan' 
                ? 'Laporan berhasil dibuat dan sisa saldo telah dikembalikan ke rekening.'
                : 'Laporan berhasil dibuat dan menunggu approval.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat laporan: ' . $e->getMessage()]);
        }
    }

    public function printReport(CashAdvance $cashAdvance)
    {
        $cashAdvance->load([
            'project:id,nama',
            'karyawan:id,nama_lengkap,nik_karyawan',
            'approvedBy:id,name',
            'rekening:id,nama_rekening,nomor_rekening',
            'perusahaan:id,nama', // Load perusahaan data
            'transactions' => function ($query) {
                $query->with('createdBy:id,name')->orderBy('created_at', 'asc');
            },
            'reports' => function ($query) {
                $query->with('approvedBy:id,name')->orderBy('created_at', 'desc');
            }
        ]);

        return view('perusahaan.cash-advance.print', compact('cashAdvance'));
    }
}