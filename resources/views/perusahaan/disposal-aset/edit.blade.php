@extends('perusahaan.layouts.app')

@section('title', 'Edit Disposal Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Disposal Aset</h1>
            <p class="text-gray-600 mt-1">{{ $disposalAset->nomor_disposal }}</p>
        </div>
        <a href="{{ route('perusahaan.disposal-aset.show', $disposalAset->hash_id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="{{ route('perusahaan.disposal-aset.update', $disposalAset->hash_id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $disposalAset->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Aset</label>
                        <input type="text" value="{{ ucfirst(str_replace('_', ' ', $disposalAset->asset_type)) }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                        <p class="text-sm text-gray-500 mt-1">Tipe aset tidak dapat diubah</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aset</label>
                        <input type="text" value="{{ $disposalAset->asset_code }} - {{ $disposalAset->asset_name }}" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                        <p class="text-sm text-gray-500 mt-1">Aset tidak dapat diubah</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Disposal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_disposal" value="{{ old('tanggal_disposal', $disposalAset->tanggal_disposal->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('tanggal_disposal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Disposal <span class="text-red-500">*</span></label>
                        <select name="jenis_disposal" id="jenis_disposal" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Jenis Disposal</option>
                            <option value="dijual" {{ old('jenis_disposal', $disposalAset->jenis_disposal) == 'dijual' ? 'selected' : '' }}>Dijual</option>
                            <option value="rusak" {{ old('jenis_disposal', $disposalAset->jenis_disposal) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="hilang" {{ old('jenis_disposal', $disposalAset->jenis_disposal) == 'hilang' ? 'selected' : '' }}>Hilang</option>
                            <option value="tidak_layak" {{ old('jenis_disposal', $disposalAset->jenis_disposal) == 'tidak_layak' ? 'selected' : '' }}>Tidak Layak</option>
                            <option value="expired" {{ old('jenis_disposal', $disposalAset->jenis_disposal) == 'expired' ? 'selected' : '' }}>Expired</option>
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
                        <textarea name="alasan_disposal" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan alasan mengapa aset ini perlu di-disposal...">{{ old('alasan_disposal', $disposalAset->alasan_disposal) }}</textarea>
                        @error('alasan_disposal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="sale_fields" class="space-y-4" style="display: {{ old('jenis_disposal', $disposalAset->jenis_disposal) == 'dijual' ? 'block' : 'none' }};">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Disposal (Harga Jual)</label>
                            <input type="number" name="nilai_disposal" value="{{ old('nilai_disposal', $disposalAset->nilai_disposal) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                            @error('nilai_disposal')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pembeli</label>
                            <input type="text" name="pembeli" value="{{ old('pembeli', $disposalAset->pembeli) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama pembeli">
                            @error('pembeli')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                        <textarea name="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan (opsional)">{{ old('catatan', $disposalAset->catatan) }}</textarea>
                        @error('catatan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kondisi Aset</label>
                        
                        @if($disposalAset->foto_kondisi)
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                            <img src="{{ Storage::url($disposalAset->foto_kondisi) }}" alt="Foto Kondisi" class="max-w-xs rounded-lg shadow-sm border">
                        </div>
                        @endif
                        
                        <input type="file" name="foto_kondisi" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Upload foto baru untuk mengganti foto lama (opsional)</p>
                        @error('foto_kondisi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Asset Info Display -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-2">Informasi Aset</h4>
                <div class="text-sm text-blue-800">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <strong>Kode:</strong> {{ $disposalAset->asset_code }}<br>
                            <strong>Nama:</strong> {{ $disposalAset->asset_name }}
                        </div>
                        <div>
                            <strong>Tipe:</strong> {{ ucfirst(str_replace('_', ' ', $disposalAset->asset_type)) }}<br>
                            <strong>Nilai Buku:</strong> {{ $disposalAset->formatted_nilai_buku }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('perusahaan.disposal-aset.show', $disposalAset->hash_id) }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisDisposalSelect = document.getElementById('jenis_disposal');
    const saleFields = document.getElementById('sale_fields');

    // Handle jenis disposal change
    jenisDisposalSelect.addEventListener('change', function() {
        if (this.value === 'dijual') {
            saleFields.style.display = 'block';
        } else {
            saleFields.style.display = 'none';
        }
    });
});
</script>
@endpush