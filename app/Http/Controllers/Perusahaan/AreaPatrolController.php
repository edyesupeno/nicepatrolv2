<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\AreaPatrol;
use App\Models\Project;
use Illuminate\Http\Request;

class AreaPatrolController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'koordinat' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'nama.required' => 'Nama POS Jaga wajib diisi',
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'area_id.exists' => 'Area tidak valid',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        $areaPatrol = AreaPatrol::create($validated);
        
        // Load the project and area relationships
        $areaPatrol->load(['project', 'area']);

        return response()->json([
            'success' => true,
            'message' => 'POS Jaga berhasil ditambahkan',
            'data' => $areaPatrol
        ]);
    }

    /**
     * Get area patrols by project (for AJAX)
     */
    public function getByProject(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            
            \Log::info('AreaPatrolController getByProject called', [
                'project_id' => $projectId,
                'user_id' => auth()->id(),
                'perusahaan_id' => auth()->user()->perusahaan_id ?? null
            ]);
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID required'
                ]);
            }

            $areaPatrols = AreaPatrol::where('project_id', $projectId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'deskripsi']);

            \Log::info('Area Patrols found', [
                'count' => $areaPatrols->count(),
                'area_patrols' => $areaPatrols->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $areaPatrols
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in AreaPatrolController getByProject', [
                'error' => $e->getMessage(),
                'project_id' => $request->get('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}