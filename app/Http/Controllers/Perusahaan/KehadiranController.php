<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\Area;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KehadiranController extends Controller
{
    public function kehadiran(Request $request)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $areas = Area::select('id', 'nama')->orderBy('nama')->get();
        
        // Filter values
        $projectId = $request->project_id;
        $areaId = $request->area_id;
        $karyawanSearch = $request->karyawan_search;
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::today();
        $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir) : Carbon::today();
        $statusFilter = $request->status;
        
        // Validasi: Project wajib dipilih untuk performa
        if (!$projectId) {
            return view('perusahaan.kehadiran.kehadiran', compact(
                'projects',
                'areas',
                'projectId',
                'areaId',
                'karyawanSearch',
                'tanggalMulai',
                'tanggalAkhir',
                'statusFilter'
            ))->with([
                'kehadirans' => collect(),
                'summary' => [
                    'hadir' => 0,
                    'terlambat' => 0,
                    'pulang_cepat' => 0,
                    'alpa' => 0,
                    'on_radius' => 0,
                    'off_radius' => 0,
                ],
                'tingkatKehadiran' => 0,
            ]);
        }
        
        // Validasi: Maksimal 31 hari untuk performa
        $daysDiff = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
        if ($daysDiff > 31) {
            return back()->with('error', 'Periode maksimal 31 hari untuk performa optimal');
        }
        
        // Get kehadiran with optimized query
        $kehadiranQuery = Kehadiran::select([
                'id',
                'karyawan_id',
                'project_id',
                'shift_id',
                'tanggal',
                'jam_masuk',
                'jam_keluar',
                'status',
                'on_radius',
                'durasi_kerja'
            ])
            ->with([
                'karyawan:id,nama_lengkap,nik_karyawan,jabatan_id,foto',
                'karyawan.jabatan:id,nama',
                'project:id,nama',
                'shift:id,kode_shift,nama_shift,warna'
            ])
            ->where('project_id', $projectId)
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')]);
        
        if ($statusFilter) {
            $kehadiranQuery->where('status', $statusFilter);
        }
        
        if ($karyawanSearch) {
            $kehadiranQuery->whereHas('karyawan', function($q) use ($karyawanSearch) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . strtolower($karyawanSearch) . '%'])
                  ->orWhereRaw('LOWER(nik_karyawan) LIKE ?', ['%' . strtolower($karyawanSearch) . '%']);
            });
        }
        
        $kehadirans = $kehadiranQuery->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->paginate(50)
            ->appends($request->all());
        
        // Calculate summary statistics with caching
        $cacheKey = "kehadiran_summary_{$projectId}_{$tanggalMulai->format('Ymd')}_{$tanggalAkhir->format('Ymd')}_{$statusFilter}";
        
        $summary = cache()->remember($cacheKey, 300, function() use ($projectId, $tanggalMulai, $tanggalAkhir, $statusFilter) {
            $summaryQuery = Kehadiran::where('project_id', $projectId)
                ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')]);
            
            if ($statusFilter) {
                $summaryQuery->where('status', $statusFilter);
            }
            
            // Get all kehadiran with shift for real-time calculation
            $allKehadiran = Kehadiran::with('shift')
                ->where('project_id', $projectId)
                ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                ->whereNotNull('shift_id')
                ->whereNotNull('jam_masuk')
                ->whereNotNull('jam_keluar')
                ->whereIn('status', ['hadir', 'terlambat', 'pulang_cepat', 'terlambat_pulang_cepat'])
                ->get();
            
            $terlambatCount = 0;
            $pulangCepatCount = 0;
            $terlambatPulangCepatCount = 0;
            
            foreach ($allKehadiran as $kehadiran) {
                if (!$kehadiran->shift) continue;
                
                try {
                    $masuk = Carbon::parse($kehadiran->jam_masuk);
                    $keluar = Carbon::parse($kehadiran->jam_keluar);
                    $shiftStart = Carbon::createFromFormat('H:i', substr($kehadiran->shift->jam_mulai, 0, 5));
                    $shiftEnd = Carbon::createFromFormat('H:i', substr($kehadiran->shift->jam_selesai, 0, 5));
                    
                    $masukTime = Carbon::createFromFormat('H:i', $masuk->format('H:i'));
                    $keluarTime = Carbon::createFromFormat('H:i', $keluar->format('H:i'));
                    
                    $batasTerlambat = $shiftStart->copy()->addMinutes(15);
                    $batasPulangCepat = $shiftEnd->copy()->subMinutes(15);
                    
                    $isTerlambat = $masukTime->gt($batasTerlambat);
                    $isPulangCepat = $keluarTime->lt($batasPulangCepat);
                    
                    // Count based on actual conditions
                    if ($isTerlambat && $isPulangCepat) {
                        $terlambatPulangCepatCount++;
                    } elseif ($isTerlambat) {
                        $terlambatCount++;
                    } elseif ($isPulangCepat) {
                        $pulangCepatCount++;
                    }
                } catch (\Exception $e) {
                    // Skip if calculation fails
                }
            }
            
            return [
                'hadir' => (clone $summaryQuery)->where('status', 'hadir')->count(),
                'terlambat' => $terlambatCount,
                'pulang_cepat' => $pulangCepatCount,
                'terlambat_pulang_cepat' => $terlambatPulangCepatCount,
                'alpa' => (clone $summaryQuery)->where('status', 'alpa')->count(),
                'on_radius' => (clone $summaryQuery)->where('on_radius', true)->count(),
                'off_radius' => (clone $summaryQuery)->where('on_radius', false)->count(),
            ];
        });
        
        // Calculate attendance rate
        $totalKehadiran = array_sum($summary);
        $tingkatKehadiran = $totalKehadiran > 0 
            ? round(($summary['hadir'] + $summary['terlambat'] + $summary['pulang_cepat']) / $totalKehadiran * 100, 1)
            : 0;
        
        return view('perusahaan.kehadiran.kehadiran', compact(
            'projects',
            'areas',
            'kehadirans',
            'summary',
            'tingkatKehadiran',
            'projectId',
            'areaId',
            'karyawanSearch',
            'tanggalMulai',
            'tanggalAkhir',
            'statusFilter'
        ));
    }

    public function schedule()
    {
        return view('perusahaan.kehadiran.schedule');
    }

    public function lokasiAbsensi()
    {
        return view('perusahaan.kehadiran.lokasi-absensi');
    }

    public function manajemenShift()
    {
        return view('perusahaan.kehadiran.manajemen-shift');
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
        
        // Validasi: Maksimal 31 hari
        $daysDiff = Carbon::parse($tanggalMulai)->diffInDays(Carbon::parse($tanggalAkhir)) + 1;
        if ($daysDiff > 31) {
            return back()->with('error', 'Periode maksimal 31 hari');
        }
        
        $project = Project::find($projectId);
        $fileName = 'Template_Kehadiran_' . str_replace(' ', '_', $project->nama) . '_' . Carbon::parse($tanggalMulai)->format('Ymd') . '-' . Carbon::parse($tanggalAkhir)->format('Ymd') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KehadiranTemplateExport($projectId, $tanggalMulai, $tanggalAkhir, $perusahaanId),
            $fileName
        );
    }
    
    public function importExcel(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);
        
        $perusahaanId = auth()->user()->perusahaan_id;
        $projectId = $request->project_id;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        
        // Validasi: Maksimal 31 hari
        $daysDiff = Carbon::parse($tanggalMulai)->diffInDays(Carbon::parse($tanggalAkhir)) + 1;
        if ($daysDiff > 31) {
            return back()->with('error', 'Periode maksimal 31 hari');
        }
        
        try {
            $import = new \App\Imports\KehadiranImport($perusahaanId, $projectId, $tanggalMulai, $tanggalAkhir);
            
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
            
            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();
            $skippedCount = $import->getSkippedCount();
            
            $message = "Import selesai: {$successCount} kehadiran berhasil diimport";
            
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} data di-skip (sudah ada dari aplikasi)";
            }
            
            if (count($errors) > 0) {
                $errorMessage = $message . ". Error: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $errorMessage .= " ... dan " . (count($errors) - 3) . " error lainnya";
                }
                return back()->with('warning', $errorMessage);
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $kehadiran = Kehadiran::with(['karyawan.jabatan', 'project', 'shift'])
            ->findOrFail($id);
        
        return response()->json($kehadiran);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'jam_keluar' => 'nullable',
            'status' => 'required|in:hadir,terlambat,pulang_cepat,terlambat_pulang_cepat,alpa,izin,sakit,cuti',
            'keterangan' => 'nullable|string',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'karyawan_id.required' => 'Karyawan wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jam_masuk.required' => 'Jam masuk wajib diisi',
            'status.required' => 'Status wajib dipilih',
        ]);
        
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Check if kehadiran already exists
        $exists = Kehadiran::where('karyawan_id', $request->karyawan_id)
            ->where('tanggal', $request->tanggal)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Kehadiran untuk karyawan ini pada tanggal tersebut sudah ada');
        }
        
        // Get shift from jadwal
        $jadwalShift = \App\Models\JadwalShift::with('shift')
            ->where('karyawan_id', $request->karyawan_id)
            ->where('tanggal', $request->tanggal)
            ->first();
        
        $shiftId = $jadwalShift ? $jadwalShift->shift_id : null;
        $shift = $jadwalShift ? $jadwalShift->shift : null;
        
        // Calculate status based on shift time (if shift exists and status is hadir/terlambat/pulang_cepat/terlambat_pulang_cepat)
        $status = $request->status;
        if ($shift && in_array($status, ['hadir', 'terlambat', 'pulang_cepat', 'terlambat_pulang_cepat']) && $request->jam_masuk && $request->jam_keluar) {
            $status = $this->calculateStatus($request->jam_masuk, $request->jam_keluar, $shift->jam_mulai, $shift->jam_selesai);
        }
        
        // Calculate durasi kerja
        $durasiKerja = null;
        if ($request->jam_masuk && $request->jam_keluar) {
            try {
                $masuk = Carbon::createFromFormat('H:i', $request->jam_masuk);
                $keluar = Carbon::createFromFormat('H:i', $request->jam_keluar);
                
                if ($keluar->lt($masuk)) {
                    $keluar->addDay();
                }
                
                $durasiKerja = $masuk->diffInMinutes($keluar);
            } catch (\Exception $e) {
                // Ignore
            }
        }
        
        Kehadiran::create([
            'karyawan_id' => $request->karyawan_id,
            'perusahaan_id' => $perusahaanId,
            'project_id' => $request->project_id,
            'shift_id' => $shiftId,
            'tanggal' => $request->tanggal,
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
            'status' => $status,
            'keterangan' => $request->keterangan,
            'durasi_kerja' => $durasiKerja,
            'on_radius' => true,
            'sumber_data' => 'excel', // Mark as manual input (same as excel)
        ]);
        
        return back()->with('success', 'Kehadiran berhasil ditambahkan');
    }
    
    /**
     * Calculate attendance status based on actual time vs shift time
     * 
     * @param string $jamMasuk Actual check-in time (datetime or HH:MM)
     * @param string $jamKeluar Actual check-out time (datetime or HH:MM)
     * @param string $shiftMulai Shift start time (HH:MM:SS or HH:MM)
     * @param string $shiftSelesai Shift end time (HH:MM:SS or HH:MM)
     * @return string Status: hadir, terlambat, pulang_cepat
     */
    private function calculateStatus($jamMasuk, $jamKeluar, $shiftMulai, $shiftSelesai)
    {
        try {
            // Parse times - handle both datetime and time-only formats
            if (strlen($jamMasuk) > 8) {
                // Datetime format: 2026-01-15 07:00:00
                $masuk = Carbon::parse($jamMasuk);
            } else {
                // Time only format: 07:00 or 07:00:00
                $masuk = Carbon::createFromFormat('H:i', substr($jamMasuk, 0, 5));
            }
            
            if ($jamKeluar) {
                if (strlen($jamKeluar) > 8) {
                    $keluar = Carbon::parse($jamKeluar);
                } else {
                    $keluar = Carbon::createFromFormat('H:i', substr($jamKeluar, 0, 5));
                }
            } else {
                $keluar = null;
            }
            
            $shiftStart = Carbon::createFromFormat('H:i', substr($shiftMulai, 0, 5));
            $shiftEnd = Carbon::createFromFormat('H:i', substr($shiftSelesai, 0, 5));
            
            // Toleransi: 15 menit untuk terlambat, 15 menit untuk pulang cepat
            $toleransiTerlambat = 15; // minutes
            $toleransiPulangCepat = 15; // minutes
            
            $isTerlambat = false;
            $isPulangCepat = false;
            
            // Extract time only for comparison
            $masukTime = Carbon::createFromFormat('H:i', $masuk->format('H:i'));
            $keluarTime = $keluar ? Carbon::createFromFormat('H:i', $keluar->format('H:i')) : null;
            
            // Check terlambat: jam masuk > shift mulai + toleransi
            $batasTerlambat = $shiftStart->copy()->addMinutes($toleransiTerlambat);
            if ($masukTime->gt($batasTerlambat)) {
                $isTerlambat = true;
            }
            
            // Check pulang cepat: jam keluar < shift selesai - toleransi
            if ($keluarTime) {
                $batasPulangCepat = $shiftEnd->copy()->subMinutes($toleransiPulangCepat);
                
                // Handle overnight shift
                if ($shiftEnd->lt($shiftStart)) {
                    $keluarTime->addDay();
                    $shiftEnd->addDay();
                    $batasPulangCepat = $shiftEnd->copy()->subMinutes($toleransiPulangCepat);
                }
                
                if ($keluarTime->lt($batasPulangCepat)) {
                    $isPulangCepat = true;
                }
            }
            
            // Determine final status
            if ($isTerlambat && $isPulangCepat) {
                // Both violations occurred
                return 'terlambat_pulang_cepat';
            } elseif ($isTerlambat) {
                return 'terlambat';
            } elseif ($isPulangCepat) {
                return 'pulang_cepat';
            } else {
                return 'hadir';
            }
            
        } catch (\Exception $e) {
            // If calculation fails, default to hadir
            return 'hadir';
        }
    }
    
    public function getKaryawanByProject($projectId)
    {
        $karyawans = \App\Models\Karyawan::select('id', 'nama_lengkap', 'nik_karyawan')
            ->where('project_id', $projectId)
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();
        
        return response()->json($karyawans);
    }
    
    public function rekap(Request $request)
    {
        $projects = Project::orderBy('nama')->get();
        $jabatans = \App\Models\Jabatan::orderBy('nama')->get();
        
        // Default values
        $projectId = $request->project_id;
        $jabatanId = $request->jabatan_id;
        $karyawanSearch = $request->karyawan_search;
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->startOfMonth();
        $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir) : Carbon::now()->endOfMonth();
        
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
        $kehadirans = collect();
        
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
            
            // Get kehadiran only for current page karyawans
            $karyawanIds = $karyawans->pluck('id')->toArray();
            
            if (!empty($karyawanIds)) {
                $kehadirans = Kehadiran::with('shift')
                    ->whereIn('karyawan_id', $karyawanIds)
                    ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                    ->get()
                    ->groupBy(function($item) {
                        return $item->karyawan_id . '_' . Carbon::parse($item->tanggal)->format('Y-m-d');
                    });
            }
        }
        
        return view('perusahaan.kehadiran.rekap', compact(
            'projects',
            'jabatans',
            'karyawans',
            'dates',
            'kehadirans',
            'projectId',
            'jabatanId',
            'karyawanSearch',
            'tanggalMulai',
            'tanggalAkhir'
        ));
    }
    
    public function rekapPdf(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        
        $projectId = $request->project_id;
        $jabatanId = $request->jabatan_id;
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);
        
        // Validasi: maksimal 31 hari
        $daysDiff = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
        if ($daysDiff > 31) {
            return back()->with('error', 'Periode maksimal 31 hari untuk PDF');
        }
        
        $project = Project::findOrFail($projectId);
        
        // Generate date range
        $dates = [];
        $currentDate = $tanggalMulai->copy();
        while ($currentDate <= $tanggalAkhir) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        // Get all karyawan (no pagination for PDF)
        $karyawansQuery = Karyawan::with(['jabatan'])
            ->where('project_id', $projectId)
            ->where('is_active', true);
        
        if ($jabatanId) {
            $karyawansQuery->where('jabatan_id', $jabatanId);
        }
        
        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->get();
        
        // Get all kehadiran
        $karyawanIds = $karyawans->pluck('id')->toArray();
        $kehadirans = collect();
        
        if (!empty($karyawanIds)) {
            $kehadirans = Kehadiran::with('shift')
                ->whereIn('karyawan_id', $karyawanIds)
                ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                ->get()
                ->groupBy(function($item) {
                    return $item->karyawan_id . '_' . Carbon::parse($item->tanggal)->format('Y-m-d');
                });
        }
        
        $pdf = Pdf::loadView('perusahaan.kehadiran.rekap-pdf', compact(
            'project',
            'karyawans',
            'dates',
            'kehadirans',
            'tanggalMulai',
            'tanggalAkhir'
        ));
        
        $pdf->setPaper('a4', 'landscape');
        
        $fileName = 'Rekap_Kehadiran_' . str_replace(' ', '_', $project->nama) . '_' . $tanggalMulai->format('Ymd') . '-' . $tanggalAkhir->format('Ymd') . '.pdf';
        
        return $pdf->download($fileName);
    }
}
