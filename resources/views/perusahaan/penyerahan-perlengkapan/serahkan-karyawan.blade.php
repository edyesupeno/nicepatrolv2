@extends('perusahaan.layouts.app')

@section('page-title', 'Serahkan Item Perlengkapan')
@section('page-subtitle', 'Kelola penyerahan item kepada ' . $karyawan->nama_lengkap)

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.serahkan-item-page', $penyerahanRecord->hash_id) }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Karyawan
    </a>
</div>

<!-- Informasi Karyawan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Informasi Karyawan</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                <p class="text-gray-900 font-medium">{{ $karyawan->nama_lengkap }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">NIK</label>
                <p class="text-gray-900">{{ $karyawan->nik_karyawan }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jabatan</label>
                <p class="text-gray-900">{{ $karyawan->jabatan->nama ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Project</label>
                <p class="text-gray-900">{{ $penyerahanRecord->project->nama }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Item</label>
                <div class="relative">
                    <input type="text" id="search_input" placeholder="Cari nama item..." 
                           class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kategori</label>
                <select id="category_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select id="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="pending">Belum Diserahkan</option>
                    <option value="completed">Sudah Diserahkan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="clearFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6">
        <div class="flex flex-wrap gap-3">
            <button onclick="selectAll()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-check-double mr-2"></i>Pilih Semua
            </button>
            <button onclick="selectNone()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Batal Pilih
            </button>
            <button onclick="selectPending()" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                <i class="fas fa-clock mr-2"></i>Pilih Belum Diserahkan
            </button>
            <div class="flex-1"></div>
            <button id="serahkan-btn" onclick="serahkanSelectedItems()" disabled 
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-hand-holding mr-2"></i>Serahkan Item (<span id="selected-count">0</span>)
            </button>
        </div>
    </div>
</div>

<!-- Items List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Daftar Item Perlengkapan</h3>
        <p class="text-sm text-gray-500 mt-1">Total {{ $items->count() }} item</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSelectAll(this)">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Diserahkan</th>
                </tr>
            </thead>
            <tbody id="items-tbody" class="bg-white divide-y divide-gray-200">
                @foreach($items as $item)
                <tr class="item-row hover:bg-gray-50" 
                    data-item-id="{{ $item->id }}"
                    data-item-name="{{ strtolower($item->item->nama_item) }}"
                    data-category-id="{{ $item->item->kategori->id ?? '' }}"
                    data-status="{{ $item->is_diserahkan ? 'completed' : 'pending' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" 
                               class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                               value="{{ $item->id }}"
                               {{ $item->is_diserahkan ? 'disabled' : '' }}
                               onchange="updateSelectedCount()">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($item->item->foto_item)
                                <img src="{{ asset('storage/' . $item->item->foto_item) }}" 
                                     alt="{{ $item->item->nama_item }}" 
                                     class="w-12 h-12 rounded-lg object-cover mr-4">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->item->nama_item }}</div>
                                <div class="text-sm text-gray-500">{{ $item->item->satuan }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            {{ $item->item->kategori->nama_kategori ?? 'Tidak ada kategori' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $item->jumlah_diserahkan }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->is_diserahkan)
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                <i class="fas fa-check mr-1"></i>Sudah Diserahkan
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                <i class="fas fa-clock mr-1"></i>Belum Diserahkan
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->tanggal_diserahkan ? $item->tanggal_diserahkan->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Empty State -->
    <div id="empty-state" class="hidden px-6 py-12 text-center">
        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg mb-2">Tidak ada item ditemukan</p>
        <p class="text-gray-400 text-sm">Coba ubah filter atau kata kunci pencarian</p>
    </div>
</div>

<!-- Action Buttons Bottom -->
<div class="mt-6 flex justify-between items-center">
    <div class="text-sm text-gray-500">
        <span id="total-items">{{ $items->count() }}</span> item total, 
        <span id="completed-items">{{ $items->where('is_diserahkan', true)->count() }}</span> sudah diserahkan
    </div>
    <div class="flex gap-3">
        @if($items->where('is_diserahkan', true)->count() > 0)
            <button onclick="showPrintModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-print mr-2"></i>Print Bukti
            </button>
            <button onclick="sendWhatsApp()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                <i class="fab fa-whatsapp mr-2"></i>Kirim WhatsApp
            </button>
        @endif
    </div>
</div>

<!-- Print Modal -->
<div id="printModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Bukti Penyerahan Perlengkapan</h3>
                    <p class="text-sm text-gray-500">Preview dan cetak bukti penyerahan</p>
                </div>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Print Content -->
            <div id="print-content" class="mt-4">
                <!-- Print content will be loaded here -->
            </div>
            
            <!-- Print Actions -->
            <div class="mt-6 flex justify-end space-x-3 pt-4 border-t">
                <button onclick="closePrintModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Tutup
                </button>
                <button onclick="printBukti()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedItems = [];

// Filter and search functions
function filterItems() {
    const searchTerm = document.getElementById('search_input').value.toLowerCase();
    const categoryFilter = document.getElementById('category_filter').value;
    const statusFilter = document.getElementById('status_filter').value;
    
    const rows = document.querySelectorAll('.item-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const itemName = row.dataset.itemName;
        const categoryId = row.dataset.categoryId;
        const status = row.dataset.status;
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !itemName.includes(searchTerm)) {
            showRow = false;
        }
        
        // Category filter
        if (categoryFilter !== 'all' && categoryId !== categoryFilter) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter !== 'all' && status !== statusFilter) {
            showRow = false;
        }
        
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    const emptyState = document.getElementById('empty-state');
    const tbody = document.getElementById('items-tbody');
    
    if (visibleCount === 0) {
        tbody.style.display = 'none';
        emptyState.classList.remove('hidden');
    } else {
        tbody.style.display = '';
        emptyState.classList.add('hidden');
    }
}

// Clear all filters
function clearFilters() {
    document.getElementById('search_input').value = '';
    document.getElementById('category_filter').value = 'all';
    document.getElementById('status_filter').value = 'all';
    filterItems();
}

// Selection functions
function selectAll() {
    const checkboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('.item-row');
        if (row.style.display !== 'none') {
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

function selectNone() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateSelectedCount();
}

function selectPending() {
    selectNone();
    const checkboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('.item-row');
        if (row.dataset.status === 'pending' && row.style.display !== 'none') {
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

function toggleSelectAll(masterCheckbox) {
    if (masterCheckbox.checked) {
        selectAll();
    } else {
        selectNone();
    }
}

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    selectedItems = Array.from(checkedBoxes).map(cb => cb.value);
    
    const count = selectedItems.length;
    document.getElementById('selected-count').textContent = count;
    document.getElementById('serahkan-btn').disabled = count === 0;
    
    // Update master checkbox
    const masterCheckbox = document.getElementById('select-all-checkbox');
    const visibleCheckboxes = Array.from(document.querySelectorAll('.item-checkbox:not(:disabled)'))
        .filter(cb => cb.closest('.item-row').style.display !== 'none');
    
    if (visibleCheckboxes.length === 0) {
        masterCheckbox.indeterminate = false;
        masterCheckbox.checked = false;
    } else if (visibleCheckboxes.every(cb => cb.checked)) {
        masterCheckbox.indeterminate = false;
        masterCheckbox.checked = true;
    } else if (visibleCheckboxes.some(cb => cb.checked)) {
        masterCheckbox.indeterminate = true;
        masterCheckbox.checked = false;
    } else {
        masterCheckbox.indeterminate = false;
        masterCheckbox.checked = false;
    }
}

// Submit selected items
async function serahkanSelectedItems() {
    if (selectedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu item untuk diserahkan'
        });
        return;
    }
    
    const result = await Swal.fire({
        title: 'Konfirmasi Penyerahan',
        text: `Serahkan ${selectedItems.length} item terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Serahkan!',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahanRecord->hash_id }}/serahkan-karyawan-items`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    karyawan_id: {{ $karyawan->id }},
                    item_ids: selectedItems 
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Reload page to update status
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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
                text: 'Terjadi kesalahan saat menyerahkan item'
            });
        }
    }
}

// Print functions
async function showPrintModal() {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahanRecord->hash_id }}/print-bukti/{{ $karyawan->id }}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('print-content').innerHTML = result.html;
            document.getElementById('printModal').classList.remove('hidden');
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
            text: 'Gagal memuat bukti penyerahan'
        });
    }
}

function closePrintModal() {
    document.getElementById('printModal').classList.add('hidden');
}

function printBukti() {
    const printContent = document.getElementById('print-content').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Bukti Penyerahan Perlengkapan</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
                    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                    .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .items-table th { background-color: #f5f5f5; }
                    .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-top: 50px; }
                    .signature-box { text-align: center; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// WhatsApp function
async function sendWhatsApp() {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahanRecord->hash_id }}/send-whatsapp/{{ $karyawan->id }}`, {
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
                text: 'Notifikasi WhatsApp berhasil dikirim',
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
            text: 'Gagal mengirim notifikasi WhatsApp'
        });
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter events
    document.getElementById('search_input').addEventListener('input', filterItems);
    document.getElementById('category_filter').addEventListener('change', filterItems);
    document.getElementById('status_filter').addEventListener('change', filterItems);
    
    // Initialize selected count
    updateSelectedCount();
});
</script>
@endpush
@endsection