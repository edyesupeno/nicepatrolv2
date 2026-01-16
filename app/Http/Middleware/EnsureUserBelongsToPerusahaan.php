<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToPerusahaan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Superadmin bisa akses semua
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // User lain harus punya perusahaan_id
        if ($user && !$user->perusahaan_id) {
            abort(403, 'User tidak terdaftar di perusahaan manapun');
        }

        return $next($request);
    }
}
