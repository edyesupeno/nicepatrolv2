@extends('perusahaan.layouts.app')

@section('page-title', 'Master Perlengkapan')
@section('page-subtitle', 'Kelola kategori dan item perlengkapan perusahaan')

@section('content')
<!-- Statistics Cards -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Kategori</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['total_kategori'] ?? 0 }}</p>
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
                <p class="text-sm font-medium text-gray-500">Kategori Aktif</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['active_kategori'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-toolbox text-purple-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Item</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['total_items'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Stok Rendah</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['low_stock_items'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="mb-6 flex justify-end">
    <a href="{{ route('perusahaan.perlengkapan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
        <i class="fas fa-plus mr-2"></i>Tambah Kategori
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
            <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama kategori..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kategoris as $kategori)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-layer-group text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $kategori->nama_kategori }}</div>
                                @if($kategori->deskripsi)
                                    <div class="text-sm text-gray-500">{{ Str::limit($kategori->deskripsi, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $kategori->project->nama ?? 'Project tidak ditemukan' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">{{ $kategori->items_count }}</span>
                            @if($kategori->active_items_count < $kategori->items_count)
                                <span class="text-xs text-gray-500">({{ $kategori->active_items_count }} aktif)</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($kategori->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $kategori->created_at->format('d/m/Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $kategori->createdBy->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('perusahaan.perlengkapan.show', $kategori->hash_id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Lihat Detail & Kelola Item">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('perusahaan.perlengkapan.edit', $kategori->hash_id) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit Kategori">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteKategori('{{ $kategori->hash_id }}', '{{ $kategori->nama_kategori }}', {{ $kategori->items_count }})" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus Kategori">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-layer-group text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Belum ada kategori perlengkapan</p>
                            <p class="text-gray-400 text-sm mb-6">Tambahkan kategori pertama untuk memulai</p>
                            <a href="{{ route('perusahaan.perlengkapan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Tambah Kategori
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($kategoris->hasPages())
<div class="mt-6">
    {{ $kategoris->links() }}
</div>
@endif

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteKategori(hashId, nama, itemCount) {
    if (itemCount > 0) {
        Swal.fire({
            title: 'Tidak Dapat Menghapus!',
            text: `Kategori "${nama}" masih memiliki ${itemCount} item. Hapus semua item terlebih dahulu.`,
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Hapus Kategori?',
        text: `Apakah Anda yakin ingin menghapus kategori "${nama}"? Data yang sudah dihapus tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/perlengkapan/${hashId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection