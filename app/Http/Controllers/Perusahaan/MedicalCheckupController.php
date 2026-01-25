<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\MedicalCheckup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MedicalCheckupController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::select([
                'id',
                'nik_karyawan', 
                'nama_lengkap',
                'project_id',
                'jabatan_id',
                'telepon'
            ])
            ->with([
                'project:id,nama',
                'jabatan:id,nama'
            ])
            ->where('is_active', true);

        // Filter berdasarkan project jika ada
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search berdasarkan nama atau NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_karyawan', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status medical checkup menggunakan subquery yang lebih efisien
        if ($request->filled('status_filter')) {
            $status = $request->status_filter;
            
            if ($status === 'no_checkup') {
                // Karyawan yang belum punya medical checkup
                $query->whereDoesntHave('medicalCheckups');
            } elseif ($status === 'expired') {
                // Karyawan dengan medical checkup expired (> 1 tahun)
                $oneYearAgo = now()->subYear()->toDateString();
                $query->whereHas('medicalCheckups', function($q) use ($oneYearAgo) {
                    $q->selectRaw('MAX(tanggal_checkup) as latest_checkup')
                      ->groupBy('karyawan_id')
                      ->havingRaw('MAX(tanggal_checkup) < ?', [$oneYearAgo]);
                });
            } elseif ($status === 'expiring_soon') {
                // Karyawan dengan medical checkup akan expired dalam 30 hari
                $oneYearAgo = now()->subYear()->toDateString();
                $thirtyDaysFromOneYearAgo = now()->subYear()->addDays(30)->toDateString();
                $query->whereHas('medicalCheckups', function($q) use ($oneYearAgo, $thirtyDaysFromOneYearAgo) {
                    $q->selectRaw('MAX(tanggal_checkup) as latest_checkup')
                      ->groupBy('karyawan_id')
                      ->havingRaw('MAX(tanggal_checkup) BETWEEN ? AND ?', [$oneYearAgo, $thirtyDaysFromOneYearAgo]);
                });
            }
        }

        // Pagination dengan limit yang wajar
        $perPage = 25; // Kurangi dari 50 ke 25 untuk performa lebih baik
        $karyawans = $query->paginate($perPage);

        // Load medical checkups hanya untuk data yang ditampilkan di halaman ini
        $karyawanIds = $karyawans->pluck('id')->toArray();
        $medicalCheckups = \App\Models\MedicalCheckup::select('id', 'karyawan_id', 'tanggal_checkup')
            ->whereIn('karyawan_id', $karyawanIds)
            ->orderBy('tanggal_checkup', 'desc')
            ->get()
            ->groupBy('karyawan_id');

        // Attach medical checkups ke karyawan
        foreach ($karyawans as $karyawan) {
            $karyawan->setRelation('medicalCheckups', 
                $medicalCheckups->get($karyawan->id, collect())
            );
        }

        // Hitung statistik dengan query yang lebih efisien
        $stats = $this->calculateStatsOptimized();

        // Get projects untuk filter
        $projects = \App\Models\Project::select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return view('perusahaan.medical-checkup.index', compact('karyawans', 'stats', 'projects'));
    }

    private function calculateStatsOptimized()
    {
        // Cache statistik selama 5 menit untuk mengurangi beban database
        $cacheKey = 'medical_checkup_stats_' . auth()->user()->perusahaan_id;
        
        return \Cache::remember($cacheKey, 300, function () {
            // Total karyawan aktif
            $totalKaryawan = Karyawan::where('is_active', true)->count();
            
            // Karyawan tanpa medical checkup
            $noCheckup = Karyawan::where('is_active', true)
                ->whereDoesntHave('medicalCheckups')
                ->count();

            // Untuk statistik yang memerlukan perhitungan tanggal, gunakan raw query yang lebih efisien
            $oneYearAgo = now()->subYear()->toDateString();
            $thirtyDaysFromOneYearAgo = now()->subYear()->addDays(30)->toDateString();

            // Karyawan dengan medical checkup valid (dalam 1 tahun terakhir)
            $validCheckup = \DB::table('karyawans as k')
                ->join(\DB::raw('(SELECT karyawan_id, MAX(tanggal_checkup) as latest_checkup FROM medical_checkups GROUP BY karyawan_id) as mc'), 'k.id', '=', 'mc.karyawan_id')
                ->where('k.is_active', true)
                ->where('k.perusahaan_id', auth()->user()->perusahaan_id)
                ->where('mc.latest_checkup', '>=', $oneYearAgo)
                ->count();

            // Karyawan dengan medical checkup expired
            $expiredCheckup = \DB::table('karyawans as k')
                ->join(\DB::raw('(SELECT karyawan_id, MAX(tanggal_checkup) as latest_checkup FROM medical_checkups GROUP BY karyawan_id) as mc'), 'k.id', '=', 'mc.karyawan_id')
                ->where('k.is_active', true)
                ->where('k.perusahaan_id', auth()->user()->perusahaan_id)
                ->where('mc.latest_checkup', '<', $oneYearAgo)
                ->count();

            // Karyawan yang akan expired dalam 30 hari
            $expiringSoon = \DB::table('karyawans as k')
                ->join(\DB::raw('(SELECT karyawan_id, MAX(tanggal_checkup) as latest_checkup FROM medical_checkups GROUP BY karyawan_id) as mc'), 'k.id', '=', 'mc.karyawan_id')
                ->where('k.is_active', true)
                ->where('k.perusahaan_id', auth()->user()->perusahaan_id)
                ->whereBetween('mc.latest_checkup', [$oneYearAgo, $thirtyDaysFromOneYearAgo])
                ->count();

            return [
                'total_karyawan' => $totalKaryawan,
                'valid_checkup' => $validCheckup,
                'expired_checkup' => $expiredCheckup,
                'expiring_soon' => $expiringSoon,
                'no_checkup' => $noCheckup,
            ];
        });
    }

    public function sendReminder(Request $request)
    {
        $request->validate([
            'karyawan_ids' => 'required|array',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'message_type' => 'required|in:expired,expiring_soon,no_checkup'
        ]);

        $karyawanIds = $request->karyawan_ids;
        $messageType = $request->message_type;

        // Get karyawan dengan nomor telepon
        $karyawans = Karyawan::whereIn('id', $karyawanIds)
            ->whereNotNull('telepon')
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($karyawans as $karyawan) {
            try {
                $message = $this->generateReminderMessage($karyawan, $messageType);
                
                // Send WhatsApp message (implement your WhatsApp service here)
                // $whatsappService = new WhatsAppService();
                // $whatsappService->sendMessage($karyawan->telepon, $message);
                
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to send medical checkup reminder', [
                    'karyawan_id' => $karyawan->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "Berhasil mengirim {$sentCount} reminder";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} gagal dikirim";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);
    }

    /**
     * Clear cache statistik medical checkup
     */
    public function clearStatsCache()
    {
        $cacheKey = 'medical_checkup_stats_' . auth()->user()->perusahaan_id;
        \Cache::forget($cacheKey);
    }

    private function generateReminderMessage($karyawan, $messageType)
    {
        $perusahaan = auth()->user()->perusahaan->nama;
        
        switch ($messageType) {
            case 'expired':
                return "Halo {$karyawan->nama_lengkap},\n\nMedical checkup Anda sudah expired. Mohon segera lakukan medical checkup terbaru.\n\nTerima kasih,\n{$perusahaan}";
                
            case 'expiring_soon':
                $latestCheckup = $karyawan->medicalCheckups()->latest('tanggal_checkup')->first();
                $expiredDate = Carbon::parse($latestCheckup->tanggal_checkup)->addYear();
                $daysLeft = Carbon::now()->diffInDays($expiredDate, false);
                
                return "Halo {$karyawan->nama_lengkap},\n\nMedical checkup Anda akan expired dalam {$daysLeft} hari (tanggal {$expiredDate->format('d/m/Y')}). Mohon segera jadwalkan medical checkup terbaru.\n\nTerima kasih,\n{$perusahaan}";
                
            case 'no_checkup':
                return "Halo {$karyawan->nama_lengkap},\n\nAnda belum memiliki data medical checkup. Mohon segera lakukan medical checkup dan upload hasilnya.\n\nTerima kasih,\n{$perusahaan}";
                
            default:
                return "Halo {$karyawan->nama_lengkap},\n\nMohon perhatikan status medical checkup Anda.\n\nTerima kasih,\n{$perusahaan}";
        }
    }

    public function export(Request $request)
    {
        // Implement export functionality if needed
        return response()->json([
            'success' => false,
            'message' => 'Export functionality will be implemented soon'
        ]);
    }

    /**
     * Store a new medical checkup for a karyawan
     */
    public function store(Request $request, $karyawanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        
        if (!$karyawanId) {
            return response()->json(['error' => 'Invalid karyawan ID'], 404);
        }

        $karyawan = Karyawan::findOrFail($karyawanId);

        $validated = $request->validate([
            'jenis_checkup' => 'required|string|max:255',
            'tanggal_checkup' => 'required|date',
            'status_kesehatan' => 'required|in:Sehat,Perlu Perhatian,Tidak Sehat',
            'tinggi_badan' => 'nullable|numeric|min:0',
            'berat_badan' => 'nullable|numeric|min:0',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'tekanan_darah' => 'nullable|string|max:20',
            'gula_darah' => 'nullable|numeric|min:0',
            'kolesterol' => 'nullable|numeric|min:0',
            'rumah_sakit' => 'nullable|string|max:255',
            'nama_dokter' => 'nullable|string|max:255',
            'diagnosis' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
        ], [
            'jenis_checkup.required' => 'Jenis checkup wajib diisi',
            'tanggal_checkup.required' => 'Tanggal checkup wajib diisi',
            'status_kesehatan.required' => 'Status kesehatan wajib dipilih',
        ]);

        $validated['karyawan_id'] = $karyawan->id;

        $medicalCheckup = MedicalCheckup::create($validated);

        // Clear cache statistik
        $this->clearStatsCache();

        return redirect()->back()->with('success', 'Data medical checkup berhasil ditambahkan');
    }

    /**
     * Update medical checkup
     */
    public function update(Request $request, $karyawanHashId, $checkupHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $checkupId = \Vinkla\Hashids\Facades\Hashids::decode($checkupHashId)[0] ?? null;
        
        if (!$karyawanId || !$checkupId) {
            return response()->json(['error' => 'Invalid ID'], 404);
        }

        $karyawan = Karyawan::findOrFail($karyawanId);
        $medicalCheckup = MedicalCheckup::where('karyawan_id', $karyawan->id)
                                      ->findOrFail($checkupId);

        $validated = $request->validate([
            'jenis_checkup' => 'required|string|max:255',
            'tanggal_checkup' => 'required|date',
            'status_kesehatan' => 'required|in:Sehat,Perlu Perhatian,Tidak Sehat',
            'tinggi_badan' => 'nullable|numeric|min:0',
            'berat_badan' => 'nullable|numeric|min:0',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'tekanan_darah' => 'nullable|string|max:20',
            'gula_darah' => 'nullable|numeric|min:0',
            'kolesterol' => 'nullable|numeric|min:0',
            'rumah_sakit' => 'nullable|string|max:255',
            'nama_dokter' => 'nullable|string|max:255',
            'diagnosis' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
        ]);

        $medicalCheckup->update($validated);

        // Clear cache statistik
        $this->clearStatsCache();

        return redirect()->back()->with('success', 'Data medical checkup berhasil diperbarui');
    }

    /**
     * Delete medical checkup
     */
    public function destroy($karyawanHashId, $checkupHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $checkupId = \Vinkla\Hashids\Facades\Hashids::decode($checkupHashId)[0] ?? null;
        
        if (!$karyawanId || !$checkupId) {
            return response()->json(['error' => 'Invalid ID'], 404);
        }

        $karyawan = Karyawan::findOrFail($karyawanId);
        $medicalCheckup = MedicalCheckup::where('karyawan_id', $karyawan->id)
                                      ->findOrFail($checkupId);

        $medicalCheckup->delete();

        // Clear cache statistik
        $this->clearStatsCache();

        return redirect()->back()->with('success', 'Data medical checkup berhasil dihapus');
    }
}