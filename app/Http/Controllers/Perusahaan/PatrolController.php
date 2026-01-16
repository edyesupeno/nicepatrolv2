<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KategoriInsiden;
use App\Models\AreaPatrol;
use App\Models\RutePatrol;
use App\Models\Project;
use Illuminate\Http\Request;

class PatrolController extends Controller
{
    public function kategoriInsiden(Request $request)
    {
        $query = KategoriInsiden::select([
                'id',
                'perusahaan_id',
                'nama',
                'deskripsi',
                'is_active',
                'created_at'
            ])
            ->with('projects:id,nama');

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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $kategoriInsidens = $query->latest()->paginate(15)->withQueryString();
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.patrol.kategori-insiden', compact('kategoriInsidens', 'projects'));
    }

    public function storeKategoriInsiden(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.max' => 'Nama kategori maksimal 255 karakter',
            'is_active.required' => 'Status wajib dipilih',
            'projects.required' => 'Minimal pilih 1 project',
            'projects.min' => 'Minimal pilih 1 project',
            'projects.*.exists' => 'Project tidak valid',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        $kategoriInsiden = KategoriInsiden::create($validated);
        $kategoriInsiden->projects()->sync($request->projects);

        return redirect()->route('perusahaan.patrol.kategori-insiden')
            ->with('success', 'Kategori insiden berhasil ditambahkan');
    }

    public function editKategoriInsiden(KategoriInsiden $kategoriInsiden)
    {
        $kategoriInsiden->load('projects');
        return response()->json([
            'id' => $kategoriInsiden->id,
            'hash_id' => $kategoriInsiden->hash_id,
            'nama' => $kategoriInsiden->nama,
            'deskripsi' => $kategoriInsiden->deskripsi,
            'is_active' => $kategoriInsiden->is_active,
            'project_ids' => $kategoriInsiden->projects->pluck('id')->toArray(),
        ]);
    }

    public function updateKategoriInsiden(Request $request, KategoriInsiden $kategoriInsiden)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.max' => 'Nama kategori maksimal 255 karakter',
            'is_active.required' => 'Status wajib dipilih',
            'projects.required' => 'Minimal pilih 1 project',
            'projects.min' => 'Minimal pilih 1 project',
            'projects.*.exists' => 'Project tidak valid',
        ]);

        $kategoriInsiden->update($validated);
        $kategoriInsiden->projects()->sync($request->projects);

        return redirect()->route('perusahaan.patrol.kategori-insiden')
            ->with('success', 'Kategori insiden berhasil diupdate');
    }

    public function destroyKategoriInsiden(KategoriInsiden $kategoriInsiden)
    {
        $kategoriInsiden->projects()->detach();
        $kategoriInsiden->delete();

        return redirect()->route('perusahaan.patrol.kategori-insiden')
            ->with('success', 'Kategori insiden berhasil dihapus');
    }

    public function area(Request $request)
    {
        $query = AreaPatrol::select([
                'id',
                'perusahaan_id',
                'project_id',
                'nama',
                'deskripsi',
                'alamat',
                'koordinat',
                'is_active',
                'created_at'
            ])
            ->with('project:id,nama');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%")
                  ->orWhere('alamat', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $areaPatrols = $query->latest()->paginate(15)->withQueryString();
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.patrol.area', compact('areaPatrols', 'projects'));
    }

    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'koordinat' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama.required' => 'Nama area wajib diisi',
            'nama.max' => 'Nama area maksimal 255 karakter',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        AreaPatrol::create($validated);

        return redirect()->route('perusahaan.patrol.area')
            ->with('success', 'Area patrol berhasil ditambahkan');
    }

    public function editArea(AreaPatrol $areaPatrol)
    {
        $areaPatrol->load('project');
        return response()->json([
            'id' => $areaPatrol->id,
            'hash_id' => $areaPatrol->hash_id,
            'project_id' => $areaPatrol->project_id,
            'nama' => $areaPatrol->nama,
            'deskripsi' => $areaPatrol->deskripsi,
            'alamat' => $areaPatrol->alamat,
            'koordinat' => $areaPatrol->koordinat,
            'is_active' => $areaPatrol->is_active,
        ]);
    }

    public function updateArea(Request $request, AreaPatrol $areaPatrol)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'koordinat' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama.required' => 'Nama area wajib diisi',
            'nama.max' => 'Nama area maksimal 255 karakter',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $areaPatrol->update($validated);

        return redirect()->route('perusahaan.patrol.area')
            ->with('success', 'Area patrol berhasil diupdate');
    }

    public function destroyArea(AreaPatrol $areaPatrol)
    {
        $areaPatrol->delete();

        return redirect()->route('perusahaan.patrol.area')
            ->with('success', 'Area patrol berhasil dihapus');
    }

    public function rutePatrol(Request $request)
    {
        $query = RutePatrol::select([
                'id',
                'perusahaan_id',
                'area_patrol_id',
                'nama',
                'deskripsi',
                'estimasi_waktu',
                'is_active',
                'created_at'
            ])
            ->with('areaPatrol:id,nama,project_id', 'areaPatrol.project:id,nama');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_patrol_id', $request->area_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $rutePatrols = $query->latest()->paginate(15)->withQueryString();
        $areaPatrols = AreaPatrol::select('id', 'nama', 'project_id')
            ->with('project:id,nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.patrol.rute-patrol', compact('rutePatrols', 'areaPatrols'));
    }

    public function storeRutePatrol(Request $request)
    {
        $validated = $request->validate([
            'area_patrol_id' => 'required|exists:area_patrols,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'estimasi_waktu' => 'nullable|integer|min:1',
            'is_active' => 'required|boolean',
        ], [
            'area_patrol_id.required' => 'Area patrol wajib dipilih',
            'area_patrol_id.exists' => 'Area patrol tidak valid',
            'nama.required' => 'Nama rute wajib diisi',
            'nama.max' => 'Nama rute maksimal 255 karakter',
            'estimasi_waktu.integer' => 'Estimasi waktu harus berupa angka',
            'estimasi_waktu.min' => 'Estimasi waktu minimal 1 menit',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        RutePatrol::create($validated);

        return redirect()->route('perusahaan.patrol.rute-patrol')
            ->with('success', 'Rute patrol berhasil ditambahkan');
    }

    public function editRutePatrol(RutePatrol $rutePatrol)
    {
        $rutePatrol->load('areaPatrol');
        return response()->json([
            'id' => $rutePatrol->id,
            'hash_id' => $rutePatrol->hash_id,
            'area_patrol_id' => $rutePatrol->area_patrol_id,
            'nama' => $rutePatrol->nama,
            'deskripsi' => $rutePatrol->deskripsi,
            'estimasi_waktu' => $rutePatrol->estimasi_waktu,
            'is_active' => $rutePatrol->is_active,
        ]);
    }

    public function updateRutePatrol(Request $request, RutePatrol $rutePatrol)
    {
        $validated = $request->validate([
            'area_patrol_id' => 'required|exists:area_patrols,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'estimasi_waktu' => 'nullable|integer|min:1',
            'is_active' => 'required|boolean',
        ], [
            'area_patrol_id.required' => 'Area patrol wajib dipilih',
            'area_patrol_id.exists' => 'Area patrol tidak valid',
            'nama.required' => 'Nama rute wajib diisi',
            'nama.max' => 'Nama rute maksimal 255 karakter',
            'estimasi_waktu.integer' => 'Estimasi waktu harus berupa angka',
            'estimasi_waktu.min' => 'Estimasi waktu minimal 1 menit',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $rutePatrol->update($validated);

        return redirect()->route('perusahaan.patrol.rute-patrol')
            ->with('success', 'Rute patrol berhasil diupdate');
    }

    public function destroyRutePatrol(RutePatrol $rutePatrol)
    {
        $rutePatrol->delete();

        return redirect()->route('perusahaan.patrol.rute-patrol')
            ->with('success', 'Rute patrol berhasil dihapus');
    }

    public function checkpoint()
    {
        return view('perusahaan.patrol.checkpoint');
    }

    public function asetKawasan()
    {
        return view('perusahaan.patrol.aset-kawasan');
    }
}
