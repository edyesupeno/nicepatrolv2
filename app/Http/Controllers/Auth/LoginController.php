<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Check if accessing from mobile domain
        if (request()->getHost() === env('MOBILE_DOMAIN', 'devapp.nicepatrol.id')) {
            if (Auth::check()) {
                return redirect()->route('mobile.home');
            }
            return view('mobile.auth.login');
        }
        
        // Dashboard login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif.',
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to appropriate domain based on current host
        $host = $request->getHost();
        
        // If logging out from mobile domain, redirect to mobile login
        if ($host === env('MOBILE_DOMAIN', 'app.nicepatrol.id')) {
            return redirect()->to('https://' . env('MOBILE_DOMAIN', 'app.nicepatrol.id') . '/login');
        }
        
        // If logging out from dashboard domain, redirect to dashboard login
        if ($host === env('DASHBOARD_DOMAIN', 'devdash.nicepatrol.id')) {
            return redirect()->to('https://' . env('DASHBOARD_DOMAIN', 'devdash.nicepatrol.id') . '/login');
        }
        
        // Fallback to login route (should not happen in normal flow)
        return redirect()->route('login');
    }
}
