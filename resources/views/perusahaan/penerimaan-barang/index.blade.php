@extends('perusahaan.layouts.app')

@section('title', 'Penerimaan Barang')
@section('page-title', 'Penerimaan Barang')
@section('page-subtitle', 'Kelola data penerimaan barang masuk')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-box text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Penerimaan Barang</h1>
                <p class="text-sm text-gray-600">Kelola data barang yang diterima</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Cari barang..." 
                    class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    id="searchInput"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            
            <!-- Filter -->
            <div class="relative">
                <select class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none" id="filterKategori">
                    <option value="">Semua Kategori</option>
                    <option value="Dokumen">Dokumen</option>
                    <option value="Material">Material</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Logistik">Logistik</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
            
            <!-- Add Button -->
            <a href="{{ route('perusahaan.penerimaan-barang.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-plus mr-2"></i>
                Input Barang
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Barang</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_barang'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['hari_ini'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Kondisi Baik</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['kondisi_baik'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Perlu Perhatian</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['perlu_perhatian'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Penerimaan Barang</h2>
                <div class="flex items-center space-x-2">
                    <button onclick="printAllReports()" class="p-2 text-green-600 hover:text-green-800 transition" title="Cetak Semua Laporan">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 transition" title="Download Excel">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No. Penerimaan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Barang
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kondisi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengirim
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Area
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            POS
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                    @forelse($penerimaanBarangs as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->nomor_penerimaan ?? 'PB20240117001' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($item->foto_barang ?? false)
                                <div class="w-10 h-10 bg-gray-200 rounded-lg mr-3 overflow-hidden">
                                    <img src="{{ Storage::url($item->foto_barang) }}" alt="Foto Barang" class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="w-10 h-10 bg-gray-200 rounded-lg mr-3 flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->nama_barang ?? 'Paket Dokumen A1' }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->tujuan_departemen ?? 'HRD Department' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $kategori = $item->kategori_barang ?? 'Dokumen';
                                $badgeClass = match($kategori) {
                                    'Dokumen' => 'bg-blue-100 text-blue-800',
                                    'Material' => 'bg-green-100 text-green-800',
                                    'Elektronik' => 'bg-purple-100 text-purple-800',
                                    'Logistik' => 'bg-orange-100 text-orange-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $item->jumlah_barang ?? '1' }} {{ $item->satuan ?? 'Pcs' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $kondisi = $item->kondisi_barang ?? 'Baik';
                                $kondisiClass = match($kondisi) {
                                    'Baik' => 'bg-green-100 text-green-800',
                                    'Rusak' => 'bg-red-100 text-red-800',
                                    'Segel Terbuka' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $kondisiIcon = match($kondisi) {
                                    'Baik' => 'fas fa-check-circle',
                                    'Rusak' => 'fas fa-times-circle',
                                    'Segel Terbuka' => 'fas fa-exclamation-circle',
                                    default => 'fas fa-question-circle'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kondisiClass }}">
                                <i class="{{ $kondisiIcon }} mr-1"></i>
                                {{ $kondisi }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $item->pengirim ?? 'Client' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->project)
                                <div class="text-sm text-gray-900">{{ $item->project->nama }}</div>
                            @else
                                <div class="text-sm text-gray-400">-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->area)
                                <div class="text-sm text-gray-900">{{ $item->area->nama }}</div>
                                @if($item->area->alamat)
                                    <div class="text-xs text-gray-500">{{ $item->area->alamat }}</div>
                                @endif
                            @else
                                <div class="text-sm text-gray-400">-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->pos)
                                <div class="text-sm text-gray-900">{{ $item->pos }}</div>
                            @else
                                <div class="text-sm text-gray-400">-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $item->tanggal_terima ? \Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y H:i') : '17/01/2024 14:30' }}</div>
                            <div class="text-sm text-gray-500">{{ $item->petugas_penerima ?? auth()->user()->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button onclick="viewDetail('{{ $item->hash_id ?? 'sample' }}')" class="text-blue-600 hover:text-blue-900 transition" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="printReport('{{ $item->hash_id ?? 'sample' }}')" class="text-green-600 hover:text-green-900 transition" title="Cetak Laporan">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button onclick="editItem('{{ $item->hash_id ?? 'sample' }}')" class="text-indigo-600 hover:text-indigo-900 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteItem('{{ $item->hash_id ?? 'sample' }}')" class="text-red-600 hover:text-red-900 transition" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-box text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data</h3>
                                <p class="text-gray-500 mb-4">Belum ada data penerimaan barang yang tercatat</p>
                                <a href="{{ route('perusahaan.penerimaan-barang.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                    <i class="fas fa-plus mr-2"></i>
                                    Input Barang Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($penerimaanBarangs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $penerimaanBarangs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Filter functionality
document.getElementById('filterKategori').addEventListener('change', function() {
    const filterValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');
    
    rows.forEach(row => {
        if (!filterValue) {
            row.style.display = '';
        } else {
            const kategoriCell = row.querySelector('td:nth-child(3)');
            if (kategoriCell && kategoriCell.textContent.toLowerCase().includes(filterValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});

// Action functions
function viewDetail(hashId) {
    window.location.href = `{{ route('perusahaan.penerimaan-barang.index') }}/${hashId}`;
}

function editItem(hashId) {
    window.location.href = `{{ route('perusahaan.penerimaan-barang.index') }}/${hashId}/edit`;
}

function printReport(hashId) {
    window.open(`{{ route('perusahaan.penerimaan-barang.index') }}/${hashId}/print`, '_blank');
}

function printAllReports() {
    Swal.fire({
        title: 'Cetak Semua Laporan?',
        text: "Akan membuka tab baru untuk setiap laporan penyaluran",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Cetak Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get all visible rows (not filtered out)
            const visibleRows = document.querySelectorAll('#tableBody tr:not([style*="display: none"])');
            let printCount = 0;
            
            visibleRows.forEach((row, index) => {
                const printButton = row.querySelector('button[onclick*="printReport"]');
                if (printButton) {
                    const hashId = printButton.getAttribute('onclick').match(/'([^']+)'/)[1];
                    if (hashId && hashId !== 'sample') {
                        setTimeout(() => {
                            window.open(`{{ route('perusahaan.penerimaan-barang.index') }}/${hashId}/print`, '_blank');
                        }, index * 500); // Delay each print by 500ms
                        printCount++;
                    }
                }
            });
            
            if (printCount > 0) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `Membuka ${printCount} laporan untuk dicetak`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Data',
                    text: 'Tidak ada laporan yang dapat dicetak',
                });
            }
        }
    });
}

function deleteItem(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data penerimaan barang akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // TODO: Implement delete API call
            fetch(`{{ route('perusahaan.penerimaan-barang.index') }}/${hashId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
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
                    text: 'Terjadi kesalahan saat menghapus data'
                });
            });
        }
    });
}
</script>
@endpush