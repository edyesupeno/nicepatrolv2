@extends('mobile.layouts.app')

@section('title', 'Absensi - Security Officer')

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
        <!-- Permission Status Card -->
        <div id="permissionCard" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-4" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Izin Diperlukan</h3>
                        <p class="text-sm text-gray-600" id="permissionMessage">Kamera dan GPS diperlukan untuk absensi</p>
                    </div>
                </div>
                <button onclick="requestPermissions()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">
                    Berikan Izin
                </button>
            </div>
        </div>

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
let permissionsChecked = false;

// Load data when page loads
document.addEventListener('DOMContentLoaded', async function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    updateCurrentDate();
    await checkAllPermissions();
    await loadAbsensiStatus();
    await loadLokasiAbsensi();
});

// Check all required permissions
async function checkAllPermissions() {
    if (permissionsChecked) return;
    
    try {
        // Check if permissions API is supported
        if (!navigator.permissions) {
            console.warn('Permissions API not supported');
            permissionsChecked = true;
            return;
        }
        
        // Check camera permission
        const cameraPermission = await navigator.permissions.query({ name: 'camera' });
        
        // Check geolocation permission
        const locationPermission = await navigator.permissions.query({ name: 'geolocation' });
        
        // Show permission status
        showPermissionStatus(cameraPermission.state, locationPermission.state);
        
        // Listen for permission changes
        cameraPermission.addEventListener('change', () => {
            showPermissionStatus(cameraPermission.state, locationPermission.state);
        });
        
        locationPermission.addEventListener('change', () => {
            showPermissionStatus(cameraPermission.state, locationPermission.state);
        });
        
        permissionsChecked = true;
        
    } catch (error) {
        console.error('Error checking permissions:', error);
        permissionsChecked = true;
    }
}

// Show localhost-specific permission dialog
function showLocalhostPermissionDialog() {
    Swal.fire({
        title: 'Development Mode - Localhost',
        html: `
            <div class="text-left">
                <p class="mb-4">Anda sedang menggunakan aplikasi di localhost HTTP. Browser memerlukan HTTPS untuk mengakses kamera dan GPS.</p>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-green-800 mb-2">🚀 Solusi Terbaik - Ngrok (Recommended):</h4>
                    <ol class="text-sm text-green-700 space-y-1">
                        <li>1. Install ngrok: <code class="bg-green-100 px-1 rounded">brew install ngrok</code></li>
                        <li>2. Jalankan: <code class="bg-green-100 px-1 rounded">ngrok http 8000</code></li>
                        <li>3. Buka URL HTTPS yang diberikan ngrok</li>
                        <li>4. Kamera dan GPS akan langsung berfungsi! ✅</li>
                    </ol>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-blue-800 mb-2">🔧 Alternatif - Chrome Flags:</h4>
                    <p class="text-sm text-blue-700">Jalankan script: <code class="bg-blue-100 px-1 rounded">./start-chrome-dev.sh</code></p>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>💡 Kenapa Ngrok?</strong> Ngrok memberikan HTTPS tunnel ke localhost, jadi browser akan menganggapnya sebagai secure origin dan mengizinkan akses kamera/GPS.
                    </p>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Coba Lagi',
        showCancelButton: true,
        cancelButtonText: 'Lanjutkan Tanpa Izin',
        allowOutsideClick: false,
        customClass: {
            popup: 'text-left'
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            // Try to request permissions anyway
            await testLocalhostPermissions();
        } else {
            // Show warning about limited functionality
            showLimitedFunctionalityWarning();
        }
    });
}

// Test localhost permissions
async function testLocalhostPermissions() {
    const results = {
        camera: false,
        location: false
    };
    
    // Show loading
    Swal.fire({
        title: 'Testing Permissions...',
        text: 'Mencoba mengakses kamera dan GPS...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Test camera
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        stream.getTracks().forEach(track => track.stop());
        results.camera = true;
    } catch (error) {
        console.error('Camera test failed:', error);
        results.camera = false;
    }
    
    // Test location
    try {
        await getCurrentPositionWithTimeout();
        results.location = true;
    } catch (error) {
        console.error('Location test failed:', error);
        results.location = false;
    }
    
    Swal.close();
    
    if (results.camera && results.location) {
        Swal.fire({
            title: 'Berhasil! 🎉',
            text: 'Semua permissions berhasil diberikan. Fitur absensi dapat digunakan.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        
        // Hide permission card
        document.getElementById('permissionCard').style.display = 'none';
    } else {
        showLocalhostPermissionResults(results);
    }
}

// Show localhost permission results
function showLocalhostPermissionResults(results) {
    let deniedPermissions = [];
    if (!results.camera) deniedPermissions.push('Kamera');
    if (!results.location) deniedPermissions.push('GPS/Lokasi');
    
    Swal.fire({
        title: 'Permissions Masih Terbatas',
        html: `
            <div class="text-left">
                <p class="mb-4">Yang belum berfungsi: <strong>${deniedPermissions.join(', ')}</strong></p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-red-800 mb-2">🚫 Penyebab Umum:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• Browser memblokir HTTP localhost untuk security</li>
                        <li>• Chrome flags belum diatur dengan benar</li>
                        <li>• Browser belum di-restart setelah setting flags</li>
                    </ul>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-green-800 mb-2">🚀 Solusi Terbaik - Ngrok:</h4>
                    <ol class="text-sm text-green-700 space-y-1">
                        <li>1. Install: <code class="bg-green-100 px-1 rounded">brew install ngrok</code></li>
                        <li>2. Jalankan: <code class="bg-green-100 px-1 rounded">ngrok http 8000</code></li>
                        <li>3. Buka URL HTTPS yang diberikan ngrok</li>
                        <li>4. Permissions akan langsung berfungsi!</li>
                    </ol>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-800 mb-2">✅ Alternatif Lain:</h4>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>• Setup SSL certificate untuk localhost</li>
                        <li>• Gunakan ngrok: <code class="bg-green-100 px-1 rounded text-xs">ngrok http 8000</code></li>
                        <li>• Test di production server (HTTPS)</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'warning',
        confirmButtonText: 'Coba Lagi',
        showCancelButton: true,
        cancelButtonText: 'Lanjutkan',
        customClass: {
            popup: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        } else {
            showLimitedFunctionalityWarning();
        }
    });
}

// Show fallback permission dialog for unsupported browsers
function showFallbackPermissionDialog() {
    Swal.fire({
        title: 'Browser Compatibility',
        html: `
            <div class="text-left">
                <p class="mb-4">Browser Anda mungkin tidak mendukung Permissions API modern.</p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Catatan:</h4>
                    <p class="text-sm text-yellow-700">Permissions akan diminta saat Anda menggunakan fitur kamera dan GPS.</p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">💡 Tips:</h4>
                    <p class="text-sm text-blue-700">Pastikan untuk mengklik "Allow/Izinkan" saat browser meminta akses kamera dan lokasi.</p>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Mengerti',
        customClass: {
            popup: 'text-left'
        }
    });
}

// Show limited functionality warning
function showLimitedFunctionalityWarning() {
    const permissionCard = document.getElementById('permissionCard');
    const permissionMessage = document.getElementById('permissionMessage');
    
    permissionMessage.textContent = 'Fitur terbatas - Kamera dan GPS tidak tersedia';
    permissionCard.style.display = 'block';
    
    // Change button text
    const button = permissionCard.querySelector('button');
    button.textContent = 'Coba Lagi';
    button.onclick = () => window.location.reload();
}

// Show permission status
function showPermissionStatus(cameraState, locationState) {
    const needsPermission = cameraState !== 'granted' || locationState !== 'granted';
    const permissionCard = document.getElementById('permissionCard');
    const permissionMessage = document.getElementById('permissionMessage');
    
    if (needsPermission) {
        let missingPermissions = [];
        if (cameraState !== 'granted') missingPermissions.push('Kamera');
        if (locationState !== 'granted') missingPermissions.push('GPS');
        
        permissionMessage.textContent = `${missingPermissions.join(' dan ')} diperlukan untuk absensi`;
        permissionCard.style.display = 'block';
    } else {
        permissionCard.style.display = 'none';
        
        // Show success toast if permissions were just granted
        if (permissionsChecked) {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            
            toast.fire({
                icon: 'success',
                title: '✓ Semua izin telah diberikan'
            });
        }
    }
}

// Show permission dialog
function showPermissionDialog(cameraState, locationState) {
    let message = 'Untuk menggunakan fitur absensi, aplikasi memerlukan izin:';
    let permissions = [];
    
    if (cameraState !== 'granted') {
        permissions.push('📷 <strong>Kamera</strong> - untuk mengambil foto selfie');
    }
    
    if (locationState !== 'granted') {
        permissions.push('📍 <strong>Lokasi (GPS)</strong> - untuk memverifikasi posisi Anda');
    }
    
    if (permissions.length === 0) return;
    
    const permissionList = permissions.map(p => `<li class="text-left mb-2">${p}</li>`).join('');
    
    Swal.fire({
        title: 'Izin Diperlukan',
        html: `
            <div class="text-left">
                <p class="mb-4">${message}</p>
                <ul class="mb-4">
                    ${permissionList}
                </ul>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>💡 Tips:</strong> Jika diminta izin oleh browser, pilih "Allow" atau "Izinkan"
                    </p>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Berikan Izin',
        showCancelButton: true,
        cancelButtonText: 'Nanti Saja',
        allowOutsideClick: false,
        customClass: {
            popup: 'text-left'
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            await requestPermissions();
        }
    });
}

// Request permissions
async function requestPermissions() {
    const results = {
        camera: false,
        location: false
    };
    
    // Show loading
    Swal.fire({
        title: 'Meminta Izin...',
        text: 'Mohon berikan izin kamera dan lokasi',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Request camera permission
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        stream.getTracks().forEach(track => track.stop()); // Stop immediately
        results.camera = true;
    } catch (error) {
        console.error('Camera permission denied:', error);
        results.camera = false;
    }
    
    // Request location permission
    try {
        await getCurrentPositionWithTimeout();
        results.location = true;
    } catch (error) {
        console.error('Location permission denied:', error);
        results.location = false;
    }
    
    Swal.close();
    
    // Show results
    showPermissionResults(results);
    
    // Update permission status
    setTimeout(checkAllPermissions, 1000);
}

// Show permission results
function showPermissionResults(results) {
    const allGranted = results.camera && results.location;
    
    if (allGranted) {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Semua izin telah diberikan. Anda dapat menggunakan fitur absensi.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } else {
        let deniedPermissions = [];
        if (!results.camera) deniedPermissions.push('Kamera');
        if (!results.location) deniedPermissions.push('Lokasi');
        
        Swal.fire({
            title: 'Izin Tidak Lengkap',
            html: `
                <div class="text-left">
                    <p class="mb-4">Izin yang belum diberikan: <strong>${deniedPermissions.join(', ')}</strong></p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>⚠️ Perhatian:</strong> Tanpa izin ini, fitur absensi tidak dapat berfungsi dengan baik.
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Cara memberikan izin:</p>
                    <ol class="text-sm text-gray-600 space-y-1">
                        <li>1. Klik ikon 🔒 atau ⓘ di address bar browser</li>
                        <li>2. Pilih "Allow" untuk Kamera dan Lokasi</li>
                        <li>3. Refresh halaman ini</li>
                    </ol>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Coba Lagi',
            showCancelButton: true,
            cancelButtonText: 'Lanjutkan',
            customClass: {
                popup: 'text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    }
}

// Enhanced start camera with better error handling
async function startCamera() {
    try {
        // Show loading state
        const startBtn = document.getElementById('startCameraBtn');
        startBtn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>';
        startBtn.disabled = true;
        
        // Check if camera is available
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Kamera tidak didukung oleh browser ini');
        }
        
        // Request camera access
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
        
        // Wait for video to load
        await new Promise((resolve) => {
            video.onloadedmetadata = resolve;
        });
        
        // Show video, hide placeholder
        document.getElementById('cameraPlaceholder').style.display = 'none';
        video.style.display = 'block';
        
        // Update buttons
        startBtn.style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        
    } catch (error) {
        console.error('Error accessing camera:', error);
        
        // Reset button
        const startBtn = document.getElementById('startCameraBtn');
        startBtn.innerHTML = 'Aktifkan Kamera';
        startBtn.disabled = false;
        
        // Show specific error messages
        let errorMessage = 'Gagal mengakses kamera.';
        let errorTitle = 'Error Kamera';
        
        if (error.name === 'NotAllowedError') {
            errorTitle = 'Izin Kamera Ditolak';
            errorMessage = `
                <div class="text-left">
                    <p class="mb-4">Izin kamera ditolak. Untuk menggunakan fitur absensi, Anda perlu memberikan izin kamera.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800">
                            <strong>Cara memberikan izin:</strong><br>
                            1. Klik ikon 🔒 di address bar<br>
                            2. Pilih "Allow" untuk Kamera<br>
                            3. Refresh halaman ini
                        </p>
                    </div>
                </div>
            `;
        } else if (error.name === 'NotFoundError') {
            errorMessage = 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera.';
        } else if (error.name === 'NotReadableError') {
            errorMessage = 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi lain dan coba lagi.';
        }
        
        Swal.fire({
            title: errorTitle,
            html: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'text-left'
            }
        });
    }
}

// Enhanced location checking with better error handling
async function checkLocationAndDistance() {
    if (!navigator.geolocation) {
        Swal.fire({
            title: 'GPS Tidak Didukung',
            text: 'Browser ini tidak mendukung fitur GPS. Gunakan browser yang lebih baru.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    try {
        // Show loading
        Swal.fire({
            title: 'Mengecek Lokasi...',
            text: 'Mohon tunggu, sedang mendapatkan lokasi Anda',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const position = await getCurrentPositionWithTimeout();
        Swal.close();
        
        const distance = calculateDistance(
            position.coords.latitude,
            position.coords.longitude,
            selectedLokasi.latitude,
            selectedLokasi.longitude
        );
        
        const isInRadius = distance <= selectedLokasi.radius;
        
        if (!isInRadius) {
            Swal.fire({
                title: 'Peringatan Lokasi',
                html: `
                    <div class="text-left">
                        <p class="mb-4">Anda berada <strong>${Math.round(distance)}m</strong> dari lokasi absensi.</p>
                        <p class="mb-4">Radius yang diizinkan: <strong>${selectedLokasi.radius}m</strong></p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <strong>⚠️ Catatan:</strong> Absensi di luar radius mungkin tidak disetujui oleh sistem.
                            </p>
                        </div>
                    </div>
                `,
                icon: 'warning',
                confirmButtonText: 'Lanjutkan',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'text-left'
                }
            }).then((result) => {
                if (!result.isConfirmed) {
                    // Reset selection
                    resetLocationSelection();
                }
            });
        } else {
            // Show success message for being in radius
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            
            toast.fire({
                icon: 'success',
                title: `✓ Lokasi terverifikasi (${Math.round(distance)}m dari titik absensi)`
            });
        }
        
        // Store current position
        selectedLokasi.currentLat = position.coords.latitude;
        selectedLokasi.currentLng = position.coords.longitude;
        
    } catch (error) {
        Swal.close();
        console.error('Error getting location:', error);
        
        let errorTitle = 'Error GPS';
        let errorMessage = 'Gagal mendapatkan lokasi.';
        
        if (error.code === error.PERMISSION_DENIED) {
            errorTitle = 'Izin Lokasi Ditolak';
            errorMessage = `
                <div class="text-left">
                    <p class="mb-4">Izin lokasi ditolak. Untuk menggunakan fitur absensi, Anda perlu memberikan izin lokasi.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800">
                            <strong>Cara memberikan izin:</strong><br>
                            1. Klik ikon 🔒 di address bar<br>
                            2. Pilih "Allow" untuk Lokasi<br>
                            3. Pastikan GPS aktif di perangkat<br>
                            4. Refresh halaman ini
                        </p>
                    </div>
                </div>
            `;
        } else if (error.code === error.POSITION_UNAVAILABLE) {
            errorMessage = `
                <div class="text-left">
                    <p class="mb-4">Lokasi tidak dapat ditentukan.</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">
                            <strong>Solusi:</strong><br>
                            1. Pastikan GPS aktif di perangkat<br>
                            2. Pindah ke area dengan sinyal GPS yang baik<br>
                            3. Coba lagi dalam beberapa saat
                        </p>
                    </div>
                </div>
            `;
        } else if (error.code === error.TIMEOUT) {
            errorMessage = `
                <div class="text-left">
                    <p class="mb-4">Waktu tunggu GPS habis.</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">
                            <strong>Solusi:</strong><br>
                            1. Pindah ke area terbuka<br>
                            2. Pastikan GPS aktif<br>
                            3. Coba lagi
                        </p>
                    </div>
                </div>
            `;
        }
        
        Swal.fire({
            title: errorTitle,
            html: errorMessage,
            icon: 'error',
            confirmButtonText: 'Coba Lagi',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                checkLocationAndDistance();
            } else {
                resetLocationSelection();
            }
        });
    }
}

// Get current position with timeout and better error handling
function getCurrentPositionWithTimeout() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('GPS tidak didukung oleh browser ini'));
            return;
        }
        
        const options = {
            enableHighAccuracy: true,
            timeout: 30000, // Increase to 30 seconds
            maximumAge: 30000 // 30 seconds cache
        };
        
        // Add timeout wrapper
        const timeoutId = setTimeout(() => {
            reject(new Error('Timeout: GPS tidak merespons dalam 30 detik'));
        }, 32000); // Slightly longer than geolocation timeout
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                clearTimeout(timeoutId);
                resolve(position);
            },
            (error) => {
                clearTimeout(timeoutId);
                
                // Create more descriptive error
                let customError = new Error();
                customError.code = error.code;
                
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        customError.message = 'Izin lokasi ditolak. Berikan izin GPS untuk melanjutkan.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        customError.message = 'Lokasi tidak dapat ditentukan. Pastikan GPS aktif dan pindah ke area dengan sinyal GPS yang baik.';
                        break;
                    case error.TIMEOUT:
                        customError.message = 'GPS timeout. Pindah ke area terbuka dan coba lagi.';
                        break;
                    default:
                        customError.message = 'Error GPS: ' + error.message;
                }
                
                reject(customError);
            },
            options
        );
    });
}

// Reset location selection
function resetLocationSelection() {
    selectedLokasi = null;
    document.getElementById('cameraSection').style.display = 'none';
    
    // Remove selection from all items
    document.querySelectorAll('.lokasi-item').forEach(item => {
        item.classList.remove('border-blue-500', 'bg-blue-50');
        item.querySelector('.w-3.h-3').classList.add('hidden');
    });
}

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
    } else if (absensiStatus.can_take_break) {
        // Can take break
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-orange-100 flex items-center justify-center';
        statusTitle.textContent = 'Siap Istirahat';
        statusMessage.textContent = 'Pilih lokasi dan ambil foto untuk istirahat';
        submitText.textContent = 'Istirahat';
    } else if (absensiStatus.can_return_from_break) {
        // Can return from break
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center';
        statusTitle.textContent = 'Siap Kembali Bekerja';
        statusMessage.textContent = 'Pilih lokasi dan ambil foto untuk kembali bekerja';
        submitText.textContent = 'Kembali Bekerja';
    } else if (absensiStatus.can_check_out) {
        // Can check out
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center';
        statusTitle.textContent = 'Siap Absen Pulang';
        statusMessage.textContent = 'Pilih lokasi dan ambil foto untuk absen pulang';
        submitText.textContent = 'Absen Pulang';
    } else {
        // Already completed
        statusIcon.innerHTML = `
            <svg class="w-10 h-10 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
        `;
        statusIcon.className = 'w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center';
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

// Enhanced location checking with simple error handling
async function checkLocationAndDistance() {
    if (!navigator.geolocation) {
        Swal.fire({
            title: 'GPS Tidak Didukung',
            text: 'Browser ini tidak mendukung fitur GPS. Gunakan browser yang lebih baru.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    try {
        // Show loading
        Swal.fire({
            title: 'Mengecek Lokasi...',
            text: 'Mohon tunggu, sedang mendapatkan lokasi Anda',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const position = await getCurrentPositionWithTimeout();
        Swal.close();
        
        const distance = calculateDistance(
            position.coords.latitude,
            position.coords.longitude,
            selectedLokasi.latitude,
            selectedLokasi.longitude
        );
        
        const isInRadius = distance <= selectedLokasi.radius;
        
        if (!isInRadius) {
            Swal.fire({
                title: 'Peringatan Lokasi',
                text: `Anda berada ${Math.round(distance)}m dari lokasi absensi. Radius yang diizinkan: ${selectedLokasi.radius}m. Lanjutkan absensi?`,
                icon: 'warning',
                confirmButtonText: 'Lanjutkan',
                showCancelButton: true,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) {
                    resetLocationSelection();
                }
            });
        } else {
            // Show success message for being in radius
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            
            toast.fire({
                icon: 'success',
                title: `✓ Lokasi terverifikasi (${Math.round(distance)}m dari titik absensi)`
            });
        }
        
        // Store current position
        selectedLokasi.currentLat = position.coords.latitude;
        selectedLokasi.currentLng = position.coords.longitude;
        
    } catch (error) {
        Swal.close();
        console.error('Error getting location:', error);
        
        // Simple error dialog
        Swal.fire({
            title: 'Error GPS',
            text: error.message || 'Lokasi tidak dapat ditentukan. Pastikan GPS aktif dan pindah ke area dengan sinyal GPS yang baik.',
            icon: 'error',
            confirmButtonText: 'Coba Lagi',
            showCancelButton: true,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                checkLocationAndDistance();
            } else {
                resetLocationSelection();
            }
        });
    }
}

// Get current position
function getCurrentPosition() {
    return getCurrentPositionWithTimeout();
}

// Enhanced start camera with better error handling
async function startCamera() {
    try {
        // Show loading state
        const startBtn = document.getElementById('startCameraBtn');
        startBtn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>';
        startBtn.disabled = true;
        
        // Check if camera is available
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Kamera tidak didukung oleh browser ini');
        }
        
        // Request camera access
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
        
        // Wait for video to load
        await new Promise((resolve) => {
            video.onloadedmetadata = resolve;
        });
        
        // Show video, hide placeholder
        document.getElementById('cameraPlaceholder').style.display = 'none';
        video.style.display = 'block';
        
        // Update buttons
        startBtn.style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        
    } catch (error) {
        console.error('Error accessing camera:', error);
        
        // Reset button
        const startBtn = document.getElementById('startCameraBtn');
        startBtn.innerHTML = 'Aktifkan Kamera';
        startBtn.disabled = false;
        
        // Show simple error message
        let errorMessage = 'Gagal mengakses kamera. Pastikan izin kamera diberikan dan tidak digunakan aplikasi lain.';
        
        if (error.name === 'NotAllowedError') {
            errorMessage = 'Izin kamera ditolak. Klik ikon kunci di address bar dan pilih "Allow" untuk kamera.';
        } else if (error.name === 'NotFoundError') {
            errorMessage = 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera.';
        } else if (error.name === 'NotReadableError') {
            errorMessage = 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain dan coba lagi.';
        }
        
        Swal.fire({
            title: 'Error Kamera',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
        });
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
        
        // Determine endpoint based on status
        let endpoint = '/absensi/check-in';
        if (absensiStatus.can_take_break) {
            endpoint = '/absensi/take-break';
        } else if (absensiStatus.can_return_from_break) {
            endpoint = '/absensi/return-from-break';
        } else if (absensiStatus.can_check_out) {
            endpoint = '/absensi/check-out';
        }
        
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