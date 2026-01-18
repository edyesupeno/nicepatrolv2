@extends('mobile.layouts.app')

@section('title', 'Aktivitas - Nice Patrol')

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                </svg>
                <h1 class="text-lg font-semibold text-gray-800">Aktivitas</h1>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white border-b border-gray-200">
        <div class="flex">
            <button onclick="switchTab('patrol')" id="tab-patrol" class="flex-1 py-3 px-4 text-center font-medium text-sm border-b-2 border-blue-600 text-blue-600">
                Patrol
            </button>
            <button onclick="switchTab('patroli-mandiri')" id="tab-patroli-mandiri" class="flex-1 py-3 px-4 text-center font-medium text-sm border-b-2 border-transparent text-gray-500">
                Patroli Mandiri
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <!-- Tab Patrol -->
        <div id="content-patrol" class="tab-content">
            <!-- Stats Cards -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold text-gray-800">Aktivitas Hari Ini</h2>
                    <button class="text-blue-600 text-sm font-medium">Ganti Tanggal</button>
                </div>
                
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <!-- Area Stats -->
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-blue-600 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            <span id="area-count" class="text-lg font-bold text-blue-600">0</span>
                        </div>
                        <p class="text-xs text-blue-600 font-medium">Checkpoint</p>
                    </div>

                    <!-- Rounds Stats -->
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-green-600 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span id="rounds-count" class="text-lg font-bold text-green-600">0</span>
                        </div>
                        <p class="text-xs text-green-600 font-medium">Rounds</p>
                    </div>

                    <!-- Assets Stats -->
                    <div class="bg-orange-50 rounded-xl p-3 text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-orange-600 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-2 .89-2 2v11c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                            </svg>
                            <span id="assets-count" class="text-lg font-bold text-orange-600">0</span>
                        </div>
                        <p class="text-xs text-orange-600 font-medium">Asets</p>
                    </div>
                </div>
            </div>

            <!-- Detail Patrol -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Detail Patrol</h3>
                </div>
                
                <div id="patrol-list" class="divide-y divide-gray-100">
                    <!-- Loading -->
                    <div id="patrol-loading" class="p-4 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Memuat data patrol...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Patroli Mandiri -->
        <div id="content-patroli-mandiri" class="tab-content hidden">
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Patroli Mandiri</h3>
                <p class="text-gray-500 text-sm mb-4">Fitur ini akan segera tersedia</p>
            </div>
        </div>
    </div>
</div>

@include('mobile.partials.bottom-nav-security')
@endsection

@push('scripts')
<script>
let currentTab = 'patrol';

// Switch tabs
function switchTab(tab) {
    currentTab = tab;
    
    // Update tab buttons
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(`tab-${tab}`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`tab-${tab}`).classList.add('border-blue-600', 'text-blue-600');
    
    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.getElementById(`content-${tab}`).classList.remove('hidden');
    
    // Load data for active tab
    if (tab === 'patrol') {
        loadPatrolData();
    }
}

// Load patrol data
async function loadPatrolData() {
    try {
        const response = await API.get('/patrolis');
        
        if (response.success) {
            displayPatrolData(response.data);
        } else {
            showError('Gagal memuat data patrol');
        }
    } catch (error) {
        console.error('Error loading patrol data:', error);
        showError('Terjadi kesalahan saat memuat data');
    }
}

// Display patrol data
function displayPatrolData(data) {
    const patrolList = document.getElementById('patrol-list');
    const loading = document.getElementById('patrol-loading');
    
    loading.style.display = 'none';
    
    if (!data.data || data.data.length === 0) {
        patrolList.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <p class="text-gray-500">Belum ada data patrol hari ini</p>
            </div>
        `;
        return;
    }
    
    // Update stats
    updateStats(data.data);
    
    // Display patrol list
    const patrolItems = data.data.map(patrol => {
        const status = getPatrolStatus(patrol.status);
        const checkpoints = patrol.details || [];
        
        // Get rute name from first checkpoint
        const ruteName = checkpoints.length > 0 && checkpoints[0].checkpoint?.rute_patrol ? 
            checkpoints[0].checkpoint.rute_patrol.nama : 
            patrol.lokasi?.nama || 'Rute';
        
        return `
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${status.class}">
                            ${status.text}
                        </span>
                        <span class="ml-2 text-sm font-medium text-gray-800">${ruteName}</span>
                    </div>
                    <button onclick="togglePatrolDetail(${patrol.id})" class="text-gray-400">
                        <svg class="w-5 h-5 transform transition-transform" id="arrow-${patrol.id}" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                        </svg>
                    </button>
                </div>
                
                <div class="text-xs text-gray-500 mb-2">
                    <span>Mulai: ${formatTime(patrol.waktu_mulai)}</span>
                    ${patrol.waktu_selesai ? `<span class="ml-3">Selesai: ${formatTime(patrol.waktu_selesai)}</span>` : ''}
                    ${checkpoints.length > 0 ? `<a href="/security/gps-tracking?patrol_id=${patrol.id}" class="ml-3 text-blue-600 hover:text-blue-800 font-medium">Lihat GPS</a>` : ''}
                </div>
                
                <!-- Checkpoint Details (Hidden by default) -->
                <div id="detail-${patrol.id}" class="hidden mt-3 space-y-2">
                    ${checkpoints.map(detail => `
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="font-medium text-sm text-gray-800">${cleanCheckpointName(detail.checkpoint?.nama || 'Checkpoint')}</p>
                                    <p class="text-xs text-gray-500">Scan: ${formatTime(detail.waktu_scan)}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getCheckpointStatus(detail.status).class}">
                                    ${getCheckpointStatus(detail.status).text}
                                </span>
                            </div>
                            
                            <!-- GPS Location Info -->
                            ${detail.latitude && detail.longitude ? `
                                <div class="mt-2 p-2 bg-blue-50 rounded border-l-4 border-blue-400">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-medium text-blue-800">Lokasi GPS</p>
                                            <p class="text-xs text-blue-600">${detail.latitude}, ${detail.longitude}</p>
                                        </div>
                                        <a href="https://maps.google.com/?q=${detail.latitude},${detail.longitude}" 
                                           target="_blank" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${detail.catatan ? `<p class="text-xs text-gray-600 mt-2">${detail.catatan}</p>` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }).join('');
    
    patrolList.innerHTML = patrolItems;
}

// Update statistics
function updateStats(patrols) {
    let totalCheckpoints = 0;
    let totalRounds = 0;
    let totalAssets = 0;
    
    patrols.forEach(patrol => {
        totalRounds += 1;
        if (patrol.details) {
            totalCheckpoints += patrol.details.length;
            // Estimate assets (you might want to get actual count from API)
            totalAssets += patrol.details.length * 2; // Rough estimate
        }
    });
    
    document.getElementById('area-count').textContent = totalCheckpoints;
    document.getElementById('rounds-count').textContent = totalRounds;
    document.getElementById('assets-count').textContent = totalAssets;
}

// Clean checkpoint name (remove route part)
function cleanCheckpointName(fullName) {
    // Remove common patterns like "- Rute ...", "- Route ...", etc.
    let cleanName = fullName
        .replace(/\s*-\s*[Rr]ute.*$/i, '')  // Remove "- Rute ..." part
        .replace(/\s*-\s*[Rr]oute.*$/i, '') // Remove "- Route ..." part
        .replace(/\s*-\s*pengecekan.*$/i, '') // Remove "- pengecekan ..." part
        .trim();
    
    // If the result is too short or empty, return the original
    if (cleanName.length < 3) {
        return fullName;
    }
    
    return cleanName;
}

// Toggle patrol detail
function togglePatrolDetail(patrolId) {
    const detail = document.getElementById(`detail-${patrolId}`);
    const arrow = document.getElementById(`arrow-${patrolId}`);
    
    if (detail.classList.contains('hidden')) {
        detail.classList.remove('hidden');
        arrow.classList.add('rotate-180');
    } else {
        detail.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

// Get patrol status
function getPatrolStatus(status) {
    switch (status) {
        case 'berlangsung':
            return { text: 'Sedang Cek', class: 'bg-yellow-100 text-yellow-800' };
        case 'selesai':
            return { text: 'Sudah Cek', class: 'bg-green-100 text-green-800' };
        case 'dibatalkan':
            return { text: 'Belum Cek', class: 'bg-red-100 text-red-800' };
        default:
            return { text: 'Belum Cek', class: 'bg-gray-100 text-gray-800' };
    }
}

// Get checkpoint status
function getCheckpointStatus(status) {
    switch (status) {
        case 'normal':
            return { text: 'Sudah Cek', class: 'bg-green-100 text-green-800' };
        case 'bermasalah':
            return { text: 'Belum Cek', class: 'bg-red-100 text-red-800' };
        default:
            return { text: 'Sedang Cek', class: 'bg-yellow-100 text-yellow-800' };
    }
}

// Format time
function formatTime(timeString) {
    if (!timeString) return '-';
    
    const date = new Date(timeString);
    return date.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

// Show error
function showError(message) {
    const patrolList = document.getElementById('patrol-list');
    const loading = document.getElementById('patrol-loading');
    
    loading.style.display = 'none';
    patrolList.innerHTML = `
        <div class="p-4 text-center">
            <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <p class="text-red-500">${message}</p>
        </div>
    `;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    loadPatrolData();
});
</script>
@endpush