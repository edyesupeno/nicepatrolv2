@extends('perusahaan.layouts.app')

@section('title', 'Tambah Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Kru Change</h3>
            <a href="{{ route('perusahaan.kru-change.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('perusahaan.kru-change.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror" required>
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Area Selection -->
                <div>
                    <label for="area_patrol_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Area Patroli <span class="text-red-500">*</span>
                    </label>
                    <select name="area_patrol_id" id="area_patrol_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('area_patrol_id') border-red-500 @enderror" required disabled>
                        <option value="">Pilih Area</option>
                    </select>
                    @error('area_patrol_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tim Keluar -->
                <div>
                    <label for="tim_keluar_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tim Keluar <span class="text-red-500">*</span>
                    </label>
                    <select name="tim_keluar_id" id="tim_keluar_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('tim_keluar_id') border-red-500 @enderror" required disabled>
                        <option value="">Pilih Tim Keluar</option>
                    </select>
                    @error('tim_keluar_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tim Masuk -->
                <div>
                    <label for="tim_masuk_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tim Masuk <span class="text-red-500">*</span>
                    </label>
                    <select name="tim_masuk_id" id="tim_masuk_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('tim_masuk_id') border-red-500 @enderror" required disabled>
                        <option value="">Pilih Tim Masuk</option>
                    </select>
                    @error('tim_masuk_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Handover -->
                <div>
                    <label for="waktu_mulai_handover" class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Mulai Handover <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="waktu_mulai_handover" id="waktu_mulai_handover" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('waktu_mulai_handover') border-red-500 @enderror" 
                           value="{{ old('waktu_mulai_handover', now()->format('Y-m-d\TH:i')) }}" required>
                    @error('waktu_mulai_handover')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Petugas Keluar -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Petugas Keluar <span class="text-red-500">*</span>
                    </label>
                    <div id="petugas_keluar_container" class="border border-gray-300 rounded-lg p-3 bg-gray-50 min-h-[100px]">
                        <p class="text-gray-500 text-sm">Pilih tim keluar terlebih dahulu</p>
                    </div>
                    @error('petugas_keluar_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Petugas Masuk -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Petugas Masuk
                    </label>
                    <div id="petugas_masuk_container" class="border border-gray-300 rounded-lg p-3 bg-gray-50 min-h-[100px]">
                        <p class="text-gray-500 text-sm">Pilih tim masuk terlebih dahulu</p>
                    </div>
                    @error('petugas_masuk_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supervisor -->
                <div>
                    <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supervisor
                    </label>
                    <select name="supervisor_id" id="supervisor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('supervisor_id') border-red-500 @enderror" disabled>
                        <option value="">Pilih Supervisor</option>
                    </select>
                    @error('supervisor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Catatan Keluar -->
            <div class="mt-6">
                <label for="catatan_keluar" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Tim Keluar
                </label>
                <textarea name="catatan_keluar" id="catatan_keluar" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('catatan_keluar') border-red-500 @enderror" 
                          placeholder="Catatan atau instruksi khusus dari tim keluar untuk handover...">{{ old('catatan_keluar') }}</textarea>
                @error('catatan_keluar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto Tim -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Foto Tim Keluar -->
                <div>
                    <label for="foto_tim_keluar" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Tim Keluar
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="foto_tim_keluar" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload foto</span>
                                    <input id="foto_tim_keluar" name="foto_tim_keluar" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
                        </div>
                    </div>
                    @error('foto_tim_keluar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Tim Masuk -->
                <div>
                    <label for="foto_tim_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Tim Masuk
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="foto_tim_masuk" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload foto</span>
                                    <input id="foto_tim_masuk" name="foto_tim_masuk" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
                        </div>
                    </div>
                    @error('foto_tim_masuk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex space-x-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <a href="{{ route('perusahaan.kru-change.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let timPatrolisData = {};

$(document).ready(function() {
    // Load data when project changes
    $('#project_id').change(function() {
        const projectId = $(this).val();
        
        // Reset dependent dropdowns
        $('#area_patrol_id, #tim_keluar_id, #tim_masuk_id, #supervisor_id')
            .empty()
            .append('<option value="">Loading...</option>')
            .prop('disabled', true);
        
        // Reset petugas containers
        $('#petugas_keluar_container, #petugas_masuk_container').html('<p class="text-gray-500 text-sm">Pilih tim terlebih dahulu</p>');
        
        if (projectId) {
            $.get(`/perusahaan/kru-change-projects/${projectId}/data`)
                .done(function(response) {
                    if (response.success) {
                        // Store tim patrolis data for later use
                        timPatrolisData = {};
                        response.tim_patrolis.forEach(tim => {
                            timPatrolisData[tim.id] = tim;
                        });
                        
                        // Populate areas
                        $('#area_patrol_id').empty().append('<option value="">Pilih Area</option>');
                        response.areas.forEach(area => {
                            $('#area_patrol_id').append(`<option value="${area.id}">${area.nama}</option>`);
                        });
                        $('#area_patrol_id').prop('disabled', false);
                        
                        // Populate teams
                        $('#tim_keluar_id, #tim_masuk_id').empty().append('<option value="">Pilih Tim</option>');
                        response.tim_patrolis.forEach(tim => {
                            const option = `<option value="${tim.id}">${tim.nama_tim} (${tim.jenis_regu})</option>`;
                            $('#tim_keluar_id, #tim_masuk_id').append(option);
                        });
                        $('#tim_keluar_id, #tim_masuk_id').prop('disabled', false);
                        
                        // Populate supervisors
                        $('#supervisor_id').empty().append('<option value="">Pilih Supervisor</option>');
                        response.security_officers.forEach(officer => {
                            const option = `<option value="${officer.id}">${officer.name}</option>`;
                            $('#supervisor_id').append(option);
                        });
                        $('#supervisor_id').prop('disabled', false);
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Gagal memuat data project', 'error');
                    $('#area_patrol_id, #tim_keluar_id, #tim_masuk_id, #supervisor_id')
                        .empty()
                        .append('<option value="">Error loading data</option>')
                        .prop('disabled', true);
                });
        } else {
            $('#area_patrol_id, #tim_keluar_id, #tim_masuk_id, #supervisor_id')
                .empty()
                .append('<option value="">Pilih Project dulu</option>')
                .prop('disabled', true);
        }
    });
    
    // Handle tim keluar selection
    $('#tim_keluar_id').change(function() {
        const selectedValue = $(this).val();
        
        // Prevent selecting same team for masuk
        $('#tim_masuk_id option').prop('disabled', false);
        if (selectedValue) {
            $(`#tim_masuk_id option[value="${selectedValue}"]`).prop('disabled', true);
        }
        
        // Populate petugas keluar
        populatePetugasContainer('keluar', selectedValue);
    });
    
    // Handle tim masuk selection
    $('#tim_masuk_id').change(function() {
        const selectedValue = $(this).val();
        
        // Prevent selecting same team for keluar
        $('#tim_keluar_id option').prop('disabled', false);
        if (selectedValue) {
            $(`#tim_keluar_id option[value="${selectedValue}"]`).prop('disabled', true);
        }
        
        // Populate petugas masuk
        populatePetugasContainer('masuk', selectedValue);
    });
});

function populatePetugasContainer(type, timId) {
    const containerId = `petugas_${type}_container`;
    const container = $(`#${containerId}`);
    
    if (!timId || !timPatrolisData[timId]) {
        container.html('<p class="text-gray-500 text-sm">Pilih tim terlebih dahulu</p>');
        return;
    }
    
    const tim = timPatrolisData[timId];
    const anggota = tim.anggota_aktif || [];
    
    if (anggota.length === 0) {
        container.html('<p class="text-yellow-600 text-sm">Tim ini belum memiliki anggota aktif</p>');
        return;
    }
    
    // Function to get role name
    function getRoleName(role) {
        switch(role) {
            case 'leader': return 'Danru (Komandan Regu)';
            case 'wakil_leader': return 'Wakil Leader';
            case 'anggota': return 'Anggota';
            default: return 'Unknown';
        }
    }
    
    // Function to get role badge
    function getRoleBadge(role) {
        switch(role) {
            case 'leader': return '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Danru (Komandan Regu)</span>';
            case 'wakil_leader': return '<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Wakil Leader</span>';
            case 'anggota': return '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Anggota</span>';
            default: return '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Unknown</span>';
        }
    }
    
    let html = '<div class="space-y-2">';
    html += `<p class="text-sm font-medium text-gray-700 mb-2">Anggota Tim ${tim.nama_tim}:</p>`;
    
    anggota.forEach(member => {
        const user = member.user;
        const isChecked = true; // Auto-select all by default
        
        html += `
            <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded cursor-pointer">
                <input type="checkbox" 
                       name="petugas_${type}_ids[]" 
                       value="${user.id}" 
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       ${isChecked ? 'checked' : ''}>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                    <div class="text-xs text-gray-500">${user.email}</div>
                    <div class="mt-1">${getRoleBadge(member.role)}</div>
                </div>
            </label>
        `;
    });
    
    html += '</div>';
    container.html(html);
}

// Photo preview functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle foto tim keluar
    const fotoKeluarInput = document.getElementById('foto_tim_keluar');
    if (fotoKeluarInput) {
        fotoKeluarInput.addEventListener('change', function(e) {
            handlePhotoPreview(e.target, 'keluar');
        });
    }

    // Handle foto tim masuk
    const fotoMasukInput = document.getElementById('foto_tim_masuk');
    if (fotoMasukInput) {
        fotoMasukInput.addEventListener('change', function(e) {
            handlePhotoPreview(e.target, 'masuk');
        });
    }
});

function handlePhotoPreview(input, type) {
    const file = input.files[0];
    const container = input.closest('.border-dashed');
    
    if (file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 2MB'
            });
            input.value = '';
            return;
        }

        // Validate file type
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Salah',
                text: 'File harus berupa gambar (PNG, JPG, JPEG)'
            });
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            container.innerHTML = `
                <div class="relative">
                    <img src="${e.target.result}" alt="Preview Foto Tim ${type === 'keluar' ? 'Keluar' : 'Masuk'}" 
                         class="max-h-48 mx-auto rounded-lg shadow-md">
                    <button type="button" onclick="removePhotoPreview('${type}')" 
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <p class="text-xs text-gray-500 mt-2 text-center">${file.name}</p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}

function removePhotoPreview(type) {
    const input = document.getElementById(`foto_tim_${type}`);
    input.value = '';
    
    const container = input.closest('.border-dashed');
    container.innerHTML = `
        <div class="space-y-1 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="flex text-sm text-gray-600">
                <label for="foto_tim_${type}" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Upload foto</span>
                    <input id="foto_tim_${type}" name="foto_tim_${type}" type="file" class="sr-only" accept="image/*" onchange="handlePhotoPreview(this, '${type}')">
                </label>
                <p class="pl-1">atau drag and drop</p>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
        </div>
    `;
}
</script>
@endpush