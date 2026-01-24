@extends('perusahaan.layouts.app')

@section('title', 'Tambah Patroli Mandiri')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Patroli Mandiri</h1>
            <nav class="text-sm text-gray-600 mt-1">
                <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="hover:text-blue-600">Patroli Mandiri</a>
                <span class="mx-2">/</span>
                <span>Tambah</span>
            </nav>
        </div>
        <div>
            <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Form Patroli Mandiri</h3>
                </div>
                <div class="p-6">
                    <form id="patroliMandiriForm" action="{{ route('perusahaan.patroli-mandiri.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Project -->
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror" 
                                        id="project_id" name="project_id" required>
                                    <option value="">Pilih Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Area Patrol -->
                            <div>
                                <label for="area_patrol_id" class="block text-sm font-medium text-gray-700 mb-2">Area Patrol</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('area_patrol_id') border-red-500 @enderror" 
                                        id="area_patrol_id" name="area_patrol_id">
                                    <option value="">Pilih Area (Opsional)</option>
                                </select>
                                @error('area_patrol_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Petugas -->
                            <div>
                                <label for="petugas_id" class="block text-sm font-medium text-gray-700 mb-2">Petugas <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('petugas_id') border-red-500 @enderror" 
                                        id="petugas_id" name="petugas_id" required>
                                    <option value="">Pilih Petugas</option>
                                    @foreach($petugas as $user)
                                        <option value="{{ $user->id }}" {{ old('petugas_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('petugas_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Lokasi -->
                            <div>
                                <label for="nama_lokasi" class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_lokasi') border-red-500 @enderror" 
                                        id="nama_lokasi_select" name="nama_lokasi_select">
                                    <option value="">Pilih atau ketik nama lokasi baru</option>
                                </select>
                                <input type="hidden" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi') }}" required>
                                @error('nama_lokasi')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <!-- Koordinat GPS -->
                            <div>
                                <label for="koordinat" class="block text-sm font-medium text-gray-700 mb-2">Koordinat GPS <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('koordinat') border-red-500 @enderror" 
                                       id="koordinat" name="koordinat" value="{{ old('koordinat') }}" 
                                       placeholder="Contoh: -6.200000, 106.800000 (copy dari Google Maps)" required>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Copy koordinat dari Google Maps (format: latitude, longitude)
                                </p>
                                @error('koordinat')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @error('latitude')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @error('longitude')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Hidden inputs for latitude and longitude -->
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        </div>

                        <!-- Status Lokasi -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Lokasi <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 {{ old('status_lokasi') == 'aman' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}" onclick="selectStatus('aman')">
                                    <input type="radio" name="status_lokasi" id="status_aman" value="aman" 
                                           {{ old('status_lokasi') == 'aman' ? 'checked' : '' }} required class="sr-only">
                                    <div class="flex items-center">
                                        <i class="fas fa-shield-alt text-green-600 text-xl mr-3"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">Aman</div>
                                            <div class="text-sm text-gray-500">Kondisi lokasi normal dan aman</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 {{ old('status_lokasi') == 'tidak_aman' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}" onclick="selectStatus('tidak_aman')">
                                    <input type="radio" name="status_lokasi" id="status_tidak_aman" value="tidak_aman" 
                                           {{ old('status_lokasi') == 'tidak_aman' ? 'checked' : '' }} required class="sr-only">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">Tidak Aman</div>
                                            <div class="text-sm text-gray-500">Ada kendala atau masalah</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('status_lokasi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Kendala (Hidden by default) -->
                        <div id="jenisKendalaSection" class="mb-4" style="display: {{ old('status_lokasi') == 'tidak_aman' ? 'block' : 'none' }};">
                            <label for="jenis_kendala" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kendala <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kendala') border-red-500 @enderror" 
                                    id="jenis_kendala_select" name="jenis_kendala_select">
                                <option value="">Pilih atau ketik jenis kendala baru</option>
                            </select>
                            <input type="hidden" id="jenis_kendala" name="jenis_kendala" value="{{ old('jenis_kendala') }}">
                            @error('jenis_kendala')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi Kendala -->
                        <div id="deskripsiKendalaSection" class="mb-4" style="display: {{ old('status_lokasi') == 'tidak_aman' ? 'block' : 'none' }};">
                            <label for="deskripsi_kendala" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kendala</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('deskripsi_kendala') border-red-500 @enderror" 
                                      id="deskripsi_kendala" name="deskripsi_kendala" rows="3" 
                                      placeholder="Jelaskan detail kendala yang ditemukan...">{{ old('deskripsi_kendala') }}</textarea>
                            @error('deskripsi_kendala')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan Petugas -->
                        <div class="mb-4">
                            <label for="catatan_petugas" class="block text-sm font-medium text-gray-700 mb-2">Catatan Petugas</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan_petugas') border-red-500 @enderror" 
                                      id="catatan_petugas" name="catatan_petugas" rows="3" 
                                      placeholder="Catatan tambahan dari petugas...">{{ old('catatan_petugas') }}</textarea>
                            @error('catatan_petugas')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tindakan yang Diambil -->
                        <div id="tindakanSection" class="mb-4" style="display: {{ old('status_lokasi') == 'tidak_aman' ? 'block' : 'none' }};">
                            <label for="tindakan_yang_diambil" class="block text-sm font-medium text-gray-700 mb-2">Tindakan yang Diambil</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tindakan_yang_diambil') border-red-500 @enderror" 
                                      id="tindakan_yang_diambil" name="tindakan_yang_diambil" rows="3" 
                                      placeholder="Jelaskan tindakan yang sudah diambil...">{{ old('tindakan_yang_diambil') }}</textarea>
                            @error('tindakan_yang_diambil')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Foto Lokasi -->
                        <div class="mb-4">
                            <label for="foto_lokasi" class="block text-sm font-medium text-gray-700 mb-2">Foto Lokasi <span class="text-red-500">*</span></label>
                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('foto_lokasi') border-red-500 @enderror" 
                                   id="foto_lokasi" name="foto_lokasi" accept="image/*" required>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                            @error('foto_lokasi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <div id="fotoLokasiPreview" class="mt-2 hidden">
                                <img id="fotoLokasiImg" src="" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        </div>

                        <!-- Foto Kendala -->
                        <div id="fotoKendalaSection" class="mb-6" style="display: {{ old('status_lokasi') == 'tidak_aman' ? 'block' : 'none' }};">
                            <label for="foto_kendala" class="block text-sm font-medium text-gray-700 mb-2">Foto Kendala</label>
                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('foto_kendala') border-red-500 @enderror" 
                                   id="foto_kendala" name="foto_kendala" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                            @error('foto_kendala')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <div id="fotoKendalaPreview" class="mt-2 hidden">
                                <img id="fotoKendalaImg" src="" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save mr-2"></i>Simpan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Panduan Pengisian</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <h4 class="font-medium text-green-600 mb-2">
                            <i class="fas fa-shield-alt mr-2"></i>Status Aman
                        </h4>
                        <p class="text-sm text-gray-600">
                            Pilih jika kondisi lokasi normal dan tidak ada masalah. Hanya perlu foto lokasi sebagai dokumentasi.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="font-medium text-red-600 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Status Tidak Aman
                        </h4>
                        <p class="text-sm text-gray-600">
                            Pilih jika ada kendala atau masalah. Wajib isi jenis kendala dan disarankan upload foto kendala.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-medium text-purple-600 mb-2">
                            <i class="fas fa-users mr-2"></i>Petugas & Lokasi
                        </h4>
                        <p class="text-sm text-gray-600 mb-2">
                            Gunakan fitur pencarian untuk memilih petugas dan lokasi dengan mudah.
                        </p>
                        <div class="bg-purple-50 border border-purple-200 rounded p-2">
                            <p class="text-xs text-purple-700 font-medium">Tips:</p>
                            <p class="text-xs text-purple-600">• Ketik nama petugas untuk mencari</p>
                            <p class="text-xs text-purple-600">• Ketik nama lokasi atau buat baru</p>
                            <p class="text-xs text-purple-600">• Ketik jenis kendala atau buat baru</p>
                            <p class="text-xs text-purple-600">• Area akan muncul setelah pilih project</p>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-600 mr-2 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Catatan:</p>
                                <p class="text-sm text-yellow-700">Semua field yang bertanda (*) wajib diisi. Foto lokasi selalu wajib untuk dokumentasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    initializeSelect2();
    
    // Load areas if project is already selected (for old input)
    const projectId = document.getElementById('project_id').value;
    if (projectId) {
        loadAreasByProject(projectId);
    }
    
    // Initialize koordinat input if latitude and longitude exist
    const lat = document.getElementById('latitude').value;
    const lng = document.getElementById('longitude').value;
    if (lat && lng) {
        document.getElementById('koordinat').value = `${lat}, ${lng}`;
    }
});

function setupEventListeners() {
    // Project change
    document.getElementById('project_id').addEventListener('change', function() {
        loadAreasByProject(this.value);
    });

    // Koordinat input processing
    document.getElementById('koordinat').addEventListener('input', function() {
        processKoordinatInput(this.value);
    });

    // File inputs for preview
    document.getElementById('foto_lokasi').addEventListener('change', function() {
        previewImage(this, 'fotoLokasiPreview', 'fotoLokasiImg');
    });

    document.getElementById('foto_kendala').addEventListener('change', function() {
        previewImage(this, 'fotoKendalaPreview', 'fotoKendalaImg');
    });
}

function selectStatus(status) {
    // Update radio buttons
    document.getElementById('status_aman').checked = (status === 'aman');
    document.getElementById('status_tidak_aman').checked = (status === 'tidak_aman');
    
    // Update visual appearance
    const amanDiv = document.getElementById('status_aman').closest('div');
    const tidakAmanDiv = document.getElementById('status_tidak_aman').closest('div');
    
    if (status === 'aman') {
        amanDiv.classList.add('border-green-500', 'bg-green-50');
        amanDiv.classList.remove('border-gray-300');
        tidakAmanDiv.classList.remove('border-red-500', 'bg-red-50');
        tidakAmanDiv.classList.add('border-gray-300');
    } else {
        tidakAmanDiv.classList.add('border-red-500', 'bg-red-50');
        tidakAmanDiv.classList.remove('border-gray-300');
        amanDiv.classList.remove('border-green-500', 'bg-green-50');
        amanDiv.classList.add('border-gray-300');
    }
    
    // Toggle kendala fields
    toggleKendalaFields(status === 'tidak_aman');
    updateFormValidation(status === 'tidak_aman');
}

function toggleKendalaFields(show) {
    const sections = ['jenisKendalaSection', 'deskripsiKendalaSection', 'tindakanSection', 'fotoKendalaSection'];
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        section.style.display = show ? 'block' : 'none';
    });
}

function updateFormValidation(isNotSafe) {
    const jenisKendalaSelect = document.getElementById('jenis_kendala');
    jenisKendalaSelect.required = isNotSafe;
    
    if (!isNotSafe) {
        jenisKendalaSelect.value = '';
    }
}

async function loadAreasByProject(projectId) {
    const select = document.getElementById('area_patrol_id');
    select.innerHTML = '<option value="">Loading...</option>';
    
    if (!projectId) {
        select.innerHTML = '<option value="">Pilih Area (Opsional)</option>';
        return;
    }

    try {
        console.log('Loading areas for project:', projectId);
        const response = await fetch(`{{ url('perusahaan/patroli-mandiri-areas') }}/${projectId}`);
        const data = await response.json();
        
        console.log('Areas response:', data);
        
        select.innerHTML = '<option value="">Pilih Area (Opsional)</option>';
        
        if (data.success && data.data.length > 0) {
            data.data.forEach(area => {
                const option = document.createElement('option');
                option.value = area.id;
                option.textContent = area.nama;
                select.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Tidak ada area untuk project ini';
            option.disabled = true;
            select.appendChild(option);
        }
    } catch (error) {
        console.error('Error loading areas:', error);
        select.innerHTML = '<option value="">Error loading areas</option>';
    }
}

function initializeSelect2() {
    // Initialize Petugas Select2
    $('#petugas_id').select2({
        placeholder: 'Cari dan pilih petugas...',
        allowClear: true,
        width: '100%'
    });

    // Initialize Nama Lokasi Select2 with tagging
    $('#nama_lokasi_select').select2({
        placeholder: 'Ketik untuk mencari atau tambah lokasi baru...',
        allowClear: true,
        tags: true,
        width: '100%',
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term + ' (Lokasi Baru)',
                newTag: true
            };
        },
        ajax: {
            url: '{{ route("perusahaan.patroli-mandiri.search-locations") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 10) < data.total
                    }
                };
            },
            cache: true
        }
    });

    // Initialize Jenis Kendala Select2 with tagging
    $('#jenis_kendala_select').select2({
        placeholder: 'Ketik untuk mencari atau tambah jenis kendala baru...',
        allowClear: true,
        tags: true,
        width: '100%',
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term.toLowerCase().replace(/\s+/g, '_'),
                text: term + ' (Jenis Baru)',
                newTag: true
            };
        },
        ajax: {
            url: '{{ route("perusahaan.patroli-mandiri.search-jenis-kendala") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 10) < data.total
                    }
                };
            },
            cache: true
        }
    });

    // Handle nama lokasi selection
    $('#nama_lokasi_select').on('select2:select', function (e) {
        const data = e.params.data;
        document.getElementById('nama_lokasi').value = data.text.replace(' (Lokasi Baru)', '');
    });

    // Handle nama lokasi clear
    $('#nama_lokasi_select').on('select2:clear', function (e) {
        document.getElementById('nama_lokasi').value = '';
    });

    // Handle jenis kendala selection
    $('#jenis_kendala_select').on('select2:select', function (e) {
        const data = e.params.data;
        let value = data.id;
        if (data.newTag) {
            // For new tags, use the processed value (lowercase with underscores)
            value = data.id;
        }
        document.getElementById('jenis_kendala').value = value;
    });

    // Handle jenis kendala clear
    $('#jenis_kendala_select').on('select2:clear', function (e) {
        document.getElementById('jenis_kendala').value = '';
    });
}

function processKoordinatInput(koordinatValue) {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Clear previous values
    latInput.value = '';
    lngInput.value = '';
    
    if (!koordinatValue.trim()) return;
    
    // Remove extra spaces and split by comma
    const parts = koordinatValue.trim().split(',');
    
    if (parts.length === 2) {
        const lat = parseFloat(parts[0].trim());
        const lng = parseFloat(parts[1].trim());
        
        // Validate latitude and longitude ranges
        if (!isNaN(lat) && !isNaN(lng) && 
            lat >= -90 && lat <= 90 && 
            lng >= -180 && lng <= 180) {
            latInput.value = lat;
            lngInput.value = lng;
        }
    }
}

function previewImage(input, previewId, imgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(imgId).src = e.target.result;
            document.getElementById(previewId).classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush