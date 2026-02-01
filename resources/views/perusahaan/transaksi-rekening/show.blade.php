@extends('perusahaan.layouts.app')

@section('title', 'Detail Transaksi - ' . $transaksiRekening->nomor_transaksi)
@section('page-title', 'Detail Transaksi')
@section('page-subtitle', 'Informasi lengkap transaksi rekening')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    Transaksi Rekening
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $transaksiRekening->nomor_transaksi }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-receipt text-blue-500 mr-2"></i>
                Detail Transaksi
            </h1>
            <p class="text-gray-600 mt-1">{{ $transaksiRekening->nomor_transaksi }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(!$transaksiRekening->is_verified)
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="verifyTransaction()">
                <i class="fas fa-check mr-2"></i>
                Verifikasi
            </button>
            <a href="{{ route('perusahaan.keuangan.transaksi-rekening.edit', $transaksiRekening->hash_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="deleteTransaction()">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
            @else
            <button type="button" class="inline-flex items-center px-4 py-2 border border-yellow-300 rounded-lg text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" onclick="unverifyTransaction()">
                <i class="fas fa-times mr-2"></i>
                Batalkan Verifikasi
            </button>
            @endif
            <a href="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaction Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Main Transaction Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Transaksi
                    </h3>
                    @if($transaksiRekening->is_verified)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i>
                        Verified
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i>
                        Pending
                    </span>
                    @endif
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Nomor Transaksi:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $transaksiRekening->nomor_transaksi }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Tanggal Transaksi:</span>
                                <span class="text-sm text-gray-900">{{ $transaksiRekening->tanggal_transaksi ? $transaksiRekening->tanggal_transaksi->format('d F Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Jenis Transaksi:</span>
                                @if($transaksiRekening->jenis_transaksi === 'debit')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    Debit (Masuk)
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-arrow-down mr-1"></i>
                                    Kredit (Keluar)
                                </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Kategori:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $transaksiRekening->kategori_transaksi_label }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm font-medium text-gray-500">Referensi:</span>
                                <span class="text-sm text-gray-900">{{ $transaksiRekening->referensi ?: '-' }}</span>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Jumlah:</span>
                                <span class="text-lg font-bold {{ $transaksiRekening->jenis_transaksi === 'debit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaksiRekening->jenis_transaksi === 'debit' ? '+' : '-' }}{{ $transaksiRekening->formatted_jumlah }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Saldo Sebelum:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $transaksiRekening->formatted_saldo_sebelum }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Saldo Sesudah:</span>
                                <span class="text-sm font-semibold {{ $transaksiRekening->saldo_sesudah >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaksiRekening->formatted_saldo_sesudah }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Dibuat oleh:</span>
                                <span class="text-sm text-gray-900">{{ $transaksiRekening->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm font-medium text-gray-500">Waktu Dibuat:</span>
                                <span class="text-sm text-gray-900">{{ $transaksiRekening->created_at ? $transaksiRekening->created_at->format('d F Y H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-comment-alt mr-2 text-gray-500"></i>
                        Keterangan
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $transaksiRekening->keterangan }}</p>
                </div>
            </div>

            <!-- Verification Info -->
            @if($transaksiRekening->is_verified)
            <div class="bg-white rounded-xl shadow-sm border border-green-200">
                <div class="px-6 py-4 border-b border-green-200 bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                        Informasi Verifikasi
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Diverifikasi oleh:</p>
                            <p class="text-gray-900">{{ $transaksiRekening->verifiedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Waktu Verifikasi:</p>
                            <p class="text-gray-900">{{ $transaksiRekening->verified_at ? $transaksiRekening->verified_at->format('d F Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Rekening Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200" style="border-left: 4px solid {{ $transaksiRekening->rekening->warna_card }} !important;">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-university mr-2 text-blue-500"></i>
                        Informasi Rekening
                    </h3>
                </div>
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $transaksiRekening->rekening->nama_rekening }}</h4>
                    <p class="text-sm text-gray-500 mb-4">{{ $transaksiRekening->rekening->project->nama ?? 'N/A' }}</p>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Bank:</span>
                            <span class="text-sm text-gray-900">{{ $transaksiRekening->rekening->nama_bank }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Nomor:</span>
                            <span class="text-sm text-gray-900">{{ $transaksiRekening->rekening->nomor_rekening }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Pemilik:</span>
                            <span class="text-sm text-gray-900">{{ $transaksiRekening->rekening->nama_pemilik }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Jenis:</span>
                            <span class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $transaksiRekening->rekening->jenis_rekening)) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Saldo Saat Ini:</span>
                            <span class="text-sm font-semibold {{ $transaksiRekening->rekening->saldo_saat_ini >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaksiRekening->rekening->formatted_saldo_saat_ini }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                        Aksi Cepat
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('perusahaan.keuangan.laporan-arus-kas.show', $transaksiRekening->rekening->hash_id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-chart-line mr-2"></i>
                            Lihat Arus Kas Rekening
                        </a>
                        <a href="{{ route('perusahaan.keuangan.transaksi-rekening.create') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Transaksi Baru
                        </a>
                        <a href="{{ route('perusahaan.keuangan.rekening.show', $transaksiRekening->rekening->hash_id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-university mr-2"></i>
                            Detail Rekening
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function verifyTransaction() {
    Swal.fire({
        title: 'Verifikasi Transaksi',
        text: 'Yakin ingin memverifikasi transaksi ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Verifikasi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/keuangan/transaksi-rekening/{{ $transaksiRekening->hash_id }}/verify`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
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
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    });
}

function unverifyTransaction() {
    Swal.fire({
        title: 'Batalkan Verifikasi',
        text: 'Yakin ingin membatalkan verifikasi transaksi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/keuangan/transaksi-rekening/{{ $transaksiRekening->hash_id }}/unverify`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
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
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    });
}

function deleteTransaction() {
    Swal.fire({
        title: 'Hapus Transaksi',
        text: 'Yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/keuangan/transaksi-rekening/{{ $transaksiRekening->hash_id }}`;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.appendChild(methodInput);
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection