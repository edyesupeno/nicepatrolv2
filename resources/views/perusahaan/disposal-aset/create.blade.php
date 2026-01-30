@extends('perusahaan.layouts.app')

@section('title', 'Ajukan Disposal Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ajukan Disposal Aset</h1>
            <p class="text-gray-600 mt-1">Buat pengajuan disposal untuk aset yang akan dihapus</p>
        </div>
        <a href="{{ route('perusahaan.disposal-aset.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="{{ route('perusahaan.disposal-aset.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Aset <span class="text-red-500">*</span></label>
                        <select name="asset_type" id="asset_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Tipe Aset</option>
                            <option value="data_aset" {{ old('asset_type') == 'data_aset' ? 'selected' : '' }}>Data Aset</option>
                            <option value="aset_kendaraan" {{ old('asset_type') == 'aset_kendaraan' ? 'selected' : '' }}>Aset Kendaraan</option>
                        </select>
                        @error('asset_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aset <span class="text-red-500">*</span></label>
                        <select name="asset_id" id="asset_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih aset terlebih dahulu</option>
                        </select>
                        @error('asset_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Disposal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_disposal" value="{{ old('tanggal_disposal', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('tanggal_disposal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Disposal <span class="text-red-500">*</span></label>
                        <select name="jenis_disposal" id="jenis_disposal" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Jenis Disposal</option>
                            <option value="dijual" {{ old('jenis_disposal') == 'dijual' ? 'selected' : '' }}>Dijual</option>
                            <option value="rusak" {{ old('jenis_disposal') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="hilang" {{ old('jenis_disposal') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                            <option value="tidak_layak" {{ old('jenis_disposal') == 'tidak_layak' ? 'selected' : '' }}>Tidak Layak</option>
                            <option value="expired" {{ old('jenis_disposal') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                        @error('jenis_disposal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Disposal <span class="text-red-500">*</span></label>
                        <textarea name="alasan_disposal" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan alasan mengapa aset ini perlu di-disposal...">{{ old('alasan_disposal') }}</textarea>
                        @error('alasan_disposal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="sale_fields" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Disposal (Harga Jual)</label>
                            <input type="number" name="nilai_disposal" value="{{ old('nilai_disposal') }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                            @error('nilai_disposal')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pembeli</label>
                            <input type="text" name="pembeli" value="{{ old('pembeli') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama pembeli">
                            @error('pembeli')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                        <textarea name="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kondisi Aset</label>
                        <input type="file" name="foto_kondisi" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Upload foto kondisi aset saat ini (opsional)</p>
                        @error('foto_kondisi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Asset Info Display -->
            <div id="asset_info" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg" style="display: none;">
                <h4 class="font-medium text-blue-900 mb-2">Informasi Aset</h4>
                <div id="asset_details" class="text-sm text-blue-800"></div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('perusahaan.disposal-aset.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Ajukan Disposal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const assetTypeSelect = document.getElementById('asset_type');
    const assetSelect = document.getElementById('asset_id');
    const jenisDisposalSelect = document.getElementById('jenis_disposal');
    const saleFields = document.getElementById('sale_fields');
    const assetInfo = document.getElementById('asset_info');
    const assetDetails = document.getElementById('asset_details');

    // Handle jenis disposal change
    jenisDisposalSelect.addEventListener('change', function() {
        if (this.value === 'dijual') {
            saleFields.style.display = 'block';
        } else {
            saleFields.style.display = 'none';
        }
    });

    // Handle project and asset type change
    function loadAssets() {
        const projectId = projectSelect.value;
        const assetType = assetTypeSelect.value;

        if (!projectId || !assetType) {
            assetSelect.innerHTML = '<option value="">Pilih project dan tipe aset terlebih dahulu</option>';
            assetInfo.style.display = 'none';
            return;
        }

        assetSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`{{ route('perusahaan.disposal-aset.get-assets') }}?project_id=${projectId}&asset_type=${assetType}`)
            .then(response => response.json())
            .then(data => {
                assetSelect.innerHTML = '<option value="">Pilih Aset</option>';
                
                if (data.success && data.data.length > 0) {
                    data.data.forEach(asset => {
                        const option = document.createElement('option');
                        option.value = asset.id;
                        option.textContent = asset.display;
                        option.dataset.assetData = JSON.stringify(asset);
                        assetSelect.appendChild(option);
                    });
                } else {
                    assetSelect.innerHTML = '<option value="">Tidak ada aset aktif</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                assetSelect.innerHTML = '<option value="">Error loading assets</option>';
            });
    }

    projectSelect.addEventListener('change', loadAssets);
    assetTypeSelect.addEventListener('change', loadAssets);

    // Handle asset selection
    assetSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.dataset.assetData) {
            const asset = JSON.parse(selectedOption.dataset.assetData);
            
            assetDetails.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Kode:</strong> ${asset.code}<br>
                        <strong>Nama:</strong> ${asset.name}
                    </div>
                    <div>
                        <strong>Kategori:</strong> ${asset.category}<br>
                        <strong>Nilai Buku:</strong> Rp ${new Intl.NumberFormat('id-ID').format(asset.value)}
                    </div>
                </div>
            `;
            assetInfo.style.display = 'block';
        } else {
            assetInfo.style.display = 'none';
        }
    });

    // Initialize if there are old values
    @if(old('jenis_disposal') == 'dijual')
        saleFields.style.display = 'block';
    @endif

    @if(old('project_id') && old('asset_type'))
        loadAssets();
    @endif
});
</script>
@endpush