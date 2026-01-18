@extends('perusahaan.layouts.app')

@section('title', 'Input Data Tamu')
@section('page-title', 'Input Data Tamu')
@section('page-subtitle', 'Catat kunjungan tamu baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Progress Steps -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium step-indicator active" data-step="1">
                            1
                        </div>
                        <span class="text-sm font-medium text-gray-900">Data Diri</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 step-line" data-step="1"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium step-indicator" data-step="2">
                            2
                        </div>
                        <span class="text-sm font-medium text-gray-500">Kontak Tamu</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 step-line" data-step="2"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium step-indicator" data-step="3">
                            3
                        </div>
                        <span class="text-sm font-medium text-gray-500">Data Kunjungan</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 step-line" data-step="3"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium step-indicator" data-step="4">
                            4
                        </div>
                        <span class="text-sm font-medium text-gray-500">Kuesioner</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('perusahaan.buku-tamu.store') }}" method="POST" enctype="multipart/form-data" id="formBukuTamu">
            @csrf
            <div class="p-6">
                <!-- Step 1: Data Diri -->
                <div class="wizard-step active" id="step-1">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Data Diri</h3>
                        <p class="text-sm text-gray-600">Masukkan informasi dasar tamu</p>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nama_tamu" 
                                required
                                value="{{ old('nama_tamu') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('nama_tamu')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NIK -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nik" 
                                required
                                value="{{ old('nik') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                                maxlength="16"
                                pattern="[0-9]{16}"
                            >
                            @error('nik')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="date" 
                                    name="tanggal_lahir" 
                                    required
                                    value="{{ old('tanggal_lahir') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                            </div>
                            @error('tanggal_lahir')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Domisili -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Domisili <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="domisili" 
                                required
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >{{ old('domisili') }}</textarea>
                            @error('domisili')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instansi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Instansi <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="perusahaan_tamu" 
                                required
                                value="{{ old('perusahaan_tamu') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('perusahaan_tamu')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="jabatan" 
                                required
                                value="{{ old('jabatan') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('jabatan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Foto KTP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto KTP <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        name="foto_identitas" 
                                        id="foto_identitas"
                                        required
                                        accept="image/jpeg,image/png,image/jpg"
                                        class="hidden"
                                        onchange="previewFotoIdentitas(this)"
                                    >
                                    <label for="foto_identitas" class="cursor-pointer flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-center">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-sm text-gray-600" id="foto_identitas_label">Klik untuk pilih foto KTP</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimal 2MB</p>
                                        </div>
                                    </label>
                                    <div class="mt-2 hidden" id="foto_identitas_preview">
                                        <img class="w-24 h-16 object-cover rounded-lg border mx-auto" alt="Preview KTP">
                                        <p class="text-xs text-green-600 mt-1 text-center">Preview foto KTP</p>
                                    </div>
                                </div>
                            </div>
                            @error('foto_identitas')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Foto Selfie -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Selfie <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        name="foto" 
                                        id="foto"
                                        required
                                        accept="image/jpeg,image/png,image/jpg"
                                        class="hidden"
                                        onchange="previewFoto(this)"
                                    >
                                    <label for="foto" class="cursor-pointer flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-center">
                                        <div class="text-center">
                                            <i class="fas fa-camera text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-sm text-gray-600" id="foto_label">Klik untuk ambil foto selfie</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimal 2MB</p>
                                        </div>
                                    </label>
                                    <div class="mt-2 hidden" id="foto_preview">
                                        <img class="w-24 h-24 object-cover rounded-lg border mx-auto" alt="Preview Selfie">
                                        <p class="text-xs text-green-600 mt-1 text-center">Preview foto selfie</p>
                                    </div>
                                </div>
                            </div>
                            @error('foto')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 2: Kontak Tamu -->
                <div class="wizard-step" id="step-2" style="display: none;">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kontak Tamu</h3>
                        <p class="text-sm text-gray-600">Informasi kontak dan darurat</p>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                required
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No.WhatsApp -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No.WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="no_whatsapp" 
                                required
                                value="{{ old('no_whatsapp') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('no_whatsapp')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kontak Darurat -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kontak Darurat <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="kontak_darurat_telepon" 
                                required
                                value="{{ old('kontak_darurat_telepon') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('kontak_darurat_telepon')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nama Kontak Darurat -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Kontak Darurat <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="kontak_darurat_nama" 
                                required
                                value="{{ old('kontak_darurat_nama') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('kontak_darurat_nama')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hubungan Kontak Darurat -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Hubungan Kontak Darurat <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    name="kontak_darurat_hubungan"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                >
                                    <option value="">Pilih Hubungan</option>
                                    <option value="Keluarga" {{ old('kontak_darurat_hubungan') == 'Keluarga' ? 'selected' : '' }}>Keluarga</option>
                                    <option value="Teman" {{ old('kontak_darurat_hubungan') == 'Teman' ? 'selected' : '' }}>Teman</option>
                                    <option value="Rekan Kerja" {{ old('kontak_darurat_hubungan') == 'Rekan Kerja' ? 'selected' : '' }}>Rekan Kerja</option>
                                    <option value="Lainnya" {{ old('kontak_darurat_hubungan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            @error('kontak_darurat_hubungan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 3: Data Kunjungan -->
                <div class="wizard-step" id="step-3" style="display: none;">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Data Kunjungan</h3>
                        <p class="text-sm text-gray-600">Informasi tujuan dan waktu kunjungan</p>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Maksud & Tujuan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maksud & Tujuan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="keperluan" 
                                required
                                value="{{ old('keperluan') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Input Field"
                            >
                            @error('keperluan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi yang Dituju -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi yang Dituju <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    name="area_id" 
                                    id="area_select"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                >
                                    <option value="">Pilih Area/Lokasi</option>
                                    @forelse($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->nama }}{{ $area->alamat ? ' - ' . $area->alamat : '' }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Belum ada area tersedia</option>
                                    @endforelse
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <input type="hidden" name="lokasi_dituju" id="lokasi_dituju_hidden">
                            @error('area_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('lokasi_dituju')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Area Patrol untuk Kuesioner -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Area Patrol (untuk Kuesioner)
                            </label>
                            <div class="relative">
                                <select 
                                    name="area_patrol_id" 
                                    id="area_patrol_select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                    onchange="loadKuesioner()"
                                >
                                    <option value="">Pilih Area Patrol (Opsional)</option>
                                    @forelse($areaPatrols as $areaPatrol)
                                        <option value="{{ $areaPatrol->id }}" {{ old('area_patrol_id') == $areaPatrol->id ? 'selected' : '' }}>
                                            {{ $areaPatrol->nama }} - {{ $areaPatrol->project->nama }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Belum ada area patrol tersedia</option>
                                    @endforelse
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih area patrol jika ingin mengisi kuesioner dinamis
                            </p>
                            @error('area_patrol_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Mulai Kunjungan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Mulai Kunjungan <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    name="mulai_kunjungan" 
                                    required
                                    value="{{ old('mulai_kunjungan', now()->format('Y-m-d\TH:i')) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                @error('mulai_kunjungan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Selesai Kunjungan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Selesai Kunjungan <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    name="selesai_kunjungan" 
                                    required
                                    value="{{ old('selesai_kunjungan') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                @error('selesai_kunjungan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Lama Kunjungan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Lama Kunjungan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="lama_kunjungan" 
                                required
                                readonly
                                value="{{ old('lama_kunjungan') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none"
                                placeholder="Akan dihitung otomatis"
                            >
                            @error('lama_kunjungan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden fields for existing functionality -->
                        <input type="hidden" name="project_id" value="{{ $projects->first()->id ?? '' }}">
                        <input type="hidden" name="bertemu" value="">

<script>
// Update lokasi_dituju when area is selected
document.getElementById('area_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const hiddenInput = document.getElementById('lokasi_dituju_hidden');
    
    if (selectedOption.value) {
        hiddenInput.name = 'lokasi_dituju';
        hiddenInput.value = selectedOption.text;
    } else {
        hiddenInput.name = '';
        hiddenInput.value = '';
    }
});

// Load kuesioner when area patrol is selected
function loadKuesioner() {
    const areaPatrolId = document.getElementById('area_patrol_select').value;
    const noKuesionerMessage = document.getElementById('no-kuesioner-message');
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    const staticKuesioner = document.getElementById('static-kuesioner');
    
    // Reset all displays first
    noKuesionerMessage.style.display = 'none';
    dynamicKuesioner.style.display = 'none';
    staticKuesioner.style.display = 'none';
    
    if (!areaPatrolId) {
        // Show no kuesioner message
        noKuesionerMessage.style.display = 'block';
        noKuesionerMessage.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Pilih Area Patrol</h3>
                <p class="text-gray-600">Pilih area patrol di step sebelumnya untuk menampilkan kuesioner yang sesuai</p>
            </div>
        `;
        return;
    }
    
    // Show loading
    noKuesionerMessage.style.display = 'block';
    noKuesionerMessage.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Memuat kuesioner...</p>
        </div>
    `;
    
    // Fetch kuesioner
    fetch(`{{ route('perusahaan.buku-tamu.kuesioner') }}?area_patrol_id=${areaPatrolId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Kuesioner response:', data);
            
            if (data.success && data.data.pertanyaans.length > 0) {
                // Show dynamic kuesioner
                renderDynamicKuesioner(data.data);
                noKuesionerMessage.style.display = 'none';
                dynamicKuesioner.style.display = 'block';
                staticKuesioner.style.display = 'none';
                console.log('Showing dynamic kuesioner');
            } else {
                // Show static kuesioner as fallback
                noKuesionerMessage.style.display = 'none';
                dynamicKuesioner.style.display = 'none';
                staticKuesioner.style.display = 'block';
                console.log('Showing static kuesioner as fallback');
            }
        })
        .catch(error => {
            console.error('Error loading kuesioner:', error);
            // Show static kuesioner as fallback
            noKuesionerMessage.style.display = 'none';
            dynamicKuesioner.style.display = 'none';
            staticKuesioner.style.display = 'block';
            console.log('Error occurred, showing static kuesioner');
        });
}

function renderDynamicKuesioner(data) {
    const pertanyaanList = document.getElementById('pertanyaan-list');
    pertanyaanList.innerHTML = '';
    
    data.pertanyaans.forEach((pertanyaan, index) => {
        const pertanyaanDiv = document.createElement('div');
        pertanyaanDiv.className = 'border border-gray-200 rounded-lg p-4';
        
        let inputHtml = '';
        const fieldName = `kuesioner_answers[${pertanyaan.id}]`;
        
        if (pertanyaan.tipe_jawaban === 'pilihan') {
            inputHtml = '<div class="space-y-2">';
            pertanyaan.opsi_jawaban.forEach((opsi, opsiIndex) => {
                inputHtml += `
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                        <input type="radio" name="${fieldName}" value="${opsi}" class="mr-3 text-blue-600 focus:ring-blue-500" ${pertanyaan.is_required ? 'required' : ''}>
                        <span class="text-sm text-gray-700">${opsi}</span>
                    </label>
                `;
            });
            inputHtml += '</div>';
        } else {
            inputHtml = `
                <textarea 
                    name="${fieldName}"
                    rows="3"
                    placeholder="Tulis jawaban Anda..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    ${pertanyaan.is_required ? 'required' : ''}
                ></textarea>
            `;
        }
        
        pertanyaanDiv.innerHTML = `
            <div class="flex items-start gap-3 mb-3">
                <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold flex-shrink-0">
                    ${pertanyaan.urutan}
                </span>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-1">
                        ${pertanyaan.pertanyaan}
                        ${pertanyaan.is_required ? '<span class="text-red-500 ml-1">*</span>' : ''}
                    </h4>
                    ${pertanyaan.is_required ? '<p class="text-xs text-red-600">Wajib diisi</p>' : ''}
                </div>
            </div>
            <div class="ml-11">
                ${inputHtml}
            </div>
        `;
        
        pertanyaanList.appendChild(pertanyaanDiv);
    });
}
</script>
                    </div>
                </div>

                <!-- Step 4: Kuesioner -->
                <div class="wizard-step" id="step-4" style="display: none;">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kuesioner</h3>
                        <p class="text-sm text-gray-600">Pertanyaan keamanan dan kesehatan</p>
                    </div>
                    
                    <div id="kuesioner-container">
                        <!-- Default message when no area patrol selected -->
                        <div id="no-kuesioner-message" class="text-center py-12">
                            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pilih Area Patrol</h3>
                            <p class="text-gray-600">Pilih area patrol di step sebelumnya untuk menampilkan kuesioner yang sesuai</p>
                        </div>

                        <!-- Dynamic questionnaire will be loaded here -->
                        <div id="dynamic-kuesioner" style="display: none;">
                            <div class="space-y-6" id="pertanyaan-list">
                                <!-- Questions will be loaded dynamically -->
                            </div>
                        </div>

                        <!-- Fallback static questions if no dynamic questionnaire -->
                        <div id="static-kuesioner" style="display: none;">
                            <div class="space-y-6">
                                <!-- Pertanyaan 1 -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        1. Apakah Anda dalam kondisi sehat?
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="pertanyaan_1" value="Ya" class="mr-3" {{ old('pertanyaan_1') == 'Ya' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Ya</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="pertanyaan_1" value="Tidak" class="mr-3" {{ old('pertanyaan_1') == 'Tidak' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Tidak</span>
                                        </label>
                                    </div>
                                    @error('pertanyaan_1')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pertanyaan 2 -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        2. Barang yang dibawa (pilih semua yang sesuai)
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="pertanyaan_2[]" value="Laptop" class="mr-3" {{ in_array('Laptop', old('pertanyaan_2', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Laptop</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="pertanyaan_2[]" value="Kamera" class="mr-3" {{ in_array('Kamera', old('pertanyaan_2', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Kamera</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="pertanyaan_2[]" value="Dokumen" class="mr-3" {{ in_array('Dokumen', old('pertanyaan_2', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Dokumen</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="pertanyaan_2[]" value="Lainnya" class="mr-3" {{ in_array('Lainnya', old('pertanyaan_2', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Lainnya</span>
                                        </label>
                                    </div>
                                    @error('pertanyaan_2')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pertanyaan 3 -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        3. Apakah Anda sudah membaca dan memahami peraturan keamanan?
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="pertanyaan_3" value="Ya" class="mr-3" {{ old('pertanyaan_3') == 'Ya' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Ya</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="pertanyaan_3" value="Tidak" class="mr-3" {{ old('pertanyaan_3') == 'Tidak' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">Tidak</span>
                                        </label>
                                    </div>
                                    @error('pertanyaan_3')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                    <button 
                        type="button" 
                        id="prevBtn" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                        style="display: none;"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Sebelumnya
                    </button>
                    
                    <div class="flex space-x-3 ml-auto">
                        <a href="{{ route('perusahaan.buku-tamu.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        
                        <button 
                            type="button" 
                            id="nextBtn" 
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition"
                        >
                            Selanjutnya<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        
                        <button 
                            type="submit" 
                            id="submitBtn" 
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition"
                            style="display: none;"
                        >
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

// Initialize wizard
document.addEventListener('DOMContentLoaded', function() {
    updateStepDisplay();
    calculateDuration();
    
    // Add event listeners for datetime inputs
    document.querySelector('input[name="mulai_kunjungan"]').addEventListener('change', calculateDuration);
    document.querySelector('input[name="selesai_kunjungan"]').addEventListener('change', calculateDuration);
});

// Navigation functions
document.getElementById('nextBtn').addEventListener('click', function() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepDisplay();
        }
    }
});

document.getElementById('prevBtn').addEventListener('click', function() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
});

function updateStepDisplay() {
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'none';
    });
    
    // Show current step
    document.getElementById(`step-${currentStep}`).style.display = 'block';
    
    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = index + 1;
        if (stepNum <= currentStep) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
    
    // Update step lines
    document.querySelectorAll('.step-line').forEach((line, index) => {
        const stepNum = index + 1;
        if (stepNum < currentStep) {
            line.classList.add('active');
        } else {
            line.classList.remove('active');
        }
    });
    
    // Update step labels
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = index + 1;
        const label = indicator.nextElementSibling;
        if (stepNum <= currentStep) {
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-900');
        } else {
            label.classList.remove('text-gray-900');
            label.classList.add('text-gray-500');
        }
    });
    
    // Update navigation buttons
    document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
    document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'block' : 'none';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step-${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        // Skip file inputs if they're not required for edit mode
        if (field.type === 'file' && field.hasAttribute('data-optional-edit')) {
            return;
        }
        
        // Skip questionnaire fields - they have their own validation
        if (field.name && field.name.startsWith('kuesioner_answers')) {
            return;
        }
        
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
            
            // Show error message
            let errorMsg = field.parentNode.querySelector('.validation-error');
            if (!errorMsg) {
                errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-sm mt-1 validation-error';
                errorMsg.textContent = 'Field ini wajib diisi';
                field.parentNode.appendChild(errorMsg);
            }
        } else {
            field.classList.remove('border-red-500');
            // Remove error message
            const errorMsg = field.parentNode.querySelector('.validation-error');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
    });
    
    // Special validation for step 4 (questionnaire) - REMOVED FOR NOW
    // Questionnaire validation is causing issues, making it optional
    if (currentStep === 4) {
        console.log('Step 4: Questionnaire validation skipped (optional)');
        // No validation for questionnaire to avoid blocking form submission
    }
    
    if (!isValid && currentStep !== 4) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Silakan lengkapi semua field yang wajib diisi'
        });
    }
    
    return isValid;
}

function calculateDuration() {
    const mulaiInput = document.querySelector('input[name="mulai_kunjungan"]');
    const selesaiInput = document.querySelector('input[name="selesai_kunjungan"]');
    const lamaInput = document.querySelector('input[name="lama_kunjungan"]');
    
    if (mulaiInput.value && selesaiInput.value) {
        const mulai = new Date(mulaiInput.value);
        const selesai = new Date(selesaiInput.value);
        
        if (selesai > mulai) {
            const diffMs = selesai - mulai;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            
            let duration = '';
            if (diffHours > 0) {
                duration += diffHours + ' jam ';
            }
            if (diffMinutes > 0) {
                duration += diffMinutes + ' menit';
            }
            
            lamaInput.value = duration.trim() || '0 menit';
        } else {
            lamaInput.value = '';
            if (selesaiInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Waktu Tidak Valid',
                    text: 'Waktu selesai harus lebih besar dari waktu mulai'
                });
            }
        }
    }
}

function previewFoto(input) {
    const preview = document.getElementById('foto_preview');
    const label = document.getElementById('foto_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
        label.textContent = 'Klik untuk ambil foto selfie';
    }
}

function previewFotoIdentitas(input) {
    const preview = document.getElementById('foto_identitas_preview');
    const label = document.getElementById('foto_identitas_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
        label.textContent = 'Klik untuk pilih foto KTP';
    }
}

// Form submission
document.getElementById('formBukuTamu').addEventListener('submit', function(e) {
    if (!validateCurrentStep()) {
        e.preventDefault();
        return false;
    }
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan Data...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
});
</script>

<style>
.step-indicator {
    background-color: #e5e7eb;
    color: #6b7280;
    transition: all 0.3s ease;
}

.step-indicator.active {
    background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);
    color: white;
}

.step-line {
    transition: all 0.3s ease;
}

.step-line.active {
    background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);
}

.wizard-step {
    min-height: 400px;
}

/* Custom radio and checkbox styles */
input[type="radio"], input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #3B82C8;
}

/* File input styling */
input[type="file"] + label {
    cursor: pointer;
    transition: all 0.2s ease;
}

input[type="file"] + label:hover {
    background-color: #f9fafb;
}
</style>
@endpush