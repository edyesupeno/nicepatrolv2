<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KartuTamu;
use App\Models\Project;
use App\Models\Area;
use App\Models\BukuTamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuTamuController extends Controller
{
    public function index(Request $request)
    {
        // Get all enabled project-area combinations, even if no cards exist yet
        $query = DB::table('project_guest_card_areas')
            ->join('projects', 'project_guest_card_areas.project_id', '=', 'projects.id')
            ->join('areas', 'project_guest_card_areas.area_id', '=', 'areas.id')
            ->leftJoin('kartu_tamus', function($join) {
                $join->on('project_guest_card_areas.project_id', '=', 'kartu_tamus.project_id')
                     ->on('project_guest_card_areas.area_id', '=', 'kartu_tamus.area_id');
            })
            ->where('projects.perusahaan_id', auth()->user()->perusahaan_id)
            ->where('projects.enable_guest_card', true)
            ->select(
                'project_guest_card_areas.project_id',
                'project_guest_card_areas.area_id',
                'projects.nama as project_nama',
                'areas.nama as area_nama',
                DB::raw('COUNT(kartu_tamus.id) as total_kartu'),
                DB::raw('COUNT(CASE WHEN kartu_tamus.current_guest_id IS NOT NULL THEN 1 END) as terpakai'),
                DB::raw("COUNT(CASE WHEN kartu_tamus.current_guest_id IS NULL AND kartu_tamus.status = 'aktif' AND kartu_tamus.is_active = true THEN 1 END) as tersedia")
            )
            ->groupBy('project_guest_card_areas.project_id', 'project_guest_card_areas.area_id', 'projects.nama', 'areas.nama');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('projects.nama', 'ILIKE', "%{$search}%")
                  ->orWhere('areas.nama', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_guest_card_areas.project_id', $request->project_id);
        }

        $kartuSummary = $query->orderBy('projects.nama')
                             ->orderBy('areas.nama')
                             ->paginate(15);

        // Only show projects that have guest card enabled
        $projects = Project::where('is_active', true)
                          ->where('enable_guest_card', true)
                          ->get();

        // Statistics - only count cards from projects with guest card enabled
        $guestCardProjectIds = Project::where('enable_guest_card', true)->pluck('id');
        
        $stats = [
            'total_kartu' => KartuTamu::whereIn('project_id', $guestCardProjectIds)->count(),
            'tersedia' => KartuTamu::whereIn('project_id', $guestCardProjectIds)->available()->count(),
            'terpakai' => KartuTamu::whereIn('project_id', $guestCardProjectIds)->assigned()->count(),
            'rusak_hilang' => KartuTamu::whereIn('project_id', $guestCardProjectIds)->whereIn('status', ['rusak', 'hilang'])->count(),
        ];

        return view('perusahaan.kartu-tamu.index', compact('kartuSummary', 'projects', 'stats'));
    }

    public function show(Request $request)
    {
        $projectId = $request->get('project_id');
        $areaId = $request->get('area_id');

        if (!$projectId || !$areaId) {
            return redirect()->route('perusahaan.kartu-tamu.index')
                           ->with('error', 'Project dan Area harus dipilih');
        }

        $project = Project::findOrFail($projectId);
        $area = Area::findOrFail($areaId);

        $query = KartuTamu::with(['currentGuest'])
            ->where('project_id', $projectId)
            ->where('area_id', $areaId);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_kartu', 'ILIKE', "%{$search}%")
                  ->orWhere('nfc_kartu', 'ILIKE', "%{$search}%")
                  ->orWhere('keterangan', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kartuTamus = $query->orderBy('no_kartu')->paginate(20);

        // Statistics for this area
        $stats = [
            'total' => KartuTamu::where('project_id', $projectId)->where('area_id', $areaId)->count(),
            'tersedia' => KartuTamu::where('project_id', $projectId)->where('area_id', $areaId)->available()->count(),
            'terpakai' => KartuTamu::where('project_id', $projectId)->where('area_id', $areaId)->assigned()->count(),
            'rusak' => KartuTamu::where('project_id', $projectId)->where('area_id', $areaId)->where('status', 'rusak')->count(),
            'hilang' => KartuTamu::where('project_id', $projectId)->where('area_id', $areaId)->where('status', 'hilang')->count(),
        ];

        return view('perusahaan.kartu-tamu.detail', compact('kartuTamus', 'project', 'area', 'stats', 'projectId', 'areaId'));
    }

    public function create(Request $request)
    {
        $projectId = $request->get('project_id');
        $areaId = $request->get('area_id');

        if (!$projectId || !$areaId) {
            return redirect()->route('perusahaan.kartu-tamu.index')
                           ->with('error', 'Project dan Area harus dipilih');
        }

        $project = Project::where('enable_guest_card', true)->findOrFail($projectId);
        
        // Check if this area is enabled for guest cards in this project
        $isAreaEnabled = DB::table('project_guest_card_areas')
            ->where('project_id', $projectId)
            ->where('area_id', $areaId)
            ->exists();
            
        if (!$isAreaEnabled) {
            return redirect()->route('perusahaan.kartu-tamu.index')
                           ->with('error', 'Area ini tidak menggunakan sistem kartu tamu');
        }

        $area = Area::findOrFail($areaId);

        return view('perusahaan.kartu-tamu.create', compact('project', 'area', 'projectId', 'areaId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_id' => 'required|exists:areas,id',
            'no_kartu' => 'required|string|max:50|unique:kartu_tamus,no_kartu',
            'nfc_kartu' => 'nullable|string|max:100',
            'status' => 'required|in:aktif,rusak,hilang',
            'keterangan' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'area_id.required' => 'Area wajib dipilih',
            'no_kartu.required' => 'Nomor kartu wajib diisi',
            'no_kartu.unique' => 'Nomor kartu sudah digunakan',
            'status.required' => 'Status wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        KartuTamu::create($validated);

        return redirect()->route('perusahaan.kartu-tamu.detail', [
            'project_id' => $validated['project_id'],
            'area_id' => $validated['area_id']
        ])->with('success', 'Kartu tamu berhasil ditambahkan');
    }

    public function edit(KartuTamu $kartuTamu)
    {
        $project = $kartuTamu->project;
        $area = $kartuTamu->area;

        return view('perusahaan.kartu-tamu.edit', compact('kartuTamu', 'project', 'area'));
    }

    public function update(Request $request, KartuTamu $kartuTamu)
    {
        $validated = $request->validate([
            'no_kartu' => 'required|string|max:50|unique:kartu_tamus,no_kartu,' . $kartuTamu->id,
            'nfc_kartu' => 'nullable|string|max:100',
            'status' => 'required|in:aktif,rusak,hilang',
            'keterangan' => 'nullable|string',
        ], [
            'no_kartu.required' => 'Nomor kartu wajib diisi',
            'no_kartu.unique' => 'Nomor kartu sudah digunakan',
            'status.required' => 'Status wajib dipilih',
        ]);

        // If status changed from aktif to rusak/hilang and card is assigned, return it
        if ($kartuTamu->status === 'aktif' && in_array($validated['status'], ['rusak', 'hilang']) && $kartuTamu->is_assigned) {
            $kartuTamu->returnFromGuest();
        }

        $kartuTamu->update($validated);

        return redirect()->route('perusahaan.kartu-tamu.detail', [
            'project_id' => $kartuTamu->project_id,
            'area_id' => $kartuTamu->area_id
        ])->with('success', 'Kartu tamu berhasil diupdate');
    }

    public function destroy(KartuTamu $kartuTamu)
    {
        $projectId = $kartuTamu->project_id;
        $areaId = $kartuTamu->area_id;

        // If card is assigned, return it first
        if ($kartuTamu->is_assigned) {
            $kartuTamu->returnFromGuest();
        }

        $kartuTamu->delete();

        return redirect()->route('perusahaan.kartu-tamu.detail', [
            'project_id' => $projectId,
            'area_id' => $areaId
        ])->with('success', 'Kartu tamu berhasil dihapus');
    }

    /**
     * Assign card to guest
     */
    public function assignCard(Request $request, KartuTamu $kartuTamu)
    {
        \Log::info('assignCard called', [
            'card_id' => $kartuTamu->id,
            'card_no' => $kartuTamu->no_kartu,
            'request_data' => $request->all(),
            'raw_input' => $request->getContent()
        ]);

        try {
            // Get guest_id from request
            $guestHashId = $request->input('guest_id');
            
            if (!$guestHashId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guest ID is required'
                ], 400);
            }

            \Log::info('Processing assignment', [
                'guest_hash_id' => $guestHashId,
                'card_available' => $kartuTamu->is_available
            ]);

            if (!$kartuTamu->is_available) {
                \Log::warning('Card not available', ['card_id' => $kartuTamu->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak tersedia untuk dipinjamkan'
                ], 400);
            }

            // Find guest by hash_id - decode it first since hash_id is not a database column
            $guestId = \Vinkla\Hashids\Facades\Hashids::decode($guestHashId)[0] ?? null;
            
            \Log::info('Decoding guest hash', [
                'hash_id' => $guestHashId,
                'decoded_id' => $guestId
            ]);
            
            if (!$guestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid guest ID'
                ], 400);
            }

            $guest = BukuTamu::withoutGlobalScope('perusahaan')->find($guestId);
            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu tidak ditemukan'
                ], 404);
            }

            \Log::info('Guest found', [
                'guest_id' => $guest->id,
                'guest_name' => $guest->nama_tamu,
                'is_visiting' => $guest->is_visiting
            ]);

            // Check if guest is still visiting
            if (!$guest->is_visiting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu hanya dapat diberikan kepada tamu yang sedang berkunjung'
                ], 400);
            }

            // Check if guest already has a card
            if ($guest->no_kartu_pinjam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah memiliki kartu yang dipinjam'
                ], 400);
            }

            // Assign card to guest
            $kartuTamu->assignToGuest($guest->id);
            
            // Update guest record with card number
            $guest->update([
                'no_kartu_pinjam' => $kartuTamu->no_kartu
            ]);

            \Log::info('Card assigned successfully', [
                'card_no' => $kartuTamu->no_kartu,
                'guest_name' => $guest->nama_tamu
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kartu berhasil dipinjamkan ke tamu'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in assignCard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return card from guest
     */
    public function returnCard(KartuTamu $kartuTamu)
    {
        if (!$kartuTamu->is_assigned) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak sedang dipinjamkan'
            ], 400);
        }

        $kartuTamu->returnFromGuest();

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil dikembalikan'
        ]);
    }

    /**
     * Get available cards for area
     */
    public function getAvailableCards(Request $request)
    {
        $areaId = $request->get('area_id');
        
        if (!$areaId) {
            return response()->json([
                'success' => false,
                'message' => 'Area ID required'
            ]);
        }

        try {
            $cards = KartuTamu::where('area_id', $areaId)
                ->available()
                ->select('id', 'no_kartu', 'nfc_kartu')
                ->orderBy('no_kartu')
                ->get();

            // Transform the response to use hash_id instead of id
            $cardsData = $cards->map(function($card) {
                return [
                    'id' => $card->hash_id, // Use hash_id instead of numeric id
                    'no_kartu' => $card->no_kartu,
                    'nfc_kartu' => $card->nfc_kartu
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $cardsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}