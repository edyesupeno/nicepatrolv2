<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\Shift;
use App\Models\JadwalShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::orderBy('nama')->get();
        $jabatans = \App\Models\Jabatan::orderBy('nama')->get();
        
        // Default values - 7 hari (1 minggu)
        $projectId = $request->project_id;
        
        // Handle quick actions
        if ($request->quick_action) {
            switch ($request->quick_action) {
                case 'minggu_ini':
                    $tanggalMulai = Carbon::now()->startOfWeek();
                    $tanggalAkhir = Carbon::now()->endOfWeek();
                    break;
                case 'minggu_depan':
                    $tanggalMulai = Carbon::now()->addWeek()->startOfWeek();
                    $tanggalAkhir = Carbon::now()->addWeek()->endOfWeek();
                    break;
                case 'bulan_ini':
                    $tanggalMulai = Carbon::now()->startOfMonth();
                    $tanggalAkhir = Carbon::now()->endOfMonth();
                    break;
                default:
                    $tanggalMulai = Carbon::now()->startOfWeek();
                    $tanggalAkhir = Carbon::now()->endOfWeek();
            }
        } else {
            $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfWeek();
            $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir) : Carbon::now()->endOfWeek();
        }
        
        $karyawanSearch = $request->karyawan_search;
        $shiftFilter = $request->shift_id;
        
        // Generate date range
        $dates = [];
        $currentDate = $tanggalMulai->copy();
        while ($currentDate <= $tanggalAkhir) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get karyawan with pagination
        $karyawansQuery = Karyawan::with(['jabatan', 'project'])
            ->where('is_active', true);
        
        if ($projectId) {
            $karyawansQuery->where('project_id', $projectId);
        }
        
        if ($karyawanSearch) {
            $karyawansQuery->where(function($q) use ($karyawanSearch) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . strtolower($karyawanSearch) . '%'])
                  ->orWhereRaw('LOWER(nik_karyawan) LIKE ?', ['%' . strtolower($karyawanSearch) . '%']);
            });
        }
        
        $karyawans = $karyawansQuery->orderBy('nama_lengkap')
            ->paginate(20)
            ->appends($request->all());
        
        // Get shifts for the selected project
        $shifts = [];
        if ($projectId) {
            $shifts = Shift::where('project_id', $projectId)->orderBy('kode_shift')->get();
        }
        
        // Get all shifts for generate modal
        $allShifts = Shift::orderBy('kode_shift')->get();
        
        // Get jadwal shifts for the karyawans and date range
        $karyawanIds = $karyawans->pluck('id')->toArray();
        $jadwalShifts = JadwalShift::with('shift')
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->get()
            ->groupBy(function($item) {
                return $item->karyawan_id . '_' . $item->tanggal->format('Y-m-d');
            });
        
        return view('perusahaan.schedule.index', compact(
            'projects',
            'jabatans',
            'karyawans',
            'dates',
            'shifts',
            'allShifts',
            'jadwalShifts',
            'projectId',
            'tanggalMulai',
            'tanggalAkhir',
            'karyawanSearch',
            'shiftFilter'
        ));
    }
    
    public function copyLastWeek(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);
        
        $karyawanId = $validated['karyawan_id'];
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Current range yang sedang ditampilkan
        $currentStart = Carbon::parse($validated['tanggal_mulai']);
        $currentEnd = Carbon::parse($validated['tanggal_akhir']);
        
        // Calculate previous week range (7 hari sebelumnya)
        $daysDiff = $currentStart->diffInDays($currentEnd) + 1;
        $previousStart = $currentStart->copy()->subDays($daysDiff);
        $previousEnd = $currentEnd->copy()->subDays($daysDiff);
        
        // Get previous week's schedule
        $previousSchedule = JadwalShift::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$previousStart->format('Y-m-d'), $previousEnd->format('Y-m-d')])
            ->get();
        
        if ($previousSchedule->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak ada jadwal pada periode sebelumnya (' . $previousStart->format('d M') . ' - ' . $previousEnd->format('d M Y') . ')'
            ]);
        }
        
        // Copy to current range
        $count = 0;
        foreach ($previousSchedule as $jadwal) {
            $daysFromStart = $previousStart->diffInDays($jadwal->tanggal);
            $newDate = $currentStart->copy()->addDays($daysFromStart);
            
            JadwalShift::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId,
                    'tanggal' => $newDate->format('Y-m-d'),
                ],
                [
                    'shift_id' => $jadwal->shift_id,
                    'perusahaan_id' => $perusahaanId,
                ]
            );
            $count++;
        }
        
        return response()->json([
            'success' => true, 
            'message' => "Berhasil menyalin {$count} hari jadwal dari periode sebelumnya"
        ]);
    }
    
    public function setMonthSchedule(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'shift_id' => 'required|exists:shifts,id',
            'bulan' => 'required|date_format:Y-m',
            'hari_kerja' => 'array',
            'hari_kerja.*' => 'in:0,1,2,3,4,5,6', // 0=Minggu, 6=Sabtu
        ]);
        
        $karyawanId = $validated['karyawan_id'];
        $shiftId = $validated['shift_id'];
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Parse bulan
        $bulan = Carbon::parse($validated['bulan'] . '-01');
        $startDate = $bulan->copy()->startOfMonth();
        $endDate = $bulan->copy()->endOfMonth();
        
        $hariKerja = $validated['hari_kerja'] ?? [1, 2, 3, 4, 5]; // Default Senin-Jumat
        
        // Loop through all days in month
        $currentDate = $startDate->copy();
        $count = 0;
        
        while ($currentDate <= $endDate) {
            // Check if this day is a working day
            if (in_array($currentDate->dayOfWeek, $hariKerja)) {
                JadwalShift::updateOrCreate(
                    [
                        'karyawan_id' => $karyawanId,
                        'tanggal' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'shift_id' => $shiftId,
                        'perusahaan_id' => $perusahaanId,
                    ]
                );
                $count++;
            }
            
            $currentDate->addDay();
        }
        
        return response()->json(['success' => true, 'message' => "Berhasil mengatur {$count} hari jadwal"]);
    }
    
    public function generateByJabatan(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'shift_id' => 'required|exists:shifts,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get all karyawan with this jabatan in this project
        $karyawans = Karyawan::where('project_id', $validated['project_id'])
            ->where('jabatan_id', $validated['jabatan_id'])
            ->where('is_active', true)
            ->get();
        
        if ($karyawans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada karyawan aktif dengan jabatan ini di project tersebut'
            ]);
        }
        
        $startDate = Carbon::parse($validated['tanggal_mulai']);
        $endDate = Carbon::parse($validated['tanggal_akhir']);
        
        $totalDays = 0;
        $totalKaryawan = $karyawans->count();
        
        // Loop through each karyawan
        foreach ($karyawans as $karyawan) {
            $currentDate = $startDate->copy();
            
            // Loop through all days in range
            while ($currentDate <= $endDate) {
                JadwalShift::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tanggal' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'shift_id' => $validated['shift_id'],
                        'perusahaan_id' => $perusahaanId,
                    ]
                );
                
                $currentDate->addDay();
            }
            
            $totalDays = $startDate->diffInDays($endDate) + 1;
        }
        
        return response()->json([
            'success' => true,
            'message' => "Berhasil generate jadwal untuk {$totalKaryawan} karyawan ({$totalDays} hari)"
        ]);
    }
    
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        
        $projectId = $request->project_id;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $project = Project::find($projectId);
        $fileName = 'Template_Jadwal_' . str_replace(' ', '_', $project->nama) . '_' . date('Ymd') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\JadwalShiftTemplateExport($projectId, $tanggalMulai, $tanggalAkhir, $perusahaanId),
            $fileName
        );
    }
    
    public function importExcel(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);
        
        // Validasi: Project harus punya shift
        $shiftCount = Shift::where('project_id', $request->project_id)->count();
        if ($shiftCount == 0) {
            return back()->with('error', 'Project ini belum memiliki shift. Silakan buat shift terlebih dahulu.');
        }
        
        $perusahaanId = auth()->user()->perusahaan_id;
        $projectId = $request->project_id;
        
        try {
            $import = new \App\Imports\JadwalShiftImport($perusahaanId, $projectId);
            
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
            
            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();
            
            if (count($errors) > 0) {
                // Show only first 3 errors for better UX
                $errorMessage = "Import selesai dengan {$successCount} jadwal berhasil. ";
                $errorMessage .= "Namun ada beberapa error: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $errorMessage .= " ... dan " . (count($errors) - 3) . " error lainnya";
                }
                return back()->with('warning', $errorMessage);
            }
            
            return back()->with('success', "Berhasil import {$successCount} jadwal dari Excel");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
    
    public function updateShift(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);
        
        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        
        // Update or create jadwal shift
        JadwalShift::updateOrCreate(
            [
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal' => $validated['tanggal'],
            ],
            [
                'shift_id' => $validated['shift_id'],
                'perusahaan_id' => $validated['perusahaan_id'],
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Shift berhasil diupdate']);
    }
    
    public function rekap(Request $request)
    {
        $projects = Project::orderBy('nama')->get();
        $jabatans = \App\Models\Jabatan::orderBy('nama')->get();
        
        // Default values - 7 hari untuk menghindari error 31 hari
        $projectId = $request->project_id;
        $jabatanId = $request->jabatan_id;
        $karyawanSearch = $request->karyawan_search;
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now();
        $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir) : Carbon::now()->addDays(6);
        
        // Validasi: maksimal 31 hari
        if ($request->has('project_id')) {
            $daysDiff = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
            if ($daysDiff > 31) {
                return back()->with('error', 'Periode maksimal 31 hari');
            }
        }
        
        // Generate date range
        $dates = [];
        $currentDate = $tanggalMulai->copy();
        while ($currentDate <= $tanggalAkhir) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        $karyawans = collect();
        $jadwalShifts = collect();
        $shifts = collect();
        
        if ($projectId) {
            // Get karyawan with filters and pagination
            $karyawansQuery = Karyawan::with(['jabatan'])
                ->where('project_id', $projectId)
                ->where('is_active', true);
            
            // Filter by jabatan
            if ($jabatanId) {
                $karyawansQuery->where('jabatan_id', $jabatanId);
            }
            
            // Search by name or NIK
            if ($karyawanSearch) {
                $karyawansQuery->where(function($q) use ($karyawanSearch) {
                    $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . strtolower($karyawanSearch) . '%'])
                      ->orWhereRaw('LOWER(nik_karyawan) LIKE ?', ['%' . strtolower($karyawanSearch) . '%']);
                });
            }
            
            $karyawans = $karyawansQuery->orderBy('nama_lengkap')
                ->paginate(50)
                ->appends($request->all());
            
            // Get jadwal shifts only for current page karyawans
            $karyawanIds = $karyawans->pluck('id')->toArray();
            
            if (!empty($karyawanIds)) {
                $jadwalShifts = JadwalShift::with('shift')
                    ->whereIn('karyawan_id', $karyawanIds)
                    ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                    ->get()
                    ->groupBy(function($item) {
                        return $item->karyawan_id . '_' . $item->tanggal->format('Y-m-d');
                    });
            }
            
            // Get shifts for legend
            $shifts = Shift::where('project_id', $projectId)->orderBy('kode_shift')->get();
        }
        
        return view('perusahaan.schedule.rekap', compact(
            'projects',
            'jabatans',
            'karyawans',
            'dates',
            'jadwalShifts',
            'shifts',
            'projectId',
            'jabatanId',
            'karyawanSearch',
            'tanggalMulai',
            'tanggalAkhir'
        ));
    }
    
    public function exportPdf(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        
        $projectId = $request->project_id;
        $jabatanId = $request->jabatan_id;
        $karyawanSearch = $request->karyawan_search;
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);
        
        // Validasi: maksimal 31 hari
        $daysDiff = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
        if ($daysDiff > 31) {
            return back()->with('error', 'Periode maksimal 31 hari');
        }
        
        // Generate date range
        $dates = [];
        $currentDate = $tanggalMulai->copy();
        while ($currentDate <= $tanggalAkhir) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get project
        $project = Project::find($projectId);
        
        // Get karyawan (NO PAGINATION for PDF - get all)
        $karyawansQuery = Karyawan::with(['jabatan'])
            ->where('project_id', $projectId)
            ->where('is_active', true);
        
        if ($jabatanId) {
            $karyawansQuery->where('jabatan_id', $jabatanId);
        }
        
        if ($karyawanSearch) {
            $karyawansQuery->where(function($q) use ($karyawanSearch) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . strtolower($karyawanSearch) . '%'])
                  ->orWhereRaw('LOWER(nik_karyawan) LIKE ?', ['%' . strtolower($karyawanSearch) . '%']);
            });
        }
        
        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->get();
        
        // Get jadwal shifts
        $karyawanIds = $karyawans->pluck('id')->toArray();
        $jadwalShifts = collect();
        
        if (!empty($karyawanIds)) {
            $jadwalShifts = JadwalShift::with('shift')
                ->whereIn('karyawan_id', $karyawanIds)
                ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                ->get()
                ->groupBy(function($item) {
                    return $item->karyawan_id . '_' . $item->tanggal->format('Y-m-d');
                });
        }
        
        // Get shifts for legend
        $shifts = Shift::where('project_id', $projectId)->orderBy('kode_shift')->get();
        
        $pdf = \PDF::loadView('perusahaan.schedule.rekap-pdf', compact(
            'project',
            'karyawans',
            'dates',
            'jadwalShifts',
            'shifts',
            'tanggalMulai',
            'tanggalAkhir'
        ));
        
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'Rekap_Jadwal_' . str_replace(' ', '_', $project->nama) . '_' . $tanggalMulai->format('Ymd') . '-' . $tanggalAkhir->format('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }
}
