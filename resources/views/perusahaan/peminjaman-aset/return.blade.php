@extends('perusahaan.layouts.app')

@section('title', 'Kembalikan Aset')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id) }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kembalikan Aset</h1>
            <p class="text-gray-600 mt-1">{{ $peminjamanAset->kode_peminjaman }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Pengembalian -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Form Pengembalian Aset</h2>
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <div class="text-red-800">{{ session('error') }}</div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('perusahaan.peminjaman-aset.return', $peminjamanAset->hash_id) }}" method="POST" enctype="multipart/form-data" id="returnForm">
                @csrf
                
                <div class="space-y-6">
                    <!-- Kondisi Saat Dikembalikan -->
                    <div>
                        <label for="kondisi_saat_dikembalikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Kondisi Saat Dikembalikan <span class="text-red-500">*</span>
                        </label>
                        <select name="kondisi_saat_dikembalikan" id="kondisi_saat_dikembalikan" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Kondisi</option>
                            @foreach($kondisiOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('kondisi_saat_dikembalikan', 'baik') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('kondisi_saat_dikembalikan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan Pengembalian -->
                    <div>
                        <label for="catatan_pengembalian" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Pengembalian
                        </label>
                        <textarea name="catatan_pengembalian" id="catatan_pengembalian" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan kondisi aset, kerusakan, atau informasi lainnya...">{{ old('catatan_pengembalian') }}</textarea>
                        @error('catatan_pengembalian')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Bukti Pengembalian -->
                    <div>
                        <label for="file_bukti_pengembalian" class="block text-sm font-medium text-gray-700 mb-2">
                            File Bukti Pengembalian
                        </label>
                        <input type="file" name="file_bukti_pengembalian" id="file_bukti_pengembalian" accept=".jpg,.jpeg,.png,.pdf" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maksimal 5MB. Upload foto kondisi aset saat dikembalikan.</p>
                        @error('file_bukti_pengembalian')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Informasi Keterlambatan -->
                    @if($peminjamanAset->is_terlambat)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                <div>
                                    <h4 class="text-red-800 font-medium">Pengembalian Terlambat</h4>
                                    <p class="text-red-700 text-sm">
                                        Aset ini terlambat dikembalikan {{ $peminjamanAset->keterlambatan }} hari dari tanggal yang dijadwalkan 
                                        ({{ $peminjamanAset->tanggal_rencana_kembali->format('d/m/Y') }}).
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id) }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Pengembalian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <!-- Informasi Peminjaman -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Peminjaman</h3>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600 text-sm">Kode:</span>
                    <p class="font-medium">{{ $peminjamanAset->kode_peminjaman }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Peminjam:</span>
                    <p class="font-medium">{{ $peminjamanAset->peminjam_nama }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Tanggal Pinjam:</span>
                    <p class="font-medium">{{ $peminjamanAset->tanggal_peminjaman->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Rencana Kembali:</span>
                    <p class="font-medium {{ $peminjamanAset->is_terlambat ? 'text-red-600' : '' }}">
                        {{ $peminjamanAset->tanggal_rencana_kembali->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Durasi Peminjaman:</span>
                    <p class="font-medium">{{ $peminjamanAset->durasi_peminjaman }} hari</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Kondisi Saat Dipinjam:</span>
                    <p class="font-medium">{{ $peminjamanAset->kondisi_saat_dipinjam_label }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Aset -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Aset</h3>
            
            @if($peminjamanAset->aset_info && $peminjamanAset->aset_info->foto_url)
                <div class="mb-4">
                    <img src="{{ $peminjamanAset->aset_info->foto_url }}" alt="Foto Aset" class="w-full h-32 object-cover rounded-lg">
                </div>
            @endif
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600 text-sm">Kode Aset:</span>
                    <p class="font-medium">{{ $peminjamanAset->aset_kode }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Nama Aset:</span>
                    <p class="font-medium">{{ $peminjamanAset->aset_nama }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Kategori:</span>
                    <p class="font-medium">{{ $peminjamanAset->aset_kategori }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Jumlah Dipinjam:</span>
                    <p class="font-medium">{{ $peminjamanAset->jumlah_dipinjam }}</p>
                </div>
            </div>
        </div>

        <!-- Keperluan -->
        @if($peminjamanAset->keperluan)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Keperluan</h3>
                <p class="text-gray-700 text-sm">{{ $peminjamanAset->keperluan }}</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kondisiSelect = document.getElementById('kondisi_saat_dikembalikan');
    const catatanTextarea = document.getElementById('catatan_pengembalian');

    // Auto-suggest catatan based on kondisi
    kondisiSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        
        if (selectedValue === 'rusak_ringan') {
            if (!catatanTextarea.value) {
                catatanTextarea.value = 'Aset mengalami kerusakan ringan: ';
                catatanTextarea.focus();
                catatanTextarea.setSelectionRange(catatanTextarea.value.length, catatanTextarea.value.length);
            }
        } else if (selectedValue === 'rusak_berat') {
            if (!catatanTextarea.value) {
                catatanTextarea.value = 'Aset mengalami kerusakan berat: ';
                catatanTextarea.focus();
                catatanTextarea.setSelectionRange(catatanTextarea.value.length, catatanTextarea.value.length);
            }
        } else if (selectedValue === 'hilang') {
            if (!catatanTextarea.value) {
                catatanTextarea.value = 'Aset hilang. Keterangan: ';
                catatanTextarea.focus();
                catatanTextarea.setSelectionRange(catatanTextarea.value.length, catatanTextarea.value.length);
            }
        }
    });
});
</script>
@endpush