@extends('perusahaan.layouts.app')

@section('page-title', 'Detail Kategori Perlengkapan')
@section('page-subtitle', 'Kelola kategori dan item perlengkapan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('perusahaan.perlengkapan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Kategori
    </a>
    <div class="flex items-center space-x-3">
        <button onclick="showAddItemModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Tambah Item
        </button>
        <a href="{{ route('perusahaan.perlengkapan.edit', $kategori->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
            <i class="fas fa-edit mr-2"></i>Edit Kategori
        </a>
        <button onclick="deleteKategori('{{ $kategori->hash_id }}', '{{ $kategori->nama_kategori }}', {{ $items->count() }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
            <i class="fas fa-trash mr-2"></i>Hapus
        </button>
    </div>
</div>

<!-- Category Info -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Informasi Kategori</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Kategori</label>
                <p class="text-lg font-semibold text-gray-900">{{ $kategori->nama_kategori }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Project</label>
                <p class="text-gray-900">{{ $kategori->project->nama }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                @if($kategori->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                    </span>
                @endif
            </div>
            @if($kategori->deskripsi)
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                <p class="text-gray-900">{{ $kategori->deskripsi }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Items Statistics -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-toolbox text-blue-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Item</p>
                <p class="text-lg font-semibold text-gray-900">{{ $items->count() }}</p>
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
                <p class="text-sm font-medium text-gray-500">Item Aktif</p>
                <p class="text-lg font-semibold text-gray-900">{{ $items->where('is_active', true)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-purple-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Stok</p>
                <p class="text-lg font-semibold text-gray-900">{{ $items->sum('stok_tersedia') }}</p>
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
                <p class="text-lg font-semibold text-gray-900">{{ $items->filter(function($item) { return $item->is_low_stock; })->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Item Perlengkapan</h3>
            <div class="flex items-center space-x-3">
                <!-- Search Input -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari item..." 
                           class="w-64 px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                <!-- Status Filter -->
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="low_stock">Stok Rendah</option>
                    <option value="out_of_stock">Stok Habis</option>
                </select>
                <!-- Clear Filter Button -->
                <button onclick="clearFilters()" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition" title="Reset Filter">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <!-- Filter Summary -->
        <div id="filterSummary" class="mt-2 text-sm text-gray-500 hidden">
            Menampilkan <span id="visibleCount">0</span> dari <span id="totalCount">{{ $items->count() }}</span> item
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                <tr class="item-row hover:bg-gray-50" 
                    data-item-name="{{ strtolower($item->nama_item) }}"
                    data-item-description="{{ strtolower($item->deskripsi ?? '') }}"
                    data-item-unit="{{ strtolower($item->satuan) }}"
                    data-status="{{ $item->is_active ? 'active' : 'inactive' }}"
                    data-stock-status="{{ $item->is_out_of_stock ? 'out_of_stock' : ($item->is_low_stock ? 'low_stock' : 'normal') }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($item->foto_item)
                                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $item->foto_url }}" alt="{{ $item->nama_item }}">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-toolbox text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->nama_item }}</div>
                                @if($item->deskripsi)
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->deskripsi, 50) }}</div>
                                @endif
                                <div class="text-xs text-gray-400">{{ $item->satuan }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">{{ $item->stok_tersedia }}</span>
                            @if($item->stok_minimum > 0)
                                <span class="text-xs text-gray-500">(min: {{ $item->stok_minimum }})</span>
                            @endif
                        </div>
                        @if($item->is_low_stock)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Stok Rendah
                            </span>
                        @elseif($item->is_out_of_stock)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                <i class="fas fa-times-circle mr-1"></i>Habis
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $item->formatted_harga_satuan }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <button onclick="showStockHistoryModal('{{ $item->hash_id }}', '{{ $item->nama_item }}')" class="text-purple-600 hover:text-purple-900 transition-colors" title="Riwayat Stok">
                                <i class="fas fa-history"></i>
                            </button>
                            <button onclick="showUpdateStokModal('{{ $item->hash_id }}', '{{ $item->nama_item }}', {{ $item->stok_tersedia }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Update Stok">
                                <i class="fas fa-boxes"></i>
                            </button>
                            <button onclick="showEditItemModal('{{ $item->hash_id }}')" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit Item">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteItem('{{ $item->hash_id }}', '{{ $item->nama_item }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus Item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyState">
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-toolbox text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Belum ada item dalam kategori ini</p>
                            <p class="text-gray-400 text-sm mb-6">Tambahkan item pertama untuk memulai</p>
                            <button onclick="showAddItemModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Tambah Item
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
                
                <!-- No Results State (Hidden by default) -->
                <tr id="noResultsState" class="hidden">
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Tidak ada item yang ditemukan</p>
                            <p class="text-gray-400 text-sm mb-6">Coba ubah kata kunci pencarian atau filter</p>
                            <button onclick="clearFilters()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                                <i class="fas fa-times mr-2"></i>Reset Filter
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Item Modal -->
<div id="addItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Item Baru</h3>
                <button onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addItemForm" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_item" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Seragam, Topi, dll">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="satuan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Pcs, Set, Pasang, dll">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi item..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                            <input type="number" name="stok_awal" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                            <input type="number" name="stok_minimum" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                            <input type="number" name="harga_satuan" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Item</label>
                        <input type="file" name="foto_item" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG hingga 5MB</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="add_is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                        <label for="add_is_active" class="ml-2 block text-sm text-gray-900">Item Aktif</label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAddItemModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Simpan Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div id="editItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Item</h3>
                <button onclick="closeEditItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editItemForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_item" id="edit_nama_item" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Seragam, Topi, dll">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="satuan" id="edit_satuan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Pcs, Set, Pasang, dll">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi item..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                            <input type="number" name="stok_minimum" id="edit_stok_minimum" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                            <input type="number" name="harga_satuan" id="edit_harga_satuan" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Item</label>
                        <div id="currentPhotoPreview" class="mb-2 hidden">
                            <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                            <img id="currentPhoto" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                        </div>
                        <input type="file" name="foto_item" id="edit_foto_item" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG hingga 5MB</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">Item Aktif</label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditItemModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="updateStokModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Stok</h3>
                <button onclick="closeUpdateStokModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="updateStokForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                        <p id="stokItemName" class="text-lg font-semibold text-gray-900"></p>
                        <p class="text-sm text-gray-500">Stok saat ini: <span id="currentStok" class="font-medium"></span></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Operasi <span class="text-red-500">*</span></label>
                            <select name="operasi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="tambah">Tambah Stok</option>
                                <option value="kurang">Kurangi Stok</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan perubahan stok..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeUpdateStokModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Update Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock History Modal -->
<div id="stockHistoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Riwayat Stok</h3>
                    <p id="stockHistoryItemName" class="text-sm text-gray-500"></p>
                </div>
                <button onclick="closeStockHistoryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Loading State -->
            <div id="stockHistoryLoading" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Memuat riwayat stok...</p>
            </div>
            
            <!-- History Content -->
            <div id="stockHistoryContent" class="hidden">
                <!-- Current Stock Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-blue-900">Stok Saat Ini</h4>
                            <p class="text-sm text-blue-700">Saldo akhir berdasarkan riwayat transaksi</p>
                        </div>
                        <div class="text-right">
                            <div id="currentStockDisplay" class="text-2xl font-bold text-blue-900"></div>
                            <div class="text-sm text-blue-700">unit</div>
                        </div>
                    </div>
                </div>
                
                <!-- History Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sebelum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Sesudah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                            </tr>
                        </thead>
                        <tbody id="stockHistoryTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- History rows will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Empty State -->
                <div id="stockHistoryEmpty" class="hidden text-center py-8">
                    <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Belum ada riwayat stok</p>
                    <p class="text-gray-400 text-sm">Riwayat transaksi akan muncul setelah ada perubahan stok</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Forms -->
<form id="deleteKategoriForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="deleteItemForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
let currentItemId = null;
let currentEditItemId = null;

// Search and Filter Functions
function filterItems() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('.item-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const itemName = row.dataset.itemName;
        const itemDescription = row.dataset.itemDescription;
        const itemUnit = row.dataset.itemUnit;
        const status = row.dataset.status;
        const stockStatus = row.dataset.stockStatus;
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && 
            !itemName.includes(searchTerm) && 
            !itemDescription.includes(searchTerm) && 
            !itemUnit.includes(searchTerm)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter !== 'all') {
            if (statusFilter === 'active' && status !== 'active') {
                showRow = false;
            } else if (statusFilter === 'inactive' && status !== 'inactive') {
                showRow = false;
            } else if (statusFilter === 'low_stock' && stockStatus !== 'low_stock') {
                showRow = false;
            } else if (statusFilter === 'out_of_stock' && stockStatus !== 'out_of_stock') {
                showRow = false;
            }
        }
        
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update filter summary
    const totalCount = rows.length;
    const filterSummary = document.getElementById('filterSummary');
    const visibleCountSpan = document.getElementById('visibleCount');
    const totalCountSpan = document.getElementById('totalCount');
    
    if (searchTerm || statusFilter !== 'all') {
        filterSummary.classList.remove('hidden');
        visibleCountSpan.textContent = visibleCount;
        totalCountSpan.textContent = totalCount;
    } else {
        filterSummary.classList.add('hidden');
    }
    
    // Show/hide empty states
    const emptyState = document.getElementById('emptyState');
    const noResultsState = document.getElementById('noResultsState');
    
    if (totalCount === 0) {
        // No items at all - show original empty state
        if (emptyState) emptyState.style.display = '';
        noResultsState.classList.add('hidden');
    } else if (visibleCount === 0) {
        // Items exist but none match filter - show no results state
        if (emptyState) emptyState.style.display = 'none';
        noResultsState.classList.remove('hidden');
    } else {
        // Items are visible - hide both empty states
        if (emptyState) emptyState.style.display = 'none';
        noResultsState.classList.add('hidden');
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = 'all';
    filterItems();
}

// Event listeners for search and filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterItems);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterItems);
    }
    
    // Initialize filter summary count
    const totalCount = document.querySelectorAll('.item-row').length;
    const totalCountSpan = document.getElementById('totalCount');
    if (totalCountSpan) {
        totalCountSpan.textContent = totalCount;
    }
});

// Add Item Modal Functions
function showAddItemModal() {
    document.getElementById('addItemModal').classList.remove('hidden');
}

function closeAddItemModal() {
    document.getElementById('addItemModal').classList.add('hidden');
    document.getElementById('addItemForm').reset();
}

// Edit Item Modal Functions
function showEditItemModal(itemId) {
    currentEditItemId = itemId;
    
    // Fetch item data
    fetch(`/perusahaan/perlengkapan/{{ $kategori->hash_id }}/items/${itemId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = data.data;
            
            // Fill form with item data
            document.getElementById('edit_nama_item').value = item.nama_item || '';
            document.getElementById('edit_satuan').value = item.satuan || '';
            document.getElementById('edit_deskripsi').value = item.deskripsi || '';
            document.getElementById('edit_stok_minimum').value = item.stok_minimum || '';
            document.getElementById('edit_harga_satuan').value = item.harga_satuan || '';
            document.getElementById('edit_is_active').checked = item.is_active;
            
            // Show current photo if exists
            if (item.foto_url) {
                document.getElementById('currentPhoto').src = item.foto_url;
                document.getElementById('currentPhotoPreview').classList.remove('hidden');
            } else {
                document.getElementById('currentPhotoPreview').classList.add('hidden');
            }
            
            document.getElementById('editItemModal').classList.remove('hidden');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat data item'
            });
        }
    })
    .catch(error => {
        console.error('Error fetching item data:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memuat data item'
        });
    });
}

function closeEditItemModal() {
    document.getElementById('editItemModal').classList.add('hidden');
    document.getElementById('editItemForm').reset();
    document.getElementById('currentPhotoPreview').classList.add('hidden');
    currentEditItemId = null;
}

// Update Stok Modal Functions
function showUpdateStokModal(itemId, itemName, currentStok) {
    currentItemId = itemId;
    document.getElementById('stokItemName').textContent = itemName;
    document.getElementById('currentStok').textContent = currentStok;
    document.getElementById('updateStokModal').classList.remove('hidden');
}

function closeUpdateStokModal() {
    document.getElementById('updateStokModal').classList.add('hidden');
    document.getElementById('updateStokForm').reset();
    currentItemId = null;
}

// Stock History Modal Functions
function showStockHistoryModal(itemId, itemName) {
    document.getElementById('stockHistoryItemName').textContent = itemName;
    document.getElementById('stockHistoryModal').classList.remove('hidden');
    document.getElementById('stockHistoryLoading').classList.remove('hidden');
    document.getElementById('stockHistoryContent').classList.add('hidden');
    
    // Load stock history
    loadStockHistory(itemId);
}

function closeStockHistoryModal() {
    document.getElementById('stockHistoryModal').classList.add('hidden');
}

async function loadStockHistory(itemId) {
    try {
        const response = await fetch(`/perusahaan/perlengkapan/{{ $kategori->hash_id }}/items/${itemId}/stock-history`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayStockHistory(result.data);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: result.message || 'Gagal memuat riwayat stok'
            });
        }
    } catch (error) {
        console.error('Error loading stock history:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memuat riwayat stok'
        });
    } finally {
        document.getElementById('stockHistoryLoading').classList.add('hidden');
        document.getElementById('stockHistoryContent').classList.remove('hidden');
    }
}

function displayStockHistory(data) {
    const { item, histories } = data;
    
    // Update current stock display
    document.getElementById('currentStockDisplay').textContent = item.stok_tersedia;
    
    const tbody = document.getElementById('stockHistoryTableBody');
    const emptyState = document.getElementById('stockHistoryEmpty');
    
    if (histories.length === 0) {
        tbody.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    
    // Build table rows
    tbody.innerHTML = histories.map(history => `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm text-gray-900">${formatDate(history.created_at)}</div>
                <div class="text-xs text-gray-500">${formatTime(history.created_at)}</div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${history.tipe_transaksi_color}">
                    <i class="${history.tipe_transaksi_icon} mr-1"></i>
                    ${history.tipe_transaksi_text}
                </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-sm font-medium ${history.tipe_transaksi === 'masuk' || history.tipe_transaksi === 'return' ? 'text-green-600' : 'text-red-600'}">
                    ${history.formatted_jumlah}
                </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                ${history.stok_sebelum}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                ${history.stok_sesudah}
            </td>
            <td class="px-4 py-3">
                <div class="text-sm text-gray-900">${history.keterangan}</div>
                ${history.referensi_tipe ? `<div class="text-xs text-gray-500">Ref: ${history.referensi_tipe}</div>` : ''}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                ${history.created_by ? history.created_by.name : 'System'}
            </td>
        </tr>
    `).join('');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Form Submissions
document.getElementById('addItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Debug: log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    try {
        const response = await fetch(`/perusahaan/perlengkapan/{{ $kategori->hash_id }}/items`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const result = await response.json();
        console.log('Response data:', result);
        
        if (result.success) {
            closeAddItemModal();
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
                text: result.message || 'Terjadi kesalahan saat menyimpan data',
                html: result.errors ? '<pre>' + JSON.stringify(result.errors, null, 2) + '</pre>' : undefined
            });
        }
    } catch (error) {
        console.error('Fetch error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengirim data: ' + error.message
        });
    }
});

// Edit Item Form Submission
document.getElementById('editItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentEditItemId) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'ID item tidak ditemukan'
        });
        return;
    }
    
    const formData = new FormData(this);
    // Add method spoofing for PUT request
    formData.append('_method', 'PUT');
    
    try {
        const response = await fetch(`/perusahaan/perlengkapan/{{ $kategori->hash_id }}/items/${currentEditItemId}`, {
            method: 'POST', // Laravel method spoofing
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeEditItemModal();
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
                text: result.message || 'Terjadi kesalahan saat mengupdate item',
                html: result.errors ? '<pre>' + JSON.stringify(result.errors, null, 2) + '</pre>' : undefined
            });
        }
    } catch (error) {
        console.error('Edit item error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengupdate item: ' + error.message
        });
    }
});

document.getElementById('updateStokForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentItemId) return;
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/perusahaan/perlengkapan/{{ $kategori->hash_id }}/items/${currentItemId}/stok`, {
            method: 'PATCH',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            text: 'Terjadi kesalahan saat mengupdate stok'
        });
    }
});

// Delete Functions
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
            const form = document.getElementById('deleteKategoriForm');
            form.action = `/perusahaan/perlengkapan/${hashId}`;
            form.submit();
        }
    });
}

async function deleteItem(itemId, itemName) {
    const result = await Swal.fire({
        title: 'Hapus Item?',
        text: `Apakah Anda yakin ingin menghapus item "${itemName}"? Data yang sudah dihapus tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/perlengkapan/{{ $kategori->hash_id }}/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                text: 'Terjadi kesalahan saat menghapus item'
            });
        }
    }
}
</script>
@endpush
@endsection