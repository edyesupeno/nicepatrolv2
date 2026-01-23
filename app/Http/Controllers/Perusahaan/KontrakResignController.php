<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Resign;
use App\Models\Project;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KontrakResignController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'kontrak-habis');

        // Get filter options
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        if ($activeTab === 'kontrak-habis') {
            return $this->kontrakHabis($request, $projects);
        } else {
            return $this->resign($request, $projects);
        }
    }

    private function kontrakHabis(Request $request, $projects)
    {
        // Untuk sementara, kita akan menampilkan karyawan yang sudah resign (tanggal_keluar tidak null)
        // dan karyawan yang masih aktif tapi bisa difilter berdasarkan tanggal masuk untuk simulasi kontrak
        $query = Karyawan::with(['project', 'jabatan']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by contract status
        $contractFilter = $request->get('contract_filter', 'expired');
        $today = now()->toDateString();
        
        if ($contractFilter === 'expired') {
            // Karyawan yang sudah resign (tanggal_keluar sudah ada)
            $query->whereNotNull('tanggal_keluar');
        } elseif ($contractFilter === 'expiring_soon') {
            // Karyawan aktif yang sudah bekerja lebih dari 2 tahun (simulasi kontrak akan habis)
            $query->where('is_active', true)
                  ->whereNull('tanggal_keluar')
                  ->where('tanggal_masuk', '<=', now()->subYears(2)->toDateString());
        } elseif ($contractFilter === 'all') {
            // Semua karyawan (aktif dan tidak aktif)
            // No additional filter needed
        } else {
            // Default: tampilkan karyawan yang sudah resign
            $query->whereNotNull('tanggal_keluar');
        }

        // Search by karyawan name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nik_karyawan', 'like', '%' . $request->search . '%');
            });
        }

        $karyawans = $query->orderBy('tanggal_keluar', 'desc')
                          ->orderBy('tanggal_masuk', 'desc')
                          ->paginate(20);

        // Add contract status to each karyawan
        foreach ($karyawans as $karyawan) {
            if ($karyawan->tanggal_keluar) {
                // Karyawan sudah resign
                $daysAgo = (int) now()->diffInDays($karyawan->tanggal_keluar, false);
                $karyawan->contract_status = 'expired';
                $karyawan->days_info = abs($daysAgo) . ' hari yang lalu resign';
                $karyawan->display_date = $karyawan->tanggal_keluar;
            } else {
                // Karyawan masih aktif, hitung berdasarkan tanggal masuk
                $workingDays = (int) $karyawan->tanggal_masuk->diffInDays(now());
                $workingYears = floor($workingDays / 365);
                
                if ($workingYears >= 2) {
                    $karyawan->contract_status = 'expiring_soon';
                    $karyawan->days_info = $workingYears . ' tahun bekerja';
                } else {
                    $karyawan->contract_status = 'active';
                    $karyawan->days_info = $workingYears . ' tahun bekerja';
                }
                $karyawan->display_date = $karyawan->tanggal_masuk;
            }
        }

        // Statistics
        $stats = [
            'expired' => Karyawan::whereNotNull('tanggal_keluar')->count(),
            'expiring_soon' => Karyawan::where('is_active', true)
                ->whereNull('tanggal_keluar')
                ->where('tanggal_masuk', '<=', now()->subYears(2)->toDateString())
                ->count(),
            'total_contract' => Karyawan::count(),
        ];

        return view('perusahaan.kontrak-resign.index', compact('karyawans', 'projects', 'stats'))
            ->with('activeTab', 'kontrak-habis');
    }

    private function resign(Request $request, $projects)
    {
        $query = Resign::with(['karyawan', 'project', 'createdBy', 'approvedBy']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis resign
        if ($request->filled('jenis_resign')) {
            $query->where('jenis_resign', $request->jenis_resign);
        }

        // Filter by date range
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->byDateRange($request->tanggal_mulai, $request->tanggal_selesai);
        }

        // Search by karyawan name
        if ($request->filled('search')) {
            $query->whereHas('karyawan', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nik_karyawan', 'like', '%' . $request->search . '%');
            });
        }

        $resigns = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Resign::count(),
            'pending' => Resign::pending()->count(),
            'approved' => Resign::approved()->count(),
            'rejected' => Resign::rejected()->count(),
        ];

        return view('perusahaan.kontrak-resign.index', compact('resigns', 'projects', 'stats'))
            ->with('activeTab', 'resign');
    }

    public function createResign()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.kontrak-resign.create-resign', compact('projects'));
    }

    public function storeResign(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_pengajuan' => 'required|date|after_or_equal:today',
            'tanggal_resign_efektif' => 'required|date|after:tanggal_pengajuan',
            'jenis_resign' => 'required|in:resign_pribadi,kontrak_habis,phk,pensiun,meninggal_dunia,lainnya',
            'alasan_resign' => 'required|string|max:2000',
            'handover_notes' => 'nullable|string|max:2000',
            'handover_items' => 'nullable|array',
            'handover_items.*' => 'string|max:255',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'karyawan_id.required' => 'Karyawan wajib dipilih',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan wajib diisi',
            'tanggal_pengajuan.after_or_equal' => 'Tanggal pengajuan tidak boleh kurang dari hari ini',
            'tanggal_resign_efeftif.required' => 'Tanggal resign efektif wajib diisi',
            'tanggal_resign_efektif.after' => 'Tanggal resign efektif harus setelah tanggal pengajuan',
            'jenis_resign.required' => 'Jenis resign wajib dipilih',
            'alasan_resign.required' => 'Alasan resign wajib diisi',
            'alasan_resign.max' => 'Alasan resign maksimal 2000 karakter',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Check if karyawan already has pending resign
                $existingResign = Resign::where('karyawan_id', $validated['karyawan_id'])
                    ->where('status', 'pending')
                    ->exists();

                if ($existingResign) {
                    throw new \Exception('Karyawan ini sudah memiliki pengajuan resign yang sedang diproses');
                }

                $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
                $validated['created_by'] = auth()->id();

                Resign::create($validated);
            });

            return redirect()->route('perusahaan.kontrak-resign.index', ['tab' => 'resign'])
                ->with('success', 'Pengajuan resign berhasil dibuat');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat pengajuan resign: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showResign(Resign $resign)
    {
        $resign->load(['karyawan', 'project', 'createdBy', 'approvedBy']);
        
        return view('perusahaan.kontrak-resign.show-resign', compact('resign'));
    }

    public function editResign(Resign $resign)
    {
        if (!$resign->canEdit()) {
            return redirect()->route('perusahaan.kontrak-resign.index', ['tab' => 'resign'])
                ->with('error', 'Pengajuan resign tidak dapat diedit');
        }

        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.kontrak-resign.edit-resign', compact('resign', 'projects'));
    }

    public function updateResign(Request $request, Resign $resign)
    {
        if (!$resign->canEdit()) {
            return redirect()->route('perusahaan.kontrak-resign.index', ['tab' => 'resign'])
                ->with('error', 'Pengajuan resign tidak dapat diedit');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_pengajuan' => 'required|date|after_or_equal:today',
            'tanggal_resign_efektif' => 'required|date|after:tanggal_pengajuan',
            'jenis_resign' => 'required|in:resign_pribadi,kontrak_habis,phk,pensiun,meninggal_dunia,lainnya',
            'alasan_resign' => 'required|string|max:2000',
            'handover_notes' => 'nullable|string|max:2000',
            'handover_items' => 'nullable|array',
            'handover_items.*' => 'string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $resign) {
                // Check if karyawan already has other pending resign (exclude current)
                $existingResign = Resign::where('karyawan_id', $validated['karyawan_id'])
                    ->where('status', 'pending')
                    ->where('id', '!=', $resign->id)
                    ->exists();

                if ($existingResign) {
                    throw new \Exception('Karyawan ini sudah memiliki pengajuan resign lain yang sedang diproses');
                }

                $resign->update($validated);
            });

            return redirect()->route('perusahaan.kontrak-resign.index', ['tab' => 'resign'])
                ->with('success', 'Pengajuan resign berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate pengajuan resign: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyResign(Resign $resign)
    {
        if (!$resign->canDelete()) {
            return redirect()->route('perusahaan.kontrak-resign.index', ['tab' => 'resign'])
                ->with('error', 'Pengajuan resign tidak dapat dihapus');
        }

        $resign->delete();

        return redirect()->route('perusahaan.kontrak-resign.index', ['tab' => 'resign'])
            ->with('success', 'Pengajuan resign berhasil dihapus');
    }

    public function approveResign(Request $request, Resign $resign)
    {
        if (!$resign->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan resign tidak dapat disetujui'
            ], 403);
        }

        $validated = $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
            'is_blacklist' => 'boolean',
            'blacklist_reason' => 'required_if:is_blacklist,true|nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($validated, $resign) {
                $resign->update([
                    'status' => 'approved',
                    'catatan_approval' => $validated['catatan_approval'],
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'is_blacklist' => $validated['is_blacklist'] ?? false,
                    'blacklist_reason' => $validated['blacklist_reason'] ?? null,
                ]);

                // Update karyawan status to inactive
                $resign->karyawan->update([
                    'is_active' => false,
                    'tanggal_keluar' => $resign->tanggal_resign_efektif,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan resign berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pengajuan resign: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectResign(Request $request, Resign $resign)
    {
        if (!$resign->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan resign tidak dapat ditolak'
            ], 403);
        }

        $validated = $request->validate([
            'catatan_approval' => 'required|string|max:500',
        ], [
            'catatan_approval.required' => 'Alasan penolakan wajib diisi',
        ]);

        $resign->update([
            'status' => 'rejected',
            'catatan_approval' => $validated['catatan_approval'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan resign berhasil ditolak'
        ]);
    }

    public function getKaryawansByProject($projectId)
    {
        $karyawans = Karyawan::where('project_id', $projectId)
            ->where('is_active', true)
            ->select('id', 'nik_karyawan', 'nama_lengkap', 'tanggal_masuk', 'tanggal_keluar')
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json($karyawans);
    }
}