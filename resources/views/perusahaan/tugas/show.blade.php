@extends('perusahaan.layouts.app')

@section('title', 'Detail Tugas - ' . $tugas->judul)
@section('page-title', 'Detail Tugas')
@section('page-subtitle', $tugas->judul)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.tugas.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-sky-600">
                    <i class="fas fa-tasks mr-2"></i>
                    Tugas
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
            <!-- Tugas Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-{{ $tugas->prioritas_color }}-500">
                                    <i class="{{ $tugas->prioritas_icon }} text-white"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $tugas->judul }}</h1>
                                    <p class="text-sm text-gray-500">ID: {{ $tugas->hash_id }}</p>
                                </div>
                            </div>
                            
                            <!-- Badges -->
                            <div class="flex items-center gap-2 mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $tugas->prioritas_color }}-100 text-{{ $tugas->prioritas_color }}-700">
                                    <i class="{{ $tugas->prioritas_icon }} mr-2"></i>{{ $tugas->prioritas_label }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $tugas->status_color }}-100 text-{{ $tugas->status_color }}-700">
                                    {{ $tugas->status_label }}
                                </span>
                                @if($tugas->is_urgent)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Mendesak
                                    </span>
                                @endif
                                @if($tugas->is_overdue)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                        <i class="fas fa-clock mr-2"></i>Terlambat
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('perusahaan.tugas.edit', $tugas->hash_id) }}" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                            <button onclick="confirmDelete('{{ $tugas->hash_id }}')" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                                <i class="fas fa-trash mr-2"></i>Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Tugas</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed">{{ $tugas->deskripsi }}</p>
                        </div>
                    </div>

                    @if($tugas->detail_lokasi)
                    <!-- Location Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Detail Lokasi</h3>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-500 mr-3 mt-1"></i>
                                <p class="text-gray-700 leading-relaxed">{{ $tugas->detail_lokasi }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Project</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-project-diagram text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $tugas->project->nama }}</p>
                                        <p class="text-xs text-gray-500">Project</p>
                                    </div>
                                </div>
                                @if($tugas->area)
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $tugas->area->nama }}</p>
                                            <p class="text-xs text-gray-500">Area</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <i class="fas fa-users text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $tugas->target_type_label }}</p>
                                        <p class="text-xs text-gray-500">Target Penugasan</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Waktu</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $tugas->batas_pengerjaan->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">Batas Pengerjaan</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-hourglass-half text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        @if($tugas->days_remaining >= 0)
                                            <p class="text-sm font-medium text-green-600">{{ $tugas->days_remaining }} hari lagi</p>
                                            <p class="text-xs text-gray-500">Sisa Waktu</p>
                                        @else
                                            <p class="text-sm font-medium text-red-600">{{ abs($tugas->days_remaining) }} hari terlambat</p>
                                            <p class="text-xs text-gray-500">Keterlambatan</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $tugas->creator->name }}</p>
                                        <p class="text-xs text-gray-500">Dibuat oleh</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-3 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $tugas->created_at->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Waktu Dibuat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Penugasan</h3>
                        <span class="text-sm text-gray-500" id="assignmentCount">{{ $assignmentStats['total'] }} orang</span>
                    </div>

                    <!-- Search and Filter -->
                    <div class="mb-4 flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="assignmentSearch"
                                placeholder="Cari nama atau email..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm"
                            >
                        </div>
                        <select 
                            id="assignmentStatusFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm"
                        >
                            <option value="">Semua Status</option>
                            <option value="assigned">Ditugaskan</option>
                            <option value="in_progress">Sedang Dikerjakan</option>
                            <option value="completed">Selesai</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>

                    <!-- Assignments Table Container -->
                    <div id="assignmentsContainer">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
                            <span class="ml-3 text-gray-600">Memuat data penugasan...</span>
                        </div>
                    </div>

                    <!-- Pagination Container -->
                    <div id="assignmentsPagination" class="mt-4 hidden"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Progress Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress</h3>
                
                <!-- Completion Progress -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Selesai</span>
                        <span class="text-sm font-bold text-green-600">{{ $assignmentStats['total'] > 0 ? round(($assignmentStats['completed'] / $assignmentStats['total']) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: {{ $assignmentStats['total'] > 0 ? round(($assignmentStats['completed'] / $assignmentStats['total']) * 100, 1) : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $assignmentStats['completed'] }} dari {{ $assignmentStats['total'] }} orang</p>
                </div>

                <!-- In Progress -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Sedang Dikerjakan</span>
                        <span class="text-sm font-bold text-blue-600">{{ $assignmentStats['total'] > 0 ? round(($assignmentStats['in_progress'] / $assignmentStats['total']) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-300" style="width: {{ $assignmentStats['total'] > 0 ? round(($assignmentStats['in_progress'] / $assignmentStats['total']) * 100, 1) : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $assignmentStats['in_progress'] }} dari {{ $assignmentStats['total'] }} orang</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-users text-blue-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Total Penugasan</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $assignmentStats['total'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Selesai</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $assignmentStats['completed'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-spinner text-blue-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Sedang Dikerjakan</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $assignmentStats['in_progress'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-user-clock text-yellow-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Ditugaskan</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $assignmentStats['assigned'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 mr-3"></i>
                            <span class="text-sm text-gray-700">Ditolak</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $assignmentStats['rejected'] }}</span>
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
                            <p class="text-sm font-medium text-gray-900">Tugas Dibuat</p>
                            <p class="text-xs text-gray-500">{{ $tugas->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($tugas->published_at)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-paper-plane text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Ditugaskan</p>
                            <p class="text-xs text-gray-500">{{ $tugas->published_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($assignmentStats['in_progress'] > 0)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-play text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Mulai Dikerjakan</p>
                            <p class="text-xs text-gray-500" id="firstStartTime">Memuat...</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($assignmentStats['completed'] > 0)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Pertama Selesai</p>
                            <p class="text-xs text-gray-500" id="firstCompletedTime">Memuat...</p>
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
    loadAssignments();
    
    // Search functionality with debounce
    document.getElementById('assignmentSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadAssignments();
        }, 500);
    });
    
    // Status filter
    document.getElementById('assignmentStatusFilter').addEventListener('change', function() {
        currentPage = 1;
        loadAssignments();
    });
});

async function loadAssignments(page = 1) {
    if (isLoading) return;
    
    isLoading = true;
    const container = document.getElementById('assignmentsContainer');
    const pagination = document.getElementById('assignmentsPagination');
    
    // Show loading state
    if (page === 1) {
        container.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
                <span class="ml-3 text-gray-600">Memuat data penugasan...</span>
            </div>
        `;
    }
    
    try {
        const search = document.getElementById('assignmentSearch').value;
        const status = document.getElementById('assignmentStatusFilter').value;
        
        const params = new URLSearchParams({
            page: page,
            per_page: 20,
            search: search,
            status: status
        });
        
        const response = await fetch(`{{ route('perusahaan.tugas.assignments', $tugas->hash_id) }}?${params}`);
        const data = await response.json();
        
        if (data.success) {
            renderAssignments(data.data, page === 1);
            renderPagination(data.pagination);
            
            // Update timeline times if available
            if (data.data.length > 0) {
                const firstInProgress = data.data.find(a => a.started_at);
                if (firstInProgress) {
                    const firstStartElement = document.getElementById('firstStartTime');
                    if (firstStartElement) {
                        const date = new Date(firstInProgress.started_at);
                        firstStartElement.textContent = date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                }
                
                const firstCompleted = data.data.find(a => a.completed_at);
                if (firstCompleted) {
                    const firstCompletedElement = document.getElementById('firstCompletedTime');
                    if (firstCompletedElement) {
                        const date = new Date(firstCompleted.completed_at);
                        firstCompletedElement.textContent = date.toLocaleDateString('id-ID', {
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
        console.error('Error loading assignments:', error);
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
                <p class="text-red-500 mb-2">Gagal memuat data penugasan</p>
                <button onclick="loadAssignments(${page})" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-redo mr-2"></i>Coba Lagi
                </button>
            </div>
        `;
    } finally {
        isLoading = false;
    }
}

function renderAssignments(assignments, replace = true) {
    const container = document.getElementById('assignmentsContainer');
    
    if (assignments.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Tidak ada penugasan ditemukan</p>
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
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ${assignments.map(assignment => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                        <span class="text-white text-sm font-medium">${assignment.user.name.charAt(0).toUpperCase()}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">${assignment.user.name}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-600">${assignment.user.email}</p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                ${getStatusBadge(assignment.status)}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: ${assignment.progress_percentage || 0}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">${assignment.progress_percentage || 0}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                ${getTimeInfo(assignment)}
                            </td>
                            <td class="px-4 py-4">
                                ${assignment.notes ? 
                                    `<p class="text-sm text-gray-600">${assignment.notes.substring(0, 50)}${assignment.notes.length > 50 ? '...' : ''}</p>` :
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

function getStatusBadge(status) {
    const statusConfig = {
        'assigned': { color: 'blue', icon: 'fas fa-user-clock', label: 'Ditugaskan' },
        'in_progress': { color: 'yellow', icon: 'fas fa-spinner', label: 'Dikerjakan' },
        'completed': { color: 'green', icon: 'fas fa-check-circle', label: 'Selesai' },
        'rejected': { color: 'red', icon: 'fas fa-times-circle', label: 'Ditolak' }
    };
    
    const config = statusConfig[status] || { color: 'gray', icon: 'fas fa-info', label: status };
    
    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${config.color}-100 text-${config.color}-700">
        <i class="${config.icon} mr-1"></i>${config.label}
    </span>`;
}

function getTimeInfo(assignment) {
    if (assignment.completed_at) {
        return `<p class="text-sm text-gray-900">${formatDate(assignment.completed_at)}</p><p class="text-xs text-gray-500">Selesai</p>`;
    } else if (assignment.started_at) {
        return `<p class="text-sm text-gray-900">${formatDate(assignment.started_at)}</p><p class="text-xs text-gray-500">Dimulai</p>`;
    } else {
        return `<p class="text-sm text-gray-900">${formatDate(assignment.created_at)}</p><p class="text-xs text-gray-500">Ditugaskan</p>`;
    }
}

function renderPagination(pagination) {
    const container = document.getElementById('assignmentsPagination');
    
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
            dari <span class="font-medium">${pagination.total}</span> penugasan
        </div>
    `;
    
    // Pagination buttons
    paginationHTML += '<div class="flex items-center space-x-2">';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `
            <button onclick="loadAssignments(${pagination.current_page - 1})" 
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
                <button onclick="loadAssignments(${i})" 
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    ${i}
                </button>
            `;
        }
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHTML += `
            <button onclick="loadAssignments(${pagination.current_page + 1})" 
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
        text: "Tugas akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/tugas/${hashId}`;
            form.submit();
        }
    });
}
</script>
@endpush