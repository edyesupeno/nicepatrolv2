<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KategoriPerlengkapan;
use App\Models\ItemPerlengkapan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PerlengkapanController extends Controller
{
    public function index(Request $request)
    {
        $query = KategoriPerlengkapan::select([
                'id',
                'project_id', 
                'created_by',
                'nama_kategori',
                'deskripsi',
                'is_active',
                'created_at'
            ])
            ->with([
                'project:id,nama', 
                'createdBy:id,name'
            ])
            ->withCount(['items', 'activeItems']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search by nama kategori
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $kategoris = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();

        // Statistics - optimized with specific selects
        $stats = [
            'total_kategori' => KategoriPerlengkapan::count(),
            'active_kategori' => KategoriPerlengkapan::where('is_active', true)->count(),
            'total_items' => ItemPerlengkapan::count(),
            'low_stock_items' => ItemPerlengkapan::whereColumn('stok_tersedia', '<=', 'stok_minimum')
                                                ->where('stok_minimum', '>', 0)
                                                ->count(),
        ];

        return view('perusahaan.perlengkapan.index', compact('kategoris', 'projects', 'stats'));
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.perlengkapan.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'nama_kategori.required' => 'Nama kategori wajib diisi',
        ]);

        // Auto-assign perusahaan_id dan created_by - optimized
        $user = auth()->user();
        $validated['perusahaan_id'] = $user->perusahaan_id;
        $validated['created_by'] = $user->id;
        $validated['is_active'] = $request->has('is_active');

        try {
            $kategori = KategoriPerlengkapan::create($validated);

            return redirect()->route('perusahaan.perlengkapan.show', $kategori->hash_id)
                ->with('success', 'Kategori perlengkapan berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $kategori = KategoriPerlengkapan::select([
                'id',
                'project_id',
                'created_by', 
                'nama_kategori',
                'deskripsi',
                'is_active',
                'created_at'
            ])
            ->with([
                'project:id,nama', 
                'createdBy:id,name'
            ])
            ->findOrFail($id);

        // Get items with stock info
        $items = ItemPerlengkapan::select([
                'id',
                'kategori_perlengkapan_id',
                'created_by',
                'nama_item',
                'deskripsi', 
                'satuan',
                'stok_tersedia',
                'stok_minimum',
                'harga_satuan',
                'foto_item',
                'is_active'
            ])
            ->where('kategori_perlengkapan_id', $kategori->id)
            ->with('createdBy:id,name')
            ->orderBy('nama_item')
            ->get();
        
        return view('perusahaan.perlengkapan.show', compact('kategori', 'items'));
    }

    public function edit($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $kategori = KategoriPerlengkapan::select([
                'id',
                'project_id',
                'nama_kategori', 
                'deskripsi',
                'is_active'
            ])
            ->findOrFail($id);
            
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.perlengkapan.edit', compact('kategori', 'projects'));
    }

    public function update(Request $request, $hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $kategori = KategoriPerlengkapan::findOrFail($id);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        // Handle checkbox properly
        $validated['is_active'] = $request->has('is_active');

        try {
            $kategori->update($validated);

            return redirect()->route('perusahaan.perlengkapan.show', $kategori->hash_id)
                ->with('success', 'Kategori perlengkapan berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $kategori = KategoriPerlengkapan::findOrFail($id);
        
        try {
            // Check if kategori has items
            if ($kategori->items()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki item perlengkapan');
            }
            
            $kategori->delete();

            return redirect()->route('perusahaan.perlengkapan.index')
                ->with('success', 'Kategori perlengkapan berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // Item Management Methods
    public function storeItem(Request $request, $kategoriHashId)
    {
        try {
            // Decode hash ID to get real ID
            $kategoriId = \Vinkla\Hashids\Facades\Hashids::decode($kategoriHashId)[0] ?? null;
            if (!$kategoriId) {
                \Log::error('Invalid kategori hash ID: ' . $kategoriHashId);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid kategori ID'
                ], 404);
            }

            $kategori = KategoriPerlengkapan::findOrFail($kategoriId);
            \Log::info('Found kategori: ' . $kategori->nama_kategori);

            $validated = $request->validate([
                'nama_item' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'satuan' => 'required|string|max:50',
                'stok_awal' => 'required|integer|min:0',
                'stok_minimum' => 'nullable|integer|min:0',
                'harga_satuan' => 'nullable|numeric|min:0',
                'foto_item' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            \Log::info('Validation passed', $validated);

            $validated['kategori_perlengkapan_id'] = $kategori->id;
            $validated['created_by'] = auth()->id();
            $validated['stok_tersedia'] = $validated['stok_awal'];
            $validated['is_active'] = $request->has('is_active');

            // Handle foto upload
            if ($request->hasFile('foto_item')) {
                try {
                    $file = $request->file('foto_item');
                    $filename = 'item_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Ensure directory exists
                    $directory = 'perlengkapan/items';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }
                    
                    $path = $file->storeAs($directory, $filename, 'public');
                    $validated['foto_item'] = $path;
                    \Log::info('File uploaded: ' . $path);
                } catch (\Exception $e) {
                    \Log::error('File upload error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengupload foto: ' . $e->getMessage()
                    ], 500);
                }
            }

            \Log::info('Creating item with data:', $validated);
            $item = ItemPerlengkapan::create($validated);
            \Log::info('Item created successfully: ' . $item->id);

            return response()->json([
                'success' => true,
                'message' => 'Item perlengkapan berhasil ditambahkan',
                'data' => $item
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store item error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Clean up uploaded file if database save fails
            if (isset($validated['foto_item'])) {
                Storage::disk('public')->delete($validated['foto_item']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateItem(Request $request, $kategoriHashId, $itemHashId)
    {
        try {
            // Decode hash IDs
            $kategoriId = \Vinkla\Hashids\Facades\Hashids::decode($kategoriHashId)[0] ?? null;
            $itemId = \Vinkla\Hashids\Facades\Hashids::decode($itemHashId)[0] ?? null;
            
            if (!$kategoriId || !$itemId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 404);
            }

            $kategori = KategoriPerlengkapan::findOrFail($kategoriId);
            $item = ItemPerlengkapan::where('kategori_perlengkapan_id', $kategori->id)
                                  ->findOrFail($itemId);

            $validated = $request->validate([
                'nama_item' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'satuan' => 'required|string|max:50',
                'stok_minimum' => 'nullable|integer|min:0',
                'harga_satuan' => 'nullable|numeric|min:0',
                'foto_item' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $validated['is_active'] = $request->has('is_active');

            // Handle foto upload
            if ($request->hasFile('foto_item')) {
                try {
                    // Delete old photo
                    if ($item->foto_item) {
                        Storage::disk('public')->delete($item->foto_item);
                    }
                    
                    $file = $request->file('foto_item');
                    $filename = 'item_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Ensure directory exists
                    $directory = 'perlengkapan/items';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }
                    
                    $path = $file->storeAs($directory, $filename, 'public');
                    $validated['foto_item'] = $path;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengupload foto: ' . $e->getMessage()
                    ], 500);
                }
            }

            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item perlengkapan berhasil diperbarui',
                'data' => $item
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getItem($kategoriHashId, $itemHashId)
    {
        try {
            // Decode hash IDs
            $kategoriId = \Vinkla\Hashids\Facades\Hashids::decode($kategoriHashId)[0] ?? null;
            $itemId = \Vinkla\Hashids\Facades\Hashids::decode($itemHashId)[0] ?? null;
            
            if (!$kategoriId || !$itemId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 404);
            }

            $kategori = KategoriPerlengkapan::findOrFail($kategoriId);
            $item = ItemPerlengkapan::select([
                    'id',
                    'kategori_perlengkapan_id',
                    'nama_item',
                    'deskripsi',
                    'satuan',
                    'stok_minimum',
                    'harga_satuan',
                    'foto_item',
                    'is_active'
                ])
                ->where('kategori_perlengkapan_id', $kategori->id)
                ->findOrFail($itemId);

            return response()->json([
                'success' => true,
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
    }

    public function destroyItem($kategoriHashId, $itemHashId)
    {
        // Decode hash IDs
        $kategoriId = \Vinkla\Hashids\Facades\Hashids::decode($kategoriHashId)[0] ?? null;
        $itemId = \Vinkla\Hashids\Facades\Hashids::decode($itemHashId)[0] ?? null;
        
        if (!$kategoriId || !$itemId) {
            return response()->json(['error' => 'Invalid ID'], 404);
        }

        $kategori = KategoriPerlengkapan::findOrFail($kategoriId);
        $item = ItemPerlengkapan::where('kategori_perlengkapan_id', $kategori->id)
                              ->findOrFail($itemId);

        try {
            // Check if item has been distributed
            if ($item->penyerahanItems()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus item yang sudah pernah diserahkan'
                ], 400);
            }

            // Delete photo file
            if ($item->foto_item) {
                Storage::disk('public')->delete($item->foto_item);
            }
            
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item perlengkapan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStok(Request $request, $kategoriHashId, $itemHashId)
    {
        // Decode hash IDs
        $kategoriId = \Vinkla\Hashids\Facades\Hashids::decode($kategoriHashId)[0] ?? null;
        $itemId = \Vinkla\Hashids\Facades\Hashids::decode($itemHashId)[0] ?? null;
        
        if (!$kategoriId || !$itemId) {
            return response()->json(['error' => 'Invalid ID'], 404);
        }

        $kategori = KategoriPerlengkapan::findOrFail($kategoriId);
        $item = ItemPerlengkapan::where('kategori_perlengkapan_id', $kategori->id)
                              ->findOrFail($itemId);

        $validated = $request->validate([
            'operasi' => 'required|in:tambah,kurang',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $stokLama = $item->stok_tersedia;
            
            // Use the updateStok method which includes history tracking
            $item->updateStok(
                $validated['jumlah'],
                $validated['operasi'],
                $validated['keterangan'] ?? 'Penyesuaian stok manual',
                'manual',
                null
            );

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diperbarui',
                'data' => [
                    'stok_lama' => $stokLama,
                    'stok_baru' => $item->stok_tersedia,
                    'perubahan' => $validated['operasi'] === 'tambah' ? "+{$validated['jumlah']}" : "-{$validated['jumlah']}"
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStockHistory(Request $request, $kategoriHashId, $itemHashId)
    {
        // Decode hash IDs
        $kategoriId = \Vinkla\Hashids\Facades\Hashids::decode($kategoriHashId)[0] ?? null;
        $itemId = \Vinkla\Hashids\Facades\Hashids::decode($itemHashId)[0] ?? null;
        
        if (!$kategoriId || !$itemId) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            $kategori = KategoriPerlengkapan::findOrFail($kategoriId);
            $item = ItemPerlengkapan::where('kategori_perlengkapan_id', $kategori->id)
                                  ->findOrFail($itemId);

            // Get stock history with relationships
            $histories = \App\Models\ItemStockHistory::select([
                    'id',
                    'tipe_transaksi',
                    'jumlah',
                    'stok_sebelum',
                    'stok_sesudah',
                    'keterangan',
                    'referensi_tipe',
                    'referensi_id',
                    'created_by',
                    'created_at'
                ])
                ->where('item_perlengkapan_id', $item->id)
                ->with('createdBy:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(100) // Limit to last 100 transactions
                ->get();

            // Add computed attributes
            $histories->each(function ($history) {
                $history->tipe_transaksi_text = $history->tipe_transaksi_text;
                $history->tipe_transaksi_color = $history->tipe_transaksi_color;
                $history->tipe_transaksi_icon = $history->tipe_transaksi_icon;
                $history->formatted_jumlah = $history->formatted_jumlah;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'item' => [
                        'id' => $item->id,
                        'nama_item' => $item->nama_item,
                        'stok_tersedia' => $item->stok_tersedia,
                        'satuan' => $item->satuan
                    ],
                    'histories' => $histories
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat stok: ' . $e->getMessage()
            ], 500);
        }
    }
}