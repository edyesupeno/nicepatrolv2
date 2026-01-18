<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Add storage routes without middleware
            Route::get('/storage/{path}', function ($path) {
                $fullPath = storage_path('app/public/' . $path);
                
                if (!file_exists($fullPath)) {
                    abort(404);
                }
                
                $mimeType = mime_content_type($fullPath);
                
                return response()->file($fullPath, [
                    'Content-Type' => $mimeType,
                    'Cache-Control' => 'public, max-age=3600',
                ]);
            })->where('path', '.*');
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxies for Cloudflare
        $middleware->trustProxies(at: '*');
        
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperadminMiddleware::class,
            'perusahaan' => \App\Http\Middleware\PerusahaanMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'mobile' => \App\Http\Middleware\CheckMobileRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
