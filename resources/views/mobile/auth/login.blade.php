@extends('mobile.layouts.app')

@section('title', 'Masuk - Nice Patrol')

@section('content')
<div class="min-h-screen flex flex-col bg-gradient-to-br from-indigo-50 via-white to-blue-50">
    <!-- Header with Logo -->
    <div class="pt-6 pb-2 text-center">
        <div class="flex items-center justify-center mb-4">
            <div class="bg-indigo-600 p-3 rounded-2xl shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                </svg>
            </div>
        </div>
        <h1 class="text-xl font-bold text-gray-800">Nice Patrol</h1>
        <p class="text-sm text-gray-500 mt-1">Masuk ke dashboard Anda</p>
    </div>

    <!-- Illustration -->
    <div class="flex-shrink-0 px-6 mb-6">
        <div class="relative h-48 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-3xl overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-4 right-8 w-16 h-16 bg-indigo-200 rounded-full opacity-50"></div>
            <div class="absolute bottom-8 left-8 w-12 h-12 bg-blue-200 rounded-full opacity-50"></div>
            <div class="absolute top-12 left-16 w-8 h-8 bg-indigo-300 rounded-full opacity-30"></div>
            
            <!-- Security Icon -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <!-- Shield with checkmark -->
                    <div class="relative inline-block">
                        <svg class="w-32 h-32 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                        <!-- Checkmark -->
                        <svg class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    
                    <!-- Text -->
                    <div class="mt-4">
                        <p class="text-sm font-semibold text-indigo-700">Sistem Patroli Keamanan</p>
                        <p class="text-xs text-indigo-500 mt-1">Monitoring & Reporting</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="flex-1 px-6 pb-8">
        <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Masuk</h2>
        
            <form id="loginForm">
                <!-- Email Input -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="block w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="hama@email.com"
                            required
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="block w-full pl-12 pr-12 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="••••••••"
                            required
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center"
                        >
                            <svg id="eyeIcon" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    id="loginBtn"
                    class="w-full bg-indigo-600 text-white py-3.5 rounded-xl font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all shadow-lg shadow-indigo-200"
                >
                    Masuk
                </button>
            </form>
            
            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">© 2026 Nice Patrol. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Debug: Log API URL
console.log('API_BASE_URL:', API_BASE_URL);
console.log('Current hostname:', window.location.hostname);
console.log('Current port:', window.location.port);

// Toggle Password Visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>';
    }
});

// Form Submit - Call API
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const loginBtn = document.getElementById('loginBtn');
    
    // Debug logging
    console.log('=== LOGIN ATTEMPT ===');
    console.log('Email:', email);
    console.log('API_BASE_URL:', API_BASE_URL);
    console.log('Full URL will be:', `${API_BASE_URL}/login`);
    
    // Validation
    if (!email || !password) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Email dan Password harus diisi!',
        });
        return;
    }
    
    // Disable button & show loading
    loginBtn.disabled = true;
    loginBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        // Call API using API helper
        console.log('Calling API.post...');
        const data = await API.post('/login', {
            email: email,
            password: password,
        });
        
        console.log('API Response:', data);
        
        if (data.success) {
            // Save token to localStorage
            localStorage.setItem('auth_token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil',
                text: `Selamat datang, ${data.data.user.name}!`,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Redirect based on role
                if (data.data.user.role === 'security_officer') {
                    window.location.href = '/security/home';
                } else if (data.data.user.role === 'office_employee') {
                    window.location.href = '/employee/home';
                } else {
                    window.location.href = '/';
                }
            });
        } else {
            // Show error
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: data.message || 'Email atau password salah',
            });
            
            // Reset button
            loginBtn.disabled = false;
            loginBtn.innerHTML = 'Masuk';
        }
    } catch (error) {
        console.error('Login error:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack
        });
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan: ' + error.message,
        });
        
        // Reset button
        loginBtn.disabled = false;
        loginBtn.innerHTML = 'Masuk';
    }
});
</script>
@endpush
@endsection
