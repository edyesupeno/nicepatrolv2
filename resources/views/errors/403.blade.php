<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Icon -->
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-lock text-red-600 text-4xl"></i>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-gray-800 mb-2">403</h1>
            
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Akses Ditolak</h2>
            
            <!-- Message -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}
                </p>
            </div>

            <!-- Info Box -->
            @if(auth()->check())
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                    <p class="text-xs font-semibold text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Informasi Akun Anda:
                    </p>
                    <div class="space-y-1 text-xs text-blue-800">
                        <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <p><strong>Role:</strong> 
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if(auth()->user()->role === 'superadmin')
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6 text-left">
                        <p class="text-xs font-semibold text-purple-900 mb-2">
                            <i class="fas fa-crown mr-1"></i>
                            Petunjuk untuk Superadmin:
                        </p>
                        <p class="text-xs text-purple-800 leading-relaxed">
                            Untuk mengakses panel perusahaan, silakan logout dan login menggunakan akun admin perusahaan (contoh: abb@nicepatrol.id).
                        </p>
                    </div>
                @endif
            @endif

            <!-- Actions -->
            <div class="space-y-3">
                @if(auth()->check())
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout & Login Ulang
                        </button>
                    </form>

                    <!-- Back Button -->
                    <button onclick="window.history.back()" class="w-full px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </button>

                    <!-- Dashboard Links -->
                    @if(auth()->user()->role === 'superadmin')
                        <a href="{{ route('dashboard') }}" class="block w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard Superadmin
                        </a>
                    @elseif(auth()->user()->perusahaan_id)
                        <a href="{{ route('perusahaan.dashboard') }}" class="block w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard Perusahaan
                        </a>
                    @endif
                @else
                    <!-- Login Button -->
                    <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login
                    </a>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                <i class="fas fa-shield-alt mr-1"></i>
                Nice Patrol System - Secure Access
            </p>
        </div>
    </div>
</body>
</html>
