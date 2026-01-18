@extends('mobile.layouts.app')

@section('title', 'Patrol - Nice Patrol')

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <h1 class="text-lg font-semibold text-gray-800">Patrol</h1>
            </div>
        </div>
    </div>

    <!-- Current Patrol Status -->
    <div class="p-4">
        <div id="current-patrol" class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <!-- Loading -->
            <div id="patrol-loading" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <p class="text-gray-500 text-sm">Memuat status patrol...</p>
            </div>
            
            <!-- No Active Patrol -->
            <div id="no-patrol" class="text-center py-6 hidden">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Patrol Aktif</h3>
                <p class="text-gray-500 text-sm mb-4">Mulai patrol dengan scan QR code checkpoint</p>
                <button onclick="startPatrol()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Mulai Patrol
                </button>
            </div>
            
            <!-- Active Patrol -->
            <div id="active-patrol" class="hidden">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Patrol Aktif</h3>
                        <p id="patrol-location" class="text-sm text-gray-500">-</p>
                    </div>
                    <span id="patrol-status" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Berlangsung
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600" id="patrol-duration">00:00</p>
                        <p class="text-xs text-gray-500">Durasi</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600" id="checkpoints-scanned">0</p>
                        <p class="text-xs text-gray-500">Checkpoint</p>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button onclick="scanCheckpoint()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Scan Checkpoint
                    </button>
                    <button onclick="endPatrol()" class="px-4 py-3 border border-red-300 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition">
                        Selesai
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Checkpoints -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Checkpoint Terbaru</h3>
            </div>
            
            <div id="recent-checkpoints">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

@include('mobile.partials.bottom-nav-security')
@endsection

@push('scripts')
<script>
let currentPatrol = null;
let patrolTimer = null;

// Load current patrol status
async function loadPatrolStatus() {
    try {
        // Get current patrol from localStorage first
        const storedPatrol = localStorage.getItem('current_patroli');
        if (storedPatrol) {
            currentPatrol = JSON.parse(storedPatrol);
        }
        
        // Fetch latest patrol data from API
        const response = await API.get('/patrolis');
        
        if (response.success && response.data.data.length > 0) {
            // Find active patrol
            const activePatrol = response.data.data.find(p => p.status === 'berlangsung');
            
            if (activePatrol) {
                currentPatrol = activePatrol;
                localStorage.setItem('current_patroli', JSON.stringify(activePatrol));
                showActivePatrol(activePatrol);
                loadRecentCheckpoints(activePatrol.id);
            } else {
                showNoPatrol();
            }
        } else {
            showNoPatrol();
        }
    } catch (error) {
        console.error('Error loading patrol status:', error);
        
        // Fallback to stored patrol
        if (currentPatrol) {
            showActivePatrol(currentPatrol);
        } else {
            showNoPatrol();
        }
    } finally {
        document.getElementById('patrol-loading').classList.add('hidden');
    }
}

// Show active patrol
function showActivePatrol(patrol) {
    document.getElementById('no-patrol').classList.add('hidden');
    document.getElementById('active-patrol').classList.remove('hidden');
    
    // Get rute name from first checkpoint detail
    const ruteName = patrol.details && patrol.details.length > 0 && patrol.details[0].checkpoint?.rute_patrol ? 
        patrol.details[0].checkpoint.rute_patrol.nama : 
        patrol.lokasi?.nama || 'Rute Patrol';
    
    // Update patrol info
    document.getElementById('patrol-location').textContent = ruteName;
    
    // Start timer
    startPatrolTimer(patrol.waktu_mulai);
    
    // Update checkpoints count
    const checkpointsCount = patrol.details ? patrol.details.length : 0;
    document.getElementById('checkpoints-scanned').textContent = checkpointsCount;
}

// Show no patrol state
function showNoPatrol() {
    document.getElementById('active-patrol').classList.add('hidden');
    document.getElementById('no-patrol').classList.remove('hidden');
    
    // Clear timer
    if (patrolTimer) {
        clearInterval(patrolTimer);
        patrolTimer = null;
    }
}

// Start patrol timer
function startPatrolTimer(startTime) {
    if (patrolTimer) {
        clearInterval(patrolTimer);
    }
    
    const start = new Date(startTime);
    
    patrolTimer = setInterval(() => {
        const now = new Date();
        const diff = now - start;
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        const duration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        document.getElementById('patrol-duration').textContent = duration;
    }, 1000);
}

// Load recent checkpoints
async function loadRecentCheckpoints(patrolId) {
    try {
        const response = await API.get(`/patrolis/${patrolId}`);
        
        if (response.success && response.data.details) {
            displayRecentCheckpoints(response.data.details);
        }
    } catch (error) {
        console.error('Error loading checkpoints:', error);
    }
}

// Display recent checkpoints
function displayRecentCheckpoints(checkpoints) {
    const container = document.getElementById('recent-checkpoints');
    
    if (!checkpoints || checkpoints.length === 0) {
        container.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <p class="text-gray-500 text-sm">Belum ada checkpoint yang di-scan</p>
            </div>
        `;
        return;
    }
    
    const checkpointItems = checkpoints.slice(0, 5).map(checkpoint => {
        const status = getCheckpointStatus(checkpoint.status);
        
        return `
            <div class="p-4 border-b border-gray-100 last:border-b-0">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">${cleanCheckpointName(checkpoint.checkpoint?.nama || 'Checkpoint')}</h4>
                        <p class="text-sm text-gray-500">Scan: ${formatTime(checkpoint.waktu_scan)}</p>
                        ${checkpoint.catatan ? `<p class="text-xs text-gray-600 mt-1">${checkpoint.catatan}</p>` : ''}
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${status.class}">
                        ${status.text}
                    </span>
                </div>
                
                <!-- GPS Location -->
                ${checkpoint.latitude && checkpoint.longitude ? `
                    <div class="mt-2 p-2 bg-blue-50 rounded border-l-4 border-blue-400">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-blue-800">Lokasi GPS</p>
                                <p class="text-xs text-blue-600">${checkpoint.latitude}, ${checkpoint.longitude}</p>
                            </div>
                            <a href="https://maps.google.com/?q=${checkpoint.latitude},${checkpoint.longitude}" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
    
    container.innerHTML = checkpointItems;
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

// Get checkpoint status
function getCheckpointStatus(status) {
    switch (status) {
        case 'normal':
            return { text: 'Normal', class: 'bg-green-100 text-green-800' };
        case 'bermasalah':
            return { text: 'Ada Masalah', class: 'bg-red-100 text-red-800' };
        default:
            return { text: 'Pending', class: 'bg-yellow-100 text-yellow-800' };
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

// Start patrol
function startPatrol() {
    window.location.href = '/security/scan';
}

// Scan checkpoint
function scanCheckpoint() {
    window.location.href = '/security/scan';
}

// End patrol
async function endPatrol() {
    if (!currentPatrol) return;
    
    const result = await Swal.fire({
        title: 'Selesai Patrol?',
        text: 'Apakah Anda yakin ingin menyelesaikan patrol ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Selesai',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await API.put(`/patrolis/${currentPatrol.id}`, {
                status: 'selesai',
                waktu_selesai: new Date().toISOString()
            });
            
            if (response.success) {
                localStorage.removeItem('current_patroli');
                currentPatrol = null;
                
                Swal.fire({
                    title: 'Patrol Selesai!',
                    text: 'Terima kasih telah menyelesaikan patrol',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    loadPatrolStatus();
                });
            } else {
                throw new Error(response.message || 'Gagal menyelesaikan patrol');
            }
        } catch (error) {
            console.error('Error ending patrol:', error);
            Swal.fire('Error', 'Gagal menyelesaikan patrol: ' + error.message, 'error');
        }
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    loadPatrolStatus();
    
    // Refresh data every 30 seconds
    setInterval(() => {
        if (currentPatrol) {
            loadRecentCheckpoints(currentPatrol.id);
        }
    }, 30000);
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (patrolTimer) {
        clearInterval(patrolTimer);
    }
});
</script>
@endpush