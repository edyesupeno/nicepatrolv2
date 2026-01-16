@extends('mobile.layouts.app')

@section('title', 'Home - Nice Patrol')

@section('content')
<div class="min-h-screen pb-20" style="background-color: #0071CE;">
    <!-- Header -->
    <div class="text-white px-4 pt-3 pb-3">
        <!-- Top Bar -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-3">
                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-md overflow-hidden">
                    <img id="userPhoto" src="https://ui-avatars.com/api/?name=User&background=0071CE&color=fff&size=56" alt="User" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-base font-medium"><span id="greeting">Selamat Siang</span>, <span id="userName">Jacob</span>!</p>
                    <p class="text-sm opacity-90" id="userRole">Security</p>
                </div>
            </div>
            <button class="relative">
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                </svg>
                <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
        </div>

        <!-- Shift Info Card -->
        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white bg-opacity-30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm opacity-90">Shift: I</p>
                    <p class="font-semibold text-base">07:00 - 15:00</p>
                </div>
            </div>
            <button class="bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md">
                Absen Masuk
            </button>
        </div>
    </div>

    <!-- Layanan Section -->
    <div class="px-4 mt-0">
        <h3 class="text-white text-lg font-semibold mb-2">Layanan</h3>
        <div class="grid grid-cols-4 gap-1.5">
            <!-- POB -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">POB</span>
                </div>
            </a>

            <!-- Buku Tamu -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">Buku Tamu</span>
                </div>
            </a>

            <!-- Tugas -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">Tugas</span>
                </div>
            </a>

            <!-- Call Center -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">Call Center</span>
                </div>
            </a>

            <!-- Kru Change -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">Kru Change</span>
                </div>
            </a>

            <!-- Blacklist -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">Blacklist</span>
                </div>
            </a>

            <!-- HSSE -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v2h-2V7zm0 4h2v6h-2v-6z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">HSSE</span>
                </div>
            </a>

            <!-- Otorisasi -->
            <a href="#" class="flex flex-col items-center">
                <div class="w-full aspect-square bg-white rounded-2xl shadow-md flex flex-col items-center justify-center p-1.5 hover:shadow-lg transition">
                    <svg class="w-9 h-9 mb-1" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                    <span style="font-size: 9px !important; font-weight: 500; color: #1f2937; line-height: 1.2;">Otorisasi</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Cari Lokasi Patroli -->
    <div class="px-4 mt-2 pb-4">
        <div class="bg-white rounded-2xl shadow-md p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-10 h-10" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Cari Lokasi Patroli</p>
                </div>
            </div>
            <button class="text-white px-5 py-2 rounded-xl text-sm font-semibold border-2" style="background-color: transparent; border-color: #0071CE; color: #0071CE;">
                Cari Patroli
            </button>
        </div>
    </div>
</div>

<!-- White Background Section -->
<div class="bg-white rounded-t-3xl pt-3" style="margin-top: -17rem;">
    <!-- Area Tabs -->
    <div class="px-4">
        <div class="flex items-center space-x-2 overflow-x-auto pb-2">
            <button class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full shadow-sm flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                </svg>
            </button>
            <button class="flex-shrink-0 px-4 py-2 text-white rounded-full text-sm font-semibold shadow-md" style="background-color: #0071CE;">
                Area 1
            </button>
            <button class="flex-shrink-0 px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold shadow-sm">
                Area 2
            </button>
            <button class="flex-shrink-0 px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold shadow-sm">
                Area 3
            </button>
            <button class="flex-shrink-0 px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold shadow-sm">
                Area 4
            </button>
            <button class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full shadow-sm flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Patrol Areas -->
    <div class="px-4 mt-4 pb-6">
        <div class="grid grid-cols-2 gap-3">
            <!-- Patrol Area A -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-32 bg-gray-200 relative">
                    <img src="https://via.placeholder.com/200x150?text=Patrol+Area+A" alt="Area A" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        Belum
                    </div>
                </div>
                <div class="p-3">
                    <h4 class="font-semibold text-gray-800 text-sm">Patrol Area A</h4>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        </svg>
                        31 Km Widi Mines
                    </p>
                </div>
            </div>

            <!-- Patrol Area B -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="h-32 bg-gray-200 relative">
                    <img src="https://via.placeholder.com/200x150?text=Patrol+Area+B" alt="Area B" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        Aman
                    </div>
                </div>
                <div class="p-3">
                    <h4 class="font-semibold text-gray-800 text-sm">Patrol Area B</h4>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        </svg>
                        23 Km Widi Mines
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Hari Ini -->
    <div class="px-4 mt-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-800">Aktivitas Hari Ini</h3>
            <a href="#" class="text-sm font-semibold" style="color: #0071CE;">View All</a>
        </div>

        <!-- Progress Cards -->
        <div class="grid grid-cols-3 gap-3">
            <!-- Area -->
            <div class="bg-white rounded-2xl shadow-md p-4 text-center">
                <div class="relative w-16 h-16 mx-auto mb-2">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                        <circle cx="32" cy="32" r="28" stroke="#0071CE" stroke-width="6" fill="none" 
                                stroke-dasharray="176" stroke-dashoffset="88" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-800">4/8</span>
                    </div>
                </div>
                <p class="text-xs text-gray-600 font-semibold">AREA</p>
            </div>

            <!-- Rounds -->
            <div class="bg-white rounded-2xl shadow-md p-4 text-center">
                <div class="relative w-16 h-16 mx-auto mb-2">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                        <circle cx="32" cy="32" r="28" stroke="#10b981" stroke-width="6" fill="none" 
                                stroke-dasharray="176" stroke-dashoffset="88" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-800">10/20</span>
                    </div>
                </div>
                <p class="text-xs text-gray-600 font-semibold">ROUNDS</p>
            </div>

            <!-- Assets -->
            <div class="bg-white rounded-2xl shadow-md p-4 text-center">
                <div class="relative w-16 h-16 mx-auto mb-2">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                        <circle cx="32" cy="32" r="28" stroke="#f59e0b" stroke-width="6" fill="none" 
                                stroke-dasharray="176" stroke-dashoffset="88" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-800">25/50</span>
                    </div>
                </div>
                <p class="text-xs text-gray-600 font-semibold">ASSETS</p>
            </div>
        </div>
    </div>

    <!-- Tugas Section -->
    <div class="px-4 mt-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-800">Tugas</h3>
            <a href="#" class="text-sm font-semibold" style="color: #0071CE;">View All</a>
        </div>

        <!-- Task Tabs -->
        <div class="flex space-x-2 mb-3">
            <button class="px-4 py-2 text-white rounded-xl text-sm font-semibold" style="background-color: #0071CE;">Tugas</button>
            <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold">Proses</button>
            <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold">Tinjau</button>
            <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold">Selesai</button>
        </div>

        <!-- Task Item -->
        <div class="bg-white rounded-2xl shadow-md p-4">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 text-sm">POI Hilayat</h4>
                        <p class="text-xs text-gray-500 mt-1">Pemeriksaan Ruangan Dokter</p>
                    </div>
                </div>
                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full font-semibold">High</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">11 Apr 2025, 9:24 AM</p>
            <button class="w-full mt-3 bg-red-50 text-red-600 py-2 rounded-xl text-sm font-semibold">
                Belum Ditangani
            </button>
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
@include('mobile.partials.bottom-nav-security')

@push('scripts')
<script>
// Get greeting based on time
function getGreeting() {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
}

// Load and display user data
async function loadUserData() {
    try {
        // Fetch fresh user data from API
        const response = await API.get('/user');
        
        if (response.success) {
            const user = response.data;
            
            // Update localStorage with fresh data
            localStorage.setItem('user', JSON.stringify(user));
            
            // Update UI
            document.getElementById('userName').textContent = user.name;
            document.getElementById('userRole').textContent = user.role_display || 'Security';
            document.getElementById('greeting').textContent = getGreeting();
            
            // Set foto
            const userPhoto = document.getElementById('userPhoto');
            if (user.foto) {
                console.log('User foto URL:', user.foto);
                userPhoto.src = user.foto;
                userPhoto.onerror = function() {
                    console.log('Failed to load foto, using avatar');
                    // Fallback to avatar if image fails to load
                    this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0071CE&color=fff&size=56`;
                };
            } else {
                console.log('No foto, using avatar');
                // Default avatar with user initials
                userPhoto.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0071CE&color=fff&size=56`;
            }
        }
    } catch (error) {
        console.error('Failed to load user data:', error);
        
        // Fallback to localStorage data
        const user = API.getUser();
        if (user) {
            document.getElementById('userName').textContent = user.name;
            document.getElementById('userRole').textContent = user.role_display || 'Security';
            document.getElementById('greeting').textContent = getGreeting();
            
            const userPhoto = document.getElementById('userPhoto');
            if (user.foto) {
                userPhoto.src = user.foto;
            } else {
                userPhoto.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0071CE&color=fff&size=56`;
            }
        }
    }
}

// Check authentication
if (!API.isAuthenticated()) {
    window.location.href = '/login';
} else {
    // Load user data from API
    loadUserData();
}
</script>
@endpush
@endsection
