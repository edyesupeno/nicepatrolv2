<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\PenerimaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PenerimaanBarangController extends Controller
{
    public function index()
    {
        $penerimaanBarangs = PenerimaanBarang::with(['perusahaan', 'project', 'area'])
            ->orderBy('tanggal_terima', 'desc')
            ->paginate(20);
        
        // Stats untuk dashboard cards
        $stats = [
            'total_barang' => PenerimaanBarang::count(),
            'hari_ini' => PenerimaanBarang::hariIni()->count(),
            'kondisi_baik' => PenerimaanBarang::byKondisi('Baik')->count(),
            'perlu_perhatian' => PenerimaanBarang::whereIn('kondisi_barang', ['Rusak', 'Segel Terbuka'])->count(),
        ];
        
        return view('perusahaan.penerimaan-barang.index', compact('penerimaanBarangs', 'stats'));
    }

    public function create()
    {
        $projects = \App\Models\Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.penerimaan-barang.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_barang' => 'required|in:Dokumen,Material,Elektronik,Logistik,Lainnya',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'kondisi_barang' => 'required|in:Baik,Rusak,Segel Terbuka',
            'pengirim' => 'required|in:Kurir,Client,Lainnya',
            'tujuan_departemen' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'pos' => 'nullable|string|max:255',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'tanggal_terima' => 'required|date',
            'status' => 'required|string',
            'petugas_penerima' => 'required|string|max:255',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi',
            'kategori_barang.required' => 'Kategori barang wajib dipilih',
            'kategori_barang.in' => 'Kategori barang tidak valid',
            'jumlah_barang.required' => 'Jumlah barang wajib diisi',
            'jumlah_barang.integer' => 'Jumlah barang harus berupa angka',
            'jumlah_barang.min' => 'Jumlah barang minimal 1',
            'satuan.required' => 'Satuan wajib dipilih',
            'kondisi_barang.required' => 'Kondisi barang wajib dipilih',
            'kondisi_barang.in' => 'Kondisi barang tidak valid',
            'pengirim.required' => 'Pengirim wajib dipilih',
            'pengirim.in' => 'Pengirim tidak valid',
            'tujuan_departemen.required' => 'Tujuan departemen wajib diisi',
            'project_id.exists' => 'Project yang dipilih tidak valid',
            'area_id.exists' => 'Area yang dipilih tidak valid',
            'pos.max' => 'POS maksimal 255 karakter',
            'foto_barang.image' => 'File harus berupa gambar',
            'foto_barang.mimes' => 'Format gambar harus JPG, PNG, atau JPEG',
            'foto_barang.max' => 'Ukuran gambar maksimal 5MB',
        ]);

        // Auto-assign perusahaan_id (sesuai multi-tenancy rules)
        if (!auth()->user()->isSuperAdmin()) {
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        }

        // Auto-assign created_by (WAJIB untuk audit trail)
        $validated['created_by'] = auth()->id();

        // Generate nomor penerimaan
        $validated['nomor_penerimaan'] = $this->generateNomorPenerimaan();

        // Handle foto upload
        if ($request->hasFile('foto_barang')) {
            $file = $request->file('foto_barang');
            $filename = 'penerimaan_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('penerimaan-barang', $filename, 'public');
            $validated['foto_barang'] = $path;
        }

        try {
            $penerimaanBarang = PenerimaanBarang::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data penerimaan barang berhasil disimpan',
                'redirect' => route('perusahaan.penerimaan-barang.index')
            ]);

        } catch (\Exception $e) {
            // Delete uploaded file if database save fails
            if (isset($validated['foto_barang'])) {
                Storage::disk('public')->delete($validated['foto_barang']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penerimaanBarang = PenerimaanBarang::findOrFail($id);
        
        return view('perusahaan.penerimaan-barang.show', compact('penerimaanBarang'));
    }

    public function edit($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penerimaanBarang = PenerimaanBarang::findOrFail($id);
        $projects = \App\Models\Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.penerimaan-barang.edit', compact('penerimaanBarang', 'projects'));
    }

    public function update(Request $request, $hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penerimaanBarang = PenerimaanBarang::findOrFail($id);
        
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_barang' => 'required|in:Dokumen,Material,Elektronik,Logistik,Lainnya',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'kondisi_barang' => 'required|in:Baik,Rusak,Segel Terbuka',
            'pengirim' => 'required|in:Kurir,Client,Lainnya',
            'tujuan_departemen' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'area_id' => 'nullable|exists:areas,id',
            'pos' => 'nullable|string|max:255',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'keterangan' => 'nullable|string',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto_barang')) {
            // Delete old photo
            if ($penerimaanBarang->foto_barang) {
                Storage::disk('public')->delete($penerimaanBarang->foto_barang);
            }
            
            $file = $request->file('foto_barang');
            $filename = 'penerimaan_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('penerimaan-barang', $filename, 'public');
            $validated['foto_barang'] = $path;
        }

        try {
            $penerimaanBarang->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data penerimaan barang berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penerimaanBarang = PenerimaanBarang::findOrFail($id);
        
        try {
            // Delete photo file
            if ($penerimaanBarang->foto_barang) {
                Storage::disk('public')->delete($penerimaanBarang->foto_barang);
            }
            
            $penerimaanBarang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data penerimaan barang berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateNomorPenerimaan()
    {
        $prefix = 'PB';
        $date = now()->format('Ymd');
        
        // Get last number for today
        $lastNumber = PenerimaanBarang::where('nomor_penerimaan', 'like', $prefix . $date . '%')
            ->orderBy('nomor_penerimaan', 'desc')
            ->first();
        
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber->nomor_penerimaan, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . $date . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    public function getAreasByProject($projectId)
    {
        if (!$projectId) {
            return response()->json([]);
        }
        
        $areas = \App\Models\Area::where('project_id', $projectId)
            ->select('id', 'nama', 'alamat')
            ->orderBy('nama')
            ->get();
        
        return response()->json($areas);
    }

    public function searchPos(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $posList = PenerimaanBarang::where('pos', 'LIKE', '%' . $query . '%')
            ->whereNotNull('pos')
            ->select('pos')
            ->distinct()
            ->orderBy('pos')
            ->limit(10)
            ->pluck('pos');
        
        return response()->json($posList);
    }
}