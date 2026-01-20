<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set project scope untuk user yang login
        if (auth()->check()) {
            $user = auth()->user();
            
            // Jika bukan superadmin, set project scope
            if (!$user->isSuperAdmin()) {
                // Get project_id dari karyawan
                $projectId = null;
                if ($user->karyawan && $user->karyawan->project_id) {
                    $projectId = $user->karyawan->project_id;
                }
                
                // Set ke request untuk digunakan di controller
                $request->merge(['user_project_id' => $projectId]);
            }
        }
        
        return $next($request);
    }
}