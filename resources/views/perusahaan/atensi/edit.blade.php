@extends('perusahaan.layouts.app')

@section('title', 'Edit Atensi')
@section('page-title', 'Edit Atensi')
@section('page-subtitle', 'Update pengumuman atau perintah untuk tim')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('perusahaan.atensi.update', $atensi->hash_id) }}" method="POST" id="formAtensi">
            @csrf
            @method('PUT')
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Judul Atensi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Atensi <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="judul" 
                            required
                            value="{{ old('judul', $atensi->judul) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Masukkan Judul Atensi"
                        >
                        @error('judul')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Penerima -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Target Penerima <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="target_type" 
                            id="target_type"
                            required
                            onchange="handleTargetTypeChange()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Target</option>
                            <option value="all" {{ old('target_type', $atensi->target_type) == 'all' ? 'selected' : '' }}>👥 Semua Orang di Project</option>
                            <option value="area" {{ old('target_type', $atensi->target_type) == 'area' ? 'selected' : '' }}>📍 Berdasarkan Area</option>
                            <option value="jabatan" {{ old('target_type', $atensi->target_type) == 'jabatan' ? 'selected' : '' }}>👔 Berdasarkan Jabatan</option>
                            <option value="specific_users" {{ old('target_type', $atensi->target_type) == 'specific_users' ? 'selected' : '' }}>👤 Orang Tertentu</option>
                        </select>
                        @error('target_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Data (Dynamic) -->
                    <div id="target_data_container" class="hidden">
                        <!-- Jabatan Selection -->
                        <div id="jabatan_selection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jabatan</label>
                            <select 
                                name="target_data[jabatan_ids][]" 
                                id="jabatan_ids"
                                multiple
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                                @foreach($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}" 
                                        {{ (old('target_data.jabatan_ids') && in_array($jabatan->id, old('target_data.jabatan_ids'))) || 
                                           ($atensi->target_data && isset($atensi->target_data['jabatan_ids']) && in_array($jabatan->id, $atensi->target_data['jabatan_ids'])) ? 'selected' : '' }}>
                                        {{ $jabatan->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Users Selection -->
                        <div id="users_selection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Orang</label>
                            <select 
                                name="target_data[user_ids][]" 
                                id="user_ids"
                                multiple
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ (old('target_data.user_ids') && in_array($user->id, old('target_data.user_ids'))) || 
                                           ($atensi->target_data && isset($atensi->target_data['user_ids']) && in_array($user->id, $atensi->target_data['user_ids'])) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Project -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="project_id" 
                            id="project_id"
                            required
                            onchange="loadAreas()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $atensi->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Area -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="area_id" 
                            id="area_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Area</option>
                        </select>
                        @error('area_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prioritas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="prioritas" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Prioritas</option>
                            <option value="low" {{ old('prioritas', $atensi->prioritas) == 'low' ? 'selected' : '' }}>🟢 Rendah</option>
                            <option value="medium" {{ old('prioritas', $atensi->prioritas) == 'medium' ? 'selected' : '' }}>🟡 Sedang</option>
                            <option value="high" {{ old('prioritas', $atensi->prioritas) == 'high' ? 'selected' : '' }}>🔴 Tinggi</option>
                        </select>
                        @error('prioritas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_mulai" 
                                required
                                value="{{ old('tanggal_mulai', $atensi->tanggal_mulai->format('Y-m-d')) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                            @error('tanggal_mulai')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_selesai" 
                                required
                                value="{{ old('tanggal_selesai', $atensi->tanggal_selesai->format('Y-m-d')) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                            @error('tanggal_selesai')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="deskripsi" 
                            required
                            rows="5"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Tambahkan Deskripsi"
                        >{{ old('deskripsi', $atensi->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Options -->
                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="hidden" name="is_urgent" value="0">
                            <input type="checkbox" name="is_urgent" value="1" 
                                {{ old('is_urgent', $atensi->is_urgent) ? 'checked' : '' }} 
                                class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Mendesak</span>
                        </label>

                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" 
                                {{ old('is_active', $atensi->is_active) ? 'checked' : '' }} 
                                class="rounded border-gray-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('perusahaan.atensi.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition text-center">
                        Batal
                    </a>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium transition"
                    >
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function loadAreas() {
    const projectId = document.getElementById('project_id').value;
    const areaSelect = document.getElementById('area_id');
    const currentAreaId = {{ $atensi->area_id ?? 'null' }};
    
    // Clear existing options
    areaSelect.innerHTML = '<option value="">Pilih Area</option>';
    
    if (!projectId) return;
    
    try {
        console.log('Loading areas for project ID:', projectId); // Debug log
        const response = await fetch(`/perusahaan/projects/${projectId}/areas`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const areas = await response.json();
        console.log('Areas loaded:', areas); // Debug log
        
        areas.forEach(area => {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.nama;
            
            // Select current area if it matches
            if (area.id == currentAreaId) {
                option.selected = true;
            }
            
            areaSelect.appendChild(option);
        });
        
        if (areas.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Tidak ada area untuk project ini';
            option.disabled = true;
            areaSelect.appendChild(option);
        }
    } catch (error) {
        console.error('Error loading areas:', error);
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Error loading areas';
        option.disabled = true;
        areaSelect.appendChild(option);
    }
}

function handleTargetTypeChange() {
    const targetType = document.getElementById('target_type').value;
    const container = document.getElementById('target_data_container');
    const jabatanSelection = document.getElementById('jabatan_selection');
    const usersSelection = document.getElementById('users_selection');
    
    // Hide all selections
    container.classList.add('hidden');
    jabatanSelection.classList.add('hidden');
    usersSelection.classList.add('hidden');
    
    // Show relevant selection
    if (targetType === 'jabatan') {
        container.classList.remove('hidden');
        jabatanSelection.classList.remove('hidden');
    } else if (targetType === 'specific_users') {
        container.classList.remove('hidden');
        usersSelection.classList.remove('hidden');
    }
}

// Initialize Select2 for multiple selects
$(document).ready(function() {
    $('#jabatan_ids').select2({
        placeholder: 'Pilih jabatan...',
        allowClear: true
    });
    
    $('#user_ids').select2({
        placeholder: 'Pilih orang...',
        allowClear: true
    });
    
    // Initialize target type on page load
    handleTargetTypeChange();
    
    // Load areas for current project
    const projectId = document.getElementById('project_id').value;
    if (projectId) {
        loadAreas();
    }
});
</script>
@endpush