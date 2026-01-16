<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Icon -->
            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-yellow-600 text-4xl"></i>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-gray-800 mb-2">404</h1>
            
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Halaman Tidak Ditemukan</h2>
            
            <!-- Message -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700 leading-relaxed">
                    Maaf, halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <!-- Back Button -->
                <button onclick="window.history.back()" class="w-full px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </button>

                <!-- Dashboard Links -->
                @if(auth()->check())
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
                    <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition">
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
                Nice Patrol System
            </p>
        </div>
    </div>
</body>
</html>
