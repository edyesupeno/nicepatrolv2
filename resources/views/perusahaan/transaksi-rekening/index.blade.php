@extends('perusahaan.layouts.app')

@section('title', 'Transaksi Rekening')
@section('page-title', 'Transaksi Rekening')
@section('page-subtitle', 'Kelola transaksi debit dan kredit rekening perusahaan')

@section('content')
<div class="space-y-6">
    @if($rekenings->isEmpty())
    <!-- Empty State - No Rekening -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-6">
                <i class="fas fa-university text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Rekening</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Anda perlu membuat rekening terlebih dahulu sebelum dapat mengelola transaksi. 
                Rekening digunakan untuk mencatat semua transaksi debit dan kredit perusahaan.
            </p>
            <div class="flex justify-center space-x-3">
                <a href="{{ route('perusahaan.keuangan.rekening.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Rekening Pertama
                </a>
                <a href="{{ route('perusahaan.keuangan.rekening.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-university mr-2"></i>
                    Kelola Rekening
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Header Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('perusahaan.keuangan.laporan-arus-kas.index') }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-chart-line mr-2"></i>
            Laporan Arus Kas
        </a>
        <a href="{{ route('perusahaan.keuangan.transaksi-rekening.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i>
            Tambah Transaksi
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-university text-blue-500 mr-2"></i>
                        Rekening
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="rekening_id">
                        <option value="">Semua Rekening</option>
                        @foreach($rekenings as $rekening)
                            <option value="{{ $rekening->id }}" {{ request('rekening_id') == $rekening->id ? 'selected' : '' }}>
                                {{ $rekening->nama_rekening }} - {{ $rekening->project->nama ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-exchange-alt text-blue-500 mr-2"></i>
                        Jenis
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="jenis_transaksi">
                        <option value="">Semua Jenis</option>
                        <option value="debit" {{ request('jenis_transaksi') === 'debit' ? 'selected' : '' }}>Debit (Masuk)</option>
                        <option value="kredit" {{ request('jenis_transaksi') === 'kredit' ? 'selected' : '' }}>Kredit (Keluar)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags text-blue-500 mr-2"></i>
                        Kategori
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="kategori_transaksi">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\TransaksiRekening::getAvailableKategori() as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori_transaksi') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        Status
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="is_verified">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('is_verified') === '1' ? 'selected' : '' }}>Verified</option>
                        <option value="0" {{ request('is_verified') === '0' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-blue-500 mr-2"></i>
                        Pencarian
                    </label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="search" 
                           placeholder="No. transaksi, keterangan, referensi..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="flex space-x-3 mt-6">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-undo mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transaksi Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list mr-2 text-blue-500"></i>
                Daftar Transaksi
            </h3>
            <div class="text-sm text-gray-500">
                Total: {{ $transaksis->total() }} transaksi
            </div>
        </div>
        <div class="p-6">
            @if($transaksis->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekening</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Sesudah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaksis as $transaksi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d/m/Y') : '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $transaksi->created_at ? $transaksi->created_at->format('H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $transaksi->nomor_transaksi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $transaksi->rekening->warna_card }};"></div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $transaksi->rekening->nama_rekening }}</div>
                                        <div class="text-sm text-gray-500">{{ $transaksi->rekening->project->nama ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaksi->jenis_transaksi === 'debit')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-up mr-1"></i>Debit
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-arrow-down mr-1"></i>Kredit
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $transaksi->kategori_transaksi_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $transaksi->keterangan }}">
                                    {{ $transaksi->keterangan }}
                                </div>
                                @if($transaksi->referensi)
                                <div class="text-xs text-gray-500">Ref: {{ $transaksi->referensi }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="font-medium {{ $transaksi->jenis_transaksi === 'debit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaksi->jenis_transaksi === 'debit' ? '+' : '-' }}{{ $transaksi->formatted_jumlah }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="font-medium {{ $transaksi->saldo_sesudah >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaksi->formatted_saldo_sesudah }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaksi->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Verified
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $transaksi->user->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('perusahaan.keuangan.transaksi-rekening.show', $transaksi->hash_id) }}" 
                                       class="inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs text-blue-700 hover:bg-blue-50" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$transaksi->is_verified)
                                    <a href="{{ route('perusahaan.keuangan.transaksi-rekening.edit', $transaksi->hash_id) }}" 
                                       class="inline-flex items-center px-2 py-1 border border-yellow-300 rounded text-xs text-yellow-700 hover:bg-yellow-50" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="inline-flex items-center px-2 py-1 border border-green-300 rounded text-xs text-green-700 hover:bg-green-50" 
                                            onclick="verifyTransaction('{{ $transaksi->hash_id }}')" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs text-red-700 hover:bg-red-50" 
                                            onclick="deleteTransaction('{{ $transaksi->hash_id }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    <button type="button" class="inline-flex items-center px-2 py-1 border border-yellow-300 rounded text-xs text-yellow-700 hover:bg-yellow-50" 
                                            onclick="unverifyTransaction('{{ $transaksi->hash_id }}')" title="Unverify">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $transaksis->firstItem() ?? 0 }} - {{ $transaksis->lastItem() ?? 0 }} 
                    dari {{ $transaksis->total() }} transaksi
                </div>
                <div>
                    {{ $transaksis->appends(request()->query())->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada transaksi</h3>
                <p class="text-gray-500 mb-6">Belum ada transaksi yang sesuai dengan filter yang dipilih</p>
                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Transaksi
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
function verifyTransaction(hashId) {
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
            fetch(`/perusahaan/keuangan/transaksi-rekening/${hashId}/verify`, {
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

function unverifyTransaction(hashId) {
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
            fetch(`/perusahaan/keuangan/transaksi-rekening/${hashId}/unverify`, {
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

function deleteTransaction(hashId) {
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
            form.action = `/perusahaan/keuangan/transaksi-rekening/${hashId}`;
            
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