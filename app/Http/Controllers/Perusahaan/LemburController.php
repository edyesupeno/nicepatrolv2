<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\PayrollSetting;
use App\Models\TemplateKomponenGaji;
use App\Imports\LemburImport;
use App\Exports\LemburTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class LemburController extends Controller
{
    /**
     * Check if lemburs table exists
     */
    private function tableExists(): bool
    {
        try {
            return Schema::hasTable('lemburs');
        } catch (\Exception $e) {
            \Log::error('Error checking lemburs table: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get error response when table doesn't exist
     */
    private function getTableNotExistsResponse($redirectRoute = 'perusahaan.lembur.index')
    {
        $message = 'Fitur Lembur belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.';
        
        if ($redirectRoute === 'perusahaan.lembur.index') {
            // For index page, return view with empty data
            $lemburs = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                20, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')
                ->where('perusahaan_id', auth()->user()->perusahaan_id)
                ->orderBy('nama')
                ->get();

            $stats = [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
            ];

            return view('perusahaan.lembur.index', compact('lemburs', 'projects', 'stats'))
                ->with('info', $message);
        }
        
        return redirect()->route($redirectRoute)->with('error', $message);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Check if lemburs table exists
            if (!$this->tableExists()) {
                return $this->getTableNotExistsResponse('perusahaan.lembur.index');
            }

            $query = Lembur::with(['karyawan', 'project', 'approvedBy'])
                ->orderBy('created_at', 'desc');

            // Filter berdasarkan project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('tanggal_lembur', '>=', $request->tanggal_mulai);
            }

            if ($request->filled('tanggal_selesai')) {
                $query->whereDate('tanggal_lembur', '<=', $request->tanggal_selesai);
            }

            // Search berdasarkan nama karyawan
            if ($request->filled('search')) {
                $query->whereHas('karyawan', function($q) use ($request) {
                    $q->where('nama_lengkap', 'like', '%' . $request->search . '%');
                });
            }

            $lemburs = $query->paginate(20);

            // Get projects untuk filter
            $projects = Project::select('id', 'nama')
                ->where('perusahaan_id', auth()->user()->perusahaan_id)
                ->orderBy('nama')
                ->get();

            // Statistics
            $stats = [
                'total' => Lembur::count(),
                'pending' => Lembur::where('status', 'pending')->count(),
                'approved' => Lembur::where('status', 'approved')->count(),
                'rejected' => Lembur::where('status', 'rejected')->count(),
            ];

            return view('perusahaan.lembur.index', compact('lemburs', 'projects', 'stats'));

        } catch (\Exception $e) {
            \Log::error('Error in LemburController@index: ' . $e->getMessage());
            
            // Return empty view with error message
            $lemburs = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                20, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')
                ->where('perusahaan_id', auth()->user()->perusahaan_id)
                ->orderBy('nama')
                ->get();

            $stats = [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
            ];

            return view('perusahaan.lembur.index', compact('lemburs', 'projects', 'stats'))
                ->with('error', 'Terjadi kesalahan saat memuat data lembur. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function create()
    {
        try {
            // Check if lemburs table exists
            if (!$this->tableExists()) {
                // Show create form with info message instead of redirecting
                $projects = Project::select('id', 'nama')
                    ->where('perusahaan_id', auth()->user()->perusahaan_id)
                    ->orderBy('nama')
                    ->get();

                return view('perusahaan.lembur.create', compact('projects'))
                    ->with('info', 'Fitur Lembur belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $projects = Project::select('id', 'nama')
                ->where('perusahaan_id', auth()->user()->perusahaan_id)
                ->orderBy('nama')
                ->get();

            return view('perusahaan.lembur.create', compact('projects'));

        } catch (\Exception $e) {
            \Log::error('Error in LemburController@create: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.lembur.index')
                ->with('error', 'Terjadi kesalahan saat memuat halaman. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Check if lemburs table exists
            if (!$this->tableExists()) {
                return $this->getTableNotExistsResponse();
            }

            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'karyawan_id' => 'required|exists:karyawans,id',
                'tanggal_lembur' => 'required|date',
                'jam_mulai' => 'required|date_format:H:i',
                'jam_selesai' => 'required|date_format:H:i',
                'alasan_lembur' => 'required|string|max:500',
                'deskripsi_pekerjaan' => 'required|string|max:1000',
                'tarif_lembur_per_jam' => 'nullable|numeric|min:0',
            ]);

            // Auto-assign perusahaan_id
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

            // Validasi karyawan belongs to project
            $karyawan = Karyawan::where('id', $validated['karyawan_id'])
                ->where('project_id', $validated['project_id'])
                ->where('is_active', true)
                ->first();

            if (!$karyawan) {
                return back()->withErrors(['karyawan_id' => 'Karyawan tidak ditemukan di project ini.']);
            }

            // Validate time logic
            $jamMulai = Carbon::parse($validated['jam_mulai']);
            $jamSelesai = Carbon::parse($validated['jam_selesai']);
            $jamSelesaiAdjusted = $jamSelesai->copy();
            
            // Handle overnight shift
            if ($jamSelesaiAdjusted->lt($jamMulai)) {
                $jamSelesaiAdjusted->addDay();
            }
            
            $totalJam = $jamSelesaiAdjusted->diffInHours($jamMulai, true);
            
            // Validate reasonable working hours (max 12 hours)
            if ($totalJam > 12) {
                return back()->withErrors(['jam_selesai' => 'Total jam lembur tidak boleh lebih dari 12 jam.']);
            }
            
            if ($totalJam <= 0) {
                return back()->withErrors(['jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.']);
            }
            
            // Validate minimum overtime duration (30 minutes = 0.5 hours)
            if ($totalJam < 0.5) {
                return back()->withErrors(['jam_selesai' => 'Durasi lembur minimal adalah 30 menit.']);
            }

            // Auto-calculate overtime rate if not provided
            if (empty($validated['tarif_lembur_per_jam'])) {
                $overtimeRateData = $this->calculateOvertimeRateForKaryawan($validated['tanggal_lembur'], $karyawan);
                $validated['tarif_lembur_per_jam'] = $overtimeRateData['overtime_rate'] ?? 0;
            }

            // Use the already calculated total hours (rounded)
            $validated['total_jam'] = round($totalJam, 0);
            
            // Calculate total upah (rounded)
            $validated['total_upah_lembur'] = round($totalJam * $validated['tarif_lembur_per_jam'], 0);

            DB::transaction(function () use ($validated) {
                $lembur = Lembur::create($validated);
            });

            return redirect()->route('perusahaan.lembur.index')
                ->with('success', 'Permintaan lembur berhasil dibuat.');

        } catch (\Exception $e) {
            \Log::error('Error in LemburController@store: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.lembur.index')
                ->with('error', 'Terjadi kesalahan saat menyimpan data lembur. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lembur $lembur)
    {
        $lembur->load(['karyawan', 'project', 'approvedBy']);
        
        return view('perusahaan.lembur.show', compact('lembur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lembur $lembur)
    {
        if (!$lembur->canEdit()) {
            return redirect()->route('perusahaan.lembur.index')
                ->with('error', 'Permintaan lembur yang sudah diproses tidak dapat diedit.');
        }

        $projects = Project::select('id', 'nama')
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.lembur.edit', compact('lembur', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lembur $lembur)
    {
        if (!$lembur->canEdit()) {
            return redirect()->route('perusahaan.lembur.index')
                ->with('error', 'Permintaan lembur yang sudah diproses tidak dapat diedit.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'alasan_lembur' => 'required|string|max:500',
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'tarif_lembur_per_jam' => 'nullable|numeric|min:0',
        ]);

        // Validasi karyawan belongs to project
        $karyawan = Karyawan::where('id', $validated['karyawan_id'])
            ->where('project_id', $validated['project_id'])
            ->where('is_active', true)
            ->first();

        if (!$karyawan) {
            return back()->withErrors(['karyawan_id' => 'Karyawan tidak ditemukan di project ini.']);
        }

        // Validate time logic
        $jamMulai = Carbon::parse($validated['jam_mulai']);
        $jamSelesai = Carbon::parse($validated['jam_selesai']);
        $jamSelesaiAdjusted = $jamSelesai->copy();
        
        // Handle overnight shift
        if ($jamSelesaiAdjusted->lt($jamMulai)) {
            $jamSelesaiAdjusted->addDay();
        }
        
        $totalJam = $jamSelesaiAdjusted->diffInHours($jamMulai, true);
        
        // Validate reasonable working hours (max 12 hours)
        if ($totalJam > 12) {
            return back()->withErrors(['jam_selesai' => 'Total jam lembur tidak boleh lebih dari 12 jam.']);
        }
        
        if ($totalJam <= 0) {
            return back()->withErrors(['jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.']);
        }
        
        // Validate minimum overtime duration (30 minutes = 0.5 hours)
        if ($totalJam < 0.5) {
            return back()->withErrors(['jam_selesai' => 'Durasi lembur minimal adalah 30 menit.']);
        }

        // Auto-calculate overtime rate if not provided
        if (empty($validated['tarif_lembur_per_jam'])) {
            $overtimeRateData = $this->calculateOvertimeRateForKaryawan($validated['tanggal_lembur'], $karyawan);
            $validated['tarif_lembur_per_jam'] = $overtimeRateData['overtime_rate'] ?? 0;
        }

        // Use the already calculated total hours (rounded)
        $validated['total_jam'] = round($totalJam, 0);
        
        // Calculate total upah (rounded)
        $validated['total_upah_lembur'] = round($totalJam * $validated['tarif_lembur_per_jam'], 0);

        try {
            DB::transaction(function () use ($lembur, $validated) {
                $lembur->update($validated);
            });

            return redirect()->route('perusahaan.lembur.index')
                ->with('success', 'Permintaan lembur berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui permintaan lembur: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lembur $lembur)
    {
        if (!$lembur->canDelete()) {
            return redirect()->route('perusahaan.lembur.index')
                ->with('error', 'Permintaan lembur yang sudah diproses tidak dapat dihapus.');
        }

        try {
            $lembur->delete();
            
            return redirect()->route('perusahaan.lembur.index')
                ->with('success', 'Permintaan lembur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('perusahaan.lembur.index')
                ->with('error', 'Gagal menghapus permintaan lembur: ' . $e->getMessage());
        }
    }

    /**
     * Approve lembur
     */
    public function approve(Request $request, Lembur $lembur)
    {
        if ($lembur->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan lembur sudah diproses sebelumnya.'
            ]);
        }

        $validated = $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
        ]);

        try {
            $lembur->approve(auth()->id(), $validated['catatan_approval'] ?? null);
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan lembur berhasil disetujui.',
                'data' => [
                    'status' => $lembur->status_text,
                    'approved_by' => $lembur->approvedBy->name ?? '',
                    'approved_at' => $lembur->approved_at?->format('d/m/Y H:i'),
                    'total_upah' => number_format($lembur->total_upah_lembur, 0, ',', '.')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui permintaan lembur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject lembur
     */
    public function reject(Request $request, Lembur $lembur)
    {
        if ($lembur->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan lembur sudah diproses sebelumnya.'
            ]);
        }

        $validated = $request->validate([
            'catatan_approval' => 'required|string|max:500',
        ]);

        try {
            $lembur->reject(auth()->id(), $validated['catatan_approval']);
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan lembur berhasil ditolak.',
                'data' => [
                    'status' => $lembur->status_text,
                    'approved_by' => $lembur->approvedBy->name ?? '',
                    'approved_at' => $lembur->approved_at?->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan lembur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get karyawan by project (AJAX)
     */
    public function getKaryawanByProject(Request $request, $projectId)
    {
        $search = $request->get('search', '');
        
        $query = Karyawan::select('id', 'nama_lengkap', 'nik_karyawan')
            ->where('project_id', $projectId)
            ->where('is_active', true);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nik_karyawan', 'like', '%' . $search . '%');
            });
        }
        
        $karyawans = $query->orderBy('nama_lengkap')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }

    /**
     * Calculate total monthly salary including all fixed allowances
     */
    private function calculateTotalMonthlySalary($karyawan)
    {
        // Start with base salary
        $totalSalary = $karyawan->gaji_pokok ?? 0;
        
        // Get all fixed allowances for this employee
        $fixedAllowances = $this->getEmployeeFixedAllowances($karyawan);
        
        // Add all fixed allowances
        foreach ($fixedAllowances as $allowance) {
            $totalSalary += $allowance['nilai'];
        }
        
        return $totalSalary;
    }
    
    /**
     * Get all fixed allowances for an employee
     */
    private function getEmployeeFixedAllowances($karyawan)
    {
        $allowances = [];
        
        // Get employee-specific allowance templates (highest priority)
        $employeeTemplates = TemplateKomponenGaji::with('komponenPayroll')
            ->where('karyawan_id', $karyawan->id)
            ->where('aktif', true)
            ->whereHas('komponenPayroll', function($q) {
                $q->where('jenis', 'Tunjangan')
                  ->where('kategori', 'Fixed')
                  ->where('aktif', true);
            })
            ->get();
            
        foreach ($employeeTemplates as $template) {
            $allowances[$template->komponen_payroll_id] = [
                'nama' => $template->komponenPayroll->nama_komponen,
                'nilai' => $template->nilai,
                'source' => 'employee_specific'
            ];
        }
        
        // Get jabatan-specific allowances (if not overridden by employee-specific)
        if ($karyawan->jabatan_id) {
            $jabatanTemplates = TemplateKomponenGaji::with('komponenPayroll')
                ->where('jabatan_id', $karyawan->jabatan_id)
                ->where('aktif', true)
                ->whereHas('komponenPayroll', function($q) {
                    $q->where('jenis', 'Tunjangan')
                      ->where('kategori', 'Fixed')
                      ->where('aktif', true);
                })
                ->get();
                
            foreach ($jabatanTemplates as $template) {
                // Only add if not already set by employee-specific template
                if (!isset($allowances[$template->komponen_payroll_id])) {
                    $allowances[$template->komponen_payroll_id] = [
                        'nama' => $template->komponenPayroll->nama_komponen,
                        'nilai' => $template->nilai,
                        'source' => 'jabatan_specific'
                    ];
                }
            }
        }
        
        // Get project-specific allowances (if not overridden)
        if ($karyawan->project_id) {
            $projectTemplates = TemplateKomponenGaji::with('komponenPayroll')
                ->where('project_id', $karyawan->project_id)
                ->where('aktif', true)
                ->whereHas('komponenPayroll', function($q) {
                    $q->where('jenis', 'Tunjangan')
                      ->where('kategori', 'Fixed')
                      ->where('aktif', true);
                })
                ->get();
                
            foreach ($projectTemplates as $template) {
                // Only add if not already set by higher priority templates
                if (!isset($allowances[$template->komponen_payroll_id])) {
                    $allowances[$template->komponen_payroll_id] = [
                        'nama' => $template->komponenPayroll->nama_komponen,
                        'nilai' => $template->nilai,
                        'source' => 'project_specific'
                    ];
                }
            }
        }
        
        // Get default/general allowances (lowest priority)
        $defaultTemplates = TemplateKomponenGaji::with('komponenPayroll')
            ->whereNull('karyawan_id')
            ->whereNull('jabatan_id')
            ->whereNull('project_id')
            ->where('aktif', true)
            ->whereHas('komponenPayroll', function($q) {
                $q->where('jenis', 'Tunjangan')
                  ->where('kategori', 'Fixed')
                  ->where('aktif', true);
            })
            ->get();
            
        foreach ($defaultTemplates as $template) {
            // Only add if not already set by higher priority templates
            if (!isset($allowances[$template->komponen_payroll_id])) {
                $allowances[$template->komponen_payroll_id] = [
                    'nama' => $template->komponenPayroll->nama_komponen,
                    'nilai' => $template->nilai,
                    'source' => 'default'
                ];
            }
        }
        
        return $allowances;
    }

    /**
     * Calculate overtime rate for a specific employee and date
     */
    private function calculateOvertimeRateForKaryawan($date, $karyawan)
    {
        // Get payroll settings
        $payrollSetting = PayrollSetting::first();
        if (!$payrollSetting) {
            return ['overtime_rate' => 0];
        }
        
        // Determine day type
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 = Sunday, 6 = Saturday
        
        // Check if it's a holiday (you can extend this logic)
        $isHoliday = false; // You can implement holiday checking logic here
        
        // Determine overtime multiplier
        $multiplier = 0;
        
        if ($isHoliday) {
            $multiplier = $payrollSetting->lembur_hari_libur ?? 3.0;
        } elseif ($dayOfWeek == 0 || $dayOfWeek == 6) { // Sunday or Saturday
            $multiplier = $payrollSetting->lembur_akhir_pekan ?? 2.0;
        } else {
            $multiplier = $payrollSetting->lembur_hari_kerja ?? 1.5;
        }
        
        // Calculate total monthly salary (gaji pokok + all fixed allowances)
        $totalMonthlySalary = $this->calculateTotalMonthlySalary($karyawan);
        $fixedAllowances = $this->getEmployeeFixedAllowances($karyawan);
        
        // Calculate hourly rate from total monthly salary
        // Assuming 173 working hours per month (standard calculation)
        $hourlyRate = $totalMonthlySalary > 0 ? ($totalMonthlySalary / 173) : 0;
        $overtimeRate = $hourlyRate * $multiplier;
        
        return [
            'overtime_rate' => round($overtimeRate, 0),
            'multiplier' => $multiplier,
            'hourly_rate' => round($hourlyRate, 0),
            'gaji_pokok' => $karyawan->gaji_pokok ?? 0,
            'total_monthly_salary' => round($totalMonthlySalary, 0),
            'fixed_allowances' => $fixedAllowances,
            'allowances_total' => round(array_sum(array_column($fixedAllowances, 'nilai')), 0)
        ];
    }

    /**
     * Get overtime rate based on date and payroll settings (AJAX)
     */
    public function getOvertimeRate(Request $request)
    {
        $date = $request->get('date');
        $karyawanId = $request->get('karyawan_id');
        
        if (!$date) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal diperlukan'
            ]);
        }
        
        // Get karyawan
        $karyawan = null;
        if ($karyawanId) {
            $karyawan = Karyawan::find($karyawanId);
            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ]);
            }
        }
        
        // Check if karyawan has salary set
        if (!$karyawan || !$karyawan->gaji_pokok || $karyawan->gaji_pokok <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Gaji pokok karyawan belum diset. Silakan set gaji pokok terlebih dahulu di menu Manajemen Gaji.'
            ]);
        }
        
        // Get payroll settings
        $payrollSetting = PayrollSetting::first();
        if (!$payrollSetting) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan payroll belum dikonfigurasi. Silakan konfigurasi di menu Pengaturan Payroll.'
            ]);
        }
        
        // Calculate overtime rate
        $rateData = $this->calculateOvertimeRateForKaryawan($date, $karyawan);
        
        // Determine day type
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek;
        
        $dayType = '';
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            $dayType = 'Akhir Pekan (Sabtu - Minggu)';
        } else {
            $dayType = 'Hari Kerja (Senin - Jumat)';
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'multiplier' => $rateData['multiplier'],
                'day_type' => $dayType,
                'gaji_pokok' => $rateData['gaji_pokok'],
                'allowances_total' => $rateData['allowances_total'],
                'total_monthly_salary' => $rateData['total_monthly_salary'],
                'hourly_rate' => $rateData['hourly_rate'],
                'overtime_rate' => $rateData['overtime_rate'],
                'max_hours' => $payrollSetting->lembur_max_jam_per_hari ?? 4,
                'date_info' => $carbonDate->format('l, d F Y'),
                'allowances_breakdown' => $rateData['fixed_allowances'],
            ]
        ]);
    }
    
    /**
     * Download template for import
     */
    public function downloadTemplate(Request $request)
    {
        $projectId = $request->get('project_id');
        $employeeIds = $request->get('employee_ids');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Validate project belongs to user's perusahaan
        if ($projectId) {
            $project = Project::where('id', $projectId)
                ->where('perusahaan_id', auth()->user()->perusahaan_id)
                ->first();
                
            if (!$project) {
                return redirect()->back()->with('error', 'Project tidak ditemukan');
            }
        }
        
        // Parse employee IDs
        $selectedEmployeeIds = [];
        if ($employeeIds) {
            $selectedEmployeeIds = array_map('intval', explode(',', $employeeIds));
        }
        
        $export = new LemburTemplateExport($projectId, $selectedEmployeeIds, $startDate, $endDate);
        $filename = 'template-import-lembur';
        
        if ($projectId && $project) {
            $filename .= '-' . str_replace(' ', '-', strtolower($project->nama));
        }
        
        if ($startDate && $endDate) {
            $filename .= '-' . $startDate . '-to-' . $endDate;
        }
        
        return Excel::download($export, $filename . '.xlsx');
    }

    /**
     * Import lembur from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            // Import without project/employee restrictions - read everything from Excel
            $import = new LemburImport(auth()->user()->perusahaan_id);
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $silentlySkippedCount = $import->getSilentlySkippedCount();
            $errorRows = $import->getErrorRows();

            $message = "Import selesai. {$importedCount} data berhasil diimport";
            
            if ($silentlySkippedCount > 0) {
                $message .= ", {$silentlySkippedCount} data dilewati";
            }
            
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} data gagal karena error";
            }

            // If there are errors, store them in session for display
            if (!empty($errorRows)) {
                session(['import_errors' => $errorRows]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'imported_count' => $importedCount,
                    'skipped_count' => $skippedCount,
                    'silently_skipped_count' => $silentlySkippedCount,
                    'has_errors' => !empty($errorRows)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show import errors
     */
    public function showImportErrors()
    {
        $importErrors = session('import_errors', []);
        
        if (empty($importErrors)) {
            return redirect()->route('perusahaan.lembur.index')
                ->with('info', 'Tidak ada error import untuk ditampilkan.');
        }

        return view('perusahaan.lembur.import-errors', compact('importErrors'));
    }

    public function searchKaryawan(Request $request)
    {
        $projectId = $request->get('project_id');
        $search = $request->get('search', '');
        
        if (!$projectId || strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
        
        // Validate project belongs to current user's perusahaan
        $project = Project::where('id', $projectId)
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->first();
            
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan atau tidak memiliki akses'
            ]);
        }
        
        // Search karyawan - global scope will automatically filter by perusahaan_id
        $karyawans = Karyawan::select('id', 'nama_lengkap', 'nik_karyawan')
            ->where('project_id', $projectId)
            ->where('is_active', true)
            ->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'ilike', '%' . $search . '%')
                  ->orWhere('nik_karyawan', 'ilike', '%' . $search . '%');
            })
            ->orderBy('nama_lengkap')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }
}