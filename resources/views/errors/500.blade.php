<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-red-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Icon -->
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-red-600 text-4xl"></i>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-gray-800 mb-2">500</h1>
            
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Server Error</h2>
            
            <!-- Message -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700 leading-relaxed">
                    Maaf, terjadi kesalahan pada server. Tim kami sedang memperbaikinya.
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <!-- Refresh Button -->
                <button onclick="window.location.reload()" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Muat Ulang Halaman
                </button>

                <!-- Back Button -->
                <button onclick="window.history.back()" class="w-full px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </button>

                <!-- Dashboard Links -->
                @if(auth()->check())
                    @if(auth()->user()->role === 'superadmin')
                        <a href="{{ route('dashboard') }}" class="block w-full px-6 py-3 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard Superadmin
                        </a>
                    @elseif(auth()->user()->perusahaan_id)
                        <a href="{{ route('perusahaan.dashboard') }}" class="block w-full px-6 py-3 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard Perusahaan
                        </a>
                    @endif
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
