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
        
        // Query untuk statistics (tanpa pagination)
        $statsQuery = Karyawan::select([
                'id',
                'project_id',
                'gaji_pokok'
            ])
            ->where('is_active', true);
        
        if ($projectId) {
            $statsQuery->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $statsQuery->where('jabatan_id', $jabatanId);
        }
        
        if ($search) {
            $statsQuery->where(function($q) use ($search) {
                $q->where('nik_karyawan', 'ilike', "%{$search}%")
                  ->orWhere('nama_lengkap', 'ilike', "%{$search}%");
            });
        }
        
        $statsData = $statsQuery->get();
        
        // Calculate statistics
        $totalKaryawan = $statsData->count();
        $totalGajiPokok = $statsData->sum('gaji_pokok');
        $rataRataGaji = $totalKaryawan > 0 ? $totalGajiPokok / $totalKaryawan : 0;
        
        // Query karyawan dengan pagination
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
            ->where('is_active', true);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->where('jabatan_id', $jabatanId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nik_karyawan', 'ilike', "%{$search}%")
                  ->orWhere('nama_lengkap', 'ilike', "%{$search}%");
            });
        }
        
        // Pagination: 50 data per halaman
        $karyawans = $query->orderBy('project_id')->orderBy('nama_lengkap')->paginate(50);
        
        // Group by project
        $groupedKaryawans = $karyawans->groupBy('project_id');
        
        // Get projects and jabatans for filters (dengan cache)
        $projects = Cache::remember('projects_' . $perusahaanId, 3600, function () {
            return Project::select('id', 'nama')->orderBy('nama')->get();
        });
        
        $jabatans = Cache::remember('jabatans_' . $perusahaanId, 3600, function () {
            return Jabatan::select('id', 'nama')->orderBy('nama')->get();
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
}
