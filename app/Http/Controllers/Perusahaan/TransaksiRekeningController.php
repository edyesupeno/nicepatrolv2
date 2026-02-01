<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\TransaksiRekening;
use App\Models\Rekening;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransaksiRekeningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if projects exist
        $projectCount = Project::count();
        if ($projectCount === 0) {
            return redirect()->route('perusahaan.projects.create')
                ->with('info', 'Anda perlu membuat project terlebih dahulu sebelum dapat mengelola transaksi keuangan.');
        }

        $rekenings = Rekening::with('project:id,nama')
            ->select('id', 'project_id', 'nama_rekening', 'warna_card')
            ->active()
            ->orderBy('project_id')
            ->orderBy('nama_rekening')
            ->get();

        // Check if no rekening exists
        if ($rekenings->isEmpty()) {
            return view('perusahaan.transaksi-rekening.index', [
                'transaksis' => collect()->paginate(20),
                'rekenings' => collect()
            ]);
        }

        $query = TransaksiRekening::with(['rekening.project', 'user:id,name'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan rekening
        if ($request->filled('rekening_id')) {
            $query->where('rekening_id', $request->rekening_id);
        }

        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori_transaksi')) {
            $query->where('kategori_transaksi', $request->kategori_transaksi);
        }

        // Filter berdasarkan status verifikasi
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified === '1');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_transaksi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('referensi', 'like', "%{$search}%");
            });
        }

        $transaksis = $query->paginate(20);

        return view('perusahaan.transaksi-rekening.index', compact('transaksis', 'rekenings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if projects exist
        $projectCount = Project::count();
        if ($projectCount === 0) {
            return redirect()->route('perusahaan.projects.create')
                ->with('info', 'Anda perlu membuat project terlebih dahulu sebelum dapat menambah transaksi.');
        }

        $rekenings = Rekening::with('project:id,nama')
            ->select('id', 'project_id', 'nama_rekening', 'saldo_saat_ini', 'mata_uang', 'warna_card')
            ->active()
            ->orderBy('project_id')
            ->orderBy('nama_rekening')
            ->get();

        // Check if no rekening exists
        if ($rekenings->isEmpty()) {
            return redirect()->route('perusahaan.keuangan.rekening.create')
                ->with('info', 'Anda perlu membuat rekening terlebih dahulu sebelum dapat menambah transaksi.');
        }

        $kategoriTransaksi = TransaksiRekening::getAvailableKategori();

        return view('perusahaan.transaksi-rekening.create', compact('rekenings', 'kategoriTransaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rekening_id' => 'required|exists:rekenings,id',
            'tanggal_transaksi' => 'required|date',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'jumlah' => 'required|numeric|min:0.01',
            'kategori_transaksi' => 'required|string',
            'keterangan' => 'required|string|max:1000',
            'referensi' => 'nullable|string|max:255',
            'is_verified' => 'boolean'
        ], [
            'rekening_id.required' => 'Rekening harus dipilih',
            'rekening_id.exists' => 'Rekening tidak valid',
            'tanggal_transaksi.required' => 'Tanggal transaksi harus diisi',
            'tanggal_transaksi.date' => 'Format tanggal tidak valid',
            'jenis_transaksi.required' => 'Jenis transaksi harus dipilih',
            'jenis_transaksi.in' => 'Jenis transaksi tidak valid',
            'jumlah.required' => 'Jumlah transaksi harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal Rp 0,01',
            'kategori_transaksi.required' => 'Kategori transaksi harus dipilih',
            'keterangan.required' => 'Keterangan harus diisi',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter'
        ]);

        // Validasi saldo untuk transaksi kredit
        if ($validated['jenis_transaksi'] === 'kredit') {
            $rekening = Rekening::find($validated['rekening_id']);
            if ($rekening->saldo_saat_ini < $validated['jumlah']) {
                return back()->withErrors([
                    'jumlah' => 'Saldo tidak mencukupi. Saldo saat ini: ' . $rekening->formatted_saldo_saat_ini
                ])->withInput();
            }
        }

        try {
            $transaksi = TransaksiRekening::createTransaksi($validated);

            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('success', 'Transaksi berhasil dicatat dengan nomor: ' . $transaksi->nomor_transaksi);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransaksiRekening $transaksiRekening)
    {
        $transaksiRekening->load(['rekening.project', 'user', 'verifiedBy']);
        
        return view('perusahaan.transaksi-rekening.show', compact('transaksiRekening'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransaksiRekening $transaksiRekening)
    {
        // Hanya transaksi yang belum diverifikasi yang bisa diedit
        if ($transaksiRekening->is_verified) {
            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('error', 'Transaksi yang sudah diverifikasi tidak dapat diedit');
        }

        $rekenings = Rekening::with('project:id,nama')
            ->select('id', 'project_id', 'nama_rekening', 'saldo_saat_ini', 'mata_uang', 'warna_card')
            ->active()
            ->orderBy('project_id')
            ->orderBy('nama_rekening')
            ->get();

        $kategoriTransaksi = TransaksiRekening::getAvailableKategori();

        return view('perusahaan.transaksi-rekening.edit', compact('transaksiRekening', 'rekenings', 'kategoriTransaksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransaksiRekening $transaksiRekening)
    {
        // Hanya transaksi yang belum diverifikasi yang bisa diedit
        if ($transaksiRekening->is_verified) {
            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('error', 'Transaksi yang sudah diverifikasi tidak dapat diedit');
        }

        $validated = $request->validate([
            'rekening_id' => 'required|exists:rekenings,id',
            'tanggal_transaksi' => 'required|date',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'jumlah' => 'required|numeric|min:0.01',
            'kategori_transaksi' => 'required|string',
            'keterangan' => 'required|string|max:1000',
            'referensi' => 'nullable|string|max:255'
        ]);

        try {
            // Revert saldo rekening lama
            $rekeningLama = Rekening::find($transaksiRekening->rekening_id);
            $rekeningLama->update(['saldo_saat_ini' => $transaksiRekening->saldo_sebelum]);

            // Update transaksi dengan data baru
            $rekeningBaru = Rekening::find($validated['rekening_id']);
            $saldoSebelum = $rekeningBaru->saldo_saat_ini;
            
            if ($validated['jenis_transaksi'] === 'debit') {
                $saldoSesudah = $saldoSebelum + $validated['jumlah'];
            } else {
                // Validasi saldo untuk transaksi kredit
                if ($saldoSebelum < $validated['jumlah']) {
                    return back()->withErrors([
                        'jumlah' => 'Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($saldoSebelum, 0, ',', '.')
                    ])->withInput();
                }
                $saldoSesudah = $saldoSebelum - $validated['jumlah'];
            }

            $transaksiRekening->update([
                'rekening_id' => $validated['rekening_id'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'jumlah' => $validated['jumlah'],
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'kategori_transaksi' => $validated['kategori_transaksi'],
                'keterangan' => $validated['keterangan'],
                'referensi' => $validated['referensi']
            ]);

            // Update saldo rekening baru
            $rekeningBaru->update(['saldo_saat_ini' => $saldoSesudah]);

            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('success', 'Transaksi berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransaksiRekening $transaksiRekening)
    {
        // Hanya transaksi yang belum diverifikasi yang bisa dihapus
        if ($transaksiRekening->is_verified) {
            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('error', 'Transaksi yang sudah diverifikasi tidak dapat dihapus');
        }

        try {
            // Revert saldo rekening
            $rekening = Rekening::find($transaksiRekening->rekening_id);
            $rekening->update(['saldo_saat_ini' => $transaksiRekening->saldo_sebelum]);

            $transaksiRekening->delete();

            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('perusahaan.keuangan.transaksi-rekening.index')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Verify transaksi
     */
    public function verify(TransaksiRekening $transaksiRekening)
    {
        $transaksiRekening->verify();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diverifikasi'
        ]);
    }

    /**
     * Unverify transaksi
     */
    public function unverify(TransaksiRekening $transaksiRekening)
    {
        $transaksiRekening->unverify();

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi transaksi berhasil dibatalkan'
        ]);
    }
}