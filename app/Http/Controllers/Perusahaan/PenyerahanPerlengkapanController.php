<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\PenyerahanPerlengkapan;
use App\Models\PenyerahanPerlengkapanItem;
use App\Models\PenyerahanPerlengkapanKaryawan;
use App\Models\KategoriPerlengkapan;
use App\Models\ItemPerlengkapan;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyerahanPerlengkapanController extends Controller
{
    public function index(Request $request)
    {
        $query = PenyerahanPerlengkapan::select([
                'id',
                'project_id',
                'created_by',
                'karyawan_id',
                'tanggal_mulai',
                'tanggal_selesai',
                'status',
                'keterangan',
                'created_at'
            ])
            ->with([
                'project:id,nama',
                'karyawan:id,nama_lengkap,jabatan_id',
                'karyawan.jabatan:id,nama',
                'createdBy:id,name'
            ])
            ->withCount('items');

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by karyawan name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_sampai);
        }

        $penyerahans = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();

        // Statistics
        $stats = [
            'total_penyerahan' => PenyerahanPerlengkapan::count(),
            'draft' => PenyerahanPerlengkapan::where('status', 'draft')->count(),
            'diserahkan' => PenyerahanPerlengkapan::where('status', 'diserahkan')->count(),
            'dikembalikan' => PenyerahanPerlengkapan::where('status', 'dikembalikan')->count(),
        ];

        return view('perusahaan.penyerahan-perlengkapan.index', compact('penyerahans', 'projects', 'stats'));
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.penyerahan-perlengkapan.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
        ]);

        try {
            // Auto-assign perusahaan_id dan created_by
            $user = auth()->user();
            $validated['perusahaan_id'] = $user->perusahaan_id;
            $validated['created_by'] = $user->id;
            $validated['status'] = 'draft'; // Status awal adalah draft

            // Create penyerahan record
            $penyerahan = PenyerahanPerlengkapan::create($validated);

            return redirect()->route('perusahaan.penyerahan-perlengkapan.serahkan-item-page', $penyerahan->hash_id)
                ->with('success', 'Jadwal penyerahan perlengkapan berhasil dibuat');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($hashId)
    {
        // Redirect directly to serahkan item page since we don't need detail view anymore
        return redirect()->route('perusahaan.penyerahan-perlengkapan.serahkan-item-page', $hashId);
    }

    public function edit($hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::select([
                'id',
                'project_id',
                'karyawan_id',
                'tanggal_mulai',
                'tanggal_selesai',
                'status',
                'keterangan'
            ])
            ->with([
                'items.item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                'items.item.kategori:id,nama_kategori'
            ])
            ->findOrFail($id);

        // Only allow editing if status is diserahkan (sedang diserahkan) or dikembalikan (selesai diserahkan)
        if (!in_array($penyerahan->status, ['diserahkan', 'dikembalikan'])) {
            return redirect()->back()
                ->with('error', 'Hanya penyerahan dengan status sedang diserahkan atau selesai diserahkan yang dapat diedit');
        }
            
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.penyerahan-perlengkapan.edit', compact('penyerahan', 'projects'));
    }

    public function update(Request $request, $hashId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        // Only allow updating if status is draft, diserahkan, or dikembalikan
        if (!in_array($penyerahan->status, ['draft', 'diserahkan', 'dikembalikan'])) {
            return redirect()->back()
                ->with('error', 'Hanya penyerahan dengan status draft, sedang diserahkan, atau selesai diserahkan yang dapat diupdate');
        }
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
        ]);

        try {
            $penyerahan->update($validated);

            return redirect()->route('perusahaan.penyerahan-perlengkapan.serahkan-item-page', $penyerahan->hash_id)
                ->with('success', 'Penyerahan perlengkapan berhasil diperbarui');

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
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);
        
        try {
            // Only allow deletion if status is draft, diserahkan, or dikembalikan
            if (!in_array($penyerahan->status, ['draft', 'diserahkan', 'dikembalikan'])) {
                return redirect()->back()
                    ->with('error', 'Hanya penyerahan dengan status draft, sedang diserahkan, atau selesai diserahkan yang dapat dihapus');
            }

            DB::transaction(function () use ($penyerahan) {
                // Restore stock for all items (if any)
                foreach ($penyerahan->items as $penyerahanItem) {
                    $penyerahanItem->item->updateStok(
                        $penyerahanItem->jumlah_diserahkan, 
                        'tambah',
                        "Pembatalan penyerahan - pengembalian stok",
                        'pembatalan',
                        $penyerahan->id
                    );
                }
                
                // Delete penyerahan items first
                $penyerahan->items()->delete();
                
                // Delete penyerahan record
                $penyerahan->delete();
            });

            return redirect()->route('perusahaan.penyerahan-perlengkapan.index')
                ->with('success', 'Penyerahan perlengkapan berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // API Methods for AJAX requests
    public function getKaryawanByJabatan(Request $request)
    {
        $jabatanId = $request->get('jabatan_id');
        $projectId = $request->get('project_id');

        $karyawans = Karyawan::select('id', 'nama_lengkap', 'jabatan_id')
            ->where('jabatan_id', $jabatanId)
            ->where('project_id', $projectId)
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }

    public function getJabatanByProject(Request $request)
    {
        $projectId = $request->get('project_id');

        $jabatans = Jabatan::select('id', 'nama')
            ->whereHas('karyawans', function($query) use ($projectId) {
                $query->where('project_id', $projectId)
                      ->where('is_active', true);
            })
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jabatans
        ]);
    }

    public function getItemsByKategori(Request $request)
    {
        $kategoriId = $request->get('kategori_id');
        $search = $request->get('search');

        if (!$kategoriId) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori ID is required'
            ], 400);
        }

        try {
            // First verify that the kategori belongs to the current user's perusahaan
            $kategori = KategoriPerlengkapan::find($kategoriId);
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found'
                ], 404);
            }

            $query = ItemPerlengkapan::select([
                    'id',
                    'nama_item',
                    'satuan',
                    'stok_tersedia',
                    'stok_minimum',
                    'foto_item'
                ])
                ->where('kategori_perlengkapan_id', $kategoriId)
                ->where('is_active', true)
                ->where('stok_tersedia', '>', 0);

            if ($search) {
                $query->where('nama_item', 'like', "%{$search}%");
            }

            $items = $query->orderBy('nama_item')->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getItemsByKategori: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKategoriByProject(Request $request)
    {
        $projectId = $request->get('project_id');

        $kategoris = KategoriPerlengkapan::select('id', 'nama_kategori')
            ->where('project_id', $projectId)
            ->where('is_active', true)
            ->whereHas('items', function($query) {
                $query->where('is_active', true)
                      ->where('stok_tersedia', '>', 0);
            })
            ->orderBy('nama_kategori')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kategoris
        ]);
    }

    public function pilihKaryawanPage($penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::select([
                'id',
                'project_id',
                'tanggal_mulai',
                'tanggal_selesai',
                'status',
                'keterangan'
            ])
            ->with('project:id,nama')
            ->findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya jadwal dengan status draft yang dapat diubah');
        }

        // Get all jabatans for the project
        $jabatans = Jabatan::select('id', 'nama')
            ->whereHas('karyawans', function($query) use ($penyerahan) {
                $query->where('project_id', $penyerahan->project_id)
                      ->where('is_active', true);
            })
            ->orderBy('nama')
            ->get();

        // Get selected karyawan IDs
        $selectedKaryawanIds = PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $penyerahan->id)
            ->pluck('karyawan_id')
            ->toArray();

        return view('perusahaan.penyerahan-perlengkapan.pilih-karyawan', compact('penyerahan', 'jabatans', 'selectedKaryawanIds'));
    }

    public function pilihItemPage($penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::select([
                'id',
                'project_id',
                'tanggal_mulai',
                'tanggal_selesai',
                'status',
                'keterangan'
            ])
            ->with('project:id,nama')
            ->findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya jadwal dengan status draft yang dapat diubah');
        }

        // Get available kategoris for the project
        $kategoris = KategoriPerlengkapan::select('id', 'nama_kategori')
            ->where('project_id', $penyerahan->project_id)
            ->where('is_active', true)
            ->whereHas('items', function($query) {
                $query->where('is_active', true)
                      ->where('stok_tersedia', '>', 0);
            })
            ->orderBy('nama_kategori')
            ->get();

        // Get selected karyawan count
        $selectedKaryawanCount = $penyerahan->karyawans()->count();

        // Get selected items with their quantities
        $selectedItems = $penyerahan->items()
            ->select('item_perlengkapan_id as item_id', 'jumlah_diserahkan as jumlah')
            ->groupBy('item_perlengkapan_id', 'jumlah_diserahkan')
            ->get()
            ->toArray();

        return view('perusahaan.penyerahan-perlengkapan.pilih-item', compact('penyerahan', 'kategoris', 'selectedKaryawanCount', 'selectedItems'));
    }

    public function serahkanItemPage($penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            abort(404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::select([
                'id',
                'project_id',
                'tanggal_mulai',
                'tanggal_selesai',
                'status',
                'keterangan'
            ])
            ->with('project:id,nama')
            ->findOrFail($id);

        if (!in_array($penyerahan->status, ['draft', 'diserahkan'])) {
            return redirect()->back()
                ->with('error', 'Penyerahan sudah tidak dapat diubah');
        }

        // Get karyawans with their items
        $karyawans = $penyerahan->karyawanList()
            ->select(['karyawans.id', 'karyawans.nama_lengkap', 'karyawans.nik_karyawan', 'karyawans.jabatan_id'])
            ->with([
                'jabatan:id,nama',
                'items' => function($query) use ($penyerahan) {
                    $query->where('penyerahan_perlengkapan_id', $penyerahan->id)
                          ->with([
                              'item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                              'item.kategori:id,nama_kategori'
                          ]);
                }
            ])
            ->get();

        // Add status information to pivot
        foreach ($karyawans as $karyawan) {
            $penyerahanKaryawan = $penyerahan->karyawans()->where('karyawan_id', $karyawan->id)->first();
            if ($penyerahanKaryawan) {
                $karyawan->pivot->status_color = $penyerahanKaryawan->status_color;
                $karyawan->pivot->status_text = $penyerahanKaryawan->status_text;
            }
        }

        $totalItems = $penyerahan->items()->count();

        return view('perusahaan.penyerahan-perlengkapan.serahkan-item', compact('penyerahan', 'karyawans', 'totalItems'));
    }

    // New API methods for table-based serahkan item interface
    public function getSerahkanItemsData(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            $query = PenyerahanPerlengkapanItem::select([
                    'id',
                    'item_perlengkapan_id',
                    'karyawan_id',
                    'jumlah_diserahkan',
                    'is_diserahkan',
                    'tanggal_diserahkan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->with([
                    'item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                    'item.kategori:id,nama_kategori',
                    'karyawan:id,nama_lengkap,nik_karyawan'
                ]);

            // Filter by status
            $status = $request->get('status', 'pending');
            if ($status === 'pending') {
                $query->where('is_diserahkan', false);
            } else {
                $query->where('is_diserahkan', true);
            }

            // Filter by karyawan
            if ($request->filled('karyawan_id') && $request->karyawan_id !== 'all') {
                $query->where('karyawan_id', $request->karyawan_id);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('karyawan', function($kq) use ($search) {
                        $kq->where('nama_lengkap', 'like', "%{$search}%")
                          ->orWhere('nik_karyawan', 'like', "%{$search}%");
                    })->orWhereHas('item', function($iq) use ($search) {
                        $iq->where('nama_item', 'like', "%{$search}%");
                    });
                });
            }

            // Get pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get total count before pagination
            $total = $query->count();

            // Apply pagination
            $items = $query->orderBy('karyawan_id')
                ->orderBy('item_perlengkapan_id')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Calculate pagination info
            $lastPage = ceil($total / $perPage);

            // Get counts for tabs
            $pendingCount = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                ->where('is_diserahkan', false)
                ->count();
            
            $completedCount = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                ->where('is_diserahkan', true)
                ->count();

            return response()->json([
                'success' => true,
                'data' => $items,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? (($page - 1) * $perPage + 1) : 0,
                    'to' => min($page * $perPage, $total)
                ],
                'counts' => [
                    'pending' => $pendingCount,
                    'completed' => $completedCount
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getSerahkanItemsData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serahkanSingleItem(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:penyerahan_perlengkapan_items,id'
        ]);

        try {
            DB::transaction(function () use ($id, $validated) {
                $item = PenyerahanPerlengkapanItem::where('id', $validated['item_id'])
                    ->where('penyerahan_perlengkapan_id', $id)
                    ->where('is_diserahkan', false) // Only if not already handed over
                    ->with('item')
                    ->firstOrFail();

                // Reduce stock for this item
                $karyawan = \App\Models\Karyawan::find($item->karyawan_id);
                $item->item->updateStok(
                    $item->jumlah_diserahkan, 
                    'kurang',
                    "Penyerahan kepada {$karyawan->nama_lengkap}",
                    'penyerahan',
                    $id
                );

                // Update item as handed over
                $item->update([
                    'is_diserahkan' => true,
                    'tanggal_diserahkan' => now()
                ]);

                // Update karyawan status if needed
                $this->updateKaryawanStatus($id, $item->karyawan_id);
                
                // Update main penyerahan status
                $this->updateMainPenyerahanStatus($id);
            });

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diserahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyerahkan item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serahkanMultipleItems(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:penyerahan_perlengkapan_items,id'
        ]);

        try {
            DB::transaction(function () use ($id, $validated) {
                // Get items that will be marked as handed over (only those not already handed over)
                $items = PenyerahanPerlengkapanItem::whereIn('id', $validated['item_ids'])
                    ->where('penyerahan_perlengkapan_id', $id)
                    ->where('is_diserahkan', false) // Only items that haven't been handed over yet
                    ->with('item')
                    ->get();

                $karyawanIds = [];
                foreach ($items as $item) {
                    // Reduce stock for this item
                    $karyawan = \App\Models\Karyawan::find($item->karyawan_id);
                    $item->item->updateStok(
                        $item->jumlah_diserahkan, 
                        'kurang',
                        "Penyerahan kepada {$karyawan->nama_lengkap}",
                        'penyerahan',
                        $id
                    );
                    
                    // Update item as handed over
                    $item->update([
                        'is_diserahkan' => true,
                        'tanggal_diserahkan' => now()
                    ]);
                    
                    if (!in_array($item->karyawan_id, $karyawanIds)) {
                        $karyawanIds[] = $item->karyawan_id;
                    }
                }

                // Update karyawan status for all affected karyawan
                foreach ($karyawanIds as $karyawanId) {
                    $this->updateKaryawanStatus($id, $karyawanId);
                }
                
                // Update main penyerahan status
                $this->updateMainPenyerahanStatus($id);
            });

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diserahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyerahkan item: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateKaryawanStatus($penyerahanId, $karyawanId)
    {
        $penyerahanKaryawan = PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $penyerahanId)
            ->where('karyawan_id', $karyawanId)
            ->first();
        
        if ($penyerahanKaryawan) {
            $totalItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahanId)
                ->where('karyawan_id', $karyawanId)
                ->count();
            
            $diserahkanItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahanId)
                ->where('karyawan_id', $karyawanId)
                ->where('is_diserahkan', true)
                ->count();
            
            if ($diserahkanItems === 0) {
                $status = 'belum_diserahkan';
            } elseif ($diserahkanItems === $totalItems) {
                $status = 'sudah_diserahkan';
            } else {
                $status = 'sebagian_diserahkan';
            }
            
            $penyerahanKaryawan->update(['status_penyerahan' => $status]);
        }
    }

    private function updateMainPenyerahanStatus($penyerahanId)
    {
        $penyerahan = PenyerahanPerlengkapan::find($penyerahanId);
        
        if (!$penyerahan) {
            return;
        }

        // Count total items and diserahkan items
        $totalItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahanId)->count();
        $diserahkanItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahanId)
            ->where('is_diserahkan', true)
            ->count();

        // Determine new status based on handover progress
        if ($diserahkanItems === 0) {
            // No items handed over yet - keep as draft
            $newStatus = 'draft';
        } elseif ($diserahkanItems === $totalItems) {
            // All items handed over - mark as completed (dikembalikan)
            $newStatus = 'dikembalikan';
        } else {
            // Some items handed over - mark as in progress (diserahkan)
            $newStatus = 'diserahkan';
        }

        // Only update if status actually changed
        if ($penyerahan->status !== $newStatus) {
            $penyerahan->update(['status' => $newStatus]);
        }
    }

    public function getKaryawanForSelection(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);
        
        // IMPORTANT: Return empty result if no jabatan is selected to prevent heavy data loading
        if (!$request->filled('jabatan_id') || $request->jabatan_id === '' || $request->jabatan_id === 'all') {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Pilih jabatan terlebih dahulu untuk memuat data karyawan'
            ]);
        }
        
        $query = Karyawan::select([
                'id',
                'nik_karyawan',
                'nama_lengkap',
                'jabatan_id'
            ])
            ->with('jabatan:id,nama')
            ->where('project_id', $penyerahan->project_id)
            ->where('is_active', true);

        // Filter by jabatan (now required)
        $query->where('jabatan_id', $request->jabatan_id);

        // Search by name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_karyawan', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->orderBy('nama_lengkap')->get();

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }

    // Status Management
    public function pilihKaryawan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        $validated = $request->validate([
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawans,id'
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Clear existing karyawan
                $penyerahan->karyawans()->delete();
                
                // Add selected karyawan
                foreach ($validated['karyawan_ids'] as $karyawanId) {
                    \App\Models\PenyerahanPerlengkapanKaryawan::create([
                        'penyerahan_perlengkapan_id' => $penyerahan->id,
                        'karyawan_id' => $karyawanId,
                        'status_penyerahan' => 'belum_diserahkan'
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dipilih'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memilih karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pilihItem(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        if ($penyerahan->karyawans()->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih karyawan terlebih dahulu'
            ], 400);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:item_perlengkapans,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Validate stock availability before proceeding
                $karyawanCount = $penyerahan->karyawans()->count();
                
                foreach ($validated['items'] as $itemData) {
                    $item = \App\Models\ItemPerlengkapan::find($itemData['item_id']);
                    $totalNeeded = $itemData['jumlah'] * $karyawanCount;
                    
                    if ($item->stok_tersedia < $totalNeeded) {
                        throw new \Exception("Stok tidak mencukupi untuk item '{$item->nama_item}'. Dibutuhkan: {$totalNeeded}, Tersedia: {$item->stok_tersedia}");
                    }
                }
                
                // Clear existing items
                $penyerahan->items()->delete();
                
                // Add items for each karyawan
                foreach ($penyerahan->karyawans as $penyerahanKaryawan) {
                    foreach ($validated['items'] as $itemData) {
                        PenyerahanPerlengkapanItem::create([
                            'penyerahan_perlengkapan_id' => $penyerahan->id,
                            'item_perlengkapan_id' => $itemData['item_id'],
                            'karyawan_id' => $penyerahanKaryawan->karyawan_id,
                            'jumlah_diserahkan' => $itemData['jumlah'],
                            'kondisi_saat_diserahkan' => 'Baik',
                            'is_diserahkan' => false
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dipilih untuk semua karyawan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memilih item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serahkanItem(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if (!in_array($penyerahan->status, ['draft', 'diserahkan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status penyerahan tidak valid untuk aksi ini'
            ], 400);
        }

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:penyerahan_perlengkapan_items,id'
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Get items that will be marked as handed over (only those not already handed over)
                $itemsToHandOver = PenyerahanPerlengkapanItem::whereIn('id', $validated['item_ids'])
                    ->where('penyerahan_perlengkapan_id', $penyerahan->id)
                    ->where('karyawan_id', $validated['karyawan_id'])
                    ->where('is_diserahkan', false) // Only items that haven't been handed over yet
                    ->with('item')
                    ->get();

                // Reduce stock for each item being handed over
                $karyawan = \App\Models\Karyawan::find($validated['karyawan_id']);
                foreach ($itemsToHandOver as $penyerahanItem) {
                    $penyerahanItem->item->updateStok(
                        $penyerahanItem->jumlah_diserahkan, 
                        'kurang',
                        "Penyerahan kepada {$karyawan->nama_lengkap}",
                        'penyerahan',
                        $penyerahan->id
                    );
                }

                // Update selected items as diserahkan
                PenyerahanPerlengkapanItem::whereIn('id', $validated['item_ids'])
                    ->where('penyerahan_perlengkapan_id', $penyerahan->id)
                    ->where('karyawan_id', $validated['karyawan_id'])
                    ->update([
                        'is_diserahkan' => true,
                        'tanggal_diserahkan' => now()
                    ]);

                // Update karyawan status
                $this->updateKaryawanStatus($penyerahan->id, $validated['karyawan_id']);

                // Update main penyerahan status
                $this->updateMainPenyerahanStatus($penyerahan->id);
            });

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diserahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyerahkan item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setDiserahkan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah ke sedang diserahkan'
            ], 400);
        }

        if ($penyerahan->karyawans()->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih karyawan terlebih dahulu'
            ], 400);
        }

        if ($penyerahan->items()->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tambahkan item perlengkapan terlebih dahulu'
            ], 400);
        }

        try {
            $penyerahan->update(['status' => 'diserahkan']);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah ke sedang diserahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getItemsByKaryawan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $karyawanId = $request->get('karyawan_id');

        $items = PenyerahanPerlengkapanItem::select([
                'id',
                'item_perlengkapan_id',
                'jumlah_diserahkan',
                'is_diserahkan',
                'tanggal_diserahkan'
            ])
            ->where('penyerahan_perlengkapan_id', $id)
            ->where('karyawan_id', $karyawanId)
            ->with([
                'item:id,nama_item,satuan'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function serahkan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'diserahkan') {
            return response()->json([
                'success' => false,
                'message' => 'Penyerahan sudah tidak dalam status sedang diserahkan'
            ], 400);
        }

        try {
            $penyerahan->update([
                'status' => 'dikembalikan',
                'tanggal_diserahkan' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penyerahan berhasil dikonfirmasi sebagai selesai'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi penyerahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function kembalikan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'dikembalikan') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya penyerahan yang sudah selesai diserahkan yang dapat dikembalikan'
            ], 400);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:penyerahan_perlengkapan_items,id',
            'items.*.jumlah_dikembalikan' => 'required|integer|min:0',
            'items.*.kondisi_dikembalikan' => 'required|in:baik,rusak,hilang',
            'items.*.keterangan_item' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                foreach ($validated['items'] as $itemData) {
                    $penyerahanItem = PenyerahanPerlengkapanItem::findOrFail($itemData['item_id']);
                    
                    // Update penyerahan item
                    $penyerahanItem->update([
                        'jumlah_dikembalikan' => $itemData['jumlah_dikembalikan'],
                        'kondisi_saat_dikembalikan' => $itemData['kondisi_dikembalikan'],
                        'keterangan_item' => $itemData['keterangan_item'],
                    ]);

                    // Update stock only if condition is good
                    if ($itemData['kondisi_dikembalikan'] === 'baik') {
                        $penyerahanItem->item->updateStok(
                            $itemData['jumlah_dikembalikan'], 
                            'tambah',
                            "Pengembalian item dalam kondisi baik",
                            'return',
                            $penyerahan->id
                        );
                    }
                }

                // Update penyerahan status
                $penyerahan->update([
                    'status' => 'dikembalikan',
                    'tanggal_dikembalikan' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian berhasil diproses'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pengembalian: ' . $e->getMessage()
            ], 500);
        }
    }

    // New methods for separated employee selection workflow
    public function getSelectedKaryawan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            // Get selected karyawan IDs first
            $selectedKaryawanIds = PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $id)
                ->pluck('karyawan_id')
                ->toArray();

            if (empty($selectedKaryawanIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 10,
                        'total' => 0,
                        'last_page' => 1
                    ]
                ]);
            }

            // Build query for karyawan details
            $query = Karyawan::select([
                    'id',
                    'nik_karyawan',
                    'nama_lengkap',
                    'jabatan_id'
                ])
                ->with('jabatan:id,nama')
                ->whereIn('id', $selectedKaryawanIds);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nik_karyawan', 'like', "%{$search}%");
                });
            }

            // Apply jabatan filter
            if ($request->filled('jabatan_id') && $request->jabatan_id !== 'all') {
                $query->where('jabatan_id', $request->jabatan_id);
            }

            // Get pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get total count before pagination
            $total = $query->count();

            // Apply pagination
            $selectedKaryawans = $query->orderBy('nama_lengkap')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Calculate pagination info
            $lastPage = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $selectedKaryawans,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addKaryawan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        $validated = $request->validate([
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawans,id'
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Add new karyawan (don't clear existing ones)
                foreach ($validated['karyawan_ids'] as $karyawanId) {
                    // Check if already exists
                    $exists = PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $penyerahan->id)
                        ->where('karyawan_id', $karyawanId)
                        ->exists();
                    
                    if (!$exists) {
                        PenyerahanPerlengkapanKaryawan::create([
                            'penyerahan_perlengkapan_id' => $penyerahan->id,
                            'karyawan_id' => $karyawanId,
                            'status_penyerahan' => 'belum_diserahkan'
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeKaryawan(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id'
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Remove karyawan from selection
                PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $penyerahan->id)
                    ->where('karyawan_id', $validated['karyawan_id'])
                    ->delete();
                
                // Also remove any items assigned to this karyawan
                PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahan->id)
                    ->where('karyawan_id', $validated['karyawan_id'])
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus dari daftar penerima'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    // New methods for item selection workflow
    public function getSelectedItems(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            // First check if penyerahan exists
            $penyerahanRecord = PenyerahanPerlengkapan::find($id);
            if (!$penyerahanRecord) {
                return response()->json(['success' => false, 'message' => 'Penyerahan not found'], 404);
            }

            // Build query for selected items
            $query = PenyerahanPerlengkapanItem::select([
                    'item_perlengkapan_id',
                    'jumlah_diserahkan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->with([
                    'item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                    'item.kategori:id,nama_kategori'
                ])
                ->groupBy('item_perlengkapan_id', 'jumlah_diserahkan');

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('item', function($q) use ($search) {
                    $q->where('nama_item', 'like', "%{$search}%");
                });
            }

            // Apply kategori filter
            if ($request->filled('kategori_id') && $request->kategori_id !== 'all') {
                $query->whereHas('item', function($q) use ($request) {
                    $q->where('kategori_perlengkapan_id', $request->kategori_id);
                });
            }

            // Get pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get total count before pagination
            $total = $query->count();

            // Apply pagination
            $selectedItems = $query->orderBy('item_perlengkapan_id')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Calculate pagination info
            $lastPage = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $selectedItems,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? (($page - 1) * $perPage + 1) : 0,
                    'to' => min($page * $perPage, $total)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getSelectedItems: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addItems(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        if ($penyerahan->karyawans()->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih karyawan terlebih dahulu'
            ], 400);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:item_perlengkapans,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Validate stock availability before proceeding
                $karyawanCount = $penyerahan->karyawans()->count();
                
                foreach ($validated['items'] as $itemData) {
                    $item = \App\Models\ItemPerlengkapan::find($itemData['item_id']);
                    
                    // Count existing items for this item type
                    $existingCount = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahan->id)
                        ->where('item_perlengkapan_id', $itemData['item_id'])
                        ->count();
                    
                    // Calculate how many new items will be added
                    $newItemsCount = 0;
                    foreach ($penyerahan->karyawans as $penyerahanKaryawan) {
                        $exists = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahan->id)
                            ->where('item_perlengkapan_id', $itemData['item_id'])
                            ->where('karyawan_id', $penyerahanKaryawan->karyawan_id)
                            ->exists();
                        
                        if (!$exists) {
                            $newItemsCount += $itemData['jumlah'];
                        }
                    }
                    
                    // Check if we have enough stock for new items
                    if ($newItemsCount > 0 && $item->stok_tersedia < $newItemsCount) {
                        throw new \Exception("Stok tidak mencukupi untuk item '{$item->nama_item}'. Dibutuhkan: {$newItemsCount}, Tersedia: {$item->stok_tersedia}");
                    }
                }
                
                // Add items for each karyawan
                foreach ($penyerahan->karyawans as $penyerahanKaryawan) {
                    foreach ($validated['items'] as $itemData) {
                        // Check if item already exists for this karyawan
                        $exists = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahan->id)
                            ->where('item_perlengkapan_id', $itemData['item_id'])
                            ->where('karyawan_id', $penyerahanKaryawan->karyawan_id)
                            ->exists();
                        
                        if (!$exists) {
                            PenyerahanPerlengkapanItem::create([
                                'penyerahan_perlengkapan_id' => $penyerahan->id,
                                'item_perlengkapan_id' => $itemData['item_id'],
                                'karyawan_id' => $penyerahanKaryawan->karyawan_id,
                                'jumlah_diserahkan' => $itemData['jumlah'],
                                'kondisi_saat_diserahkan' => 'Baik',
                                'is_diserahkan' => false
                            ]);
                        }
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeItem(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:item_perlengkapans,id'
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Remove item from all karyawan
                PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahan->id)
                    ->where('item_perlengkapan_id', $validated['item_id'])
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari daftar penyerahan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateItemQuantity(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }
        
        $penyerahan = PenyerahanPerlengkapan::findOrFail($id);

        if ($penyerahan->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya jadwal dengan status draft yang dapat diubah'
            ], 400);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:item_perlengkapans,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($penyerahan, $validated) {
                // Update quantity for all karyawan
                PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $penyerahan->id)
                    ->where('item_perlengkapan_id', $validated['item_id'])
                    ->update(['jumlah_diserahkan' => $validated['quantity']]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Jumlah item berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jumlah: ' . $e->getMessage()
            ], 500);
        }
    }

    // New API methods for employee-centric serahkan item interface
    public function getKaryawanData(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            $penyerahanRecord = PenyerahanPerlengkapan::findOrFail($id);

            // Get karyawan IDs from penyerahan_perlengkapan_karyawans
            $karyawanIds = PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $id)
                ->pluck('karyawan_id')
                ->toArray();

            if (empty($karyawanIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => 10,
                        'total' => 0,
                        'last_page' => 1,
                        'from' => 0,
                        'to' => 0
                    ]
                ]);
            }

            // Build query for karyawan with their item statistics
            $query = Karyawan::select([
                    'id',
                    'nik_karyawan',
                    'nama_lengkap',
                    'jabatan_id'
                ])
                ->with('jabatan:id,nama')
                ->whereIn('id', $karyawanIds);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nik_karyawan', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            $statusFilter = $request->get('status', 'all');

            // Get pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get total count before pagination
            $total = $query->count();

            // Apply pagination
            $karyawans = $query->orderBy('nama_lengkap')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Add item statistics for each karyawan
            foreach ($karyawans as $karyawan) {
                $totalItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $karyawan->id)
                    ->count();

                $diserahkanItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $karyawan->id)
                    ->where('is_diserahkan', true)
                    ->count();

                // Determine status
                if ($diserahkanItems === 0) {
                    $status = 'belum_diserahkan';
                } elseif ($diserahkanItems === $totalItems) {
                    $status = 'sudah_diserahkan';
                } else {
                    $status = 'sebagian_diserahkan';
                }

                $karyawan->total_items = $totalItems;
                $karyawan->diserahkan_items = $diserahkanItems;
                $karyawan->status_penyerahan = $status;
            }

            // Apply status filter after adding statistics
            if ($statusFilter !== 'all') {
                $karyawans = $karyawans->filter(function($karyawan) use ($statusFilter) {
                    return $karyawan->status_penyerahan === $statusFilter;
                });
                
                // Recalculate pagination for filtered results
                $total = $karyawans->count();
                $karyawans = $karyawans->slice(($page - 1) * $perPage, $perPage)->values();
            }

            // Calculate pagination info
            $lastPage = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $karyawans,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? (($page - 1) * $perPage + 1) : 0,
                    'to' => min($page * $perPage, $total)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getKaryawanData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKaryawanItems(Request $request, $penyerahan, $karyawanId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            $items = PenyerahanPerlengkapanItem::select([
                    'id',
                    'item_perlengkapan_id',
                    'jumlah_diserahkan',
                    'is_diserahkan',
                    'tanggal_diserahkan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->with([
                    'item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                    'item.kategori:id,nama_kategori'
                ])
                ->orderBy('is_diserahkan')
                ->orderBy('item_perlengkapan_id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getKaryawanItems: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serahkanKaryawanItems(Request $request, $penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:penyerahan_perlengkapan_items,id'
        ]);

        try {
            DB::transaction(function () use ($id, $validated) {
                // Get the items that will be marked as handed over
                $itemsToHandOver = PenyerahanPerlengkapanItem::whereIn('id', $validated['item_ids'])
                    ->where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $validated['karyawan_id'])
                    ->where('is_diserahkan', false) // Only items that haven't been handed over yet
                    ->with('item')
                    ->get();

                // Reduce stock for each item being handed over
                $karyawan = \App\Models\Karyawan::find($validated['karyawan_id']);
                foreach ($itemsToHandOver as $penyerahanItem) {
                    $penyerahanItem->item->updateStok(
                        $penyerahanItem->jumlah_diserahkan, 
                        'kurang',
                        "Penyerahan kepada {$karyawan->nama_lengkap}",
                        'penyerahan',
                        $id
                    );
                }

                // Update selected items as diserahkan
                PenyerahanPerlengkapanItem::whereIn('id', $validated['item_ids'])
                    ->where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $validated['karyawan_id'])
                    ->update([
                        'is_diserahkan' => true,
                        'tanggal_diserahkan' => now()
                    ]);

                // Update karyawan status
                $this->updateKaryawanStatus($id, $validated['karyawan_id']);
                
                // Update main penyerahan status
                $this->updateMainPenyerahanStatus($id);
            });

            // Get karyawan name for response
            $karyawan = Karyawan::find($validated['karyawan_id']);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diserahkan',
                'show_print' => true,
                'karyawan_name' => $karyawan->nama_lengkap
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in serahkanKaryawanItems: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyerahkan item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPrintBukti(Request $request, $penyerahan, $karyawanId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            $penyerahanRecord = PenyerahanPerlengkapan::with('project:id,nama')->findOrFail($id);
            $karyawan = Karyawan::with('jabatan:id,nama')->findOrFail($karyawanId);

            // Get diserahkan items for this karyawan
            $items = PenyerahanPerlengkapanItem::select([
                    'id',
                    'item_perlengkapan_id',
                    'jumlah_diserahkan',
                    'tanggal_diserahkan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->where('is_diserahkan', true)
                ->with([
                    'item:id,nama_item,satuan',
                    'item.kategori:id,nama_kategori'
                ])
                ->get();

            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada item yang sudah diserahkan untuk karyawan ini'
                ]);
            }

            // Generate print HTML
            $html = view('perusahaan.penyerahan-perlengkapan.print-bukti', [
                'penyerahan' => $penyerahanRecord,
                'karyawan' => $karyawan,
                'items' => $items
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPrintBukti: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat bukti penyerahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendWhatsApp(Request $request, $penyerahan, $karyawanId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 404);
        }

        try {
            $penyerahanRecord = PenyerahanPerlengkapan::with('project:id,nama')->findOrFail($id);
            $karyawan = Karyawan::with(['user:id,no_whatsapp', 'jabatan:id,nama'])->findOrFail($karyawanId);

            if (!$karyawan->user || !$karyawan->user->no_whatsapp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp karyawan tidak ditemukan'
                ]);
            }

            // Get diserahkan items count
            $itemsCount = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->where('is_diserahkan', true)
                ->count();

            if ($itemsCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada item yang sudah diserahkan untuk karyawan ini'
                ]);
            }

            // Prepare WhatsApp message
            $message = "Halo {$karyawan->nama_lengkap},\n\n";
            $message .= "Anda telah menerima penyerahan perlengkapan:\n";
            $message .= "Project: {$penyerahanRecord->project->nama}\n";
            $message .= "Tanggal: " . now()->format('d/m/Y H:i') . "\n";
            $message .= "Total Item: {$itemsCount} item\n\n";
            $message .= "Terima kasih.";

            // Send WhatsApp using existing service
            $whatsappService = app(\App\Services\WhatsAppService::class);
            $result = $whatsappService->sendMessage($karyawan->user->no_whatsapp, $message);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi WhatsApp berhasil dikirim'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim WhatsApp: ' . ($result['error'] ?? 'Unknown error')
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Error in sendWhatsApp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serahkanKaryawanPage($penyerahan, $karyawanId)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            abort(404);
        }

        try {
            $penyerahanRecord = PenyerahanPerlengkapan::select([
                    'id',
                    'project_id',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'status',
                    'keterangan'
                ])
                ->with('project:id,nama')
                ->findOrFail($id);

            $karyawan = Karyawan::select([
                    'id',
                    'nik_karyawan',
                    'nama_lengkap',
                    'jabatan_id'
                ])
                ->with('jabatan:id,nama')
                ->findOrFail($karyawanId);

            // Verify karyawan is in this penyerahan
            $penyerahanKaryawan = PenyerahanPerlengkapanKaryawan::where('penyerahan_perlengkapan_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$penyerahanKaryawan) {
                return redirect()->back()
                    ->with('error', 'Karyawan tidak terdaftar dalam penyerahan ini');
            }

            // Get all items for this karyawan
            $items = PenyerahanPerlengkapanItem::select([
                    'id',
                    'item_perlengkapan_id',
                    'jumlah_diserahkan',
                    'is_diserahkan',
                    'tanggal_diserahkan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->where('karyawan_id', $karyawanId)
                ->with([
                    'item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                    'item.kategori:id,nama_kategori'
                ])
                ->get();

            // Get categories for filtering
            $categories = $items->pluck('item.kategori')
                ->filter()
                ->unique('id')
                ->sortBy('nama_kategori')
                ->values();

            return view('perusahaan.penyerahan-perlengkapan.serahkan-karyawan', compact(
                'penyerahanRecord',
                'karyawan',
                'items',
                'categories'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in serahkanKaryawanPage: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memuat halaman: ' . $e->getMessage());
        }
    }

    public function laporanPenyerahan($penyerahan)
    {
        // Decode hash ID to get real ID
        $id = \Vinkla\Hashids\Facades\Hashids::decode($penyerahan)[0] ?? null;
        if (!$id) {
            abort(404);
        }

        try {
            $penyerahanRecord = PenyerahanPerlengkapan::select([
                    'id',
                    'project_id',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'status',
                    'keterangan',
                    'created_at'
                ])
                ->with('project:id,nama')
                ->findOrFail($id);

            // Get karyawan with their handover statistics
            $karyawans = PenyerahanPerlengkapanKaryawan::select([
                    'karyawan_id',
                    'status_penyerahan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->with([
                    'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id',
                    'karyawan.jabatan:id,nama'
                ])
                ->get();

            // Calculate statistics for each karyawan
            foreach ($karyawans as $karyawan) {
                $totalItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $karyawan->karyawan_id)
                    ->count();

                $diserahkanItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $karyawan->karyawan_id)
                    ->where('is_diserahkan', true)
                    ->count();

                // Get last handover date
                $lastHandoverDate = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                    ->where('karyawan_id', $karyawan->karyawan_id)
                    ->where('is_diserahkan', true)
                    ->whereNotNull('tanggal_diserahkan')
                    ->orderBy('tanggal_diserahkan', 'desc')
                    ->value('tanggal_diserahkan');

                $karyawan->total_items = $totalItems;
                $karyawan->diserahkan_items = $diserahkanItems;
                $karyawan->persentase = $totalItems > 0 ? round(($diserahkanItems / $totalItems) * 100, 1) : 0;
                $karyawan->tanggal_terakhir_diserahkan = $lastHandoverDate ? \Carbon\Carbon::parse($lastHandoverDate) : null;
            }

            // Overall statistics
            $totalKaryawan = $karyawans->count();
            $totalItems = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)->count();
            $totalDiserahkan = PenyerahanPerlengkapanItem::where('penyerahan_perlengkapan_id', $id)
                ->where('is_diserahkan', true)
                ->count();
            
            $overallPersentase = $totalItems > 0 ? round(($totalDiserahkan / $totalItems) * 100, 1) : 0;

            // Status breakdown
            $statusBreakdown = [
                'belum_diserahkan' => $karyawans->where('status_penyerahan', 'belum_diserahkan')->count(),
                'sebagian_diserahkan' => $karyawans->where('status_penyerahan', 'sebagian_diserahkan')->count(),
                'sudah_diserahkan' => $karyawans->where('status_penyerahan', 'sudah_diserahkan')->count(),
            ];

            // Get items breakdown by category
            $itemsByCategory = PenyerahanPerlengkapanItem::select([
                    'item_perlengkapan_id',
                    'jumlah_diserahkan',
                    'is_diserahkan',
                    'tanggal_diserahkan'
                ])
                ->where('penyerahan_perlengkapan_id', $id)
                ->with([
                    'item:id,nama_item,satuan,foto_item,kategori_perlengkapan_id',
                    'item.kategori:id,nama_kategori'
                ])
                ->get()
                ->groupBy('item.kategori.nama_kategori')
                ->map(function ($items, $kategori) {
                    $total = $items->count();
                    $diserahkan = $items->where('is_diserahkan', true)->count();
                    
                    // Get unique items with their details
                    $uniqueItems = $items->groupBy('item_perlengkapan_id')->map(function ($itemGroup) {
                        $firstItem = $itemGroup->first();
                        $lastHandoverDate = $itemGroup->where('is_diserahkan', true)
                            ->whereNotNull('tanggal_diserahkan')
                            ->sortByDesc('tanggal_diserahkan')
                            ->first();
                        
                        return [
                            'nama_item' => $firstItem->item->nama_item,
                            'satuan' => $firstItem->item->satuan,
                            'foto_item' => $firstItem->item->foto_item,
                            'jumlah_diserahkan' => $itemGroup->sum('jumlah_diserahkan'),
                            'is_diserahkan' => $itemGroup->where('is_diserahkan', true)->count() > 0,
                            'tanggal_diserahkan' => $lastHandoverDate ? \Carbon\Carbon::parse($lastHandoverDate->tanggal_diserahkan) : null
                        ];
                    })->values();
                    
                    return [
                        'kategori' => $kategori ?: 'Tanpa Kategori',
                        'total' => $total,
                        'diserahkan' => $diserahkan,
                        'persentase' => $total > 0 ? round(($diserahkan / $total) * 100, 1) : 0,
                        'items' => $uniqueItems
                    ];
                })
                ->values();

            return view('perusahaan.penyerahan-perlengkapan.laporan', compact(
                'penyerahanRecord',
                'karyawans',
                'totalKaryawan',
                'totalItems',
                'totalDiserahkan',
                'overallPersentase',
                'statusBreakdown',
                'itemsByCategory'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in laporanPenyerahan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
        }
    }
}