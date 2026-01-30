<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerusahaanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // TEMPORARY: Disable authentication for testing
        // Auto-login as test user if not authenticated
        if (!auth()->check()) {
            $testUser = \App\Models\User::where('email', 'abb@nicepatrol.id')->first();
            if ($testUser) {
                auth()->login($testUser);
                \Log::info('Auto-logged in test user for development');
            } else {
                \Log::warning('PerusahaanMiddleware: User not authenticated, redirecting to login', [
                    'url' => $request->url(),
                    'session_id' => session()->getId()
                ]);
                return redirect()->route('login');
            }
        }

        if (auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses panel perusahaan hanya untuk Admin Perusahaan. Superadmin tidak dapat mengakses panel ini. Silakan logout dan login menggunakan akun admin perusahaan (contoh: abb@nicepatrol.id).');
        }

        if (!auth()->user()->perusahaan_id) {
            abort(403, 'Anda tidak terdaftar di perusahaan manapun. Silakan hubungi administrator untuk mendaftarkan akun Anda ke perusahaan.');
        }

        return $next($request);
    }
}
