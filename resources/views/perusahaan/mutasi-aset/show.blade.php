@extends('perusahaan.layouts.app')

@section('title', 'Detail Mutasi Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Mutasi Aset</h1>
            <nav class="text-sm text-gray-600 mt-1">
                <a href="{{ route('perusahaan.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">›</span>
                <a href="{{ route('perusahaan.mutasi-aset.index') }}" class="hover:text-blue-600">Mutasi Aset</a>
                <span class="mx-2">›</span>
                <span>Detail</span>
            </nav>
        </div>
        <div class="flex gap-2">
            @if(in_array($mutasiAset->status, ['disetujui', 'selesai']))
                <a href="{{ route('perusahaan.mutasi-aset.print', $mutasiAset->hash_id) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2" target="_blank">
                    <i class="fas fa-print"></i> Print
                </a>
            @endif
            @if($mutasiAset->status == 'pending')
                <a href="{{ route('perusahaan.mutasi-aset.edit', $mutasiAset->hash_id) }}" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('perusahaan.mutasi-aset.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Mutasi -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Informasi Mutasi
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-32 font-semibold text-gray-700">Nomor Mutasi</span>
                                <span class="text-gray-900">{{ $mutasiAset->nomor_mutasi }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-semibold text-gray-700">Tanggal Mutasi</span>
                                <span class="text-gray-900">{{ $mutasiAset->tanggal_mutasi->format('d F Y') }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-semibold text-gray-700">Status</span>
                                <span>{!! $mutasiAset->status_badge !!}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-700">Karyawan PJ</span>
                                <span class="text-gray-900">{{ $mutasiAset->karyawan->nama_lengkap }}</span>
                                <span class="text-sm text-gray-500">{{ $mutasiAset->karyawan->nik_karyawan }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-700">Project Asal</span>
                                <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm mt-1 w-fit">
                                    {{ $mutasiAset->projectAsal->nama ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-700">Project Tujuan</span>
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mt-1 w-fit">
                                    {{ $mutasiAset->projectTujuan->nama ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Aset -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        @if($mutasiAset->asset_type == 'data_aset')
                            <i class="fas fa-box"></i>
                            Informasi Aset
                        @else
                            <i class="fas fa-car"></i>
                            Informasi Kendaraan
                        @endif
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-lg flex items-center justify-center text-white text-2xl
                                    {{ $mutasiAset->asset_type == 'data_aset' ? 'bg-blue-500' : 'bg-green-500' }}">
                            @if($mutasiAset->asset_type == 'data_aset')
                                <i class="fas fa-box"></i>
                            @else
                                <i class="fas fa-car"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $mutasiAset->asset_name }}</h3>
                            <p class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $mutasiAset->asset_type)) }}</p>
                        </div>
                    </div>

                    @if($mutasiAset->asset_type == 'data_aset' && $mutasiAset->dataAset)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="w-24 font-semibold text-gray-700">Kode Aset</span>
                                    <span class="text-gray-900">{{ $mutasiAset->dataAset->kode_aset }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-24 font-semibold text-gray-700">Kategori</span>
                                    <span class="text-gray-900">{{ $mutasiAset->dataAset->kategori }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="w-24 font-semibold text-gray-700">Kondisi</span>
                                    <span class="inline-block px-2 py-1 rounded text-sm
                                        {{ $mutasiAset->dataAset->kondisi == 'baik' ? 'bg-green-100 text-green-800' : 
                                           ($mutasiAset->dataAset->kondisi == 'rusak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($mutasiAset->dataAset->kondisi) }}
                                    </span>
                                </div>
                                @if($mutasiAset->dataAset->nilai_perolehan)
                                <div class="flex">
                                    <span class="w-24 font-semibold text-gray-700">Nilai</span>
                                    <span class="text-gray-900">Rp {{ number_format($mutasiAset->dataAset->nilai_perolehan, 0, ',', '.') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @elseif($mutasiAset->asset_type == 'aset_kendaraan' && $mutasiAset->asetKendaraan)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-700">Nomor Polisi</span>
                                    <span class="inline-block bg-gray-900 text-white px-3 py-1 rounded font-mono text-lg mt-1 w-fit">
                                        {{ $mutasiAset->asetKendaraan->nomor_polisi }}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="w-24 font-semibold text-gray-700">Merk/Model</span>
                                    <span class="text-gray-900">{{ $mutasiAset->asetKendaraan->merk }} {{ $mutasiAset->asetKendaraan->model }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="w-16 font-semibold text-gray-700">Tahun</span>
                                    <span class="text-gray-900">{{ $mutasiAset->asetKendaraan->tahun_pembuatan ?? 'N/A' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-16 font-semibold text-gray-700">Warna</span>
                                    <span class="text-gray-900">{{ $mutasiAset->asetKendaraan->warna ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Alasan & Keterangan -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-lg">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <i class="fas fa-comment-alt"></i>
                        Alasan & Keterangan
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Alasan Mutasi:</h3>
                        <div class="bg-gray-50 border-l-4 border-indigo-500 p-4 rounded">
                            {{ $mutasiAset->alasan_mutasi }}
                        </div>
                    </div>
                    @if($mutasiAset->keterangan)
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Keterangan:</h3>
                            <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded">
                                {{ $mutasiAset->keterangan }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            @if($mutasiAset->status != 'pending')
                <!-- Informasi Persetujuan -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="bg-yellow-500 text-white px-6 py-4 rounded-t-lg">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Informasi Persetujuan
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div>
                            <span class="font-semibold text-gray-700">Disetujui Oleh</span>
                            <p class="text-gray-900">{{ $mutasiAset->disetujuiOleh->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Tanggal</span>
                            <p class="text-gray-900">{{ $mutasiAset->tanggal_persetujuan ? $mutasiAset->tanggal_persetujuan->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        @if($mutasiAset->catatan_persetujuan)
                            <div>
                                <span class="font-semibold text-gray-700">Catatan</span>
                                <p class="text-gray-900">{{ $mutasiAset->catatan_persetujuan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($mutasiAset->dokumen_pendukung)
                <!-- Dokumen Pendukung -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="bg-gray-600 text-white px-6 py-4 rounded-t-lg">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-file"></i>
                            Dokumen Pendukung
                        </h2>
                    </div>
                    <div class="p-6 text-center">
                        <a href="{{ Storage::url($mutasiAset->dokumen_pendukung) }}" target="_blank" 
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-download"></i>
                            Lihat Dokumen
                        </a>
                    </div>
                </div>
            @endif

            <!-- Aksi -->
            @if($mutasiAset->status == 'pending')
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="bg-red-600 text-white px-6 py-4 rounded-t-lg">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-cogs"></i>
                            Aksi Persetujuan
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <button type="button" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2"
                                onclick="approveMutasi('approve')">
                            <i class="fas fa-check"></i>
                            Setujui
                        </button>
                        <button type="button" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2"
                                onclick="approveMutasi('reject')">
                            <i class="fas fa-times"></i>
                            Tolak
                        </button>
                    </div>
                </div>
            @elseif($mutasiAset->status == 'disetujui')
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Selesaikan Mutasi
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 text-sm mb-4">
                            Klik tombol ini setelah aset berhasil dipindahkan ke project tujuan
                        </p>
                        <button type="button" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2"
                                onclick="completeMutasi()">
                            <i class="fas fa-check-circle"></i>
                            Selesaikan Mutasi
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveMutasi(action) {
    const title = action === 'approve' ? 'Setujui Mutasi?' : 'Tolak Mutasi?';
    const text = action === 'approve' ? 'Mutasi aset akan disetujui' : 'Mutasi aset akan ditolak';
    
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: action === 'approve' ? 'Ya, Setujui!' : 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Catatan persetujuan (opsional)',
        inputAttributes: {
            'aria-label': 'Catatan persetujuan'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/mutasi-aset/{{ $mutasiAset->hash_id }}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    catatan_persetujuan: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memproses persetujuan'
                });
            });
        }
    });
}

function completeMutasi() {
    Swal.fire({
        title: 'Selesaikan Mutasi?',
        text: 'Pastikan aset sudah berhasil dipindahkan ke project tujuan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Selesaikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/mutasi-aset/{{ $mutasiAset->hash_id }}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyelesaikan mutasi'
                });
            });
        }
    });
}
</script>
@endpush

@push('scripts')
<script>
function approveMutasi(action) {
    const title = action === 'approve' ? 'Setujui Mutasi?' : 'Tolak Mutasi?';
    const text = action === 'approve' ? 'Mutasi aset akan disetujui' : 'Mutasi aset akan ditolak';
    
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: action === 'approve' ? 'Ya, Setujui!' : 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Catatan persetujuan (opsional)',
        inputAttributes: {
            'aria-label': 'Catatan persetujuan'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/mutasi-aset/{{ $mutasiAset->hash_id }}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    catatan_persetujuan: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memproses persetujuan'
                });
            });
        }
    });
}

function completeMutasi() {
    Swal.fire({
        title: 'Selesaikan Mutasi?',
        text: 'Pastikan aset sudah berhasil dipindahkan ke project tujuan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Selesaikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/mutasi-aset/{{ $mutasiAset->hash_id }}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyelesaikan mutasi'
                });
            });
        }
    });
}
</script>
@endpush