@extends('perusahaan.layouts.app')

@section('page-title', 'Kelola Karyawan Penerima')
@section('page-subtitle', 'Pilih karyawan untuk penyerahan perlengkapan')

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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
        </div>
    </div>
</div>

<!-- Tambah Karyawan Baru - HIDDEN -->
<div id="add_karyawan_section" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6" style="display: none;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Tambah Karyawan Penerima</h3>
        <p class="text-sm text-gray-500 mt-1">Pilih karyawan yang akan menerima perlengkapan</p>
    </div>
    <div class="p-6">
        <form id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Project <span class="text-red-500">*</span></label>
                    <select id="project_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                        <option value="{{ $penyerahan->project_id }}" selected>{{ $penyerahan->project->nama }}</option>
                    </select>
                    <div class="mt-1 text-xs text-yellow-600">
                        <i class="fas fa-info-circle mr-1"></i>Wajib pilih project untuk mencegah data terlalu besar
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jabatan <span class="text-red-500">*</span></label>
                    <select id="jabatan_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">- Pilih Jabatan -</option>
                        @foreach($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                        @endforeach
                    </select>
                    <div class="mt-1 text-xs text-red-600">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Wajib pilih jabatan untuk mencegah data terlalu berat
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan (opsional)</label>
                    <div class="relative">
                        <input type="text" id="search_input" placeholder="Cari nama karyawan atau NIK..." class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        <span id="karyawan_count">Pilih jabatan untuk melihat karyawan</span>
                        <span class="text-gray-400 ml-2">({{ $jabatans->count() }} jabatan tersedia)</span>
                        <button type="button" id="reset_search" class="text-blue-600 hover:text-blue-800 ml-2">
                            <i class="fas fa-times-circle mr-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabel Karyawan Tersedia -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select_all_checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                    </tr>
                </thead>
                <tbody id="karyawan_table_body" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <!-- Loading State -->
        <div id="loading_state" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-500">Memuat data karyawan...</p>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="empty_state" class="px-6 py-12 text-center hidden">
            <div class="flex flex-col items-center">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-2">Tidak ada karyawan tersedia</p>
                <p class="text-gray-400 text-sm">Semua karyawan sudah terpilih atau tidak ada yang sesuai filter</p>
            </div>
        </div>

        <!-- Tombol Tambah -->
        <div class="mt-4 flex justify-end">
            <button id="add_selected" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition" disabled>
                <i class="fas fa-plus mr-2"></i>Tambah Karyawan (<span id="selected_count">0</span>)
            </button>
        </div>
    </div>
</div>

<!-- Daftar Karyawan Terpilih -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Karyawan Terpilih</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar karyawan yang akan menerima perlengkapan</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Total: <span id="total_selected" class="font-semibold text-gray-900">{{ count($selectedKaryawanIds) }}</span> karyawan
                </div>
                <button id="toggle_add_section" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Karyawan
                </button>
            </div>
        </div>
        
        <!-- Search and Filter for Selected Karyawan -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan Terpilih</label>
                <div class="relative">
                    <input type="text" id="selected_search_input" placeholder="Cari nama atau NIK..." class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jabatan</label>
                <select id="selected_jabatan_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
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
    
    <div id="selected_karyawan_container">
        <!-- Content will be loaded via JavaScript -->
    </div>
    
    <!-- Pagination for Selected Karyawan -->
    <div id="selected_pagination_container" class="px-6 py-4 border-t border-gray-200 hidden">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan <span id="selected_showing_from">1</span> sampai <span id="selected_showing_to">10</span> dari <span id="selected_showing_total">0</span> karyawan
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
            <i class="fas fa-user-plus text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Belum ada karyawan terpilih</p>
            <p class="text-gray-400 text-sm">Pilih karyawan dari tabel di atas untuk menambahkan ke daftar penerima</p>
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
let selectedKaryawanIds = @json($selectedKaryawanIds);
let allKaryawans = [];
let tempSelectedIds = []; // For new selections

// Load karyawan data (exclude already selected)
async function loadKaryawans() {
    const jabatanId = document.getElementById('jabatan_select').value;
    const search = document.getElementById('search_input').value;
    
    // Show loading state
    document.getElementById('loading_state').classList.remove('hidden');
    document.getElementById('empty_state').classList.add('hidden');
    
    // Clear table first
    document.getElementById('karyawan_table_body').innerHTML = '';
    
    // Check if jabatan is selected
    if (!jabatanId || jabatanId === '') {
        document.getElementById('loading_state').classList.add('hidden');
        showEmptyStateWithMessage('Pilih jabatan terlebih dahulu untuk memuat data karyawan');
        updateKaryawanCount(0);
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/karyawan-selection?jabatan_id=${jabatanId}&search=${encodeURIComponent(search)}`);
        const result = await response.json();
        
        if (result.success) {
            // Filter out already selected karyawan
            const availableKaryawans = result.data.filter(k => !selectedKaryawanIds.includes(k.id));
            allKaryawans = availableKaryawans;
            
            if (availableKaryawans.length === 0) {
                if (result.message) {
                    showEmptyStateWithMessage(result.message);
                } else {
                    showEmptyStateWithMessage('Semua karyawan pada jabatan ini sudah terpilih atau tidak ada yang sesuai filter');
                }
            } else {
                renderKaryawanTable(availableKaryawans);
            }
            updateKaryawanCount(availableKaryawans.length);
        } else {
            showEmptyStateWithMessage(result.message || 'Gagal memuat data karyawan');
        }
    } catch (error) {
        console.error('Error loading karyawans:', error);
        showEmptyStateWithMessage('Terjadi kesalahan saat memuat data');
    } finally {
        document.getElementById('loading_state').classList.add('hidden');
    }
}

function renderKaryawanTable(karyawans) {
    const tbody = document.getElementById('karyawan_table_body');
    
    if (karyawans.length === 0) {
        showEmptyState();
        return;
    }
    
    tbody.innerHTML = karyawans.map(karyawan => {
        const isSelected = tempSelectedIds.includes(karyawan.id);
        return `
            <tr class="hover:bg-gray-50 ${isSelected ? 'bg-green-50' : ''}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" 
                           class="karyawan-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500" 
                           value="${karyawan.id}" 
                           ${isSelected ? 'checked' : ''}>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${karyawan.nama_lengkap}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${karyawan.nik_karyawan}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${karyawan.jabatan.nama}</div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.karyawan-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', handleKaryawanSelection);
    });
    
    updateSelectAllCheckbox();
    updateSelectedCount();
}

function showEmptyState() {
    document.getElementById('karyawan_table_body').innerHTML = '';
    document.getElementById('empty_state').classList.remove('hidden');
}

function showEmptyStateWithMessage(message) {
    document.getElementById('karyawan_table_body').innerHTML = '';
    const emptyState = document.getElementById('empty_state');
    
    // Update the empty state message
    const emptyStateContent = emptyState.querySelector('.flex.flex-col.items-center');
    emptyStateContent.innerHTML = `
        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg mb-2">${message}</p>
        <p class="text-gray-400 text-sm">Pilih jabatan yang berbeda atau ubah filter pencarian</p>
    `;
    
    emptyState.classList.remove('hidden');
}

function handleKaryawanSelection(event) {
    const karyawanId = parseInt(event.target.value);
    const isChecked = event.target.checked;
    
    if (isChecked) {
        if (!tempSelectedIds.includes(karyawanId)) {
            tempSelectedIds.push(karyawanId);
        }
        event.target.closest('tr').classList.add('bg-green-50');
    } else {
        tempSelectedIds = tempSelectedIds.filter(id => id !== karyawanId);
        event.target.closest('tr').classList.remove('bg-green-50');
    }
    
    updateSelectAllCheckbox();
    updateSelectedCount();
}

function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById('select_all_checkbox');
    const visibleCheckboxes = document.querySelectorAll('.karyawan-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.karyawan-checkbox:checked');
    
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
    const count = tempSelectedIds.length;
    document.getElementById('selected_count').textContent = count;
    document.getElementById('add_selected').disabled = count === 0;
}

function updateKaryawanCount(count) {
    document.getElementById('karyawan_count').textContent = `${count} karyawan tersedia`;
}

// Add selected karyawan
document.getElementById('add_selected').addEventListener('click', async function() {
    if (tempSelectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu karyawan'
        });
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/add-karyawan`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ karyawan_ids: tempSelectedIds })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update selected list
            selectedKaryawanIds = [...selectedKaryawanIds, ...tempSelectedIds];
            tempSelectedIds = [];
            
            // Reload both tables
            loadKaryawans();
            loadSelectedKaryawans(selectedPagination.current_page);
            
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
            text: 'Terjadi kesalahan saat menambah karyawan'
        });
    }
});

// Remove karyawan
async function removeKaryawan(karyawanId, karyawanName) {
    const result = await Swal.fire({
        title: 'Hapus Karyawan?',
        text: `Hapus ${karyawanName} dari daftar penerima?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/remove-karyawan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ karyawan_id: karyawanId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update selected list
                selectedKaryawanIds = selectedKaryawanIds.filter(id => id !== karyawanId);
                
                // Reload both tables
                loadKaryawans();
                loadSelectedKaryawans(selectedPagination.current_page);
                
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
                text: 'Terjadi kesalahan saat menghapus karyawan'
            });
        }
    }
}

// Pagination state for selected karyawan
let selectedPagination = {
    currentPage: 1,
    perPage: 10,
    total: 0,
    lastPage: 1
};

// Load selected karyawan list with pagination and search
async function loadSelectedKaryawans(page = 1) {
    console.log('Loading selected karyawans for page:', page);
    
    const search = document.getElementById('selected_search_input')?.value || '';
    const jabatanId = document.getElementById('selected_jabatan_select')?.value || 'all';
    const perPage = document.getElementById('selected_per_page')?.value || 10;
    
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: perPage,
            search: search,
            jabatan_id: jabatanId
        });
        
        const url = `/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/selected-karyawan?${params}`;
        console.log('Fetching URL:', url);
        
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('API result:', result);
        
        if (result.success) {
            console.log('Found', result.data.length, 'selected karyawans');
            selectedPagination = result.pagination;
            renderSelectedKaryawans(result.data);
            renderSelectedPagination();
        } else {
            console.error('Error loading selected karyawans:', result.message);
            showSelectedEmptyState();
        }
    } catch (error) {
        console.error('Error loading selected karyawans:', error);
        showSelectedEmptyState();
    }
}

function showSelectedEmptyState() {
    console.log('Showing selected empty state');
    
    const container = document.getElementById('selected_karyawan_container');
    const emptyState = document.getElementById('empty_selected_state');
    const paginationContainer = document.getElementById('selected_pagination_container');
    const totalSelected = document.getElementById('total_selected');
    
    if (container) container.innerHTML = '';
    if (emptyState) emptyState.classList.remove('hidden');
    if (paginationContainer) paginationContainer.classList.add('hidden');
    if (totalSelected) totalSelected.textContent = '0';
}

function renderSelectedKaryawans(karyawans) {
    console.log('Rendering selected karyawans:', karyawans.length);
    
    const container = document.getElementById('selected_karyawan_container');
    const emptyState = document.getElementById('empty_selected_state');
    
    if (!container || !emptyState) {
        console.error('Required DOM elements not found');
        return;
    }
    
    if (karyawans.length === 0) {
        console.log('No karyawans, showing empty state');
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${karyawans.map(karyawan => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${karyawan.nama_lengkap}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${karyawan.nik_karyawan}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${karyawan.jabatan.nama}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="removeKaryawan(${karyawan.id}, '${karyawan.nama_lengkap}')" class="text-red-600 hover:text-red-900 transition-colors">
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

function renderSelectedPagination() {
    const paginationContainer = document.getElementById('selected_pagination_container');
    const pageNumbers = document.getElementById('selected_page_numbers');
    const prevBtn = document.getElementById('selected_prev_btn');
    const nextBtn = document.getElementById('selected_next_btn');
    
    // Update showing info
    document.getElementById('selected_showing_from').textContent = selectedPagination.from || 0;
    document.getElementById('selected_showing_to').textContent = selectedPagination.to || 0;
    document.getElementById('selected_showing_total').textContent = selectedPagination.total || 0;
    document.getElementById('total_selected').textContent = selectedPagination.total || 0;
    
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
        pagesHtml += `<button class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" onclick="loadSelectedKaryawans(1)">1</button>`;
        if (currentPage > 4) {
            pagesHtml += `<span class="px-2 py-1 text-sm text-gray-500">...</span>`;
        }
    }
    
    // Show pages around current page
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
        const isActive = i === currentPage;
        pagesHtml += `<button class="px-2 py-1 text-sm border rounded ${isActive ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50'}" onclick="loadSelectedKaryawans(${i})">${i}</button>`;
    }
    
    // Show last page
    if (currentPage < lastPage - 2) {
        if (currentPage < lastPage - 3) {
            pagesHtml += `<span class="px-2 py-1 text-sm text-gray-500">...</span>`;
        }
        pagesHtml += `<button class="px-2 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" onclick="loadSelectedKaryawans(${lastPage})">${lastPage}</button>`;
    }
    
    pageNumbers.innerHTML = pagesHtml;
}

// Event Listeners
const jabatanSelect = document.getElementById('jabatan_select');
const searchInput = document.getElementById('search_input');

if (jabatanSelect) {
    jabatanSelect.addEventListener('change', loadKaryawans);
} else {
    console.error('Jabatan select not found');
}

if (searchInput) {
    searchInput.addEventListener('input', debounce(loadKaryawans, 500));
} else {
    console.error('Search input not found');
}

// Toggle add section visibility
const toggleButton = document.getElementById('toggle_add_section');
if (toggleButton) {
    toggleButton.addEventListener('click', function() {
        console.log('Toggle button clicked');
        
        const addSection = document.getElementById('add_karyawan_section');
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
            
            // Show initial message for the add section (not affecting selected employees table)
            if (document.getElementById('karyawan_table_body') && document.getElementById('empty_state')) {
                showEmptyStateWithMessage('Pilih jabatan terlebih dahulu untuk memuat data karyawan');
                updateKaryawanCount(0);
            }
        } else {
            addSection.style.display = 'none';
            button.innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah Karyawan';
            button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    });
} else {
    console.error('Toggle button not found');
}

// Event listeners for selected karyawan search and pagination
document.getElementById('selected_search_input').addEventListener('input', debounce(function() {
    selectedPagination.currentPage = 1;
    loadSelectedKaryawans(1);
}, 500));

document.getElementById('selected_jabatan_select').addEventListener('change', function() {
    selectedPagination.currentPage = 1;
    loadSelectedKaryawans(1);
});

document.getElementById('selected_per_page').addEventListener('change', function() {
    selectedPagination.currentPage = 1;
    loadSelectedKaryawans(1);
});

// Pagination button event listeners
document.getElementById('selected_prev_btn').addEventListener('click', function() {
    if (selectedPagination.current_page > 1) {
        loadSelectedKaryawans(selectedPagination.current_page - 1);
    }
});

document.getElementById('selected_next_btn').addEventListener('click', function() {
    if (selectedPagination.current_page < selectedPagination.last_page) {
        loadSelectedKaryawans(selectedPagination.current_page + 1);
    }
});

document.getElementById('select_all_checkbox').addEventListener('change', function() {
    const isChecked = this.checked;
    const visibleKaryawanIds = allKaryawans.map(k => k.id);
    
    if (isChecked) {
        // Add all visible karyawan to temp selection
        visibleKaryawanIds.forEach(id => {
            if (!tempSelectedIds.includes(id)) {
                tempSelectedIds.push(id);
            }
        });
    } else {
        // Remove all visible karyawan from temp selection
        tempSelectedIds = tempSelectedIds.filter(id => !visibleKaryawanIds.includes(id));
    }
    
    // Update checkboxes
    document.querySelectorAll('.karyawan-checkbox').forEach(checkbox => {
        const karyawanId = parseInt(checkbox.value);
        checkbox.checked = tempSelectedIds.includes(karyawanId);
        
        if (checkbox.checked) {
            checkbox.closest('tr').classList.add('bg-green-50');
        } else {
            checkbox.closest('tr').classList.remove('bg-green-50');
        }
    });
    
    updateSelectedCount();
});

document.getElementById('reset_search').addEventListener('click', function() {
    document.getElementById('search_input').value = '';
    document.getElementById('jabatan_select').value = '';
    
    // Clear the table and show message to select jabatan
    document.getElementById('karyawan_table_body').innerHTML = '';
    showEmptyStateWithMessage('Pilih jabatan terlebih dahulu untuk memuat data karyawan');
    updateKaryawanCount(0);
});

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
    console.log('DOM loaded, initializing page...');
    
    // Load selected karyawans on page load (this should always show with pagination)
    loadSelectedKaryawans();
    
    // For the add karyawan section (which is hidden by default), 
    // prepare the initial state but don't load data until jabatan is selected
    // Only call this if the elements exist
    if (document.getElementById('karyawan_table_body') && document.getElementById('empty_state')) {
        showEmptyStateWithMessage('Pilih jabatan terlebih dahulu untuk memuat data karyawan');
        updateKaryawanCount(0);
    }
    
    console.log('Page initialization complete');
});
</script>
@endpush
@endsection