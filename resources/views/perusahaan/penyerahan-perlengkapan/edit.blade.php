@extends('perusahaan.layouts.app')

@section('page-title', 'Edit Penyerahan Perlengkapan')
@section('page-subtitle', 'Edit informasi penyerahan perlengkapan')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Penyerahan
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Edit Penyerahan Perlengkapan</h3>
        <p class="text-sm text-gray-500 mt-1">Hanya informasi dasar yang dapat diedit. Item tidak dapat diubah setelah penyerahan dibuat.</p>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('perusahaan.penyerahan-perlengkapan.update', $penyerahan->hash_id) }}">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Dasar</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $penyerahan->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $penyerahan->tanggal_mulai->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_mulai')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $penyerahan->tanggal_selesai->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_selesai')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Karyawan Selection -->
            <div class="mb-8">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Karyawan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                        <select name="jabatan_id" id="jabatan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                        <select name="karyawan_id" id="karyawan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Loading...</option>
                        </select>
                        @error('karyawan_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Current Items (Read Only) -->
            <div class="mb-8">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Item yang Diserahkan</h4>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">Item tidak dapat diubah setelah penyerahan dibuat. Untuk mengubah item, hapus penyerahan ini dan buat yang baru.</p>
                    
                    <div class="space-y-3">
                        @foreach($penyerahan->items as $item)
                        <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-200">
                            <div class="flex items-center">
                                @if($item->item->foto_item)
                                    <img class="h-10 w-10 rounded object-cover" src="{{ $item->item->foto_url }}" alt="{{ $item->item->nama_item }}">
                                @else
                                    <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-toolbox text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->item->nama_item }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->item->kategori->nama_kategori ?? 'Kategori tidak ditemukan' }}</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $item->jumlah_diserahkan }} {{ $item->item->satuan }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan tambahan...">{{ old('keterangan', $penyerahan->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">Update Penyerahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Current values
const currentKaryawanId = {{ $penyerahan->karyawan_id }};
const currentJabatanId = {{ $penyerahan->karyawan->jabatan_id ?? 'null' }};

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    const projectId = document.getElementById('project_id').value;
    if (projectId) {
        loadJabatanByProject(projectId, currentJabatanId);
    }
});

// Project change handler
document.getElementById('project_id').addEventListener('change', function() {
    const projectId = this.value;
    
    // Reset dependent dropdowns
    document.getElementById('jabatan_id').innerHTML = '<option value="">Loading...</option>';
    document.getElementById('karyawan_id').innerHTML = '<option value="">Pilih Jabatan terlebih dahulu</option>';
    
    if (projectId) {
        loadJabatanByProject(projectId);
    } else {
        document.getElementById('jabatan_id').innerHTML = '<option value="">Pilih Project terlebih dahulu</option>';
    }
});

// Jabatan change handler
document.getElementById('jabatan_id').addEventListener('change', function() {
    const jabatanId = this.value;
    const projectId = document.getElementById('project_id').value;
    
    document.getElementById('karyawan_id').innerHTML = '<option value="">Loading...</option>';
    
    if (jabatanId && projectId) {
        loadKaryawanByJabatan(jabatanId, projectId, currentKaryawanId);
    } else {
        document.getElementById('karyawan_id').innerHTML = '<option value="">Pilih Jabatan terlebih dahulu</option>';
    }
});

// Load functions
async function loadJabatanByProject(projectId, selectedJabatanId = null) {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/jabatan-by-project?project_id=${projectId}`);
        const result = await response.json();
        
        if (result.success) {
            let options = '<option value="">Pilih Jabatan</option>';
            result.data.forEach(jabatan => {
                const selected = selectedJabatanId && jabatan.id == selectedJabatanId ? 'selected' : '';
                options += `<option value="${jabatan.id}" ${selected}>${jabatan.nama}</option>`;
            });
            document.getElementById('jabatan_id').innerHTML = options;
            
            // If we have a selected jabatan, load karyawan
            if (selectedJabatanId) {
                loadKaryawanByJabatan(selectedJabatanId, projectId, currentKaryawanId);
            }
        }
    } catch (error) {
        console.error('Error loading jabatan:', error);
        document.getElementById('jabatan_id').innerHTML = '<option value="">Error loading jabatan</option>';
    }
}

async function loadKaryawanByJabatan(jabatanId, projectId, selectedKaryawanId = null) {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/karyawan-by-jabatan?jabatan_id=${jabatanId}&project_id=${projectId}`);
        const result = await response.json();
        
        if (result.success) {
            let options = '<option value="">Pilih Karyawan</option>';
            result.data.forEach(karyawan => {
                const selected = selectedKaryawanId && karyawan.id == selectedKaryawanId ? 'selected' : '';
                options += `<option value="${karyawan.id}" ${selected}>${karyawan.nama_lengkap}</option>`;
            });
            document.getElementById('karyawan_id').innerHTML = options;
        }
    } catch (error) {
        console.error('Error loading karyawan:', error);
        document.getElementById('karyawan_id').innerHTML = '<option value="">Error loading karyawan</option>';
    }
}
</script>
@endpush
@endsection