@extends('perusahaan.layouts.app')

@section('page-title', 'Penyerahan Perlengkapan')
@section('page-subtitle', 'Kelola penyerahan perlengkapan kepada karyawan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <!-- Search -->
        <form method="GET" class="flex items-center space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan..." class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-search"></i>
            </button>
            @if(request()->hasAny(['search', 'project_id', 'status', 'tanggal_dari', 'tanggal_sampai']))
                <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="flex items-center space-x-3">
        <button onclick="showFilterModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <a href="{{ route('perusahaan.penyerahan-perlengkapan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Buat Jadwal
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-blue-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Penyerahan</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['total_penyerahan'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-gray-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Draft</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['draft'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding text-yellow-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Sedang Diserahkan</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['diserahkan'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Selesai Diserahkan</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['dikembalikan'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Penyerahan Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Daftar Penyerahan Perlengkapan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($penyerahans as $penyerahan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $penyerahan->project->nama }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $penyerahan->tanggal_mulai->format('d/m/Y') }}</div>
                        <div class="text-sm text-gray-500">s/d {{ $penyerahan->tanggal_selesai->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($penyerahan->items_count > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $penyerahan->items_count }} item{{ $penyerahan->items_count > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Belum ada item
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($penyerahan->status === 'draft')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-file-alt mr-1"></i>Draft
                            </span>
                        @elseif($penyerahan->status === 'diserahkan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-hand-holding mr-1"></i>Sedang Diserahkan
                            </span>
                        @elseif($penyerahan->status === 'dikembalikan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Selesai Diserahkan
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-3">
                            <!-- 1. Pilih Karyawan - Selalu bisa diklik untuk draft -->
                            @if($penyerahan->status === 'draft')
                                <button onclick="pilihKaryawanFromIndex('{{ $penyerahan->hash_id }}')" class="text-blue-600 hover:text-blue-900 transition-colors" title="Kelola Karyawan ({{ $penyerahan->karyawans()->count() }} dipilih)">
                                    <i class="fas fa-users text-lg"></i>
                                </button>
                            @else
                                <span class="text-green-600" title="Karyawan terpilih ({{ $penyerahan->karyawans()->count() }} orang)">
                                    <i class="fas fa-users text-lg"></i>
                                </span>
                            @endif
                            
                            <!-- 2. Pilih Item - Selalu bisa diklik untuk draft -->
                            @if($penyerahan->status === 'draft')
                                <button onclick="pilihItemFromIndex('{{ $penyerahan->hash_id }}')" class="text-orange-600 hover:text-orange-900 transition-colors" title="Kelola Item ({{ $penyerahan->items()->count() }} dipilih)">
                                    <i class="fas fa-box text-lg"></i>
                                </button>
                            @else
                                <span class="text-green-600" title="Item terpilih ({{ $penyerahan->items()->count() }} item)">
                                    <i class="fas fa-box text-lg"></i>
                                </span>
                            @endif
                            
                            <!-- 3. Serahkan - Selalu bisa diklik untuk draft dan diserahkan -->
                            @if(in_array($penyerahan->status, ['draft', 'diserahkan']))
                                <button onclick="serahkanFromIndex('{{ $penyerahan->hash_id }}')" class="text-purple-600 hover:text-purple-900 transition-colors" title="Kelola Penyerahan">
                                    <i class="fas fa-handshake text-lg"></i>
                                </button>
                            @else
                                <span class="text-green-600" title="Penyerahan selesai">
                                    <i class="fas fa-handshake text-lg"></i>
                                </span>
                            @endif
                            
                            <!-- Action buttons -->
                            <div class="border-l border-gray-300 pl-3 ml-3 flex items-center space-x-2">
                                <a href="{{ route('perusahaan.penyerahan-perlengkapan.laporan', $penyerahan->hash_id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Lihat Laporan Progress">
                                    <i class="fas fa-chart-pie"></i>
                                </a>
                                @if(in_array($penyerahan->status, ['diserahkan', 'dikembalikan']))
                                    <a href="{{ route('perusahaan.penyerahan-perlengkapan.edit', $penyerahan->hash_id) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(in_array($penyerahan->status, ['draft', 'diserahkan', 'dikembalikan']))
                                    <button onclick="deletePenyerahan('{{ $penyerahan->hash_id }}', 'jadwal penyerahan')" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-handshake text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Belum ada jadwal penyerahan</p>
                            <p class="text-gray-400 text-sm mb-6">Buat jadwal penyerahan pertama untuk memulai</p>
                            <a href="{{ route('perusahaan.penyerahan-perlengkapan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Buat Jadwal
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($penyerahans->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $penyerahans->links() }}
    </div>
    @endif
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Filter Penyerahan</h3>
                <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="GET" action="{{ route('perusahaan.penyerahan-perlengkapan.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="diserahkan" {{ request('status') === 'diserahkan' ? 'selected' : '' }}>Sedang Diserahkan</option>
                                <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Selesai Diserahkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeFilterModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deletePenyerahanForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
// Filter Modal Functions
function showFilterModal() {
    document.getElementById('filterModal').classList.remove('hidden');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}

// Action Functions from Index
function pilihKaryawanFromIndex(hashId) {
    window.location.href = `/perusahaan/penyerahan-perlengkapan/${hashId}/pilih-karyawan`;
}

function pilihItemFromIndex(hashId) {
    window.location.href = `/perusahaan/penyerahan-perlengkapan/${hashId}/pilih-item`;
}

function serahkanFromIndex(hashId) {
    window.location.href = `/perusahaan/penyerahan-perlengkapan/${hashId}/serahkan-item`;
}

// Action Functions
async function serahkanPenyerahan(hashId, karyawanName) {
    const result = await Swal.fire({
        title: 'Konfirmasi Penyerahan',
        text: `Apakah Anda yakin ingin mengkonfirmasi penyerahan kepada ${karyawanName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Serahkan!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/penyerahan-perlengkapan/${hashId}/serahkan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengkonfirmasi penyerahan'
            });
        }
    }
}

async function deletePenyerahan(hashId, itemName) {
    const result = await Swal.fire({
        title: 'Hapus Penyerahan?',
        text: `Apakah Anda yakin ingin menghapus ${itemName}? Data yang sudah dihapus tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        const form = document.getElementById('deletePenyerahanForm');
        form.action = `/perusahaan/penyerahan-perlengkapan/${hashId}`;
        form.submit();
    }
}

function showKembalikanModal(hashId) {
    // TODO: Implement return modal
    Swal.fire({
        icon: 'info',
        title: 'Fitur Pengembalian',
        text: 'Fitur pengembalian akan tersedia di halaman detail penyerahan'
    });
}
</script>
@endpush
@endsection