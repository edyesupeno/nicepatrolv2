<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\BukuTamu;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuTamuController extends Controller
{
    public function index(Request $request)
    {
        $query = BukuTamu::with(['project', 'area', 'inputBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_tamu', 'ILIKE', "%{$search}%")
                  ->orWhere('perusahaan_tamu', 'ILIKE', "%{$search}%")
                  ->orWhere('keperluan', 'ILIKE', "%{$search}%")
                  ->orWhere('bertemu', 'ILIKE', "%{$search}%")
                  ->orWhere('qr_code', 'ILIKE', "%{$search}%")
                  ->orWhere('no_kartu_pinjam', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('check_in', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in', '<=', $request->date_to);
        }

        // Filter by time period
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->today();
                    break;
                case 'week':
                    $query->thisWeek();
                    break;
                case 'visiting':
                    $query->visiting();
                    break;
            }
        }

        $bukuTamus = $query->orderBy('check_in', 'desc')
                          ->paginate(15);

        $projects = Project::where('is_active', true)->get();
        $areas = \App\Models\Area::get();

        // Statistics
        $stats = [
            'total_today' => BukuTamu::today()->count(),
            'visiting_now' => BukuTamu::visiting()->count(),
            'total_week' => BukuTamu::thisWeek()->count(),
            'total_all' => BukuTamu::count(),
        ];

        return view('perusahaan.buku-tamu.index', compact('bukuTamus', 'projects', 'areas', 'stats'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        $areas = \App\Models\Area::get();

        return view('perusahaan.buku-tamu.create', compact('projects', 'users', 'areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'nama_tamu' => 'required|string|max:255',
            'perusahaan_tamu' => 'nullable|string|max:255',
            'keperluan' => 'required|string|max:255',
            'bertemu' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_identitas' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kontak_darurat_nama' => 'nullable|string|max:255',
            'kontak_darurat_telepon' => 'nullable|string|max:20',
            'kontak_darurat_hubungan' => 'nullable|string|max:100',
            'no_kartu_pinjam' => 'nullable|string|max:50',
            'keterangan_tambahan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'area_id.exists' => 'Area tidak valid',
            'nama_tamu.required' => 'Nama tamu wajib diisi',
            'keperluan.required' => 'Keperluan wajib diisi',
            'foto.image' => 'File foto harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'foto_identitas.image' => 'File foto identitas harus berupa gambar',
            'foto_identitas.mimes' => 'Format foto identitas harus jpeg, png, atau jpg',
            'foto_identitas.max' => 'Ukuran foto identitas maksimal 2MB',
            'kontak_darurat_telepon.max' => 'Nomor telepon maksimal 20 karakter',
            'kontak_darurat_hubungan.max' => 'Hubungan maksimal 100 karakter',
            'no_kartu_pinjam.max' => 'Nomor kartu maksimal 50 karakter',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['input_by'] = auth()->id();
        $validated['status'] = 'sedang_berkunjung';
        $validated['check_in'] = now();

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'buku-tamu/' . time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public', $filename);
            $validated['foto'] = $filename;
        }

        // Handle identity photo upload
        if ($request->hasFile('foto_identitas')) {
            $fotoIdentitas = $request->file('foto_identitas');
            $filename = 'buku-tamu/identitas/' . time() . '_' . $fotoIdentitas->getClientOriginalName();
            $fotoIdentitas->storeAs('public', $filename);
            $validated['foto_identitas'] = $filename;
        }

        $bukuTamu = BukuTamu::create($validated);

        return redirect()->route('perusahaan.buku-tamu.index')
            ->with('success', 'Data tamu berhasil dicatat. QR Code: ' . $bukuTamu->qr_code);
    }

    public function show(BukuTamu $bukuTamu)
    {
        $bukuTamu->load(['project', 'area', 'inputBy']);
        
        return view('perusahaan.buku-tamu.show', compact('bukuTamu'));
    }

    public function edit(BukuTamu $bukuTamu)
    {
        $projects = Project::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        $areas = \App\Models\Area::get();

        return view('perusahaan.buku-tamu.edit', compact('bukuTamu', 'projects', 'users', 'areas'));
    }

    public function update(Request $request, BukuTamu $bukuTamu)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'nama_tamu' => 'required|string|max:255',
            'perusahaan_tamu' => 'nullable|string|max:255',
            'keperluan' => 'required|string|max:255',
            'bertemu' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_identitas' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kontak_darurat_nama' => 'nullable|string|max:255',
            'kontak_darurat_telepon' => 'nullable|string|max:20',
            'kontak_darurat_hubungan' => 'nullable|string|max:100',
            'no_kartu_pinjam' => 'nullable|string|max:50',
            'keterangan_tambahan' => 'nullable|string',
            'status' => 'required|in:sedang_berkunjung,sudah_keluar',
            'catatan' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'area_id.exists' => 'Area tidak valid',
            'nama_tamu.required' => 'Nama tamu wajib diisi',
            'keperluan.required' => 'Keperluan wajib diisi',
            'status.required' => 'Status wajib dipilih',
            'foto.image' => 'File foto harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'foto_identitas.image' => 'File foto identitas harus berupa gambar',
            'foto_identitas.mimes' => 'Format foto identitas harus jpeg, png, atau jpg',
            'foto_identitas.max' => 'Ukuran foto identitas maksimal 2MB',
            'kontak_darurat_telepon.max' => 'Nomor telepon maksimal 20 karakter',
            'kontak_darurat_hubungan.max' => 'Hubungan maksimal 100 karakter',
            'no_kartu_pinjam.max' => 'Nomor kartu maksimal 50 karakter',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($bukuTamu->foto) {
                Storage::delete('public/' . $bukuTamu->foto);
            }

            $foto = $request->file('foto');
            $filename = 'buku-tamu/' . time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public', $filename);
            $validated['foto'] = $filename;
        }

        // Handle identity photo upload
        if ($request->hasFile('foto_identitas')) {
            // Delete old identity photo if exists
            if ($bukuTamu->foto_identitas) {
                Storage::delete('public/' . $bukuTamu->foto_identitas);
            }

            $fotoIdentitas = $request->file('foto_identitas');
            $filename = 'buku-tamu/identitas/' . time() . '_' . $fotoIdentitas->getClientOriginalName();
            $fotoIdentitas->storeAs('public', $filename);
            $validated['foto_identitas'] = $filename;
        }

        // Handle status change
        if ($validated['status'] === 'sudah_keluar' && $bukuTamu->status === 'sedang_berkunjung') {
            $validated['check_out'] = now();
        } elseif ($validated['status'] === 'sedang_berkunjung' && $bukuTamu->status === 'sudah_keluar') {
            $validated['check_out'] = null;
        }

        $bukuTamu->update($validated);

        return redirect()->route('perusahaan.buku-tamu.index')
            ->with('success', 'Data tamu berhasil diupdate');
    }

    public function destroy(BukuTamu $bukuTamu)
    {
        // Delete photos if exist
        if ($bukuTamu->foto) {
            Storage::delete('public/' . $bukuTamu->foto);
        }
        if ($bukuTamu->foto_identitas) {
            Storage::delete('public/' . $bukuTamu->foto_identitas);
        }

        $bukuTamu->delete();

        return redirect()->route('perusahaan.buku-tamu.index')
            ->with('success', 'Data tamu berhasil dihapus');
    }

    /**
     * Check out guest
     */
    public function checkOut(Request $request, BukuTamu $bukuTamu)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        $bukuTamu->checkOut($validated['catatan'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Tamu berhasil check out',
            'data' => [
                'status' => $bukuTamu->status_label,
                'check_out' => $bukuTamu->check_out->format('d M Y H:i'),
                'duration' => $bukuTamu->duration,
            ]
        ]);
    }

    /**
     * Get guest by QR code
     */
    public function getByQrCode(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $bukuTamu = BukuTamu::where('qr_code', $validated['qr_code'])
                           ->with(['project', 'inputBy'])
                           ->first();

        if (!$bukuTamu) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bukuTamu,
        ]);
    }

    /**
     * Generate QR Code image
     */
    public function generateQrCode(BukuTamu $bukuTamu)
    {
        $bukuTamu->load([
            'project', 
            'area',
            'perusahaan:id,nama,logo',
            'inputBy:id,name'
        ]);
        
        return view('perusahaan.buku-tamu.qr-code', compact('bukuTamu'));
    }
}