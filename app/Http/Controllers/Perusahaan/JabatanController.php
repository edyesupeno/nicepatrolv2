<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Project;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::with('projects');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->whereHas('projects', function($q) use ($request) {
                $q->where('projects.id', $request->project_id);
            });
        }

        $jabatans = $query->latest()->paginate(15)->withQueryString();
        $projects = Project::where('is_active', true)->orderBy('nama')->get();

        return view('perusahaan.jabatans.index', compact('jabatans', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
        ], [
            'nama.required' => 'Nama jabatan wajib diisi',
            'nama.max' => 'Nama jabatan maksimal 255 karakter',
            'projects.required' => 'Minimal pilih 1 project',
            'projects.min' => 'Minimal pilih 1 project',
            'projects.*.exists' => 'Project tidak valid',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        $jabatan = Jabatan::create($validated);
        $jabatan->projects()->sync($request->projects);

        return redirect()->route('perusahaan.jabatans.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    public function edit(Jabatan $jabatan)
    {
        $jabatan->load('projects');
        return response()->json([
            'id' => $jabatan->id,
            'hash_id' => $jabatan->hash_id,
            'nama' => $jabatan->nama,
            'deskripsi' => $jabatan->deskripsi,
            'project_ids' => $jabatan->projects->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
        ], [
            'nama.required' => 'Nama jabatan wajib diisi',
            'nama.max' => 'Nama jabatan maksimal 255 karakter',
            'projects.required' => 'Minimal pilih 1 project',
            'projects.min' => 'Minimal pilih 1 project',
            'projects.*.exists' => 'Project tidak valid',
        ]);

        $jabatan->update($validated);
        $jabatan->projects()->sync($request->projects);

        return redirect()->route('perusahaan.jabatans.index')
            ->with('success', 'Jabatan berhasil diupdate');
    }

    public function destroy(Jabatan $jabatan)
    {
        $jabatan->projects()->detach();
        $jabatan->delete();

        return redirect()->route('perusahaan.jabatans.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }
}
