<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectContact;
use Illuminate\Http\Request;

class ProjectContactController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $query = $project->contacts();

        // Filter by jenis_kontak
        if ($request->filled('jenis_kontak')) {
            $query->where('jenis_kontak', $request->jenis_kontak);
        }

        // Filter by is_active
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Only active by default
        if (!$request->has('is_active')) {
            $query->where('is_active', true);
        }

        $contacts = $query->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kontak')
            ->orderBy('nama_kontak')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'hash_id' => $contact->hash_id,
                    'nama_kontak' => $contact->nama_kontak,
                    'jabatan_kontak' => $contact->jabatan_kontak,
                    'nomor_telepon' => $contact->nomor_telepon,
                    'email' => $contact->email,
                    'jenis_kontak' => $contact->jenis_kontak,
                    'jenis_kontak_label' => $contact->jenis_kontak_label,
                    'jenis_kontak_icon' => $contact->jenis_kontak_icon,
                    'jenis_kontak_color' => $contact->jenis_kontak_color,
                    'keterangan' => $contact->keterangan,
                    'is_primary' => $contact->is_primary,
                    'is_active' => $contact->is_active,
                ];
            })
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'jabatan_kontak' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'jenis_kontak' => 'required|in:polisi,pemadam_kebakaran,ambulans,security,manager_project,supervisor,teknisi,lainnya',
            'keterangan' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['project_id'] = $project->id;
        $validated['perusahaan_id'] = $request->user()->perusahaan_id;
        $validated['is_primary'] = $validated['is_primary'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Jika set sebagai primary, unset primary lainnya untuk jenis kontak yang sama
        if ($validated['is_primary']) {
            ProjectContact::where('project_id', $project->id)
                ->where('jenis_kontak', $validated['jenis_kontak'])
                ->update(['is_primary' => false]);
        }

        $contact = ProjectContact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil ditambahkan',
            'data' => [
                'id' => $contact->id,
                'hash_id' => $contact->hash_id,
                'nama_kontak' => $contact->nama_kontak,
                'jabatan_kontak' => $contact->jabatan_kontak,
                'nomor_telepon' => $contact->nomor_telepon,
                'email' => $contact->email,
                'jenis_kontak' => $contact->jenis_kontak,
                'jenis_kontak_label' => $contact->jenis_kontak_label,
                'jenis_kontak_icon' => $contact->jenis_kontak_icon,
                'jenis_kontak_color' => $contact->jenis_kontak_color,
                'keterangan' => $contact->keterangan,
                'is_primary' => $contact->is_primary,
                'is_active' => $contact->is_active,
            ]
        ], 201);
    }

    public function show(Project $project, ProjectContact $contact)
    {
        // Pastikan contact milik project ini
        if ($contact->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kontak tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $contact->id,
                'hash_id' => $contact->hash_id,
                'nama_kontak' => $contact->nama_kontak,
                'jabatan_kontak' => $contact->jabatan_kontak,
                'nomor_telepon' => $contact->nomor_telepon,
                'email' => $contact->email,
                'jenis_kontak' => $contact->jenis_kontak,
                'jenis_kontak_label' => $contact->jenis_kontak_label,
                'jenis_kontak_icon' => $contact->jenis_kontak_icon,
                'jenis_kontak_color' => $contact->jenis_kontak_color,
                'keterangan' => $contact->keterangan,
                'is_primary' => $contact->is_primary,
                'is_active' => $contact->is_active,
            ]
        ]);
    }

    public function update(Request $request, Project $project, ProjectContact $contact)
    {
        // Pastikan contact milik project ini
        if ($contact->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kontak tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'jabatan_kontak' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'jenis_kontak' => 'required|in:polisi,pemadam_kebakaran,ambulans,security,manager_project,supervisor,teknisi,lainnya',
            'keterangan' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_primary'] = $validated['is_primary'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Jika set sebagai primary, unset primary lainnya untuk jenis kontak yang sama
        if ($validated['is_primary']) {
            ProjectContact::where('project_id', $project->id)
                ->where('jenis_kontak', $validated['jenis_kontak'])
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diupdate',
            'data' => [
                'id' => $contact->id,
                'hash_id' => $contact->hash_id,
                'nama_kontak' => $contact->nama_kontak,
                'jabatan_kontak' => $contact->jabatan_kontak,
                'nomor_telepon' => $contact->nomor_telepon,
                'email' => $contact->email,
                'jenis_kontak' => $contact->jenis_kontak,
                'jenis_kontak_label' => $contact->jenis_kontak_label,
                'jenis_kontak_icon' => $contact->jenis_kontak_icon,
                'jenis_kontak_color' => $contact->jenis_kontak_color,
                'keterangan' => $contact->keterangan,
                'is_primary' => $contact->is_primary,
                'is_active' => $contact->is_active,
            ]
        ]);
    }

    public function destroy(Project $project, ProjectContact $contact)
    {
        // Pastikan contact milik project ini
        if ($contact->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kontak tidak ditemukan'
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil dihapus'
        ]);
    }

    /**
     * Get emergency contacts by type
     */
    public function emergency(Request $request, Project $project)
    {
        $emergencyTypes = ['polisi', 'pemadam_kebakaran', 'ambulans'];
        
        $contacts = $project->activeContacts()
            ->whereIn('jenis_kontak', $emergencyTypes)
            ->orderBy('jenis_kontak')
            ->orderBy('is_primary', 'desc')
            ->orderBy('nama_kontak')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contacts->groupBy('jenis_kontak')->map(function ($group) {
                return $group->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'hash_id' => $contact->hash_id,
                        'nama_kontak' => $contact->nama_kontak,
                        'jabatan_kontak' => $contact->jabatan_kontak,
                        'nomor_telepon' => $contact->nomor_telepon,
                        'email' => $contact->email,
                        'jenis_kontak' => $contact->jenis_kontak,
                        'jenis_kontak_label' => $contact->jenis_kontak_label,
                        'jenis_kontak_icon' => $contact->jenis_kontak_icon,
                        'jenis_kontak_color' => $contact->jenis_kontak_color,
                        'keterangan' => $contact->keterangan,
                        'is_primary' => $contact->is_primary,
                    ];
                });
            })
        ]);
    }
}