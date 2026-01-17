@extends('perusahaan.layouts.app')

@section('title', 'Detail Atensi - ' . $atensi->judul)
@section('page-title', 'Detail Atensi')
@section('page-subtitle', $atensi->judul)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.atensi.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-sky-600">
                    <i class="fas fa-bullhorn mr-2"></i>
                    Atensi
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Atensi Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-{{ $atensi->prioritas_color }}-500">
                                    <i class="{{ $atensi->prioritas_icon }} text-white"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $atensi->judul }}</h1>
                                    <p class="text-sm text-gray-500">ID: {{ $atensi->hash_id }}</p>
                                </div>
                            </div>
                            
                            <!-- Badges -->
                            <div class="flex items-center gap-2 mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $atensi->prioritas_color }}-100 text-{{ $atensi->prioritas_color }}-700">
                                    <i class="{{ $atensi->prioritas_icon }} mr-2"></i>{{ $atensi->prioritas_label }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $atensi->status_color }}-100 text-{{ $atensi->status_color }}-700">
                                    {{ $atensi->status_label }}
                                </span>
                                @if($atensi->is_urgent)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Mendesak
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('perusahaan.atensi.edit', $atensi->hash_id) }}" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                            <button onclick="confirmDelete('{{ $atensi->hash_id }}')" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                                <i class="fas fa-trash mr-2"></i>Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed">{{ $atensi->deskripsi }}</p>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Project</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-project-diagram text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $atensi->project->nama }}</p>
                                        <p class="text-xs text-gray-500">Project</p>
                                    </div>
                                </div>
                                @if($atensi->area)
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $atensi->area->nama }}</p>
                                            <p class="text-xs text-gray-500">Area</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <i class="fas fa-users text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $atensi->target_type_label }}</p>
                                        <p class="text-xs text-gray-500">Target Penerima</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Waktu</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-plus text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $atensi->tanggal_mulai->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">Tanggal Mulai</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-minus text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $atensi->tanggal_selesai->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">Tanggal Selesai</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $atensi->creator->name }}</p>
                                        <p class="text-xs text-gray-500">Dibuat oleh</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $atensi->created_at->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Waktu Dibuat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Penerima</h3>
                        <span class="text-sm text-gray-500" id="recipientCount">{{ $recipientStats['total'] }} orang</span>
                    </div>

                    <!-- Search and Filter -->
                    <div class="mb-4 flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="recipientSearch"
                                placeholder="Cari nama atau email..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm"
                            >
                        </div>
                        <select 
                            id="recipientStatusFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm"
                        >
                            <option value="">Semua Status</option>
                            <option value="read">Sudah Dibaca</option>
                            <option value="unread">Belum Dibaca</option>
                            <option value="acknowledged">Dikonfirmasi</option>
                            <option value="unacknowledged">Belum Dikonfirmasi</option>
                        </select>
                    </div>

                    <!-- Recipients Table Container -->
                    <div id="recipientsContainer">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
                            <span class="ml-3 text-gray-600">Memuat data penerima...</span>
                        </div>
                    </div>

                    <!-- Pagination Container -->
                    <div id="recipientsPagination" class="mt-4 hidden"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Progress Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress</h3>
                
                <!-- Read Progress -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Dibaca</span>
                        <span class="text-sm font-bold text-blue-600">{{ $atensi->read_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-300" style="width: {{ $atensi->read_percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $recipientStats['read'] }} dari {{ $recipientStats['total'] }} orang</p>
                </div>

                <!-- Acknowledgment Progress -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Dikonfirmasi</span>
                        <span class="text-sm font-bold text-green-600">{{ $atensi->acknowledgment_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: {{ $atensi->acknowledgment_percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $recipientStats['acknowledged'] }} dari {{ $recipientStats['total'] }} orang</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-users text-blue-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Total Penerima</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $recipientStats['total'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-eye text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Sudah Dibaca</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $recipientStats['read'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-double text-purple-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Dikonfirmasi</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $recipientStats['acknowledged'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-orange-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Belum Dibaca</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $recipientStats['total'] - $recipientStats['read'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-plus text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Atensi Dibuat</p>
                            <p class="text-xs text-gray-500">{{ $atensi->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($atensi->published_at)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-paper-plane text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Dikirim ke Penerima</p>
                            <p class="text-xs text-gray-500">{{ $atensi->published_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($recipientStats['read'] > 0)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-eye text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Mulai Dibaca</p>
                            <p class="text-xs text-gray-500" id="firstReadTime">Memuat...</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let isLoading = false;
let searchTimeout;

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', function() {
    loadRecipients();
    
    // Search functionality with debounce
    document.getElementById('recipientSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadRecipients();
        }, 500);
    });
    
    // Status filter
    document.getElementById('recipientStatusFilter').addEventListener('change', function() {
        currentPage = 1;
        loadRecipients();
    });
});

async function loadRecipients(page = 1) {
    if (isLoading) return;
    
    isLoading = true;
    const container = document.getElementById('recipientsContainer');
    const pagination = document.getElementById('recipientsPagination');
    
    // Show loading state
    if (page === 1) {
        container.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
                <span class="ml-3 text-gray-600">Memuat data penerima...</span>
            </div>
        `;
    }
    
    try {
        const search = document.getElementById('recipientSearch').value;
        const status = document.getElementById('recipientStatusFilter').value;
        
        const params = new URLSearchParams({
            page: page,
            per_page: 20,
            search: search,
            status: status
        });
        
        const response = await fetch(`{{ route('perusahaan.atensi.recipients', $atensi->hash_id) }}?${params}`);
        const data = await response.json();
        
        if (data.success) {
            renderRecipients(data.data, page === 1);
            renderPagination(data.pagination);
            
            // Update first read time if available
            if (data.data.length > 0) {
                const firstRead = data.data.find(r => r.read_at);
                if (firstRead) {
                    const firstReadElement = document.getElementById('firstReadTime');
                    if (firstReadElement) {
                        const date = new Date(firstRead.read_at);
                        firstReadElement.textContent = date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                }
            }
        } else {
            throw new Error(data.message || 'Gagal memuat data');
        }
    } catch (error) {
        console.error('Error loading recipients:', error);
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
                <p class="text-red-500 mb-2">Gagal memuat data penerima</p>
                <button onclick="loadRecipients(${page})" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-redo mr-2"></i>Coba Lagi
                </button>
            </div>
        `;
    } finally {
        isLoading = false;
    }
}

function renderRecipients(recipients, replace = true) {
    const container = document.getElementById('recipientsContainer');
    
    if (recipients.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Tidak ada penerima ditemukan</p>
            </div>
        `;
        return;
    }
    
    const tableHTML = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Baca</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Konfirmasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ${recipients.map(recipient => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                        <span class="text-white text-sm font-medium">${recipient.user.name.charAt(0).toUpperCase()}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">${recipient.user.name}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-600">${recipient.user.email}</p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                ${recipient.read_at ? 
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700"><i class="fas fa-check mr-1"></i>Dibaca</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700"><i class="fas fa-minus mr-1"></i>Belum</span>'
                                }
                            </td>
                            <td class="px-4 py-4 text-center">
                                ${recipient.acknowledged_at ? 
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700"><i class="fas fa-check-double mr-1"></i>Dikonfirmasi</span>' :
                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700"><i class="fas fa-minus mr-1"></i>Belum</span>'
                                }
                            </td>
                            <td class="px-4 py-4">
                                ${recipient.acknowledged_at ? 
                                    `<p class="text-sm text-gray-900">${formatDate(recipient.acknowledged_at)}</p><p class="text-xs text-gray-500">Dikonfirmasi</p>` :
                                    recipient.read_at ? 
                                        `<p class="text-sm text-gray-900">${formatDate(recipient.read_at)}</p><p class="text-xs text-gray-500">Dibaca</p>` :
                                        '<p class="text-sm text-gray-400">-</p>'
                                }
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = tableHTML;
}

function renderPagination(pagination) {
    const container = document.getElementById('recipientsPagination');
    
    if (pagination.last_page <= 1) {
        container.classList.add('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    
    let paginationHTML = '<div class="flex items-center justify-between">';
    
    // Info
    paginationHTML += `
        <div class="text-sm text-gray-700">
            Menampilkan <span class="font-medium">${pagination.from}</span> sampai <span class="font-medium">${pagination.to}</span> 
            dari <span class="font-medium">${pagination.total}</span> penerima
        </div>
    `;
    
    // Pagination buttons
    paginationHTML += '<div class="flex items-center space-x-2">';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `
            <button onclick="loadRecipients(${pagination.current_page - 1})" 
                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Sebelumnya
            </button>
        `;
    }
    
    // Page numbers (show max 5 pages)
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, startPage + 4);
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            paginationHTML += `
                <span class="px-3 py-2 text-sm font-medium text-white bg-sky-600 border border-sky-600 rounded-md">
                    ${i}
                </span>
            `;
        } else {
            paginationHTML += `
                <button onclick="loadRecipients(${i})" 
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    ${i}
                </button>
            `;
        }
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHTML += `
            <button onclick="loadRecipients(${pagination.current_page + 1})" 
                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Selanjutnya
            </button>
        `;
    }
    
    paginationHTML += '</div></div>';
    
    container.innerHTML = paginationHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Atensi akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/atensi/${hashId}`;
            form.submit();
        }
    });
}
</script>
@endpush