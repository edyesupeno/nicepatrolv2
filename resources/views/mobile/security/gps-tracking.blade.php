@extends('mobile.layouts.app')

@section('title', 'GPS Tracking - Nice Patrol')

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center">
                <button onclick="history.back()" class="mr-3 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <h1 class="text-lg font-semibold text-gray-800">Tracking Checkpoint</h1>
            </div>
        </div>
    </div>

    <!-- Patrol Info -->
    <div class="p-4">
        <div id="patrol-info" class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <!-- Loading -->
            <div id="info-loading" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <p class="text-gray-500 text-sm">Memuat informasi patrol...</p>
            </div>
            
            <!-- Patrol Info Content -->
            <div id="info-content" class="hidden">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-800" id="patrol-title">Patrol Info</h3>
                        <p class="text-sm text-gray-500" id="patrol-subtitle">-</p>
                    </div>
                    <span id="patrol-status-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                        -
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <p class="text-lg font-bold text-blue-600" id="total-locations">0</p>
                        <p class="text-xs text-gray-500">Lokasi GPS</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-green-600" id="patrol-duration">-</p>
                        <p class="text-xs text-gray-500">Durasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- GPS Locations List -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Lokasi GPS Tracking</h3>
                    <button onclick="openAllInMaps()" class="text-blue-600 text-sm font-medium">
                        Buka di Maps
                    </button>
                </div>
            </div>
            
            <div id="gps-locations">
                <!-- Loading -->
                <div id="locations-loading" class="p-4 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                    <p class="text-gray-500 text-sm">Memuat lokasi GPS...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('mobile.partials.bottom-nav-security')
@endsection

@push('scripts')
<script>
let patrolData = null;
let gpsLocations = [];

// Get patrol ID from URL
function getPatrolId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('patrol_id');
}

// Load GPS tracking data
async function loadGpsTracking() {
    const patrolId = getPatrolId();
    
    if (!patrolId) {
        showError('ID Patrol tidak ditemukan');
        return;
    }
    
    try {
        const response = await API.get(`/patrolis/${patrolId}/gps-locations`);
        
        if (response.success) {
            patrolData = response.data.patroli;
            gpsLocations = response.data.locations;
            
            displayPatrolInfo();
            displayGpsLocations();
        } else {
            showError('Gagal memuat data GPS tracking');
        }
    } catch (error) {
        console.error('Error loading GPS tracking:', error);
        showError('Terjadi kesalahan saat memuat data');
    }
}

// Display patrol info
function displayPatrolInfo() {
    document.getElementById('info-loading').classList.add('hidden');
    document.getElementById('info-content').classList.remove('hidden');
    
    document.getElementById('patrol-title').textContent = `Patrol - ${gpsLocations.length} Checkpoint`;
    document.getElementById('patrol-subtitle').textContent = `Petugas: ${patrolData.user} | Rute: ${patrolData.lokasi}`;
    
    // Status badge
    const statusBadge = document.getElementById('patrol-status-badge');
    const status = getPatrolStatus(patrolData.status);
    statusBadge.textContent = status.text;
    statusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${status.class}`;
    
    // Statistics
    document.getElementById('total-locations').textContent = gpsLocations.length;
    
    // Duration
    if (patrolData.waktu_mulai) {
        const start = new Date(patrolData.waktu_mulai);
        const now = new Date();
        const diff = now - start;
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        document.getElementById('patrol-duration').textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }
}

// Display GPS locations
function displayGpsLocations() {
    const container = document.getElementById('gps-locations');
    const loading = document.getElementById('locations-loading');
    
    loading.classList.add('hidden');
    
    if (gpsLocations.length === 0) {
        container.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <p class="text-gray-500">Belum ada data lokasi GPS</p>
            </div>
        `;
        return;
    }
    
    const locationItems = gpsLocations.map((location, index) => {
        const status = getCheckpointStatus(location.status);
        
        return `
            <div class="p-4 border-b border-gray-100 last:border-b-0">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mr-2">
                                ${index + 1}
                            </span>
                            <h4 class="font-medium text-gray-800">${location.checkpoint}</h4>
                        </div>
                        <p class="text-sm text-gray-500 ml-8">Scan: ${formatTime(location.waktu_scan)}</p>
                        ${location.catatan ? `<p class="text-xs text-gray-600 ml-8 mt-1">${location.catatan}</p>` : ''}
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${status.class}">
                        ${status.text}
                    </span>
                </div>
                
                <!-- GPS Coordinates -->
                <div class="ml-8 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-blue-800">Koordinat GPS</p>
                            <p class="text-sm text-blue-600 font-mono">${location.latitude}, ${location.longitude}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="copyCoordinates('${location.latitude}, ${location.longitude}')" 
                                    class="text-blue-600 hover:text-blue-800" title="Copy koordinat">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                                </svg>
                            </button>
                            <a href="${location.google_maps_url}" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800" title="Buka di Google Maps">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    container.innerHTML = locationItems;
}

// Copy coordinates to clipboard
async function copyCoordinates(coordinates) {
    try {
        await navigator.clipboard.writeText(coordinates);
        
        Swal.fire({
            title: 'Berhasil!',
            text: 'Koordinat berhasil disalin',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    } catch (error) {
        console.error('Failed to copy coordinates:', error);
        
        Swal.fire({
            title: 'Gagal',
            text: 'Gagal menyalin koordinat',
            icon: 'error',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

// Open all locations in Google Maps
function openAllInMaps() {
    if (gpsLocations.length === 0) {
        Swal.fire('Info', 'Tidak ada lokasi GPS untuk ditampilkan', 'info');
        return;
    }
    
    // Create waypoints for Google Maps
    const waypoints = gpsLocations.map(location => `${location.latitude},${location.longitude}`).join('|');
    const mapsUrl = `https://maps.google.com/maps/dir/${waypoints}`;
    
    window.open(mapsUrl, '_blank');
}

// Get patrol status
function getPatrolStatus(status) {
    switch (status) {
        case 'berlangsung':
            return { text: 'Berlangsung', class: 'bg-yellow-100 text-yellow-800' };
        case 'selesai':
            return { text: 'Selesai', class: 'bg-green-100 text-green-800' };
        case 'dibatalkan':
            return { text: 'Dibatalkan', class: 'bg-red-100 text-red-800' };
        default:
            return { text: 'Unknown', class: 'bg-gray-100 text-gray-800' };
    }
}

// Get checkpoint status
function getCheckpointStatus(status) {
    switch (status) {
        case 'normal':
            return { text: 'Normal', class: 'bg-green-100 text-green-800' };
        case 'bermasalah':
            return { text: 'Bermasalah', class: 'bg-red-100 text-red-800' };
        default:
            return { text: 'Unknown', class: 'bg-gray-100 text-gray-800' };
    }
}

// Format time
function formatTime(timeString) {
    if (!timeString) return '-';
    
    const date = new Date(timeString);
    return date.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
}

// Show error
function showError(message) {
    document.getElementById('info-loading').classList.add('hidden');
    document.getElementById('locations-loading').classList.add('hidden');
    
    Swal.fire('Error', message, 'error');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    loadGpsTracking();
});
</script>
@endpush