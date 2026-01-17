@extends('mobile.layouts.app')

@section('title', 'Home - Employee')

@section('content')
<div class="min-h-screen" style="background-color: #E6F1FA;">
    <!-- Header - Sticky -->
    <div class="sticky top-0 z-50" style="background-color: #0071CE;">
        <!-- User Info Only -->
        <div class="px-4 pt-2 pb-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center overflow-hidden border-2 border-white/30">
                        <img id="userPhoto" src="" alt="Profile" class="w-full h-full object-cover hidden">
                        <div id="userInitial" class="text-white font-semibold text-xl"></div>
                    </div>
                    <div>
                        <div class="text-white text-sm font-bold mb-1">Selamat Siang, <span id="userName">Edy</span>!</div>
                        <div class="text-white text-sm" id="userRole">Admin</div>
                    </div>
                </div>
                <button onclick="showNotifications()" class="relative">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Transition Area - Dark Blue Background -->
    <div style="background-color: #00559B; padding-top: 15px; position: relative;">
        <!-- Main Content - White Background Area (overlapping) -->
        <div class="content-fade" style="background-color: #E6F1FA; position: absolute; top: 120px; left: 0; right: 0; bottom: 0; z-index: 1; border-top-left-radius: 24px; border-top-right-radius: 24px;"></div>
        
        <!-- Shift Card - Floating Card with Shadow (on top) -->
        <div class="px-4 relative z-10">
            <div class="rounded-3xl shadow-lg p-3" style="background: linear-gradient(135deg, #0071CE 0%, #005bb5 100%);">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="text-white text-base font-semibold mb-0">Shift Pagi</div>
                        <div class="text-white text-lg font-bold mb-1" id="shiftTime">07:00 - 15:00</div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-white/90 text-sm font-medium" id="shiftStatus">Sedang Berlangsung</span>
                        </div>
                    </div>
                    <div class="text-center bg-white/20 rounded-2xl px-3 py-2 backdrop-blur-sm border border-white/30">
                        <div class="text-white text-xs font-medium" id="currentMonth">DEC</div>
                        <div class="text-white text-2xl font-bold" id="currentDate">24</div>
                    </div>
                </div>
                
                <!-- Absensi Status -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <div class="text-white text-xs mb-1">Absensi Masuk Hari Ini</div>
                        <div class="bg-transparent backdrop-blur-sm rounded-2xl px-3 py-1 text-center border-2 border-white/60">
                            <span class="text-white text-sm font-bold" id="absensiMasuk">--:--</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-white text-xs mb-1">Absensi Pulang Hari Ini</div>
                        <div class="bg-transparent backdrop-blur-sm rounded-2xl px-3 py-1 text-center border-2 border-white/60">
                            <span class="text-white text-sm font-bold" id="absensiPulang">--:--</span>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance Button -->
                <button onclick="navigateToAbsensi()" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-2xl transition-colors" id="absensiButton">
                    <span id="absensiButtonText">Absen Masuk</span>
                </button>
            </div>
        </div>
        
        <!-- Content inside white background -->
        <div style="position: relative; z-index: 2; padding-top: 1px;">
            <!-- Services Menu -->
            <div class="px-4 mb-2">
                <h3 class="text-gray-800 font-semibold text-lg mb-2">Layanan</h3>
                <div class="grid grid-cols-4 gap-1">
                    <button onclick="navigateToAbsensi()" class="flex flex-col items-center group">
                        <div class="aspect-square w-full bg-white rounded-2xl p-3 flex flex-col items-center justify-center shadow-sm border border-gray-100 group-active:scale-95 transition-transform">
                            <svg class="w-8 h-8 text-blue-600 mb-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <span class="text-xs text-gray-700 font-medium">Absensi</span>
                        </div>
                    </button>
                    
                    <button onclick="showComingSoon()" class="flex flex-col items-center group">
                        <div class="aspect-square w-full bg-white rounded-2xl p-3 flex flex-col items-center justify-center shadow-sm border border-gray-100 group-active:scale-95 transition-transform">
                            <svg class="w-8 h-8 text-gray-600 mb-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                            </svg>
                            <span class="text-xs text-gray-700 font-medium">Atensi</span>
                        </div>
                    </button>
                    
                    <button onclick="showComingSoon()" class="flex flex-col items-center group">
                        <div class="aspect-square w-full bg-white rounded-2xl p-3 flex flex-col items-center justify-center shadow-sm border border-gray-100 group-active:scale-95 transition-transform">
                            <svg class="w-8 h-8 text-blue-600 mb-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="text-xs text-gray-700 font-medium">HSSE</span>
                        </div>
                    </button>
                    
                    <button onclick="showComingSoon()" class="flex flex-col items-center group">
                        <div class="aspect-square w-full bg-white rounded-2xl p-3 flex flex-col items-center justify-center shadow-sm border border-gray-100 group-active:scale-95 transition-transform">
                            <svg class="w-8 h-8 text-gray-600 mb-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            <span class="text-xs text-gray-700 font-medium">Izin</span>
                        </div>
                    </button>
                </div>
            </div>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Izin</span>
                        </div>
                    </button>
                </div>
            </div>
            
            <!-- Absensi Section (from profile) -->
            <div class="px-4 mb-8">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-800">Riwayat Absensi</h3>
                    <a href="/employee/absensi-schedule" class="text-sm font-semibold" style="color: #0071CE;">Lihat Semua</a>
                </div>
                
                <div class="flex space-x-3 overflow-x-auto pb-2">
                    <!-- Hadir -->
                    <div onclick="showAbsensiDetail('Hadir', 'H', document.getElementById('absenHadir').textContent, '10 Jam 10 Menit')" class="flex-shrink-0 w-32 bg-green-50 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-green-800 mb-1" id="absenHadir">0</p>
                        <p class="text-xs text-green-800 font-bold">H</p>
                    </div>
                    
                    <!-- Terlambat -->
                    <div onclick="showAbsensiDetail('Terlambat', 'T', document.getElementById('absenTerlambat').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-red-900 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                        <div class="w-10 h-10 bg-red-700 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-white mb-1" id="absenTerlambat">0</p>
                        <p class="text-xs text-white font-bold">T</p>
                    </div>
                    
                    <!-- Pulang Cepat -->
                    <div onclick="showAbsensiDetail('Pulang Cepat', 'PC', document.getElementById('absenPulangCepat').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-yellow-50 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                        <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.9l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-yellow-800 mb-1" id="absenPulangCepat">0</p>
                        <p class="text-xs text-yellow-800 font-bold">PC</p>
                    </div>
                    
                    <!-- Terlambat & Pulang Cepat -->
                    <div onclick="showAbsensiDetail('Terlambat & Pulang Cepat', 'TPC', document.getElementById('absenTPC').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-gray-900 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-white mb-1" id="absenTPC">0</p>
                        <p class="text-xs text-white font-bold">TPC</p>
                    </div>
                    
                    <!-- Alpa -->
                    <div onclick="showAbsensiDetail('Alpa', 'A', document.getElementById('absenAlpa').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-red-50 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-red-800 mb-1" id="absenAlpa">0</p>
                        <p class="text-xs text-red-800 font-bold">A</p>
                    </div>
                </div>
            </div>
            
            <!-- Attendance Summary -->
            <div class="px-4 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-gray-800 font-semibold text-lg">Riwayat Absensi</h3>
                    <button onclick="navigateToAbsensi()" class="text-gray-400 text-sm font-medium">View All</button>
                </div>
                
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center">
                            <div class="bg-green-50 rounded-2xl p-4 mb-3">
                                <div class="text-green-600 text-xs font-medium mb-2">Hadir</div>
                                <div class="text-gray-800 font-bold text-2xl mb-1" id="hadirCount">10</div>
                                <div class="text-gray-400 text-xs" id="hadirHours">80 Jam 10 Menit</div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-yellow-50 rounded-2xl p-4 mb-3">
                                <div class="text-yellow-600 text-xs font-medium mb-2">Keluar Awal</div>
                                <div class="text-gray-800 font-bold text-2xl mb-1" id="keluarAwalCount">0</div>
                                <div class="text-gray-400 text-xs" id="keluarAwalHours">0 Jam 0 Menit</div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-red-50 rounded-2xl p-4 mb-3">
                                <div class="text-red-600 text-xs font-medium mb-2">Terlambat</div>
                                <div class="text-gray-800 font-bold text-2xl mb-1" id="terlambatCount">0</div>
                                <div class="text-gray-400 text-xs" id="terlambatHours">0 Jam 0 Menit</div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-gray-50 rounded-2xl p-4 mb-3">
                                <div class="text-gray-600 text-xs font-medium mb-2">Cuti</div>
                                <div class="text-gray-800 font-bold text-2xl mb-1" id="cutiCount">1</div>
                                <div class="text-gray-400 text-xs" id="cutiHours">Tersisa 11 Hari</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- Tasks/Assignments -->
        <div class="px-4 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-gray-800 font-semibold text-lg">Tugas</h3>
                <button onclick="showComingSoon()" class="text-gray-600 text-sm font-medium">View All</button>
            </div>
            
            <!-- Task Tabs -->
            <div class="flex space-x-1 mb-6 bg-white backdrop-blur-sm rounded-xl p-1 border border-gray-200">
                <button class="flex-1 py-3 px-4 text-sm font-medium text-white bg-blue-500 rounded-lg shadow-sm">Tugas</button>
                <button class="flex-1 py-3 px-4 text-sm font-medium text-gray-600">Proses</button>
                <button class="flex-1 py-3 px-4 text-sm font-medium text-gray-600">Tinjau</button>
                <button class="flex-1 py-3 px-4 text-sm font-medium text-gray-600">Selesai</button>
            </div>
            
            <!-- Task Item -->
            <div class="bg-white backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-red-400/30">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-800">PM Hidayat</div>
                            <div class="text-xs text-gray-600">11 Apr 2025, 9:24 AM</div>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-red-500/20 text-red-700 text-xs font-medium rounded-full border border-red-400/30">High</span>
                </div>
                <div class="text-sm text-gray-800 font-medium mb-2">Pembersihan Ruangan Dokter</div>
                <div class="text-xs text-gray-600">Belum Dikerjakan</div>
            </div>
        </div>
        
        <!-- Bottom spacing for navigation -->
        <div class="h-20"></div>
    </div>
</div>

<!-- Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 safe-area-pb shadow-lg z-50">
    <div class="flex justify-around">
        <button class="flex flex-col items-center py-2 text-blue-600 group">
            <div class="w-8 h-8 mb-1 flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </div>
            <span class="text-xs font-medium">Beranda</span>
        </button>
        
        <button onclick="showComingSoon()" class="flex flex-col items-center py-2 text-gray-400 group">
            <div class="w-8 h-8 mb-1 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <span class="text-xs font-medium">Tugas</span>
        </button>
        
        <button onclick="navigateToAbsensi()" class="flex flex-col items-center py-2 text-gray-400 group">
            <div class="w-8 h-8 mb-1 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="text-xs font-medium">Absen</span>
        </button>
        
        <button onclick="showComingSoon()" class="flex flex-col items-center py-2 text-gray-400 group">
            <div class="w-8 h-8 mb-1 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <span class="text-xs font-medium">Claim</span>
        </button>
        
        <button onclick="navigateToProfile()" class="flex flex-col items-center py-2 text-gray-400 group">
            <div class="w-8 h-8 mb-1 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="text-xs font-medium">Profil</span>
        </button>
    </div>
</div>

@push('styles')
<style>
.safe-area-pb {
    padding-bottom: env(safe-area-inset-bottom);
}

/* Add gradient fade effect above bottom navigation */
.content-fade {
    position: relative;
}

.content-fade::after {
    content: '';
    position: fixed;
    bottom: 80px;
    left: 0;
    right: 0;
    height: 20px;
    background: linear-gradient(to bottom, transparent, rgba(230, 241, 250, 0.8));
    pointer-events: none;
    z-index: 40;
}
</style>
@endpush

@push('scripts')
<script>
let userData = null;
let todayShift = null;
let absensiSummary = null;

// Load data when page loads
document.addEventListener('DOMContentLoaded', async function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    await loadUserData();
    await loadTodayShift();
    await loadAbsensiSummary();
    await loadTodayAttendanceStatus();
    updateCurrentDate();
});

// Load user data
async function loadUserData() {
    try {
        // Force fresh data from server (bypass cache)
        const response = await fetch(`${API_BASE_URL}/user?_t=${Date.now()}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${API.getToken()}`,
                'Accept': 'application/json',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            userData = result.data;
            updateUserInfo();
        }
    } catch (error) {
        console.error('Error loading user data:', error);
    }
}

// Load today's shift
async function loadTodayShift() {
    try {
        const response = await API.get('/shift/today');
        if (response.success && response.data) {
            todayShift = response.data;
            updateShiftInfo();
        } else {
            // No shift today
            document.getElementById('shiftTime').textContent = 'Tidak ada shift';
            document.getElementById('shiftStatus').textContent = 'Libur';
        }
    } catch (error) {
        console.error('Error loading shift data:', error);
    }
}

// Load absensi summary
async function loadAbsensiSummary() {
    try {
        const response = await API.get('/absensi/summary');
        if (response.success) {
            absensiSummary = response.data;
            updateAbsensiSummary();
        }
    } catch (error) {
        console.error('Error loading absensi summary:', error);
    }
}

// Load today's attendance status
async function loadTodayAttendanceStatus() {
    try {
        const response = await API.get('/absensi/today-status');
        if (response.success) {
            updateAttendanceButton(response.data);
            updateAttendanceDisplay(response.data);
        }
    } catch (error) {
        console.error('Error loading attendance status:', error);
    }
}

// Update attendance button based on status
function updateAttendanceButton(status) {
    const button = document.getElementById('absensiButton');
    const buttonText = document.getElementById('absensiButtonText');
    
    if (status.can_check_in) {
        button.className = 'w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-2xl transition-colors';
        buttonText.textContent = 'Absen Masuk';
    } else if (status.can_check_out) {
        button.className = 'w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-2xl transition-colors';
        buttonText.textContent = 'Absen Keluar';
    } else {
        button.className = 'w-full bg-gray-400 text-white font-semibold py-3 px-4 rounded-2xl cursor-not-allowed';
        buttonText.textContent = 'Absensi Selesai';
        button.onclick = null;
    }
}

// Update attendance display
function updateAttendanceDisplay(status) {
    const absensiMasuk = document.getElementById('absensiMasuk');
    const absensiPulang = document.getElementById('absensiPulang');
    
    if (status.kehadiran) {
        absensiMasuk.textContent = status.kehadiran.jam_masuk || '--:--';
        absensiPulang.textContent = status.kehadiran.jam_keluar || '--:--';
    } else {
        absensiMasuk.textContent = '--:--';
        absensiPulang.textContent = '--:--';
    }
}

// Update user info in header
function updateUserInfo() {
    if (!userData) return;
    
    document.getElementById('userName').textContent = userData.name;
    
    // Gunakan jabatan_name dari API, fallback ke role_display jika tidak ada
    const jabatanName = userData.jabatan_name || userData.role_display || 'Employee';
    document.getElementById('userRole').textContent = jabatanName;
    
    // Update photo
    if (userData.foto) {
        const photoImg = document.getElementById('userPhoto');
        photoImg.src = userData.foto;
        photoImg.classList.remove('hidden');
        document.getElementById('userInitial').style.display = 'none';
    } else {
        // Show initial
        const initial = userData.name.charAt(0).toUpperCase();
        document.getElementById('userInitial').textContent = initial;
    }
}

// Update shift info
function updateShiftInfo() {
    if (!todayShift || !todayShift.shift) return;
    
    const shift = todayShift.shift;
    
    // Format waktu tanpa detik (hanya jam:menit)
    const jamMulai = shift.jam_mulai.substring(0, 5); // HH:MM
    const jamSelesai = shift.jam_selesai.substring(0, 5); // HH:MM
    const jamFormatted = `${jamMulai} - ${jamSelesai}`;
    
    document.getElementById('shiftTime').textContent = jamFormatted;
    
    // Determine shift status based on current time
    const now = new Date();
    const currentTime = now.getHours() * 60 + now.getMinutes();
    const [startHour, startMin] = shift.jam_mulai.split(':').map(Number);
    const [endHour, endMin] = shift.jam_selesai.split(':').map(Number);
    const shiftStart = startHour * 60 + startMin;
    const shiftEnd = endHour * 60 + endMin;
    
    let status = 'Belum Dimulai';
    let statusColor = 'bg-gray-400';
    
    if (currentTime >= shiftStart && currentTime <= shiftEnd) {
        status = 'Sedang Berlangsung';
        statusColor = 'bg-green-400';
    } else if (currentTime > shiftEnd) {
        status = 'Selesai';
        statusColor = 'bg-blue-400';
    }
    
    document.getElementById('shiftStatus').textContent = status;
    const statusDot = document.querySelector('.w-3.h-3.rounded-full');
    statusDot.className = `w-3 h-3 rounded-full animate-pulse ${statusColor}`;
}

// Update absensi summary
function updateAbsensiSummary() {
    if (!absensiSummary) return;
    
    const summary = absensiSummary.summary;
    const jamKerja = absensiSummary.total_jam_kerja;
    
    // Update counts for Riwayat Absensi section
    document.getElementById('hadirCount').textContent = summary.H || 0;
    document.getElementById('keluarAwalCount').textContent = summary.PC || 0;
    document.getElementById('terlambatCount').textContent = summary.T || 0;
    document.getElementById('cutiCount').textContent = summary.A || 0;
    
    // Update hours for Riwayat Absensi section
    document.getElementById('hadirHours').textContent = jamKerja.H || '0 Jam 0 Menit';
    document.getElementById('keluarAwalHours').textContent = jamKerja.PC || '0 Jam 0 Menit';
    document.getElementById('terlambatHours').textContent = jamKerja.T || '0 Jam 0 Menit';
    document.getElementById('cutiHours').textContent = 'Tersisa 11 Hari'; // Static for now
    
    // Update counts for Absensi section (from profile)
    document.getElementById('absenHadir').textContent = summary.H || 0;
    document.getElementById('absenTerlambat').textContent = summary.T || 0;
    document.getElementById('absenPulangCepat').textContent = summary.PC || 0;
    document.getElementById('absenTPC').textContent = summary.TPC || 0;
    document.getElementById('absenAlpa').textContent = summary.A || 0;
    
    // Store total jam kerja for popup
    window.absensiJamKerja = jamKerja;
}

// Show absensi detail popup (from profile)
function showAbsensiDetail(nama, singkatan, jumlah, totalJam) {
    // Get total jam kerja from API data if available
    if (window.absensiJamKerja && window.absensiJamKerja[singkatan]) {
        totalJam = window.absensiJamKerja[singkatan];
    }
    
    Swal.fire({
        title: `<strong>${nama}</strong>`,
        html: `
            <div class="text-left">
                <p class="text-gray-600 mb-2">Singkatan: <strong>${singkatan}</strong></p>
                <p class="text-gray-600 mb-2">Jumlah: <strong>${jumlah} hari</strong></p>
                <p class="text-gray-600">Total Jam Kerja: <strong>${totalJam}</strong></p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#0071CE'
    });
}

// Update current date
function updateCurrentDate() {
    const now = new Date();
    const day = now.getDate();
    const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 
                   'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
    const month = months[now.getMonth()];
    
    document.getElementById('currentDate').textContent = day;
    document.getElementById('currentMonth').textContent = month;
}

// Navigation functions
function navigateToAbsensi() {
    window.location.href = '/employee/absensi';
}

function navigateToProfile() {
    window.location.href = '/profile';
}

function showNotifications() {
    Swal.fire({
        title: 'Notifikasi',
        text: 'Tidak ada notifikasi baru',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function showComingSoon() {
    Swal.fire({
        title: 'Coming Soon',
        text: 'Fitur ini akan segera tersedia',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

// Auto refresh data every 5 minutes
setInterval(async () => {
    await loadTodayShift();
    await loadTodayAttendanceStatus();
    updateCurrentDate();
}, 5 * 60 * 1000);
</script>
@endpush
@endsection