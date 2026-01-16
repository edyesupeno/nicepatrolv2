<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Project;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::with('project');

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search by kode_shift or nama_shift
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_shift', 'like', "%{$search}%")
                  ->orWhere('nama_shift', 'like', "%{$search}%");
            });
        }

        $shifts = $query->orderBy('project_id')
            ->orderBy('kode_shift')
            ->get();
        
        $projects = Project::orderBy('nama')->get();
        
        return view('perusahaan.shifts.index', compact('shifts', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'kode_shift' => 'required|string|max:10',
            'nama_shift' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'durasi_istirahat' => 'required|integer|min:0',
            'toleransi_keterlambatan' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'warna' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'kode_shift.required' => 'Kode shift harus diisi',
            'kode_shift.max' => 'Kode shift maksimal 10 karakter',
            'nama_shift.required' => 'Nama shift harus diisi',
            'nama_shift.max' => 'Nama shift maksimal 100 karakter',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid',
            'durasi_istirahat.required' => 'Durasi istirahat harus diisi',
            'durasi_istirahat.integer' => 'Durasi istirahat harus berupa angka',
            'durasi_istirahat.min' => 'Durasi istirahat minimal 0 menit',
            'toleransi_keterlambatan.required' => 'Toleransi keterlambatan harus diisi',
            'toleransi_keterlambatan.integer' => 'Toleransi keterlambatan harus berupa angka',
            'toleransi_keterlambatan.min' => 'Toleransi keterlambatan minimal 0 menit',
            'warna.required' => 'Warna harus dipilih',
            'warna.regex' => 'Format warna tidak valid',
        ]);

        // Auto-assign perusahaan_id
        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        // Validasi kode shift unik per project
        $exists = Shift::where('project_id', $validated['project_id'])
            ->where('kode_shift', $validated['kode_shift'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kode shift sudah digunakan di project ini');
        }

        Shift::create($validated);

        return redirect()->route('perusahaan.kehadiran.manajemen-shift')
            ->with('success', 'Shift berhasil ditambahkan');
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'kode_shift' => 'required|string|max:10',
            'nama_shift' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'durasi_istirahat' => 'required|integer|min:0',
            'toleransi_keterlambatan' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'warna' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'kode_shift.required' => 'Kode shift harus diisi',
            'kode_shift.max' => 'Kode shift maksimal 10 karakter',
            'nama_shift.required' => 'Nama shift harus diisi',
            'nama_shift.max' => 'Nama shift maksimal 100 karakter',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid',
            'durasi_istirahat.required' => 'Durasi istirahat harus diisi',
            'durasi_istirahat.integer' => 'Durasi istirahat harus berupa angka',
            'durasi_istirahat.min' => 'Durasi istirahat minimal 0 menit',
            'toleransi_keterlambatan.required' => 'Toleransi keterlambatan harus diisi',
            'toleransi_keterlambatan.integer' => 'Toleransi keterlambatan harus berupa angka',
            'toleransi_keterlambatan.min' => 'Toleransi keterlambatan minimal 0 menit',
            'warna.required' => 'Warna harus dipilih',
            'warna.regex' => 'Format warna tidak valid',
        ]);

        // Validasi kode shift unik per project (kecuali dirinya sendiri)
        $exists = Shift::where('project_id', $validated['project_id'])
            ->where('kode_shift', $validated['kode_shift'])
            ->where('id', '!=', $shift->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kode shift sudah digunakan di project ini');
        }

        $shift->update($validated);

        return redirect()->route('perusahaan.kehadiran.manajemen-shift')
            ->with('success', 'Shift berhasil diperbarui');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('perusahaan.kehadiran.manajemen-shift')
            ->with('success', 'Shift berhasil dihapus');
    }
}
