@extends('perusahaan.layouts.app')

@section('title', 'Edit Mutasi Aset')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Edit Mutasi Aset</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $mutasiAset->nomor_mutasi }}</p>
                    </div>
                    <a href="{{ route('perusahaan.mutasi-aset.show', $mutasiAset->hash_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
                
                <form action="{{ route('perusahaan.mutasi-aset.update', $mutasiAset->hash_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <label for="tanggal_mutasi" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Mutasi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_mutasi') border-red-500 @enderror" 
                                           id="tanggal_mutasi" name="tanggal_mutasi" value="{{ old('tanggal_mutasi', $mutasiAset->tanggal_mutasi->format('Y-m-d')) }}" required>
                                    @error('tanggal_mutasi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="project_asal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Project Asal <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_asal_id') border-red-500 @enderror" 
                                            id="project_asal_id" name="project_asal_id" required onchange="loadAssetsByProject()">
                                        <option value="">Pilih Project Asal</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_asal_id', $mutasiAset->project_asal_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_asal_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="asset_type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipe Aset <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('asset_type') border-red-500 @enderror" 
                                            id="asset_type" name="asset_type" required onchange="loadAssetsByProject()">
                                        <option value="">Pilih Tipe Aset</option>
                                        <option value="data_aset" {{ old('asset_type', $mutasiAset->asset_type) == 'data_aset' ? 'selected' : '' }}>Data Aset</option>
                                        <option value="aset_kendaraan" {{ old('asset_type', $mutasiAset->asset_type) == 'aset_kendaraan' ? 'selected' : '' }}>Aset Kendaraan</option>
                                    </select>
                                    @error('asset_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Aset <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('asset_id') border-red-500 @enderror" 
                                            id="asset_id" name="asset_id" required>
                                        <option value="">Pilih project asal dan tipe aset terlebih dahulu</option>
                                    </select>
                                    @error('asset_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="project_tujuan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Project Tujuan <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_tujuan_id') border-red-500 @enderror" 
                                            id="project_tujuan_id" name="project_tujuan_id" required>
                                        <option value="">Pilih Project Tujuan</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_tujuan_id', $mutasiAset->project_tujuan_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_tujuan_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div>
                                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Karyawan Penanggung Jawab <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('karyawan_id') border-red-500 @enderror" 
                                            id="karyawan_id" name="karyawan_id" required>
                                        <option value="">Cari dan pilih karyawan...</option>
                                    </select>
                                    @error('karyawan_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="alasan_mutasi" class="block text-sm font-medium text-gray-700 mb-2">
                                        Alasan Mutasi <span class="text-red-500">*</span>
                                    </label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alasan_mutasi') border-red-500 @enderror" 
                                              id="alasan_mutasi" name="alasan_mutasi" rows="4" required placeholder="Jelaskan alasan mutasi aset...">{{ old('alasan_mutasi', $mutasiAset->alasan_mutasi) }}</textarea>
                                    @error('alasan_mutasi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                        Keterangan
                                    </label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror" 
                                              id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $mutasiAset->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="dokumen_pendukung" class="block text-sm font-medium text-gray-700 mb-2">
                                        Dokumen Pendukung
                                    </label>
                                    @if($mutasiAset->dokumen_pendukung)
                                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p class="text-sm text-blue-800 mb-2">
                                                <i class="fas fa-file mr-1"></i>
                                                Dokumen saat ini:
                                            </p>
                                            <a href="{{ Storage::url($mutasiAset->dokumen_pendukung) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                Lihat Dokumen
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dokumen_pendukung') border-red-500 @enderror" 
                                           id="dokumen_pendukung" name="dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png">
                                    <p class="mt-1 text-sm text-gray-500">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                                    @error('dokumen_pendukung')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ route('perusahaan.mutasi-aset.show', $mutasiAset->hash_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Update Mutasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px;
    padding-left: 0;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
}
.select2-dropdown {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const currentAssetId = {{ $mutasiAset->asset_id }};
const currentKaryawanId = {{ $mutasiAset->karyawan_id }};
let assetSelect2 = null;
let karyawanSelect2 = null;

// Initialize Select2 for Karyawan
function initKaryawanSelect2() {
    if (karyawanSelect2) {
        karyawanSelect2.destroy();
    }
    
    karyawanSelect2 = $('#karyawan_id').select2({
        placeholder: 'Cari dan pilih karyawan...',
        allowClear: true,
        ajax: {
            url: '{{ route("perusahaan.mutasi-aset.search-karyawan") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.success ? data.data : []
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    // Set current karyawan
    if (currentKaryawanId) {
        fetch(`{{ route("perusahaan.mutasi-aset.search-karyawan") }}?search=`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectedKaryawan = data.data.find(k => k.id == currentKaryawanId);
                    if (selectedKaryawan) {
                        const option = new Option(selectedKaryawan.text, selectedKaryawan.id, true, true);
                        karyawanSelect2.append(option).trigger('change');
                    }
                }
            });
    }
}

// Initialize Select2 for Assets
function initAssetSelect2() {
    if (assetSelect2) {
        assetSelect2.destroy();
    }
    
    const projectAsalId = document.getElementById('project_asal_id').value;
    const assetType = document.getElementById('asset_type').value;
    
    if (!projectAsalId || !assetType) {
        $('#asset_id').html('<option value="">Pilih project asal dan tipe aset terlebih dahulu</option>');
        return;
    }
    
    assetSelect2 = $('#asset_id').select2({
        placeholder: 'Cari dan pilih aset...',
        allowClear: true,
        ajax: {
            url: '{{ route("perusahaan.mutasi-aset.assets-by-project") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    project_id: projectAsalId,
                    asset_type: assetType,
                    current_asset_id: currentAssetId
                };
            },
            processResults: function (data) {
                return {
                    results: data.success ? data.data : []
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Set current asset
    if (currentAssetId) {
        fetch(`{{ route("perusahaan.mutasi-aset.assets-by-project") }}?project_id=${projectAsalId}&asset_type=${assetType}&current_asset_id=${currentAssetId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectedAsset = data.data.find(a => a.id == currentAssetId);
                    if (selectedAsset) {
                        const option = new Option(selectedAsset.text, selectedAsset.id, true, true);
                        assetSelect2.append(option).trigger('change');
                    }
                }
            });
    }
}

// Function to load assets based on selected project and asset type
function loadAssetsByProject() {
    const projectAsalId = document.getElementById('project_asal_id').value;
    const assetType = document.getElementById('asset_type').value;
    
    if (projectAsalId && assetType) {
        initAssetSelect2();
    } else {
        // Clear asset select
        if (assetSelect2) {
            assetSelect2.destroy();
        }
        
        let message = '';
        if (!projectAsalId && !assetType) {
            message = 'Pilih project asal dan tipe aset terlebih dahulu';
        } else if (!projectAsalId) {
            message = 'Pilih project asal terlebih dahulu';
        } else if (!assetType) {
            message = 'Pilih tipe aset terlebih dahulu';
        }
        
        $('#asset_id').html(`<option value="">${message}</option>`);
    }
}

// Validate project tujuan different from project asal
function validateProjectTujuan() {
    const projectAsal = document.getElementById('project_asal_id').value;
    const projectTujuan = document.getElementById('project_tujuan_id').value;
    
    if (projectAsal && projectTujuan && projectAsal === projectTujuan) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Project tujuan harus berbeda dengan project asal'
        });
        document.getElementById('project_tujuan_id').value = '';
    }
}

// Event listeners
document.getElementById('project_asal_id').addEventListener('change', loadAssetsByProject);
document.getElementById('asset_type').addEventListener('change', loadAssetsByProject);
document.getElementById('project_tujuan_id').addEventListener('change', validateProjectTujuan);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Karyawan Select2
    initKaryawanSelect2();
    
    // Initialize Asset Select2
    const projectAsalId = document.getElementById('project_asal_id').value;
    const assetType = document.getElementById('asset_type').value;
    
    if (projectAsalId && assetType) {
        initAssetSelect2();
    }
});
</script>
@endpush