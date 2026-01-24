<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\PatroliMandiri;
use App\Models\Project;
use App\Models\AreaPatrol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class PatroliMandiriController extends Controller
{
    /**
     * Check if patroli_mandiri table exists
     */
    private function tableExists(): bool
    {
        try {
            return Schema::hasTable('patroli_mandiri');
        } catch (\Exception $e) {
            \Log::error('Error checking patroli_mandiri table: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get error response when table doesn't exist
     */
    private function getTableNotExistsResponse($redirectRoute = 'perusahaan.patroli-mandiri.index')
    {
        $message = 'Fitur Patroli Mandiri belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.';
        
        if ($redirectRoute === 'perusahaan.patroli-mandiri.index') {
            // For index page, return view with empty data
            $patroliMandiri = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                15, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return view('perusahaan.patroli-mandiri.index', compact('patroliMandiri', 'projects'))
                ->with('info', $message);
        }
        
        return redirect()->route($redirectRoute)->with('error', $message);
    }

    public function index(Request $request)
    {
        try {
            // Check if patroli_mandiri table exists
            if (!$this->tableExists()) {
                return $this->getTableNotExistsResponse('perusahaan.patroli-mandiri.index');
            }

            $query = PatroliMandiri::with([
                'project:id,nama',
                'areaPatrol:id,nama',
                'petugas:id,name',
                'reviewer:id,name'
            ]);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lokasi', 'ILIKE', "%{$search}%")
                      ->orWhere('deskripsi_kendala', 'ILIKE', "%{$search}%")
                      ->orWhere('catatan_petugas', 'ILIKE', "%{$search}%")
                      ->orWhereHas('petugas', function($sq) use ($search) {
                          $sq->where('name', 'ILIKE', "%{$search}%");
                      });
                });
            }

            // Filter by project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter by status lokasi
            if ($request->filled('status_lokasi')) {
                $query->where('status_lokasi', $request->status_lokasi);
            }

            // Filter by prioritas
            if ($request->filled('prioritas')) {
                $query->where('prioritas', $request->prioritas);
            }

            // Filter by status laporan
            if ($request->filled('status_laporan')) {
                $query->where('status_laporan', $request->status_laporan);
            }

            // Filter by date range
            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('waktu_laporan', '>=', $request->tanggal_mulai);
            }
            if ($request->filled('tanggal_selesai')) {
                $query->whereDate('waktu_laporan', '<=', $request->tanggal_selesai);
            }

            $patroliMandiri = $query->orderBy('waktu_laporan', 'desc')->paginate(15);

            // Get filter options
            $projects = Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return view('perusahaan.patroli-mandiri.index', compact('patroliMandiri', 'projects'));

        } catch (\Exception $e) {
            \Log::error('Error in PatroliMandiriController@index: ' . $e->getMessage());
            return $this->getTableNotExistsResponse();
        }
    }

    public function create()
    {
        if (!$this->tableExists()) {
            return $this->getTableNotExistsResponse();
        }

        // Get data for form
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $petugas = User::select('id', 'name')
            ->where('role', 'security_officer')
            ->where('is_active', true)
            ->whereHas('karyawan', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('perusahaan.patroli-mandiri.create', compact('projects', 'petugas'));
    }

    public function store(Request $request)
    {
        if (!$this->tableExists()) {
            return redirect()->route('perusahaan.patroli-mandiri.index')
                ->with('error', 'Fitur tidak tersedia');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_patrol_id' => 'nullable|exists:area_patrols,id',
            'petugas_id' => 'required|exists:users,id',
            'nama_lokasi' => 'required|string|max:255',
            'koordinat' => 'required|string|regex:/^-?\d+\.?\d*,\s*-?\d+\.?\d*$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status_lokasi' => 'required|in:aman,tidak_aman',
            'jenis_kendala' => 'nullable|string|max:255',
            'deskripsi_kendala' => 'nullable|string|max:1000',
            'catatan_petugas' => 'nullable|string|max:1000',
            'tindakan_yang_diambil' => 'nullable|string|max:1000',
            'foto_lokasi' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_kendala' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'petugas_id.required' => 'Petugas harus dipilih',
            'petugas_id.exists' => 'Petugas tidak valid',
            'nama_lokasi.required' => 'Nama lokasi harus diisi',
            'koordinat.required' => 'Koordinat GPS harus diisi',
            'koordinat.regex' => 'Format koordinat tidak valid. Gunakan format: latitude, longitude (contoh: -6.200000, 106.800000)',
            'latitude.required' => 'Koordinat latitude harus diisi',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.required' => 'Koordinat longitude harus diisi',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
            'status_lokasi.required' => 'Status lokasi harus dipilih',
            'status_lokasi.in' => 'Status lokasi tidak valid',
            'foto_lokasi.required' => 'Foto lokasi harus diupload',
            'foto_lokasi.image' => 'File foto lokasi harus berupa gambar',
            'foto_lokasi.mimes' => 'Foto lokasi harus berformat jpeg, png, atau jpg',
            'foto_lokasi.max' => 'Ukuran foto lokasi maksimal 2MB',
            'foto_kendala.image' => 'File foto kendala harus berupa gambar',
            'foto_kendala.mimes' => 'Foto kendala harus berformat jpeg, png, atau jpg',
            'foto_kendala.max' => 'Ukuran foto kendala maksimal 2MB',
        ]);

        // Validasi tambahan: jika tidak aman, jenis kendala wajib
        if ($request->status_lokasi === 'tidak_aman' && !$request->jenis_kendala) {
            return back()->withErrors(['jenis_kendala' => 'Jenis kendala harus dipilih untuk lokasi tidak aman'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
            $validated['waktu_laporan'] = now();

            // Upload foto lokasi
            if ($request->hasFile('foto_lokasi')) {
                $fotoLokasi = $request->file('foto_lokasi');
                $fotoLokasiPath = $fotoLokasi->store('patroli-mandiri/lokasi', 'public');
                $validated['foto_lokasi'] = $fotoLokasiPath;
            }

            // Upload foto kendala jika ada
            if ($request->hasFile('foto_kendala')) {
                $fotoKendala = $request->file('foto_kendala');
                $fotoKendalaPath = $fotoKendala->store('patroli-mandiri/kendala', 'public');
                $validated['foto_kendala'] = $fotoKendalaPath;
            }

            // Create patroli mandiri
            $patroliMandiri = PatroliMandiri::create($validated);

            // Set prioritas dan generate maps URL
            $patroliMandiri->setPrioritas();
            $patroliMandiri->generateMapsUrl();
            $patroliMandiri->save();

            DB::commit();

            return redirect()->route('perusahaan.patroli-mandiri.index')
                ->with('success', 'Laporan patroli mandiri berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating patroli mandiri: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal membuat laporan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(PatroliMandiri $patroliMandiri)
    {
        if (!$this->tableExists()) {
            return $this->getTableNotExistsResponse();
        }

        $patroliMandiri->load([
            'project:id,nama',
            'areaPatrol:id,nama',
            'petugas:id,name,email',
            'reviewer:id,name,email'
        ]);

        return view('perusahaan.patroli-mandiri.show', compact('patroliMandiri'));
    }

    public function review(Request $request, PatroliMandiri $patroliMandiri)
    {
        if (!$this->tableExists()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur tidak tersedia'
            ], 503);
        }

        $validated = $request->validate([
            'status_laporan' => 'required|in:reviewed,resolved',
            'review_catatan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $patroliMandiri->update([
                'status_laporan' => $validated['status_laporan'],
                'review_catatan' => $validated['review_catatan'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review berhasil disimpan',
                'data' => $patroliMandiri->fresh(['reviewer:id,name'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reviewing patroli mandiri: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan review: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(PatroliMandiri $patroliMandiri)
    {
        if (!$this->tableExists()) {
            return $this->getTableNotExistsResponse();
        }

        // Only allow editing if status is still submitted
        if ($patroliMandiri->status_laporan !== 'submitted') {
            return redirect()->route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id)
                ->with('error', 'Laporan yang sudah direview tidak dapat diedit');
        }

        $patroliMandiri->load([
            'project:id,nama',
            'areaPatrol:id,nama',
            'petugas:id,name'
        ]);

        // Get data for form
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $areas = AreaPatrol::select('id', 'nama')
            ->where('project_id', $patroliMandiri->project_id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $petugas = User::select('id', 'name')
            ->where('role', 'security_officer')
            ->where('is_active', true)
            ->whereHas('karyawan', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('perusahaan.patroli-mandiri.edit', compact('patroliMandiri', 'projects', 'areas', 'petugas'));
    }

    public function update(Request $request, PatroliMandiri $patroliMandiri)
    {
        if (!$this->tableExists()) {
            return redirect()->route('perusahaan.patroli-mandiri.index')
                ->with('error', 'Fitur tidak tersedia');
        }

        // Only allow editing if status is still submitted
        if ($patroliMandiri->status_laporan !== 'submitted') {
            return redirect()->route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id)
                ->with('error', 'Laporan yang sudah direview tidak dapat diedit');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_patrol_id' => 'nullable|exists:area_patrols,id',
            'petugas_id' => 'required|exists:users,id',
            'nama_lokasi' => 'required|string|max:255',
            'koordinat' => 'required|string|regex:/^-?\d+\.?\d*,\s*-?\d+\.?\d*$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status_lokasi' => 'required|in:aman,tidak_aman',
            'jenis_kendala' => 'nullable|string|max:255',
            'deskripsi_kendala' => 'nullable|string|max:1000',
            'catatan_petugas' => 'nullable|string|max:1000',
            'tindakan_yang_diambil' => 'nullable|string|max:1000',
            'foto_lokasi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_kendala' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'petugas_id.required' => 'Petugas harus dipilih',
            'petugas_id.exists' => 'Petugas tidak valid',
            'nama_lokasi.required' => 'Nama lokasi harus diisi',
            'koordinat.required' => 'Koordinat GPS harus diisi',
            'koordinat.regex' => 'Format koordinat tidak valid. Gunakan format: latitude, longitude (contoh: -6.200000, 106.800000)',
            'latitude.required' => 'Koordinat latitude harus diisi',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.required' => 'Koordinat longitude harus diisi',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
            'status_lokasi.required' => 'Status lokasi harus dipilih',
            'status_lokasi.in' => 'Status lokasi tidak valid',
            'foto_lokasi.image' => 'File foto lokasi harus berupa gambar',
            'foto_lokasi.mimes' => 'Foto lokasi harus berformat jpeg, png, atau jpg',
            'foto_lokasi.max' => 'Ukuran foto lokasi maksimal 2MB',
            'foto_kendala.image' => 'File foto kendala harus berupa gambar',
            'foto_kendala.mimes' => 'Foto kendala harus berformat jpeg, png, atau jpg',
            'foto_kendala.max' => 'Ukuran foto kendala maksimal 2MB',
        ]);

        // Validasi tambahan: jika tidak aman, jenis kendala wajib
        if ($request->status_lokasi === 'tidak_aman' && !$request->jenis_kendala) {
            return back()->withErrors(['jenis_kendala' => 'Jenis kendala harus dipilih untuk lokasi tidak aman'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Upload foto lokasi baru jika ada
            if ($request->hasFile('foto_lokasi')) {
                // Delete old photo
                if ($patroliMandiri->foto_lokasi) {
                    Storage::disk('public')->delete($patroliMandiri->foto_lokasi);
                }
                
                $fotoLokasi = $request->file('foto_lokasi');
                $fotoLokasiPath = $fotoLokasi->store('patroli-mandiri/lokasi', 'public');
                $validated['foto_lokasi'] = $fotoLokasiPath;
            }

            // Upload foto kendala baru jika ada
            if ($request->hasFile('foto_kendala')) {
                // Delete old photo
                if ($patroliMandiri->foto_kendala) {
                    Storage::disk('public')->delete($patroliMandiri->foto_kendala);
                }
                
                $fotoKendala = $request->file('foto_kendala');
                $fotoKendalaPath = $fotoKendala->store('patroli-mandiri/kendala', 'public');
                $validated['foto_kendala'] = $fotoKendalaPath;
            }

            // Clear kendala fields if status changed to aman
            if ($validated['status_lokasi'] === 'aman') {
                $validated['jenis_kendala'] = null;
                $validated['deskripsi_kendala'] = null;
                $validated['tindakan_yang_diambil'] = null;
                
                // Delete foto kendala if exists
                if ($patroliMandiri->foto_kendala) {
                    Storage::disk('public')->delete($patroliMandiri->foto_kendala);
                    $validated['foto_kendala'] = null;
                }
            }

            $patroliMandiri->update($validated);

            // Update prioritas dan generate maps URL
            $patroliMandiri->setPrioritas();
            $patroliMandiri->generateMapsUrl();
            $patroliMandiri->save();

            DB::commit();

            return redirect()->route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id)
                ->with('success', 'Laporan patroli mandiri berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating patroli mandiri: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal mengupdate laporan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(PatroliMandiri $patroliMandiri)
    {
        if (!$this->tableExists()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur tidak tersedia'
            ], 503);
        }

        try {
            DB::beginTransaction();

            // Delete photos
            if ($patroliMandiri->foto_lokasi) {
                Storage::disk('public')->delete($patroliMandiri->foto_lokasi);
            }
            if ($patroliMandiri->foto_kendala) {
                Storage::disk('public')->delete($patroliMandiri->foto_kendala);
            }

            $patroliMandiri->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan patroli mandiri berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting patroli mandiri: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get areas by project for AJAX
     */
    public function getAreasByProject($project)
    {
        try {
            $areas = AreaPatrol::select('id', 'nama')
                ->where('project_id', $project)
                ->where('perusahaan_id', auth()->user()->perusahaan_id) // Add security filter
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $areas
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading areas by project: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat area',
                'data' => []
            ], 500);
        }
    }

    /**
     * Search locations for Select2
     */
    public function searchLocations(Request $request)
    {
        $query = $request->get('q', '');
        
        $locations = PatroliMandiri::select('nama_lokasi as text')
            ->where('nama_lokasi', 'ILIKE', "%{$query}%")
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->distinct()
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->text,
                    'text' => $item->text
                ];
            });

        return response()->json([
            'data' => $locations,
            'total' => $locations->count()
        ]);
    }

    /**
     * Search jenis kendala for Select2
     */
    public function searchJenisKendala(Request $request)
    {
        $query = $request->get('q', '');
        
        // Get existing jenis kendala from database
        $existingKendala = PatroliMandiri::select('jenis_kendala as text')
            ->where('jenis_kendala', 'ILIKE', "%{$query}%")
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->whereNotNull('jenis_kendala')
            ->distinct()
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->text,
                    'text' => ucwords(str_replace('_', ' ', $item->text))
                ];
            });

        // Default jenis kendala options
        $defaultKendala = collect([
            ['id' => 'kebakaran', 'text' => 'Kebakaran'],
            ['id' => 'aset_rusak', 'text' => 'Aset Rusak'],
            ['id' => 'aset_hilang', 'text' => 'Aset Hilang'],
            ['id' => 'orang_mencurigakan', 'text' => 'Orang Mencurigakan'],
            ['id' => 'kabel_terbuka', 'text' => 'Kabel Terbuka'],
            ['id' => 'pencurian', 'text' => 'Pencurian'],
            ['id' => 'sabotase', 'text' => 'Sabotase'],
            ['id' => 'demo', 'text' => 'Demo'],
        ])->filter(function($item) use ($query) {
            return empty($query) || stripos($item['text'], $query) !== false;
        });

        // Merge and remove duplicates
        $allKendala = $defaultKendala->merge($existingKendala)
            ->unique('id')
            ->values()
            ->take(10);

        return response()->json([
            'data' => $allKendala,
            'total' => $allKendala->count()
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        if (!$this->tableExists()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur tidak tersedia'
            ], 503);
        }

        try {
            $stats = [
                'total_laporan' => PatroliMandiri::count(),
                'lokasi_aman' => PatroliMandiri::aman()->count(),
                'lokasi_tidak_aman' => PatroliMandiri::tidakAman()->count(),
                'belum_direview' => PatroliMandiri::where('status_laporan', 'submitted')->count(),
                'prioritas_kritis' => PatroliMandiri::byPrioritas('kritis')->count(),
                'laporan_hari_ini' => PatroliMandiri::whereDate('waktu_laporan', today())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting patroli mandiri statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }
}