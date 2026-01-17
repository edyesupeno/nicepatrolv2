<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectContact;
use Illuminate\Http\Request;

class ProjectContactController extends Controller
{
    public function index(Project $project)
    {
        $contacts = $project->contacts()
            ->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kontak')
            ->orderBy('nama_kontak')
            ->get();

        return view('perusahaan.projects.contacts.index', compact('project', 'contacts'));
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
        ], [
            'nama_kontak.required' => 'Nama kontak wajib diisi',
            'jabatan_kontak.required' => 'Jabatan kontak wajib diisi',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi',
            'email.email' => 'Format email tidak valid',
            'jenis_kontak.required' => 'Jenis kontak wajib dipilih',
            'jenis_kontak.in' => 'Jenis kontak tidak valid',
        ]);

        $validated['project_id'] = $project->id;
        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['is_primary'] = $request->input('is_primary') == '1';
        $validated['is_active'] = $request->input('is_active', '1') == '1'; // Default active

        // Jika set sebagai primary, unset primary lainnya untuk jenis kontak yang sama
        if ($validated['is_primary']) {
            ProjectContact::where('project_id', $project->id)
                ->where('jenis_kontak', $validated['jenis_kontak'])
                ->update(['is_primary' => false]);
        }

        ProjectContact::create($validated);

        return redirect()->route('perusahaan.projects.contacts.index', $project->hash_id)
            ->with('success', 'Kontak berhasil ditambahkan');
    }

    public function edit(Project $project, ProjectContact $contact)
    {
        // Pastikan contact milik project ini
        if ($contact->project_id !== $project->id) {
            abort(404);
        }

        return response()->json($contact);
    }

    public function update(Request $request, Project $project, ProjectContact $contact)
    {
        // Pastikan contact milik project ini
        if ($contact->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'jabatan_kontak' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'jenis_kontak' => 'required|in:polisi,pemadam_kebakaran,ambulans,security,manager_project,supervisor,teknisi,lainnya',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kontak.required' => 'Nama kontak wajib diisi',
            'jabatan_kontak.required' => 'Jabatan kontak wajib diisi',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi',
            'email.email' => 'Format email tidak valid',
            'jenis_kontak.required' => 'Jenis kontak wajib dipilih',
            'jenis_kontak.in' => 'Jenis kontak tidak valid',
        ]);

        $validated['is_primary'] = $request->input('is_primary') == '1';
        $validated['is_active'] = $request->input('is_active', '1') == '1'; // Default active

        // Jika set sebagai primary, unset primary lainnya untuk jenis kontak yang sama
        if ($validated['is_primary']) {
            ProjectContact::where('project_id', $project->id)
                ->where('jenis_kontak', $validated['jenis_kontak'])
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return redirect()->route('perusahaan.projects.contacts.index', $project->hash_id)
            ->with('success', 'Kontak berhasil diupdate');
    }

    public function destroy(Project $project, ProjectContact $contact)
    {
        // Pastikan contact milik project ini
        if ($contact->project_id !== $project->id) {
            abort(404);
        }

        $contact->delete();

        return redirect()->route('perusahaan.projects.contacts.index', $project->hash_id)
            ->with('success', 'Kontak berhasil dihapus');
    }

    /**
     * Get contacts by jenis for API/AJAX
     */
    public function getByJenis(Project $project, $jenis)
    {
        $contacts = $project->activeContacts()
            ->where('jenis_kontak', $jenis)
            ->orderBy('is_primary', 'desc')
            ->orderBy('nama_kontak')
            ->get();

        return response()->json($contacts);
    }
}