<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ManajemenGajiController extends Controller
{
    public function index(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get filters
        $projectId = $request->get('project_id');
        $jabatanId = $request->get('jabatan_id');
        $search = $request->get('search');
        
        // Query untuk statistics (tanpa pagination) - SEMUA data yang sesuai filter
        $statsQuery = Karyawan::select([
                'id',
                'perusahaan_id',
                'project_id',
                'jabatan_id',
                'gaji_pokok',
                'nik_karyawan',
                'nama_lengkap'
            ])
            ->where('perusahaan_id', $perusahaanId) // Explicit filter untuk memastikan
            ->where('is_active', true);
        
        // Apply same filters as main query
        if ($projectId) {
            $statsQuery->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $statsQuery->where('jabatan_id', $jabatanId);
        }
        
        if ($search) {
            $statsQuery->where(function($q) use ($search) {
                $q->where('nik_karyawan', 'ILIKE', "%{$search}%")
                  ->orWhere('nama_lengkap', 'ILIKE', "%{$search}%");
            });
        }
        
        // Get ALL data for statistics (no pagination)
        $statsData = $statsQuery->get();
        
        // Calculate statistics from ALL matching records
        $totalKaryawan = $statsData->count();
        $totalGajiPokok = $statsData->sum('gaji_pokok') ?: 0; // Ensure not null
        $rataRataGaji = $totalKaryawan > 0 ? round($totalGajiPokok / $totalKaryawan, 0) : 0;
        
        // Query karyawan dengan pagination (same filters as stats)
        $query = Karyawan::select([
                'id',
                'perusahaan_id',
                'project_id',
                'jabatan_id',
                'nik_karyawan',
                'nama_lengkap',
                'foto',
                'gaji_pokok',
                'is_active'
            ])
            ->with([
                'project:id,nama',
                'jabatan:id,nama'
            ])
            ->where('perusahaan_id', $perusahaanId) // Explicit filter untuk memastikan
            ->where('is_active', true);
        
        // Apply same filters as statistics query
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->where('jabatan_id', $jabatanId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nik_karyawan', 'ILIKE', "%{$search}%")
                  ->orWhere('nama_lengkap', 'ILIKE', "%{$search}%");
            });
        }
        
        // Pagination: 50 data per halaman
        $karyawans = $query->orderBy('project_id')->orderBy('nama_lengkap')->paginate(50);
        
        // Group by project
        $groupedKaryawans = $karyawans->groupBy('project_id');
        
        // Get projects and jabatans for filters (clear cache if needed)
        $cacheKey = 'projects_' . $perusahaanId;
        $projects = Cache::remember($cacheKey, 3600, function () use ($perusahaanId) {
            return Project::select('id', 'nama')
                ->where('perusahaan_id', $perusahaanId)
                ->orderBy('nama')
                ->get();
        });
        
        $cacheKey = 'jabatans_' . $perusahaanId;
        $jabatans = Cache::remember($cacheKey, 3600, function () use ($perusahaanId) {
            return Jabatan::select('id', 'nama')
                ->where('perusahaan_id', $perusahaanId)
                ->orderBy('nama')
                ->get();
        });
        
        return view('perusahaan.manajemen-gaji.index', compact(
            'groupedKaryawans',
            'karyawans',
            'projects',
            'jabatans',
            'projectId',
            'jabatanId',
            'search',
            'totalKaryawan',
            'totalGajiPokok',
            'rataRataGaji'
        ));
    }
    
    /**
     * Debug method to check statistics calculation
     */
    public function debugStats(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }
        
        $perusahaanId = auth()->user()->perusahaan_id;
        $projectId = $request->get('project_id');
        
        // Query without pagination
        $allQuery = Karyawan::select(['id', 'project_id', 'gaji_pokok', 'nama_lengkap'])
            ->where('perusahaan_id', $perusahaanId)
            ->where('is_active', true);
            
        if ($projectId) {
            $allQuery->where('project_id', $projectId);
        }
        
        $allData = $allQuery->get();
        
        // Query with pagination
        $paginatedData = Karyawan::select(['id', 'project_id', 'gaji_pokok', 'nama_lengkap'])
            ->where('perusahaan_id', $perusahaanId)
            ->where('is_active', true)
            ->when($projectId, function($q) use ($projectId) {
                return $q->where('project_id', $projectId);
            })
            ->paginate(50);
        
        return response()->json([
            'perusahaan_id' => $perusahaanId,
            'project_id' => $projectId,
            'all_count' => $allData->count(),
            'all_total_gaji' => $allData->sum('gaji_pokok'),
            'paginated_count' => $paginatedData->count(),
            'paginated_total_gaji' => $paginatedData->sum('gaji_pokok'),
            'paginated_info' => [
                'current_page' => $paginatedData->currentPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
            ],
            'sample_data' => $allData->take(5)->toArray(),
        ]);
    }
    
    public function updateGajiPokok(Request $request, Karyawan $karyawan)
    {
        try {
            $validated = $request->validate([
                'gaji_pokok' => 'required|numeric|min:0',
            ], [
                'gaji_pokok.required' => 'Gaji pokok wajib diisi',
                'gaji_pokok.numeric' => 'Gaji pokok harus berupa angka',
                'gaji_pokok.min' => 'Gaji pokok tidak boleh negatif',
            ]);
            
            $karyawan->update([
                'gaji_pokok' => $validated['gaji_pokok'],
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gaji pokok berhasil diupdate',
                'gaji_pokok' => $karyawan->gaji_pokok,
                'gaji_pokok_formatted' => 'Rp ' . number_format($karyawan->gaji_pokok, 0, ',', '.'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update gaji pokok: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function updateMassal(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'nullable|exists:projects,id',
                'jabatan_id' => 'nullable|exists:jabatans,id',
                'gaji_pokok' => 'required|numeric|min:0',
            ], [
                'gaji_pokok.required' => 'Gaji pokok wajib diisi',
                'gaji_pokok.numeric' => 'Gaji pokok harus berupa angka',
                'gaji_pokok.min' => 'Gaji pokok tidak boleh negatif',
            ]);
            
            $count = 0;
            
            // Gunakan transaction dan lock untuk mencegah race condition
            DB::transaction(function () use ($validated, &$count) {
                $query = Karyawan::where('is_active', true);
                
                if (!empty($validated['project_id'])) {
                    $query->where('project_id', $validated['project_id']);
                }
                
                if (!empty($validated['jabatan_id'])) {
                    $query->where('jabatan_id', $validated['jabatan_id']);
                }
                
                // Lock rows untuk mencegah concurrent update
                $karyawans = $query->lockForUpdate()->get(['id', 'gaji_pokok']);
                $count = $karyawans->count();
                
                // Update dengan atomic operation
                if ($count > 0) {
                    Karyawan::whereIn('id', $karyawans->pluck('id'))
                        ->update([
                            'gaji_pokok' => $validated['gaji_pokok'],
                            'updated_at' => now()
                        ]);
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil update gaji pokok untuk {$count} karyawan",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update gaji pokok: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Clear cache for projects and jabatans
     */
    public function clearCache()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        Cache::forget('projects_' . $perusahaanId);
        Cache::forget('jabatans_' . $perusahaanId);
        
        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }
}
