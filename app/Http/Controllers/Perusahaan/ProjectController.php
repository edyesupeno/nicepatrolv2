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
            'batas_cuti_tahunan' => 'required|integer|min:1|max:365',
        ], [
            'kantor_id.required' => 'Kantor wajib dipilih',
            'nama.required' => 'Nama project wajib diisi',
            'timezone.required' => 'Timezone wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'batas_cuti_tahunan.required' => 'Batas cuti tahunan wajib diisi',
            'batas_cuti_tahunan.min' => 'Batas cuti tahunan minimal 1 hari',
            'batas_cuti_tahunan.max' => 'Batas cuti tahunan maksimal 365 hari',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Project::create($validated);

        return redirect()->route('perusahaan.projects.index')
            ->with('success', 'Project berhasil ditambahkan');
    }

    public function edit(Project $project)
    {
        $project->load('guestCardAreas');
        $project->guest_card_area_ids = $project->guestCardAreas->pluck('id')->toArray();
        
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
            'batas_cuti_tahunan' => 'required|integer|min:1|max:365',
        ], [
            'kantor_id.required' => 'Kantor wajib dipilih',
            'nama.required' => 'Nama project wajib diisi',
            'timezone.required' => 'Timezone wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'batas_cuti_tahunan.required' => 'Batas cuti tahunan wajib diisi',
            'batas_cuti_tahunan.min' => 'Batas cuti tahunan minimal 1 hari',
            'batas_cuti_tahunan.max' => 'Batas cuti tahunan maksimal 365 hari',
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

    public function updateGuestBookSettings(Request $request, Project $project)
    {
        $validated = $request->validate([
            'guest_book_mode' => 'required|in:standard_migas,simple',
            'enable_questionnaire' => 'nullable|boolean',
            'enable_guest_card' => 'nullable|boolean',
            'guest_card_area_ids' => 'nullable|array',
            'guest_card_area_ids.*' => 'exists:areas,id',
        ], [
            'guest_book_mode.required' => 'Mode buku tamu wajib dipilih',
            'guest_book_mode.in' => 'Mode buku tamu tidak valid',
            'guest_card_area_ids.*.exists' => 'Area yang dipilih tidak valid',
        ]);

        $validated['enable_questionnaire'] = $request->has('enable_questionnaire');
        $validated['enable_guest_card'] = $request->has('enable_guest_card');

        $project->update($validated);

        // Sync guest card areas
        if ($validated['enable_guest_card'] && !empty($validated['guest_card_area_ids'])) {
            $project->guestCardAreas()->sync($validated['guest_card_area_ids']);
        } else {
            // If guest card is disabled or no areas selected, remove all associations
            $project->guestCardAreas()->detach();
        }

        return redirect()->route('perusahaan.projects.index')
            ->with('success', 'Pengaturan buku tamu berhasil diupdate');
    }

    public function updateBatasCuti(Request $request, Project $project)
    {
        $validated = $request->validate([
            'batas_cuti_tahunan' => 'required|integer|min:1|max:365',
        ], [
            'batas_cuti_tahunan.required' => 'Batas cuti tahunan wajib diisi',
            'batas_cuti_tahunan.min' => 'Batas cuti tahunan minimal 1 hari',
            'batas_cuti_tahunan.max' => 'Batas cuti tahunan maksimal 365 hari',
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Batas cuti tahunan berhasil diupdate',
            'data' => [
                'batas_cuti_tahunan' => $project->batas_cuti_tahunan
            ]
        ]);
    }

    public function getJabatans($id)
    {
        $project = Project::with('jabatans')->findOrFail($id);
        
        return response()->json($project->jabatans);
    }

    public function getAreas(Project $project)
    {
        $areas = $project->areas()->select('id', 'nama')->orderBy('nama')->get();
        
        return response()->json($areas);
    }
}
