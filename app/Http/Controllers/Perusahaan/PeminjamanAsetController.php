<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanAset;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanAsetController extends Controller
{
    public function index(Request $request)
    {
        $query = PeminjamanAset::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                },
                'dataAset' => function($query) {
                    $query->withoutGlobalScopes()->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
                },
                'asetKendaraan' => function($query) {
                    $query->withoutGlobalScopes()->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
                },
                'peminjamKaryawan' => function($query) {
                    $query->withoutGlobalScopes()->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
                },
                'createdBy:id,name'
            ])
            ->select([
                'id',
                'project_id',
                'aset_type',
                'data_aset_id',
                'aset_kendaraan_id',
                'peminjam_karyawan_id',
                'created_by',
                'kode_peminjaman',
                'tanggal_peminjaman',
                'tanggal_rencana_kembali',
                'tanggal_kembali_aktual',
                'jumlah_dipinjam',
                'status_peminjaman',
                'keperluan',
                'kondisi_saat_dipinjam',
                'kondisi_saat_dikembalikan',
                'created_at'
            ])
            // Add validation to ensure we only get records with valid data
            ->whereNotNull('peminjam_karyawan_id')
            ->where(function($subQuery) {
                $subQuery->where(function($q) {
                    $q->where('aset_type', 'data_aset')
                      ->whereNotNull('data_aset_id');
                })->orWhere(function($q) {
                    $q->where('aset_type', 'aset_kendaraan')
                      ->whereNotNull('aset_kendaraan_id');
                });
            });

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status_peminjaman')) {
            $query->where('status_peminjaman', $request->status_peminjaman);
        }

        // Filter by aset type
        if ($request->filled('aset_type')) {
            $query->where('aset_type', $request->aset_type);
        }

        // Filter by aset
        if ($request->filled('data_aset_id')) {
            $query->where('data_aset_id', $request->data_aset_id);
        }

        // Filter by kendaraan
        if ($request->filled('aset_kendaraan_id')) {
            $query->where('aset_kendaraan_id', $request->aset_kendaraan_id);
        }

        // Filter terlambat
        if ($request->filled('terlambat') && $request->terlambat == '1') {
            $query->terlambat();
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Get per_page from request, default 20, max 100
        $perPage = min((int) $request->get('per_page', 20), 100);

        $peminjamans = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Preserve filter parameters in pagination links
        $peminjamans->appends($request->query());

        // Data untuk filter
        $projects = Project::withoutGlobalScope('project_access')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
            
        $statusOptions = PeminjamanAset::getStatusOptions();
        $asetTypeOptions = PeminjamanAset::getAsetTypeOptions();

        return view('perusahaan.peminjaman-aset.index', compact(
            'peminjamans',
            'projects',
            'statusOptions',
            'asetTypeOptions'
        ));
    }

    public function create()
    {
        $projects = Project::withoutGlobalScope('project_access')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
            
        $kondisiOptions = PeminjamanAset::getKondisiOptions();
        $asetTypeOptions = PeminjamanAset::getAsetTypeOptions();

        return view('perusahaan.peminjaman-aset.create', compact(
            'projects',
            'kondisiOptions',
            'asetTypeOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'aset_type' => 'required|in:data_aset,aset_kendaraan',
            'data_aset_id' => 'required_if:aset_type,data_aset|nullable|exists:data_asets,id',
            'aset_kendaraan_id' => 'required_if:aset_type,aset_kendaraan|nullable|exists:aset_kendaraans,id',
            'peminjam_karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_peminjaman' => 'required|date|after_or_equal:today',
            'tanggal_rencana_kembali' => 'required|date|after:tanggal_peminjaman',
            'jumlah_dipinjam' => 'required|integer|min:1|max:100',
            'keperluan' => 'required|string|max:1000',
            'kondisi_saat_dipinjam' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_peminjaman' => 'nullable|string|max:1000',
            'file_bukti_peminjaman' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        // Set peminjam_user_id to null since we only use karyawan
        $validated['peminjam_user_id'] = null;

        // Validasi aset berdasarkan type
        if ($validated['aset_type'] === 'data_aset') {
            $validated['aset_kendaraan_id'] = null;
        } else {
            $validated['data_aset_id'] = null;
        }

        // Handle file upload
        if ($request->hasFile('file_bukti_peminjaman')) {
            $validated['file_bukti_peminjaman'] = $request->file('file_bukti_peminjaman')
                ->store('peminjaman-aset/bukti-peminjaman', 'public');
        }

        // Auto-assign perusahaan_id dan created_by akan dilakukan di model
        $peminjaman = PeminjamanAset::create($validated);

        return redirect()
            ->route('perusahaan.peminjaman-aset.show', $peminjaman->hash_id)
            ->with('success', 'Peminjaman aset berhasil dibuat dengan kode: ' . $peminjaman->kode_peminjaman);
    }

    public function show(PeminjamanAset $peminjamanAset)
    {
        $peminjamanAset->load([
            'project' => function($query) {
                $query->withoutGlobalScope('project_access')->select('id', 'nama');
            },
            'dataAset' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
            },
            'asetKendaraan' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
            },
            'peminjamKaryawan' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
            },
            'createdBy:id,name',
            'approvedBy:id,name',
            'returnedBy:id,name'
        ]);
        
        return view('perusahaan.peminjaman-aset.show', compact('peminjamanAset'));
    }

    public function edit(PeminjamanAset $peminjamanAset)
    {
        // Hanya bisa edit jika status masih pending atau approved
        if (!in_array($peminjamanAset->status_peminjaman, ['pending', 'approved'])) {
            return redirect()
                ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                ->with('error', 'Peminjaman dengan status "' . $peminjamanAset->status_label . '" tidak dapat diedit');
        }

        // Load relations for display
        $peminjamanAset->load([
            'project' => function($query) {
                $query->withoutGlobalScope('project_access')->select('id', 'nama');
            },
            'dataAset' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
            },
            'asetKendaraan' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
            },
            'peminjamKaryawan' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
            }
        ]);

        $projects = Project::withoutGlobalScope('project_access')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
            
        $kondisiOptions = PeminjamanAset::getKondisiOptions();
        $asetTypeOptions = PeminjamanAset::getAsetTypeOptions();

        return view('perusahaan.peminjaman-aset.edit', compact(
            'peminjamanAset',
            'projects',
            'kondisiOptions',
            'asetTypeOptions'
        ));
    }

    public function update(Request $request, PeminjamanAset $peminjamanAset)
    {
        // Hanya bisa update jika status masih pending atau approved
        if (!in_array($peminjamanAset->status_peminjaman, ['pending', 'approved'])) {
            return redirect()
                ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                ->with('error', 'Peminjaman dengan status "' . $peminjamanAset->status_label . '" tidak dapat diubah');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'aset_type' => 'required|in:data_aset,aset_kendaraan',
            'data_aset_id' => 'required_if:aset_type,data_aset|nullable|exists:data_asets,id',
            'aset_kendaraan_id' => 'required_if:aset_type,aset_kendaraan|nullable|exists:aset_kendaraans,id',
            'peminjam_karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after:tanggal_peminjaman',
            'jumlah_dipinjam' => 'required|integer|min:1|max:100',
            'keperluan' => 'required|string|max:1000',
            'kondisi_saat_dipinjam' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_peminjaman' => 'nullable|string|max:1000',
            'file_bukti_peminjaman' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        // Set peminjam_user_id to null since we only use karyawan
        $validated['peminjam_user_id'] = null;

        // Validasi aset berdasarkan type
        if ($validated['aset_type'] === 'data_aset') {
            $validated['aset_kendaraan_id'] = null;
        } else {
            $validated['data_aset_id'] = null;
        }

        // Handle file upload
        if ($request->hasFile('file_bukti_peminjaman')) {
            // Delete old file
            if ($peminjamanAset->file_bukti_peminjaman) {
                Storage::disk('public')->delete($peminjamanAset->file_bukti_peminjaman);
            }
            
            $validated['file_bukti_peminjaman'] = $request->file('file_bukti_peminjaman')
                ->store('peminjaman-aset/bukti-peminjaman', 'public');
        }

        $peminjamanAset->update($validated);

        return redirect()
            ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
            ->with('success', 'Data peminjaman berhasil diperbarui');
    }

    public function destroy(PeminjamanAset $peminjamanAset)
    {
        // Hanya bisa hapus jika status pending atau ditolak
        if (!in_array($peminjamanAset->status_peminjaman, ['pending', 'ditolak'])) {
            return redirect()
                ->route('perusahaan.peminjaman-aset.index')
                ->with('error', 'Peminjaman dengan status "' . $peminjamanAset->status_label . '" tidak dapat dihapus');
        }

        // Delete files
        if ($peminjamanAset->file_bukti_peminjaman) {
            Storage::disk('public')->delete($peminjamanAset->file_bukti_peminjaman);
        }
        if ($peminjamanAset->file_bukti_pengembalian) {
            Storage::disk('public')->delete($peminjamanAset->file_bukti_pengembalian);
        }

        $kodePeminjaman = $peminjamanAset->kode_peminjaman;
        $peminjamanAset->delete();

        return redirect()
            ->route('perusahaan.peminjaman-aset.index')
            ->with('success', "Peminjaman {$kodePeminjaman} berhasil dihapus");
    }

    // Action methods
    public function approve(PeminjamanAset $peminjamanAset)
    {
        if ($peminjamanAset->status_peminjaman !== 'pending') {
            return redirect()
                ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                ->with('error', 'Hanya peminjaman dengan status "Menunggu Persetujuan" yang dapat disetujui');
        }

        $peminjamanAset->approve();

        return redirect()
            ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
            ->with('success', 'Peminjaman berhasil disetujui');
    }

    public function reject(Request $request, PeminjamanAset $peminjamanAset)
    {
        if ($peminjamanAset->status_peminjaman !== 'pending') {
            return redirect()
                ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                ->with('error', 'Hanya peminjaman dengan status "Menunggu Persetujuan" yang dapat ditolak');
        }

        $validated = $request->validate([
            'catatan_penolakan' => 'required|string|max:1000',
        ]);

        $peminjamanAset->reject(auth()->id(), $validated['catatan_penolakan']);

        return redirect()
            ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
            ->with('success', 'Peminjaman berhasil ditolak');
    }

    public function borrow(PeminjamanAset $peminjamanAset)
    {
        if ($peminjamanAset->status_peminjaman !== 'approved') {
            return redirect()
                ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                ->with('error', 'Hanya peminjaman dengan status "Disetujui" yang dapat dipinjam');
        }

        $peminjamanAset->borrow();

        return redirect()
            ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
            ->with('success', 'Aset berhasil dipinjam');
    }

    public function returnForm(PeminjamanAset $peminjamanAset)
    {
        if ($peminjamanAset->status_peminjaman !== 'dipinjam') {
            return redirect()
                ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                ->with('error', 'Hanya peminjaman dengan status "Sedang Dipinjam" yang dapat dikembalikan');
        }

        // Load relations for display
        $peminjamanAset->load([
            'project' => function($query) {
                $query->withoutGlobalScope('project_access')->select('id', 'nama');
            },
            'dataAset' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
            },
            'asetKendaraan' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
            },
            'peminjamKaryawan' => function($query) {
                $query->withoutGlobalScopes()->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
            }
        ]);

        $kondisiOptions = PeminjamanAset::getKondisiPengembalianOptions();

        return view('perusahaan.peminjaman-aset.return', compact('peminjamanAset', 'kondisiOptions'));
    }

    public function returnAsset(Request $request, PeminjamanAset $peminjamanAset)
    {
        try {
            // Log the request for debugging
            \Log::info('Return asset request received', [
                'peminjaman_id' => $peminjamanAset->id,
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'accept_header' => $request->header('Accept'),
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'request_data' => $request->except(['file_bukti_pengembalian'])
            ]);

            if ($peminjamanAset->status_peminjaman !== 'dipinjam') {
                $message = 'Hanya peminjaman dengan status "Sedang Dipinjam" yang dapat dikembalikan';
                
                if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400, ['Content-Type' => 'application/json']);
                }
                
                return redirect()
                    ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
                    ->with('error', $message);
            }

            $validated = $request->validate([
                'kondisi_saat_dikembalikan' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
                'catatan_pengembalian' => 'nullable|string|max:1000',
                'file_bukti_pengembalian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
            ]);

            // Handle file upload
            if ($request->hasFile('file_bukti_pengembalian')) {
                $validated['file_bukti_pengembalian'] = $request->file('file_bukti_pengembalian')
                    ->store('peminjaman-aset/bukti-pengembalian', 'public');
            }

            // Update peminjaman
            $peminjamanAset->update([
                'status_peminjaman' => 'dikembalikan',
                'returned_by' => auth()->id(),
                'returned_at' => now(),
                'tanggal_kembali_aktual' => now()->toDateString(),
                'kondisi_saat_dikembalikan' => $validated['kondisi_saat_dikembalikan'],
                'catatan_pengembalian' => $validated['catatan_pengembalian'] ?? null,
                'file_bukti_pengembalian' => $validated['file_bukti_pengembalian'] ?? null,
            ]);

            // Don't regenerate session to avoid issues with Cloudflare tunnel
            // $request->session()->regenerate();

            \Log::info('Asset return successful', [
                'peminjaman_id' => $peminjamanAset->id,
                'user_id' => auth()->id(),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson()
            ]);

            $successMessage = 'Aset berhasil dikembalikan';
            $redirectUrl = route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'redirect_url' => $redirectUrl
                ], 200, ['Content-Type' => 'application/json']);
            }

            // For regular form submission, redirect with success message
            return redirect($redirectUrl)
                ->with('success', $successMessage)
                ->with('alert', [
                    'type' => 'success',
                    'message' => $successMessage
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error in returnAsset', [
                'peminjaman_id' => $peminjamanAset->id,
                'user_id' => auth()->id(),
                'errors' => $e->errors()
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422, ['Content-Type' => 'application/json']);
            }
            
            return redirect()
                ->route('perusahaan.peminjaman-aset.return-form', $peminjamanAset->hash_id)
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Error in returnAsset', [
                'peminjaman_id' => $peminjamanAset->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['file_bukti_pengembalian'])
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat memproses pengembalian aset. Silakan coba lagi.';
            
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500, ['Content-Type' => 'application/json']);
            }
            
            return redirect()
                ->route('perusahaan.peminjaman-aset.return-form', $peminjamanAset->hash_id)
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    // Export bukti peminjaman ke PDF
    public function exportBuktiPeminjaman(PeminjamanAset $peminjamanAset)
    {
        $peminjamanAset->load([
            'project' => function($query) {
                $query->withoutGlobalScope('project_access')->select('id', 'nama');
            },
            'dataAset' => function($query) {
                $query->withoutGlobalScope('perusahaan');
            },
            'asetKendaraan' => function($query) {
                $query->withoutGlobalScope('perusahaan');
            },
            'peminjamKaryawan',
            'createdBy:id,name',
            'approvedBy:id,name',
            'perusahaan:id,nama,alamat,telepon,email'
        ]);

        $pdf = Pdf::loadView('perusahaan.peminjaman-aset.bukti-pdf', compact('peminjamanAset'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Bukti-Peminjaman-" . str_replace(['/', '\\'], '_', $peminjamanAset->kode_peminjaman) . ".pdf");
    }

    // Dashboard untuk peminjaman yang akan jatuh tempo
    public function jatuhTempo()
    {
        $akanJatuhTempo = PeminjamanAset::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                },
                'dataAset' => function($query) {
                    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_aset', 'nama_aset');
                },
                'asetKendaraan' => function($query) {
                    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'nomor_polisi');
                },
                'peminjamKaryawan:id,nama_lengkap'
            ])
            ->select([
                'id', 'project_id', 'aset_type', 'data_aset_id', 'aset_kendaraan_id', 'peminjam_karyawan_id',
                'kode_peminjaman', 'tanggal_rencana_kembali', 'status_peminjaman'
            ])
            ->akanJatuhTempo(7) // 7 hari ke depan
            ->orderBy('tanggal_rencana_kembali')
            ->get();

        $terlambat = PeminjamanAset::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                },
                'dataAset' => function($query) {
                    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_aset', 'nama_aset');
                },
                'asetKendaraan' => function($query) {
                    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'nomor_polisi');
                },
                'peminjamKaryawan:id,nama_lengkap'
            ])
            ->select([
                'id', 'project_id', 'aset_type', 'data_aset_id', 'aset_kendaraan_id', 'peminjam_karyawan_id',
                'kode_peminjaman', 'tanggal_rencana_kembali', 'status_peminjaman'
            ])
            ->terlambat()
            ->orderBy('tanggal_rencana_kembali')
            ->get();

        return view('perusahaan.peminjaman-aset.jatuh-tempo', compact('akanJatuhTempo', 'terlambat'));
    }

    // API endpoint untuk search aset berdasarkan type
    public function searchAsets(Request $request)
    {
        $type = $request->get('type', 'data_aset');
        $search = $request->get('search', '');
        $projectId = $request->get('project_id');
        
        if ($type === 'aset_kendaraan') {
            $query = AsetKendaraan::where('status_kendaraan', 'aktif');
            
            // Filter by project if provided
            if ($projectId) {
                $query->where('project_id', $projectId);
            }
            
            $asets = $query->where(function($subQuery) use ($search) {
                    $subQuery->where('merk', 'like', "%{$search}%")
                          ->orWhere('model', 'like', "%{$search}%")
                          ->orWhere('kode_kendaraan', 'like', "%{$search}%")
                          ->orWhere('nomor_polisi', 'like', "%{$search}%");
                })
                ->select('id', 'kode_kendaraan as kode', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi')
                ->limit(20)
                ->get()
                ->map(function($item) {
                    $nama = "{$item->merk} {$item->model} ({$item->tahun_pembuatan})";
                    return [
                        'id' => $item->id,
                        'text' => "{$item->kode} - {$nama} - {$item->nomor_polisi}",
                        'kode' => $item->kode,
                        'nama' => $nama,
                        'kategori' => "Kendaraan - {$item->jenis_kendaraan}",
                        'extra' => $item->nomor_polisi
                    ];
                });
        } else {
            $query = DataAset::where('status', 'ada');
            
            // Filter by project if provided
            if ($projectId) {
                $query->where('project_id', $projectId);
            }
            
            $asets = $query->where(function($subQuery) use ($search) {
                    $subQuery->where('nama_aset', 'like', "%{$search}%")
                          ->orWhere('kode_aset', 'like', "%{$search}%")
                          ->orWhere('kategori', 'like', "%{$search}%");
                })
                ->select('id', 'kode_aset as kode', 'nama_aset as nama', 'kategori')
                ->limit(20)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'text' => "{$item->kode} - {$item->nama}",
                        'kode' => $item->kode,
                        'nama' => $item->nama,
                        'kategori' => $item->kategori,
                        'extra' => null
                    ];
                });
        }

        return response()->json($asets);
    }

    // Export laporan peminjaman aset ke PDF
    public function exportLaporan(Request $request)
    {
        $query = PeminjamanAset::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                },
                'dataAset' => function($query) {
                    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_aset', 'nama_aset', 'kategori');
                },
                'asetKendaraan' => function($query) {
                    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi');
                },
                'peminjamKaryawan:id,nama_lengkap,nik_karyawan',
                'createdBy:id,name'
            ])
            ->select([
                'id',
                'project_id',
                'aset_type',
                'data_aset_id',
                'aset_kendaraan_id',
                'peminjam_karyawan_id',
                'created_by',
                'kode_peminjaman',
                'tanggal_peminjaman',
                'tanggal_rencana_kembali',
                'tanggal_kembali_aktual',
                'jumlah_dipinjam',
                'status_peminjaman',
                'keperluan',
                'kondisi_saat_dipinjam',
                'kondisi_saat_dikembalikan',
                'created_at'
            ]);

        // Apply same filters as index
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status_peminjaman')) {
            $query->where('status_peminjaman', $request->status_peminjaman);
        }

        if ($request->filled('aset_type')) {
            $query->where('aset_type', $request->aset_type);
        }

        if ($request->filled('data_aset_id')) {
            $query->where('data_aset_id', $request->data_aset_id);
        }

        if ($request->filled('aset_kendaraan_id')) {
            $query->where('aset_kendaraan_id', $request->aset_kendaraan_id);
        }

        if ($request->filled('terlambat') && $request->terlambat == '1') {
            $query->terlambat();
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Date range filter
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_peminjaman', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_peminjaman', '<=', $request->tanggal_sampai);
        }

        $peminjamans = $query->orderBy('created_at', 'desc')->get();

        // Get filter info for report header
        $filterInfo = [
            'project' => $request->filled('project_id') ? 
                Project::withoutGlobalScope('project_access')->find($request->project_id)?->nama : 'Semua Project',
            'status' => $request->filled('status_peminjaman') ? 
                PeminjamanAset::getStatusOptions()[$request->status_peminjaman] ?? $request->status_peminjaman : 'Semua Status',
            'aset_type' => $request->filled('aset_type') ? 
                PeminjamanAset::getAsetTypeOptions()[$request->aset_type] ?? $request->aset_type : 'Semua Tipe Aset',
            'tanggal_dari' => $request->tanggal_dari,
            'tanggal_sampai' => $request->tanggal_sampai,
            'search' => $request->search,
            'terlambat' => $request->terlambat == '1' ? 'Ya' : 'Tidak'
        ];

        $perusahaan = auth()->user()->perusahaan;

        $pdf = Pdf::loadView('perusahaan.peminjaman-aset.laporan-pdf', compact('peminjamans', 'filterInfo', 'perusahaan'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan-Peminjaman-Aset-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    // API endpoint untuk search karyawan
    public function searchKaryawan(Request $request)
    {
        $search = $request->get('search', '');
        
        $karyawans = Karyawan::where('is_active', true)
            ->where(function($query) use ($search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nik_karyawan', 'like', "%{$search}%");
            })
            ->select('id', 'nama_lengkap', 'nik_karyawan')
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->nama_lengkap} ({$item->nik_karyawan})",
                    'nama' => $item->nama_lengkap,
                    'nik' => $item->nik_karyawan
                ];
            });

        return response()->json($karyawans);
    }
}