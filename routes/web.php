<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

// Dashboard Auth Routes (dash.nicepatrol.test) - MUST BE FIRST
Route::domain(env('DASHBOARD_DOMAIN', 'dash.nicepatrol.test'))->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Forgot Password Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOTP'])->name('password.send-otp');
    Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify');
    Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOTP'])->name('password.verify-otp');
    Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOTP'])->name('password.resend-otp');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// Mobile Auth Routes (for IP access from phone)
// This handles http://192.168.x.x:8000/login
Route::get('/login', function() {
    $host = request()->getHost();
    // If accessing from IP, show mobile login
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return view('mobile.auth.login');
    }
    // If accessing from mobile domain
    if ($host === env('MOBILE_DOMAIN', 'app.nicepatrol.test')) {
        return view('mobile.auth.login');
    }
    // Otherwise 404
    abort(404);
})->name('mobile.login.fallback');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Redirect dashboard based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Superadmin Routes
    Route::prefix('admin')->name('admin.')->middleware('superadmin')->group(function () {
        Route::resource('perusahaans', \App\Http\Controllers\Admin\PerusahaanController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('lokasis', \App\Http\Controllers\Admin\LokasiController::class);
        Route::resource('checkpoints', \App\Http\Controllers\Admin\CheckpointController::class);
        Route::resource('patrolis', \App\Http\Controllers\Admin\PatroliController::class);
        
        // System Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SystemSettingController::class, 'update'])->name('settings.update');
    });

    // Admin Perusahaan Routes
    Route::prefix('perusahaan')->name('perusahaan.')->middleware('perusahaan')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Perusahaan\DashboardController::class, 'index'])->name('dashboard');
        
        // Profil Perusahaan
        Route::get('/profil', [\App\Http\Controllers\Perusahaan\ProfilController::class, 'index'])->name('profil.index');
        Route::put('/profil', [\App\Http\Controllers\Perusahaan\ProfilController::class, 'update'])->name('profil.update');
        Route::post('/profil/upload-logo', [\App\Http\Controllers\Perusahaan\ProfilController::class, 'uploadLogo'])->name('profil.upload-logo');
        
        Route::resource('kantors', \App\Http\Controllers\Perusahaan\KantorController::class);
        Route::resource('projects', \App\Http\Controllers\Perusahaan\ProjectController::class);
        Route::get('projects/{project}/jabatans', [\App\Http\Controllers\Perusahaan\ProjectController::class, 'getJabatans'])->name('projects.jabatans');
        Route::get('projects/{project}/guest-card-areas', [\App\Http\Controllers\Perusahaan\ProjectController::class, 'getAreas'])->name('projects.guest-card-areas');
        Route::put('projects/{project}/guest-book-settings', [\App\Http\Controllers\Perusahaan\ProjectController::class, 'updateGuestBookSettings'])->name('projects.guest-book-settings');
        
        // Project Contacts Routes
        Route::get('projects/{project}/contacts', [\App\Http\Controllers\Perusahaan\ProjectContactController::class, 'index'])->name('projects.contacts.index');
        Route::post('projects/{project}/contacts', [\App\Http\Controllers\Perusahaan\ProjectContactController::class, 'store'])->name('projects.contacts.store');
        Route::get('projects/{project}/contacts/{contact}/edit', [\App\Http\Controllers\Perusahaan\ProjectContactController::class, 'edit'])->name('projects.contacts.edit');
        Route::put('projects/{project}/contacts/{contact}', [\App\Http\Controllers\Perusahaan\ProjectContactController::class, 'update'])->name('projects.contacts.update');
        Route::delete('projects/{project}/contacts/{contact}', [\App\Http\Controllers\Perusahaan\ProjectContactController::class, 'destroy'])->name('projects.contacts.destroy');
        Route::get('projects/{project}/contacts/jenis/{jenis}', [\App\Http\Controllers\Perusahaan\ProjectContactController::class, 'getByJenis'])->name('projects.contacts.by-jenis');
        
        // Buku Tamu Routes - specific routes MUST come before resource routes
        Route::get('buku-tamu/project-settings', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getProjectSettings'])->name('buku-tamu.project-settings');
        Route::get('buku-tamu/kuesioner', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getKuesionerByProject'])->name('buku-tamu.kuesioner');
        Route::get('buku-tamu/kuesioner-by-area', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getKuesionerByArea'])->name('buku-tamu.kuesioner-by-area');
        Route::get('buku-tamu/questionnaire', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'showQuestionnaire'])->name('buku-tamu.questionnaire');
        Route::get('buku-tamu/guest-info', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getGuestInfo'])->name('buku-tamu.guest-info');
        Route::get('buku-tamu-qr/{bukuTamu}', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'generateQrCode'])->name('buku-tamu.qr-code');
        Route::post('buku-tamu-scan', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getByQrCode'])->name('buku-tamu.scan');
        Route::post('buku-tamu/{bukuTamu}/check-out', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'checkOut'])->name('buku-tamu.check-out');
        Route::post('buku-tamu/{bukuTamu}/return-card', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'returnCard'])->name('buku-tamu.return-card');
        Route::post('buku-tamu/{bukuTamu}/questionnaire', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'saveGuestQuestionnaire'])->name('buku-tamu.save-questionnaire');
        
        // Project API Routes for Buku Tamu
        Route::get('projects/{project}/security-officers', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getSecurityOfficersByProject'])->name('projects.security-officers');
        Route::get('security-officer/areas', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getAreasBySecurityOfficer'])->name('security-officer.areas');
        Route::get('area-patrols/by-area', [\App\Http\Controllers\Perusahaan\BukuTamuController::class, 'getPosJagaByArea'])->name('area-patrols.by-area');
        
        // Debug route for security officers
        Route::get('debug/security-officers/{project}', function($projectId) {
            $karyawans = \App\Models\Karyawan::with(['jabatan:id,nama', 'user:id,role,email'])
                ->where('project_id', $projectId)
                ->where('is_active', true)
                ->whereHas('user', function($query) {
                    $query->where('role', 'security_officer');
                })
                ->select('id', 'nama_lengkap', 'jabatan_id', 'user_id')
                ->orderBy('nama_lengkap')
                ->get();
                
            return response()->json([
                'success' => true,
                'project_id' => $projectId,
                'count' => $karyawans->count(),
                'data' => $karyawans
            ]);
        });
        
        // Debug route for areas by security officer
        Route::get('debug/security-officer-areas', function(Request $request) {
            $securityOfficerName = $request->get('security_officer');
            $projectId = $request->get('project_id');
            
            $karyawan = \App\Models\Karyawan::where('nama_lengkap', $securityOfficerName)
                ->where('project_id', $projectId)
                ->where('is_active', true)
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Security officer tidak ditemukan'
                ]);
            }

            $areas = $karyawan->areas()
                ->select('areas.id', 'areas.nama', 'areas.alamat', 'karyawan_areas.is_primary')
                ->orderBy('karyawan_areas.is_primary', 'desc')
                ->orderBy('areas.nama')
                ->get();

            return response()->json([
                'success' => true,
                'security_officer' => $securityOfficerName,
                'project_id' => $projectId,
                'karyawan_id' => $karyawan->id,
                'areas_count' => $areas->count(),
                'data' => $areas
            ]);
        });
        
        Route::resource('buku-tamu', \App\Http\Controllers\Perusahaan\BukuTamuController::class);

        // Area Routes
        Route::get('areas/by-project', [\App\Http\Controllers\Perusahaan\AreaController::class, 'getByProject'])->name('areas.by-project');
        
        // Area Patrol (POS Jaga) Routes
        Route::get('area-patrols/by-project', [\App\Http\Controllers\Perusahaan\AreaPatrolController::class, 'getByProject'])->name('area-patrols.by-project');
        Route::post('area-patrols', [\App\Http\Controllers\Perusahaan\AreaPatrolController::class, 'store'])->name('area-patrols.store');
        
        // Debug route for buku tamu
        Route::post('buku-tamu-debug', function(Request $request) {
            \Log::info('Debug Buku Tamu Request:', $request->all());
            return response()->json(['success' => true, 'data' => $request->all()]);
        })->name('buku-tamu.debug');

        // Penerimaan Barang Routes
        Route::resource('penerimaan-barang', \App\Http\Controllers\Perusahaan\PenerimaanBarangController::class);
        Route::get('penerimaan-barang-areas/{project}', [\App\Http\Controllers\Perusahaan\PenerimaanBarangController::class, 'getAreasByProject'])->name('penerimaan-barang.areas-by-project');
        Route::get('penerimaan-barang-search-pos', [\App\Http\Controllers\Perusahaan\PenerimaanBarangController::class, 'searchPos'])->name('penerimaan-barang.search-pos');

        // Kartu Tamu Routes
        Route::get('kartu-tamu', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'index'])->name('kartu-tamu.index');
        Route::get('kartu-tamu/detail', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'show'])->name('kartu-tamu.detail');
        Route::get('kartu-tamu/create', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'create'])->name('kartu-tamu.create');
        Route::post('kartu-tamu', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'store'])->name('kartu-tamu.store');
        Route::get('kartu-tamu/{kartuTamu}/edit', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'edit'])->name('kartu-tamu.edit');
        Route::put('kartu-tamu/{kartuTamu}', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'update'])->name('kartu-tamu.update');
        Route::delete('kartu-tamu/{kartuTamu}', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'destroy'])->name('kartu-tamu.destroy');
        Route::post('kartu-tamu/{kartuTamu}/assign', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'assignCard'])->name('kartu-tamu.assign');
        Route::post('kartu-tamu/{kartuTamu}/return', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'returnCard'])->name('kartu-tamu.return');
        Route::get('kartu-tamu-available', [\App\Http\Controllers\Perusahaan\KartuTamuController::class, 'getAvailableCards'])->name('kartu-tamu.available');

        // Tugas Routes
        Route::resource('tugas', \App\Http\Controllers\Perusahaan\TugasController::class);
        Route::get('tugas/{tugas}/assignments', [\App\Http\Controllers\Perusahaan\TugasController::class, 'getAssignments'])->name('tugas.assignments');
        Route::get('tugas-projects/{project}/areas', [\App\Http\Controllers\Perusahaan\TugasController::class, 'getAreasByProject'])->name('tugas.areas-by-project');

        // Atensi Routes
        Route::resource('atensi', \App\Http\Controllers\Perusahaan\AtensiController::class);
        Route::get('atensi/{atensi}/recipients', [\App\Http\Controllers\Perusahaan\AtensiController::class, 'getRecipients'])->name('atensi.recipients');
        Route::get('projects/{project}/areas', [\App\Http\Controllers\Perusahaan\AtensiController::class, 'getAreasByProject'])->name('atensi.areas-by-project');
        Route::get('atensi-users', [\App\Http\Controllers\Perusahaan\AtensiController::class, 'getUsersByCriteria'])->name('atensi.users-by-criteria');
        Route::resource('areas', \App\Http\Controllers\Perusahaan\AreaController::class);
        Route::resource('jabatans', \App\Http\Controllers\Perusahaan\JabatanController::class);
        Route::get('status-karyawan', [\App\Http\Controllers\Perusahaan\StatusKaryawanController::class, 'index'])->name('status-karyawan.index');
        
        // Kehadiran Routes
        Route::get('kehadiran', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'kehadiran'])->name('kehadiran.index');
        Route::get('kehadiran/rekap-kehadiran', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'rekap'])->name('kehadiran.rekap-kehadiran');
        Route::get('kehadiran/rekap-kehadiran/export-pdf', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'rekapPdf'])->name('kehadiran.rekap-kehadiran.export-pdf');
        Route::get('kehadiran/{kehadiran}/show', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'show'])->name('kehadiran.show');
        Route::post('kehadiran', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'store'])->name('kehadiran.store');
        Route::put('kehadiran/{kehadiran}', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'update'])->name('kehadiran.update');
        Route::delete('kehadiran/{kehadiran}', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'destroy'])->name('kehadiran.destroy');
        Route::get('karyawan/by-project/{projectId}', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'getKaryawanByProject'])->name('karyawan.by-project');
        Route::get('kehadiran/download-template', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'downloadTemplate'])->name('kehadiran.download-template');
        Route::post('kehadiran/import-excel', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'importExcel'])->name('kehadiran.import-excel');
        
        // Schedule Routes
        Route::get('kehadiran/schedule', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'index'])->name('kehadiran.schedule');
        Route::get('kehadiran/rekap', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'rekap'])->name('kehadiran.rekap');
        Route::get('kehadiran/rekap/export-pdf', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'exportPdf'])->name('kehadiran.rekap.export-pdf');
        Route::post('schedule/update-shift', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'updateShift'])->name('schedule.update-shift');
        Route::post('schedule/copy-last-week', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'copyLastWeek'])->name('schedule.copy-last-week');
        Route::post('schedule/set-month', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'setMonthSchedule'])->name('schedule.set-month');
        Route::post('schedule/generate-by-jabatan', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'generateByJabatan'])->name('schedule.generate-by-jabatan');
        Route::get('schedule/download-template', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'downloadTemplate'])->name('schedule.download-template');
        Route::post('schedule/import-excel', [\App\Http\Controllers\Perusahaan\ScheduleController::class, 'importExcel'])->name('schedule.import-excel');
        
        // Lokasi Absensi Routes
        Route::get('kehadiran/lokasi-absensi', [\App\Http\Controllers\Perusahaan\LokasiAbsensiController::class, 'index'])->name('kehadiran.lokasi-absensi');
        Route::post('lokasi-absensi', [\App\Http\Controllers\Perusahaan\LokasiAbsensiController::class, 'store'])->name('lokasi-absensi.store');
        Route::put('lokasi-absensi/{lokasi}', [\App\Http\Controllers\Perusahaan\LokasiAbsensiController::class, 'update'])->name('lokasi-absensi.update');
        Route::delete('lokasi-absensi/{lokasi}', [\App\Http\Controllers\Perusahaan\LokasiAbsensiController::class, 'destroy'])->name('lokasi-absensi.destroy');
        
        // Shift Routes
        Route::get('kehadiran/manajemen-shift', [\App\Http\Controllers\Perusahaan\ShiftController::class, 'index'])->name('kehadiran.manajemen-shift');
        Route::post('shifts', [\App\Http\Controllers\Perusahaan\ShiftController::class, 'store'])->name('shifts.store');
        Route::put('shifts/{shift}', [\App\Http\Controllers\Perusahaan\ShiftController::class, 'update'])->name('shifts.update');
        Route::delete('shifts/{shift}', [\App\Http\Controllers\Perusahaan\ShiftController::class, 'destroy'])->name('shifts.destroy');
        
        Route::get('karyawans/download-template', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'downloadTemplate'])->name('karyawans.download-template');
        Route::post('karyawans/import-excel', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'importExcel'])->name('karyawans.import-excel');
        Route::get('karyawans/import-progress', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'importProgress'])->name('karyawans.import-progress');
        Route::get('karyawans/export-page', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'exportPage'])->name('karyawans.export-page');
        Route::post('karyawans/export-excel', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'exportExcel'])->name('karyawans.export-excel');
        Route::resource('karyawans', \App\Http\Controllers\Perusahaan\KaryawanController::class);
        Route::post('karyawans/{karyawan}/upload-foto', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'uploadFoto'])->name('karyawans.upload-foto');
        Route::put('karyawans/{karyawan}/update-nama', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updateNama'])->name('karyawans.update-nama');
        Route::put('karyawans/{karyawan}/update-pekerjaan', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updatePekerjaan'])->name('karyawans.update-pekerjaan');
        Route::put('karyawans/{karyawan}/update-pribadi', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updatePribadi'])->name('karyawans.update-pribadi');
        Route::put('karyawans/{karyawan}/update-rekening-bank', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updateRekeningBank'])->name('karyawans.update-rekening-bank');
        Route::put('karyawans/{karyawan}/update-bpjs', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updateBpjs'])->name('karyawans.update-bpjs');
        Route::put('karyawans/{karyawan}/update-email', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updateEmail'])->name('karyawans.update-email');
        Route::put('karyawans/{karyawan}/update-role', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'updateRole'])->name('karyawans.update-role');
        Route::put('karyawans/{karyawan}/reset-password', [\App\Http\Controllers\Perusahaan\KaryawanController::class, 'resetPassword'])->name('karyawans.reset-password');
        Route::post('karyawans/{karyawan}/pengalaman-kerja', [\App\Http\Controllers\Perusahaan\PengalamanKerjaController::class, 'store'])->name('karyawans.pengalaman-kerja.store');
        Route::put('karyawans/{karyawan}/pengalaman-kerja/{pengalaman}', [\App\Http\Controllers\Perusahaan\PengalamanKerjaController::class, 'update'])->name('karyawans.pengalaman-kerja.update');
        Route::delete('karyawans/{karyawan}/pengalaman-kerja/{pengalaman}', [\App\Http\Controllers\Perusahaan\PengalamanKerjaController::class, 'destroy'])->name('karyawans.pengalaman-kerja.destroy');
        Route::post('karyawans/{karyawan}/pendidikan', [\App\Http\Controllers\Perusahaan\PendidikanController::class, 'store'])->name('karyawans.pendidikan.store');
        Route::put('karyawans/{karyawan}/pendidikan/{pendidikan}', [\App\Http\Controllers\Perusahaan\PendidikanController::class, 'update'])->name('karyawans.pendidikan.update');
        Route::delete('karyawans/{karyawan}/pendidikan/{pendidikan}', [\App\Http\Controllers\Perusahaan\PendidikanController::class, 'destroy'])->name('karyawans.pendidikan.destroy');
        Route::post('karyawans/{karyawan}/sertifikasi', [\App\Http\Controllers\Perusahaan\SertifikasiController::class, 'store'])->name('karyawans.sertifikasi.store');
        Route::put('karyawans/{karyawan}/sertifikasi/{sertifikasi}', [\App\Http\Controllers\Perusahaan\SertifikasiController::class, 'update'])->name('karyawans.sertifikasi.update');
        Route::delete('karyawans/{karyawan}/sertifikasi/{sertifikasi}', [\App\Http\Controllers\Perusahaan\SertifikasiController::class, 'destroy'])->name('karyawans.sertifikasi.destroy');
        Route::post('karyawans/{karyawan}/medical-checkup', [\App\Http\Controllers\Perusahaan\MedicalCheckupController::class, 'store'])->name('karyawans.medical-checkup.store');
        Route::put('karyawans/{karyawan}/medical-checkup/{checkup}', [\App\Http\Controllers\Perusahaan\MedicalCheckupController::class, 'update'])->name('karyawans.medical-checkup.update');
        Route::delete('karyawans/{karyawan}/medical-checkup/{checkup}', [\App\Http\Controllers\Perusahaan\MedicalCheckupController::class, 'destroy'])->name('karyawans.medical-checkup.destroy');
        
        // Payroll Routes
        Route::get('payroll/generate', [\App\Http\Controllers\Perusahaan\PayrollController::class, 'generate'])->name('payroll.generate');
        Route::post('payroll/generate', [\App\Http\Controllers\Perusahaan\PayrollController::class, 'store'])->name('payroll.store');
        Route::get('daftar-payroll', [\App\Http\Controllers\Perusahaan\DaftarPayrollController::class, 'index'])->name('daftar-payroll.index');
        Route::get('daftar-payroll/{payroll}', [\App\Http\Controllers\Perusahaan\DaftarPayrollController::class, 'show'])->name('daftar-payroll.show');
        Route::put('daftar-payroll/{payroll}/update-component', [\App\Http\Controllers\Perusahaan\DaftarPayrollController::class, 'updateComponent'])->name('daftar-payroll.update-component');
        Route::post('daftar-payroll/{payroll}/approve', [\App\Http\Controllers\Perusahaan\DaftarPayrollController::class, 'approve'])->name('daftar-payroll.approve');
        Route::post('daftar-payroll/bulk-approve', [\App\Http\Controllers\Perusahaan\DaftarPayrollController::class, 'bulkApprove'])->name('daftar-payroll.bulk-approve');
        Route::delete('daftar-payroll/{payroll}', [\App\Http\Controllers\Perusahaan\DaftarPayrollController::class, 'destroy'])->name('daftar-payroll.destroy');
        Route::get('setting-payroll', [\App\Http\Controllers\Perusahaan\SettingPayrollController::class, 'index'])->name('setting-payroll.index');
        Route::post('setting-payroll', [\App\Http\Controllers\Perusahaan\SettingPayrollController::class, 'update'])->name('setting-payroll.update');
        Route::get('komponen-payroll', [\App\Http\Controllers\Perusahaan\KomponenPayrollController::class, 'index'])->name('komponen-payroll.index');
        Route::post('komponen-payroll', [\App\Http\Controllers\Perusahaan\KomponenPayrollController::class, 'store'])->name('komponen-payroll.store');
        Route::put('komponen-payroll/{komponenPayroll}', [\App\Http\Controllers\Perusahaan\KomponenPayrollController::class, 'update'])->name('komponen-payroll.update');
        Route::delete('komponen-payroll/{komponenPayroll}', [\App\Http\Controllers\Perusahaan\KomponenPayrollController::class, 'destroy'])->name('komponen-payroll.destroy');
        
        // Template Komponen Routes
        Route::get('template-komponen', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'index'])->name('template-komponen.index');
        Route::post('template-komponen', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'store'])->name('template-komponen.store');
        Route::put('template-komponen/{templateKomponen}', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'update'])->name('template-komponen.update');
        Route::delete('template-komponen/{templateKomponen}', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'destroy'])->name('template-komponen.destroy');
        Route::post('template-komponen/delete-by-name', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'destroyByName'])->name('template-komponen.destroy-by-name');
        Route::get('template-komponen/get-karyawans', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'getKaryawans'])->name('template-komponen.get-karyawans');
        Route::get('template-komponen/get-used-jabatans', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'getUsedJabatans'])->name('template-komponen.get-used-jabatans');
        Route::get('template-komponen/get-by-name', [\App\Http\Controllers\Perusahaan\TemplateKomponenController::class, 'getTemplateByName'])->name('template-komponen.get-by-name');
        
        // API Routes for dropdowns
        Route::get('jabatans/by-project/{project}', function($projectId) {
            // Get jabatan dengan count karyawan per jabatan
            $jabatans = \App\Models\Jabatan::select('id', 'nama')
                ->withCount(['karyawans' => function($query) use ($projectId) {
                    $query->where('project_id', $projectId)->where('is_active', true);
                }])
                ->whereHas('karyawans', function($query) use ($projectId) {
                    $query->where('project_id', $projectId)->where('is_active', true);
                })
                ->orderBy('nama')
                ->get()
                ->map(function($jabatan) {
                    return [
                        'id' => $jabatan->id,
                        'nama' => $jabatan->nama,
                        'karyawan_count' => $jabatan->karyawans_count
                    ];
                });
            
            return response()->json($jabatans);
        });
        
        // Template Karyawan Routes
        Route::get('template-karyawan', [\App\Http\Controllers\Perusahaan\TemplateKaryawanController::class, 'index'])->name('template-karyawan.index');
        Route::post('template-karyawan', [\App\Http\Controllers\Perusahaan\TemplateKaryawanController::class, 'store'])->name('template-karyawan.store');
        Route::post('template-karyawan/delete-by-name', [\App\Http\Controllers\Perusahaan\TemplateKaryawanController::class, 'destroyByName'])->name('template-karyawan.destroy-by-name');
        Route::get('template-karyawan/get-karyawans', [\App\Http\Controllers\Perusahaan\TemplateKaryawanController::class, 'getKaryawans'])->name('template-karyawan.get-karyawans');
        Route::get('template-karyawan/get-by-karyawan', [\App\Http\Controllers\Perusahaan\TemplateKaryawanController::class, 'getTemplateByKaryawan'])->name('template-karyawan.get-by-karyawan');
        Route::get('template-karyawan/get-jabatan-template', [\App\Http\Controllers\Perusahaan\TemplateKaryawanController::class, 'getJabatanTemplateByKaryawan'])->name('template-karyawan.get-jabatan-template');
        
        // Manajemen Gaji Routes
        Route::get('manajemen-gaji', [\App\Http\Controllers\Perusahaan\ManajemenGajiController::class, 'index'])->name('manajemen-gaji.index');
        Route::get('manajemen-gaji/debug-stats', [\App\Http\Controllers\Perusahaan\ManajemenGajiController::class, 'debugStats'])->name('manajemen-gaji.debug-stats');
        Route::post('manajemen-gaji/clear-cache', [\App\Http\Controllers\Perusahaan\ManajemenGajiController::class, 'clearCache'])->name('manajemen-gaji.clear-cache');
        Route::put('manajemen-gaji/{karyawan}/update-gaji-pokok', [\App\Http\Controllers\Perusahaan\ManajemenGajiController::class, 'updateGajiPokok'])->name('manajemen-gaji.update-gaji-pokok');
        Route::post('manajemen-gaji/update-massal', [\App\Http\Controllers\Perusahaan\ManajemenGajiController::class, 'updateMassal'])->name('manajemen-gaji.update-massal');
        
        Route::resource('checkpoints', \App\Http\Controllers\Perusahaan\CheckpointController::class);
        Route::resource('patrolis', \App\Http\Controllers\Perusahaan\PatroliController::class)->only(['index', 'show']);
        Route::resource('users', \App\Http\Controllers\Perusahaan\UserController::class);
        
        // Patrol Management Routes
        Route::prefix('patrol')->name('patrol.')->group(function () {
            Route::get('kategori-insiden', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'kategoriInsiden'])->name('kategori-insiden');
            Route::post('kategori-insiden', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeKategoriInsiden'])->name('kategori-insiden.store');
            Route::get('kategori-insiden/{kategoriInsiden}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editKategoriInsiden'])->name('kategori-insiden.edit');
            Route::put('kategori-insiden/{kategoriInsiden}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateKategoriInsiden'])->name('kategori-insiden.update');
            Route::delete('kategori-insiden/{kategoriInsiden}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyKategoriInsiden'])->name('kategori-insiden.destroy');
            
            Route::get('area', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'area'])->name('area');
            Route::post('area', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeArea'])->name('area.store');
            Route::get('area/{areaPatrol}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editArea'])->name('area.edit');
            Route::put('area/{areaPatrol}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateArea'])->name('area.update');
            Route::delete('area/{areaPatrol}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyArea'])->name('area.destroy');
            
            Route::get('rute-patrol', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'rutePatrol'])->name('rute-patrol');
            Route::post('rute-patrol', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeRutePatrol'])->name('rute-patrol.store');
            Route::get('rute-patrol/{rutePatrol}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editRutePatrol'])->name('rute-patrol.edit');
            Route::put('rute-patrol/{rutePatrol}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateRutePatrol'])->name('rute-patrol.update');
            Route::delete('rute-patrol/{rutePatrol}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyRutePatrol'])->name('rute-patrol.destroy');
            
            Route::get('checkpoint', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'checkpoint'])->name('checkpoint');
            Route::post('checkpoint', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeCheckpoint'])->name('checkpoint.store');
            Route::get('checkpoint/{checkpoint}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editCheckpoint'])->name('checkpoint.edit');
            Route::put('checkpoint/{checkpoint}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateCheckpoint'])->name('checkpoint.update');
            Route::delete('checkpoint/{checkpoint}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyCheckpoint'])->name('checkpoint.destroy');
            Route::get('checkpoint/{checkpoint}/qr', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'showCheckpointQr'])->name('checkpoint.qr');
            Route::get('checkpoint/{checkpoint}/aset', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'checkpointAset'])->name('checkpoint.aset');
            Route::post('checkpoint/{checkpoint}/aset', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeCheckpointAset'])->name('checkpoint.aset.store');
            
            Route::get('aset-kawasan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'asetKawasan'])->name('aset-kawasan');
            Route::post('aset-kawasan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeAsetKawasan'])->name('aset-kawasan.store');
            Route::get('aset-kawasan/{asetKawasan}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editAsetKawasan'])->name('aset-kawasan.edit');
            Route::put('aset-kawasan/{asetKawasan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateAsetKawasan'])->name('aset-kawasan.update');
            Route::delete('aset-kawasan/{asetKawasan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyAsetKawasan'])->name('aset-kawasan.destroy');
            
            Route::get('inventaris-patroli', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'inventarisPatroli'])->name('inventaris-patroli');
            Route::post('inventaris-patroli', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeInventarisPatroli'])->name('inventaris-patroli.store');
            Route::get('inventaris-patroli/{inventarisPatroli}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editInventarisPatroli'])->name('inventaris-patroli.edit');
            Route::put('inventaris-patroli/{inventarisPatroli}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateInventarisPatroli'])->name('inventaris-patroli.update');
            Route::delete('inventaris-patroli/{inventarisPatroli}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyInventarisPatroli'])->name('inventaris-patroli.destroy');
            
            Route::get('kuesioner-patroli', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'kuesionerPatroli'])->name('kuesioner-patroli');
            Route::post('kuesioner-patroli', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storeKuesionerPatroli'])->name('kuesioner-patroli.store');
            Route::get('kuesioner-patroli/{kuesionerPatroli}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editKuesionerPatroli'])->name('kuesioner-patroli.edit');
            Route::put('kuesioner-patroli/{kuesionerPatroli}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateKuesionerPatroli'])->name('kuesioner-patroli.update');
            Route::delete('kuesioner-patroli/{kuesionerPatroli}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyKuesionerPatroli'])->name('kuesioner-patroli.destroy');
            Route::get('kuesioner-patroli/{kuesionerPatroli}/pertanyaan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'kelolaPertanyaan'])->name('kuesioner-patroli.pertanyaan');
            Route::post('kuesioner-patroli/{kuesionerPatroli}/pertanyaan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storePertanyaan'])->name('kuesioner-patroli.pertanyaan.store');
            Route::put('kuesioner-patroli/{kuesionerPatroli}/pertanyaan/{pertanyaan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updatePertanyaan'])->name('kuesioner-patroli.pertanyaan.update');
            Route::delete('kuesioner-patroli/{kuesionerPatroli}/pertanyaan/{pertanyaan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyPertanyaan'])->name('kuesioner-patroli.pertanyaan.destroy');
            Route::post('kuesioner-patroli/{kuesionerPatroli}/urutan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateUrutanPertanyaan'])->name('kuesioner-patroli.urutan');
            Route::get('kuesioner-patroli/{kuesionerPatroli}/preview', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'previewKuesioner'])->name('kuesioner-patroli.preview');
            
            Route::get('pemeriksaan-patroli', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'pemeriksaanPatroli'])->name('pemeriksaan-patroli');
            Route::post('pemeriksaan-patroli', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storePemeriksaanPatroli'])->name('pemeriksaan-patroli.store');
            Route::get('pemeriksaan-patroli/{pemeriksaanPatroli}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editPemeriksaanPatroli'])->name('pemeriksaan-patroli.edit');
            Route::put('pemeriksaan-patroli/{pemeriksaanPatroli}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updatePemeriksaanPatroli'])->name('pemeriksaan-patroli.update');
            Route::delete('pemeriksaan-patroli/{pemeriksaanPatroli}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyPemeriksaanPatroli'])->name('pemeriksaan-patroli.destroy');
            Route::get('pemeriksaan-patroli/{pemeriksaanPatroli}/pertanyaan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'kelolaPertanyaanPemeriksaan'])->name('pemeriksaan-patroli.pertanyaan');
            Route::post('pemeriksaan-patroli/{pemeriksaanPatroli}/pertanyaan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storePertanyaanPemeriksaan'])->name('pemeriksaan-patroli.pertanyaan.store');
            Route::put('pemeriksaan-patroli/{pemeriksaanPatroli}/pertanyaan/{pertanyaan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updatePertanyaanPemeriksaan'])->name('pemeriksaan-patroli.pertanyaan.update');
            Route::delete('pemeriksaan-patroli/{pemeriksaanPatroli}/pertanyaan/{pertanyaan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyPertanyaanPemeriksaan'])->name('pemeriksaan-patroli.pertanyaan.destroy');
            Route::post('pemeriksaan-patroli/{pemeriksaanPatroli}/urutan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateUrutanPertanyaanPemeriksaan'])->name('pemeriksaan-patroli.urutan');
            Route::get('pemeriksaan-patroli/{pemeriksaanPatroli}/preview', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'previewPemeriksaan'])->name('pemeriksaan-patroli.preview');
            
            // Pertanyaan Tamu Routes
            Route::get('pertanyaan-tamu', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'pertanyaanTamu'])->name('pertanyaan-tamu');
            Route::post('pertanyaan-tamu', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storePertanyaanTamu'])->name('pertanyaan-tamu.store');
            Route::get('pertanyaan-tamu/{kuesionerTamu}/edit', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'editPertanyaanTamu'])->name('pertanyaan-tamu.edit');
            Route::put('pertanyaan-tamu/{kuesionerTamu}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updatePertanyaanTamu'])->name('pertanyaan-tamu.update');
            Route::delete('pertanyaan-tamu/{kuesionerTamu}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyPertanyaanTamu'])->name('pertanyaan-tamu.destroy');
            Route::get('pertanyaan-tamu/{kuesionerTamu}/kelola', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'kelolaPertanyaanTamu'])->name('pertanyaan-tamu.kelola');
            Route::post('pertanyaan-tamu/{kuesionerTamu}/pertanyaan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'storePertanyaanDetailTamu'])->name('pertanyaan-tamu.pertanyaan.store');
            Route::put('pertanyaan-tamu/{kuesionerTamu}/pertanyaan/{pertanyaan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updatePertanyaanDetailTamu'])->name('pertanyaan-tamu.pertanyaan.update');
            Route::delete('pertanyaan-tamu/{kuesionerTamu}/pertanyaan/{pertanyaan}', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'destroyPertanyaanDetailTamu'])->name('pertanyaan-tamu.pertanyaan.destroy');
            Route::post('pertanyaan-tamu/{kuesionerTamu}/urutan', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'updateUrutanPertanyaanTamu'])->name('pertanyaan-tamu.urutan');
            Route::get('pertanyaan-tamu/{kuesionerTamu}/preview', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'previewKuesionerTamu'])->name('pertanyaan-tamu.preview');
            Route::get('get-areas-by-project', [\App\Http\Controllers\Perusahaan\PatrolController::class, 'getAreasByProject'])->name('get-areas-by-project');
        });
        
        // Tim Patroli Routes
        Route::prefix('tim-patroli')->name('tim-patroli.')->group(function () {
            Route::get('master', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'master'])->name('master');
            Route::get('create', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'store'])->name('store');
            Route::get('{timPatroli}/edit', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'edit'])->name('edit');
            Route::put('{timPatroli}', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'update'])->name('update');
            Route::delete('{timPatroli}', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'destroy'])->name('destroy');
            Route::get('get-data-by-project/{project}', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'getDataByProject'])->name('get-data-by-project');
            Route::post('get-rutes-by-areas', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'getRutesByAreas'])->name('get-rutes-by-areas');
            Route::post('get-checkpoints-by-rutes', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'getCheckpointsByRutes'])->name('get-checkpoints-by-rutes');
            Route::get('inventaris', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'inventaris'])->name('inventaris');
            
            // Anggota Tim Patroli Routes
            Route::prefix('{timPatroli}/anggota')->name('anggota.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'index'])->name('index');
                Route::get('create', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'store'])->name('store');
                Route::get('{anggotaTimPatroli}', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'show'])->name('show');
                Route::get('{anggotaTimPatroli}/edit', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'edit'])->name('edit');
                Route::put('{anggotaTimPatroli}', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'update'])->name('update');
                Route::delete('{anggotaTimPatroli}', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'destroy'])->name('destroy');
                Route::patch('{anggotaTimPatroli}/nonaktifkan', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'nonaktifkan'])->name('nonaktifkan');
                Route::patch('{anggotaTimPatroli}/aktifkan', [\App\Http\Controllers\Perusahaan\AnggotaTimPatroliController::class, 'aktifkan'])->name('aktifkan');
            });
        });
        
        // Laporan Patroli Routes
        Route::prefix('laporan-patroli')->name('laporan-patroli.')->group(function () {
            Route::get('insiden', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'insiden'])->name('insiden');
            Route::get('kawasan', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'kawasan'])->name('kawasan');
            Route::get('kawasan/{area}/detail', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'kawasanDetail'])->name('kawasan.detail');
            Route::get('aset-bermasalah', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'asetBermasalah'])->name('aset-bermasalah');
            Route::get('inventaris', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'inventaris'])->name('inventaris');
            Route::get('kru-change', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'kruChange'])->name('kru-change');
        });
    });
});

// Mobile PWA Routes - app.nicepatrol.id
Route::domain(env('MOBILE_DOMAIN', 'app.nicepatrol.id'))->group(function () {
    // PWA Manifest (dynamic)
    Route::get('/mobile/manifest', [\App\Http\Controllers\Mobile\ManifestController::class, 'manifest'])->name('mobile.manifest');
    
    // Login page (no auth required)
    Route::get('/login', [\App\Http\Controllers\Mobile\AuthController::class, 'showLogin'])->name('mobile.login');
    Route::get('/', function() {
        return redirect()->route('mobile.login');
    });
    
    // Security Officer Views (no auth middleware - handled by JS with token)
    Route::prefix('security')->name('mobile.security.')->group(function () {
        Route::get('/home', function() {
            return view('mobile.security.home');
        })->name('home');
        Route::get('/scan', function() {
            return view('mobile.security.scan');
        })->name('scan-page');
        Route::get('/checkpoint/{checkpoint}', function($checkpoint) {
            return view('mobile.security.checkpoint');
        })->name('checkpoint');
        Route::get('/shift-schedule', function() {
            return view('mobile.security.shift-schedule');
        })->name('shift-schedule');
        Route::get('/absensi-schedule', function() {
            return view('mobile.security.absensi-schedule');
        })->name('absensi-schedule');
        Route::get('/absensi', function() {
            return view('mobile.security.absensi');
        })->name('absensi');
        Route::get('/patroli', [\App\Http\Controllers\Mobile\PatroliController::class, 'index'])->name('patroli.index');
        Route::get('/patroli/create', [\App\Http\Controllers\Mobile\PatroliController::class, 'create'])->name('patroli.create');
        Route::get('/scan-qr', [\App\Http\Controllers\Mobile\ScanController::class, 'index'])->name('scan-qr');
        Route::get('/aktivitas', function() {
            return view('mobile.security.aktivitas');
        })->name('aktivitas');
        Route::get('/patrol', function() {
            return view('mobile.security.patrol');
        })->name('patrol');
        Route::get('/gps-tracking', function() {
            return view('mobile.security.gps-tracking');
        })->name('gps-tracking');
    });
    
    // Office Employee Views (no auth middleware - handled by JS with token)
    Route::prefix('employee')->name('mobile.employee.')->group(function () {
        Route::get('/home', function() {
            return view('mobile.employee.home');
        })->name('home');
        Route::get('/kehadiran', function() {
            return view('mobile.employee.kehadiran');
        })->name('kehadiran.index');
        Route::get('/absensi', function() {
            return view('mobile.employee.absensi');
        })->name('absensi');
    });
    
    // Shared Views
    Route::get('/profile', function() {
        return view('mobile.security.profile');
    })->name('mobile.profile');
});


// Fallback routes for IP access (mobile testing from phone)
// This handles http://192.168.x.x:8000/security/home etc

// PWA Manifest for IP access
Route::get('/mobile/manifest', [\App\Http\Controllers\Mobile\ManifestController::class, 'manifest']);

Route::prefix('security')->group(function () {
    Route::get('/home', function() {
        return view('mobile.security.home');
    });
    Route::get('/scan', function() {
        return view('mobile.security.scan');
    });
    Route::get('/checkpoint/{checkpoint}', function($checkpoint) {
        return view('mobile.security.checkpoint');
    });
    Route::get('/shift-schedule', function() {
        return view('mobile.security.shift-schedule');
    });
    Route::get('/absensi-schedule', function() {
        return view('mobile.security.absensi-schedule');
    });
    Route::get('/absensi', function() {
        return view('mobile.security.absensi');
    });
    Route::get('/patroli', function() {
        return view('mobile.security.patroli');
    });
    Route::get('/scan-qr', function() {
        return view('mobile.security.scan');
    });
    Route::get('/aktivitas', function() {
        return view('mobile.security.aktivitas');
    });
    Route::get('/patrol', function() {
        return view('mobile.security.patrol');
    });
    Route::get('/gps-tracking', function() {
        return view('mobile.security.gps-tracking');
    });
});

Route::prefix('employee')->group(function () {
    Route::get('/home', function() {
        return view('mobile.employee.home');
    });
    Route::get('/kehadiran', function() {
        return view('mobile.employee.kehadiran');
    });
    Route::get('/absensi', function() {
        return view('mobile.employee.absensi');
    });
});

Route::get('/profile', function() {
    return view('mobile.security.profile');
});

// API Documentation Routes
Route::get('/api-docs', function() {
    return response()->file(public_path('api-docs.html'));
})->name('api.docs');

Route::get('/docs/api/swagger.yaml', function() {
    $yamlPath = base_path('docs/api/swagger.yaml');
    if (file_exists($yamlPath)) {
        return response()->file($yamlPath, [
            'Content-Type' => 'application/x-yaml'
        ]);
    }
    return abort(404);
})->name('api.swagger.yaml');
