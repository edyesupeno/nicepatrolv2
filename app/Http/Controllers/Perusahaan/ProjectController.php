<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Kantor;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['kantor', 'jabatans']);

        // Filter by kantor
        if ($request->filled('kantor_id')) {
            $query->where('kantor_id', $request->kantor_id);
        }

        $projects = $query->paginate(9);
        $kantors = Kantor::where('is_active', true)->get();

        return view('perusahaan.projects.index', compact('projects', 'kantors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kantor_id' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:255',
            'timezone' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'kantor_id.required' => 'Kantor wajib dipilih',
            'nama.required' => 'Nama project wajib diisi',
            'timezone.required' => 'Timezone wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Project::create($validated);

        return redirect()->route('perusahaan.projects.index')
            ->with('success', 'Project berhasil ditambahkan');
    }

    public function edit(Project $project)
    {
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'kantor_id' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:255',
            'timezone' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'kantor_id.required' => 'Kantor wajib dipilih',
            'nama.required' => 'Nama project wajib diisi',
            'timezone.required' => 'Timezone wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $project->update($validated);

        return redirect()->route('perusahaan.projects.index')
            ->with('success', 'Project berhasil diupdate');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('perusahaan.projects.index')
            ->with('success', 'Project berhasil dihapus');
    }

    public function getJabatans($id)
    {
        $project = Project::with('jabatans')->findOrFail($id);
        
        return response()->json($project->jabatans);
    }
}
