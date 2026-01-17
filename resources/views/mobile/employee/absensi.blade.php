@extends('mobile.layouts.app')

@section('title', 'Absensi - Employee')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button onclick="history.back()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-800">Absensi</h1>
                        <p class="text-sm text-gray-600" id="currentDate">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-4 py-6">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" id="statusIcon">
                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2" id="statusTitle">Siap Absensi</h2>
                <p class="text-gray-600 mb-4" id="statusMessage">Pilih lokasi dan ambil foto untuk absensi</p>
                
                <!-- Time Display -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-500 mb-1">Masuk</div>
                        <div class="text-lg font-bold text-gray-800" id="jamMasuk">--:--</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500 mb-1">Keluar</div>
                        <div class="text-lg font-bold text-gray-800" id="jamKeluar">--:--</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Selection -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6" id="locationSection">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Lokasi Absensi</h3>
            <div id="lokasiList" class="space-y-3">
                <!-- Locations will be loaded here -->
            </div>
        </div>

        <!-- Camera Section -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6" id="cameraSection" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ambil Foto Selfie</h3>
            
            <!-- Camera Preview -->
            <div class="relative mb-4">
                <video id="cameraPreview" class="w-full h-64 bg-gray-100 rounded-xl object-cover" autoplay playsinline style="display: none;"></video>
                <canvas id="photoCanvas" class="w-full h-64 bg-gray-100 rounded-xl" style="display: none;"></canvas>
                <div id="cameraPlaceholder" class="w-full h-64 bg-gray-100 rounded-xl flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5a3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97c0-.33-.03-.65-.07-.97l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.32-.07.65-.07.97c0 .33.03.65.07.97L2.46 14.4c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.31.61.22l2.49-1c.52.39 1.06.73 1.69.98l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.25 1.17-.59 1.69-.98l2.49 1c.22.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.63Z"/>
                        </svg>
                        <p class="text-gray-500">Kamera belum aktif</p>
                    </div>
                </div>
            </div>

            <!-- Camera Controls -->
            <div class="flex justify-center space-x-4">
                <button id="startCameraBtn" onclick="startCamera()" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium">
                    Aktifkan Kamera
                </button>
                <button id="captureBtn" onclick="capturePhoto()" class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium" style="display: none;">
                    Ambil Foto
                </button>
                <button id="retakeBtn" onclick="retakePhoto()" class="px-6 py-3 bg-gray-600 text-white rounded-xl font-medium" style="display: none;">
                    Ulangi
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button id="submitBtn" onclick="submitAbsensi()" class="w-full py-4 bg-blue-600 text-white rounded-xl font-semibold text-lg" style="display: none;">
            <span id="submitText">Absen Masuk</span>
        </button>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Memproses absensi...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedLokasi = null;
let capturedPhoto = null;
let currentStream = null;
let absensiStatus = null;

// Load data when page loads
document.addEventListener('DOMContentLoaded', async function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    updateCurrentDate();
    await loadAbsensiStatus();
    await loadLokasiAbsensi();
});

// Update current date
function updateCurrentDate() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', options);
}

// Load today's attendance status
async function loadAbsensiStatus() {
    try {
        const response = await API.get('/absensi/today-status');
        if (response.success) {
            absensiStatus = response.data;
            updateStatusDisplay();
        }
    } catch (error) {
        console.error('Error loading attendance status:', error);
    }
}

// Update status display
function updateStatusDisplay() {
    if (!absensiStatus) return;
    
    const statusIcon = document.getElementById('statusIcon');
    const statusTitle = document.getElementById('statusTitle');
    const statusMessage = document.getElementById('statusMessage');
    const jamMasuk = document.getElementById('jamMasuk');
    const jamKeluar = document.getElementById('jamKeluar');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    
    if (absensiStatus.kehadiran) {
        jamMasuk.textContent = absensiStatus.kehadiran.jam_masuk || '--:--';
        jamKeluar.textContent = absensiStatus.kehadiran.jam_keluar || '--:--';
    }
    
    if (absensiStatus.can_check_in) {
        // Can check in
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center';
        statusTitle.textContent = 'Siap Absen Masuk';
        statusMessage.textContent = 'Pilih lokasi dan ambil foto untuk absen masuk';
        submitText.textContent = 'Absen Masuk';
    } else if (absensiStatus.can_check_out) {
        // Can check out
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-orange-100 flex items-center justify-center';
        statusTitle.textContent = 'Siap Absen Keluar';
        statusMessage.textContent = 'Pilih lokasi dan ambil foto untuk absen keluar';
        submitText.textContent = 'Absen Keluar';
    } else {
        // Already completed
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center';
        statusTitle.textContent = 'Absensi Selesai';
        statusMessage.textContent = 'Anda sudah menyelesaikan absensi hari ini';
        
        // Hide location and camera sections
        document.getElementById('locationSection').style.display = 'none';
        document.getElementById('cameraSection').style.display = 'none';
    }
}

// Load available locations
async function loadLokasiAbsensi() {
    try {
        const response = await API.get('/absensi/lokasi');
        if (response.success) {
            renderLokasiList(response.data);
        }
    } catch (error) {
        console.error('Error loading locations:', error);
        Swal.fire('Error', 'Gagal memuat lokasi absensi', 'error');
    }
}

// Render location list
function renderLokasiList(lokasis) {
    const container = document.getElementById('lokasiList');
    
    if (lokasis.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-gray-500">Tidak ada lokasi absensi tersedia</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = lokasis.map(lokasi => `
        <div class="border border-gray-200 rounded-xl p-4 cursor-pointer hover:border-blue-300 transition-colors lokasi-item" 
             data-lokasi='${JSON.stringify(lokasi)}' onclick="selectLokasi(this)">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800 mb-1">${lokasi.nama_lokasi}</h4>
                    <p class="text-sm text-gray-600 mb-2">${lokasi.alamat}</p>
                    <div class="flex items-center text-xs text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        Radius: ${lokasi.radius}m
                    </div>
                </div>
                <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-blue-600 rounded-full hidden"></div>
                </div>
            </div>
        </div>
    `).join('');
}

// Select location
function selectLokasi(element) {
    // Remove previous selection
    document.querySelectorAll('.lokasi-item').forEach(item => {
        item.classList.remove('border-blue-500', 'bg-blue-50');
        item.querySelector('.w-3.h-3').classList.add('hidden');
    });
    
    // Add selection to clicked item
    element.classList.add('border-blue-500', 'bg-blue-50');
    element.querySelector('.w-3.h-3').classList.remove('hidden');
    
    // Store selected location
    selectedLokasi = JSON.parse(element.dataset.lokasi);
    
    // Show camera section
    document.getElementById('cameraSection').style.display = 'block';
    
    // Check location permission and get current position
    checkLocationAndDistance();
}

// Check location permission and distance
async function checkLocationAndDistance() {
    if (!navigator.geolocation) {
        Swal.fire('Error', 'Geolocation tidak didukung oleh browser ini', 'error');
        return;
    }
    
    try {
        const position = await getCurrentPosition();
        const distance = calculateDistance(
            position.coords.latitude,
            position.coords.longitude,
            selectedLokasi.latitude,
            selectedLokasi.longitude
        );
        
        const isInRadius = distance <= selectedLokasi.radius;
        
        if (!isInRadius) {
            Swal.fire({
                title: 'Peringatan',
                text: `Anda berada ${Math.round(distance)}m dari lokasi absensi. Radius yang diizinkan: ${selectedLokasi.radius}m`,
                icon: 'warning',
                confirmButtonText: 'Lanjutkan',
                showCancelButton: true,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) {
                    // Reset selection
                    selectedLokasi = null;
                    document.getElementById('cameraSection').style.display = 'none';
                }
            });
        }
        
        // Store current position
        selectedLokasi.currentLat = position.coords.latitude;
        selectedLokasi.currentLng = position.coords.longitude;
        
    } catch (error) {
        console.error('Error getting location:', error);
        Swal.fire('Error', 'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin lokasi diberikan.', 'error');
    }
}

// Get current position
function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        });
    });
}

// Calculate distance between two coordinates
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Earth radius in meters
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Start camera
async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'user',
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        });
        
        currentStream = stream;
        const video = document.getElementById('cameraPreview');
        video.srcObject = stream;
        
        // Show video, hide placeholder
        document.getElementById('cameraPlaceholder').style.display = 'none';
        video.style.display = 'block';
        
        // Update buttons
        document.getElementById('startCameraBtn').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        
    } catch (error) {
        console.error('Error accessing camera:', error);
        Swal.fire('Error', 'Gagal mengakses kamera. Pastikan izin kamera diberikan.', 'error');
    }
}

// Capture photo
function capturePhoto() {
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('photoCanvas');
    const context = canvas.getContext('2d');
    
    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    context.drawImage(video, 0, 0);
    
    // Convert to blob
    canvas.toBlob((blob) => {
        capturedPhoto = blob;
        
        // Show canvas, hide video
        video.style.display = 'none';
        canvas.style.display = 'block';
        
        // Update buttons
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('retakeBtn').style.display = 'inline-block';
        
        // Show submit button
        document.getElementById('submitBtn').style.display = 'block';
        
        // Stop camera stream
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
    }, 'image/jpeg', 0.8);
}

// Retake photo
function retakePhoto() {
    capturedPhoto = null;
    
    // Reset display
    document.getElementById('photoCanvas').style.display = 'none';
    document.getElementById('cameraPlaceholder').style.display = 'flex';
    
    // Reset buttons
    document.getElementById('retakeBtn').style.display = 'none';
    document.getElementById('startCameraBtn').style.display = 'inline-block';
    document.getElementById('submitBtn').style.display = 'none';
}

// Submit attendance
async function submitAbsensi() {
    if (!selectedLokasi) {
        Swal.fire('Error', 'Pilih lokasi absensi terlebih dahulu', 'error');
        return;
    }
    
    if (!capturedPhoto) {
        Swal.fire('Error', 'Ambil foto selfie terlebih dahulu', 'error');
        return;
    }
    
    if (!selectedLokasi.currentLat || !selectedLokasi.currentLng) {
        Swal.fire('Error', 'Lokasi tidak terdeteksi', 'error');
        return;
    }
    
    // Show loading
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    try {
        // Prepare form data
        const formData = new FormData();
        formData.append('lokasi_absensi_id', selectedLokasi.id);
        formData.append('latitude', selectedLokasi.currentLat);
        formData.append('longitude', selectedLokasi.currentLng);
        formData.append('foto', capturedPhoto, 'selfie.jpg');
        
        // Determine endpoint
        const endpoint = absensiStatus.can_check_in ? '/absensi/check-in' : '/absensi/check-out';
        
        // Submit
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${API.getToken()}`,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: result.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload page to update status
                window.location.reload();
            });
        } else {
            throw new Error(result.message);
        }
        
    } catch (error) {
        console.error('Error submitting attendance:', error);
        Swal.fire('Error', error.message || 'Gagal mencatat absensi', 'error');
    } finally {
        document.getElementById('loadingOverlay').style.display = 'none';
    }
}

// Cleanup camera stream when leaving page
window.addEventListener('beforeunload', () => {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endpush
@endsection