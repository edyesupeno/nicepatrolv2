<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PerusahaanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\CheckpointController;
use App\Http\Controllers\Api\PatroliController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

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

