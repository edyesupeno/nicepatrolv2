<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\TimPatroli;
use App\Models\Project;
use App\Models\User;
use App\Models\AreaPatrol;
use App\Models\RutePatrol;
use App\Models\Checkpoint;
use App\Models\InventarisPatroli;
use App\Models\KuesionerPatroli;
use App\Models\PemeriksaanPatroli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimPatroliController extends Controller
{
    public function master(Request $request)
    {
        $query = TimPatroli::select([
                'id',
                'perusahaan_id',
                'project_id',
                'nama_tim',
                'shift_id',
                'leader_id',
                'is_active',
                'created_at'
            ])
            ->with([
                'project:id,nama', 
                'leader:id,name',
                'shift:id,nama_shift,jam_mulai,jam_selesai',
                'areas:id,nama',
                'rutes:id,nama',
                'checkpoints:id,nama',
                'inventaris:id,nama',
                'kuesioners:id,judul',
                'pemeriksaans:id,nama'
            ])
            ->withCount(['areas', 'rutes', 'checkpoints', 'inventaris', 'kuesioners', 'pemeriksaans']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_tim', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by shift
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $timPatrolis = $query->latest()->paginate(15)->withQueryString();
        
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        // Get shifts for filter dropdown
        $shifts = \App\Models\Shift::select('id', 'nama_shift')
            ->orderBy('nama_shift')
            ->get();

        return view('perusahaan.tim-patroli.master', compact('timPatrolis', 'projects', 'shifts'));
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $users = User::select('id', 'name', 'email')
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('perusahaan.tim-patroli.create', compact('projects', 'users'));
    }

    public function getDataByProject(Request $request, $projectId)
    {
        try {
            // Get current user's perusahaan_id
            $perusahaanId = auth()->user()->perusahaan_id ?? null;
            
            if (!$perusahaanId) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'User tidak memiliki perusahaan yang valid',
                    'areas' => [],
                    'rutes' => [],
                    'checkpoints' => [],
                    'shifts' => [],
                    'inventaris' => [],
                    'kuesioners' => [],
                    'pemeriksaans' => [],
                ], 401);
            }

            // Validate project belongs to user's perusahaan
            $project = Project::where('id', $projectId)
                ->where('perusahaan_id', $perusahaanId)
                ->first();
                
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Project tidak ditemukan atau tidak memiliki akses',
                    'areas' => [],
                    'rutes' => [],
                    'checkpoints' => [],
                    'shifts' => [],
                    'inventaris' => [],
                    'kuesioners' => [],
                    'pemeriksaans' => [],
                ], 404);
            }

            $areas = AreaPatrol::select('id', 'nama')
                ->where('project_id', $projectId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            // Don't load rutes and checkpoints initially - they will be loaded when areas are selected
            $rutes = [];
            $checkpoints = [];

            // Get shifts for the selected project
            $shifts = \App\Models\Shift::select('id', 'kode_shift', 'nama_shift', 'jam_mulai', 'jam_selesai')
                ->where('project_id', $projectId)
                ->orderBy('nama_shift')
                ->get();

            $inventaris = InventarisPatroli::select('id', 'nama', 'kategori')
                ->where('perusahaan_id', $perusahaanId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            $kuesioners = KuesionerPatroli::select('id', 'judul')
                ->where('perusahaan_id', $perusahaanId)
                ->where('is_active', true)
                ->orderBy('judul')
                ->get();

            $pemeriksaans = PemeriksaanPatroli::select('id', 'nama', 'frekuensi')
                ->where('perusahaan_id', $perusahaanId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'areas' => $areas,
                'rutes' => $rutes,
                'checkpoints' => $checkpoints,
                'shifts' => $shifts,
                'inventaris' => $inventaris,
                'kuesioners' => $kuesioners,
                'pemeriksaans' => $pemeriksaans,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getDataByProject: ' . $e->getMessage(), [
                'project_id' => $projectId,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'areas' => [],
                'rutes' => [],
                'checkpoints' => [],
                'shifts' => [],
                'inventaris' => [],
                'kuesioners' => [],
                'pemeriksaans' => [],
            ], 500);
        }
    }

    public function getRutesByAreas(Request $request)
    {
        try {
            $areaIds = $request->input('area_ids', []);
            $perusahaanId = auth()->user()->perusahaan_id ?? null;
            
            if (!$perusahaanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki perusahaan yang valid',
                    'rutes' => [],
                ], 401);
            }

            if (empty($areaIds)) {
                return response()->json([
                    'success' => true,
                    'rutes' => [],
                ]);
            }

            $rutes = RutePatrol::withoutGlobalScope('perusahaan')
                ->select('rute_patrols.id', 'rute_patrols.nama', 'rute_patrols.area_patrol_id')
                ->whereIn('area_patrol_id', $areaIds)
                ->where('rute_patrols.is_active', true)
                ->where('rute_patrols.perusahaan_id', $perusahaanId)
                ->orderBy('rute_patrols.nama')
                ->get();

            return response()->json([
                'success' => true,
                'rutes' => $rutes,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRutesByAreas: ' . $e->getMessage(), [
                'area_ids' => $request->input('area_ids', []),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data rute: ' . $e->getMessage(),
                'rutes' => [],
            ], 500);
        }
    }

    public function getCheckpointsByRutes(Request $request)
    {
        try {
            $ruteIds = $request->input('rute_ids', []);
            $perusahaanId = auth()->user()->perusahaan_id ?? null;
            
            if (!$perusahaanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki perusahaan yang valid',
                    'checkpoints' => [],
                ], 401);
            }

            if (empty($ruteIds)) {
                return response()->json([
                    'success' => true,
                    'checkpoints' => [],
                ]);
            }

            $checkpoints = Checkpoint::withoutGlobalScope('perusahaan')
                ->select('checkpoints.id', 'checkpoints.nama', 'checkpoints.rute_patrol_id', 'rute_patrols.nama as rute_nama')
                ->join('rute_patrols', 'checkpoints.rute_patrol_id', '=', 'rute_patrols.id')
                ->whereIn('checkpoints.rute_patrol_id', $ruteIds)
                ->where('checkpoints.is_active', true)
                ->where('checkpoints.perusahaan_id', $perusahaanId)
                ->orderBy('rute_patrols.nama')
                ->orderBy('checkpoints.urutan')
                ->get();

            return response()->json([
                'success' => true,
                'checkpoints' => $checkpoints,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCheckpointsByRutes: ' . $e->getMessage(), [
                'rute_ids' => $request->input('rute_ids', []),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data checkpoint: ' . $e->getMessage(),
                'checkpoints' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_tim' => 'required|string|max:255',
            'shift_id' => 'required|exists:shifts,id',
            'leader_id' => 'nullable|exists:users,id',
            'areas' => 'nullable|array',
            'areas.*' => 'exists:area_patrols,id',
            'rutes' => 'nullable|array',
            'rutes.*' => 'exists:rute_patrols,id',
            'checkpoints' => 'nullable|array',
            'checkpoints.*' => 'exists:checkpoints,id',
            'inventaris' => 'nullable|array',
            'inventaris.*' => 'exists:inventaris_patrolis,id',
            'kuesioners' => 'nullable|array',
            'kuesioners.*' => 'exists:kuesioner_patrolis,id',
            'pemeriksaans' => 'nullable|array',
            'pemeriksaans.*' => 'exists:pemeriksaan_patrolis,id',
            'is_active' => 'required|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'nama_tim.required' => 'Nama tim wajib diisi',
            'shift_id.required' => 'Shift wajib dipilih',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        DB::transaction(function () use ($validated) {
            $timPatroli = TimPatroli::create([
                'perusahaan_id' => $validated['perusahaan_id'],
                'project_id' => $validated['project_id'],
                'nama_tim' => $validated['nama_tim'],
                'shift_id' => $validated['shift_id'],
                'leader_id' => $validated['leader_id'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            // Sync relationships
            if (!empty($validated['areas'])) {
                $timPatroli->areas()->sync($validated['areas']);
            }
            if (!empty($validated['rutes'])) {
                $timPatroli->rutes()->sync($validated['rutes']);
            }
            if (!empty($validated['checkpoints'])) {
                // Sync checkpoints with urutan (order)
                $checkpointData = [];
                foreach ($validated['checkpoints'] as $index => $checkpointId) {
                    $checkpointData[$checkpointId] = ['urutan' => $index + 1];
                }
                $timPatroli->checkpoints()->sync($checkpointData);
            }
            if (!empty($validated['inventaris'])) {
                $timPatroli->inventaris()->sync($validated['inventaris']);
            }
            if (!empty($validated['kuesioners'])) {
                $timPatroli->kuesioners()->sync($validated['kuesioners']);
            }
            if (!empty($validated['pemeriksaans'])) {
                $timPatroli->pemeriksaans()->sync($validated['pemeriksaans']);
            }
        });

        return redirect()->route('perusahaan.tim-patroli.master')
            ->with('success', 'Tim patroli berhasil ditambahkan');
    }

    public function edit(TimPatroli $timPatroli)
    {
        $timPatroli->load(['areas', 'rutes', 'checkpoints', 'inventaris', 'kuesioners', 'pemeriksaans']);
        
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $users = User::select('id', 'name', 'email')
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get shifts for the selected project
        $shifts = \App\Models\Shift::select('id', 'nama_shift', 'jam_mulai', 'jam_selesai')
            ->where('project_id', $timPatroli->project_id)
            ->orderBy('nama_shift')
            ->get();

        // Get data based on selected project
        $areas = AreaPatrol::select('id', 'nama')
            ->where('project_id', $timPatroli->project_id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $rutes = RutePatrol::withoutGlobalScope('perusahaan')
            ->select('rute_patrols.id', 'rute_patrols.nama')
            ->join('area_patrols', 'rute_patrols.area_patrol_id', '=', 'area_patrols.id')
            ->where('area_patrols.project_id', $timPatroli->project_id)
            ->where('rute_patrols.is_active', true)
            ->where('rute_patrols.perusahaan_id', auth()->user()->perusahaan_id)
            ->orderBy('rute_patrols.nama')
            ->get();

        $checkpoints = Checkpoint::withoutGlobalScope('perusahaan')
            ->select('checkpoints.id', 'checkpoints.nama', 'checkpoints.rute_patrol_id', 'rute_patrols.nama as rute_nama')
            ->join('rute_patrols', 'checkpoints.rute_patrol_id', '=', 'rute_patrols.id')
            ->join('area_patrols', 'rute_patrols.area_patrol_id', '=', 'area_patrols.id')
            ->where('area_patrols.project_id', $timPatroli->project_id)
            ->where('checkpoints.perusahaan_id', auth()->user()->perusahaan_id)
            ->orderBy('rute_patrols.nama')
            ->orderBy('checkpoints.urutan')
            ->get();

        $inventaris = InventarisPatroli::select('id', 'nama', 'kategori')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $kuesioners = KuesionerPatroli::select('id', 'judul')
            ->where('is_active', true)
            ->orderBy('judul')
            ->get();

        $pemeriksaans = PemeriksaanPatroli::select('id', 'nama', 'frekuensi')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.tim-patroli.edit', compact(
            'timPatroli', 
            'projects', 
            'users', 
            'shifts',
            'areas', 
            'rutes', 
            'checkpoints',
            'inventaris', 
            'kuesioners', 
            'pemeriksaans'
        ));
    }

    public function update(Request $request, TimPatroli $timPatroli)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_tim' => 'required|string|max:255',
            'shift_id' => 'required|exists:shifts,id',
            'leader_id' => 'nullable|exists:users,id',
            'areas' => 'nullable|array',
            'areas.*' => 'exists:area_patrols,id',
            'rutes' => 'nullable|array',
            'rutes.*' => 'exists:rute_patrols,id',
            'checkpoints' => 'nullable|array',
            'checkpoints.*' => 'exists:checkpoints,id',
            'inventaris' => 'nullable|array',
            'inventaris.*' => 'exists:inventaris_patrolis,id',
            'kuesioners' => 'nullable|array',
            'kuesioners.*' => 'exists:kuesioner_patrolis,id',
            'pemeriksaans' => 'nullable|array',
            'pemeriksaans.*' => 'exists:pemeriksaan_patrolis,id',
            'is_active' => 'required|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'nama_tim.required' => 'Nama tim wajib diisi',
            'shift_id.required' => 'Shift wajib dipilih',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        DB::transaction(function () use ($validated, $timPatroli) {
            $timPatroli->update([
                'project_id' => $validated['project_id'],
                'nama_tim' => $validated['nama_tim'],
                'shift_id' => $validated['shift_id'],
                'leader_id' => $validated['leader_id'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            // Sync relationships
            $timPatroli->areas()->sync($validated['areas'] ?? []);
            $timPatroli->rutes()->sync($validated['rutes'] ?? []);
            
            // Sync checkpoints with urutan
            if (!empty($validated['checkpoints'])) {
                $checkpointData = [];
                foreach ($validated['checkpoints'] as $index => $checkpointId) {
                    $checkpointData[$checkpointId] = ['urutan' => $index + 1];
                }
                $timPatroli->checkpoints()->sync($checkpointData);
            } else {
                $timPatroli->checkpoints()->sync([]);
            }
            
            $timPatroli->inventaris()->sync($validated['inventaris'] ?? []);
            $timPatroli->kuesioners()->sync($validated['kuesioners'] ?? []);
            $timPatroli->pemeriksaans()->sync($validated['pemeriksaans'] ?? []);
        });

        return redirect()->route('perusahaan.tim-patroli.master')
            ->with('success', 'Tim patroli berhasil diupdate');
    }

    public function destroy(TimPatroli $timPatroli)
    {
        DB::transaction(function () use ($timPatroli) {
            $timPatroli->areas()->detach();
            $timPatroli->rutes()->detach();
            $timPatroli->checkpoints()->detach();
            $timPatroli->inventaris()->detach();
            $timPatroli->kuesioners()->detach();
            $timPatroli->pemeriksaans()->detach();
            $timPatroli->delete();
        });

        return redirect()->route('perusahaan.tim-patroli.master')
            ->with('success', 'Tim patroli berhasil dihapus');
    }

    public function inventaris()
    {
        return view('perusahaan.tim-patroli.inventaris');
    }
}
