@extends('perusahaan.layouts.app')

@section('title', 'Input Data Tamu')
@section('page-title', 'Input Data Tamu')
@section('page-subtitle', 'Catat kunjungan tamu baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user-plus text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Input Data Tamu</h3>
                    <p class="text-sm text-gray-600">Masukkan informasi tamu yang berkunjung</p>
                </div>
            </div>

            <form action="{{ route('perusahaan.buku-tamu.store') }}" method="POST" enctype="multipart/form-data" id="formBukuTamu">
                @csrf
                
                <!-- Project Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            name="project_id"
                            id="project_select"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                            onchange="handleProjectChange()"
                        >
                            <option value="">Pilih Project</option>
                            @forelse($projects as $project)
                                <option value="{{ $project->id }}" 
                                    data-mode="{{ $project->guest_book_mode }}"
                                    data-questionnaire="{{ $project->enable_questionnaire ? '1' : '0' }}"
                                    {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @empty
                                <option value="" disabled>Belum ada project tersedia</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('project_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mode Display -->
                <div id="project-mode-display" class="hidden mb-6">
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-blue-900" id="mode-display">Mode Simple</p>
                                <p class="text-xs text-blue-700" id="mode-description">Form sederhana untuk input data tamu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Officer Selection -->
                <div id="security-selection" class="mb-6" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Petugas <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            name="bertemu"
                            id="security_select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                        >
                            <option value="">Pilih Nama Petugas</option>
                            <!-- Will be populated based on selected project -->
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('bertemu')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Area Selection -->
                <div id="area-selection" class="mb-6" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Area/Lokasi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            name="area_id"
                            id="area_select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                        >
                            <option value="">Pilih Area/Lokasi</option>
                            <!-- Will be populated based on selected project -->
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('area_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- POS Jaga Selection -->
                <div id="pos-jaga-selection" class="mb-6" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        POS Jaga <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text"
                            name="pos_jaga_search"
                            id="pos_jaga_search"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Cari atau tambah POS Jaga baru..."
                            autocomplete="off"
                            onkeyup="searchPosJaga(this.value)"
                            onfocus="showPosJagaDropdown()"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        
                        <!-- Dropdown untuk POS Jaga -->
                        <div id="pos_jaga_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            <div id="pos_jaga_list">
                                <!-- Will be populated dynamically -->
                            </div>
                            <div id="pos_jaga_add_new" class="border-t border-gray-200 p-3 bg-gray-50 hidden">
                                <button type="button" onclick="addNewPosJaga()" class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded flex items-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    <span>Tambah POS Jaga: "<span id="new_pos_name"></span>"</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden fields untuk POS Jaga -->
                    <input type="hidden" name="area_patrol_id" id="area_patrol_id_hidden">
                    <input type="hidden" name="pos_jaga_nama" id="pos_jaga_nama_hidden">
                    
                    <!-- Selected POS Jaga Display -->
                    <div id="selected_pos_jaga" class="mt-2 hidden">
                        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>
                                <div>
                                    <p class="text-sm font-medium text-green-900" id="selected_pos_name">-</p>
                                    <p class="text-xs text-green-700" id="selected_pos_area">-</p>
                                </div>
                            </div>
                            <button type="button" onclick="clearPosJagaSelection()" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    @error('area_patrol_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data Tamu Section -->
                <div id="data-tamu-section" style="display: none;">
                    <!-- Debug Panel -->
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h5 class="font-semibold text-yellow-800 mb-2">Debug Panel</h5>
                        <p class="text-sm text-yellow-700">Current Mode: <span id="debug-mode">-</span></p>
                        <button type="button" onclick="debugToggleForm()" class="mt-2 px-3 py-1 bg-yellow-600 text-white text-sm rounded">
                            Force Toggle Form
                        </button>
                    </div>
                    
                    <!-- Simple Mode Form -->
                    <div id="simple-form" class="hidden">
                        <div class="border-t border-gray-200 pt-6 mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Data Tamu - Mode Simple</h4>
                        </div>

                        <div class="space-y-6">
                            <!-- Nama Tamu -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Tamu <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="nama_tamu" 
                                    required
                                    value="{{ old('nama_tamu') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Masukkan nama lengkap tamu"
                                >
                                @error('nama_tamu')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Instansi/Perusahaan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Dari Instansi/Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="perusahaan_tamu" 
                                    required
                                    value="{{ old('perusahaan_tamu') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Masukkan nama instansi atau perusahaan"
                                >
                                @error('perusahaan_tamu')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Keperluan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keperluan <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="keperluan" 
                                    required
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Jelaskan maksud dan tujuan kunjungan"
                                >{{ old('keperluan') }}</textarea>
                                @error('keperluan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Foto Tamu -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Tamu <span class="text-red-500">*</span>
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
                                                <p class="text-sm text-gray-600" id="foto_label">Ambil foto tamu</p>
                                                <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimal 2MB</p>
                                            </div>
                                        </label>
                                        <div class="mt-2 hidden" id="foto_preview">
                                            <img class="w-24 h-24 object-cover rounded-lg border mx-auto" alt="Preview Tamu">
                                            <p class="text-xs text-green-600 mt-1 text-center">Preview foto tamu</p>
                                        </div>
                                    </div>
                                </div>
                                @error('foto')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Waktu Kunjungan -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                            <!-- Lama Kunjungan (Auto calculated) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Lama Kunjungan
                                </label>
                                <input 
                                    type="text" 
                                    name="lama_kunjungan" 
                                    readonly
                                    value="{{ old('lama_kunjungan') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none"
                                    placeholder="Akan dihitung otomatis"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- MIGAS Standard Form -->
                    <div id="migas-form" class="hidden">
                        <!-- Debug indicator -->
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <strong>MIGAS Standard Form Active</strong> - Form lengkap sesuai standar MIGAS
                        </div>
                        
                        <!-- Step 1: Data Diri -->
                        <div class="border-t border-gray-200 pt-6 mb-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">1</div>
                                <h4 class="text-lg font-semibold text-gray-900">Data Diri</h4>
                            </div>

                            <div class="space-y-6">
                                <!-- Nama -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="nama_tamu" 
                                        required
                                        value="{{ old('nama_tamu') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Masukkan nama lengkap sesuai KTP"
                                    >
                                    @error('nama_tamu')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- NIK -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="nik" 
                                        required
                                        maxlength="16"
                                        pattern="[0-9]{16}"
                                        value="{{ old('nik') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Masukkan 16 digit NIK"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
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
                                    <input 
                                        type="date" 
                                        name="tanggal_lahir" 
                                        required
                                        value="{{ old('tanggal_lahir') }}"
                                        max="{{ date('Y-m-d', strtotime('-17 years')) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
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
                                        placeholder="Masukkan alamat tempat tinggal lengkap"
                                    >{{ old('domisili') }}</textarea>
                                    @error('domisili')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Instansi/Perusahaan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Instansi / Perusahaan <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="perusahaan_tamu" 
                                        required
                                        value="{{ old('perusahaan_tamu') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Masukkan nama instansi atau perusahaan"
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
                                        placeholder="Masukkan jabatan di instansi/perusahaan"
                                    >
                                    @error('jabatan')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Foto KTP dan Foto Selfie -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                                    id="foto_identitas_migas"
                                                    required
                                                    accept="image/jpeg,image/png,image/jpg"
                                                    class="hidden"
                                                    onchange="previewFotoIdentitasMigas(this)"
                                                >
                                                <label for="foto_identitas_migas" class="cursor-pointer flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-center">
                                                    <div class="text-center">
                                                        <i class="fas fa-id-card text-gray-400 text-2xl mb-2"></i>
                                                        <p class="text-sm text-gray-600" id="foto_identitas_migas_label">Pilih foto KTP</p>
                                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimal 2MB</p>
                                                    </div>
                                                </label>
                                                <div class="mt-2 hidden" id="foto_identitas_migas_preview">
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
                                                    id="foto_migas"
                                                    required
                                                    accept="image/jpeg,image/png,image/jpg"
                                                    class="hidden"
                                                    onchange="previewFotoMigas(this)"
                                                >
                                                <label for="foto_migas" class="cursor-pointer flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-center">
                                                    <div class="text-center">
                                                        <i class="fas fa-camera text-gray-400 text-2xl mb-2"></i>
                                                        <p class="text-sm text-gray-600" id="foto_migas_label">Ambil foto selfie</p>
                                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimal 2MB</p>
                                                    </div>
                                                </label>
                                                <div class="mt-2 hidden" id="foto_migas_preview">
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
                        </div>

                        <!-- Step 2: Kontak Tamu -->
                        <div class="border-t border-gray-200 pt-6 mb-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">2</div>
                                <h4 class="text-lg font-semibold text-gray-900">Kontak Tamu</h4>
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
                                        placeholder="contoh@email.com"
                                    >
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- No WhatsApp -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        No WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="tel" 
                                        name="no_whatsapp" 
                                        required
                                        value="{{ old('no_whatsapp') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="08xxxxxxxxxx"
                                        pattern="[0-9+\-\s]+"
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
                                        placeholder="08xxxxxxxxxx"
                                        pattern="[0-9+\-\s]+"
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
                                        placeholder="Nama lengkap kontak darurat"
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
                                            <option value="">Pilih hubungan</option>
                                            <option value="Orang Tua" {{ old('kontak_darurat_hubungan') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                                            <option value="Suami/Istri" {{ old('kontak_darurat_hubungan') == 'Suami/Istri' ? 'selected' : '' }}>Suami/Istri</option>
                                            <option value="Anak" {{ old('kontak_darurat_hubungan') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                            <option value="Saudara" {{ old('kontak_darurat_hubungan') == 'Saudara' ? 'selected' : '' }}>Saudara</option>
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
                        <div class="border-t border-gray-200 pt-6 mb-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">3</div>
                                <h4 class="text-lg font-semibold text-gray-900">Data Kunjungan</h4>
                            </div>

                            <div class="space-y-6">
                                <!-- Maksud dan Tujuan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Maksud dan Tujuan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        name="keperluan" 
                                        required
                                        rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Jelaskan maksud dan tujuan kunjungan secara detail"
                                    >{{ old('keperluan') }}</textarea>
                                    @error('keperluan')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Lokasi yang Dituju -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Lokasi yang Dituju <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="lokasi_dituju" 
                                        required
                                        value="{{ old('lokasi_dituju') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Masukkan lokasi spesifik yang akan dikunjungi"
                                    >
                                    @error('lokasi_dituju')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Waktu Kunjungan -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                        <p class="text-xs text-gray-500 mt-1">Default: hari dan jam sekarang</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Akhir Kunjungan
                                        </label>
                                        <input 
                                            type="datetime-local" 
                                            name="selesai_kunjungan" 
                                            value="{{ old('selesai_kunjungan') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                        @error('selesai_kunjungan')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 mt-1">Opsional: biarkan kosong jika belum tahu, akan terisi saat checkout</p>
                                    </div>
                                </div>

                                <!-- Lama Kunjungan (Auto calculated) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Lama Kunjungan (Otomatis)
                                    </label>
                                    <input 
                                        type="text" 
                                        name="lama_kunjungan" 
                                        readonly
                                        value="{{ old('lama_kunjungan') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none"
                                        placeholder="Akan dihitung otomatis berdasarkan waktu mulai dan akhir"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Durasi akan dihitung otomatis jika waktu akhir diisi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="guest_book_mode" value="simple" id="guest_book_mode_input">
                <input type="hidden" name="enable_questionnaire" value="0" id="enable_questionnaire_input">

                <!-- Submit Button -->
                <div id="submit-section" class="flex justify-end space-x-3 pt-6 border-t border-gray-200" style="display: none;">
                    <a href="{{ route('perusahaan.buku-tamu.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    
                    <!-- Questionnaire Button (only show if enabled) -->
                    <button 
                        type="button" 
                        id="questionnaire-button"
                        onclick="openQuestionnaire()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition"
                        style="display: none;"
                    >
                        <i class="fas fa-clipboard-list mr-2"></i>Isi Kuesioner
                    </button>
                    
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                    >
                        <i class="fas fa-save mr-2"></i>Simpan Data Tamu
                    </button>
                </div>
            </form>
            
            <!-- Questionnaire Modal -->
            <div id="questionnaire-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between pb-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900" id="questionnaire-title">
                                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                Kuesioner Tamu
                            </h3>
                            <button type="button" onclick="closeQuestionnaire()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="py-4">
                            <div id="questionnaire-loading" class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
                                <p class="text-gray-600">Memuat kuesioner...</p>
                            </div>
                            
                            <div id="questionnaire-content" style="display: none;">
                                <div id="questionnaire-description" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-800"></p>
                                </div>
                                
                                <form id="questionnaire-form">
                                    <div id="questionnaire-questions">
                                        <!-- Questions will be loaded here -->
                                    </div>
                                </form>
                            </div>
                            
                            <div id="questionnaire-error" style="display: none;" class="text-center py-8">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-600 mb-2"></i>
                                <p class="text-red-600">Kuesioner tidak tersedia untuk area ini</p>
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeQuestionnaire()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Tutup
                            </button>
                            <button type="button" onclick="saveQuestionnaire()" id="save-questionnaire-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" style="display: none;">
                                <i class="fas fa-save mr-2"></i>Simpan Jawaban
                            </button>
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
    // Initialize datetime calculation
    const mulaiInput = document.querySelector('input[name="mulai_kunjungan"]');
    const selesaiInput = document.querySelector('input[name="selesai_kunjungan"]');
    
    if (mulaiInput) {
        mulaiInput.addEventListener('change', calculateDuration);
    }
    if (selesaiInput) {
        selesaiInput.addEventListener('change', calculateDuration);
    }
});

// Handle project selection change
function handleProjectChange() {
    const projectSelect = document.getElementById('project_select');
    const projectId = projectSelect.value;
    
    console.log('Project changed to:', projectId); // Debug log
    
    if (!projectId) {
        hideAllSections();
        return;
    }
    
    // Get project settings from option data attributes
    const selectedOption = projectSelect.options[projectSelect.selectedIndex];
    const guestBookMode = selectedOption.dataset.mode || 'simple';
    const enableQuestionnaire = selectedOption.dataset.questionnaire === '1';
    
    console.log('Project mode detected:', guestBookMode); // Debug log
    console.log('Enable questionnaire:', enableQuestionnaire); // Debug log
    
    // Update hidden fields
    document.getElementById('guest_book_mode_input').value = guestBookMode;
    document.getElementById('enable_questionnaire_input').value = enableQuestionnaire ? '1' : '0';
    
    // Update mode display
    updateModeDisplay(guestBookMode, enableQuestionnaire);
    
    // Load security officers for this project
    loadSecurityOfficers(projectId);
    
    // Show next section
    showSecuritySelection();
}

function updateModeDisplay(mode, questionnaire) {
    const modeDisplay = document.getElementById('mode-display');
    const modeDescription = document.getElementById('mode-description');
    const projectModeDisplay = document.getElementById('project-mode-display');
    
    if (mode === 'simple') {
        modeDisplay.textContent = 'Mode Simple';
        modeDescription.textContent = 'Form sederhana untuk input data tamu';
    } else if (mode === 'standard_migas' || mode === 'migas') {
        modeDisplay.textContent = 'Mode Standard MIGAS';
        modeDescription.textContent = 'Form lengkap sesuai standar MIGAS';
    } else {
        modeDisplay.textContent = 'Mode Simple';
        modeDescription.textContent = 'Form sederhana untuk input data tamu';
    }
    
    projectModeDisplay.classList.remove('hidden');
}

function hideAllSections() {
    document.getElementById('project-mode-display').classList.add('hidden');
    document.getElementById('security-selection').style.display = 'none';
    document.getElementById('area-selection').style.display = 'none';
    document.getElementById('pos-jaga-selection').style.display = 'none';
    document.getElementById('data-tamu-section').style.display = 'none';
    document.getElementById('submit-section').style.display = 'none';
}

function showSecuritySelection() {
    document.getElementById('security-selection').style.display = 'block';
}

function showAreaSelection() {
    document.getElementById('area-selection').style.display = 'block';
}

function showPosJagaSelection() {
    document.getElementById('pos-jaga-selection').style.display = 'block';
}

function showDataTamuSection() {
    const guestBookMode = document.getElementById('guest_book_mode_input').value;
    const enableQuestionnaire = document.getElementById('enable_questionnaire_input').value === '1';
    const simpleForm = document.getElementById('simple-form');
    const migasForm = document.getElementById('migas-form');
    const questionnaireButton = document.getElementById('questionnaire-button');
    
    console.log('showDataTamuSection called with mode:', guestBookMode); // Debug log
    console.log('Enable questionnaire:', enableQuestionnaire); // Debug log
    console.log('Simple form element:', simpleForm); // Debug log
    console.log('MIGAS form element:', migasForm); // Debug log
    
    // Update debug display
    updateDebugDisplay();
    
    document.getElementById('data-tamu-section').style.display = 'block';
    document.getElementById('submit-section').style.display = 'flex';
    
    // Show/hide questionnaire button based on project settings
    if (enableQuestionnaire && questionnaireButton) {
        questionnaireButton.style.display = 'block';
        console.log('Questionnaire button shown'); // Debug log
    } else if (questionnaireButton) {
        questionnaireButton.style.display = 'none';
        console.log('Questionnaire button hidden'); // Debug log
    }
    
    if (guestBookMode === 'migas' || guestBookMode === 'standard_migas') {
        // Show MIGAS form, hide simple form
        console.log('Showing MIGAS form'); // Debug log
        if (simpleForm) {
            simpleForm.style.display = 'none';
            simpleForm.classList.add('hidden');
        }
        if (migasForm) {
            migasForm.style.display = 'block';
            migasForm.classList.remove('hidden');
            console.log('MIGAS form should now be visible'); // Debug log
        }
        
        // Make MIGAS fields required
        setMigasFieldsRequired(true);
        setSimpleFieldsRequired(false);
    } else {
        // Show simple form, hide MIGAS form
        console.log('Showing Simple form'); // Debug log
        if (migasForm) {
            migasForm.style.display = 'none';
            migasForm.classList.add('hidden');
        }
        if (simpleForm) {
            simpleForm.style.display = 'block';
            simpleForm.classList.remove('hidden');
            console.log('Simple form should now be visible'); // Debug log
        }
        
        // Make simple fields required
        setSimpleFieldsRequired(true);
        setMigasFieldsRequired(false);
    }
}

function setSimpleFieldsRequired(required) {
    const simpleForm = document.getElementById('simple-form');
    if (!simpleForm) return;
    
    const fields = simpleForm.querySelectorAll('input, textarea, select');
    
    fields.forEach(field => {
        if (required) {
            // Only set required if it was originally required
            if (field.dataset.originalRequired !== 'false') {
                field.setAttribute('required', 'required');
            }
        } else {
            field.removeAttribute('required');
        }
    });
}

function setMigasFieldsRequired(required) {
    const migasForm = document.getElementById('migas-form');
    if (!migasForm) return;
    
    const fields = migasForm.querySelectorAll('input, textarea, select');
    
    fields.forEach(field => {
        if (required) {
            // Only set required if it was originally required
            if (field.dataset.originalRequired !== 'false') {
                field.setAttribute('required', 'required');
            }
        } else {
            field.removeAttribute('required');
        }
    });
}

// Load security officers based on project (only users with security role)
function loadSecurityOfficers(projectId) {
    const securitySelect = document.getElementById('security_select');
    
    // Clear existing options
    securitySelect.innerHTML = '<option value="">Memuat security officers...</option>';
    
    console.log('Loading security officers for project:', projectId);
    
    fetch(`/perusahaan/projects/${projectId}/security-officers`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            console.log('Security officers response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Security officers response data:', data);
            
            securitySelect.innerHTML = '<option value="">Pilih Security Officer</option>';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(security => {
                    const option = document.createElement('option');
                    option.value = security.nama_lengkap;
                    option.textContent = `${security.nama_lengkap} - ${security.jabatan?.nama || 'Security'}`;
                    securitySelect.appendChild(option);
                });
                console.log(`✅ Loaded ${data.data.length} security officers`);
            } else {
                securitySelect.innerHTML = '<option value="">Tidak ada security officer tersedia</option>';
                console.log('⚠️ No security officers found');
            }
        })
        .catch(error => {
            console.error('❌ Error loading security officers:', error);
            securitySelect.innerHTML = '<option value="">Error memuat security officers</option>';
        });
    
    // Add change event listener
    securitySelect.addEventListener('change', function() {
        if (this.value) {
            // Load areas for selected security officer
            loadAreasBySecurityOfficer(this.value);
            showAreaSelection();
        }
    });
}

// Load areas based on selected security officer (from karyawan_areas)
function loadAreasBySecurityOfficer(securityOfficerName) {
    const areaSelect = document.getElementById('area_select');
    const projectId = document.getElementById('project_select').value;
    
    // Clear existing options
    areaSelect.innerHTML = '<option value="">Memuat area...</option>';
    
    console.log('Loading areas for security officer:', securityOfficerName);
    
    fetch(`/perusahaan/security-officer/areas?security_officer=${encodeURIComponent(securityOfficerName)}&project_id=${projectId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            console.log('Areas response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Areas response data:', data);
            
            areaSelect.innerHTML = '<option value="">Pilih Area/Lokasi</option>';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    
                    let displayText = area.nama;
                    if (area.alamat) {
                        displayText += ' - ' + area.alamat;
                    }
                    if (area.is_primary) {
                        displayText += ' (Utama)';
                    }
                    
                    option.textContent = displayText;
                    areaSelect.appendChild(option);
                });
                console.log(`✅ Loaded ${data.data.length} areas for security officer`);
            } else {
                areaSelect.innerHTML = '<option value="">Tidak ada area tersedia untuk security officer ini</option>';
                console.log('⚠️ No areas found for security officer');
            }
        })
        .catch(error => {
            console.error('❌ Error loading areas:', error);
            areaSelect.innerHTML = '<option value="">Error memuat area</option>';
        });
    
    // Add change event listener
    areaSelect.addEventListener('change', function() {
        if (this.value) {
            loadPosJagaByArea(this.value);
            showPosJagaSelection();
        }
    });
}

// Calculate duration between start and end time
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
            
            lamaInput.value = `${diffHours} jam ${diffMinutes} menit`;
        } else {
            lamaInput.value = '';
        }
    }
}

// Preview foto identitas
function previewFotoIdentitas(input) {
    const preview = document.getElementById('foto_identitas_preview');
    const label = document.getElementById('foto_identitas_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = preview.querySelector('img');
            img.src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview foto tamu
function previewFoto(input) {
    const preview = document.getElementById('foto_preview');
    const label = document.getElementById('foto_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = preview.querySelector('img');
            img.src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview foto identitas MIGAS
function previewFotoIdentitasMigas(input) {
    const preview = document.getElementById('foto_identitas_migas_preview');
    const label = document.getElementById('foto_identitas_migas_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = preview.querySelector('img');
            img.src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview foto tamu MIGAS
function previewFotoMigas(input) {
    const preview = document.getElementById('foto_migas_preview');
    const label = document.getElementById('foto_migas_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = preview.querySelector('img');
            img.src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Debug function to manually toggle form
function debugToggleForm() {
    const currentMode = document.getElementById('guest_book_mode_input').value;
    const newMode = (currentMode === 'simple') ? 'standard_migas' : 'simple';
    
    console.log('Debug toggle: changing from', currentMode, 'to', newMode);
    
    document.getElementById('guest_book_mode_input').value = newMode;
    document.getElementById('debug-mode').textContent = newMode;
    
    showDataTamuSection();
}

// Update debug display
function updateDebugDisplay() {
    const mode = document.getElementById('guest_book_mode_input').value;
    const debugElement = document.getElementById('debug-mode');
    if (debugElement) {
        debugElement.textContent = mode;
    }
}

// Questionnaire functions
let currentQuestionnaire = null;

function openQuestionnaire() {
    const modal = document.getElementById('questionnaire-modal');
    const projectId = document.getElementById('project_select').value;
    const areaId = document.getElementById('area_select').value;
    
    if (!projectId || !areaId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih project dan area terlebih dahulu'
        });
        return;
    }
    
    modal.style.display = 'block';
    loadQuestionnaire(projectId, areaId);
}

function closeQuestionnaire() {
    const modal = document.getElementById('questionnaire-modal');
    modal.style.display = 'none';
    
    // Reset modal content
    document.getElementById('questionnaire-loading').style.display = 'block';
    document.getElementById('questionnaire-content').style.display = 'none';
    document.getElementById('questionnaire-error').style.display = 'none';
    document.getElementById('save-questionnaire-btn').style.display = 'none';
}

function loadQuestionnaire(projectId, areaId) {
    console.log('Loading questionnaire for project:', projectId, 'area:', areaId);
    
    // Show loading
    document.getElementById('questionnaire-loading').style.display = 'block';
    document.getElementById('questionnaire-content').style.display = 'none';
    document.getElementById('questionnaire-error').style.display = 'none';
    
    // Load questionnaire from API
    fetch(`/perusahaan/buku-tamu/kuesioner-by-area?project_id=${projectId}&area_id=${areaId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Questionnaire response:', data);
        
        if (data.success && data.data) {
            currentQuestionnaire = data.data;
            renderQuestionnaire(data.data);
        } else {
            showQuestionnaireError();
        }
    })
    .catch(error => {
        console.error('Error loading questionnaire:', error);
        showQuestionnaireError();
    });
}

function renderQuestionnaire(questionnaire) {
    // Hide loading, show content
    document.getElementById('questionnaire-loading').style.display = 'none';
    document.getElementById('questionnaire-content').style.display = 'block';
    document.getElementById('save-questionnaire-btn').style.display = 'block';
    
    // Set title and description
    document.getElementById('questionnaire-title').innerHTML = 
        `<i class="fas fa-clipboard-list mr-2 text-green-600"></i>${questionnaire.judul}`;
    
    const descElement = document.querySelector('#questionnaire-description p');
    if (descElement) {
        descElement.textContent = questionnaire.deskripsi || 'Silakan jawab pertanyaan berikut dengan lengkap dan jujur.';
    }
    
    // Render questions
    const questionsContainer = document.getElementById('questionnaire-questions');
    questionsContainer.innerHTML = '';
    
    if (questionnaire.pertanyaans && questionnaire.pertanyaans.length > 0) {
        questionnaire.pertanyaans.forEach((pertanyaan, index) => {
            const questionDiv = createQuestionElement(pertanyaan, index);
            questionsContainer.appendChild(questionDiv);
        });
    }
}

function createQuestionElement(pertanyaan, index) {
    const div = document.createElement('div');
    div.className = 'mb-6';
    
    let inputHtml = '';
    const questionId = `question_${pertanyaan.id}`;
    
    // Create input based on question type
    switch (pertanyaan.tipe_jawaban) {
        case 'text':
            inputHtml = `
                <div class="mt-3">
                    <input type="text" 
                           id="${questionId}" 
                           name="answers[${pertanyaan.id}]"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan jawaban Anda"
                           ${pertanyaan.is_required ? 'required' : ''}>
                </div>
            `;
            break;
            
        case 'textarea':
            inputHtml = `
                <div class="mt-3">
                    <textarea id="${questionId}" 
                              name="answers[${pertanyaan.id}]"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                              placeholder="Tulis jawaban Anda..."
                              ${pertanyaan.is_required ? 'required' : ''}></textarea>
                </div>
            `;
            break;
            
        case 'pilihan':
        case 'radio':
            if (pertanyaan.opsi_jawaban && pertanyaan.opsi_jawaban.length > 0) {
                inputHtml = `
                    <div class="mt-3 space-y-3">
                        ${pertanyaan.opsi_jawaban.map((option, optIndex) => `
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" 
                                       name="answers[${pertanyaan.id}]" 
                                       value="${option}"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                       ${pertanyaan.is_required ? 'required' : ''}>
                                <span class="ml-3 text-sm font-medium text-gray-700">${option}</span>
                            </label>
                        `).join('')}
                    </div>
                `;
            }
            break;
            
        case 'checkbox':
            if (pertanyaan.opsi_jawaban && pertanyaan.opsi_jawaban.length > 0) {
                inputHtml = `
                    <div class="mt-3 space-y-3">
                        ${pertanyaan.opsi_jawaban.map((option, optIndex) => `
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       name="answers[${pertanyaan.id}][]" 
                                       value="${option}"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm font-medium text-gray-700">${option}</span>
                            </label>
                        `).join('')}
                    </div>
                `;
            }
            break;
            
        case 'select':
            if (pertanyaan.opsi_jawaban && pertanyaan.opsi_jawaban.length > 0) {
                inputHtml = `
                    <div class="mt-3">
                        <select id="${questionId}" 
                                name="answers[${pertanyaan.id}]"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                ${pertanyaan.is_required ? 'required' : ''}>
                            <option value="">Pilih jawaban</option>
                            ${pertanyaan.opsi_jawaban.map(option => `
                                <option value="${option}">${option}</option>
                            `).join('')}
                        </select>
                    </div>
                `;
            }
            break;
            
        default:
            inputHtml = `
                <div class="mt-3">
                    <input type="text" 
                           id="${questionId}" 
                           name="answers[${pertanyaan.id}]"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan jawaban Anda"
                           ${pertanyaan.is_required ? 'required' : ''}>
                </div>
            `;
    }
    
    div.innerHTML = `
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-semibold mr-3">
                    ${index + 1}
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-900 mb-1">
                        ${pertanyaan.pertanyaan}
                        ${pertanyaan.is_required ? '<span class="text-red-500 ml-1">*</span>' : ''}
                    </label>
                    ${pertanyaan.is_required ? '<p class="text-xs text-red-500 mb-2">Wajib diisi</p>' : ''}
                    ${inputHtml}
                </div>
            </div>
        </div>
    `;
    
    return div;
}

function showQuestionnaireError() {
    document.getElementById('questionnaire-loading').style.display = 'none';
    document.getElementById('questionnaire-content').style.display = 'none';
    document.getElementById('questionnaire-error').style.display = 'block';
    document.getElementById('save-questionnaire-btn').style.display = 'none';
}

function saveQuestionnaire() {
    const form = document.getElementById('questionnaire-form');
    const formData = new FormData(form);
    
    // Convert FormData to object for easier handling
    const answers = {};
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('answers[')) {
            const questionId = key.match(/answers\[(\d+)\]/)[1];
            if (answers[questionId]) {
                // Handle multiple values (checkboxes)
                if (Array.isArray(answers[questionId])) {
                    answers[questionId].push(value);
                } else {
                    answers[questionId] = [answers[questionId], value];
                }
            } else {
                answers[questionId] = value;
            }
        }
    }
    
    console.log('Questionnaire answers:', answers);
    
    // Store answers in hidden input for form submission
    const answersInput = document.createElement('input');
    answersInput.type = 'hidden';
    answersInput.name = 'kuesioner_answers';
    answersInput.value = JSON.stringify(answers);
    
    // Remove existing answers input if any
    const existingInput = document.querySelector('input[name="kuesioner_answers"]');
    if (existingInput) {
        existingInput.remove();
    }
    
    // Add to main form
    document.getElementById('formBukuTamu').appendChild(answersInput);
    
    // Close modal
    closeQuestionnaire();
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Jawaban kuesioner telah disimpan',
        timer: 2000,
        showConfirmButton: false
    });
}
let posJagaData = [];
let selectedAreaId = null;

// Load POS Jaga based on selected area
function loadPosJagaByArea(areaId) {
    selectedAreaId = areaId;
    
    fetch(`/perusahaan/area-patrols/by-area?area_id=${areaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                posJagaData = data.data;
                console.log('POS Jaga loaded:', posJagaData);
            } else {
                posJagaData = [];
                console.log('No POS Jaga found for area:', areaId);
            }
        })
        .catch(error => {
            console.error('Error loading POS Jaga:', error);
            posJagaData = [];
        });
}

// Search POS Jaga
function searchPosJaga(query) {
    const dropdown = document.getElementById('pos_jaga_dropdown');
    const list = document.getElementById('pos_jaga_list');
    const addNew = document.getElementById('pos_jaga_add_new');
    const newPosName = document.getElementById('new_pos_name');
    
    // Clear previous results
    list.innerHTML = '';
    
    if (!query.trim()) {
        // Show all POS Jaga if no query
        if (posJagaData.length > 0) {
            posJagaData.forEach(pos => {
                const item = createPosJagaItem(pos);
                list.appendChild(item);
            });
            addNew.classList.add('hidden');
        } else {
            list.innerHTML = '<div class="p-3 text-sm text-gray-500">Belum ada POS Jaga di area ini</div>';
            addNew.classList.remove('hidden');
            newPosName.textContent = query;
        }
    } else {
        // Filter POS Jaga based on query
        const filtered = posJagaData.filter(pos => 
            pos.nama.toLowerCase().includes(query.toLowerCase())
        );
        
        if (filtered.length > 0) {
            filtered.forEach(pos => {
                const item = createPosJagaItem(pos);
                list.appendChild(item);
            });
        } else {
            list.innerHTML = '<div class="p-3 text-sm text-gray-500">Tidak ada POS Jaga yang cocok</div>';
        }
        
        // Show add new option
        addNew.classList.remove('hidden');
        newPosName.textContent = query;
    }
    
    dropdown.classList.remove('hidden');
}

// Create POS Jaga item element
function createPosJagaItem(pos) {
    const div = document.createElement('div');
    div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
    div.onclick = () => selectPosJaga(pos);
    
    div.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-900">${pos.nama}</p>
                <p class="text-xs text-gray-500">${pos.deskripsi || 'Tidak ada deskripsi'}</p>
            </div>
            <i class="fas fa-map-marker-alt text-gray-400"></i>
        </div>
    `;
    
    return div;
}

// Select POS Jaga
function selectPosJaga(pos) {
    // Update hidden fields
    document.getElementById('area_patrol_id_hidden').value = pos.id;
    document.getElementById('pos_jaga_nama_hidden').value = pos.nama;
    
    // Update search input
    document.getElementById('pos_jaga_search').value = pos.nama;
    
    // Show selected display
    const selectedDisplay = document.getElementById('selected_pos_jaga');
    const selectedName = document.getElementById('selected_pos_name');
    const selectedArea = document.getElementById('selected_pos_area');
    
    selectedName.textContent = pos.nama;
    selectedArea.textContent = pos.deskripsi || 'Tidak ada deskripsi';
    selectedDisplay.classList.remove('hidden');
    
    // Hide dropdown
    hidePosJagaDropdown();
    
    // Show data tamu section
    showDataTamuSection();
}

// Show POS Jaga dropdown
function showPosJagaDropdown() {
    const query = document.getElementById('pos_jaga_search').value;
    searchPosJaga(query);
}

// Hide POS Jaga dropdown
function hidePosJagaDropdown() {
    document.getElementById('pos_jaga_dropdown').classList.add('hidden');
}

// Clear POS Jaga selection
function clearPosJagaSelection() {
    document.getElementById('area_patrol_id_hidden').value = '';
    document.getElementById('pos_jaga_nama_hidden').value = '';
    document.getElementById('pos_jaga_search').value = '';
    document.getElementById('selected_pos_jaga').classList.add('hidden');
    
    // Hide data tamu section
    document.getElementById('data-tamu-section').style.display = 'none';
    document.getElementById('submit-section').style.display = 'none';
}

// Add new POS Jaga
function addNewPosJaga() {
    const name = document.getElementById('pos_jaga_search').value.trim();
    
    if (!name) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Masukkan nama POS Jaga',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (!selectedAreaId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih area terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Get project ID
    const projectId = document.getElementById('project_select').value;
    
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih project terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Menambah POS Jaga...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request to create new POS Jaga
    fetch('/perusahaan/area-patrols', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            nama: name,
            project_id: projectId,
            area_id: selectedAreaId,
            deskripsi: `POS Jaga di area ${document.getElementById('area_select').options[document.getElementById('area_select').selectedIndex].text}`,
            is_active: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add to local data
            const newPos = {
                id: data.data.id,
                nama: data.data.nama,
                deskripsi: data.data.deskripsi
            };
            
            posJagaData.push(newPos);
            
            // Select the new POS Jaga
            selectPosJaga(newPos);
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `POS Jaga "${name}" berhasil ditambahkan`,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Gagal menambah POS Jaga'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menambah POS Jaga'
        });
    });
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    const searchInput = document.getElementById('pos_jaga_search');
    const dropdown = document.getElementById('pos_jaga_dropdown');
    
    if (searchInput && dropdown && !searchInput.contains(event.target) && !dropdown.contains(event.target)) {
        hidePosJagaDropdown();
    }
});
</script>
@endpush