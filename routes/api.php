<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PerusahaanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\CheckpointController;
use App\Http\Controllers\Api\PatroliController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\AbsensiController;

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

        // Perusahaan (hanya superadmin)
        Route::apiResource('perusahaans', PerusahaanController::class);

        // Users
        Route::apiResource('users', UserController::class);

        // Lokasi
        Route::apiResource('lokasis', LokasiController::class);

        // Checkpoint
        Route::apiResource('checkpoints', CheckpointController::class);

        // Patroli
        Route::apiResource('patrolis', PatroliController::class);
        Route::post('patrolis/{patroli}/scan', [PatroliController::class, 'scanCheckpoint']);
    });
};

// Use domain routing for production, prefix for local
if (app()->environment('production')) {
    Route::domain(env('API_DOMAIN', 'api.nicepatrol.id'))->prefix('v1')->group($apiRoutes);
} else {
    // Local: accessible from any domain with /api/v1 prefix
    // Note: Laravel already adds 'api' prefix from routes/api.php
    Route::prefix('v1')->group($apiRoutes);
}

