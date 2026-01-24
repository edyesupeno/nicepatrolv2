@extends('perusahaan.layouts.app')

@section('title', 'Edit Patroli Mandiri')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Patroli Mandiri</h1>
            <nav class="text-sm text-gray-600 mt-1">
                <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="hover:text-blue-600">Patroli Mandiri</a>
                <span class="mx-2">/</span>
                <a href="{{ route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id) }}" class="hover:text-blue-600">Detail</a>
                <span class="mx-2">/</span>
                <span>Edit</span>
            </nav>
        </div>
        <div>
            <a href="{{ route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    @if($patroliMandiri->status_laporan !== 'submitted')
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                <div>
                    <strong>Perhatian:</strong> Laporan ini sudah direview dan tidak dapat diedit.
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Patroli Mandiri</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('perusahaan.patroli-mandiri.update', $patroliMandiri->hash_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <!-- Project -->
                                <div>
                                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror" 
                                            id="project_id" name="project_id" required>
                                        <option value="">Pilih Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ (old('project_id') ?? $patroliMandiri->project_id) == $project->id ? 'selected' : '' }}>
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
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}" {{ (old('area_patrol_id') ?? $patroliMandiri->area_patrol_id) == $area->id ? 'selected' : '' }}>
                                                {{ $area->nama }}
                                            </option>
                                        @endforeach
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
                                            <option value="{{ $user->id }}" {{ (old('petugas_id') ?? $patroliMandiri->petugas_id) == $user->id ? 'selected' : '' }}>
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
                                        <option value="{{ old('nama_lokasi') ?? $patroliMandiri->nama_lokasi }}" selected>{{ old('nama_lokasi') ?? $patroliMandiri->nama_lokasi }}</option>
                                    </select>
                                    <input type="hidden" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi') ?? $patroliMandiri->nama_lokasi }}" required>
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
                                           id="koordinat" name="koordinat" value="{{ old('koordinat') ?? ($patroliMandiri->latitude . ', ' . $patroliMandiri->longitude) }}" 
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
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') ?? $patroliMandiri->latitude }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') ?? $patroliMandiri->longitude }}">
                            </div>

                            <!-- Status Lokasi -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Lokasi <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'aman' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}" onclick="selectStatus('aman')">
                                        <input type="radio" name="status_lokasi" id="status_aman" value="aman" 
                                               {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'aman' ? 'checked' : '' }} required class="sr-only">
                                        <div class="flex items-center">
                                            <i class="fas fa-shield-alt text-green-600 text-xl mr-3"></i>
                                            <div>
                                                <div class="font-medium text-gray-900">Aman</div>
                                                <div class="text-sm text-gray-500">Kondisi lokasi normal dan aman</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'tidak_aman' ? 'border-red-500 bg-red-50' : 'border-gray-300' }}" onclick="selectStatus('tidak_aman')">
                                        <input type="radio" name="status_lokasi" id="status_tidak_aman" value="tidak_aman" 
                                               {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'tidak_aman' ? 'checked' : '' }} required class="sr-only">
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

                            <!-- Jenis Kendala -->
                            <div id="jenisKendalaSection" class="mb-4" style="display: {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'tidak_aman' ? 'block' : 'none' }};">
                                <label for="jenis_kendala" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kendala <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kendala') border-red-500 @enderror" 
                                        id="jenis_kendala_select" name="jenis_kendala_select">
                                    <option value="{{ old('jenis_kendala') ?? $patroliMandiri->jenis_kendala }}" selected>{{ old('jenis_kendala') ?? ucwords(str_replace('_', ' ', $patroliMandiri->jenis_kendala)) }}</option>
                                </select>
                                <input type="hidden" id="jenis_kendala" name="jenis_kendala" value="{{ old('jenis_kendala') ?? $patroliMandiri->jenis_kendala }}">
                                @error('jenis_kendala')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deskripsi Kendala -->
                            <div id="deskripsiKendalaSection" class="mb-4" style="display: {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'tidak_aman' ? 'block' : 'none' }};">
                                <label for="deskripsi_kendala" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kendala</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('deskripsi_kendala') border-red-500 @enderror" 
                                          id="deskripsi_kendala" name="deskripsi_kendala" rows="3" 
                                          placeholder="Jelaskan detail kendala yang ditemukan...">{{ old('deskripsi_kendala') ?? $patroliMandiri->deskripsi_kendala }}</textarea>
                                @error('deskripsi_kendala')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Catatan Petugas -->
                            <div class="mb-4">
                                <label for="catatan_petugas" class="block text-sm font-medium text-gray-700 mb-2">Catatan Petugas</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan_petugas') border-red-500 @enderror" 
                                          id="catatan_petugas" name="catatan_petugas" rows="3" 
                                          placeholder="Catatan tambahan dari petugas...">{{ old('catatan_petugas') ?? $patroliMandiri->catatan_petugas }}</textarea>
                                @error('catatan_petugas')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tindakan yang Diambil -->
                            <div id="tindakanSection" class="mb-4" style="display: {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'tidak_aman' ? 'block' : 'none' }};">
                                <label for="tindakan_yang_diambil" class="block text-sm font-medium text-gray-700 mb-2">Tindakan yang Diambil</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tindakan_yang_diambil') border-red-500 @enderror" 
                                          id="tindakan_yang_diambil" name="tindakan_yang_diambil" rows="3" 
                                          placeholder="Jelaskan tindakan yang sudah diambil...">{{ old('tindakan_yang_diambil') ?? $patroliMandiri->tindakan_yang_diambil }}</textarea>
                                @error('tindakan_yang_diambil')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Foto Lokasi -->
                            <div class="mb-4">
                                <label for="foto_lokasi" class="block text-sm font-medium text-gray-700 mb-2">Foto Lokasi</label>
                                @if($patroliMandiri->foto_lokasi)
                                    <div class="mb-3">
                                        <img src="{{ $patroliMandiri->foto_lokasi_url }}" class="w-32 h-32 object-cover rounded-lg border">
                                        <p class="text-xs text-gray-500 mt-1">Foto saat ini. Upload file baru untuk mengganti.</p>
                                    </div>
                                @endif
                                <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('foto_lokasi') border-red-500 @enderror" 
                                       id="foto_lokasi" name="foto_lokasi" accept="image/*">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                                @error('foto_lokasi')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <div id="fotoLokasiPreview" class="mt-2 hidden">
                                    <img id="fotoLokasiImg" src="" class="w-32 h-32 object-cover rounded-lg">
                                </div>
                            </div>

                            <!-- Foto Kendala -->
                            <div id="fotoKendalaSection" class="mb-6" style="display: {{ (old('status_lokasi') ?? $patroliMandiri->status_lokasi) == 'tidak_aman' ? 'block' : 'none' }};">
                                <label for="foto_kendala" class="block text-sm font-medium text-gray-700 mb-2">Foto Kendala</label>
                                @if($patroliMandiri->foto_kendala)
                                    <div class="mb-3">
                                        <img src="{{ $patroliMandiri->foto_kendala_url }}" class="w-32 h-32 object-cover rounded-lg border">
                                        <p class="text-xs text-gray-500 mt-1">Foto saat ini. Upload file baru untuk mengganti.</p>
                                    </div>
                                @endif
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
                                <a href="{{ route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-times mr-2"></i>Batal
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i>Update Laporan
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
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Edit</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Catatan:</p>
                                    <p class="text-sm text-blue-700">Laporan hanya dapat diedit jika statusnya masih "Submitted" dan belum direview oleh supervisor.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Status Saat Ini:</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $patroliMandiri->status_laporan_badge }}">
                                {{ ucfirst($patroliMandiri->status_laporan) }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Waktu Laporan:</h4>
                            <p class="text-sm text-gray-600">{{ $patroliMandiri->waktu_laporan->format('d/m/Y H:i:s') }}</p>
                        </div>

                        @if($patroliMandiri->reviewed_by)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Direview Oleh:</h4>
                            <p class="text-sm text-gray-600">{{ $patroliMandiri->reviewer->name ?? '-' }}</p>
                        </div>
                        @endif

                        <div class="mb-4">
                            <h4 class="font-medium text-blue-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-2"></i>Koordinat GPS
                            </h4>
                            <p class="text-sm text-gray-600 mb-2">
                                Copy koordinat langsung dari Google Maps dengan format: latitude, longitude
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded p-2">
                                <p class="text-xs text-blue-700 font-medium">Cara copy dari Google Maps:</p>
                                <p class="text-xs text-blue-600">1. Buka Google Maps</p>
                                <p class="text-xs text-blue-600">2. Klik kanan pada lokasi</p>
                                <p class="text-xs text-blue-600">3. Copy koordinat yang muncul</p>
                                <p class="text-xs text-blue-600">4. Paste di field Koordinat GPS</p>
                            </div>
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
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    initializeSelect2();
});

function setupEventListeners() {
    // Koordinat input processing
    document.getElementById('koordinat').addEventListener('input', function() {
        processKoordinatInput(this.value);
    });

    // Project change
    document.getElementById('project_id').addEventListener('change', function() {
        loadAreasByProject(this.value);
    });

    // File inputs for preview
    document.getElementById('foto_lokasi').addEventListener('change', function() {
        previewImage(this, 'fotoLokasiPreview', 'fotoLokasiImg');
    });

    document.getElementById('foto_kendala').addEventListener('change', function() {
        previewImage(this, 'fotoKendalaPreview', 'fotoKendalaImg');
    });
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
    const jenisKendalaInput = document.getElementById('jenis_kendala');
    jenisKendalaInput.required = isNotSafe;
    
    if (!isNotSafe) {
        jenisKendalaInput.value = '';
        $('#jenis_kendala_select').val(null).trigger('change');
    }
}

async function loadAreasByProject(projectId) {
    const select = document.getElementById('area_patrol_id');
    const currentAreaId = '{{ $patroliMandiri->area_patrol_id }}';
    
    select.innerHTML = '<option value="">Pilih Area (Opsional)</option>';
    
    if (!projectId) return;

    try {
        const response = await fetch(`{{ url('perusahaan/patroli-mandiri-areas') }}/${projectId}`);
        const data = await response.json();
        
        if (data.success) {
            data.data.forEach(area => {
                const option = document.createElement('option');
                option.value = area.id;
                option.textContent = area.nama;
                if (area.id == currentAreaId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading areas:', error);
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