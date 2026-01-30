@extends('perusahaan.layouts.app')

@section('page-title', 'Pilih Item Perlengkapan')
@section('page-subtitle', 'Pilih item perlengkapan untuk penyerahan')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Penyerahan
    </a>
</div>

<!-- Informasi Penyerahan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Informasi Penyerahan</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Project</label>
                <p class="text-gray-900">{{ $penyerahan->project->nama }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                <p class="text-gray-900">{{ $penyerahan->tanggal_mulai->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                <p class="text-gray-900">{{ $penyerahan->tanggal_selesai->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Karyawan Terpilih</label>
                <p class="text-gray-900">{{ $selectedKaryawanCount }} orang</p>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Item Baru - HIDDEN -->
<div id="add_item_section" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6" style="display: none;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Tambah Item Perlengkapan</h3>
        <p class="text-sm text-gray-500 mt-1">Pilih kategori untuk melihat item yang tersedia</p>
    </div>
    <div class="p-6">
        <form id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kategori <span class="text-red-500">*</span></label>
                    <select id="kategori_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">- Pilih Kategori -</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <div class="mt-1 text-xs text-red-600">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Wajib pilih kategori untuk memuat data item
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Item (opsional)</label>
                    <div class="relative">
                        <input type="text" id="search_input" placeholder="Cari nama item..." class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        <span id="item_count">Pilih kategori untuk melihat item</span>
                        <span class="text-gray-400 ml-2">({{ $kategoris->count() }} kategori tersedia)</span>
                        <button type="button" id="reset_search" class="text-blue-600 hover:text-blue-800 ml-2">
                            <i class="fas fa-times-circle mr-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabel Item Tersedia -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select_all_checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="item_table_body" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <!-- Loading State -->
        <div id="loading_state" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-500">Memuat data item...</p>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="empty_state" class="px-6 py-12 text-center hidden">
            <div class="flex flex-col items-center">
                <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-2">Tidak ada item tersedia</p>
                <p class="text-gray-400 text-sm">Pilih kategori yang berbeda atau ubah filter pencarian</p>
            </div>
        </div>

        <!-- Tombol Tambah -->
        <div class="mt-4 flex justify-end">
            <button id="add_selected" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition" disabled>
                <i class="fas fa-plus mr-2"></i>Tambah Item (<span id="selected_count">0</span>)
            </button>
        </div>
    </div>
</div>

<!-- Daftar Item Terpilih -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Item Terpilih</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar item yang akan diberikan kepada semua karyawan terpilih</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Total: <span id="total_selected" class="font-semibold text-gray-900">0</span> item
                </div>
                <button id="toggle_add_section" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Item
                </button>
            </div>
        </div>
        
        <!-- Search and Filter for Selected Items -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Item Terpilih</label>
                <div class="relative">
                    <input type="text" id="selected_search_input" placeholder="Cari nama item..." class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kategori</label>
                <select id="selected_kategori_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tampilkan per halaman</label>
                <select id="selected_per_page" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                    <option value="100">100 per halaman</option>
                </select>
            </div>
        </div>
    </div>
    
    <div id="selected_item_container">
        <!-- Content will be loaded via JavaScript -->
    </div>
    
    <!-- Pagination for Selected Items -->
    <div id="selected_pagination_container" class="px-6 py-4 border-t border-gray-200 hidden">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan <span id="selected_showing_from">1</span> sampai <span id="selected_showing_to">10</span> dari <span id="selected_showing_total">0</span> item
            </div>
            <div class="flex items-center space-x-2">
                <button id="selected_prev_btn" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                </button>
                <div id="selected_page_numbers" class="flex items-center space-x-1">
                    <!-- Page numbers will be inserted here -->
                </div>
                <button id="selected_next_btn" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Empty state (hidden by default, shown by JavaScript if needed) -->
    <div id="empty_selected_state" class="px-6 py-12 text-center hidden">
        <div class="flex flex-col items-center">
            <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Belum ada item terpilih</p>
            <p class="text-gray-400 text-sm">Pilih item dari kategori di atas untuk menambahkan ke daftar penyerahan</p>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="mt-6 flex justify-end space-x-3">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
        Selesai
    </a>
</div>

@push('scripts')
<script>
let selectedItems = @json($selectedItems);
let allItems = [];
let tempSelectedItems = []; // For new selections

console.log('Initial selectedItems from server:', selectedItems);

// Pagination state for selected items
let selectedPagination = {
    currentPage: 1,
    perPage: 10,
    total: 0,
    lastPage: 1
};

// Load items by kategori (for add section)
async function loadItems() {
    const kategoriId = document.getElementById('kategori_select').value;
    const search = document.getElementById('search_input').value;
    
    console.log('loadItems called with:', { kategoriId, search });
    
    // Clear table first
    document.getElementById('item_table_body').innerHTML = '';
    
    // Check if kategori is selected
    if (!kategoriId || kategoriId === '') {
        document.getElementById('loading_state').classList.add('hidden');
        showEmptyStateWithMessage('Pilih kategori terlebih dahulu untuk memuat data item');
        updateItemCount(0);
        return;
    }
    
    document.getElementById('loading_state').classList.remove('hidden');
    document.getElementById('empty_state').classList.add('hidden');
    
    try {
        const url = `/perusahaan/penyerahan-perlengkapan/items-by-kategori?kategori_id=${kategoriId}&search=${encodeURIComponent(search)}`;
        console.log('Loading items from URL:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Sesi telah berakhir. Silakan login kembali.');
            } else if (response.status === 404) {
                throw new Error('Endpoint tidak ditemukan. Hubungi administrator.');
            } else {
                const errorText = await response.text();
                console.error('Response error text:', errorText);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        const result = await response.json();
        console.log('Items API result:', result);
        
        if (result.success) {
            // Filter out already selected items
            const availableItems = result.data.filter(item => !isItemAlreadySelected(item.id));
            allItems = availableItems;
            
            console.log('Available items after filtering:', availableItems.length);
            
            if (availableItems.length === 0) {
                if (result.data.length > 0) {
                    showEmptyStateWithMessage('Semua item pada kategori ini sudah terpilih');
                } else {
                    showEmptyStateWithMessage('Tidak ada item tersedia pada kategori ini');
                }
            } else {
                renderItemTable(availableItems);
            }
            updateItemCount(availableItems.length);
        } else {
            console.error('API returned error:', result.message);
            showEmptyStateWithMessage(result.message || 'Gagal memuat data item');
        }
    } catch (error) {
        console.error('Error loading items:', error);
        showEmptyStateWithMessage('Terjadi kesalahan saat memuat data: ' + error.message);
    } finally {
        document.getElementById('loading_state').classList.add('hidden');
    }
}

function isItemAlreadySelected(itemId) {
    const isSelected = selectedItems.some(item => item.item_id === itemId);
    console.log(`Checking if item ${itemId} is already selected:`, isSelected);
    return isSelected;
}

function renderItemTable(items) {
    console.log('renderItemTable called with items:', items);
    
    const tbody = document.getElementById('item_table_body');
    
    if (items.length === 0) {
        console.log('No items to render, showing empty state');
        showEmptyStateWithMessage('Tidak ada item tersedia');
        return;
    }
    
    console.log('Rendering', items.length, 'items');
    
    tbody.innerHTML = items.map(item => {
        const isSelected = tempSelectedItems.some(si => si.item_id === item.id);
        const selectedItem = tempSelectedItems.find(si => si.item_id === item.id);
        const jumlah = selectedItem ? selectedItem.jumlah : 1;
        
        return `
            <tr class="hover:bg-gray-50 ${isSelected ? 'bg-green-50' : ''}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" 
                           class="item-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500" 
                           value="${item.id}" 
                           ${isSelected ? 'checked' : ''}>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        ${item.foto_item ? `<img src="/storage/${item.foto_item}" alt="${item.nama_item}" class="w-10 h-10 rounded-lg object-cover mr-3">` : '<div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-box text-gray-400"></i></div>'}
                        <div>
                            <div class="text-sm font-medium text-gray-900">${item.nama_item}</div>
                            <div class="text-sm text-gray-500">${item.satuan}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${item.stok_tersedia}</div>
                    <div class="text-sm text-gray-500">Min: ${item.stok_minimum}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" 
                           class="jumlah-input w-20 px-2 py-1 border border-gray-300 rounded text-sm" 
                           value="${jumlah}" 
                           min="1" 
                           max="${item.stok_tersedia}"
                           data-item-id="${item.id}"
                           ${!isSelected ? 'disabled' : ''}>
                </td>
            </tr>
        `;
    }).join('');
    
    console.log('Table HTML generated, adding event listeners');
    
    // Add event listeners
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', handleItemSelection);
    });
    
    document.querySelectorAll('.jumlah-input').forEach(input => {
        input.addEventListener('change', handleJumlahChange);
    });
    
    updateSelectAllCheckbox();
    updateSelectedCount();
    
    console.log('renderItemTable completed');
}

function handleItemSelection(event) {
    const itemId = parseInt(event.target.value);
    const isChecked = event.target.checked;
    const jumlahInput = document.querySelector(`input[data-item-id="${itemId}"]`);
    
    if (isChecked) {
        const jumlah = parseInt(jumlahInput.value) || 1;
        if (!tempSelectedItems.some(item => item.item_id === itemId)) {
            tempSelectedItems.push({ item_id: itemId, jumlah: jumlah });
        }
        jumlahInput.disabled = false;
        event.target.closest('tr').classList.add('bg-green-50');
    } else {
        tempSelectedItems = tempSelectedItems.filter(item => item.item_id !== itemId);
        jumlahInput.disabled = true;
        event.target.closest('tr').classList.remove('bg-green-50');
    }
    
    updateSelectAllCheckbox();
    updateSelectedCount();
}

function handleJumlahChange(event) {
    const itemId = parseInt(event.target.dataset.itemId);
    const jumlah = parseInt(event.target.value) || 1;
    
    const selectedItem = tempSelectedItems.find(item => item.item_id === itemId);
    if (selectedItem) {
        selectedItem.jumlah = jumlah;
    }
}

function showEmptyStateWithMessage(message) {
    console.log('showEmptyStateWithMessage called with:', message);
    
    document.getElementById('item_table_body').innerHTML = '';
    const emptyState = document.getElementById('empty_state');
    
    if (!emptyState) {
        console.error('Empty state element not found');
        return;
    }
    
    // Update the empty state message
    const emptyStateContent = emptyState.querySelector('.flex.flex-col.items-center');
    if (emptyStateContent) {
        emptyStateContent.innerHTML = `
            <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">${message}</p>
            <p class="text-gray-400 text-sm">Pilih kategori yang berbeda atau ubah filter pencarian</p>
        `;
    } else {
        console.error('Empty state content element not found');
    }
    
    emptyState.classList.remove('hidden');
    console.log('Empty state shown with message:', message);
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('select_all_checkbox');
    const visibleCheckboxes = document.querySelectorAll('.item-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    
    if (visibleCheckboxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedCheckboxes.length === visibleCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else if (checkedCheckboxes.length > 0) {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
    } else {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    }
}

function updateSelectedCount() {
    const count = tempSelectedItems.length;
    document.getElementById('selected_count').textContent = count;
    document.getElementById('add_selected').disabled = count === 0;
}

function updateItemCount(count) {
    document.getElementById('item_count').textContent = `${count} item tersedia`;
}

// Load selected items list with pagination and search
async function loadSelectedItems(page = 1) {
    console.log('Loading selected items for page:', page);
    
    // Check if required elements exist
    if (!document.getElementById('selected_item_container')) {
        console.error('selected_item_container not found');
        return;
    }
    
    const search = document.getElementById('selected_search_input')?.value || '';
    const kategoriId = document.getElementById('selected_kategori_select')?.value || 'all';
    const perPage = document.getElementById('selected_per_page')?.value || 10;
    
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: perPage,
            search: search,
            kategori_id: kategoriId
        });
        
        const url = `/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/selected-items?${params}`;
        console.log('Fetching URL:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            if (response.status === 404) {
                console.error('API endpoint not found:', url);
                showSelectedEmptyState();
                return;
            } else if (response.status === 401) {
                console.error('Authentication failed');
                Swal.fire({
                    icon: 'error',
                    title: 'Sesi Berakhir',
                    text: 'Sesi Anda telah berakhir. Silakan login kembali.',
                    confirmButtonText: 'Login'
                }).then(() => {
                    window.location.href = '/login';
                });
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('API result:', result);
        
        if (result.success) {
            console.log('Found', result.data.length, 'selected items');
            selectedPagination = result.pagination;
            renderSelectedItems(result.data);
            renderSelectedPagination();
        } else {
            console.error('Error loading selected items:', result.message);
            showSelectedEmptyState();
        }
    } catch (error) {
        console.error('Error loading selected items:', error);
        showSelectedEmptyState();
    }
}

function renderSelectedItems(items) {
    console.log('Rendering selected items:', items.length);
    
    const container = document.getElementById('selected_item_container');
    const emptyState = document.getElementById('empty_selected_state');
    
    if (!container || !emptyState) {
        console.error('Required DOM elements not found');
        return;
    }
    
    if (items.length === 0) {
        console.log('No items, showing empty state');
        showSelectedEmptyState();
        return;
    }
    
    console.log('Hiding empty state and rendering table');
    emptyState.classList.add('hidden');
    
    const tableHtml = `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${items.map(item => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    ${item.item.foto_item ? `<img src="/storage/${item.item.foto_item}" alt="${item.item.nama_item}" class="w-10 h-10 rounded-lg object-cover mr-3">` : '<div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-box text-gray-400"></i></div>'}
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${item.item.nama_item}</div>
                                        <div class="text-sm text-gray-500">${item.item.satuan}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${item.item.kategori.nama_kategori}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm" 
                                           value="${item.jumlah_diserahkan}" 
                                           min="1" 
                                           onchange="updateItemQuantity(${item.item_perlengkapan_id}, this.value)">
                                    <span class="text-sm text-gray-500">per orang</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="removeItem(${item.item_perlengkapan_id}, '${item.item.nama_item}')" class="text-red-600 hover:text-red-900 transition-colors">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    console.log('Setting container HTML');
    container.innerHTML = tableHtml;
}

function showSelectedEmptyState() {
    console.log('Showing selected empty state');
    
    const container = document.getElementById('selected_item_container');
    const emptyState = document.getElementById('empty_selected_state');
    const paginationContainer = document.getElementById('selected_pagination_container');
    const totalSelected = document.getElementById('total_selected');
    
    if (container) container.innerHTML = '';
    if (emptyState) emptyState.classList.remove('hidden');
    if (paginationContainer) paginationContainer.classList.add('hidden');
    if (totalSelected) totalSelected.textContent = '0';
}

function renderSelectedPagination() {
    const paginationContainer = document.getElementById('selected_pagination_container');
    const pageNumbers = document.getElementById('selected_page_numbers');
    const prevBtn = document.getElementById('selected_prev_btn');
    const nextBtn = document.getElementById('selected_next_btn');
    
    if (!paginationContainer || !pageNumbers || !prevBtn || !nextBtn) {
        console.error('Pagination elements not found');
        return;
    }
    
    // Update showing info
    const showingFrom = document.getElementById('selected_showing_from');
    const showingTo = document.getElementById('selected_showing_to');
    const showingTotal = document.getElementById('selected_showing_total');
    const totalSelected = document.getElementById('total_selected');
    
    if (showingFrom) showingFrom.textContent = selectedPagination.from || 0;
    if (showingTo) showingTo.textContent = selectedPagination.to || 0;
    if (showingTotal) showingTotal.textContent = selectedPagination.total || 0;
    if (totalSelected) totalSelected.textContent = selectedPagination.total || 0;
    
    // Show/hide pagination
    if (selectedPagination.total > selectedPagination.per_page) {
        paginationContainer.classList.remove('hidden');
    } else {
        paginationContainer.classList.add('hidden');
        return;
    }
    
    // Update prev/next buttons
    prevBtn.disabled = selectedPagination.current_page <= 1;
    nextBtn.disabled = selectedPagination.current_page >= selectedPagination.last_page;
    
    // Generate page numbers
    let pagesHtml = '';
    const currentPage = selectedPagination.current_page;
    const lastPage = selectedPagination.last_page;
    
    // Show first page
    if (currentPage > 3) {
        pagesHtml += `<button class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" onclick="loadSelectedItems(1)">1</button>`;
        if (currentPage > 4) {
            pagesHtml += `<span class="px-2 py-1 text-sm text-gray-500">...</span>`;
        }
    }
    
    // Show pages around current page
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
        const isActive = i === currentPage;
        pagesHtml += `<button class="px-2 py-1 text-sm border rounded ${isActive ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50'}" onclick="loadSelectedItems(${i})">${i}</button>`;
    }
    
    // Show last page
    if (currentPage < lastPage - 2) {
        if (currentPage < lastPage - 3) {
            pagesHtml += `<span class="px-2 py-1 text-sm text-gray-500">...</span>`;
        }
        pagesHtml += `<button class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" onclick="loadSelectedItems(${lastPage})">${lastPage}</button>`;
    }
    
    pageNumbers.innerHTML = pagesHtml;
}

// Add selected items
async function addSelectedItems() {
    if (tempSelectedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu item'
        });
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/add-items`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items: tempSelectedItems })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update selected list
            selectedItems = [...selectedItems, ...tempSelectedItems];
            tempSelectedItems = [];
            
            // Reload both tables
            loadItems();
            loadSelectedItems(selectedPagination.current_page);
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
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
            text: 'Terjadi kesalahan saat menambah item'
        });
    }
}

// Remove item
async function removeItem(itemId, itemName) {
    const result = await Swal.fire({
        title: 'Hapus Item?',
        text: `Hapus ${itemName} dari daftar penyerahan?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/remove-item`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ item_id: itemId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update selected list
                selectedItems = selectedItems.filter(item => item.item_id !== itemId);
                
                // Reload both tables
                loadItems();
                loadSelectedItems(selectedPagination.current_page);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
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

// Update item quantity
async function updateItemQuantity(itemId, quantity) {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/update-item-quantity`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ item_id: itemId, quantity: quantity })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
            // Reload to reset the value
            loadSelectedItems(selectedPagination.current_page);
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
    }
}

// Event Listeners
const kategoriSelect = document.getElementById('kategori_select');
const searchInput = document.getElementById('search_input');

if (kategoriSelect) {
    kategoriSelect.addEventListener('change', loadItems);
}

if (searchInput) {
    searchInput.addEventListener('input', debounce(loadItems, 500));
}

// Toggle add section visibility
const toggleButton = document.getElementById('toggle_add_section');
if (toggleButton) {
    toggleButton.addEventListener('click', function() {
        console.log('Toggle button clicked');
        
        const addSection = document.getElementById('add_item_section');
        const button = this;
        
        if (!addSection) {
            console.error('Add section not found');
            return;
        }
        
        if (addSection.style.display === 'none') {
            addSection.style.display = 'block';
            button.innerHTML = '<i class="fas fa-minus mr-2"></i>Sembunyikan';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-gray-600', 'hover:bg-gray-700');
            
            // Show initial message for the add section
            if (document.getElementById('item_table_body') && document.getElementById('empty_state')) {
                showEmptyStateWithMessage('Pilih kategori terlebih dahulu untuk memuat data item');
                updateItemCount(0);
            }
        } else {
            addSection.style.display = 'none';
            button.innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Item';
            button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    });
} else {
    console.error('Toggle button not found');
}

// Add selected items button
const addSelectedBtn = document.getElementById('add_selected');
if (addSelectedBtn) {
    addSelectedBtn.addEventListener('click', addSelectedItems);
} else {
    console.error('Add selected button not found');
}

// Select all checkbox
const selectAllCheckbox = document.getElementById('select_all_checkbox');
if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        const visibleItemIds = allItems.map(item => item.id);
        
        if (isChecked) {
            // Add all visible items to temp selection
            visibleItemIds.forEach(itemId => {
                if (!tempSelectedItems.some(item => item.item_id === itemId)) {
                    tempSelectedItems.push({ item_id: itemId, jumlah: 1 });
                }
            });
        } else {
            // Remove all visible items from temp selection
            tempSelectedItems = tempSelectedItems.filter(item => !visibleItemIds.includes(item.item_id));
        }
        
        // Update checkboxes and inputs
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            const itemId = parseInt(checkbox.value);
            const isSelected = tempSelectedItems.some(item => item.item_id === itemId);
            checkbox.checked = isSelected;
            
            const jumlahInput = document.querySelector(`input[data-item-id="${itemId}"]`);
            if (jumlahInput) {
                jumlahInput.disabled = !isSelected;
            }
            
            if (isSelected) {
                checkbox.closest('tr').classList.add('bg-green-50');
            } else {
                checkbox.closest('tr').classList.remove('bg-green-50');
            }
        });
        
        updateSelectedCount();
    });
} else {
    console.error('Select all checkbox not found');
}

// Event listeners for selected items search and pagination
const selectedSearchInput = document.getElementById('selected_search_input');
if (selectedSearchInput) {
    selectedSearchInput.addEventListener('input', debounce(function() {
        selectedPagination.currentPage = 1;
        loadSelectedItems(1);
    }, 500));
}

const selectedKategoriSelect = document.getElementById('selected_kategori_select');
if (selectedKategoriSelect) {
    selectedKategoriSelect.addEventListener('change', function() {
        selectedPagination.currentPage = 1;
        loadSelectedItems(1);
    });
}

const selectedPerPage = document.getElementById('selected_per_page');
if (selectedPerPage) {
    selectedPerPage.addEventListener('change', function() {
        selectedPagination.currentPage = 1;
        loadSelectedItems(1);
    });
}

// Pagination button event listeners
const selectedPrevBtn = document.getElementById('selected_prev_btn');
if (selectedPrevBtn) {
    selectedPrevBtn.addEventListener('click', function() {
        if (selectedPagination.current_page > 1) {
            loadSelectedItems(selectedPagination.current_page - 1);
        }
    });
}

const selectedNextBtn = document.getElementById('selected_next_btn');
if (selectedNextBtn) {
    selectedNextBtn.addEventListener('click', function() {
        if (selectedPagination.current_page < selectedPagination.last_page) {
            loadSelectedItems(selectedPagination.current_page + 1);
        }
    });
}

const resetSearchBtn = document.getElementById('reset_search');
if (resetSearchBtn) {
    resetSearchBtn.addEventListener('click', function() {
        document.getElementById('search_input').value = '';
        document.getElementById('kategori_select').value = '';
        
        // Clear the table and show message to select kategori
        document.getElementById('item_table_body').innerHTML = '';
        showEmptyStateWithMessage('Pilih kategori terlebih dahulu untuk memuat data item');
        updateItemCount(0);
    });
}

// Utility function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing item selection page...');
    
    // Check if required elements exist
    const requiredElements = [
        'selected_item_container',
        'empty_selected_state', 
        'total_selected',
        'toggle_add_section'
    ];
    
    let missingElements = [];
    requiredElements.forEach(id => {
        if (!document.getElementById(id)) {
            missingElements.push(id);
        }
    });
    
    if (missingElements.length > 0) {
        console.error('Missing required elements:', missingElements);
        return;
    }
    
    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }
    console.log('CSRF token found:', csrfToken.getAttribute('content').substring(0, 10) + '...');
    
    // Load selected items on page load (this should always show with pagination)
    console.log('Loading selected items...');
    loadSelectedItems();
    
    // For the add item section (which is hidden by default), 
    // prepare the initial state but don't load data until kategori is selected
    if (document.getElementById('item_table_body') && document.getElementById('empty_state')) {
        showEmptyStateWithMessage('Pilih kategori terlebih dahulu untuk memuat data item');
        updateItemCount(0);
    }
    
    console.log('Item selection page initialization complete');
});
</script>
@endpush
@endsection