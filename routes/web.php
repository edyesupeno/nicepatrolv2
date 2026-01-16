<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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
        Route::resource('areas', \App\Http\Controllers\Perusahaan\AreaController::class);
        Route::resource('jabatans', \App\Http\Controllers\Perusahaan\JabatanController::class);
        Route::get('status-karyawan', [\App\Http\Controllers\Perusahaan\StatusKaryawanController::class, 'index'])->name('status-karyawan.index');
        
        // Kehadiran Routes
        Route::get('kehadiran', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'kehadiran'])->name('kehadiran.index');
        Route::get('kehadiran/rekap-kehadiran', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'rekap'])->name('kehadiran.rekap-kehadiran');
        Route::get('kehadiran/rekap-kehadiran/export-pdf', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'rekapPdf'])->name('kehadiran.rekap-kehadiran.export-pdf');
        Route::get('kehadiran/{id}/show', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'show'])->name('kehadiran.show');
        Route::post('kehadiran', [\App\Http\Controllers\Perusahaan\KehadiranController::class, 'store'])->name('kehadiran.store');
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
            // Get jabatan IDs yang ada di project ini (dari karyawan)
            $jabatanIds = \App\Models\Karyawan::where('project_id', $projectId)
                ->distinct()
                ->pluck('jabatan_id')
                ->toArray();
            
            // Return jabatan yang ada di project
            return \App\Models\Jabatan::select('id', 'nama')
                ->whereIn('id', $jabatanIds)
                ->orderBy('nama')
                ->get();
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
            Route::get('inventaris', [\App\Http\Controllers\Perusahaan\TimPatroliController::class, 'inventaris'])->name('inventaris');
        });
        
        // Laporan Patroli Routes
        Route::prefix('laporan-patroli')->name('laporan-patroli.')->group(function () {
            Route::get('insiden', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'insiden'])->name('insiden');
            Route::get('kawasan', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'kawasan'])->name('kawasan');
            Route::get('inventaris', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'inventaris'])->name('inventaris');
            Route::get('kru-change', [\App\Http\Controllers\Perusahaan\LaporanPatroliController::class, 'kruChange'])->name('kru-change');
        });
    });
});
