@extends('perusahaan.layouts.app')

@section('title', 'Input Data Tamu')
@section('page-title', 'Input Data Tamu')
@section('page-subtitle', 'Catat kunjungan tamu baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Project Selection (Before Form) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Pilih Project</h3>
                    <p class="text-sm text-gray-600">Pilih project untuk menentukan mode buku tamu</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Project Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="project_select_main"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                            onchange="handleProjectChange()"
                        >
                            <option value="">Pilih Project</option>
                            @forelse($projects as $project)
                                <option value="{{ $project->id }}" 
                                    data-mode="{{ $project->guest_book_mode }}"
                                    data-questionnaire="{{ $project->enable_questionnaire ? '1' : '0' }}"
                                    {{ $loop->first ? 'selected' : '' }}>
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
                </div>

                <!-- Area Selection -->
                <div id="area-selection" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Area/Lokasi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="area_select_main"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                            onchange="handleAreaChange()"
                        >
                            <option value="">Pilih Area/Lokasi</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mode Display -->
            <div id="project-mode-display" class="hidden mt-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900" id="mode-display-main">Standard MIGAS</p>
                            <p class="text-xs text-gray-600" id="mode-description-main">Form lengkap dengan semua field sesuai standar MIGAS</p>
                        </div>
                    </div>
                    <div class="mt-2" id="questionnaire-status-main">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                            <i class="fas fa-clipboard-list mr-1"></i>Kuesioner Aktif
                        </span>
                    </div>
                </div>
                
                <!-- Debug Panel -->
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm font-medium text-yellow-800 mb-2">Debug Mode:</p>
                    <div class="flex gap-2 flex-wrap">
                        <button type="button" onclick="simpleTest()" class="px-3 py-1 bg-green-500 text-white text-xs rounded">Simple Test</button>
                        <button type="button" onclick="showFormNow()" class="px-3 py-1 bg-blue-500 text-white text-xs rounded">Show Form</button>
                        <button type="button" onclick="console.log('Current state:', {currentStep, totalSteps: window.totalSteps, projectId: document.getElementById('project_id_input').value, mode: document.getElementById('guest_book_mode_input').value, questionnaire: document.getElementById('enable_questionnaire_input').value})" class="px-3 py-1 bg-gray-500 text-white text-xs rounded">Log State</button>
                        <button type="button" onclick="directShowForm()" class="px-3 py-1 bg-red-500 text-white text-xs rounded">DIRECT SHOW</button>
                        <button type="button" onclick="testQuestionnaire()" class="px-3 py-1 bg-purple-500 text-white text-xs rounded">Test Questionnaire</button>
                        <button type="button" onclick="showQuestionnaireDirectly()" class="px-3 py-1 bg-pink-500 text-white text-xs rounded">Show Q Direct</button>
                        <button type="button" onclick="navigateToQuestionnaire()" class="px-3 py-1 bg-indigo-500 text-white text-xs rounded">Navigate Q</button>
                    </div>
                    <div class="mt-2 text-xs text-yellow-700">
                        <strong>Current Project:</strong> <span id="debug-project-id">-</span> | 
                        <strong>Mode:</strong> <span id="debug-mode">-</span> | 
                        <strong>Questionnaire:</strong> <span id="debug-questionnaire">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100" id="main-form">
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
                        <span class="text-sm font-medium text-gray-500">Data Kunjungan</span>
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
                        <div class="standard-migas-field" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nik" 
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
                        <div class="standard-migas-field" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="date" 
                                    name="tanggal_lahir" 
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
                        <div class="standard-migas-field" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Domisili <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="domisili" 
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
                        <div class="standard-migas-field" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto KTP <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        name="foto_identitas" 
                                        id="foto_identitas"
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

                <!-- Step 2: Kontak Tamu (Standard MIGAS only) -->
                <div class="wizard-step standard-migas-step" id="step-2" style="display: none;">
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
                                    <!-- Will be populated based on selected project -->
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

                        <!-- POS Jaga untuk Kuesioner -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                POS Jaga (untuk Kuesioner)
                            </label>
                            <div class="relative">
                                <select 
                                    name="area_patrol_id" 
                                    id="area_patrol_select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                    onchange="handleAreaPatrolChange()"
                                >
                                    <option value="">Pilih POS Jaga</option>
                                    <!-- Will be populated based on selected project -->
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih POS Jaga jika ingin mengisi kuesioner tamu
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
                        <input type="hidden" name="project_id" value="1" id="project_id_input">
                        <input type="hidden" name="bertemu" value="">
                        <input type="hidden" name="guest_book_mode" value="simple" id="guest_book_mode_input">
                        <input type="hidden" name="enable_questionnaire" value="0" id="enable_questionnaire_input">
                        <input type="hidden" name="lokasi_dituju" value="" id="lokasi_dituju_hidden">

<script>
// Update lokasi_dituju when area is selected
document.getElementById('area_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const hiddenInput = document.getElementById('lokasi_dituju_hidden');
    
    if (selectedOption.value) {
        hiddenInput.value = selectedOption.text;
    } else {
        hiddenInput.value = '';
    }
});
</script>

                        <!-- Hidden fields for existing functionality -->
                        <input type="hidden" name="project_id" value="1" id="project_id_input">
                        <input type="hidden" name="bertemu" value="">
                        <input type="hidden" name="guest_book_mode" value="simple" id="guest_book_mode_input">
                        <input type="hidden" name="enable_questionnaire" value="0" id="enable_questionnaire_input">
                        <input type="hidden" name="lokasi_dituju" value="" id="lokasi_dituju_hidden">

                    </div>
                </div>

                <!-- Step 4: Kuesioner (if enabled) -->
                <div class="wizard-step questionnaire-step" id="step-4" style="display: none;">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kuesioner</h3>
                        <p class="text-sm text-gray-600">Pertanyaan keamanan dan kesehatan</p>
                    </div>
                    
                    <div id="kuesioner-container">
                        <!-- Default message when no area patrol selected -->
                        <div id="no-kuesioner-message" class="text-center py-12">
                            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pilih POS Jaga</h3>
                            <p class="text-gray-600">Pilih POS Jaga di step sebelumnya untuk menampilkan kuesioner yang sesuai</p>
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
                                <!-- Static questions here -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        1. Apakah Anda dalam kondisi sehat?
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="pertanyaan_1" value="Ya" class="mr-3">
                                            <span class="text-sm text-gray-700">Ya</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="pertanyaan_1" value="Tidak" class="mr-3">
                                            <span class="text-sm text-gray-700">Tidak</span>
                                        </label>
                                    </div>
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

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing buku tamu form...');
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
            fetch('{{ route("perusahaan.area-patrols.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nama: name,
                    project_id: projectId,
                    deskripsi: description,
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
                        project: {
                            nama: data.data.project.nama
                        }
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
    });
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    const searchInput = document.getElementById('pos_jaga_search');
    const dropdown = document.getElementById('pos_jaga_dropdown');
    
    if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
        hidePosJagaDropdown();
    }
});

// Update loadKuesioner function to work with new POS Jaga system
function loadKuesioner() {
    const areaPatrolId = document.getElementById('area_patrol_id_hidden').value;
    const noKuesionerMessage = document.getElementById('no-kuesioner-message');
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    const staticKuesioner = document.getElementById('static-kuesioner');
    
    console.log('loadKuesioner called with areaPatrolId:', areaPatrolId);
    
    // Reset all displays first
    if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
    if (dynamicKuesioner) dynamicKuesioner.style.display = 'none';
    if (staticKuesioner) staticKuesioner.style.display = 'none';
    
    if (!areaPatrolId) {
        // Show no kuesioner message
        if (noKuesionerMessage) {
            noKuesionerMessage.style.display = 'block';
            noKuesionerMessage.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Pilih POS Jaga</h3>
                    <p class="text-gray-600">Pilih POS Jaga di step sebelumnya untuk menampilkan kuesioner yang sesuai</p>
                </div>
            `;
        }
        return;
    }
    
    // Show loading
    if (noKuesionerMessage) {
        noKuesionerMessage.style.display = 'block';
        noKuesionerMessage.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                <p class="text-gray-600">Memuat kuesioner...</p>
            </div>
        `;
    }
    
    // Fetch kuesioner
    const kuesionerUrl = `/perusahaan/buku-tamu/kuesioner?area_patrol_id=${areaPatrolId}`;
    console.log('Fetching kuesioner from:', kuesionerUrl);
    
    fetch(kuesionerUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('Kuesioner response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Kuesioner response data:', data);
        
        if (data.success && data.data && data.data.pertanyaans && data.data.pertanyaans.length > 0) {
            // Show dynamic kuesioner using the correct render function
            renderDynamicKuesionerFromProject(data.data);
            
            if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
            if (dynamicKuesioner) dynamicKuesioner.style.display = 'block';
            if (staticKuesioner) staticKuesioner.style.display = 'none';
        } else {
            // Show static kuesioner as fallback
            console.log('No dynamic kuesioner found, showing static fallback');
            
            if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
            if (dynamicKuesioner) dynamicKuesioner.style.display = 'none';
            if (staticKuesioner) staticKuesioner.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error loading kuesioner:', error);
        
        // Show static kuesioner as fallback
        console.log('Error occurred, showing static kuesioner as fallback');
        
        if (noKuesionerMessage) {
            noKuesionerMessage.style.display = 'block';
            noKuesionerMessage.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Gagal Memuat Kuesioner</h3>
                    <p class="text-gray-600 mb-4">Terjadi kesalahan saat memuat kuesioner dinamis</p>
                    <button 
                        onclick="loadKuesioner()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    >
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                </div>
            `;
        }
        
        // Also show static as backup
        if (staticKuesioner) {
            staticKuesioner.style.display = 'block';
        }
    });
}

// OLD renderDynamicKuesioner function removed - now using renderDynamicKuesionerFromProject instead
</script>
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

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing buku tamu form...');
    
    // Initialize datetime calculation
    const mulaiInput = document.querySelector('input[name="mulai_kunjungan"]');
    const selesaiInput = document.querySelector('input[name="selesai_kunjungan"]');
    
    if (mulaiInput) {
        mulaiInput.addEventListener('change', calculateDuration);
    }
    if (selesaiInput) {
        selesaiInput.addEventListener('change', calculateDuration);
    }
    
    // Initialize step navigation
    initializeNavigation();
    
    // Add small delay to ensure all elements are ready
    setTimeout(() => {
        // Auto-select first project if available
        const projectSelect = document.getElementById('project_select_main');
        if (projectSelect && projectSelect.options.length > 1) {
            console.log('Auto-selecting first project...');
            console.log('Available projects:', Array.from(projectSelect.options).map(opt => ({
                value: opt.value, 
                text: opt.text, 
                mode: opt.dataset.mode, 
                questionnaire: opt.dataset.questionnaire
            })));
            
            projectSelect.selectedIndex = 1; // Select first project (skip "Pilih Project" option)
            
            // Trigger project change manually since we're setting it programmatically
            const changeEvent = new Event('change', { bubbles: true });
            projectSelect.dispatchEvent(changeEvent);
            
            // Also call handleProjectChange directly to be sure
            handleProjectChange();
        } else {
            console.log('No projects available or project select not found');
        }
    }, 100); // 100ms delay
    
    console.log('Initialization complete');
});

// Handle project selection change
function handleProjectChange() {
    console.log('=== PROJECT CHANGED ===');
    
    const projectSelect = document.getElementById('project_select_main');
    const projectId = projectSelect.value;
    
    console.log('Selected project ID:', projectId);
    
    if (!projectId) {
        console.log('No project selected, hiding form');
        document.getElementById('main-form').style.display = 'none';
        document.getElementById('project-mode-display').classList.add('hidden');
        return;
    }
    
    // Try to get settings from option data attributes first (faster)
    const selectedOption = projectSelect.options[projectSelect.selectedIndex];
    if (selectedOption && selectedOption.dataset && selectedOption.dataset.mode) {
        console.log('Using option data attributes (fast path)');
        
        const guestBookMode = selectedOption.dataset.mode || 'simple';
        const enableQuestionnaire = selectedOption.dataset.questionnaire === '1';
        
        console.log('Option data:', { mode: guestBookMode, questionnaire: enableQuestionnaire });
        
        // Set project ID
        document.getElementById('project_id_input').value = projectId;
        document.getElementById('guest_book_mode_input').value = guestBookMode;
        document.getElementById('enable_questionnaire_input').value = enableQuestionnaire ? '1' : '0';
        
        // Update displays
        updateModeDisplay(guestBookMode, enableQuestionnaire);
        updateFormMode(guestBookMode, enableQuestionnaire);
        
        // Show main form
        document.getElementById('main-form').style.display = 'block';
        document.getElementById('project-mode-display').classList.remove('hidden');
        
        // Load areas for selected project
        loadProjectAreas(projectId);
        
        // Load questionnaire if enabled
        if (enableQuestionnaire) {
            console.log('Loading questionnaire for project:', projectId);
            loadKuesionerByProject(projectId);
        }
        
        // Reset to step 1
        currentStep = 1;
        updateStepDisplay();
        
        // Update debug info
        updateDebugInfo();
        
        console.log('✅ Form shown successfully with mode:', guestBookMode, 'questionnaire:', enableQuestionnaire);
        return;
    }
    
    console.log('Option data not available, fetching project settings via API...');
    
    // Show loading state
    const projectModeDisplay = document.getElementById('project-mode-display');
    projectModeDisplay.classList.remove('hidden');
    document.getElementById('mode-display-main').textContent = 'Loading...';
    document.getElementById('mode-description-main').textContent = 'Memuat pengaturan project...';
    
    // Fetch project settings as fallback
    fetch(`{{ route('perusahaan.buku-tamu.project-settings') }}?project_id=${projectId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Project settings response:', data);
        
        if (data.success) {
            const settings = data.data;
            
            // Set project ID
            document.getElementById('project_id_input').value = projectId;
            
            // Set mode from project settings
            const guestBookMode = settings.guest_book_mode || 'simple';
            const enableQuestionnaire = settings.enable_questionnaire || false;
            
            document.getElementById('guest_book_mode_input').value = guestBookMode;
            document.getElementById('enable_questionnaire_input').value = enableQuestionnaire ? '1' : '0';
            
            // Update mode display
            updateModeDisplay(guestBookMode, enableQuestionnaire);
            
            // Update form mode
            updateFormMode(guestBookMode, enableQuestionnaire);
            
            // Show main form
            document.getElementById('main-form').style.display = 'block';
            
            // Load areas for selected project
            loadProjectAreas(projectId);
            
            // Load questionnaire if enabled
            if (enableQuestionnaire) {
                console.log('Loading questionnaire for project:', projectId);
                loadKuesionerByProject(projectId);
            }
            
            // Reset to step 1
            currentStep = 1;
            updateStepDisplay();
            
            console.log('✅ Form shown successfully with mode:', guestBookMode, 'questionnaire:', enableQuestionnaire);
        } else {
            console.error('Failed to get project settings:', data.message);
            useFallbackSettings(projectId);
        }
    })
    .catch(error => {
        console.error('Error fetching project settings:', error);
        useFallbackSettings(projectId);
    });
}

// Use fallback settings when API fails
function useFallbackSettings(projectId) {
    console.log('Using fallback settings for project:', projectId);
    
    // Try to get settings from option data attributes first
    const projectSelect = document.getElementById('project_select_main');
    const selectedOption = projectSelect.options[projectSelect.selectedIndex];
    
    let guestBookMode = 'simple';
    let enableQuestionnaire = false;
    
    if (selectedOption && selectedOption.dataset) {
        guestBookMode = selectedOption.dataset.mode || 'simple';
        enableQuestionnaire = selectedOption.dataset.questionnaire === '1';
        console.log('Using option data:', { mode: guestBookMode, questionnaire: enableQuestionnaire });
    }
    
    // Set project ID
    document.getElementById('project_id_input').value = projectId;
    
    document.getElementById('guest_book_mode_input').value = guestBookMode;
    document.getElementById('enable_questionnaire_input').value = enableQuestionnaire ? '1' : '0';
    
    // Update displays
    updateModeDisplay(guestBookMode, enableQuestionnaire);
    updateFormMode(guestBookMode, enableQuestionnaire);
    
    // Show main form
    document.getElementById('main-form').style.display = 'block';
    document.getElementById('project-mode-display').classList.remove('hidden');
    
    // Load areas for selected project
    loadProjectAreas(projectId);
    
    // Load questionnaire if enabled
    if (enableQuestionnaire) {
        console.log('Loading questionnaire for project:', projectId);
        loadKuesionerByProject(projectId);
    }
    
    // Reset to step 1
    currentStep = 1;
    updateStepDisplay();
    
    // Update debug info
    updateDebugInfo();
    
    console.log('✅ Fallback settings applied');
}

// Update mode display
function updateModeDisplay(guestBookMode, enableQuestionnaire) {
    const modeDisplayMain = document.getElementById('mode-display-main');
    const modeDescriptionMain = document.getElementById('mode-description-main');
    const questionnaireStatusMain = document.getElementById('questionnaire-status-main');
    
    if (guestBookMode === 'standard_migas') {
        modeDisplayMain.textContent = 'Standard MIGAS';
        modeDescriptionMain.textContent = 'Form lengkap dengan semua field sesuai standar MIGAS';
    } else {
        modeDisplayMain.textContent = 'Simple';
        modeDescriptionMain.textContent = 'Form sederhana dengan field dasar saja';
    }
    
    if (enableQuestionnaire) {
        questionnaireStatusMain.innerHTML = `
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                <i class="fas fa-clipboard-list mr-1"></i>Kuesioner Aktif
            </span>
        `;
    } else {
        questionnaireStatusMain.innerHTML = `
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                <i class="fas fa-clipboard mr-1"></i>Kuesioner Nonaktif
            </span>
        `;
    }
    
    // Update debug info
    updateDebugInfo();
}

// Load areas and area patrols based on selected project
function loadProjectAreas(projectId) {
    console.log('=== LOADING AREAS FOR PROJECT ===');
    console.log('Project ID:', projectId);
    
    // Use the new element IDs in the project selection section
    const areaSelectMain = document.getElementById('area_select_main');
    
    // Also update the form area select (for backward compatibility)
    const areaSelect = document.getElementById('area_select');
    
    if (!areaSelectMain) {
        console.error('❌ area_select_main element not found');
        return;
    }
    
    if (!projectId) {
        console.log('No project ID, clearing dropdowns and hiding sections');
        areaSelectMain.innerHTML = '<option value="">Pilih Area/Lokasi</option>';
        if (areaSelect) areaSelect.innerHTML = '<option value="">Pilih Area/Lokasi</option>';
        
        // Hide area section
        document.getElementById('area-selection').style.display = 'none';
        return;
    }
    
    // Show area section
    document.getElementById('area-selection').style.display = 'block';
    
    // Show loading state
    areaSelectMain.innerHTML = '<option value="">Memuat area...</option>';
    if (areaSelect) areaSelect.innerHTML = '<option value="">Memuat area...</option>';
    
    console.log('Loading areas from:', `{{ route('perusahaan.areas.by-project') }}?project_id=${projectId}`);
    
    // Load areas (regular areas)
    fetch(`{{ route('perusahaan.areas.by-project') }}?project_id=${projectId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Areas response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Areas response data:', data);
        
        areaSelectMain.innerHTML = '<option value="">Pilih Area/Lokasi</option>';
        if (areaSelect) areaSelect.innerHTML = '<option value="">Pilih Area/Lokasi</option>';
        
        if (data.success && data.data && data.data.length > 0) {
            console.log(`✅ Found ${data.data.length} areas`);
            data.data.forEach(area => {
                const optionMain = document.createElement('option');
                optionMain.value = area.id;
                optionMain.textContent = area.nama + (area.alamat ? ' - ' + area.alamat : '');
                areaSelectMain.appendChild(optionMain);
                
                // Also update form area select for backward compatibility
                if (areaSelect) {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.nama + (area.alamat ? ' - ' + area.alamat : '');
                    areaSelect.appendChild(option);
                }
                
                console.log('Added area:', area.nama);
            });
        } else {
            console.log('⚠️ No areas found');
            areaSelectMain.innerHTML += '<option value="" disabled>Belum ada area tersedia</option>';
            if (areaSelect) areaSelect.innerHTML += '<option value="" disabled>Belum ada area tersedia</option>';
        }
    })
    .catch(error => {
        console.error('❌ Error loading areas:', error);
        areaSelectMain.innerHTML = '<option value="">Error loading areas</option>';
        if (areaSelect) areaSelect.innerHTML = '<option value="">Error loading areas</option>';
    });
    
    console.log('=== END LOADING AREAS ===');
}

// Handle area change (for the main area select in project section)
function handleAreaChange() {
    const areaSelectMain = document.getElementById('area_select_main');
    const areaSelect = document.getElementById('area_select');
    
    if (!areaSelectMain) {
        console.error('area_select_main not found');
        return;
    }
    
    const selectedAreaId = areaSelectMain.value;
    const selectedOption = areaSelectMain.options[areaSelectMain.selectedIndex];
    
    console.log('Area changed:', selectedAreaId, selectedOption?.text);
    
    // Sync with form area select if it exists
    if (areaSelect && selectedAreaId) {
        areaSelect.value = selectedAreaId;
        
        // Update hidden field for lokasi_dituju
        const hiddenInput = document.getElementById('lokasi_dituju_hidden');
        if (hiddenInput && selectedOption) {
            hiddenInput.value = selectedOption.text;
        }
    }
    
    // Load questionnaire based on project settings if questionnaire is enabled
    const enableQuestionnaire = document.getElementById('enable_questionnaire_input')?.value === '1';
    const projectId = document.getElementById('project_id_input')?.value;
    
    if (enableQuestionnaire && projectId) {
        console.log('Loading questionnaire for project:', projectId);
        loadKuesionerByProject(projectId);
    }
    
    console.log('Area selection synced between main and form selects');
}

// Load questionnaire based on project (not area patrol)
function loadKuesionerByProject(projectId) {
    console.log('=== LOADING KUESIONER BY PROJECT ===');
    console.log('Project ID:', projectId);
    
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    const staticKuesioner = document.getElementById('static-kuesioner');
    const noKuesionerMessage = document.getElementById('no-kuesioner-message');
    
    console.log('Elements found:', {
        dynamicKuesioner: !!dynamicKuesioner,
        staticKuesioner: !!staticKuesioner,
        noKuesionerMessage: !!noKuesionerMessage
    });
    
    // Show loading state
    if (dynamicKuesioner) {
        dynamicKuesioner.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-600">Memuat kuesioner...</p>
            </div>
        `;
        dynamicKuesioner.style.display = 'block';
        console.log('Loading state set');
    }
    if (staticKuesioner) staticKuesioner.style.display = 'none';
    if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
    
    // Try to fetch dynamic kuesioner first
    const url = `{{ route('perusahaan.buku-tamu.kuesioner') }}?project_id=${projectId}`;
    console.log('Fetching from URL:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('=== KUESIONER API RESPONSE ===');
        console.log('Full response:', data);
        
        if (data.success && data.data && data.data.pertanyaans && data.data.pertanyaans.length > 0) {
            console.log('✅ Valid questionnaire data found');
            console.log('Questions count:', data.data.pertanyaans.length);
            
            // Show dynamic kuesioner using the correct render function
            renderDynamicKuesionerFromProject(data.data);
            if (dynamicKuesioner) dynamicKuesioner.style.display = 'block';
            if (staticKuesioner) staticKuesioner.style.display = 'none';
            if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
        } else {
            console.log('❌ No valid questionnaire data, showing static fallback');
            console.log('Data structure:', data);
            showStaticKuesioner();
        }
    })
    .catch(error => {
        console.error('❌ Error loading kuesioner:', error);
        // Show static kuesioner as fallback
        showStaticKuesioner();
    });
}

// Render dynamic kuesioner from project (correct implementation)
function renderDynamicKuesionerFromProject(kuesioner) {
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    
    if (!dynamicKuesioner) {
        console.error('Dynamic kuesioner container not found');
        return;
    }
    
    console.log('Rendering kuesioner from project:', kuesioner);
    
    // Validate kuesioner data
    if (!kuesioner) {
        console.error('Kuesioner data is null or undefined');
        dynamicKuesioner.innerHTML = '<div class="text-center py-8"><p class="text-red-500">Error: Data kuesioner tidak valid</p></div>';
        return;
    }
    
    let html = `
        <div class="mb-4">
            <h4 class="text-md font-semibold text-gray-800">${kuesioner.judul || 'Kuesioner'}</h4>
            <p class="text-sm text-gray-600 mt-1">Silakan jawab pertanyaan berikut dengan lengkap</p>
        </div>
        <div class="space-y-6">
    `;
    
    if (kuesioner.pertanyaans && kuesioner.pertanyaans.length > 0) {
        kuesioner.pertanyaans.forEach((pertanyaan, index) => {
            // Validate pertanyaan data
            if (!pertanyaan || !pertanyaan.pertanyaan) {
                console.warn('Invalid pertanyaan data at index', index, pertanyaan);
                return;
            }
            
            html += `
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ${index + 1}. ${pertanyaan.pertanyaan}
                            ${pertanyaan.is_required ? '<span class="text-red-500">*</span>' : ''}
                        </label>
                    </div>
            `;
            
            if (pertanyaan.tipe_jawaban === 'pilihan' && pertanyaan.opsi_jawaban && Array.isArray(pertanyaan.opsi_jawaban)) {
                html += '<div class="space-y-2">';
                pertanyaan.opsi_jawaban.forEach((opsi, opsiIndex) => {
                    html += `
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="kuesioner_answers[${pertanyaan.id}]" 
                                value="${opsi}"
                                ${pertanyaan.is_required ? 'required' : ''}
                                class="mr-2"
                            >
                            <span class="text-sm text-gray-700">${opsi}</span>
                        </label>
                    `;
                });
                html += '</div>';
            } else {
                html += `
                    <textarea 
                        name="kuesioner_answers[${pertanyaan.id}]" 
                        rows="3"
                        ${pertanyaan.is_required ? 'required' : ''}
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Masukkan jawaban Anda..."
                    ></textarea>
                `;
            }
            
            html += '</div>';
        });
    } else {
        html += `
            <div class="text-center py-8">
                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Tidak ada pertanyaan dalam kuesioner ini</p>
            </div>
        `;
    }
    
    html += '</div>';
    
    try {
        dynamicKuesioner.innerHTML = html;
        dynamicKuesioner.style.display = 'block';
        console.log('Dynamic kuesioner from project rendered successfully');
    } catch (error) {
        console.error('Error rendering dynamic kuesioner:', error);
        dynamicKuesioner.innerHTML = '<div class="text-center py-8"><p class="text-red-500">Error rendering questionnaire</p></div>';
    }
}

// Show static kuesioner as fallback
function showStaticKuesioner() {
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    const staticKuesioner = document.getElementById('static-kuesioner');
    const noKuesionerMessage = document.getElementById('no-kuesioner-message');
    
    console.log('Showing static kuesioner fallback');
    
    if (dynamicKuesioner) dynamicKuesioner.style.display = 'none';
    if (staticKuesioner) staticKuesioner.style.display = 'block';
    if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
    
    console.log('✅ Static kuesioner displayed successfully');
}

// Handle area patrol change to load questionnaire (DEPRECATED - now handled by project)
function handleAreaPatrolChange() {
    // This function is now deprecated since we removed POS Jaga from the main filter
    // Questionnaire loading is now handled by project settings in handleAreaChange()
    console.log('handleAreaPatrolChange called but deprecated - questionnaire now loaded by project');
}

// Clear kuesioner display
function clearKuesioner() {
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    const staticKuesioner = document.getElementById('static-kuesioner');
    const noKuesionerMessage = document.getElementById('no-kuesioner-message');
    
    if (dynamicKuesioner) dynamicKuesioner.style.display = 'none';
    if (staticKuesioner) staticKuesioner.style.display = 'none';
    if (noKuesionerMessage) {
        noKuesionerMessage.style.display = 'block';
        noKuesionerMessage.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Kuesioner Tidak Tersedia</h3>
                <p class="text-gray-600">Belum ada kuesioner yang dikonfigurasi untuk project ini</p>
            </div>
        `;
    }
}

// Update debug info
function updateDebugInfo() {
    const projectId = document.getElementById('project_id_input')?.value || '-';
    const mode = document.getElementById('guest_book_mode_input')?.value || '-';
    const questionnaire = document.getElementById('enable_questionnaire_input')?.value || '-';
    
    const debugProjectId = document.getElementById('debug-project-id');
    const debugMode = document.getElementById('debug-mode');
    const debugQuestionnaire = document.getElementById('debug-questionnaire');
    
    if (debugProjectId) debugProjectId.textContent = projectId;
    if (debugMode) debugMode.textContent = mode;
    if (debugQuestionnaire) debugQuestionnaire.textContent = questionnaire === '1' ? 'Yes' : 'No';
}

// Update form mode
function updateFormMode(guestBookMode, enableQuestionnaire) {
    console.log('Updating form mode:', guestBookMode, 'questionnaire:', enableQuestionnaire);
    
    const standardFields = document.querySelectorAll('.standard-migas-field');
    const standardSteps = document.querySelectorAll('.standard-migas-step');
    const questionnaireSteps = document.querySelectorAll('.questionnaire-step');
    
    if (guestBookMode === 'standard_migas') {
        console.log('Setting Standard MIGAS mode');
        
        // Show standard MIGAS fields and steps
        standardFields.forEach(field => {
            field.style.display = 'block';
            // Make required fields required
            const inputs = field.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.hasAttribute('data-required-migas')) {
                    input.setAttribute('required', 'required');
                }
            });
        });
        
        standardSteps.forEach(step => {
            step.style.display = 'block';
        });
        
        // Update total steps for MIGAS mode
        window.totalSteps = enableQuestionnaire ? 4 : 3;
    } else {
        console.log('Setting Simple mode');
        
        // Hide standard MIGAS fields and steps
        standardFields.forEach(field => {
            field.style.display = 'none';
            // Remove required attribute
            const inputs = field.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.removeAttribute('required');
            });
        });
        
        standardSteps.forEach(step => {
            step.style.display = 'none';
        });
        
        // Update total steps for Simple mode
        window.totalSteps = enableQuestionnaire ? 3 : 2;
    }
    
    // Handle questionnaire step
    if (enableQuestionnaire) {
        console.log('Enabling questionnaire');
        questionnaireSteps.forEach(step => {
            step.style.display = 'block';
        });
    } else {
        console.log('Disabling questionnaire');
        questionnaireSteps.forEach(step => {
            step.style.display = 'none';
        });
    }
    
    // Update step navigation
    updateStepNavigation(guestBookMode, enableQuestionnaire);
}

// Update step navigation
function updateStepNavigation(guestBookMode, enableQuestionnaire) {
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const stepLines = document.querySelectorAll('.step-line');
    
    // Reset all steps first
    stepIndicators.forEach((indicator, index) => {
        const stepContainer = indicator.closest('.flex.items-center.space-x-2');
        if (stepContainer) {
            stepContainer.style.display = 'flex';
        }
    });
    
    stepLines.forEach(line => {
        line.style.display = 'block';
    });
    
    if (guestBookMode === 'simple') {
        // Simple mode: Hide step 2 (Kontak Tamu)
        if (stepIndicators[1]) {
            const step2Container = stepIndicators[1].closest('.flex.items-center.space-x-2');
            if (step2Container) {
                step2Container.style.display = 'none';
            }
        }
        
        // Renumber remaining steps
        if (stepIndicators[2]) {
            stepIndicators[2].textContent = '2';
            const step3Label = stepIndicators[2].nextElementSibling;
            if (step3Label) step3Label.textContent = 'Data Kunjungan';
        }
        
        if (enableQuestionnaire && stepIndicators[3]) {
            stepIndicators[3].textContent = '3';
            const step4Label = stepIndicators[3].nextElementSibling;
            if (step4Label) step4Label.textContent = 'Kuesioner';
        } else if (stepIndicators[3]) {
            const step4Container = stepIndicators[3].closest('.flex.items-center.space-x-2');
            if (step4Container) {
                step4Container.style.display = 'none';
            }
        }
    } else {
        // Standard MIGAS mode: Show all steps with original numbering
        stepIndicators.forEach((indicator, index) => {
            indicator.textContent = (index + 1).toString();
        });
        
        if (!enableQuestionnaire && stepIndicators[3]) {
            const step4Container = stepIndicators[3].closest('.flex.items-center.space-x-2');
            if (step4Container) {
                step4Container.style.display = 'none';
            }
        }
    }
}

// Initialize navigation buttons
function initializeNavigation() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            console.log('Next button clicked, current step:', currentStep);
            
            if (validateCurrentStep()) {
                const totalSteps = window.totalSteps || 2;
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepDisplay();
                    console.log('Moved to step:', currentStep);
                }
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            console.log('Prev button clicked, current step:', currentStep);
            
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
                console.log('Moved to step:', currentStep);
            }
        });
    }
}

// Update step display
function updateStepDisplay() {
    console.log('Updating step display for step:', currentStep);
    
    const guestBookMode = document.getElementById('guest_book_mode_input').value;
    const enableQuestionnaire = document.getElementById('enable_questionnaire_input').value === '1';
    
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'none';
    });
    
    // Show current step based on mode
    let stepToShow;
    if (guestBookMode === 'standard_migas') {
        // Standard MIGAS mode: 1→2→3→4
        stepToShow = `step-${currentStep}`;
    } else {
        // Simple mode: 1→3→4 (skip step 2)
        if (currentStep === 1) {
            stepToShow = 'step-1'; // Data Diri
        } else if (currentStep === 2) {
            stepToShow = 'step-3'; // Data Kunjungan (skip step-2)
        } else if (currentStep === 3) {
            stepToShow = 'step-4'; // Kuesioner
        }
    }
    
    const currentStepElement = document.getElementById(stepToShow);
    if (currentStepElement) {
        currentStepElement.style.display = 'block';
        console.log('Showing step:', stepToShow);
    }
    
    // Update navigation buttons
    const totalSteps = window.totalSteps || 2;
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
    if (nextBtn) nextBtn.style.display = currentStep >= totalSteps ? 'none' : 'block';
    if (submitBtn) submitBtn.style.display = currentStep >= totalSteps ? 'block' : 'none';
    
    // Update step indicators
    updateStepIndicators();
}

// Update step indicators
function updateStepIndicators() {
    const indicators = document.querySelectorAll('.step-indicator');
    const lines = document.querySelectorAll('.step-line');
    
    // Reset all indicators
    indicators.forEach(indicator => {
        indicator.classList.remove('active');
    });
    lines.forEach(line => {
        line.classList.remove('active');
    });
    
    // Activate indicators based on current step
    const guestBookMode = document.getElementById('guest_book_mode_input').value;
    
    if (guestBookMode === 'standard_migas') {
        // Standard MIGAS mode: direct mapping
        for (let i = 0; i < currentStep && i < indicators.length; i++) {
            indicators[i].classList.add('active');
        }
        for (let i = 0; i < currentStep - 1 && i < lines.length; i++) {
            lines[i].classList.add('active');
        }
    } else {
        // Simple mode: skip step 2
        if (currentStep >= 1 && indicators[0]) {
            indicators[0].classList.add('active');
        }
        if (currentStep >= 2 && indicators[2]) {
            indicators[2].classList.add('active');
            if (lines[0]) lines[0].classList.add('active');
        }
        if (currentStep >= 3 && indicators[3]) {
            indicators[3].classList.add('active');
            if (lines[1]) lines[1].classList.add('active');
        }
    }
}

// Validate current step
function validateCurrentStep() {
    const guestBookMode = document.getElementById('guest_book_mode_input').value;
    
    let stepElement;
    if (guestBookMode === 'standard_migas') {
        stepElement = document.getElementById(`step-${currentStep}`);
    } else {
        // Simple mode mapping
        if (currentStep === 1) {
            stepElement = document.getElementById('step-1');
        } else if (currentStep === 2) {
            stepElement = document.getElementById('step-3');
        } else if (currentStep === 3) {
            stepElement = document.getElementById('step-4');
        }
    }
    
    if (!stepElement) {
        return true;
    }
    
    const requiredFields = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    let errorMessages = [];
    
    requiredFields.forEach(field => {
        // Skip hidden fields
        if (field.type === 'hidden') {
            return;
        }
        
        // Skip fields that are hidden (standard MIGAS fields in simple mode)
        const fieldContainer = field.closest('.standard-migas-field');
        if (fieldContainer && guestBookMode === 'simple' && fieldContainer.style.display === 'none') {
            return;
        }
        
        // Skip step 2 fields entirely in simple mode
        const stepContainer = field.closest('.standard-migas-step');
        if (stepContainer && guestBookMode === 'simple' && stepContainer.style.display === 'none') {
            return;
        }
        
        // Validate file inputs
        if (field.type === 'file') {
            // In simple mode, foto_identitas is optional
            if (guestBookMode === 'simple' && field.name === 'foto_identitas') {
                return;
            }
            
            if (!field.files || field.files.length === 0) {
                field.classList.add('border-red-500');
                isValid = false;
                errorMessages.push(`${field.name} wajib diupload`);
            } else {
                field.classList.remove('border-red-500');
            }
            return;
        }
        
        // Validate radio buttons
        if (field.type === 'radio') {
            const radioGroup = stepElement.querySelectorAll(`input[name="${field.name}"]`);
            const isAnySelected = Array.from(radioGroup).some(radio => radio.checked);
            
            if (!isAnySelected) {
                isValid = false;
                errorMessages.push(`Pilihan untuk ${field.name} wajib dipilih`);
                radioGroup.forEach(radio => {
                    radio.classList.add('border-red-500');
                });
            } else {
                radioGroup.forEach(radio => {
                    radio.classList.remove('border-red-500');
                });
            }
            return;
        }
        
        // Validate regular fields
        if (!field.value || !field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
            errorMessages.push(`${field.name} wajib diisi`);
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Silakan lengkapi semua field yang wajib diisi',
            confirmButtonColor: '#3B82C8'
        });
    }
    
    return isValid;
}

// Calculate duration
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

// Photo preview functions
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

// Debug functions for testing
function simpleTest() {
    console.log('=== SIMPLE TEST ===');
    
    const projectSelect = document.getElementById('project_select_main');
    if (projectSelect && projectSelect.options.length > 1) {
        console.log('Setting project to first option...');
        projectSelect.selectedIndex = 1;
        
        console.log('Calling handleProjectChange...');
        handleProjectChange();
        
        Swal.fire({
            icon: 'success',
            title: 'Test Berhasil',
            text: 'Project dipilih dan form ditampilkan',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        console.error('Project select not found or no options');
        Swal.fire({
            icon: 'error',
            title: 'Test Gagal',
            text: 'Project select tidak ditemukan atau tidak ada options'
        });
    }
}

function showFormNow() {
    console.log('=== SHOW FORM NOW ===');
    
    try {
        // Set basic values
        document.getElementById('project_id_input').value = '1';
        document.getElementById('guest_book_mode_input').value = 'simple';
        document.getElementById('enable_questionnaire_input').value = '0';
        
        // Show elements
        document.getElementById('main-form').style.display = 'block';
        document.getElementById('project-mode-display').classList.remove('hidden');
        
        // Set mode display
        document.getElementById('mode-display-main').textContent = 'Simple';
        document.getElementById('mode-description-main').textContent = 'Form sederhana dengan field dasar saja';
        
        // Reset to step 1
        currentStep = 1;
        updateStepDisplay();
        
        console.log('✅ Form shown successfully');
        
        Swal.fire({
            icon: 'success',
            title: 'Form Ditampilkan',
            text: 'Form berhasil ditampilkan dengan mode Simple',
            timer: 1500,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('Error in showFormNow:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error: ' + error.message
        });
    }
}

function directShowForm() {
    console.log('=== DIRECT SHOW FORM ===');
    
    const mainForm = document.getElementById('main-form');
    const projectModeDisplay = document.getElementById('project-mode-display');
    
    if (mainForm) {
        mainForm.style.display = 'block';
        console.log('✅ Main form displayed');
    } else {
        console.error('❌ Main form not found');
        alert('Main form element not found!');
        return;
    }
    
    if (projectModeDisplay) {
        projectModeDisplay.classList.remove('hidden');
        console.log('✅ Project mode display shown');
    }
    
    // Set basic text
    const modeDisplay = document.getElementById('mode-display-main');
    const modeDescription = document.getElementById('mode-description-main');
    
    if (modeDisplay) modeDisplay.textContent = 'Simple Mode';
    if (modeDescription) modeDescription.textContent = 'Form sederhana - debug mode';
    
    alert('Form ditampilkan! Cek apakah form sudah muncul di bawah.');
}

function testQuestionnaire() {
    console.log('=== TEST QUESTIONNAIRE ===');
    
    // Set project 1 (Kantor Jakarta) which has questionnaire
    document.getElementById('project_id_input').value = '1';
    document.getElementById('guest_book_mode_input').value = 'simple';
    document.getElementById('enable_questionnaire_input').value = '1';
    
    // Show form first
    document.getElementById('main-form').style.display = 'block';
    document.getElementById('project-mode-display').classList.remove('hidden');
    
    // Update form mode to show questionnaire step
    updateFormMode('simple', true);
    
    // Load questionnaire for project 1
    console.log('Loading questionnaire for project 1...');
    loadKuesionerByProject('1');
    
    // Show step 4 (questionnaire step) directly
    setTimeout(() => {
        // Set current step to 3 (which maps to step-4 in simple mode)
        currentStep = 3;
        window.totalSteps = 3;
        
        // Hide all steps first
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.style.display = 'none';
        });
        
        // Show questionnaire step directly
        const questionnaireStep = document.getElementById('step-4');
        if (questionnaireStep) {
            questionnaireStep.style.display = 'block';
            console.log('✅ Questionnaire step (step-4) shown');
        } else {
            console.error('❌ Questionnaire step (step-4) not found');
        }
        
        // Update navigation buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (prevBtn) prevBtn.style.display = 'block';
        if (nextBtn) nextBtn.style.display = 'none';
        if (submitBtn) submitBtn.style.display = 'block';
        
        // Update step indicators
        updateStepIndicators();
        
        console.log('✅ Questionnaire step shown with current step:', currentStep);
        
        Swal.fire({
            icon: 'success',
            title: 'Questionnaire Loaded!',
            text: 'Check the questionnaire form below. You should see the questions now.',
            timer: 3000,
            showConfirmButton: false
        });
    }, 1500); // Increased delay to ensure questionnaire is loaded
}

function showQuestionnaireDirectly() {
    console.log('=== SHOW QUESTIONNAIRE DIRECTLY ===');
    
    // Force show the questionnaire step
    const questionnaireStep = document.getElementById('step-4');
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    
    if (questionnaireStep) {
        // Hide all other steps
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.style.display = 'none';
        });
        
        // Show questionnaire step
        questionnaireStep.style.display = 'block';
        console.log('✅ Questionnaire step forced to show');
        
        if (dynamicKuesioner) {
            dynamicKuesioner.style.display = 'block';
            console.log('✅ Dynamic kuesioner container shown');
            console.log('Dynamic kuesioner content:', dynamicKuesioner.innerHTML);
        }
        
        Swal.fire({
            icon: 'info',
            title: 'Questionnaire Step Shown',
            text: 'The questionnaire step is now visible. Check if you can see the questions.',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        console.error('❌ Questionnaire step not found');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Questionnaire step element not found!'
        });
    }
}

function navigateToQuestionnaire() {
    console.log('=== NAVIGATE TO QUESTIONNAIRE ===');
    
    // First, set up the form properly
    const projectSelect = document.getElementById('project_select_main');
    if (projectSelect) {
        // Select Kantor Jakarta (project 1)
        for (let i = 0; i < projectSelect.options.length; i++) {
            if (projectSelect.options[i].value === '1') {
                projectSelect.selectedIndex = i;
                break;
            }
        }
        
        // Trigger project change
        handleProjectChange();
        
        // Wait for project to load, then navigate to questionnaire
        setTimeout(() => {
            // Set to simple mode with questionnaire
            document.getElementById('guest_book_mode_input').value = 'simple';
            document.getElementById('enable_questionnaire_input').value = '1';
            
            // Update form mode
            updateFormMode('simple', true);
            
            // Navigate to questionnaire step (step 3 in simple mode = step-4 actual)
            currentStep = 3;
            window.totalSteps = 3;
            updateStepDisplay();
            
            console.log('✅ Navigated to questionnaire step via normal flow');
            
            Swal.fire({
                icon: 'success',
                title: 'Navigation Complete',
                text: 'Navigated to questionnaire step. You should see the questions now.',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1000);
    }
}

// Form submission
document.getElementById('formBukuTamu').addEventListener('submit', function(e) {
    // Check if project is selected
    const projectId = document.getElementById('project_id_input').value;
    if (!projectId) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Project Belum Dipilih',
            text: 'Silakan pilih project terlebih dahulu'
        });
        return false;
    }
    
    // Ensure lokasi_dituju is set from area selection
    const areaSelect = document.getElementById('area_select');
    const lokasiDitujuHidden = document.getElementById('lokasi_dituju_hidden');
    
    if (areaSelect.value && areaSelect.selectedIndex > 0) {
        const selectedOption = areaSelect.options[areaSelect.selectedIndex];
        lokasiDitujuHidden.value = selectedOption.text;
        console.log('Set lokasi_dituju to:', selectedOption.text);
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

// Load kuesioner when POS Jaga is selected
function loadKuesioner() {
    const areaPatrolId = document.getElementById('area_patrol_id_hidden').value;
    const dynamicKuesioner = document.getElementById('dynamic-kuesioner');
    const noKuesionerMessage = document.getElementById('no-kuesioner-message');
    
    console.log('Loading kuesioner for area patrol ID:', areaPatrolId);
    
    if (!areaPatrolId) {
        console.log('No area patrol selected');
        if (noKuesionerMessage) noKuesionerMessage.style.display = 'block';
        if (dynamicKuesioner) dynamicKuesioner.style.display = 'none';
        return;
    }
    
    // Show loading state
    if (dynamicKuesioner) {
        dynamicKuesioner.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-600">Memuat kuesioner...</p>
            </div>
        `;
        dynamicKuesioner.style.display = 'block';
    }
    if (noKuesionerMessage) noKuesionerMessage.style.display = 'none';
    
    // Fetch kuesioner data
    fetch(`{{ route('perusahaan.buku-tamu.kuesioner') }}?area_patrol_id=${areaPatrolId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Kuesioner response:', data);
        
        if (data.success && data.data.kuesioner) {
            renderDynamicKuesionerFromProject(data.data.kuesioner);
        } else {
            if (noKuesionerMessage) noKuesionerMessage.style.display = 'block';
            if (dynamicKuesioner) dynamicKuesioner.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error loading kuesioner:', error);
        
        if (dynamicKuesioner) {
            dynamicKuesioner.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-2"></i>
                    <p class="text-red-600">Gagal memuat kuesioner</p>
                </div>
            `;
        }
    });
}

// OLD renderDynamicKuesioner function removed - using renderDynamicKuesionerFromProject instead

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

/* Guest book mode transitions */
.standard-migas-field {
    transition: all 0.3s ease;
}

.standard-migas-step {
    transition: all 0.3s ease;
}

.questionnaire-step {
    transition: all 0.3s ease;
}

/* Mode info styling */
#guest-book-mode-info {
    transition: all 0.3s ease;
}

/* Project selection styling */
#project-mode-display {
    transition: all 0.3s ease;
}

#main-form {
    transition: all 0.3s ease;
}

/* POS Jaga search dropdown styling */
#pos_jaga_dropdown {
    z-index: 1000;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

#pos_jaga_dropdown .hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

#pos_jaga_dropdown .hover\:bg-blue-50:hover {
    background-color: #eff6ff;
}

/* Search input focus styling */
#pos_jaga_search:focus {
    border-color: #3B82C8;
    box-shadow: 0 0 0 3px rgba(59, 130, 200, 0.1);
}
</style>
@endpush