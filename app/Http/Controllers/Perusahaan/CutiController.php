<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Project;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class CutiController extends Controller
{
    /**
     * Check if cutis table exists
     */
    private function tableExists(): bool
    {
        try {
            return Schema::hasTable('cutis');
        } catch (\Exception $e) {
            \Log::error('Error checking cutis table: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get error response when table doesn't exist
     */
    private function getTableNotExistsResponse($redirectRoute = 'perusahaan.cuti.index')
    {
        $message = 'Fitur Cuti belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.';
        
        if ($redirectRoute === 'perusahaan.cuti.index') {
            // For index page, return view with empty data
            $cutis = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                20, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            
            $stats = [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
            ];

            return view('perusahaan.cuti.index', compact('cutis', 'projects', 'stats'))
                ->with('info', $message);
        }
        
        return redirect()->route($redirectRoute)->with('error', $message);
    }
    public function index(Request $request)
    {
        try {
            // Check if cutis table exists
            if (!$this->tableExists()) {
                return $this->getTableNotExistsResponse('perusahaan.cuti.index');
            }

            $query = Cuti::with(['karyawan', 'project', 'createdBy', 'approvedBy']);

            // Filter by project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by jenis cuti
            if ($request->filled('jenis_cuti')) {
                $query->where('jenis_cuti', $request->jenis_cuti);
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

            $cutis = $query->orderBy('created_at', 'desc')->paginate(20);

            // Calculate remaining leave for each employee (only for annual leave)
            $currentYear = now()->year;
            $karyawanIds = $cutis->where('jenis_cuti', 'tahunan')->pluck('karyawan_id')->unique();
            
            // Bulk fetch annual leave usage for all employees
            $cutiTahunanData = [];
            if ($karyawanIds->isNotEmpty()) {
                $cutiTahunanUsage = Cuti::whereIn('karyawan_id', $karyawanIds)
                    ->where('jenis_cuti', 'tahunan')
                    ->where('status', 'approved')
                    ->whereYear('tanggal_mulai', $currentYear)
                    ->selectRaw('karyawan_id, SUM(total_hari) as total_terpakai')
                    ->groupBy('karyawan_id')
                    ->pluck('total_terpakai', 'karyawan_id');
                
                foreach ($karyawanIds as $karyawanId) {
                    $cutiTahunanData[$karyawanId] = $cutiTahunanUsage->get($karyawanId, 0);
                }
            }

            // Add remaining leave info to each cuti record
            foreach ($cutis as $cuti) {
                if ($cuti->jenis_cuti === 'tahunan') {
                    $batasCutiTahunan = $cuti->project->batas_cuti_tahunan ?? 12; // Default 12 days
                    $cutiTerpakai = $cutiTahunanData[$cuti->karyawan_id] ?? 0;
                    
                    $cuti->sisa_cuti_tahunan = $batasCutiTahunan - $cutiTerpakai;
                    $cuti->batas_cuti_tahunan = $batasCutiTahunan;
                    $cuti->cuti_terpakai = $cutiTerpakai;
                } else {
                    $cuti->sisa_cuti_tahunan = null;
                    $cuti->batas_cuti_tahunan = null;
                    $cuti->cuti_terpakai = null;
                }
            }

            // Get filter options
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            
            // Statistics
            $stats = [
                'total' => Cuti::count(),
                'pending' => Cuti::pending()->count(),
                'approved' => Cuti::approved()->count(),
                'rejected' => Cuti::rejected()->count(),
            ];

            return view('perusahaan.cuti.index', compact('cutis', 'projects', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error in CutiController@index: ' . $e->getMessage());
            
            // Return empty view with error message
            $cutis = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                20, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            
            $stats = [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
            ];

            return view('perusahaan.cuti.index', compact('cutis', 'projects', 'stats'))
                ->with('error', 'Terjadi kesalahan saat memuat data cuti. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function create()
    {
        try {
            // Check if cutis table exists
            if (!$this->tableExists()) {
                // Show create form with info message instead of redirecting
                $projects = Project::select('id', 'nama')->orderBy('nama')->get();
                
                return view('perusahaan.cuti.create', compact('projects'))
                    ->with('info', 'Fitur Cuti belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            
            return view('perusahaan.cuti.create', compact('projects'));

        } catch (\Exception $e) {
            \Log::error('Error in CutiController@create: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.cuti.index')
                ->with('error', 'Terjadi kesalahan saat memuat halaman. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Check if cutis table exists
            if (!$this->tableExists()) {
                return $this->getTableNotExistsResponse();
            }

            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'karyawan_id' => 'required|exists:karyawans,id',
                'tanggal_mulai' => 'required|date|after_or_equal:today',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,menikah,khitan,baptis,keluarga_meninggal,lainnya',
                'alasan' => 'required|string|max:1000',
            ], [
                'project_id.required' => 'Project wajib dipilih',
                'karyawan_id.required' => 'Karyawan wajib dipilih',
                'tanggal_mulai.required' => 'Tanggal mulai cuti wajib diisi',
                'tanggal_mulai.after_or_equal' => 'Tanggal mulai cuti tidak boleh kurang dari hari ini',
                'tanggal_selesai.required' => 'Tanggal selesai cuti wajib diisi',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai cuti harus setelah atau sama dengan tanggal mulai',
                'jenis_cuti.required' => 'Jenis cuti wajib dipilih',
                'alasan.required' => 'Alasan cuti wajib diisi',
                'alasan.max' => 'Alasan cuti maksimal 1000 karakter',
            ]);

            DB::transaction(function () use ($validated) {
                // Calculate total hari
                $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
                $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);
                $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

                // Validate batas cuti tahunan jika jenis cuti adalah tahunan
                if ($validated['jenis_cuti'] === 'tahunan') {
                    $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
                    $project = Project::findOrFail($validated['project_id']);
                    
                    // Hitung total cuti tahunan yang sudah diambil tahun ini
                    $currentYear = now()->year;
                    $cutiTahunanTerpakai = Cuti::where('karyawan_id', $validated['karyawan_id'])
                        ->where('jenis_cuti', 'tahunan')
                        ->where('status', 'approved')
                        ->whereYear('tanggal_mulai', $currentYear)
                        ->sum('total_hari');
                    
                    $sisaCuti = $project->batas_cuti_tahunan - $cutiTahunanTerpakai;
                    
                    if ($totalHari > $sisaCuti) {
                        throw new \Exception("Permintaan cuti melebihi sisa cuti tahunan. Sisa cuti: {$sisaCuti} hari");
                    }
                }

                // Check for overlapping cuti
                $overlapping = Cuti::where('karyawan_id', $validated['karyawan_id'])
                    ->where('status', '!=', 'rejected')
                    ->byDateRange($validated['tanggal_mulai'], $validated['tanggal_selesai'])
                    ->exists();

                if ($overlapping) {
                    throw new \Exception('Terdapat cuti yang bertabrakan dengan tanggal yang dipilih');
                }

                $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
                $validated['created_by'] = auth()->id();
                $validated['total_hari'] = $totalHari;

                Cuti::create($validated);
            });

            return redirect()->route('perusahaan.cuti.index')
                ->with('success', 'Permintaan cuti berhasil dibuat');

        } catch (\Exception $e) {
            \Log::error('Error in CutiController@store: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.cuti.index')
                ->with('error', 'Terjadi kesalahan saat menyimpan data cuti. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function show(Cuti $cuti)
    {
        $cuti->load(['karyawan', 'project', 'createdBy', 'approvedBy']);
        
        return view('perusahaan.cuti.show', compact('cuti'));
    }

    public function edit(Cuti $cuti)
    {
        if (!$cuti->canEdit()) {
            return redirect()->route('perusahaan.cuti.index')
                ->with('error', 'Permintaan cuti tidak dapat diedit');
        }

        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.cuti.edit', compact('cuti', 'projects'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        if (!$cuti->canEdit()) {
            return redirect()->route('perusahaan.cuti.index')
                ->with('error', 'Permintaan cuti tidak dapat diedit');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,menikah,khitan,baptis,keluarga_meninggal,lainnya',
            'alasan' => 'required|string|max:1000',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'karyawan_id.required' => 'Karyawan wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai cuti wajib diisi',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai cuti tidak boleh kurang dari hari ini',
            'tanggal_selesai.required' => 'Tanggal selesai cuti wajib diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai cuti harus setelah atau sama dengan tanggal mulai',
            'jenis_cuti.required' => 'Jenis cuti wajib dipilih',
            'alasan.required' => 'Alasan cuti wajib diisi',
            'alasan.max' => 'Alasan cuti maksimal 1000 karakter',
        ]);

        try {
            DB::transaction(function () use ($validated, $cuti) {
                // Calculate total hari
                $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
                $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);
                $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

                // Validate batas cuti tahunan jika jenis cuti adalah tahunan
                if ($validated['jenis_cuti'] === 'tahunan') {
                    $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
                    $project = Project::findOrFail($validated['project_id']);
                    
                    // Hitung total cuti tahunan yang sudah diambil tahun ini (exclude current cuti)
                    $currentYear = now()->year;
                    $cutiTahunanTerpakai = Cuti::where('karyawan_id', $validated['karyawan_id'])
                        ->where('jenis_cuti', 'tahunan')
                        ->where('status', 'approved')
                        ->where('id', '!=', $cuti->id)
                        ->whereYear('tanggal_mulai', $currentYear)
                        ->sum('total_hari');
                    
                    $sisaCuti = $project->batas_cuti_tahunan - $cutiTahunanTerpakai;
                    
                    if ($totalHari > $sisaCuti) {
                        throw new \Exception("Permintaan cuti melebihi sisa cuti tahunan. Sisa cuti: {$sisaCuti} hari");
                    }
                }

                // Check for overlapping cuti (exclude current cuti)
                $overlapping = Cuti::where('karyawan_id', $validated['karyawan_id'])
                    ->where('status', '!=', 'rejected')
                    ->where('id', '!=', $cuti->id)
                    ->byDateRange($validated['tanggal_mulai'], $validated['tanggal_selesai'])
                    ->exists();

                if ($overlapping) {
                    throw new \Exception('Terdapat cuti yang bertabrakan dengan tanggal yang dipilih');
                }

                $validated['total_hari'] = $totalHari;

                $cuti->update($validated);
            });

            return redirect()->route('perusahaan.cuti.index')
                ->with('success', 'Permintaan cuti berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate permintaan cuti: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Cuti $cuti)
    {
        if (!$cuti->canDelete()) {
            return redirect()->route('perusahaan.cuti.index')
                ->with('error', 'Permintaan cuti tidak dapat dihapus');
        }

        $cuti->delete();

        return redirect()->route('perusahaan.cuti.index')
            ->with('success', 'Permintaan cuti berhasil dihapus');
    }

    public function approve(Request $request, Cuti $cuti)
    {
        if (!$cuti->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan cuti tidak dapat disetujui. Role Anda: ' . auth()->user()->role . ', Status cuti: ' . $cuti->status
            ], 403);
        }

        $validated = $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
        ]);

        $cuti->update([
            'status' => 'approved',
            'catatan_approval' => $validated['catatan_approval'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Calculate updated remaining leave info for response
        $remainingLeaveInfo = null;
        if ($cuti->jenis_cuti === 'tahunan') {
            $currentYear = now()->year;
            $cutiTahunanTerpakai = Cuti::where('karyawan_id', $cuti->karyawan_id)
                ->where('jenis_cuti', 'tahunan')
                ->where('status', 'approved')
                ->whereYear('tanggal_mulai', $currentYear)
                ->sum('total_hari');
            
            $batasCutiTahunan = $cuti->project->batas_cuti_tahunan ?? 12;
            $sisaCuti = $batasCutiTahunan - $cutiTahunanTerpakai;
            
            $remainingLeaveInfo = [
                'sisa_cuti' => $sisaCuti,
                'cuti_terpakai' => $cutiTahunanTerpakai,
                'batas_cuti' => $batasCutiTahunan
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Permintaan cuti berhasil disetujui',
            'remaining_leave' => $remainingLeaveInfo
        ]);
    }

    public function reject(Request $request, Cuti $cuti)
    {
        if (!$cuti->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan cuti tidak dapat ditolak'
            ], 403);
        }

        $validated = $request->validate([
            'catatan_approval' => 'required|string|max:500',
        ], [
            'catatan_approval.required' => 'Alasan penolakan wajib diisi',
        ]);

        $cuti->update([
            'status' => 'rejected',
            'catatan_approval' => $validated['catatan_approval'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan cuti berhasil ditolak'
        ]);
    }

    public function getKaryawansByProject($projectId)
    {
        $karyawans = Karyawan::where('project_id', $projectId)
            ->where('is_active', true)
            ->select('id', 'nik_karyawan', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json($karyawans);
    }

    public function getSisaCutiTahunan($karyawanId, $projectId = null)
    {
        try {
            $sisaCuti = Cuti::getSisaCutiTahunan($karyawanId, $projectId);
            
            return response()->json([
                'success' => true,
                'data' => $sisaCuti
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sisa cuti: ' . $e->getMessage()
            ], 500);
        }
    }
}