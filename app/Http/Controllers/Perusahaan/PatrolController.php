<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KategoriInsiden;
use App\Models\AreaPatrol;
use App\Models\RutePatrol;
use App\Models\Checkpoint;
use App\Models\AsetKawasan;
use App\Models\InventarisPatroli;
use App\Models\KuesionerPatroli;
use App\Models\PertanyaanKuesioner;
use App\Models\PemeriksaanPatroli;
use App\Models\PertanyaanPemeriksaan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public function checkpoint(Request $request)
    {
        $query = Checkpoint::select([
                'id',
                'perusahaan_id',
                'rute_patrol_id',
                'nama',
                'qr_code',
                'deskripsi',
                'urutan',
                'alamat',
                'latitude',
                'longitude',
                'is_active',
                'created_at'
            ])
            ->with('rutePatrol:id,nama,area_patrol_id', 'rutePatrol.areaPatrol:id,nama')
            ->withCount('asets');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%")
                  ->orWhere('alamat', 'ILIKE', "%{$search}%")
                  ->orWhere('qr_code', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by rute
        if ($request->filled('rute_id')) {
            $query->where('rute_patrol_id', $request->rute_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $checkpoints = $query->orderBy('urutan')->paginate(15)->withQueryString();
        $rutePatrols = RutePatrol::select('id', 'nama', 'area_patrol_id')
            ->with('areaPatrol:id,nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.patrol.checkpoint', compact('checkpoints', 'rutePatrols'));
    }

    public function storeCheckpoint(Request $request)
    {
        $validated = $request->validate([
            'rute_patrol_id' => 'required|exists:rute_patrols,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'qr_code' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'required|boolean',
        ], [
            'rute_patrol_id.required' => 'Rute patrol wajib dipilih',
            'rute_patrol_id.exists' => 'Rute patrol tidak valid',
            'nama.required' => 'Nama checkpoint wajib diisi',
            'nama.max' => 'Nama checkpoint maksimal 255 karakter',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 0',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        Checkpoint::create($validated);

        return redirect()->route('perusahaan.patrol.checkpoint')
            ->with('success', 'Checkpoint berhasil ditambahkan');
    }

    public function editCheckpoint(Checkpoint $checkpoint)
    {
        $checkpoint->load('rutePatrol');
        return response()->json([
            'id' => $checkpoint->id,
            'hash_id' => $checkpoint->hash_id,
            'rute_patrol_id' => $checkpoint->rute_patrol_id,
            'nama' => $checkpoint->nama,
            'qr_code' => $checkpoint->qr_code,
            'deskripsi' => $checkpoint->deskripsi,
            'urutan' => $checkpoint->urutan,
            'alamat' => $checkpoint->alamat,
            'latitude' => $checkpoint->latitude,
            'longitude' => $checkpoint->longitude,
            'is_active' => $checkpoint->is_active,
        ]);
    }

    public function updateCheckpoint(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'rute_patrol_id' => 'required|exists:rute_patrols,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'qr_code' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'required|boolean',
        ], [
            'rute_patrol_id.required' => 'Rute patrol wajib dipilih',
            'rute_patrol_id.exists' => 'Rute patrol tidak valid',
            'nama.required' => 'Nama checkpoint wajib diisi',
            'nama.max' => 'Nama checkpoint maksimal 255 karakter',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 0',
            'latitude.between' => 'Latitude harus antara -90 sampai 90',
            'longitude.between' => 'Longitude harus antara -180 sampai 180',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $checkpoint->update($validated);

        return redirect()->route('perusahaan.patrol.checkpoint')
            ->with('success', 'Checkpoint berhasil diupdate');
    }

    public function destroyCheckpoint(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return redirect()->route('perusahaan.patrol.checkpoint')
            ->with('success', 'Checkpoint berhasil dihapus');
    }

    public function asetKawasan(Request $request)
    {
        $query = AsetKawasan::select([
                'id',
                'perusahaan_id',
                'kode_aset',
                'nama',
                'kategori',
                'merk',
                'model',
                'serial_number',
                'foto',
                'deskripsi',
                'is_active',
                'created_at'
            ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('kode_aset', 'ILIKE', "%{$search}%")
                  ->orWhere('kategori', 'ILIKE', "%{$search}%")
                  ->orWhere('merk', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', 'ILIKE', "%{$request->kategori}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $asetKawasans = $query->latest()->paginate(15)->withQueryString();

        return view('perusahaan.patrol.aset-kawasan', compact('asetKawasans'));
    }

    public function storeAsetKawasan(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama aset wajib diisi',
            'nama.max' => 'Nama aset maksimal 255 karakter',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.max' => 'Kategori maksimal 255 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('aset-kawasan', $filename, 'public');
            $validated['foto'] = $path;
        }

        AsetKawasan::create($validated);

        return redirect()->route('perusahaan.patrol.aset-kawasan')
            ->with('success', 'Aset kawasan berhasil ditambahkan');
    }

    public function editAsetKawasan(AsetKawasan $asetKawasan)
    {
        return response()->json([
            'id' => $asetKawasan->id,
            'hash_id' => $asetKawasan->hash_id,
            'kode_aset' => $asetKawasan->kode_aset,
            'nama' => $asetKawasan->nama,
            'kategori' => $asetKawasan->kategori,
            'merk' => $asetKawasan->merk,
            'model' => $asetKawasan->model,
            'serial_number' => $asetKawasan->serial_number,
            'foto' => $asetKawasan->foto,
            'deskripsi' => $asetKawasan->deskripsi,
            'is_active' => $asetKawasan->is_active,
        ]);
    }

    public function updateAsetKawasan(Request $request, AsetKawasan $asetKawasan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama aset wajib diisi',
            'nama.max' => 'Nama aset maksimal 255 karakter',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.max' => 'Kategori maksimal 255 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($asetKawasan->foto) {
                Storage::disk('public')->delete($asetKawasan->foto);
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('aset-kawasan', $filename, 'public');
            $validated['foto'] = $path;
        }

        $asetKawasan->update($validated);

        return redirect()->route('perusahaan.patrol.aset-kawasan')
            ->with('success', 'Aset kawasan berhasil diupdate');
    }

    public function destroyAsetKawasan(AsetKawasan $asetKawasan)
    {
        // Delete foto
        if ($asetKawasan->foto) {
            Storage::disk('public')->delete($asetKawasan->foto);
        }
        
        $asetKawasan->delete();

        return redirect()->route('perusahaan.patrol.aset-kawasan')
            ->with('success', 'Aset kawasan berhasil dihapus');
    }

    public function showCheckpointQr(Checkpoint $checkpoint)
    {
        $checkpoint->load([
            'rutePatrol.areaPatrol.project', 
            'perusahaan:id,nama,logo',
            'asets:id,nama,kategori'
        ])->loadCount('asets');
        return view('perusahaan.patrol.checkpoint-qr', compact('checkpoint'));
    }

    public function checkpointAset(Checkpoint $checkpoint)
    {
        $checkpoint->load('rutePatrol.areaPatrol', 'asets');
        $asetKawasans = AsetKawasan::select('id', 'kode_aset', 'nama', 'kategori', 'merk', 'model', 'foto')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();
        
        return view('perusahaan.patrol.checkpoint-aset', compact('checkpoint', 'asetKawasans'));
    }

    public function storeCheckpointAset(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'aset_ids' => 'required|array|min:1',
            'aset_ids.*' => 'exists:aset_kawasans,id',
            'catatan' => 'nullable|array',
            'catatan.*' => 'nullable|string',
        ], [
            'aset_ids.required' => 'Pilih minimal 1 aset',
            'aset_ids.min' => 'Pilih minimal 1 aset',
            'aset_ids.*.exists' => 'Aset tidak valid',
        ]);

        // Sync asets with catatan
        $syncData = [];
        foreach ($validated['aset_ids'] as $asetId) {
            $syncData[$asetId] = [
                'catatan' => $validated['catatan'][$asetId] ?? null
            ];
        }
        
        $checkpoint->asets()->sync($syncData);

        return redirect()->route('perusahaan.patrol.checkpoint.aset', $checkpoint->hash_id)
            ->with('success', 'Aset checkpoint berhasil diupdate');
    }

    public function inventarisPatroli(Request $request)
    {
        $query = InventarisPatroli::select([
                'id',
                'perusahaan_id',
                'nama',
                'kategori',
                'foto',
                'catatan',
                'is_active',
                'created_at'
            ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('kategori', 'ILIKE', "%{$search}%")
                  ->orWhere('catatan', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $inventaris = $query->latest()->paginate(15)->withQueryString();
        
        // Get unique categories
        $kategoris = InventarisPatroli::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('perusahaan.patrol.inventaris-patroli', compact('inventaris', 'kategoris'));
    }

    public function storeInventarisPatroli(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama inventaris wajib diisi',
            'nama.max' => 'Nama inventaris maksimal 255 karakter',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.max' => 'Kategori maksimal 255 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('inventaris-patroli', $filename, 'public');
            $validated['foto'] = $path;
        }

        InventarisPatroli::create($validated);

        return redirect()->route('perusahaan.patrol.inventaris-patroli')
            ->with('success', 'Inventaris patroli berhasil ditambahkan');
    }

    public function editInventarisPatroli(InventarisPatroli $inventarisPatroli)
    {
        return response()->json([
            'id' => $inventarisPatroli->id,
            'hash_id' => $inventarisPatroli->hash_id,
            'nama' => $inventarisPatroli->nama,
            'kategori' => $inventarisPatroli->kategori,
            'foto' => $inventarisPatroli->foto,
            'catatan' => $inventarisPatroli->catatan,
            'is_active' => $inventarisPatroli->is_active,
        ]);
    }

    public function updateInventarisPatroli(Request $request, InventarisPatroli $inventarisPatroli)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama inventaris wajib diisi',
            'nama.max' => 'Nama inventaris maksimal 255 karakter',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.max' => 'Kategori maksimal 255 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($inventarisPatroli->foto) {
                Storage::disk('public')->delete($inventarisPatroli->foto);
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('inventaris-patroli', $filename, 'public');
            $validated['foto'] = $path;
        }

        $inventarisPatroli->update($validated);

        return redirect()->route('perusahaan.patrol.inventaris-patroli')
            ->with('success', 'Inventaris patroli berhasil diupdate');
    }

    public function destroyInventarisPatroli(InventarisPatroli $inventarisPatroli)
    {
        // Delete foto
        if ($inventarisPatroli->foto) {
            Storage::disk('public')->delete($inventarisPatroli->foto);
        }
        
        $inventarisPatroli->delete();

        return redirect()->route('perusahaan.patrol.inventaris-patroli')
            ->with('success', 'Inventaris patroli berhasil dihapus');
    }

    // Kuesioner Methods
    public function kuesionerPatroli(Request $request)
    {
        $query = KuesionerPatroli::select([
                'id',
                'perusahaan_id',
                'judul',
                'deskripsi',
                'is_active',
                'created_at'
            ])
            ->withCount('pertanyaans');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $kuesioners = $query->latest()->paginate(15)->withQueryString();

        return view('perusahaan.patrol.kuesioner-patroli', compact('kuesioners'));
    }

    public function storeKuesionerPatroli(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'judul.required' => 'Judul kuesioner wajib diisi',
            'judul.max' => 'Judul kuesioner maksimal 255 karakter',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        KuesionerPatroli::create($validated);

        return redirect()->route('perusahaan.patrol.kuesioner-patroli')
            ->with('success', 'Kuesioner berhasil ditambahkan');
    }

    public function editKuesionerPatroli(KuesionerPatroli $kuesionerPatroli)
    {
        return response()->json([
            'id' => $kuesionerPatroli->id,
            'hash_id' => $kuesionerPatroli->hash_id,
            'judul' => $kuesionerPatroli->judul,
            'deskripsi' => $kuesionerPatroli->deskripsi,
            'is_active' => $kuesionerPatroli->is_active,
        ]);
    }

    public function updateKuesionerPatroli(Request $request, KuesionerPatroli $kuesionerPatroli)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'judul.required' => 'Judul kuesioner wajib diisi',
            'judul.max' => 'Judul kuesioner maksimal 255 karakter',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $kuesionerPatroli->update($validated);

        return redirect()->route('perusahaan.patrol.kuesioner-patroli')
            ->with('success', 'Kuesioner berhasil diupdate');
    }

    public function destroyKuesionerPatroli(KuesionerPatroli $kuesionerPatroli)
    {
        $kuesionerPatroli->delete();

        return redirect()->route('perusahaan.patrol.kuesioner-patroli')
            ->with('success', 'Kuesioner berhasil dihapus');
    }

    public function kelolaPertanyaan(KuesionerPatroli $kuesionerPatroli)
    {
        $kuesionerPatroli->load('pertanyaans');
        return view('perusahaan.patrol.kelola-pertanyaan', compact('kuesionerPatroli'));
    }

    public function storePertanyaan(Request $request, KuesionerPatroli $kuesionerPatroli)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'tipe_jawaban' => 'required|in:pilihan,text',
            'opsi_jawaban' => 'required_if:tipe_jawaban,pilihan|array|min:2',
            'opsi_jawaban.*' => 'required_if:tipe_jawaban,pilihan|string',
            'is_required' => 'required|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi',
            'tipe_jawaban.required' => 'Tipe jawaban wajib dipilih',
            'opsi_jawaban.required_if' => 'Opsi jawaban wajib diisi untuk tipe pilihan',
            'opsi_jawaban.min' => 'Minimal 2 opsi jawaban',
            'is_required.required' => 'Status wajib dipilih',
        ]);

        // Get max urutan
        $maxUrutan = $kuesionerPatroli->pertanyaans()->max('urutan') ?? 0;
        $validated['urutan'] = $maxUrutan + 1;
        $validated['kuesioner_patroli_id'] = $kuesionerPatroli->id;

        // Clean opsi_jawaban if tipe is text
        if ($validated['tipe_jawaban'] === 'text') {
            $validated['opsi_jawaban'] = null;
        }

        PertanyaanKuesioner::create($validated);

        return redirect()->route('perusahaan.patrol.kuesioner-patroli.pertanyaan', $kuesionerPatroli->hash_id)
            ->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    public function updatePertanyaan(Request $request, KuesionerPatroli $kuesionerPatroli, PertanyaanKuesioner $pertanyaan)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'tipe_jawaban' => 'required|in:pilihan,text',
            'opsi_jawaban' => 'required_if:tipe_jawaban,pilihan|array|min:2',
            'opsi_jawaban.*' => 'required_if:tipe_jawaban,pilihan|string',
            'is_required' => 'required|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi',
            'tipe_jawaban.required' => 'Tipe jawaban wajib dipilih',
            'opsi_jawaban.required_if' => 'Opsi jawaban wajib diisi untuk tipe pilihan',
            'opsi_jawaban.min' => 'Minimal 2 opsi jawaban',
            'is_required.required' => 'Status wajib dipilih',
        ]);

        // Clean opsi_jawaban if tipe is text
        if ($validated['tipe_jawaban'] === 'text') {
            $validated['opsi_jawaban'] = null;
        }

        $pertanyaan->update($validated);

        return redirect()->route('perusahaan.patrol.kuesioner-patroli.pertanyaan', $kuesionerPatroli->hash_id)
            ->with('success', 'Pertanyaan berhasil diupdate');
    }

    public function destroyPertanyaan(KuesionerPatroli $kuesionerPatroli, PertanyaanKuesioner $pertanyaan)
    {
        $pertanyaan->delete();

        return redirect()->route('perusahaan.patrol.kuesioner-patroli.pertanyaan', $kuesionerPatroli->hash_id)
            ->with('success', 'Pertanyaan berhasil dihapus');
    }

    public function updateUrutanPertanyaan(Request $request, KuesionerPatroli $kuesionerPatroli)
    {
        $validated = $request->validate([
            'urutan' => 'required|array',
            'urutan.*' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['urutan'] as $id => $urutan) {
                PertanyaanKuesioner::where('id', $id)->update(['urutan' => $urutan]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Urutan berhasil diupdate']);
    }

    public function previewKuesioner(KuesionerPatroli $kuesionerPatroli)
    {
        $kuesionerPatroli->load('pertanyaans');
        return view('perusahaan.patrol.preview-kuesioner', compact('kuesionerPatroli'));
    }

    // Pemeriksaan Methods
    public function pemeriksaanPatroli(Request $request)
    {
        $query = PemeriksaanPatroli::select([
                'id',
                'perusahaan_id',
                'nama',
                'deskripsi',
                'frekuensi',
                'pemeriksaan_terakhir',
                'is_active',
                'created_at'
            ])
            ->withCount('pertanyaans');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by frekuensi
        if ($request->filled('frekuensi')) {
            $query->where('frekuensi', $request->frekuensi);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $pemeriksaans = $query->latest()->paginate(15)->withQueryString();

        return view('perusahaan.patrol.pemeriksaan-patroli', compact('pemeriksaans'));
    }

    public function storePemeriksaanPatroli(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'frekuensi' => 'required|in:harian,mingguan,bulanan',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama pemeriksaan wajib diisi',
            'nama.max' => 'Nama pemeriksaan maksimal 255 karakter',
            'frekuensi.required' => 'Frekuensi wajib dipilih',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        PemeriksaanPatroli::create($validated);

        return redirect()->route('perusahaan.patrol.pemeriksaan-patroli')
            ->with('success', 'Pemeriksaan berhasil ditambahkan');
    }

    public function editPemeriksaanPatroli(PemeriksaanPatroli $pemeriksaanPatroli)
    {
        return response()->json([
            'id' => $pemeriksaanPatroli->id,
            'hash_id' => $pemeriksaanPatroli->hash_id,
            'nama' => $pemeriksaanPatroli->nama,
            'deskripsi' => $pemeriksaanPatroli->deskripsi,
            'frekuensi' => $pemeriksaanPatroli->frekuensi,
            'is_active' => $pemeriksaanPatroli->is_active,
        ]);
    }

    public function updatePemeriksaanPatroli(Request $request, PemeriksaanPatroli $pemeriksaanPatroli)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'frekuensi' => 'required|in:harian,mingguan,bulanan',
            'is_active' => 'required|boolean',
        ], [
            'nama.required' => 'Nama pemeriksaan wajib diisi',
            'nama.max' => 'Nama pemeriksaan maksimal 255 karakter',
            'frekuensi.required' => 'Frekuensi wajib dipilih',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        $pemeriksaanPatroli->update($validated);

        return redirect()->route('perusahaan.patrol.pemeriksaan-patroli')
            ->with('success', 'Pemeriksaan berhasil diupdate');
    }

    public function destroyPemeriksaanPatroli(PemeriksaanPatroli $pemeriksaanPatroli)
    {
        $pemeriksaanPatroli->delete();

        return redirect()->route('perusahaan.patrol.pemeriksaan-patroli')
            ->with('success', 'Pemeriksaan berhasil dihapus');
    }

    public function kelolaPertanyaanPemeriksaan(PemeriksaanPatroli $pemeriksaanPatroli)
    {
        $pemeriksaanPatroli->load('pertanyaans');
        return view('perusahaan.patrol.kelola-pertanyaan-pemeriksaan', compact('pemeriksaanPatroli'));
    }

    public function storePertanyaanPemeriksaan(Request $request, PemeriksaanPatroli $pemeriksaanPatroli)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'tipe_jawaban' => 'required|in:pilihan,text',
            'opsi_jawaban' => 'required_if:tipe_jawaban,pilihan|array|min:2',
            'opsi_jawaban.*' => 'required_if:tipe_jawaban,pilihan|string',
            'is_required' => 'required|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi',
            'tipe_jawaban.required' => 'Tipe jawaban wajib dipilih',
            'opsi_jawaban.required_if' => 'Opsi jawaban wajib diisi untuk tipe pilihan',
            'opsi_jawaban.min' => 'Minimal 2 opsi jawaban',
            'is_required.required' => 'Status wajib dipilih',
        ]);

        $maxUrutan = $pemeriksaanPatroli->pertanyaans()->max('urutan') ?? 0;
        $validated['urutan'] = $maxUrutan + 1;
        $validated['pemeriksaan_patroli_id'] = $pemeriksaanPatroli->id;

        if ($validated['tipe_jawaban'] === 'text') {
            $validated['opsi_jawaban'] = null;
        }

        PertanyaanPemeriksaan::create($validated);

        return redirect()->route('perusahaan.patrol.pemeriksaan-patroli.pertanyaan', $pemeriksaanPatroli->hash_id)
            ->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    public function updatePertanyaanPemeriksaan(Request $request, PemeriksaanPatroli $pemeriksaanPatroli, PertanyaanPemeriksaan $pertanyaan)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'tipe_jawaban' => 'required|in:pilihan,text',
            'opsi_jawaban' => 'required_if:tipe_jawaban,pilihan|array|min:2',
            'opsi_jawaban.*' => 'required_if:tipe_jawaban,pilihan|string',
            'is_required' => 'required|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi',
            'tipe_jawaban.required' => 'Tipe jawaban wajib dipilih',
            'opsi_jawaban.required_if' => 'Opsi jawaban wajib diisi untuk tipe pilihan',
            'opsi_jawaban.min' => 'Minimal 2 opsi jawaban',
            'is_required.required' => 'Status wajib dipilih',
        ]);

        if ($validated['tipe_jawaban'] === 'text') {
            $validated['opsi_jawaban'] = null;
        }

        $pertanyaan->update($validated);

        return redirect()->route('perusahaan.patrol.pemeriksaan-patroli.pertanyaan', $pemeriksaanPatroli->hash_id)
            ->with('success', 'Pertanyaan berhasil diupdate');
    }

    public function destroyPertanyaanPemeriksaan(PemeriksaanPatroli $pemeriksaanPatroli, PertanyaanPemeriksaan $pertanyaan)
    {
        $pertanyaan->delete();

        return redirect()->route('perusahaan.patrol.pemeriksaan-patroli.pertanyaan', $pemeriksaanPatroli->hash_id)
            ->with('success', 'Pertanyaan berhasil dihapus');
    }

    public function updateUrutanPertanyaanPemeriksaan(Request $request, PemeriksaanPatroli $pemeriksaanPatroli)
    {
        $validated = $request->validate([
            'urutan' => 'required|array',
            'urutan.*' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['urutan'] as $id => $urutan) {
                PertanyaanPemeriksaan::where('id', $id)->update(['urutan' => $urutan]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Urutan berhasil diupdate']);
    }

    public function previewPemeriksaan(PemeriksaanPatroli $pemeriksaanPatroli)
    {
        $pemeriksaanPatroli->load('pertanyaans');
        return view('perusahaan.patrol.preview-pemeriksaan', compact('pemeriksaanPatroli'));
    }
}
