@extends('mobile.layouts.app')

@section('title', 'Scan QR Code - Nice Patrol')

@push('styles')
<style>
    #qr-reader {
        width: 100%;
        max-width: 100%;
    }
    
    #qr-reader__camera {
        width: 100% !important;
        height: 300px !important;
        object-fit: cover;
        border-radius: 12px;
    }
    
    #qr-reader__dashboard {
        display: none !important;
    }
    
    #qr-reader__scan_region {
        border-radius: 12px !important;
    }
    
    .scan-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 2px solid #0071CE;
        border-radius: 12px;
        pointer-events: none;
        z-index: 10;
    }
    
    .scan-overlay::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 12px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    /* Hide default QR scanner UI elements */
    #qr-reader__dashboard_section {
        display: none !important;
    }
    
    #qr-reader__header_message {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="flex items-center justify-between p-4">
            <button onclick="goBack()" class="flex items-center text-gray-600">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="font-medium">Kembali</span>
            </button>
            <h1 class="text-lg font-semibold text-gray-800">Scan QR Code</h1>
            <div class="w-6"></div>
        </div>
    </div>

    <!-- Scanner Section -->
    <div class="p-4">
        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-1">Cara Scan QR Code</h3>
                    <p class="text-sm text-blue-700">Arahkan kamera ke QR Code checkpoint yang ingin Anda scan. Pastikan QR Code terlihat jelas dalam frame.</p>
                </div>
            </div>
        </div>

        <!-- Camera Container -->
        <div class="relative bg-black rounded-xl overflow-hidden mb-4">
            <video id="camera-video" class="w-full h-80 object-cover" autoplay playsinline style="display: none;"></video>
            <canvas id="camera-canvas" class="w-full h-80 object-cover" style="display: none;"></canvas>
            
            <!-- QR Reader fallback -->
            <div id="qr-reader" class="relative min-h-[300px] flex items-center justify-center" style="display: none;">
                <!-- Loading state -->
                <div id="camera-loading" class="text-white text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                    <p class="text-sm">Memuat kamera...</p>
                </div>
            </div>
            
            <!-- Initial loading -->
            <div id="initial-loading" class="min-h-[300px] flex items-center justify-center text-white">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                    <p class="text-sm">Memuat kamera...</p>
                </div>
            </div>
            
            <div class="scan-overlay"></div>
        </div>

        <!-- Status -->
        <div id="scan-status" class="text-center mb-4">
            <p class="text-gray-600">Arahkan kamera ke QR Code</p>
        </div>

        <!-- Manual Input (Fallback) -->
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h3 class="font-semibold text-gray-800 mb-3">Input Manual QR Code</h3>
            <div class="space-y-3">
                <input type="text" id="manual-qr" placeholder="Masukkan kode QR secara manual" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button onclick="processManualQR()" 
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Proses QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 mx-4 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-700">Memproses scan...</p>
    </div>
</div>
@endsection

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<!-- QR Code Detection Library -->
<script src="https://unpkg.com/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>

<script>
let html5QrcodeScanner;
let qrScanner;
let currentPatroli = null;
let cameraStream = null;

// Get current patrol from localStorage or URL params
function getCurrentPatroli() {
    // Try to get from URL params first
    const urlParams = new URLSearchParams(window.location.search);
    const patroliId = urlParams.get('patroli_id');
    
    if (patroliId) {
        return { id: patroliId };
    }
    
    // Fallback to localStorage
    const stored = localStorage.getItem('current_patroli');
    return stored ? JSON.parse(stored) : null;
}

// Initialize Camera with QR Scanner
async function initCamera() {
    try {
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('camera-canvas');
        
        // Request camera access
        cameraStream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        });
        
        video.srcObject = cameraStream;
        video.style.display = 'block';
        
        // Hide loading
        document.getElementById('initial-loading').style.display = 'none';
        
        // Initialize QR Scanner
        qrScanner = new QrScanner(
            video,
            result => {
                console.log('QR Code detected:', result.data);
                processQRCode(result.data);
            },
            {
                returnDetailedScanResult: true,
                highlightScanRegion: false,
                highlightCodeOutline: false,
            }
        );
        
        await qrScanner.start();
        
        document.getElementById('scan-status').innerHTML = '<p class="text-green-600">Kamera aktif - Arahkan ke QR Code</p>';
        
    } catch (error) {
        console.error('Camera initialization failed:', error);
        
        // Fallback to html5-qrcode
        document.getElementById('initial-loading').style.display = 'none';
        document.getElementById('qr-reader').style.display = 'flex';
        
        initQRScanner();
    }
}

// Initialize QR Scanner (fallback)
function initQRScanner() {
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0,
        disableFlip: false,
        rememberLastUsedCamera: true,
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
    };

    html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", config, false);
    
    html5QrcodeScanner.render(
        (decodedText, decodedResult) => {
            console.log('QR Code detected:', decodedText);
            // Stop scanner immediately after successful scan
            html5QrcodeScanner.clear().then(() => {
                processQRCode(decodedText);
            }).catch(err => {
                console.log('Error stopping scanner:', err);
                processQRCode(decodedText);
            });
        },
        (error) => {
            // Handle scan errors (usually just no QR code in view)
            // Don't log every frame error to avoid console spam
            if (error.includes('NotFoundException')) {
                return; // Normal - no QR code in view
            }
            console.log('Scan error:', error);
        }
    );
    
    // Hide loading indicator once scanner is ready
    setTimeout(() => {
        const loading = document.getElementById('camera-loading');
        if (loading) {
            loading.style.display = 'none';
        }
        document.getElementById('scan-status').innerHTML = '<p class="text-gray-600">Arahkan kamera ke QR Code</p>';
    }, 2000);
}

// Process QR Code (from scanner or manual input)
async function processQRCode(qrCode) {
    if (!qrCode || qrCode.trim() === '') {
        Swal.fire('Error', 'QR Code tidak valid', 'error');
        return;
    }

    // Debug authentication
    console.log('=== SCAN QR DEBUG ===');
    console.log('QR Code:', qrCode);
    console.log('Is Authenticated:', API.isAuthenticated());
    console.log('Token:', API.getToken());
    console.log('User:', API.getUser());
    console.log('API Base URL:', API_BASE_URL);

    // Check authentication
    if (!API.isAuthenticated()) {
        Swal.fire({
            title: 'Belum Login',
            text: 'Anda perlu login terlebih dahulu',
            icon: 'warning',
            confirmButtonText: 'Login',
        }).then(() => {
            window.location.href = '/login';
        });
        return;
    }

    // Show loading
    document.getElementById('loading-modal').classList.remove('hidden');
    document.getElementById('scan-status').innerHTML = '<p class="text-blue-600">Memproses scan...</p>';

    try {
        // Get current location
        let latitude = null;
        let longitude = null;
        
        try {
            const position = await getCurrentPosition();
            latitude = position.coords.latitude;
            longitude = position.coords.longitude;
        } catch (error) {
            console.log('Location not available:', error);
        }

        console.log('Sending API request...');
        console.log('URL:', `${API_BASE_URL}/scan-qr`);
        console.log('Data:', { qr_code: qrCode.trim(), latitude, longitude });

        // Send scan request to API (new endpoint that handles patrol creation)
        const response = await API.post('/scan-qr', {
            qr_code: qrCode.trim(),
            latitude: latitude,
            longitude: longitude
        });

        console.log('API Response:', response);

        document.getElementById('loading-modal').classList.add('hidden');

        if (response.success) {
            // Stop scanner
            if (qrScanner) {
                qrScanner.stop();
            }
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

            // Store current patrol info and checkpoint data
            localStorage.setItem('current_patroli', JSON.stringify(response.data.patroli));
            localStorage.setItem('current_checkpoint', JSON.stringify(response.data));

            // Show success and redirect to checkpoint detail
            Swal.fire({
                title: 'Scan Berhasil!',
                text: response.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Redirect to checkpoint detail with assets
                const checkpoint = response.data.checkpoint;
                const redirectUrl = `/security/checkpoint/${checkpoint.hash_id}?patroli_detail_id=${response.data.patrol_detail.id}`;
                console.log('Redirecting to:', redirectUrl);
                window.location.href = redirectUrl;
            });
        } else {
            document.getElementById('scan-status').innerHTML = '<p class="text-red-600">Scan gagal, coba lagi</p>';
            
            console.error('API Error:', response);
            
            Swal.fire({
                title: 'Scan Gagal',
                text: response.message,
                icon: 'error',
                confirmButtonText: 'Coba Lagi'
            });
        }
    } catch (error) {
        document.getElementById('loading-modal').classList.add('hidden');
        document.getElementById('scan-status').innerHTML = '<p class="text-red-600">Terjadi kesalahan</p>';
        
        console.error('Scan error:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack
        });
        
        Swal.fire({
            title: 'Terjadi Kesalahan',
            text: 'Gagal memproses scan QR Code: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Coba Lagi'
        });
    }
}

// Process manual QR input
function processManualQR() {
    const qrCode = document.getElementById('manual-qr').value;
    processQRCode(qrCode);
}

// Go back
function goBack() {
    // Stop all scanners
    if (qrScanner) {
        qrScanner.stop();
        qrScanner.destroy();
    }
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch(err => {
            console.log('Error clearing scanner:', err);
        });
    }
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }
    
    // Go back to previous page or security home
    if (document.referrer && document.referrer.includes(window.location.origin)) {
        window.history.back();
    } else {
        window.location.href = '/security/home';
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }

    // Initialize camera immediately
    initCamera();
});

// Check camera permission (not needed anymore, handled in initCamera)
async function checkCameraPermission() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        // Stop the stream immediately, we just needed to check permission
        stream.getTracks().forEach(track => track.stop());
        return true;
    } catch (error) {
        throw error;
    }
}

// Cleanup when page unloads
window.addEventListener('beforeunload', function() {
    if (qrScanner) {
        qrScanner.stop();
        qrScanner.destroy();
    }
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch(err => {
            console.log('Error clearing scanner on unload:', err);
        });
    }
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }
});

// Handle page visibility change (when user switches tabs)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Page is hidden, pause scanners
        if (qrScanner) {
            qrScanner.stop();
        }
        if (html5QrcodeScanner && html5QrcodeScanner.pause) {
            html5QrcodeScanner.pause();
        }
    } else {
        // Page is visible, resume scanners
        if (qrScanner) {
            qrScanner.start();
        }
        if (html5QrcodeScanner && html5QrcodeScanner.resume) {
            html5QrcodeScanner.resume();
        }
    }
});
</script>
@endpush