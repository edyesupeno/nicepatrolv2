<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMobileRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user has mobile access (security_officer or office_employee)
        if (!$user->isSecurityOfficer() && !$user->isOfficeEmployee()) {
            abort(403, 'Anda tidak memiliki akses ke aplikasi mobile. Silakan gunakan dashboard web.');
        }
        
        return $next($request);
    }
}
