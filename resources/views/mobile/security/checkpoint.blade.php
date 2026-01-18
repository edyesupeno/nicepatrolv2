@extends('mobile.layouts.app')

@section('title', 'Checkpoint - Nice Patrol')

@push('styles')
<style>
    .aset-card {
        transition: all 0.3s ease;
    }
    
    .aset-card.checked {
        border-color: #10b981;
        background-color: #f0fdf4;
    }
    
    .status-toggle {
        transition: all 0.3s ease;
    }
    
    .status-toggle.aman {
        background-color: #10b981;
    }
    
    .status-toggle.bermasalah {
        background-color: #f59e0b;
    }
    
    .status-toggle.hilang {
        background-color: #ef4444;
    }
    
    .camera-preview {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .photo-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="flex items-center justify-between p-4">
            <button onclick="goBack()" class="flex items-center text-gray-600">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="font-medium">Kembali</span>
            </button>
            <h1 class="text-lg font-semibold text-gray-800">Checkpoint</h1>
            <div class="w-6"></div>
        </div>
    </div>

    <!-- Checkpoint Info -->
    <div class="p-4">
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 id="checkpoint-name" class="text-lg font-semibold text-gray-800">Loading...</h2>
                    <p id="checkpoint-location" class="text-sm text-gray-500">Loading...</p>
                </div>
            </div>
            
            <!-- Checkpoint Image -->
            <div id="checkpoint-image" class="mb-4 hidden">
                <img id="checkpoint-photo" src="" alt="Checkpoint" class="w-full h-48 object-cover rounded-lg">
            </div>
        </div>

        <!-- Assets Section -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aset yang Perlu Diperiksa</h3>
            
            <!-- Assets List -->
            <div id="assets-list" class="space-y-3">
                <!-- Loading placeholder -->
                <div class="animate-pulse">
                    <div class="h-20 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-20 bg-gray-200 rounded-lg mb-3"></div>
                    <div class="h-20 bg-gray-200 rounded-lg"></div>
                </div>
            </div>
        </div>

        <!-- Documentation Section -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Dokumentasikan Aset Berikut <span class="text-red-500">*</span></h3>
            <div id="photo-verification-info">
                <p class="text-sm text-gray-600 mb-4">Wajib ambil foto untuk memverifikasi bahwa Anda benar-benar berada di lokasi checkpoint ini.</p>
            </div>
            
            <!-- Photo Capture -->
            <div class="space-y-3">
                <button onclick="capturePhoto()" class="w-full flex items-center justify-center space-x-2 bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg py-4 text-blue-600 hover:bg-blue-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium">Ambil Foto Wajib</span>
                </button>
                
                <!-- Photo Preview -->
                <div id="photo-preview" class="hidden">
                    <img id="captured-photo" src="" alt="Preview" class="camera-preview">
                    <div class="flex space-x-2 mt-2">
                        <button onclick="retakePhoto()" class="flex-1 bg-gray-500 text-white py-2 rounded-lg font-medium">
                            Foto Ulang
                        </button>
                        <button onclick="confirmPhoto()" class="flex-1 bg-green-500 text-white py-2 rounded-lg font-medium">
                            Gunakan Foto
                        </button>
                    </div>
                </div>
                
                <!-- Captured Photos -->
                <div id="captured-photos" class="hidden">
                    <h4 class="font-medium text-gray-700 mb-2">Foto yang Diambil:</h4>
                    <div id="photos-grid" class="grid grid-cols-4 gap-2">
                        <!-- Photos will be added here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4">
            <button onclick="submitCheckpoint()" id="submit-btn" 
                    class="w-full bg-gray-400 text-white py-3 rounded-lg font-semibold cursor-not-allowed" disabled>
                Kirim Laporan
            </button>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div id="camera-modal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden">
    <div class="w-full max-w-md mx-4">
        <div class="bg-white rounded-t-xl p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Ambil Foto</h3>
                <button onclick="closeCamera()" class="text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="relative">
            <video id="camera-video" class="w-full h-80 object-cover bg-black" autoplay playsinline></video>
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
                <button onclick="takePhoto()" class="w-16 h-16 bg-white rounded-full border-4 border-gray-300 flex items-center justify-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 mx-4 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-700">Memuat data...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
let checkpointData = null;
let patroliDetailId = null;
let asetChecks = [];
let capturedPhotos = [];
let cameraStream = null;
let currentAsetPhotoId = null; // Track which asset is being photographed

// Get URL parameters
function getUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    return {
        patroliDetailId: urlParams.get('patroli_detail_id'),
        checkpointId: window.location.pathname.split('/').pop()
    };
}

// Load checkpoint data
async function loadCheckpointData() {
    const params = getUrlParams();
    patroliDetailId = params.patroliDetailId;
    const checkpointId = params.checkpointId;
    
    if (!patroliDetailId) {
        Swal.fire('Error', 'Data patroli tidak ditemukan', 'error').then(() => {
            goBack();
        });
        return;
    }

    document.getElementById('loading-modal').classList.remove('hidden');

    try {
        const response = await API.get(`/checkpoints/${checkpointId}/asets`);
        
        if (response.success) {
            checkpointData = response.data;
            displayCheckpointInfo();
            displayAssets();
            displayPhotoVerificationInfo();
        } else {
            throw new Error(response.message || 'Gagal memuat data checkpoint');
        }
    } catch (error) {
        console.error('Error loading checkpoint:', error);
        
        // If API fails, try to get checkpoint data from localStorage or show error
        const storedCheckpoint = localStorage.getItem('current_checkpoint');
        if (storedCheckpoint) {
            try {
                const storedData = JSON.parse(storedCheckpoint);
                checkpointData = {
                    checkpoint: storedData.checkpoint,
                    asets: storedData.asets || storedData.checkpoint.asets,
                    photo_verification_aset: storedData.photo_verification_aset || (storedData.asets && storedData.asets[0])
                };
                displayCheckpointInfo();
                displayAssets();
                displayPhotoVerificationInfo();
            } catch (e) {
                Swal.fire('Error', 'Gagal memuat data checkpoint', 'error').then(() => {
                    goBack();
                });
            }
        } else {
            Swal.fire('Error', 'Gagal memuat data checkpoint: ' + error.message, 'error').then(() => {
                goBack();
            });
        }
    } finally {
        document.getElementById('loading-modal').classList.add('hidden');
    }
}

// Display checkpoint information
function displayCheckpointInfo() {
    const checkpoint = checkpointData.checkpoint;
    
    document.getElementById('checkpoint-name').textContent = checkpoint.nama || 'Checkpoint';
    document.getElementById('checkpoint-location').textContent = checkpoint.alamat || checkpoint.deskripsi || 'Lokasi tidak tersedia';
    
    // Show checkpoint image if available
    if (checkpoint.foto) {
        document.getElementById('checkpoint-image').classList.remove('hidden');
        document.getElementById('checkpoint-photo').src = checkpoint.foto;
    }
}

// Display photo verification info
function displayPhotoVerificationInfo() {
    const photoAset = checkpointData.photo_verification_aset;
    const infoDiv = document.getElementById('photo-verification-info');
    
    if (photoAset && infoDiv) {
        infoDiv.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                <div class="flex items-center gap-3">
                    ${photoAset.foto ? `
                        <img src="${photoAset.foto}" alt="${photoAset.nama}" class="w-12 h-12 object-cover rounded-lg">
                    ` : `
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                    `}
                    <div>
                        <p class="font-semibold text-blue-900">${photoAset.nama}</p>
                        <p class="text-sm text-blue-700">${photoAset.kode_aset}</p>
                        <p class="text-xs text-blue-600">${photoAset.kategori}</p>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">Wajib ambil foto aset di atas untuk memverifikasi bahwa Anda benar-benar berada di lokasi checkpoint ini.</p>
        `;
    }
}

// Display assets
function displayAssets() {
    const assetsList = document.getElementById('assets-list');
    const assets = checkpointData.asets || [];
    
    if (assets.length === 0) {
        assetsList.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8v2m0 6v2"></path>
                </svg>
                <p>Tidak ada aset yang perlu diperiksa di checkpoint ini</p>
            </div>
        `;
        return;
    }

    assetsList.innerHTML = assets.map(asset => `
        <div class="aset-card border border-gray-200 rounded-lg p-4" data-aset-id="${asset.id}">
            <div class="flex items-start space-x-3">
                ${asset.foto ? `
                    <img src="${asset.foto}" alt="${asset.nama}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                ` : `
                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-2 .89-2 2v11c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm6 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                        </svg>
                    </div>
                `}
                
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">${asset.nama}</h4>
                    <p class="text-sm text-gray-500 mb-2">${asset.kode_aset}</p>
                    <p class="text-xs text-gray-400">${asset.kategori} ${asset.merk ? '• ' + asset.merk : ''}</p>
                    
                    <!-- Status Toggle -->
                    <div class="flex space-x-1 mt-3">
                        <button onclick="setAsetStatus(${asset.id}, 'aman')" 
                                class="status-btn flex-1 py-2 px-2 rounded-lg text-xs font-medium border-2 border-green-200 text-green-700 hover:bg-green-50"
                                data-status="aman">
                            Aman
                        </button>
                        <button onclick="setAsetStatus(${asset.id}, 'bermasalah')" 
                                class="status-btn flex-1 py-2 px-2 rounded-lg text-xs font-medium border-2 border-orange-200 text-orange-700 hover:bg-orange-50"
                                data-status="bermasalah">
                            Bermasalah
                        </button>
                        <button onclick="setAsetStatus(${asset.id}, 'hilang')" 
                                class="status-btn flex-1 py-2 px-2 rounded-lg text-xs font-medium border-2 border-red-200 text-red-700 hover:bg-red-50"
                                data-status="hilang">
                            Hilang
                        </button>
                    </div>
                    
                    <!-- Catatan -->
                    <div class="mt-3 hidden" id="catatan-${asset.id}">
                        <textarea placeholder="Catatan wajib diisi untuk aset bermasalah/hilang" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none"
                                  rows="2" onchange="updateAsetCatatan(${asset.id}, this.value)"
                                  id="catatan-input-${asset.id}"></textarea>
                        <p class="text-xs text-red-500 mt-1 hidden" id="catatan-error-${asset.id}">
                            Catatan wajib diisi untuk status bermasalah/hilang
                        </p>
                    </div>
                    
                    <!-- Foto Aset (untuk status bermasalah/hilang) -->
                    <div class="mt-3 hidden" id="foto-aset-${asset.id}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Aset <span class="text-red-500">*</span>
                        </label>
                        <button onclick="captureAsetPhoto(${asset.id})" 
                                class="w-full flex items-center justify-center space-x-2 bg-orange-50 border-2 border-dashed border-orange-300 rounded-lg py-3 text-orange-600 hover:bg-orange-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Ambil Foto Aset</span>
                        </button>
                        
                        <!-- Foto Preview -->
                        <div id="aset-foto-preview-${asset.id}" class="hidden mt-2">
                            <img id="aset-foto-img-${asset.id}" src="" alt="Foto Aset" class="w-full h-32 object-cover rounded-lg">
                            <div class="flex space-x-2 mt-2">
                                <button onclick="retakeAsetPhoto(${asset.id})" class="flex-1 bg-gray-500 text-white py-2 rounded-lg font-medium text-sm">
                                    Foto Ulang
                                </button>
                                <button onclick="confirmAsetPhoto(${asset.id})" class="flex-1 bg-green-500 text-white py-2 rounded-lg font-medium text-sm">
                                    Gunakan Foto
                                </button>
                            </div>
                        </div>
                        
                        <!-- Foto Tersimpan -->
                        <div id="aset-foto-saved-${asset.id}" class="hidden mt-2">
                            <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm text-green-700 font-medium">Foto tersimpan</span>
                                </div>
                                <button onclick="changeAsetPhoto(${asset.id})" class="text-xs text-blue-600 hover:text-blue-800">
                                    Ganti Foto
                                </button>
                            </div>
                        </div>
                        
                        <p class="text-xs text-red-500 mt-1 hidden" id="foto-error-${asset.id}">
                            Foto wajib diambil untuk status bermasalah/hilang
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Initialize aset checks array
    asetChecks = assets.map(asset => ({
        aset_id: asset.id,
        status: null,
        catatan: null,
        foto: null
    }));
}

// Set asset status
function setAsetStatus(asetId, status) {
    const asetCard = document.querySelector(`[data-aset-id="${asetId}"]`);
    const statusButtons = asetCard.querySelectorAll('.status-btn');
    
    // Update button styles
    statusButtons.forEach(btn => {
        btn.classList.remove('bg-green-500', 'bg-orange-500', 'bg-red-500', 'text-white');
        btn.classList.add('border-2');
        
        if (btn.dataset.status === 'aman') {
            btn.classList.add('border-green-200', 'text-green-700', 'hover:bg-green-50');
        } else if (btn.dataset.status === 'bermasalah') {
            btn.classList.add('border-orange-200', 'text-orange-700', 'hover:bg-orange-50');
        } else if (btn.dataset.status === 'hilang') {
            btn.classList.add('border-red-200', 'text-red-700', 'hover:bg-red-50');
        }
    });
    
    // Highlight selected button
    const selectedBtn = asetCard.querySelector(`[data-status="${status}"]`);
    selectedBtn.classList.remove('border-2', 'hover:bg-green-50', 'hover:bg-orange-50', 'hover:bg-red-50');
    selectedBtn.classList.add('text-white');
    
    if (status === 'aman') {
        selectedBtn.classList.add('bg-green-500');
        selectedBtn.classList.remove('text-green-700', 'border-green-200');
    } else if (status === 'bermasalah') {
        selectedBtn.classList.add('bg-orange-500');
        selectedBtn.classList.remove('text-orange-700', 'border-orange-200');
    } else if (status === 'hilang') {
        selectedBtn.classList.add('bg-red-500');
        selectedBtn.classList.remove('text-red-700', 'border-red-200');
    }
    
    // Show/hide required fields for bermasalah/hilang
    const catatanField = document.getElementById(`catatan-${asetId}`);
    const fotoField = document.getElementById(`foto-aset-${asetId}`);
    
    if (status === 'bermasalah' || status === 'hilang') {
        catatanField.classList.remove('hidden');
        fotoField.classList.remove('hidden');
        
        // Update placeholder text
        const catatanInput = document.getElementById(`catatan-input-${asetId}`);
        catatanInput.placeholder = `Jelaskan kondisi aset yang ${status}`;
        catatanInput.classList.add('border-red-300');
        
        // Show required indicators
        showValidationErrors(asetId, status);
    } else {
        catatanField.classList.add('hidden');
        fotoField.classList.add('hidden');
        
        // Clear validation errors
        clearValidationErrors(asetId);
    }
    
    // Update aset check data
    const asetCheck = asetChecks.find(check => check.aset_id === asetId);
    if (asetCheck) {
        asetCheck.status = status;
        
        // Clear catatan and foto if status is aman
        if (status === 'aman') {
            asetCheck.catatan = null;
            asetCheck.foto = null;
        }
    }
    
    // Mark card as checked
    asetCard.classList.add('checked');
    
    // Update submit button
    updateSubmitButton();
}

// Show validation errors for required fields
function showValidationErrors(asetId, status) {
    const asetCheck = asetChecks.find(check => check.aset_id === asetId);
    
    // Check catatan
    if (!asetCheck.catatan || asetCheck.catatan.trim() === '') {
        document.getElementById(`catatan-error-${asetId}`).classList.remove('hidden');
        document.getElementById(`catatan-input-${asetId}`).classList.add('border-red-300');
    }
    
    // Check foto
    if (!asetCheck.foto) {
        document.getElementById(`foto-error-${asetId}`).classList.remove('hidden');
    }
}

// Clear validation errors
function clearValidationErrors(asetId) {
    document.getElementById(`catatan-error-${asetId}`).classList.add('hidden');
    document.getElementById(`foto-error-${asetId}`).classList.add('hidden');
    document.getElementById(`catatan-input-${asetId}`).classList.remove('border-red-300');
}

// Update asset catatan
function updateAsetCatatan(asetId, catatan) {
    const asetCheck = asetChecks.find(check => check.aset_id === asetId);
    if (asetCheck) {
        asetCheck.catatan = catatan;
        
        // Validate catatan for bermasalah/hilang status
        if ((asetCheck.status === 'bermasalah' || asetCheck.status === 'hilang')) {
            if (catatan && catatan.trim() !== '') {
                // Valid catatan
                document.getElementById(`catatan-error-${asetId}`).classList.add('hidden');
                document.getElementById(`catatan-input-${asetId}`).classList.remove('border-red-300');
                document.getElementById(`catatan-input-${asetId}`).classList.add('border-green-300');
            } else {
                // Invalid catatan
                document.getElementById(`catatan-error-${asetId}`).classList.remove('hidden');
                document.getElementById(`catatan-input-${asetId}`).classList.add('border-red-300');
                document.getElementById(`catatan-input-${asetId}`).classList.remove('border-green-300');
            }
        }
        
        updateSubmitButton();
    }
}

// Update submit button state
function updateSubmitButton() {
    const submitBtn = document.getElementById('submit-btn');
    const allChecked = asetChecks.every(check => check.status !== null);
    const hasPhotos = capturedPhotos.length > 0;
    
    // Validate required fields for bermasalah/hilang assets
    let validationErrors = [];
    
    asetChecks.forEach(check => {
        if (check.status === 'bermasalah' || check.status === 'hilang') {
            if (!check.catatan || check.catatan.trim() === '') {
                validationErrors.push('Catatan wajib diisi untuk aset bermasalah/hilang');
            }
            if (!check.foto) {
                validationErrors.push('Foto wajib diambil untuk aset bermasalah/hilang');
            }
        }
    });
    
    const isValid = allChecked && hasPhotos && validationErrors.length === 0;
    
    if (isValid) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        submitBtn.textContent = 'Kirim Laporan';
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        
        // Determine error message
        if (!allChecked) {
            submitBtn.textContent = 'Lengkapi Status Semua Aset';
        } else if (!hasPhotos) {
            submitBtn.textContent = 'Ambil Foto Wajib Dulu';
        } else if (validationErrors.length > 0) {
            submitBtn.textContent = 'Lengkapi Foto & Catatan Aset Bermasalah';
        }
    }
}

// Camera functions
async function capturePhoto() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        
        cameraStream = stream;
        document.getElementById('camera-modal').classList.remove('hidden');
        document.getElementById('camera-video').srcObject = stream;
    } catch (error) {
        console.error('Camera access denied:', error);
        Swal.fire('Error', 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.', 'error');
    }
}

function closeCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    document.getElementById('camera-modal').classList.add('hidden');
}

function takePhoto() {
    const video = document.getElementById('camera-video');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    
    const imageDataUrl = canvas.toDataURL('image/jpeg', 0.8);
    
    // Check if this is for asset photo or checkpoint photo
    if (currentAsetPhotoId) {
        // Asset photo
        document.getElementById(`aset-foto-img-${currentAsetPhotoId}`).src = imageDataUrl;
        document.getElementById(`aset-foto-preview-${currentAsetPhotoId}`).classList.remove('hidden');
        currentAsetPhotoId = null;
    } else {
        // Checkpoint photo
        document.getElementById('captured-photo').src = imageDataUrl;
        document.getElementById('photo-preview').classList.remove('hidden');
    }
    
    // Close camera
    closeCamera();
}

function retakePhoto() {
    document.getElementById('photo-preview').classList.add('hidden');
    capturePhoto();
}

function confirmPhoto() {
    const imageDataUrl = document.getElementById('captured-photo').src;
    
    // Add to captured photos
    capturedPhotos.push(imageDataUrl);
    
    // Update UI
    updatePhotosGrid();
    document.getElementById('photo-preview').classList.add('hidden');
    
    // Update submit button
    updateSubmitButton();
}

function updatePhotosGrid() {
    const photosGrid = document.getElementById('photos-grid');
    const capturedPhotosDiv = document.getElementById('captured-photos');
    
    if (capturedPhotos.length > 0) {
        capturedPhotosDiv.classList.remove('hidden');
        
        photosGrid.innerHTML = capturedPhotos.map((photo, index) => `
            <div class="relative">
                <img src="${photo}" alt="Foto ${index + 1}" class="photo-thumbnail">
                <button onclick="removePhoto(${index})" 
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">
                    ×
                </button>
            </div>
        `).join('');
    } else {
        capturedPhotosDiv.classList.add('hidden');
    }
}

function removePhoto(index) {
    capturedPhotos.splice(index, 1);
    updatePhotosGrid();
    updateSubmitButton();
}

// Asset photo functions
async function captureAsetPhoto(asetId) {
    currentAsetPhotoId = asetId;
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        
        cameraStream = stream;
        document.getElementById('camera-modal').classList.remove('hidden');
        document.getElementById('camera-video').srcObject = stream;
    } catch (error) {
        console.error('Camera access denied:', error);
        Swal.fire('Error', 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.', 'error');
    }
}

function retakeAsetPhoto(asetId) {
    document.getElementById(`aset-foto-preview-${asetId}`).classList.add('hidden');
    document.getElementById(`aset-foto-saved-${asetId}`).classList.add('hidden');
    captureAsetPhoto(asetId);
}

function confirmAsetPhoto(asetId) {
    const imageDataUrl = document.getElementById(`aset-foto-img-${asetId}`).src;
    
    // Update aset check data
    const asetCheck = asetChecks.find(check => check.aset_id === asetId);
    if (asetCheck) {
        asetCheck.foto = imageDataUrl;
    }
    
    // Update UI
    document.getElementById(`aset-foto-preview-${asetId}`).classList.add('hidden');
    document.getElementById(`aset-foto-saved-${asetId}`).classList.remove('hidden');
    
    // Clear validation error
    document.getElementById(`foto-error-${asetId}`).classList.add('hidden');
    
    // Update submit button
    updateSubmitButton();
}

function changeAsetPhoto(asetId) {
    document.getElementById(`aset-foto-saved-${asetId}`).classList.add('hidden');
    captureAsetPhoto(asetId);
}

// Submit checkpoint
async function submitCheckpoint() {
    // Validate all assets have status
    if (asetChecks.some(check => check.status === null)) {
        Swal.fire('Peringatan', 'Harap lengkapi status semua aset', 'warning');
        return;
    }
    
    // Validate checkpoint photo
    if (capturedPhotos.length === 0) {
        Swal.fire('Peringatan', 'Foto wajib diambil untuk memverifikasi lokasi checkpoint', 'warning');
        return;
    }

    // Validate bermasalah/hilang assets have required fields
    const invalidAssets = [];
    asetChecks.forEach((check, index) => {
        if (check.status === 'bermasalah' || check.status === 'hilang') {
            const asset = checkpointData.asets[index];
            const errors = [];
            
            if (!check.catatan || check.catatan.trim() === '') {
                errors.push('catatan');
            }
            
            if (!check.foto) {
                errors.push('foto');
            }
            
            if (errors.length > 0) {
                invalidAssets.push({
                    name: asset.nama,
                    status: check.status,
                    errors: errors
                });
            }
        }
    });

    if (invalidAssets.length > 0) {
        const errorMessage = invalidAssets.map(asset => 
            `${asset.name} (${asset.status}): ${asset.errors.join(' dan ')} wajib diisi`
        ).join('\n');
        
        Swal.fire({
            title: 'Lengkapi Data Aset',
            text: errorMessage,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Show loading
    document.getElementById('loading-modal').classList.remove('hidden');

    try {
        // Add checkpoint photo to first aset check (or distribute as needed)
        if (capturedPhotos.length > 0 && asetChecks.length > 0) {
            // If no asset has foto yet, add checkpoint photo to first asset
            const firstAsetWithoutPhoto = asetChecks.find(check => !check.foto);
            if (firstAsetWithoutPhoto) {
                firstAsetWithoutPhoto.foto = capturedPhotos[0];
            }
        }

        const response = await API.post(`/checkpoints/${checkpointData.checkpoint.hash_id}/aset-status`, {
            patroli_detail_id: patroliDetailId,
            aset_checks: asetChecks
        });

        if (response.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Laporan checkpoint berhasil dikirim. Terima kasih telah memverifikasi lokasi.',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                // Redirect back to patrol or home
                window.location.href = '/security/home';
            });
        } else {
            // Handle API validation errors
            if (response.errors) {
                let errorMessage = 'Validasi gagal:\n';
                Object.keys(response.errors).forEach(key => {
                    errorMessage += `• ${response.errors[key].join(', ')}\n`;
                });
                throw new Error(errorMessage);
            } else {
                throw new Error(response.message || 'Gagal mengirim laporan');
            }
        }
    } catch (error) {
        console.error('Submit error:', error);
        
        // Show user-friendly error message
        let errorMessage = error.message;
        if (errorMessage.includes('Foto wajib diisi')) {
            errorMessage = 'Foto wajib diambil untuk aset dengan status bermasalah atau hilang. Pastikan semua aset bermasalah sudah difoto.';
        } else if (errorMessage.includes('Catatan wajib diisi')) {
            errorMessage = 'Catatan wajib diisi untuk aset dengan status bermasalah atau hilang. Jelaskan kondisi aset tersebut.';
        }
        
        Swal.fire('Error', errorMessage, 'error');
    } finally {
        document.getElementById('loading-modal').classList.add('hidden');
    }
}

// Go back
function goBack() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }
    
    if (document.referrer && document.referrer.includes(window.location.origin)) {
        window.history.back();
    } else {
        window.location.href = '/security/home';
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    loadCheckpointData();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endpush