<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReimbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $karyawans = Karyawan::select('id', 'nama_lengkap')->orderBy('nama_lengkap')->get();

        // Check if no project exists
        if ($projects->isEmpty()) {
            return redirect()->route('perusahaan.projects.create')
                ->with('info', 'Anda perlu membuat project terlebih dahulu sebelum dapat mengelola reimbursement.');
        }

        // Check if no karyawan exists
        if ($karyawans->isEmpty()) {
            return redirect()->route('perusahaan.karyawans.create')
                ->with('info', 'Anda perlu membuat data karyawan terlebih dahulu sebelum dapat mengelola reimbursement.');
        }

        $query = Reimbursement::withoutGlobalScope('perusahaan')
            ->with(['project:id,nama', 'karyawan:id,nama_lengkap', 'user:id,name'])
            ->join('karyawans', 'reimbursements.karyawan_id', '=', 'karyawans.id')
            ->where('reimbursements.perusahaan_id', auth()->user()->perusahaan_id)
            ->orderBy('karyawans.nama_lengkap', 'asc')
            ->orderBy('reimbursements.created_at', 'desc')
            ->select('reimbursements.id', 'reimbursements.nomor_reimbursement', 'reimbursements.project_id', 'reimbursements.karyawan_id', 'reimbursements.user_id', 'reimbursements.judul_pengajuan', 'reimbursements.jumlah_pengajuan', 'reimbursements.jumlah_disetujui', 'reimbursements.kategori', 'reimbursements.tanggal_pengajuan', 'reimbursements.status', 'reimbursements.prioritas', 'reimbursements.is_urgent', 'reimbursements.created_at');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('reimbursements.status', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('reimbursements.kategori', $request->kategori);
        }

        // Filter berdasarkan project
        if ($request->filled('project_id')) {
            $query->where('reimbursements.project_id', $request->project_id);
        }

        // Filter berdasarkan karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('reimbursements.karyawan_id', $request->karyawan_id);
        }

        // Filter berdasarkan prioritas
        if ($request->filled('prioritas')) {
            $query->where('reimbursements.prioritas', $request->prioritas);
        }

        // Filter berdasarkan periode
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('reimbursements.tanggal_pengajuan', [$request->start_date, $request->end_date]);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reimbursements.nomor_reimbursement', 'like', "%{$search}%")
                  ->orWhere('reimbursements.judul_pengajuan', 'like', "%{$search}%")
                  ->orWhere('karyawans.nama_lengkap', 'like', "%{$search}%");
            });
        }

        $reimbursements = $query->paginate(20);

        // Statistics - use separate queries to avoid join conflicts
        $stats = [
            'total_pengajuan' => Reimbursement::count(),
            'pending' => Reimbursement::where('status', 'submitted')->count(),
            'approved' => Reimbursement::where('status', 'approved')->count(),
            'total_amount' => Reimbursement::where('status', 'approved')->sum('jumlah_disetujui'),
            'urgent' => Reimbursement::where('is_urgent', true)->count()
        ];

        return view('perusahaan.reimbursement.index', compact(
            'reimbursements',
            'projects',
            'karyawans',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $karyawans = Karyawan::select('id', 'nama_lengkap', 'project_id')->with('project:id,nama')->orderBy('nama_lengkap')->get();

        // Check if no project exists
        if ($projects->isEmpty()) {
            return redirect()->route('perusahaan.projects.create')
                ->with('info', 'Anda perlu membuat project terlebih dahulu sebelum dapat membuat reimbursement.');
        }

        // Check if no karyawan exists
        if ($karyawans->isEmpty()) {
            return redirect()->route('perusahaan.karyawans.create')
                ->with('info', 'Anda perlu membuat data karyawan terlebih dahulu sebelum dapat membuat reimbursement.');
        }

        return view('perusahaan.reimbursement.create', compact('projects', 'karyawans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'judul_pengajuan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah_pengajuan' => 'required|numeric|min:0',
            'kategori' => 'required|in:' . implode(',', array_keys(Reimbursement::getAvailableKategori())),
            'tanggal_pengajuan' => 'required|date',
            'tanggal_kejadian' => 'required|date|before_or_equal:today',
            'prioritas' => 'required|in:' . implode(',', array_keys(Reimbursement::getAvailablePrioritas())),
            'catatan_pengaju' => 'nullable|string',
            'bukti_dokumen.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
            'is_urgent' => 'boolean'
        ], [
            'project_id.required' => 'Project harus dipilih',
            'karyawan_id.required' => 'Karyawan harus dipilih',
            'judul_pengajuan.required' => 'Judul pengajuan harus diisi',
            'deskripsi.required' => 'Deskripsi harus diisi',
            'jumlah_pengajuan.required' => 'Jumlah pengajuan harus diisi',
            'jumlah_pengajuan.min' => 'Jumlah pengajuan tidak boleh negatif',
            'kategori.required' => 'Kategori harus dipilih',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan harus diisi',
            'tanggal_kejadian.required' => 'Tanggal kejadian harus diisi',
            'tanggal_kejadian.before_or_equal' => 'Tanggal kejadian tidak boleh lebih dari hari ini',
            'prioritas.required' => 'Prioritas harus dipilih',
            'bukti_dokumen.*.mimes' => 'File harus berformat: jpg, jpeg, png, pdf, doc, docx',
            'bukti_dokumen.*.max' => 'Ukuran file maksimal 5MB'
        ]);

        // Auto-assign perusahaan_id dan user_id
        if (!auth()->user()->isSuperAdmin()) {
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        }
        $validated['user_id'] = auth()->id();

        // Handle file uploads
        $buktiDokumen = [];
        if ($request->hasFile('bukti_dokumen')) {
            foreach ($request->file('bukti_dokumen') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('reimbursement/bukti', $filename, 'public');
                $buktiDokumen[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
        }
        $validated['bukti_dokumen'] = $buktiDokumen;

        // Set default status
        $validated['status'] = $request->has('submit') ? 'submitted' : 'draft';

        $reimbursement = Reimbursement::create($validated);

        $message = $validated['status'] === 'submitted' 
            ? 'Pengajuan reimbursement berhasil disubmit dan menunggu review'
            : 'Draft reimbursement berhasil disimpan';

        return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reimbursement $reimbursement)
    {
        $reimbursement->load([
            'project:id,nama',
            'karyawan:id,nama_lengkap,nik_karyawan',
            'user:id,name,email',
            'reviewedBy:id,name',
            'approvedBy:id,name',
            'paidBy:id,name',
            'rekening:id,nama_rekening,nomor_rekening'
        ]);

        return view('perusahaan.reimbursement.show', compact('reimbursement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reimbursement $reimbursement)
    {
        // Check if can be edited
        if (!$reimbursement->canBeEdited()) {
            return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
                ->with('error', 'Reimbursement dengan status ' . $reimbursement->status_label . ' tidak dapat diedit');
        }

        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $karyawans = Karyawan::select('id', 'nama_lengkap', 'project_id')->with('project:id,nama')->orderBy('nama_lengkap')->get();

        return view('perusahaan.reimbursement.edit', compact('reimbursement', 'projects', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reimbursement $reimbursement)
    {
        // Check if can be edited
        if (!$reimbursement->canBeEdited()) {
            return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
                ->with('error', 'Reimbursement dengan status ' . $reimbursement->status_label . ' tidak dapat diedit');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'judul_pengajuan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah_pengajuan' => 'required|numeric|min:0',
            'kategori' => 'required|in:' . implode(',', array_keys(Reimbursement::getAvailableKategori())),
            'tanggal_pengajuan' => 'required|date',
            'tanggal_kejadian' => 'required|date|before_or_equal:today',
            'prioritas' => 'required|in:' . implode(',', array_keys(Reimbursement::getAvailablePrioritas())),
            'catatan_pengaju' => 'nullable|string',
            'bukti_dokumen.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'is_urgent' => 'boolean',
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'integer'
        ]);

        // Handle file uploads
        $buktiDokumen = $reimbursement->bukti_dokumen ?? [];
        
        // Remove selected files
        if ($request->filled('remove_files')) {
            foreach ($request->remove_files as $index) {
                if (isset($buktiDokumen[$index])) {
                    // Delete file from storage
                    Storage::disk('public')->delete($buktiDokumen[$index]['path']);
                    unset($buktiDokumen[$index]);
                }
            }
            $buktiDokumen = array_values($buktiDokumen); // Re-index array
        }

        // Add new files
        if ($request->hasFile('bukti_dokumen')) {
            foreach ($request->file('bukti_dokumen') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('reimbursement/bukti', $filename, 'public');
                $buktiDokumen[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
        }
        $validated['bukti_dokumen'] = $buktiDokumen;

        // Update status if submitted
        if ($request->has('submit') && $reimbursement->status === 'draft') {
            $validated['status'] = 'submitted';
        }

        $reimbursement->update($validated);

        $message = isset($validated['status']) && $validated['status'] === 'submitted'
            ? 'Pengajuan reimbursement berhasil diupdate dan disubmit untuk review'
            : 'Reimbursement berhasil diupdate';

        return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reimbursement $reimbursement)
    {
        // Check if can be deleted
        if (!in_array($reimbursement->status, ['draft', 'cancelled'])) {
            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('error', 'Reimbursement dengan status ' . $reimbursement->status_label . ' tidak dapat dihapus');
        }

        // Delete associated files
        if ($reimbursement->bukti_dokumen) {
            foreach ($reimbursement->bukti_dokumen as $dokumen) {
                Storage::disk('public')->delete($dokumen['path']);
            }
        }

        $reimbursement->delete();

        return redirect()->route('perusahaan.keuangan.reimbursement.index')
            ->with('success', 'Reimbursement berhasil dihapus');
    }

    /**
     * Submit reimbursement for review
     */
    public function submit(Reimbursement $reimbursement)
    {
        if (!$reimbursement->canBeSubmitted()) {
            return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
                ->with('error', 'Reimbursement dengan status ' . $reimbursement->status_label . ' tidak dapat disubmit');
        }

        $reimbursement->update(['status' => 'submitted']);

        return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
            ->with('success', 'Reimbursement berhasil disubmit untuk review');
    }

    /**
     * Cancel reimbursement
     */
    public function cancel(Reimbursement $reimbursement)
    {
        if (!$reimbursement->canBeCancelled()) {
            return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
                ->with('error', 'Reimbursement dengan status ' . $reimbursement->status_label . ' tidak dapat dibatalkan');
        }

        $reimbursement->update(['status' => 'cancelled']);

        return redirect()->route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id)
            ->with('success', 'Reimbursement berhasil dibatalkan');
    }

    /**
     * Download file bukti
     */
    public function downloadFile(Reimbursement $reimbursement, $fileIndex)
    {
        if (!isset($reimbursement->bukti_dokumen[$fileIndex])) {
            abort(404, 'File tidak ditemukan');
        }

        $file = $reimbursement->bukti_dokumen[$fileIndex];
        $filePath = storage_path('app/public/' . $file['path']);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        return response()->download($filePath, $file['filename']);
    }

    /**
     * Get approval data (rekening and kategori)
     */
    public function getApprovalData()
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Get active rekening with explicit perusahaan_id filter
            $rekening = Rekening::where('is_active', true)
                ->where('perusahaan_id', auth()->user()->perusahaan_id)
                ->select('id', 'nama_rekening', 'nomor_rekening', 'saldo_saat_ini')
                ->orderBy('nama_rekening')
                ->get()
                ->map(function($rek) {
                    $rek->formatted_saldo = number_format($rek->saldo_saat_ini, 0, ',', '.');
                    return $rek;
                });

            // Get kategori transaksi
            $kategori = \App\Models\TransaksiRekening::getAvailableKategori();

            // Check if no rekening found
            if ($rekening->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada rekening aktif yang tersedia. Silakan tambahkan rekening terlebih dahulu.'
                ]);
            }

            return response()->json([
                'success' => true,
                'rekening' => $rekening,
                'kategori' => $kategori
            ]);
        } catch (\Exception $e) {
            \Log::error('getApprovalData error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve reimbursement
     */
    public function approve(Request $request, Reimbursement $reimbursement)
    {
        $validated = $request->validate([
            'rekening_id' => 'required|exists:rekenings,id',
            'kategori_transaksi' => 'required|in:' . implode(',', array_keys(\App\Models\TransaksiRekening::getAvailableKategori())),
            'catatan_approver' => 'nullable|string|max:1000',
            'jumlah_disetujui' => 'nullable|numeric|min:0|max:' . $reimbursement->jumlah_pengajuan
        ], [
            'rekening_id.required' => 'Rekening pembayaran harus dipilih',
            'rekening_id.exists' => 'Rekening yang dipilih tidak valid',
            'kategori_transaksi.required' => 'Kategori transaksi harus dipilih',
            'kategori_transaksi.in' => 'Kategori transaksi tidak valid'
        ]);

        try {
            \DB::transaction(function () use ($reimbursement, $validated) {
                $jumlahDisetujui = $validated['jumlah_disetujui'] ?? $reimbursement->jumlah_pengajuan;
                
                // Approve reimbursement using workflow method
                $reimbursement->approve(auth()->id(), $jumlahDisetujui, $validated['catatan_approver']);
                
                // Update rekening_id for reference
                $reimbursement->update(['rekening_id' => $validated['rekening_id']]);
                
                // Create transaksi rekening (pengeluaran)
                $keterangan = "Reimbursement {$reimbursement->nomor_reimbursement} - {$reimbursement->karyawan->nama_lengkap} - {$reimbursement->judul_pengajuan}";
                
                \App\Models\TransaksiRekening::createTransaksi([
                    'rekening_id' => $validated['rekening_id'],
                    'tanggal_transaksi' => now()->toDateString(),
                    'jenis_transaksi' => 'kredit', // Kredit = pengeluaran
                    'jumlah' => $jumlahDisetujui,
                    'kategori_transaksi' => $validated['kategori_transaksi'],
                    'keterangan' => $keterangan,
                    'referensi' => $reimbursement->nomor_reimbursement,
                    'metadata' => [
                        'reimbursement_id' => $reimbursement->id,
                        'karyawan_id' => $reimbursement->karyawan_id,
                        'project_id' => $reimbursement->project_id,
                        'kategori_reimbursement' => $reimbursement->kategori
                    ],
                    'is_verified' => true // Auto verify untuk reimbursement yang sudah di-approve
                ]);
            });

            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('success', 'Reimbursement ' . $reimbursement->nomor_reimbursement . ' berhasil disetujui dan transaksi telah dicatat');
        } catch (\Exception $e) {
            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('error', 'Gagal menyetujui reimbursement: ' . $e->getMessage());
        }
    }

    /**
     * Reject reimbursement
     */
    public function reject(Request $request, Reimbursement $reimbursement)
    {
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:1000'
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi'
        ]);

        try {
            $reimbursement->reject(auth()->id(), $validated['alasan_penolakan']);

            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('success', 'Reimbursement ' . $reimbursement->nomor_reimbursement . ' berhasil ditolak');
        } catch (\Exception $e) {
            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk approve reimbursements
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'reimbursement_ids' => 'required|array|min:1',
            'reimbursement_ids.*' => 'required|string',
            'rekening_id' => 'required|exists:rekenings,id',
            'kategori_transaksi' => 'required|in:' . implode(',', array_keys(\App\Models\TransaksiRekening::getAvailableKategori())),
            'catatan_approver' => 'nullable|string|max:1000'
        ], [
            'reimbursement_ids.required' => 'Pilih minimal satu reimbursement untuk disetujui',
            'reimbursement_ids.min' => 'Pilih minimal satu reimbursement untuk disetujui',
            'rekening_id.required' => 'Rekening pembayaran harus dipilih',
            'rekening_id.exists' => 'Rekening yang dipilih tidak valid',
            'kategori_transaksi.required' => 'Kategori transaksi harus dipilih',
            'kategori_transaksi.in' => 'Kategori transaksi tidak valid'
        ]);

        try {
            $approvedCount = 0;
            $errors = [];

            // Use database transaction for consistency
            \DB::transaction(function () use ($validated, &$approvedCount, &$errors) {
                foreach ($validated['reimbursement_ids'] as $hashId) {
                    // Decode hash ID to get actual ID
                    $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
                    if (!$id) {
                        $errors[] = "Invalid ID: {$hashId}";
                        continue;
                    }

                    $reimbursement = Reimbursement::find($id);
                    if (!$reimbursement) {
                        $errors[] = "Reimbursement tidak ditemukan: {$hashId}";
                        continue;
                    }

                    try {
                        // Use workflow method for approval
                        $reimbursement->approve(
                            auth()->id(), 
                            $reimbursement->jumlah_pengajuan, // Default to full amount
                            $validated['catatan_approver']
                        );
                        
                        // Update rekening_id for reference
                        $reimbursement->update(['rekening_id' => $validated['rekening_id']]);
                        
                        // Create transaksi rekening (pengeluaran)
                        $keterangan = "Reimbursement {$reimbursement->nomor_reimbursement} - {$reimbursement->karyawan->nama_lengkap} - {$reimbursement->judul_pengajuan}";
                        
                        \App\Models\TransaksiRekening::createTransaksi([
                            'rekening_id' => $validated['rekening_id'],
                            'tanggal_transaksi' => now()->toDateString(),
                            'jenis_transaksi' => 'kredit', // Kredit = pengeluaran
                            'jumlah' => $reimbursement->jumlah_disetujui,
                            'kategori_transaksi' => $validated['kategori_transaksi'],
                            'keterangan' => $keterangan,
                            'referensi' => $reimbursement->nomor_reimbursement,
                            'metadata' => [
                                'reimbursement_id' => $reimbursement->id,
                                'karyawan_id' => $reimbursement->karyawan_id,
                                'project_id' => $reimbursement->project_id,
                                'kategori_reimbursement' => $reimbursement->kategori
                            ],
                            'is_verified' => true // Auto verify untuk reimbursement yang sudah di-approve
                        ]);
                        
                        $approvedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Reimbursement {$reimbursement->nomor_reimbursement}: {$e->getMessage()}";
                    }
                }
            });

            $message = "Berhasil menyetujui {$approvedCount} reimbursement dan mencatat transaksi";
            if (!empty($errors)) {
                $message .= ". Beberapa item tidak dapat diproses: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " lainnya";
                }
            }

            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('error', 'Terjadi kesalahan saat memproses persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk reject reimbursements
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'reimbursement_ids' => 'required|array|min:1',
            'reimbursement_ids.*' => 'required|string',
            'alasan_penolakan' => 'required|string|max:1000'
        ], [
            'reimbursement_ids.required' => 'Pilih minimal satu reimbursement untuk ditolak',
            'reimbursement_ids.min' => 'Pilih minimal satu reimbursement untuk ditolak',
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi'
        ]);

        try {
            $rejectedCount = 0;
            $errors = [];

            // Use database transaction for consistency
            \DB::transaction(function () use ($validated, &$rejectedCount, &$errors) {
                foreach ($validated['reimbursement_ids'] as $hashId) {
                    // Decode hash ID to get actual ID
                    $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
                    if (!$id) {
                        $errors[] = "Invalid ID: {$hashId}";
                        continue;
                    }

                    $reimbursement = Reimbursement::find($id);
                    if (!$reimbursement) {
                        $errors[] = "Reimbursement tidak ditemukan: {$hashId}";
                        continue;
                    }

                    try {
                        // Use workflow method for rejection
                        $reimbursement->reject(auth()->id(), $validated['alasan_penolakan']);
                        $rejectedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Reimbursement {$reimbursement->nomor_reimbursement}: {$e->getMessage()}";
                    }
                }
            });

            $message = "Berhasil menolak {$rejectedCount} reimbursement";
            if (!empty($errors)) {
                $message .= ". Beberapa item tidak dapat diproses: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " lainnya";
                }
            }

            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('perusahaan.keuangan.reimbursement.index')
                ->with('error', 'Terjadi kesalahan saat memproses penolakan: ' . $e->getMessage());
        }
    }
}