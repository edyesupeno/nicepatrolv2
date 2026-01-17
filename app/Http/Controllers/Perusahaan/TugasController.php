<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasAssignment;
use App\Models\Project;
use App\Models\Area;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Tugas::with(['project', 'area', 'creator'])
            ->withCount(['assignments', 'completedAssignments', 'inProgressAssignments']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%")
                  ->orWhere('detail_lokasi', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by prioritas
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
                case 'due_soon':
                    $query->dueSoon();
                    break;
                case 'urgent':
                    $query->urgent();
                    break;
                default:
                    $query->where('status', $request->status);
                    break;
            }
        }

        $tugas = $query->orderBy('is_urgent', 'desc')
                      ->orderBy('batas_pengerjaan', 'asc')
                      ->orderBy('prioritas', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        $projects = Project::where('is_active', true)->get();

        return view('perusahaan.tugas.index', compact('tugas', 'projects'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        $areas = Area::all();
        $jabatans = Jabatan::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('perusahaan.tugas.create', compact('projects', 'areas', 'jabatans', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:low,medium,high',
            'batas_pengerjaan' => 'required|date|after_or_equal:today',
            'detail_lokasi' => 'nullable|string',
            'target_type' => 'required|in:all,area,jabatan,specific_users',
            'target_data' => 'nullable|array',
            'is_urgent' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'judul.required' => 'Judul tugas wajib diisi',
            'deskripsi.required' => 'Deskripsi tugas wajib diisi',
            'prioritas.required' => 'Prioritas wajib dipilih',
            'batas_pengerjaan.required' => 'Batas pengerjaan wajib diisi',
            'batas_pengerjaan.after_or_equal' => 'Batas pengerjaan tidak boleh kurang dari hari ini',
            'target_type.required' => 'Target penugasan wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['created_by'] = auth()->id();
        $validated['is_urgent'] = $request->input('is_urgent') == '1';
        $validated['is_active'] = $request->input('is_active', '1') == '1';
        $validated['status'] = 'active';
        $validated['published_at'] = now();

        $tugas = Tugas::create($validated);

        // Create assignments based on target type
        $this->createAssignments($tugas);

        // Clear cache for this tugas
        $this->clearTugasStatsCache($tugas->id);

        return redirect()->route('perusahaan.tugas.index')
            ->with('success', 'Tugas berhasil dibuat dan ditugaskan');
    }

    public function show(Tugas $tugas)
    {
        // Load only essential relationships, not assignments (will be lazy loaded)
        $tugas->load(['project', 'area', 'creator']);
        
        // Get assignment counts for statistics (optimized query with caching)
        $cacheKey = "tugas_stats_{$tugas->id}";
        $assignmentStats = Cache::remember($cacheKey, 300, function () use ($tugas) { // Cache for 5 minutes
            return [
                'total' => $tugas->assignments()->count(),
                'completed' => $tugas->assignments()->where('status', 'completed')->count(),
                'in_progress' => $tugas->assignments()->where('status', 'in_progress')->count(),
                'assigned' => $tugas->assignments()->where('status', 'assigned')->count(),
                'rejected' => $tugas->assignments()->where('status', 'rejected')->count(),
            ];
        });
        
        return view('perusahaan.tugas.show', compact('tugas', 'assignmentStats'));
    }

    public function edit(Tugas $tugas)
    {
        $projects = Project::where('is_active', true)->get();
        $areas = Area::all();
        $jabatans = Jabatan::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('perusahaan.tugas.edit', compact('tugas', 'projects', 'areas', 'jabatans', 'users'));
    }

    public function update(Request $request, Tugas $tugas)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:low,medium,high',
            'batas_pengerjaan' => 'required|date',
            'detail_lokasi' => 'nullable|string',
            'target_type' => 'required|in:all,area,jabatan,specific_users',
            'target_data' => 'nullable|array',
            'is_urgent' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|in:draft,active,completed,cancelled',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'judul.required' => 'Judul tugas wajib diisi',
            'deskripsi.required' => 'Deskripsi tugas wajib diisi',
            'prioritas.required' => 'Prioritas wajib dipilih',
            'batas_pengerjaan.required' => 'Batas pengerjaan wajib diisi',
            'target_type.required' => 'Target penugasan wajib dipilih',
        ]);

        $validated['is_urgent'] = $request->input('is_urgent') == '1';
        $validated['is_active'] = $request->input('is_active', '1') == '1';
        $validated['status'] = $request->input('status', $tugas->status);

        $tugas->update($validated);

        // Recreate assignments if target changed
        if ($tugas->wasChanged(['target_type', 'target_data', 'project_id', 'area_id'])) {
            $tugas->assignments()->delete();
            $this->createAssignments($tugas);
            $this->clearTugasStatsCache($tugas->id);
        }

        return redirect()->route('perusahaan.tugas.index')
            ->with('success', 'Tugas berhasil diupdate');
    }

    public function destroy(Tugas $tugas)
    {
        $this->clearTugasStatsCache($tugas->id);
        $tugas->delete();

        return redirect()->route('perusahaan.tugas.index')
            ->with('success', 'Tugas berhasil dihapus');
    }

    /**
     * Get assignments with pagination for lazy loading
     */
    public function getAssignments(Request $request, Tugas $tugas)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        
        $query = $tugas->assignments()
            ->with(['user:id,name,email'])
            ->select(['id', 'tugas_id', 'user_id', 'status', 'progress_percentage', 'started_at', 'completed_at', 'notes', 'created_at']);
        
        // Search by user name or email
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $assignments = $query->orderBy('status', 'asc')
                            ->orderBy('completed_at', 'desc')
                            ->orderBy('started_at', 'desc')
                            ->orderBy('created_at', 'asc')
                            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $assignments->items(),
            'pagination' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'per_page' => $assignments->perPage(),
                'total' => $assignments->total(),
                'from' => $assignments->firstItem(),
                'to' => $assignments->lastItem(),
            ]
        ]);
    }

    /**
     * Get areas by project
     */
    public function getAreasByProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $areas = $project->areas()->get();
        
        return response()->json($areas);
    }

    /**
     * Clear cache for tugas statistics
     */
    private function clearTugasStatsCache($tugasId)
    {
        Cache::forget("tugas_stats_{$tugasId}");
    }

    /**
     * Create assignments based on target type
     */
    private function createAssignments(Tugas $tugas)
    {
        $userIds = [];

        switch ($tugas->target_type) {
            case 'all':
                // Get all users in the project
                $userIds = $this->getAllProjectUsers($tugas->project_id);
                break;
                
            case 'area':
                // Get users in specific area
                if ($tugas->area_id) {
                    $userIds = $this->getUsersByArea($tugas->project_id, $tugas->area_id);
                }
                break;
                
            case 'jabatan':
                // Get users by jabatan
                if ($tugas->target_data && isset($tugas->target_data['jabatan_ids'])) {
                    $userIds = $this->getUsersByJabatan($tugas->project_id, $tugas->target_data['jabatan_ids']);
                }
                break;
                
            case 'specific_users':
                // Get specific users
                if ($tugas->target_data && isset($tugas->target_data['user_ids'])) {
                    $userIds = $tugas->target_data['user_ids'];
                }
                break;
        }

        // Create assignment records
        foreach (array_unique($userIds) as $userId) {
            TugasAssignment::create([
                'tugas_id' => $tugas->id,
                'user_id' => $userId,
                'status' => 'assigned',
            ]);
        }
    }

    private function getAllProjectUsers($projectId)
    {
        $userIds = [];
        
        // From project_user pivot
        $pivotUsers = \DB::table('project_user')
            ->where('project_id', $projectId)
            ->where('is_active', true)
            ->pluck('user_id')
            ->toArray();
            
        // From karyawans table
        $karyawanUsers = \App\Models\Karyawan::where('project_id', $projectId)
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();
            
        return array_merge($userIds, $pivotUsers, $karyawanUsers);
    }

    private function getUsersByArea($projectId, $areaId)
    {
        // This depends on how areas relate to users
        // For now, return all project users
        return $this->getAllProjectUsers($projectId);
    }

    private function getUsersByJabatan($projectId, $jabatanIds)
    {
        $userIds = [];
        
        // From project_user pivot with jabatan
        $pivotUsers = \DB::table('project_user')
            ->where('project_id', $projectId)
            ->whereIn('jabatan_id', $jabatanIds)
            ->where('is_active', true)
            ->pluck('user_id')
            ->toArray();
            
        // From karyawans table with jabatan
        $karyawanUsers = \App\Models\Karyawan::where('project_id', $projectId)
            ->whereIn('jabatan_id', $jabatanIds)
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();
            
        return array_merge($userIds, $pivotUsers, $karyawanUsers);
    }
}