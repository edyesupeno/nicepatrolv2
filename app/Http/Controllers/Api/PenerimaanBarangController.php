<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenerimaanBarang;
use App\Models\Project;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Penerimaan Barang",
 *     description="API endpoints untuk manajemen penerimaan barang"
 * )
 */
class PenerimaanBarangController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/penerimaan-barang",
     *     summary="Get list penerimaan barang",
     *     description="Mendapatkan daftar penerimaan barang dengan pagination dan filter",
     *     operationId="getPenerimaanBarangList",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Jumlah data per halaman (max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="kategori",
     *         in="query",
     *         description="Filter berdasarkan kategori barang",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Dokumen", "Material", "Elektronik", "Logistik"})
     *     ),
     *     @OA\Parameter(
     *         name="kondisi",
     *         in="query",
     *         description="Filter berdasarkan kondisi barang",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Baik", "Rusak", "Segel Terbuka"})
     *     ),
     *     @OA\Parameter(
     *         name="project_id",
     *         in="query",
     *         description="Filter berdasarkan project ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Pencarian berdasarkan nama barang atau nomor penerimaan",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/PenerimaanBarang")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $perPage = min($request->get('per_page', 20), 100);
        
        $query = PenerimaanBarang::with(['project:id,nama', 'area:id,nama', 'createdBy:id,name'])
            ->select([
                'id',
                'created_by',
                'project_id',
                'area_id',
                'nomor_penerimaan',
                'nama_barang',
                'kategori_barang',
                'jumlah_barang',
                'satuan',
                'kondisi_barang',
                'pengirim',
                'tujuan_departemen',
                'tanggal_terima',
                'status',
                'petugas_penerima'
            ]);

        // Apply filters
        if ($request->filled('kategori')) {
            $query->byKategori($request->kategori);
        }

        if ($request->filled('kondisi')) {
            $query->byKondisi($request->kondisi);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'ILIKE', "%{$search}%")
                  ->orWhere('nomor_penerimaan', 'ILIKE', "%{$search}%")
                  ->orWhere('pengirim', 'ILIKE', "%{$search}%");
            });
        }

        $penerimaanBarangs = $query->orderBy('tanggal_terima', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $penerimaanBarangs
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/penerimaan-barang",
     *     summary="Create penerimaan barang baru",
     *     description="Membuat data penerimaan barang baru",
     *     operationId="createPenerimaanBarang",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nama_barang", "kategori_barang", "jumlah_barang", "satuan", "kondisi_barang", "pengirim", "tujuan_departemen", "tanggal_terima"},
     *                 @OA\Property(property="project_id", type="integer", description="ID Project", example=1),
     *                 @OA\Property(property="area_id", type="integer", description="ID Area penyimpanan", example=1),
     *                 @OA\Property(property="pos", type="string", description="Pos jaga security", example="Pos Jaga Utama"),
     *                 @OA\Property(property="nama_barang", type="string", description="Nama barang", example="Laptop Dell Inspiron"),
     *                 @OA\Property(property="kategori_barang", type="string", enum={"Dokumen", "Material", "Elektronik", "Logistik"}, example="Elektronik"),
     *                 @OA\Property(property="jumlah_barang", type="integer", description="Jumlah barang", example=2),
     *                 @OA\Property(property="satuan", type="string", description="Satuan barang", example="unit"),
     *                 @OA\Property(property="kondisi_barang", type="string", enum={"Baik", "Rusak", "Segel Terbuka"}, example="Baik"),
     *                 @OA\Property(property="pengirim", type="string", description="Nama pengirim", example="PT. Supplier ABC"),
     *                 @OA\Property(property="tujuan_departemen", type="string", description="Departemen tujuan", example="IT Department"),
     *                 @OA\Property(property="tanggal_terima", type="string", format="date-time", description="Tanggal dan waktu penerimaan (YYYY-MM-DD HH:MM:SS atau YYYY-MM-DD)", example="2026-01-20 14:30:00"),
     *                 @OA\Property(property="keterangan", type="string", description="Keterangan tambahan", example="Barang dalam kondisi baik"),
     *                 @OA\Property(property="foto_barang", type="string", format="binary", description="Foto barang (max 10MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Penerimaan barang berhasil dibuat"),
     *             @OA\Property(property="data", ref="#/components/schemas/PenerimaanBarang")
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'pos' => 'nullable|string|max:100',
            'nama_barang' => 'required|string|max:255',
            'kategori_barang' => 'required|in:Dokumen,Material,Elektronik,Logistik',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'kondisi_barang' => 'required|in:Baik,Rusak,Segel Terbuka',
            'pengirim' => 'required|string|max:255',
            'tujuan_departemen' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'keterangan' => 'nullable|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ]);

        // CRITICAL: Validasi area access untuk regular users (konsisten dengan karyawan_areas)
        if (!empty($validated['area_id']) && $user->karyawan && !$user->isSuperAdmin() && !$user->isAdmin()) {
            $userAreaIds = $user->karyawan->areas()->pluck('areas.id')->toArray();
            if (!in_array($validated['area_id'], $userAreaIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke area tersebut. Hanya area yang di-assign ke Anda yang bisa dipilih.',
                ], 403);
            }
        }

        // IMPROVEMENT: Set current time if only date is provided (untuk datetime yang lebih akurat)
        if (!empty($validated['tanggal_terima'])) {
            $tanggalTerima = \Carbon\Carbon::parse($validated['tanggal_terima']);
            
            // Jika hanya tanggal (00:00:00), set ke waktu sekarang
            if ($tanggalTerima->format('H:i:s') === '00:00:00') {
                $validated['tanggal_terima'] = now()->format('Y-m-d H:i:s');
            }
        }

        // Auto-assign perusahaan_id dan created_by (sesuai project standards)
        $validated['perusahaan_id'] = $user->perusahaan_id;
        $validated['created_by'] = $user->id; // CRITICAL: Track siapa yang membuat record
        
        // Auto-assign project_id jika tidak diisi dan user punya project
        if (empty($validated['project_id']) && !$user->isSuperAdmin()) {
            $activeProject = $user->getFirstAccessibleProject();
            if ($activeProject) {
                $validated['project_id'] = $activeProject->id;
            }
        }
        
        // Generate nomor penerimaan otomatis
        $validated['nomor_penerimaan'] = $this->generateNomorPenerimaan();
        
        // WAJIB: Auto-assign petugas_penerima dari user yang login (tidak bisa di-override)
        $validated['petugas_penerima'] = $user->name;
        
        // Set default status
        $validated['status'] = 'Diterima';

        // Handle foto upload
        if ($request->hasFile('foto_barang')) {
            try {
                $path = \App\Helpers\ImageHelper::compressAndSave(
                    $request->file('foto_barang'),
                    'penerimaan-barang/foto',
                    500, // max 500KB
                    1200, // max width 1200px
                    85   // quality 85%
                );
                $validated['foto_barang'] = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload foto: ' . $e->getMessage(),
                ], 500);
            }
        }

        $penerimaanBarang = PenerimaanBarang::create($validated);
        
        // Load relationships
        $penerimaanBarang->load(['project:id,nama', 'area:id,nama', 'createdBy:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Penerimaan barang berhasil dibuat',
            'data' => $penerimaanBarang
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/penerimaan-barang/{id}",
     *     summary="Get detail penerimaan barang",
     *     description="Mendapatkan detail penerimaan barang berdasarkan ID",
     *     operationId="getPenerimaanBarangDetail",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Hash ID penerimaan barang",
     *         required=true,
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil"),
     *             @OA\Property(property="data", ref="#/components/schemas/PenerimaanBarang")
     *         )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function show(PenerimaanBarang $penerimaanBarang)
    {
        $penerimaanBarang->load(['project:id,nama', 'area:id,nama,alamat', 'createdBy:id,name']);
        
        // Add foto URL if exists
        if ($penerimaanBarang->foto_barang) {
            $penerimaanBarang->foto_url = \App\Helpers\ImageHelper::url($penerimaanBarang->foto_barang);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $penerimaanBarang
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/penerimaan-barang/{id}",
     *     summary="Update penerimaan barang",
     *     description="Mengupdate data penerimaan barang (hanya yang dibuat oleh user sendiri, kecuali admin)",
     *     operationId="updatePenerimaanBarang",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Hash ID penerimaan barang",
     *         required=true,
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="_method", type="string", example="PUT", description="Method override untuk form-data"),
     *                 @OA\Property(property="project_id", type="integer", description="ID Project", example=1),
     *                 @OA\Property(property="area_id", type="integer", description="ID Area penyimpanan", example=1),
     *                 @OA\Property(property="pos", type="string", description="Pos jaga security", example="Pos Jaga Utama"),
     *                 @OA\Property(property="nama_barang", type="string", description="Nama barang", example="Laptop Dell Inspiron"),
     *                 @OA\Property(property="kategori_barang", type="string", enum={"Dokumen", "Material", "Elektronik", "Logistik"}, example="Elektronik"),
     *                 @OA\Property(property="jumlah_barang", type="integer", description="Jumlah barang", example=2),
     *                 @OA\Property(property="satuan", type="string", description="Satuan barang", example="unit"),
     *                 @OA\Property(property="kondisi_barang", type="string", enum={"Baik", "Rusak", "Segel Terbuka"}, example="Baik"),
     *                 @OA\Property(property="pengirim", type="string", description="Nama pengirim", example="PT. Supplier ABC"),
     *                 @OA\Property(property="tujuan_departemen", type="string", description="Departemen tujuan", example="IT Department"),
     *                 @OA\Property(property="tanggal_terima", type="string", format="date-time", description="Tanggal dan waktu penerimaan (YYYY-MM-DD HH:MM:SS atau YYYY-MM-DD)", example="2026-01-20 14:30:00"),
     *                 @OA\Property(property="keterangan", type="string", description="Keterangan tambahan", example="Barang dalam kondisi baik"),
     *                 @OA\Property(property="foto_barang", type="string", format="binary", description="Foto barang baru (max 10MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Penerimaan barang berhasil diupdate"),
     *             @OA\Property(property="data", ref="#/components/schemas/PenerimaanBarang")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Tidak memiliki akses untuk mengupdate data ini",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Anda tidak memiliki akses untuk mengupdate data ini")
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function update(Request $request, PenerimaanBarang $penerimaanBarang)
    {
        $user = auth()->user();
        
        // CRITICAL: Double check ownership untuk keamanan ekstra
        // Global scope sudah filter, tapi kita tambah validasi eksplisit
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            if ($penerimaanBarang->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengupdate data ini. Hanya data yang Anda input sendiri yang bisa diupdate.',
                ], 403);
            }
        }
        
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'pos' => 'nullable|string|max:100',
            'nama_barang' => 'required|string|max:255',
            'kategori_barang' => 'required|in:Dokumen,Material,Elektronik,Logistik',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'kondisi_barang' => 'required|in:Baik,Rusak,Segel Terbuka',
            'pengirim' => 'required|string|max:255',
            'tujuan_departemen' => 'required|string|max:255',
            'tanggal_terima' => 'required|date',
            'keterangan' => 'nullable|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ]);

        // CRITICAL: Validasi area access untuk regular users (konsisten dengan karyawan_areas)
        if (!empty($validated['area_id']) && $user->karyawan && !$user->isSuperAdmin() && !$user->isAdmin()) {
            $userAreaIds = $user->karyawan->areas()->pluck('areas.id')->toArray();
            if (!in_array($validated['area_id'], $userAreaIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke area tersebut. Hanya area yang di-assign ke Anda yang bisa dipilih.',
                ], 403);
            }
        }

        // IMPROVEMENT: Set current time if only date is provided (untuk datetime yang lebih akurat)
        if (!empty($validated['tanggal_terima'])) {
            $tanggalTerima = \Carbon\Carbon::parse($validated['tanggal_terima']);
            
            // Jika hanya tanggal (00:00:00), set ke waktu sekarang
            if ($tanggalTerima->format('H:i:s') === '00:00:00') {
                $validated['tanggal_terima'] = now()->format('Y-m-d H:i:s');
            }
        }

        // Handle foto upload
        if ($request->hasFile('foto_barang')) {
            try {
                // Delete old foto
                \App\Helpers\ImageHelper::delete($penerimaanBarang->foto_barang);
                
                // Upload new foto
                $path = \App\Helpers\ImageHelper::compressAndSave(
                    $request->file('foto_barang'),
                    'penerimaan-barang/foto',
                    500, // max 500KB
                    1200, // max width 1200px
                    85   // quality 85%
                );
                $validated['foto_barang'] = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload foto: ' . $e->getMessage(),
                ], 500);
            }
        }

        // WAJIB: Auto-assign petugas_penerima dari user yang login (tidak bisa di-override)
        $validated['petugas_penerima'] = $user->name;

        $penerimaanBarang->update($validated);
        
        // Load relationships
        $penerimaanBarang->load(['project:id,nama', 'area:id,nama', 'createdBy:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Penerimaan barang berhasil diupdate',
            'data' => $penerimaanBarang
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/penerimaan-barang/{id}",
     *     summary="Delete penerimaan barang",
     *     description="Menghapus data penerimaan barang (soft delete) - hanya yang dibuat oleh user sendiri, kecuali admin",
     *     operationId="deletePenerimaanBarang",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Hash ID penerimaan barang",
     *         required=true,
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Penerimaan barang berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Tidak memiliki akses untuk menghapus data ini",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Anda tidak memiliki akses untuk menghapus data ini")
     *         )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function destroy(PenerimaanBarang $penerimaanBarang)
    {
        $user = auth()->user();
        
        // CRITICAL: Double check ownership untuk keamanan ekstra
        // Global scope sudah filter, tapi kita tambah validasi eksplisit
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            if ($penerimaanBarang->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini. Hanya data yang Anda input sendiri yang bisa dihapus.',
                ], 403);
            }
        }
        
        // Delete foto if exists
        \App\Helpers\ImageHelper::delete($penerimaanBarang->foto_barang);
        
        $penerimaanBarang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penerimaan barang berhasil dihapus'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/penerimaan-barang-projects",
     *     summary="Get list projects untuk dropdown",
     *     description="Mendapatkan daftar project untuk dropdown penerimaan barang (otomatis ter-filter berdasarkan user access)",
     *     operationId="getPenerimaanBarangProjects",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="Kantor Jakarta")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function getProjects()
    {
        $user = auth()->user();
        
        // CRITICAL: Filter project berdasarkan akses user
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            // Admin bisa lihat semua project di perusahaan mereka
            $projects = Project::select('id', 'nama')
                ->orderBy('nama')
                ->get();
        } else {
            // User biasa hanya bisa lihat project yang mereka akses
            $projectIds = $user->getAccessibleProjectIds();
            $projects = Project::select('id', 'nama')
                ->whereIn('id', $projectIds)
                ->orderBy('nama')
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $projects
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/penerimaan-barang-areas/{project_id}",
     *     summary="Get list areas berdasarkan project",
     *     description="Mendapatkan daftar area berdasarkan project untuk dropdown (otomatis ter-filter berdasarkan user access)",
     *     operationId="getPenerimaanBarangAreas",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="ID Project",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="Gudang A"),
     *                     @OA\Property(property="alamat", type="string", example="Lantai 1")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Tidak memiliki akses ke project tersebut",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Anda tidak memiliki akses ke project tersebut")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function getAreasByProject($projectId)
    {
        $user = auth()->user();
        
        // CRITICAL: Validasi akses project untuk keamanan
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            $userProjectIds = $user->getAccessibleProjectIds();
            if (!in_array($projectId, $userProjectIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke project tersebut',
                ], 403);
            }
        }
        
        // KONSISTEN: Selalu prioritaskan karyawan_areas untuk regular users
        if ($user->karyawan && !$user->isSuperAdmin() && !$user->isAdmin()) {
            // Regular users: Hanya area yang di-assign via karyawan_areas
            $areas = $user->karyawan->areas()
                ->select('areas.id', 'areas.nama', 'areas.alamat', 'karyawan_areas.is_primary')
                ->where('areas.project_id', $projectId)
                ->orderByDesc('karyawan_areas.is_primary') // Primary area dulu
                ->orderBy('areas.nama')
                ->get();
                
            // Format response dengan primary flag
            $areas = $areas->map(function ($area) {
                return [
                    'id' => $area->id,
                    'nama' => $area->nama,
                    'alamat' => $area->alamat,
                    'is_primary' => (bool) $area->pivot->is_primary,
                ];
            });
            
            $source = 'karyawan_areas';
        } else {
            // Admin/Superadmin: Semua area di project
            $areas = Area::select('id', 'nama', 'alamat')
                ->where('project_id', $projectId)
                ->orderBy('nama')
                ->get()
                ->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'nama' => $area->nama,
                        'alamat' => $area->alamat,
                        'is_primary' => false, // Default false untuk admin
                    ];
                });
                
            $source = 'all_project_areas';
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $areas,
            'meta' => [
                'total_areas' => $areas->count(),
                'source' => $source,
                'user_role' => $user->role,
                'project_id' => (int) $projectId,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/penerimaan-barang-my-areas",
     *     summary="Get my assigned areas",
     *     description="Mendapatkan daftar area yang di-assign ke karyawan saat ini",
     *     operationId="getMyAreas",
     *     tags={"Penerimaan Barang"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="Gudang A"),
     *                     @OA\Property(property="alamat", type="string", example="Lantai 1"),
     *                     @OA\Property(property="is_primary", type="boolean", example=true),
     *                     @OA\Property(property="project_id", type="integer", example=1),
     *                     @OA\Property(property="project_name", type="string", example="Kantor Jakarta")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total_areas", type="integer", example=3),
     *                 @OA\Property(property="primary_area_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No karyawan data found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Data karyawan tidak ditemukan")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function getMyAreas()
    {
        $user = auth()->user();
        
        // Check if user has karyawan data
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan. Hanya karyawan yang memiliki area assignment.',
            ], 404);
        }
        
        // Get areas assigned to this karyawan
        $areas = $user->karyawan->areas()
            ->select('areas.id', 'areas.nama', 'areas.alamat', 'areas.project_id', 'karyawan_areas.is_primary')
            ->with('project:id,nama')
            ->orderByDesc('karyawan_areas.is_primary') // Primary area first
            ->orderBy('areas.nama')
            ->get();
            
        // Format response with additional info
        $formattedAreas = $areas->map(function ($area) {
            return [
                'id' => $area->id,
                'nama' => $area->nama,
                'alamat' => $area->alamat,
                'is_primary' => (bool) $area->pivot->is_primary,
                'project_id' => $area->project_id,
                'project_name' => $area->project ? $area->project->nama : null,
            ];
        });
        
        // Get primary area ID
        $primaryAreaId = $areas->where('pivot.is_primary', true)->first()?->id;

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $formattedAreas,
            'meta' => [
                'total_areas' => $areas->count(),
                'primary_area_id' => $primaryAreaId,
                'karyawan_id' => $user->karyawan->id,
                'karyawan_name' => $user->karyawan->nama_lengkap,
            ]
        ]);
    }

    /**
     * Generate nomor penerimaan otomatis
     */
    private function generateNomorPenerimaan()
    {
        $prefix = 'PB';
        $date = now()->format('Ymd');
        
        // Get last number for today with proper ordering
        $lastRecord = PenerimaanBarang::withoutGlobalScopes()
            ->whereDate('created_at', today())
            ->where('nomor_penerimaan', 'LIKE', "{$prefix}{$date}%")
            ->orderBy('nomor_penerimaan', 'desc')
            ->first();
        
        if ($lastRecord) {
            // Extract number from last record
            $lastNumber = (int) substr($lastRecord->nomor_penerimaan, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Try to create unique number, with retry mechanism
        $attempts = 0;
        do {
            $number = str_pad($nextNumber + $attempts, 3, '0', STR_PAD_LEFT);
            $nomorPenerimaan = "{$prefix}{$date}{$number}";
            
            // Check if this number already exists
            $exists = PenerimaanBarang::withoutGlobalScopes()
                ->where('nomor_penerimaan', $nomorPenerimaan)
                ->exists();
            
            if (!$exists) {
                return $nomorPenerimaan;
            }
            
            $attempts++;
        } while ($attempts < 100); // Max 100 attempts
        
        // Fallback with timestamp if all attempts failed
        return "{$prefix}{$date}" . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
    }
}