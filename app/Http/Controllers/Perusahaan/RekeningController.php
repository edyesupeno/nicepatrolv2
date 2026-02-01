<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Rekening;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RekeningController extends Controller
{
    // Middleware sudah diatur di routes group

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        $query = Rekening::with(['project:id,nama'])
            ->select('id', 'project_id', 'nama_rekening', 'nomor_rekening', 'nama_bank', 'nama_pemilik', 'jenis_rekening', 'saldo_saat_ini', 'mata_uang', 'is_active', 'is_primary', 'warna_card')
            ->orderBy('is_primary', 'desc')
            ->orderBy('project_id')
            ->orderBy('nama_rekening');

        // Filter berdasarkan project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter berdasarkan jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_rekening', $request->jenis);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_rekening', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_bank', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%");
            });
        }

        $rekenings = $query->paginate(12);

        // Statistics
        $stats = [
            'total' => Rekening::count(),
            'active' => Rekening::active()->count(),
            'total_saldo' => Rekening::active()->sum('saldo_saat_ini'),
            'by_project' => Rekening::active()
                ->selectRaw('project_id, COUNT(*) as count, SUM(saldo_saat_ini) as total_saldo')
                ->with('project:id,nama')
                ->groupBy('project_id')
                ->get()
        ];

        return view('perusahaan.rekening.index', compact('rekenings', 'projects', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $colors = Rekening::getAvailableColors();
        
        return view('perusahaan.rekening.create', compact('projects', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_rekening' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:50|unique:rekenings,nomor_rekening',
            'nama_bank' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'jenis_rekening' => 'required|in:operasional,payroll,investasi,emergency,lainnya',
            'saldo_awal' => 'required|numeric|min:0',
            'mata_uang' => 'required|string|max:3',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'warna_card' => 'required|string|max:7'
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama_rekening.required' => 'Nama rekening harus diisi',
            'nomor_rekening.required' => 'Nomor rekening harus diisi',
            'nomor_rekening.unique' => 'Nomor rekening sudah terdaftar',
            'nama_bank.required' => 'Nama bank harus diisi',
            'nama_pemilik.required' => 'Nama pemilik harus diisi',
            'jenis_rekening.required' => 'Jenis rekening harus dipilih',
            'saldo_awal.required' => 'Saldo awal harus diisi',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka',
            'saldo_awal.min' => 'Saldo awal tidak boleh negatif',
            'mata_uang.required' => 'Mata uang harus diisi',
            'warna_card.required' => 'Warna card harus dipilih'
        ]);

        // Auto-assign perusahaan_id
        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['saldo_saat_ini'] = $validated['saldo_awal'];

        // Jika is_primary true, pastikan hanya satu per project
        if ($validated['is_primary'] ?? false) {
            Rekening::where('project_id', $validated['project_id'])
                ->update(['is_primary' => false]);
        }

        $rekening = Rekening::create($validated);

        return redirect()->route('perusahaan.keuangan.rekening.index')
            ->with('success', 'Rekening berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rekening $rekening)
    {
        $rekening->load(['project:id,nama', 'perusahaan:id,nama']);
        
        return view('perusahaan.rekening.show', compact('rekening'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rekening $rekening)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $colors = Rekening::getAvailableColors();
        
        return view('perusahaan.rekening.edit', compact('rekening', 'projects', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rekening $rekening)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_rekening' => 'required|string|max:255',
            'nomor_rekening' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rekenings')->ignore($rekening->id)
            ],
            'nama_bank' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'jenis_rekening' => 'required|in:operasional,payroll,investasi,emergency,lainnya',
            'saldo_awal' => 'required|numeric|min:0',
            'mata_uang' => 'required|string|max:3',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'warna_card' => 'required|string|max:7'
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama_rekening.required' => 'Nama rekening harus diisi',
            'nomor_rekening.required' => 'Nomor rekening harus diisi',
            'nomor_rekening.unique' => 'Nomor rekening sudah terdaftar',
            'nama_bank.required' => 'Nama bank harus diisi',
            'nama_pemilik.required' => 'Nama pemilik harus diisi',
            'jenis_rekening.required' => 'Jenis rekening harus dipilih',
            'saldo_awal.required' => 'Saldo awal harus diisi',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka',
            'saldo_awal.min' => 'Saldo awal tidak boleh negatif',
            'mata_uang.required' => 'Mata uang harus diisi',
            'warna_card.required' => 'Warna card harus dipilih'
        ]);

        // Jika is_primary true, pastikan hanya satu per project
        if ($validated['is_primary'] ?? false) {
            Rekening::where('project_id', $validated['project_id'])
                ->where('id', '!=', $rekening->id)
                ->update(['is_primary' => false]);
        }

        $rekening->update($validated);

        return redirect()->route('perusahaan.keuangan.rekening.index')
            ->with('success', 'Rekening berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rekening $rekening)
    {
        try {
            $rekening->delete();
            
            return redirect()->route('perusahaan.keuangan.rekening.index')
                ->with('success', 'Rekening berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('perusahaan.keuangan.rekening.index')
                ->with('error', 'Gagal menghapus rekening: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif rekening
     */
    public function toggleStatus(Rekening $rekening)
    {
        $rekening->update(['is_active' => !$rekening->is_active]);
        
        $status = $rekening->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return response()->json([
            'success' => true,
            'message' => "Rekening berhasil {$status}",
            'is_active' => $rekening->is_active
        ]);
    }

    /**
     * Set rekening sebagai primary
     */
    public function setPrimary(Rekening $rekening)
    {
        try {
            $rekening->setPrimary();
            
            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil dijadikan rekening utama'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah rekening utama: ' . $e->getMessage()
            ], 500);
        }
    }
}