<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatroliMandiri;
use App\Models\Project;
use App\Models\AreaPatrol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatroliMandiriController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = PatroliMandiri::select([
                'id',
                'project_id',
                'area_patrol_id',
                'petugas_id',
                'nama_lokasi',
                'latitude',
                'longitude',
                'waktu_laporan',
                'status_lokasi',
                'jenis_kendala',
                'prioritas',
                'status_laporan'
            ])
            ->with([
                'project:id,nama',
                'areaPatrol:id,nama',
                'petugas:id,name'
            ])
            ->where('petugas_id', auth()->id());

            // Filter by status
            if ($request->filled('status_lokasi')) {
                $query->where('status_lokasi', $request->status_lokasi);
            }

            if ($request->filled('status_laporan')) {
                $query->where('status_laporan', $request->status_laporan);
            }

            $patroliMandiri = $query->orderBy('waktu_laporan', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $patroliMandiri
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in API PatroliMandiriController@index: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data patroli mandiri'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'area_patrol_id' => 'nullable|exists:area_patrols,id',
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status_lokasi' => 'required|in:aman,tidak_aman',
            'jenis_kendala' => 'nullable|in:kebakaran,aset_rusak,aset_hilang,orang_mencurigakan,kabel_terbuka,pencurian,sabotase,demo',
            'deskripsi_kendala' => 'nullable|string|max:1000',
            'catatan_petugas' => 'nullable|string|max:1000',
            'tindakan_yang_diambil' => 'nullable|string|max:1000',
            'foto_lokasi' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_kendala' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama_lokasi.required' => 'Nama lokasi harus diisi',
            'latitude.required' => 'Koordinat latitude harus diisi',
            'longitude.required' => 'Koordinat longitude harus diisi',
            'status_lokasi.required' => 'Status lokasi harus dipilih',
            'status_lokasi.in' => 'Status lokasi tidak valid',
            'jenis_kendala.in' => 'Jenis kendala tidak valid',
            'foto_lokasi.required' => 'Foto lokasi harus diupload',
            'foto_lokasi.image' => 'File foto lokasi harus berupa gambar',
            'foto_lokasi.mimes' => 'Foto lokasi harus berformat jpeg, png, atau jpg',
            'foto_lokasi.max' => 'Ukuran foto lokasi maksimal 2MB',
            'foto_kendala.image' => 'File foto kendala harus berupa gambar',
            'foto_kendala.mimes' => 'Foto kendala harus berformat jpeg, png, atau jpg',
            'foto_kendala.max' => 'Ukuran foto kendala maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi tambahan: jika tidak aman, jenis kendala wajib
        if ($request->status_lokasi === 'tidak_aman' && !$request->jenis_kendala) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis kendala harus dipilih untuk lokasi tidak aman',
                'errors' => ['jenis_kendala' => ['Jenis kendala harus dipilih untuk lokasi tidak aman']]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $validated = $validator->validated();
            $validated['petugas_id'] = auth()->id();
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

            return response()->json([
                'success' => true,
                'message' => 'Laporan patroli mandiri berhasil dibuat',
                'data' => $patroliMandiri->load([
                    'project:id,nama',
                    'areaPatrol:id,nama'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating patroli mandiri: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan patroli mandiri: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(PatroliMandiri $patroliMandiri)
    {
        try {
            // Pastikan user hanya bisa lihat laporan sendiri (kecuali supervisor/admin)
            if ($patroliMandiri->petugas_id !== auth()->id() && !auth()->user()->hasRole(['admin', 'supervisor'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke laporan ini'
                ], 403);
            }

            $patroliMandiri->load([
                'project:id,nama',
                'areaPatrol:id,nama',
                'petugas:id,name,email',
                'reviewer:id,name,email'
            ]);

            return response()->json([
                'success' => true,
                'data' => $patroliMandiri
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing patroli mandiri: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail laporan'
            ], 500);
        }
    }

    public function update(Request $request, PatroliMandiri $patroliMandiri)
    {
        // Hanya petugas yang membuat laporan yang bisa edit (dan hanya jika belum direview)
        if ($patroliMandiri->petugas_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit laporan ini'
            ], 403);
        }

        if ($patroliMandiri->status_laporan !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Laporan yang sudah direview tidak dapat diedit'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'nama_lokasi' => 'sometimes|string|max:255',
            'status_lokasi' => 'sometimes|in:aman,tidak_aman',
            'jenis_kendala' => 'nullable|in:kebakaran,aset_rusak,aset_hilang,orang_mencurigakan,kabel_terbuka,pencurian,sabotase,demo',
            'deskripsi_kendala' => 'nullable|string|max:1000',
            'catatan_petugas' => 'nullable|string|max:1000',
            'tindakan_yang_diambil' => 'nullable|string|max:1000',
            'foto_lokasi' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'foto_kendala' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $validated = $validator->validated();

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

            $patroliMandiri->update($validated);

            // Update prioritas jika status lokasi berubah
            if (isset($validated['status_lokasi']) || isset($validated['jenis_kendala'])) {
                $patroliMandiri->setPrioritas();
                $patroliMandiri->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan patroli mandiri berhasil diupdate',
                'data' => $patroliMandiri->fresh([
                    'project:id,nama',
                    'areaPatrol:id,nama'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating patroli mandiri: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get projects for dropdown
     */
    public function getProjects()
    {
        try {
            $projects = Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $projects
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data project'
            ], 500);
        }
    }

    /**
     * Get areas by project
     */
    public function getAreasByProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Project ID tidak valid'
            ], 422);
        }

        try {
            $areas = AreaPatrol::select('id', 'nama')
                ->where('project_id', $request->project_id)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $areas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data area'
            ], 500);
        }
    }

    /**
     * Get jenis kendala options
     */
    public function getJenisKendala()
    {
        $jenisKendala = [
            ['value' => 'kebakaran', 'label' => 'Kebakaran'],
            ['value' => 'aset_rusak', 'label' => 'Aset Rusak'],
            ['value' => 'aset_hilang', 'label' => 'Aset Hilang'],
            ['value' => 'orang_mencurigakan', 'label' => 'Orang Mencurigakan'],
            ['value' => 'kabel_terbuka', 'label' => 'Kabel Terbuka'],
            ['value' => 'pencurian', 'label' => 'Pencurian'],
            ['value' => 'sabotase', 'label' => 'Sabotase'],
            ['value' => 'demo', 'label' => 'Demo'],
        ];

        return response()->json([
            'success' => true,
            'data' => $jenisKendala
        ]);
    }
}