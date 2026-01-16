<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerusahaanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
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
