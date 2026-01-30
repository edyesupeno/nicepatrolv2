@extends('perusahaan.layouts.app')

@section('page-title', 'Serahkan Item Perlengkapan')
@section('page-subtitle', 'Kelola penyerahan item kepada karyawan')

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
                <label class="block text-sm font-medium text-gray-500 mb-1">Total Karyawan</label>
                <p class="text-gray-900">{{ $karyawans->count() }} orang</p>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan</label>
                <div class="relative">
                    <input type="text" id="search_input" placeholder="Cari nama karyawan atau NIK..." 
                           class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select id="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="belum_diserahkan">Belum Diserahkan</option>
                    <option value="sebagian_diserahkan">Sebagian Diserahkan</option>
                    <option value="sudah_diserahkan">Sudah Diserahkan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tampilkan per halaman</label>
                <select id="per_page" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                    <option value="100">100 per halaman</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Karyawan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Daftar Karyawan</h3>
        <p class="text-sm text-gray-500 mt-1">Klik nama karyawan untuk mengelola item perlengkapan</p>
    </div>
    
    <!-- Table Content -->
    <div id="karyawan-container">
        <!-- Content will be loaded via JavaScript -->
    </div>
    
    <!-- Pagination -->
    <div id="pagination-container" class="px-6 py-4 border-t border-gray-200 hidden">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan <span id="showing-from">1</span> sampai <span id="showing-to">10</span> dari <span id="showing-total">0</span> karyawan
            </div>
            <div class="flex items-center space-x-2">
                <button id="prev-btn" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                </button>
                <div id="page-numbers" class="flex items-center space-x-1">
                    <!-- Page numbers will be inserted here -->
                </div>
                <button id="next-btn" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Print Bukti -->
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
let currentPage = 1;
let perPage = 10;
let searchQuery = '';
let statusFilter = 'all';

// Pagination state
let pagination = {
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
};

// Load karyawan data
async function loadKaryawanData(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: perPage,
            search: searchQuery,
            status: statusFilter
        });
        
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/karyawan-data?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            renderKaryawanTable(result.data);
            updatePagination(result.pagination);
        } else {
            showError(result.message);
        }
    } catch (error) {
        console.error('Error loading data:', error);
        showError('Gagal memuat data');
    }
}

// Render karyawan table
function renderKaryawanTable(karyawans) {
    const container = document.getElementById('karyawan-container');
    
    if (karyawans.length === 0) {
        container.innerHTML = `
            <div class="px-6 py-12 text-center">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-2">Tidak ada karyawan ditemukan</p>
                <p class="text-gray-400 text-sm">Coba ubah filter atau kata kunci pencarian</p>
            </div>
        `;
        return;
    }
    
    const tableHtml = `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diserahkan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${karyawans.map(karyawan => {
                        const statusColor = getStatusColor(karyawan.status_penyerahan);
                        const statusText = getStatusText(karyawan.status_penyerahan);
                        
                        return `
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="openSerahkanModal(${karyawan.id}, '${karyawan.nama_lengkap}', '${karyawan.nik_karyawan}')">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">${karyawan.nama_lengkap}</div>
                                            <div class="text-sm text-gray-500">${karyawan.nik_karyawan}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${karyawan.total_items} item</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${karyawan.diserahkan_items} item</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${statusColor}">
                                        ${statusText}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/serahkan-karyawan/${karyawan.id}" 
                                           class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                            <i class="fas fa-hand-holding mr-1"></i>Serahkan
                                        </a>
                                        ${karyawan.diserahkan_items > 0 ? `
                                            <button onclick="event.stopPropagation(); showPrintModal(${karyawan.id}, '${karyawan.nama_lengkap}')" 
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-print mr-1"></i>Print
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = tableHtml;
}

// Get status color
function getStatusColor(status) {
    switch(status) {
        case 'belum_diserahkan':
            return 'bg-red-100 text-red-800';
        case 'sebagian_diserahkan':
            return 'bg-yellow-100 text-yellow-800';
        case 'sudah_diserahkan':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Get status text
function getStatusText(status) {
    switch(status) {
        case 'belum_diserahkan':
            return 'Belum Diserahkan';
        case 'sebagian_diserahkan':
            return 'Sebagian Diserahkan';
        case 'sudah_diserahkan':
            return 'Sudah Diserahkan';
        default:
            return 'Unknown';
    }
}

// Show print modal
async function showPrintModal(karyawanId, namaKaryawan) {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/print-bukti/${karyawanId}`, {
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

// Close print modal
function closePrintModal() {
    document.getElementById('printModal').classList.add('hidden');
}

// Print bukti
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

// Update pagination
function updatePagination(paginationData) {
    pagination = paginationData;
    
    const container = document.getElementById('pagination-container');
    const showingFrom = document.getElementById('showing-from');
    const showingTo = document.getElementById('showing-to');
    const showingTotal = document.getElementById('showing-total');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const pageNumbers = document.getElementById('page-numbers');
    
    if (pagination.total > 0) {
        container.classList.remove('hidden');
        
        showingFrom.textContent = pagination.from || 0;
        showingTo.textContent = pagination.to || 0;
        showingTotal.textContent = pagination.total || 0;
        
        prevBtn.disabled = pagination.current_page <= 1;
        nextBtn.disabled = pagination.current_page >= pagination.last_page;
        
        // Generate page numbers
        let pagesHtml = '';
        const currentPage = pagination.current_page;
        const lastPage = pagination.last_page;
        
        for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
            const isActive = i === currentPage;
            pagesHtml += `<button class="px-2 py-1 text-sm border rounded ${isActive ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50'}" onclick="loadKaryawanData(${i})">${i}</button>`;
        }
        
        pageNumbers.innerHTML = pagesHtml;
    } else {
        container.classList.add('hidden');
    }
}

// Show error
function showError(message) {
    const container = document.getElementById('karyawan-container');
    container.innerHTML = `
        <div class="px-6 py-12 text-center">
            <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
            <p class="text-red-500 text-lg mb-2">Terjadi Kesalahan</p>
            <p class="text-gray-400 text-sm">${message}</p>
        </div>
    `;
}

// Debounce function
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Search and filters
    document.getElementById('search_input').addEventListener('input', debounce(function() {
        searchQuery = this.value;
        currentPage = 1;
        loadKaryawanData(1);
    }, 500));
    
    document.getElementById('status_filter').addEventListener('change', function() {
        statusFilter = this.value;
        currentPage = 1;
        loadKaryawanData(1);
    });
    
    document.getElementById('per_page').addEventListener('change', function() {
        perPage = parseInt(this.value);
        currentPage = 1;
        loadKaryawanData(1);
    });
    
    // Pagination
    document.getElementById('prev-btn').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadKaryawanData(currentPage);
        }
    });
    
    document.getElementById('next-btn').addEventListener('click', function() {
        if (currentPage < pagination.last_page) {
            currentPage++;
            loadKaryawanData(currentPage);
        }
    });
    
    // Load initial data
    loadKaryawanData(1);
});
</script>
@endpush
@endsection