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
        
        $query = PenerimaanBarang::with(['project:id,nama', 'area:id,nama'])
            ->select([
                'id',
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
     *                 @OA\Property(property="pos", type="string", description="Point of Storage", example="A1-B2-C3"),
     *                 @OA\Property(property="nama_barang", type="string", description="Nama barang", example="Laptop Dell Inspiron"),
     *                 @OA\Property(property="kategori_barang", type="string", enum={"Dokumen", "Material", "Elektronik", "Logistik"}, example="Elektronik"),
     *                 @OA\Property(property="jumlah_barang", type="integer", description="Jumlah barang", example=2),
     *                 @OA\Property(property="satuan", type="string", description="Satuan barang", example="unit"),
     *                 @OA\Property(property="kondisi_barang", type="string", enum={"Baik", "Rusak", "Segel Terbuka"}, example="Baik"),
     *                 @OA\Property(property="pengirim", type="string", description="Nama pengirim", example="PT. Supplier ABC"),
     *                 @OA\Property(property="tujuan_departemen", type="string", description="Departemen tujuan", example="IT Department"),
     *                 @OA\Property(property="tanggal_terima", type="string", format="date", description="Tanggal penerimaan", example="2026-01-20"),
     *                 @OA\Property(property="petugas_penerima", type="string", description="Nama petugas penerima", example="John Doe"),
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
            'petugas_penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ]);

        // Auto-assign perusahaan_id (sesuai project standards)
        $validated['perusahaan_id'] = $user->perusahaan_id;
        
        // Auto-assign project_id jika tidak diisi dan user punya project
        if (empty($validated['project_id']) && !$user->isSuperAdmin()) {
            if ($user->karyawan && $user->karyawan->project_id) {
                $validated['project_id'] = $user->karyawan->project_id;
            }
        }
        
        // Generate nomor penerimaan otomatis
        $validated['nomor_penerimaan'] = $this->generateNomorPenerimaan();
        
        // Set default petugas_penerima jika tidak diisi
        if (empty($validated['petugas_penerima'])) {
            $validated['petugas_penerima'] = $user->name;
        }
        
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
        $penerimaanBarang->load(['project:id,nama', 'area:id,nama']);

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
        $penerimaanBarang->load(['project:id,nama', 'area:id,nama,alamat']);
        
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
     *     description="Mengupdate data penerimaan barang",
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
     *                 @OA\Property(property="pos", type="string", description="Point of Storage", example="A1-B2-C3"),
     *                 @OA\Property(property="nama_barang", type="string", description="Nama barang", example="Laptop Dell Inspiron"),
     *                 @OA\Property(property="kategori_barang", type="string", enum={"Dokumen", "Material", "Elektronik", "Logistik"}, example="Elektronik"),
     *                 @OA\Property(property="jumlah_barang", type="integer", description="Jumlah barang", example=2),
     *                 @OA\Property(property="satuan", type="string", description="Satuan barang", example="unit"),
     *                 @OA\Property(property="kondisi_barang", type="string", enum={"Baik", "Rusak", "Segel Terbuka"}, example="Baik"),
     *                 @OA\Property(property="pengirim", type="string", description="Nama pengirim", example="PT. Supplier ABC"),
     *                 @OA\Property(property="tujuan_departemen", type="string", description="Departemen tujuan", example="IT Department"),
     *                 @OA\Property(property="tanggal_terima", type="string", format="date", description="Tanggal penerimaan", example="2026-01-20"),
     *                 @OA\Property(property="petugas_penerima", type="string", description="Nama petugas penerima", example="John Doe"),
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
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function update(Request $request, PenerimaanBarang $penerimaanBarang)
    {
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
            'petugas_penerima' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ]);

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

        $penerimaanBarang->update($validated);
        
        // Load relationships
        $penerimaanBarang->load(['project:id,nama', 'area:id,nama']);

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
     *     description="Menghapus data penerimaan barang (soft delete)",
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
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function destroy(PenerimaanBarang $penerimaanBarang)
    {
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
     *     path="/api/v1/penerimaan-barang/projects",
     *     summary="Get list projects untuk dropdown",
     *     description="Mendapatkan daftar project untuk dropdown penerimaan barang (otomatis ter-filter berdasarkan user)",
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
        // CRITICAL: Global scope otomatis filter berdasarkan perusahaan_id dan project_id user
        $projects = Project::select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $projects
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/penerimaan-barang/areas/{project_id}",
     *     summary="Get list areas berdasarkan project",
     *     description="Mendapatkan daftar area berdasarkan project untuk dropdown (otomatis ter-filter berdasarkan user)",
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
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function getAreasByProject($projectId)
    {
        $user = auth()->user();
        
        // CRITICAL: Validasi project_id untuk multi-tenancy
        if (!$user->isSuperAdmin()) {
            // Pastikan project_id yang diminta adalah project user
            if ($user->karyawan && $user->karyawan->project_id) {
                if ($projectId != $user->karyawan->project_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke project tersebut',
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke project manapun',
                ], 403);
            }
        }
        
        // CRITICAL: Global scope otomatis filter berdasarkan perusahaan_id dan project_id user
        $areas = Area::select('id', 'nama', 'alamat')
            ->where('project_id', $projectId)
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $areas
        ]);
    }

    /**
     * Generate nomor penerimaan otomatis
     */
    private function generateNomorPenerimaan()
    {
        $prefix = 'PB';
        $date = now()->format('Ymd');
        
        // Get last number for today
        $lastNumber = PenerimaanBarang::whereDate('created_at', today())
            ->where('nomor_penerimaan', 'LIKE', "{$prefix}{$date}%")
            ->count();
        
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$date}{$nextNumber}";
    }
}