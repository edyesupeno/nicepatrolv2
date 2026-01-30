@extends('perusahaan.layouts.app')

@section('title', 'Detail Aset Kendaraan')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.aset-kendaraan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Aset Kendaraan
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ $asetKendaraan->kode_kendaraan }}</h1>
                <p class="text-gray-600 mt-1">{{ $asetKendaraan->merk }} {{ $asetKendaraan->model }} ({{ $asetKendaraan->tahun_pembuatan }})</p>
            </div>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $asetKendaraan->status_color }}-100 text-{{ $asetKendaraan->status_color }}-800">
                    {{ $asetKendaraan->status_label }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $asetKendaraan->jenis_label }}
                </span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex justify-between items-center">
            <div class="flex space-x-3">
                <a href="{{ route('perusahaan.aset-kendaraan.edit', $asetKendaraan->hash_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <button onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
            <div class="text-sm text-gray-500">
                Dibuat: {{ $asetKendaraan->created_at->format('d/m/Y H:i') }} oleh {{ $asetKendaraan->createdBy->name ?? 'System' }}
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button type="button" onclick="showTab('basic')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600" id="tab-basic">
                    Informasi Dasar
                </button>
                <button type="button" onclick="showTab('documents')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-documents">
                    Dokumen Kendaraan
                </button>
                <button type="button" onclick="showTab('operational')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-operational">
                    Operasional
                </button>
                <button type="button" onclick="showTab('files')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-files">
                    File & Foto
                </button>
            </nav>
        </div>

        <!-- Tab Content: Informasi Dasar -->
        <div id="content-basic" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kendaraan</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Project:</span>
                                <span class="font-medium">{{ $asetKendaraan->project->nama ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis Kendaraan:</span>
                                <span class="font-medium">{{ $asetKendaraan->jenis_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Merk:</span>
                                <span class="font-medium">{{ $asetKendaraan->merk }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Model:</span>
                                <span class="font-medium">{{ $asetKendaraan->model }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tahun Pembuatan:</span>
                                <span class="font-medium">{{ $asetKendaraan->tahun_pembuatan }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Warna:</span>
                                <span class="font-medium">{{ $asetKendaraan->warna }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Umur Kendaraan:</span>
                                <span class="font-medium">{{ $asetKendaraan->umur_kendaraan ?? '-' }} tahun</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900 mb-4">Identitas Kendaraan</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Polisi:</span>
                                <span class="font-medium font-mono">{{ $asetKendaraan->nomor_polisi }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Rangka:</span>
                                <span class="font-medium font-mono">{{ $asetKendaraan->nomor_rangka }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Mesin:</span>
                                <span class="font-medium font-mono">{{ $asetKendaraan->nomor_mesin }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-green-900 mb-4">Informasi Finansial</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Pembelian:</span>
                                <span class="font-medium">{{ $asetKendaraan->tanggal_pembelian?->format('d/m/Y') ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga Pembelian:</span>
                                <span class="font-medium">{{ $asetKendaraan->formatted_harga_pembelian }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nilai Penyusutan:</span>
                                <span class="font-medium">{{ $asetKendaraan->formatted_nilai_penyusutan }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-600 font-medium">Nilai Sekarang:</span>
                                <span class="font-bold text-green-600">{{ $asetKendaraan->formatted_nilai_sekarang }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-yellow-900 mb-4">Status & Kondisi</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status Kendaraan:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $asetKendaraan->status_color }}-100 text-{{ $asetKendaraan->status_color }}-800">
                                    {{ $asetKendaraan->status_label }}
                                </span>
                            </div>
                            @if($asetKendaraan->catatan)
                                <div>
                                    <span class="text-gray-600">Catatan:</span>
                                    <p class="mt-1 text-sm text-gray-700">{{ $asetKendaraan->catatan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Dokumen Kendaraan -->
        <div id="content-documents" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: STNK & BPKB -->
                <div class="space-y-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900 mb-4">STNK (Surat Tanda Nomor Kendaraan)</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor STNK:</span>
                                <span class="font-medium">{{ $asetKendaraan->nomor_stnk }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Berlaku:</span>
                                <div class="text-right">
                                    <span class="font-medium">{{ $asetKendaraan->tanggal_berlaku_stnk?->format('d/m/Y') ?? '-' }}</span>
                                    @if($asetKendaraan->tanggal_berlaku_stnk)
                                        @php
                                            $stnkDaysLeft = floor(now()->diffInDays($asetKendaraan->tanggal_berlaku_stnk, false));
                                        @endphp
                                        @if($stnkDaysLeft < 0)
                                            <span class="block text-xs text-red-600 font-medium">Lewat {{ abs($stnkDaysLeft) }} hari</span>
                                        @elseif($stnkDaysLeft <= 30)
                                            <span class="block text-xs text-yellow-600 font-medium">{{ $stnkDaysLeft }} hari lagi</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-green-900 mb-4">BPKB (Buku Pemilik Kendaraan Bermotor)</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor BPKB:</span>
                                <span class="font-medium">{{ $asetKendaraan->nomor_bpkb }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Atas Nama:</span>
                                <span class="font-medium">{{ $asetKendaraan->atas_nama_bpkb }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Asuransi & Pajak -->
                <div class="space-y-6">
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-purple-900 mb-4">Asuransi Kendaraan</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Perusahaan Asuransi:</span>
                                <span class="font-medium">{{ $asetKendaraan->perusahaan_asuransi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Polis:</span>
                                <span class="font-medium">{{ $asetKendaraan->nomor_polis_asuransi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Berlaku:</span>
                                <div class="text-right">
                                    <span class="font-medium">{{ $asetKendaraan->tanggal_berlaku_asuransi?->format('d/m/Y') ?? '-' }}</span>
                                    @if($asetKendaraan->tanggal_berlaku_asuransi)
                                        @php
                                            $asuransiDaysLeft = floor(now()->diffInDays($asetKendaraan->tanggal_berlaku_asuransi, false));
                                        @endphp
                                        @if($asuransiDaysLeft < 0)
                                            <span class="block text-xs text-red-600 font-medium">Lewat {{ abs($asuransiDaysLeft) }} hari</span>
                                        @elseif($asuransiDaysLeft <= 30)
                                            <span class="block text-xs text-yellow-600 font-medium">{{ $asuransiDaysLeft }} hari lagi</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-orange-900 mb-4">Pajak Kendaraan</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nilai Pajak Tahunan:</span>
                                <span class="font-medium">{{ $asetKendaraan->formatted_nilai_pajak_tahunan }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jatuh Tempo:</span>
                                <div class="text-right">
                                    <span class="font-medium">{{ $asetKendaraan->jatuh_tempo_pajak?->format('d/m/Y') ?? '-' }}</span>
                                    @if($asetKendaraan->jatuh_tempo_pajak)
                                        @php
                                            $pajakDaysLeft = floor(now()->diffInDays($asetKendaraan->jatuh_tempo_pajak, false));
                                        @endphp
                                        @if($pajakDaysLeft < 0)
                                            <span class="block text-xs text-red-600 font-medium">Lewat {{ abs($pajakDaysLeft) }} hari</span>
                                        @elseif($pajakDaysLeft <= 30)
                                            <span class="block text-xs text-yellow-600 font-medium">{{ $pajakDaysLeft }} hari lagi</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Operasional -->
        <div id="content-operational" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Operasional</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Driver Utama:</span>
                                <span class="font-medium">{{ $asetKendaraan->driver_utama ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lokasi Parkir:</span>
                                <span class="font-medium">{{ $asetKendaraan->lokasi_parkir ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kilometer Terakhir:</span>
                                <span class="font-medium">{{ number_format($asetKendaraan->kilometer_terakhir ?? 0) }} km</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-yellow-900 mb-4">Maintenance</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service Terakhir:</span>
                                <span class="font-medium">{{ $asetKendaraan->tanggal_service_terakhir?->format('d/m/Y') ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service Berikutnya:</span>
                                <span class="font-medium">{{ $asetKendaraan->tanggal_service_berikutnya?->format('d/m/Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($asetKendaraan->catatan)
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-blue-900 mb-4">Catatan</h3>
                            <p class="text-gray-700">{{ $asetKendaraan->catatan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Content: File & Foto -->
        <div id="content-files" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Foto Kendaraan</h3>
                        @if($asetKendaraan->foto_kendaraan)
                            <div class="border rounded-lg p-4">
                                <img src="{{ $asetKendaraan->foto_url }}" alt="Foto Kendaraan" class="w-full h-64 object-cover rounded-lg">
                                <div class="mt-3 text-center">
                                    <a href="{{ $asetKendaraan->foto_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>Lihat Foto Penuh
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">Tidak ada foto kendaraan</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">File STNK</h3>
                        @if($asetKendaraan->file_stnk)
                            <div class="border rounded-lg p-4 text-center">
                                <i class="fas fa-file-alt text-4xl text-blue-600 mb-3"></i>
                                <p class="text-sm text-gray-600 mb-3">File STNK tersedia</p>
                                <a href="{{ $asetKendaraan->file_stnk_url }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-download mr-2"></i>Lihat File
                                </a>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">Tidak ada file STNK</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">File BPKB</h3>
                        @if($asetKendaraan->file_bpkb)
                            <div class="border rounded-lg p-4 text-center">
                                <i class="fas fa-file-alt text-4xl text-green-600 mb-3"></i>
                                <p class="text-sm text-gray-600 mb-3">File BPKB tersedia</p>
                                <a href="{{ $asetKendaraan->file_bpkb_url }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-download mr-2"></i>Lihat File
                                </a>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">Tidak ada file BPKB</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">File Asuransi</h3>
                        @if($asetKendaraan->file_asuransi)
                            <div class="border rounded-lg p-4 text-center">
                                <i class="fas fa-file-alt text-4xl text-purple-600 mb-3"></i>
                                <p class="text-sm text-gray-600 mb-3">File Asuransi tersedia</p>
                                <a href="{{ $asetKendaraan->file_asuransi_url }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-download mr-2"></i>Lihat File
                                </a>
                            </div>
                        @else
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">Tidak ada file asuransi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="delete-form" action="{{ route('perusahaan.aset-kendaraan.destroy', $asetKendaraan->hash_id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-blue-500', 'text-blue-600');
}

// Delete confirmation
function confirmDelete() {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data kendaraan {{ $asetKendaraan->kode_kendaraan }} akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush