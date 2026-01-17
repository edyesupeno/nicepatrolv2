<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Atensi;
use App\Models\AtensiRecipient;
use App\Models\Project;
use App\Models\Area;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AtensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Atensi::with(['project', 'area', 'creator'])
            ->withCount(['recipients', 'readRecipients', 'acknowledgedRecipients']); // Use withCount for better performance

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
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
            $now = now()->toDateString();
            switch ($request->status) {
                case 'active':
                    $query->active()->current();
                    break;
                case 'expired':
                    $query->where('tanggal_selesai', '<', $now);
                    break;
                case 'upcoming':
                    $query->where('tanggal_mulai', '>', $now);
                    break;
                case 'urgent':
                    $query->urgent();
                    break;
            }
        }

        $atensis = $query->orderBy('is_urgent', 'desc')
                        ->orderBy('prioritas', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        $projects = Project::where('is_active', true)->get();

        return view('perusahaan.atensi.index', compact('atensis', 'projects'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        $areas = Area::all(); // Remove is_active filter since areas table doesn't have this column
        $jabatans = Jabatan::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('perusahaan.atensi.create', compact('projects', 'areas', 'jabatans', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:low,medium,high',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_type' => 'required|in:all,area,jabatan,specific_users',
            'target_data' => 'nullable|array',
            'is_urgent' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'judul.required' => 'Judul atensi wajib diisi',
            'deskripsi.required' => 'Deskripsi atensi wajib diisi',
            'prioritas.required' => 'Prioritas wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'target_type.required' => 'Target penerima wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['created_by'] = auth()->id();
        $validated['is_urgent'] = $request->input('is_urgent') == '1';
        $validated['is_active'] = $request->input('is_active', '1') == '1';
        $validated['published_at'] = now();

        $atensi = Atensi::create($validated);

        // Create recipients based on target type
        $this->createRecipients($atensi);
        
        // Clear cache for this atensi
        $this->clearAtensiStatsCache($atensi->id);

        return redirect()->route('perusahaan.atensi.index')
            ->with('success', 'Atensi berhasil dibuat dan dikirim');
    }

    public function show(Atensi $atensi)
    {
        // Load only essential relationships, not recipients (will be lazy loaded)
        $atensi->load(['project', 'area', 'creator']);
        
        // Get recipient counts for statistics (optimized query with caching)
        $cacheKey = "atensi_stats_{$atensi->id}";
        $recipientStats = Cache::remember($cacheKey, 300, function () use ($atensi) { // Cache for 5 minutes
            return [
                'total' => $atensi->recipients()->count(),
                'read' => $atensi->recipients()->whereNotNull('read_at')->count(),
                'acknowledged' => $atensi->recipients()->whereNotNull('acknowledged_at')->count(),
            ];
        });
        
        return view('perusahaan.atensi.show', compact('atensi', 'recipientStats'));
    }

    public function edit(Atensi $atensi)
    {
        $projects = Project::where('is_active', true)->get();
        $areas = Area::all(); // Remove is_active filter since areas table doesn't have this column
        $jabatans = Jabatan::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();

        return view('perusahaan.atensi.edit', compact('atensi', 'projects', 'areas', 'jabatans', 'users'));
    }

    public function update(Request $request, Atensi $atensi)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'required|in:low,medium,high',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_type' => 'required|in:all,area,jabatan,specific_users',
            'target_data' => 'nullable|array',
            'is_urgent' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'judul.required' => 'Judul atensi wajib diisi',
            'deskripsi.required' => 'Deskripsi atensi wajib diisi',
            'prioritas.required' => 'Prioritas wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'target_type.required' => 'Target penerima wajib dipilih',
        ]);

        $validated['is_urgent'] = $request->input('is_urgent') == '1';
        $validated['is_active'] = $request->input('is_active', '1') == '1';

        $atensi->update($validated);

        // Recreate recipients if target changed
        if ($atensi->wasChanged(['target_type', 'target_data', 'project_id', 'area_id'])) {
            $atensi->recipients()->delete();
            $this->createRecipients($atensi);
            $this->clearAtensiStatsCache($atensi->id);
        }

        return redirect()->route('perusahaan.atensi.index')
            ->with('success', 'Atensi berhasil diupdate');
    }

    public function destroy(Atensi $atensi)
    {
        $this->clearAtensiStatsCache($atensi->id);
        $atensi->delete();

        return redirect()->route('perusahaan.atensi.index')
            ->with('success', 'Atensi berhasil dihapus');
    }

    /**
     * Get recipients with pagination for lazy loading
     */
    public function getRecipients(Request $request, Atensi $atensi)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        
        $query = $atensi->recipients()
            ->with(['user:id,name,email'])
            ->select(['id', 'atensi_id', 'user_id', 'read_at', 'acknowledged_at', 'created_at']);
        
        // Search by user name or email
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'acknowledged':
                    $query->whereNotNull('acknowledged_at');
                    break;
                case 'unacknowledged':
                    $query->whereNull('acknowledged_at');
                    break;
            }
        }
        
        $recipients = $query->orderBy('acknowledged_at', 'desc')
                           ->orderBy('read_at', 'desc')
                           ->orderBy('created_at', 'asc')
                           ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $recipients->items(),
            'pagination' => [
                'current_page' => $recipients->currentPage(),
                'last_page' => $recipients->lastPage(),
                'per_page' => $recipients->perPage(),
                'total' => $recipients->total(),
                'from' => $recipients->firstItem(),
                'to' => $recipients->lastItem(),
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
     * Get users by criteria
     */
    public function getUsersByCriteria(Request $request)
    {
        $query = User::where('is_active', true);

        if ($request->filled('project_id')) {
            $projectId = $request->project_id;
            
            // Get users from project_user pivot or karyawans table
            $query->where(function ($q) use ($projectId) {
                $q->whereHas('projects', function ($pq) use ($projectId) {
                    $pq->where('project_id', $projectId)->where('is_active', true);
                })->orWhereHas('karyawan', function ($kq) use ($projectId) {
                    $kq->where('project_id', $projectId)->where('is_active', true);
                });
            });
        }

        if ($request->filled('area_id')) {
            // Filter by area if needed
            // This depends on how areas relate to users
        }

        if ($request->filled('jabatan_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('jabatan_id', $request->jabatan_id);
            });
        }

        $users = $query->select('id', 'name', 'email')->get();
        
        return response()->json($users);
    }

    /**
     * Clear cache for atensi statistics
     */
    private function clearAtensiStatsCache($atensiId)
    {
        Cache::forget("atensi_stats_{$atensiId}");
    }

    /**
     * Create recipients based on target type
     */
    private function createRecipients(Atensi $atensi)
    {
        $userIds = [];

        switch ($atensi->target_type) {
            case 'all':
                // Get all users in the project
                $userIds = $this->getAllProjectUsers($atensi->project_id);
                break;
                
            case 'area':
                // Get users in specific area
                if ($atensi->area_id) {
                    $userIds = $this->getUsersByArea($atensi->project_id, $atensi->area_id);
                }
                break;
                
            case 'jabatan':
                // Get users by jabatan
                if ($atensi->target_data && isset($atensi->target_data['jabatan_ids'])) {
                    $userIds = $this->getUsersByJabatan($atensi->project_id, $atensi->target_data['jabatan_ids']);
                }
                break;
                
            case 'specific_users':
                // Get specific users
                if ($atensi->target_data && isset($atensi->target_data['user_ids'])) {
                    $userIds = $atensi->target_data['user_ids'];
                }
                break;
        }

        // Create recipient records
        foreach (array_unique($userIds) as $userId) {
            AtensiRecipient::create([
                'atensi_id' => $atensi->id,
                'user_id' => $userId,
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