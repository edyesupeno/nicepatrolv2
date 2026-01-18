@extends('perusahaan.layouts.app')

@section('title', 'Input Data Tamu')
@section('page-title', 'Input Data Tamu')
@section('page-subtitle', 'Catat kunjungan tamu baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('perusahaan.buku-tamu.store') }}" method="POST" enctype="multipart/form-data" id="formBukuTamu">
            @csrf
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Foto Tamu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera mr-2" style="color: #3B82C8;"></i>Foto Tamu
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50" id="fotoPreview">
                                <i class="fas fa-user text-gray-400 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    name="foto" 
                                    id="foto"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="hidden"
                                    onchange="previewFoto(this)"
                                >
                                <label for="foto" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-upload mr-2"></i>Pilih Foto
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                            </div>
                        </div>
                        @error('foto')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Tamu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Nama Tamu <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_tamu" 
                            required
                            value="{{ old('nama_tamu') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Masukkan nama lengkap tamu"
                        >
                        @error('nama_tamu')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="project_id" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
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

                    <!-- Area -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Area
                        </label>
                        <select 
                            name="area_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Area (Opsional)</option>
                            @forelse($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @empty
                                <option value="" disabled>Belum ada area tersedia</option>
                            @endforelse
                        </select>
                        @error('area_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Perusahaan Tamu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase mr-2" style="color: #3B82C8;"></i>Perusahaan
                        </label>
                        <input 
                            type="text" 
                            name="perusahaan_tamu" 
                            value="{{ old('perusahaan_tamu') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Nama perusahaan tamu (opsional)"
                        >
                        @error('perusahaan_tamu')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keperluan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clipboard mr-2" style="color: #3B82C8;"></i>Keperluan <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="keperluan" 
                            required
                            value="{{ old('keperluan') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Tujuan kunjungan"
                        >
                        @error('keperluan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bertemu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-handshake mr-2" style="color: #3B82C8;"></i>Bertemu
                        </label>
                        <input 
                            type="text" 
                            name="bertemu" 
                            value="{{ old('bertemu') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Nama orang yang akan ditemui (opsional)"
                        >
                        @error('bertemu')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto Identitas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2" style="color: #3B82C8;"></i>Foto Identitas (KTP/SIM)
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50" id="fotoIdentitasPreview">
                                <i class="fas fa-id-card text-gray-400 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    name="foto_identitas" 
                                    id="foto_identitas"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="hidden"
                                    onchange="previewFotoIdentitas(this)"
                                >
                                <label for="foto_identitas" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-upload mr-2"></i>Pilih Foto Identitas
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                            </div>
                        </div>
                        @error('foto_identitas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kontak Darurat -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">
                            <i class="fas fa-phone mr-2" style="color: #3B82C8;"></i>Kontak Darurat (Opsional)
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                                <input 
                                    type="text" 
                                    name="kontak_darurat_nama" 
                                    value="{{ old('kontak_darurat_nama') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                    placeholder="Nama kontak darurat"
                                >
                                @error('kontak_darurat_nama')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                <input 
                                    type="text" 
                                    name="kontak_darurat_telepon" 
                                    value="{{ old('kontak_darurat_telepon') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                    placeholder="Nomor telepon"
                                >
                                @error('kontak_darurat_telepon')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hubungan</label>
                                <select 
                                    name="kontak_darurat_hubungan"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                >
                                    <option value="">Pilih Hubungan</option>
                                    <option value="Keluarga" {{ old('kontak_darurat_hubungan') == 'Keluarga' ? 'selected' : '' }}>Keluarga</option>
                                    <option value="Teman" {{ old('kontak_darurat_hubungan') == 'Teman' ? 'selected' : '' }}>Teman</option>
                                    <option value="Rekan Kerja" {{ old('kontak_darurat_hubungan') == 'Rekan Kerja' ? 'selected' : '' }}>Rekan Kerja</option>
                                    <option value="Lainnya" {{ old('kontak_darurat_hubungan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('kontak_darurat_hubungan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- No Kartu Pinjam -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-credit-card mr-2" style="color: #3B82C8;"></i>No Kartu Pinjam
                        </label>
                        <input 
                            type="text" 
                            name="no_kartu_pinjam" 
                            value="{{ old('no_kartu_pinjam') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Nomor kartu akses yang dipinjamkan (opsional)"
                        >
                        @error('no_kartu_pinjam')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-2" style="color: #3B82C8;"></i>Keterangan Tambahan
                        </label>
                        <textarea 
                            name="keterangan_tambahan" 
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Keterangan tambahan dari petugas (opsional)"
                        >{{ old('keterangan_tambahan') }}</textarea>
                        @error('keterangan_tambahan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2" style="color: #3B82C8;"></i>Catatan
                        </label>
                        <textarea 
                            name="catatan" 
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Catatan tambahan (opsional)"
                        >{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 mb-1">Informasi</h4>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li>• Tamu akan otomatis tercatat sebagai "Sedang Berkunjung"</li>
                                    <li>• QR Code akan dibuat otomatis untuk tracking</li>
                                    <li>• Waktu check-in akan dicatat saat data disimpan</li>
                                    <li>• Anda tercatat sebagai karyawan yang menginput data</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('perusahaan.buku-tamu.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium transition"
                    >
                        <i class="fas fa-save mr-2"></i>Simpan Data Tamu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewFoto(input) {
    const preview = document.getElementById('fotoPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover rounded-lg">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<i class="fas fa-user text-gray-400 text-2xl"></i>';
    }
}

function previewFotoIdentitas(input) {
    const preview = document.getElementById('fotoIdentitasPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover rounded-lg">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<i class="fas fa-id-card text-gray-400 text-2xl"></i>';
    }
}

// Auto-focus nama tamu field
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="nama_tamu"]').focus();
});
</script>
@endpush