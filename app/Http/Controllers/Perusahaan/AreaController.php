<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Project;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::with('project');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('alamat', 'ILIKE', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('nama', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $areas = $query->latest()->paginate(15)->withQueryString();
        $projects = Project::where('is_active', true)->orderBy('nama')->get();

        return view('perusahaan.areas.index', compact('areas', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama.required' => 'Nama area wajib diisi',
            'nama.max' => 'Nama area maksimal 255 karakter',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        Area::create($validated);

        return redirect()->route('perusahaan.areas.index')
            ->with('success', 'Area berhasil ditambahkan');
    }

    public function edit(Area $area)
    {
        return response()->json($area);
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama.required' => 'Nama area wajib diisi',
            'nama.max' => 'Nama area maksimal 255 karakter',
        ]);

        $area->update($validated);

        return redirect()->route('perusahaan.areas.index')
            ->with('success', 'Area berhasil diupdate');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('perusahaan.areas.index')
            ->with('success', 'Area berhasil dihapus');
    }
}
