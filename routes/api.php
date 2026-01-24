<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PerusahaanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\CheckpointController;
use App\Http\Controllers\Api\AsetCheckpointController;
use App\Http\Controllers\Api\PatroliController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\PenerimaanBarangController;

// API Routes
// Production: api.nicepatrol.id/v1
// Local: localhost:8000/api/v1 or any-domain:8000/api/v1
$apiRoutes = function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/user', [AuthController::class, 'me']); // Alias
        Route::post('/user/upload-photo', [AuthController::class, 'uploadPhoto']);

        // Shift Schedule
        Route::get('/shift/my-schedule', [ShiftController::class, 'mySchedule']);
        Route::get('/shift/today', [ShiftController::class, 'todayShift']);

        // Absensi
        Route::get('/absensi/summary', [AbsensiController::class, 'summary']);
        Route::get('/absensi/my-schedule', [AbsensiController::class, 'mySchedule']);
        Route::get('/absensi/lokasi', [AbsensiController::class, 'getLokasiAbsensi']);
        Route::get('/absensi/today-status', [AbsensiController::class, 'checkTodayStatus']);
        Route::post('/absensi/check-in', [AbsensiController::class, 'checkIn']);
        Route::post('/absensi/check-out', [AbsensiController::class, 'checkOut']);
        Route::post('/absensi/take-break', [AbsensiController::class, 'takeBreak']);
        Route::post('/absensi/return-from-break', [AbsensiController::class, 'returnFromBreak']);

        // Perusahaan (hanya superadmin)
        Route::apiResource('perusahaans', PerusahaanController::class);

        // Users
        Route::apiResource('users', UserController::class);

        // Lokasi
        Route::apiResource('lokasis', LokasiController::class);

        // Checkpoint
        Route::apiResource('checkpoints', CheckpointController::class);
        Route::get('checkpoints/{checkpoint}/asets', [AsetCheckpointController::class, 'checkpointAsets']);
        Route::post('checkpoints/{checkpoint}/aset-status', [AsetCheckpointController::class, 'updateAsetStatus']);

        // Patroli
        Route::apiResource('patrolis', PatroliController::class);
        Route::post('patrolis/{patroli}/scan', [PatroliController::class, 'scanCheckpoint']);
        Route::get('patrolis/{patroli}/gps-locations', [PatroliController::class, 'getGpsLocations']);
        Route::post('scan-qr', [PatroliController::class, 'scanQRCode']);

        // Project Contacts
        Route::get('projects/{project}/contacts', [\App\Http\Controllers\Api\ProjectContactController::class, 'index']);
        Route::post('projects/{project}/contacts', [\App\Http\Controllers\Api\ProjectContactController::class, 'store']);
        Route::get('projects/{project}/contacts/{contact}', [\App\Http\Controllers\Api\ProjectContactController::class, 'show']);
        Route::put('projects/{project}/contacts/{contact}', [\App\Http\Controllers\Api\ProjectContactController::class, 'update']);
        Route::delete('projects/{project}/contacts/{contact}', [\App\Http\Controllers\Api\ProjectContactController::class, 'destroy']);
        Route::get('projects/{project}/contacts/emergency', [\App\Http\Controllers\Api\ProjectContactController::class, 'emergency']);

        // Penerimaan Barang
        Route::apiResource('penerimaan-barang', \App\Http\Controllers\Api\PenerimaanBarangController::class);
        Route::get('penerimaan-barang-projects', [\App\Http\Controllers\Api\PenerimaanBarangController::class, 'getProjects']);
        Route::get('penerimaan-barang-areas/{project}', [\App\Http\Controllers\Api\PenerimaanBarangController::class, 'getAreasByProject']);
        Route::get('penerimaan-barang-my-areas', [\App\Http\Controllers\Api\PenerimaanBarangController::class, 'getMyAreas']);

        // Buku Tamu (Guest Book)
        Route::apiResource('buku-tamu', \App\Http\Controllers\Api\BukuTamuController::class);
        Route::post('buku-tamu/{bukuTamu}/check-out', [\App\Http\Controllers\Api\BukuTamuController::class, 'checkOut']);
        Route::get('buku-tamu/qr/{qr_code}', [\App\Http\Controllers\Api\BukuTamuController::class, 'getByQrCode']);
        Route::get('buku-tamu-project-settings', [\App\Http\Controllers\Api\BukuTamuController::class, 'getProjectSettings']);
        Route::get('buku-tamu-kuesioner-by-area', [\App\Http\Controllers\Api\BukuTamuController::class, 'getKuesionerByArea']);
        Route::post('buku-tamu/{bukuTamu}/questionnaire', [\App\Http\Controllers\Api\BukuTamuController::class, 'saveGuestQuestionnaire']);
        Route::get('buku-tamu-statistics', [\App\Http\Controllers\Api\BukuTamuController::class, 'getStatistics']);
        Route::get('buku-tamu-available-cards', [\App\Http\Controllers\Api\BukuTamuController::class, 'getAvailableCards']);
        Route::post('buku-tamu/{bukuTamu}/assign-card', [\App\Http\Controllers\Api\BukuTamuController::class, 'assignCard']);
        Route::post('buku-tamu/{bukuTamu}/return-card', [\App\Http\Controllers\Api\BukuTamuController::class, 'returnCard']);

        // Patroli Mandiri
        Route::apiResource('patroli-mandiri', \App\Http\Controllers\Api\PatroliMandiriController::class);
        Route::get('patroli-mandiri-projects', [\App\Http\Controllers\Api\PatroliMandiriController::class, 'getProjects']);
        Route::get('patroli-mandiri-areas/{project}', [\App\Http\Controllers\Api\PatroliMandiriController::class, 'getAreasByProject']);
        Route::get('patroli-mandiri-jenis-kendala', [\App\Http\Controllers\Api\PatroliMandiriController::class, 'getJenisKendala']);
    });
};

// Use domain routing for production, prefix for local
if (app()->environment('production')) {
    Route::domain(config('app.api_domain'))->prefix('v1')->group($apiRoutes);
} else {
    // Local/Development: accessible from any domain with /api/v1 prefix
    // Note: Laravel already adds 'api' prefix from routes/api.php
    Route::prefix('v1')->group($apiRoutes);
}

