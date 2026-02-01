@extends('perusahaan.layouts.app')

@section('title', 'Pengajuan Reimbursement')
@section('page-title', 'Pengajuan Reimbursement')
@section('page-subtitle', 'Kelola pengajuan reimbursement karyawan')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-end">
        <a href="{{ route('perusahaan.keuangan.reimbursement.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i>
            Buat Pengajuan Baru
        </a>
    </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_pengajuan']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Disetujui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['approved']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Amount</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Urgent</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['urgent']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('perusahaan.keuangan.reimbursement.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i>
                            Pencarian
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nomor, judul, atau nama karyawan..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-filter mr-1"></i>
                            Status
                        </label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            @foreach(\App\Models\Reimbursement::getAvailableStatus() as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-1"></i>
                            Kategori
                        </label>
                        <select name="kategori" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach(\App\Models\Reimbursement::getAvailableKategori() as $key => $label)
                                <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Project Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-project-diagram mr-1"></i>
                            Project
                        </label>
                        <select name="project_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('perusahaan.keuangan.reimbursement.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reimbursement List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($reimbursements->count() > 0)
                <!-- Bulk Actions Bar -->
                <div id="bulk-actions" class="hidden bg-blue-50 border-b border-blue-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-blue-900">
                                <span id="selected-count">0</span> item dipilih
                            </span>
                            <button type="button" onclick="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800">
                                Batal Pilih
                            </button>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button type="button" onclick="bulkApprove()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>
                                Setujui Terpilih
                            </button>
                            <button type="button" onclick="bulkReject()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Tolak Terpilih
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    @php
                        $groupedReimbursements = $reimbursements->groupBy(function($item) {
                            return $item->karyawan->nama_lengkap ?? 'Unknown';
                        });
                    @endphp
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($groupedReimbursements as $karyawanName => $karyawanReimbursements)
                            @php
                                $totalPengajuan = $karyawanReimbursements->sum('jumlah_pengajuan');
                                $totalDisetujui = $karyawanReimbursements->sum('jumlah_disetujui');
                                $countByStatus = $karyawanReimbursements->groupBy('status')->map->count();
                                $firstReimbursement = $karyawanReimbursements->first();
                                $pendingCount = $karyawanReimbursements->where('status', 'submitted')->count();
                            @endphp
                            
                            <!-- Employee Summary Row -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400">
                                <div class="px-6 py-4 cursor-pointer hover:bg-blue-100 transition-colors" 
                                     onclick="toggleGroup('group-{{ $loop->index }}')">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- Toggle Icon -->
                                            <div class="flex-shrink-0">
                                                <i id="icon-group-{{ $loop->index }}" class="fas fa-chevron-right text-blue-600 transition-transform duration-200"></i>
                                            </div>
                                            
                                            <!-- Employee Info -->
                                            <div>
                                                <h3 class="text-lg font-bold text-blue-900">{{ $karyawanName }}</h3>
                                                <p class="text-sm text-blue-700">{{ $firstReimbursement->project->nama ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Summary Stats -->
                                        <div class="flex items-center space-x-6">
                                            <!-- Total Pengajuan -->
                                            <div class="text-center">
                                                <div class="text-sm text-gray-600">Total Pengajuan</div>
                                                <div class="font-bold text-gray-900">{{ $karyawanReimbursements->count() }}</div>
                                            </div>
                                            
                                            <!-- Total Amount -->
                                            <div class="text-center">
                                                <div class="text-sm text-gray-600">Total Jumlah</div>
                                                <div class="font-bold text-gray-900">Rp {{ number_format($totalPengajuan, 0, ',', '.') }}</div>
                                                @if($totalDisetujui > 0)
                                                    <div class="text-xs text-green-600">Disetujui: Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</div>
                                                @endif
                                            </div>
                                            
                                            <!-- Status Summary -->
                                            <div class="flex space-x-2">
                                                @if($countByStatus->get('draft', 0) > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $countByStatus->get('draft') }} Draft
                                                    </span>
                                                @endif
                                                @if($countByStatus->get('submitted', 0) > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ $countByStatus->get('submitted') }} Pending
                                                    </span>
                                                @endif
                                                @if($countByStatus->get('approved', 0) > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $countByStatus->get('approved') }} Approved
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Quick Group Actions -->
                                            @if($pendingCount > 0)
                                                <div class="flex space-x-2" onclick="event.stopPropagation()">
                                                    <button type="button" onclick="approveGroup('{{ $loop->index }}')" 
                                                            class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Setujui {{ $pendingCount }}
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Collapsible Detail Table -->
                                <div id="group-{{ $loop->index }}" class="hidden">
                                    <div class="bg-white">
                                        <table class="min-w-full">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 select-all-group" 
                                                               onchange="toggleAllInGroup('{{ $loop->index }}')">
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengajuan</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($karyawanReimbursements as $reimbursement)
                                                <tr class="hover:bg-gray-50 {{ in_array($reimbursement->status, ['submitted']) ? 'bg-yellow-50' : '' }}" 
                                                    data-group="{{ $loop->parent->index }}" data-id="{{ $reimbursement->hash_id }}">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if(in_array($reimbursement->status, ['submitted']))
                                                            <input type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 item-checkbox" 
                                                                   value="{{ $reimbursement->hash_id }}" onchange="updateBulkActions()">
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reimbursement->nomor_reimbursement }}</div>
                                                            <div class="text-sm text-gray-500">{{ Str::limit($reimbursement->judul_pengajuan, 40) }}</div>
                                                            @if($reimbursement->is_urgent)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                    Urgent
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $reimbursement->kategori_label }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-medium text-gray-900">Rp {{ number_format($reimbursement->jumlah_pengajuan, 0, ',', '.') }}</div>
                                                        @if($reimbursement->jumlah_disetujui && $reimbursement->jumlah_disetujui != $reimbursement->jumlah_pengajuan)
                                                            <div class="text-sm text-green-600">Disetujui: Rp {{ number_format($reimbursement->jumlah_disetujui, 0, ',', '.') }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $reimbursement->getStatusBadgeClass() }}">
                                                            {{ $reimbursement->status_label }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $reimbursement->getPrioritasBadgeClass() }}">
                                                            {{ $reimbursement->prioritas_label }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $reimbursement->tanggal_pengajuan->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <div class="flex items-center justify-end space-x-2">
                                                            <a href="{{ route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id) }}" 
                                                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            
                                                            @if($reimbursement->status === 'submitted')
                                                                <button type="button" onclick="approveReimbursement('{{ $reimbursement->hash_id }}', '{{ $reimbursement->nomor_reimbursement }}')" 
                                                                        class="text-green-600 hover:text-green-900 transition-colors" title="Setujui">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button type="button" onclick="rejectReimbursement('{{ $reimbursement->hash_id }}', '{{ $reimbursement->nomor_reimbursement }}')" 
                                                                        class="text-red-600 hover:text-red-900 transition-colors" title="Tolak">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                            
                                                            @if($reimbursement->canBeEdited())
                                                                <a href="{{ route('perusahaan.keuangan.reimbursement.edit', $reimbursement->hash_id) }}" 
                                                                   class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endif
                                                            @if(in_array($reimbursement->status, ['draft', 'cancelled']))
                                                                <button onclick="deleteReimbursement('{{ $reimbursement->hash_id }}', '{{ $reimbursement->nomor_reimbursement }}')" 
                                                                        class="text-red-600 hover:text-red-900 transition-colors">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reimbursements->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-invoice text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengajuan reimbursement</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan membuat pengajuan reimbursement pertama Anda.</p>
                    <a href="{{ route('perusahaan.keuangan.reimbursement.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Pengajuan Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteReimbursement(hashId, nomorReimbursement) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: `Reimbursement ${nomorReimbursement} akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('perusahaan.keuangan.reimbursement.index') }}/${hashId}`;
            form.submit();
        }
    });
}

function toggleGroup(groupId) {
    const group = document.getElementById(groupId);
    const icon = document.getElementById('icon-' + groupId);
    
    if (group.classList.contains('hidden')) {
        // Show group
        group.classList.remove('hidden');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
        icon.style.transform = 'rotate(90deg)';
    } else {
        // Hide group
        group.classList.add('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Bulk Actions Functions
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkboxes.length > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.classList.add('hidden');
    }
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateBulkActions();
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.item-checkbox, .select-all-group');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}

function toggleAllInGroup(groupIndex) {
    const selectAllCheckbox = event.target;
    const itemCheckboxes = document.querySelectorAll(`tr[data-group="${groupIndex}"] .item-checkbox`);
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateBulkActions();
}

// Individual Actions
function approveReimbursement(hashId, nomorReimbursement) {
    // First, get available rekening and categories
    const approvalDataUrl = '{{ route("perusahaan.keuangan.reimbursement.approval-data") }}';
    console.log('Fetching approval data from:', approvalDataUrl);
    
    fetch(approvalDataUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (!data.success) {
                Swal.fire('Error', data.message || 'Gagal memuat data approval', 'error');
                return;
            }
            
            if (!data.rekening || data.rekening.length === 0) {
                Swal.fire('Error', 'Tidak ada rekening aktif yang tersedia. Silakan tambahkan rekening terlebih dahulu.', 'error');
                return;
            }
            
            // Build rekening options
            let rekeningOptions = '<option value="">Pilih Rekening</option>';
            data.rekening.forEach(rek => {
                rekeningOptions += `<option value="${rek.id}">${rek.nama_rekening} - ${rek.nomor_rekening} (Rp ${rek.formatted_saldo})</option>`;
            });
            
            // Build kategori options
            let kategoriOptions = '<option value="">Pilih Kategori</option>';
            Object.entries(data.kategori).forEach(([key, label]) => {
                kategoriOptions += `<option value="${key}">${label}</option>`;
            });
            
            Swal.fire({
                title: 'Setujui Reimbursement',
                html: `
                    <div class="text-left space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Reimbursement</label>
                            <input type="text" value="${nomorReimbursement}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rekening Pembayaran <span class="text-red-500">*</span></label>
                            <select id="rekening_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                ${rekeningOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Transaksi <span class="text-red-500">*</span></label>
                            <select id="kategori_transaksi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                ${kategoriOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Persetujuan</label>
                            <textarea id="catatan_approver" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Catatan persetujuan (opsional)..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal',
                width: '500px',
                preConfirm: () => {
                    const rekeningId = document.getElementById('rekening_id').value;
                    const kategoriTransaksi = document.getElementById('kategori_transaksi').value;
                    const catatanApprover = document.getElementById('catatan_approver').value;
                    
                    if (!rekeningId) {
                        Swal.showValidationMessage('Rekening pembayaran harus dipilih');
                        return false;
                    }
                    
                    if (!kategoriTransaksi) {
                        Swal.showValidationMessage('Kategori transaksi harus dipilih');
                        return false;
                    }
                    
                    return {
                        rekening_id: rekeningId,
                        kategori_transaksi: kategoriTransaksi,
                        catatan_approver: catatanApprover
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('perusahaan/keuangan/reimbursement') }}/${hashId}/approve`;
                    
                    // CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Rekening ID
                    const rekeningInput = document.createElement('input');
                    rekeningInput.type = 'hidden';
                    rekeningInput.name = 'rekening_id';
                    rekeningInput.value = result.value.rekening_id;
                    form.appendChild(rekeningInput);
                    
                    // Kategori Transaksi
                    const kategoriInput = document.createElement('input');
                    kategoriInput.type = 'hidden';
                    kategoriInput.name = 'kategori_transaksi';
                    kategoriInput.value = result.value.kategori_transaksi;
                    form.appendChild(kategoriInput);
                    
                    // Catatan
                    if (result.value.catatan_approver) {
                        const catatan = document.createElement('input');
                        catatan.type = 'hidden';
                        catatan.name = 'catatan_approver';
                        catatan.value = result.value.catatan_approver;
                        form.appendChild(catatan);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire('Error', `Terjadi kesalahan saat memuat data: ${error.message}`, 'error');
        });
}

function rejectReimbursement(hashId, nomorReimbursement) {
    Swal.fire({
        title: 'Tolak Reimbursement?',
        text: `Apakah Anda yakin ingin menolak ${nomorReimbursement}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Alasan penolakan (wajib)...',
        inputAttributes: {
            'aria-label': 'Alasan penolakan'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan penolakan harus diisi!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('perusahaan/keuangan/reimbursement') }}/${hashId}/reject`;
            
            // CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Alasan penolakan
            const alasan = document.createElement('input');
            alasan.type = 'hidden';
            alasan.name = 'alasan_penolakan';
            alasan.value = result.value;
            form.appendChild(alasan);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Bulk Actions
function bulkApprove() {
    const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        Swal.fire('Peringatan', 'Pilih minimal satu reimbursement untuk disetujui', 'warning');
        return;
    }
    
    // First, get available rekening and categories
    const approvalDataUrl = '{{ route("perusahaan.keuangan.reimbursement.approval-data") }}';
    console.log('Bulk approve - Fetching approval data from:', approvalDataUrl);
    
    fetch(approvalDataUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Bulk approve - Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Bulk approve - Response data:', data);
            
            if (!data.success) {
                Swal.fire('Error', data.message || 'Gagal memuat data approval', 'error');
                return;
            }
            
            if (!data.rekening || data.rekening.length === 0) {
                Swal.fire('Error', 'Tidak ada rekening aktif yang tersedia. Silakan tambahkan rekening terlebih dahulu.', 'error');
                return;
            }
            
            // Build rekening options
            let rekeningOptions = '<option value="">Pilih Rekening</option>';
            data.rekening.forEach(rek => {
                rekeningOptions += `<option value="${rek.id}">${rek.nama_rekening} - ${rek.nomor_rekening} (Rp ${rek.formatted_saldo})</option>`;
            });
            
            // Build kategori options
            let kategoriOptions = '<option value="">Pilih Kategori</option>';
            Object.entries(data.kategori).forEach(([key, label]) => {
                kategoriOptions += `<option value="${key}">${label}</option>`;
            });
            
            Swal.fire({
                title: 'Setujui Reimbursement Terpilih',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-blue-800">Akan menyetujui <strong>${selectedIds.length} reimbursement</strong></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rekening Pembayaran <span class="text-red-500">*</span></label>
                            <select id="bulk_rekening_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                ${rekeningOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Transaksi <span class="text-red-500">*</span></label>
                            <select id="bulk_kategori_transaksi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                ${kategoriOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Persetujuan</label>
                            <textarea id="bulk_catatan_approver" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Catatan persetujuan (opsional)..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Setujui Semua!',
                cancelButtonText: 'Batal',
                width: '500px',
                preConfirm: () => {
                    const rekeningId = document.getElementById('bulk_rekening_id').value;
                    const kategoriTransaksi = document.getElementById('bulk_kategori_transaksi').value;
                    const catatanApprover = document.getElementById('bulk_catatan_approver').value;
                    
                    if (!rekeningId) {
                        Swal.showValidationMessage('Rekening pembayaran harus dipilih');
                        return false;
                    }
                    
                    if (!kategoriTransaksi) {
                        Swal.showValidationMessage('Kategori transaksi harus dipilih');
                        return false;
                    }
                    
                    return {
                        rekening_id: rekeningId,
                        kategori_transaksi: kategoriTransaksi,
                        catatan_approver: catatanApprover
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("perusahaan.keuangan.reimbursement.bulk-approve") }}';
                    
                    // CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Selected IDs
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'reimbursement_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    // Rekening ID
                    const rekeningInput = document.createElement('input');
                    rekeningInput.type = 'hidden';
                    rekeningInput.name = 'rekening_id';
                    rekeningInput.value = result.value.rekening_id;
                    form.appendChild(rekeningInput);
                    
                    // Kategori Transaksi
                    const kategoriInput = document.createElement('input');
                    kategoriInput.type = 'hidden';
                    kategoriInput.name = 'kategori_transaksi';
                    kategoriInput.value = result.value.kategori_transaksi;
                    form.appendChild(kategoriInput);
                    
                    // Catatan
                    if (result.value.catatan_approver) {
                        const catatan = document.createElement('input');
                        catatan.type = 'hidden';
                        catatan.name = 'catatan_approver';
                        catatan.value = result.value.catatan_approver;
                        form.appendChild(catatan);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        })
        .catch(error => {
            console.error('Bulk approve - Fetch error:', error);
            Swal.fire('Error', `Terjadi kesalahan saat memuat data: ${error.message}`, 'error');
        });
}

function bulkReject() {
    const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        Swal.fire('Peringatan', 'Pilih minimal satu reimbursement untuk ditolak', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Tolak Reimbursement Terpilih?',
        text: `Apakah Anda yakin ingin menolak ${selectedIds.length} reimbursement?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tolak Semua!',
        cancelButtonText: 'Batal',
        input: 'textarea',
        inputPlaceholder: 'Alasan penolakan (wajib)...',
        inputAttributes: {
            'aria-label': 'Alasan penolakan'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan penolakan harus diisi!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.keuangan.reimbursement.bulk-reject") }}';
            
            // CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Selected IDs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'reimbursement_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            // Alasan penolakan
            const alasan = document.createElement('input');
            alasan.type = 'hidden';
            alasan.name = 'alasan_penolakan';
            alasan.value = result.value;
            form.appendChild(alasan);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function approveGroup(groupIndex) {
    const pendingCheckboxes = document.querySelectorAll(`tr[data-group="${groupIndex}"] .item-checkbox`);
    const pendingIds = Array.from(pendingCheckboxes).map(cb => cb.value);
    
    if (pendingIds.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada reimbursement yang bisa disetujui di grup ini', 'warning');
        return;
    }
    
    // First, get available rekening and categories
    const approvalDataUrl = '{{ route("perusahaan.keuangan.reimbursement.approval-data") }}';
    console.log('Group approve - Fetching approval data from:', approvalDataUrl);
    
    fetch(approvalDataUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Group approve - Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Group approve - Response data:', data);
            
            if (!data.success) {
                Swal.fire('Error', data.message || 'Gagal memuat data approval', 'error');
                return;
            }
            
            if (!data.rekening || data.rekening.length === 0) {
                Swal.fire('Error', 'Tidak ada rekening aktif yang tersedia. Silakan tambahkan rekening terlebih dahulu.', 'error');
                return;
            }
            
            // Build rekening options
            let rekeningOptions = '<option value="">Pilih Rekening</option>';
            data.rekening.forEach(rek => {
                rekeningOptions += `<option value="${rek.id}">${rek.nama_rekening} - ${rek.nomor_rekening} (Rp ${rek.formatted_saldo})</option>`;
            });
            
            // Build kategori options
            let kategoriOptions = '<option value="">Pilih Kategori</option>';
            Object.entries(data.kategori).forEach(([key, label]) => {
                kategoriOptions += `<option value="${key}">${label}</option>`;
            });
            
            Swal.fire({
                title: 'Setujui Semua Reimbursement Karyawan',
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-blue-800">Akan menyetujui <strong>${pendingIds.length} reimbursement</strong> dari karyawan ini</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rekening Pembayaran <span class="text-red-500">*</span></label>
                            <select id="group_rekening_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                ${rekeningOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Transaksi <span class="text-red-500">*</span></label>
                            <select id="group_kategori_transaksi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                ${kategoriOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Persetujuan</label>
                            <textarea id="group_catatan_approver" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Catatan persetujuan (opsional)..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Setujui Semua!',
                cancelButtonText: 'Batal',
                width: '500px',
                preConfirm: () => {
                    const rekeningId = document.getElementById('group_rekening_id').value;
                    const kategoriTransaksi = document.getElementById('group_kategori_transaksi').value;
                    const catatanApprover = document.getElementById('group_catatan_approver').value;
                    
                    if (!rekeningId) {
                        Swal.showValidationMessage('Rekening pembayaran harus dipilih');
                        return false;
                    }
                    
                    if (!kategoriTransaksi) {
                        Swal.showValidationMessage('Kategori transaksi harus dipilih');
                        return false;
                    }
                    
                    return {
                        rekening_id: rekeningId,
                        kategori_transaksi: kategoriTransaksi,
                        catatan_approver: catatanApprover
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("perusahaan.keuangan.reimbursement.bulk-approve") }}';
                    
                    // CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Selected IDs
                    pendingIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'reimbursement_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    // Rekening ID
                    const rekeningInput = document.createElement('input');
                    rekeningInput.type = 'hidden';
                    rekeningInput.name = 'rekening_id';
                    rekeningInput.value = result.value.rekening_id;
                    form.appendChild(rekeningInput);
                    
                    // Kategori Transaksi
                    const kategoriInput = document.createElement('input');
                    kategoriInput.type = 'hidden';
                    kategoriInput.name = 'kategori_transaksi';
                    kategoriInput.value = result.value.kategori_transaksi;
                    form.appendChild(kategoriInput);
                    
                    // Catatan
                    if (result.value.catatan_approver) {
                        const catatan = document.createElement('input');
                        catatan.type = 'hidden';
                        catatan.name = 'catatan_approver';
                        catatan.value = result.value.catatan_approver;
                        form.appendChild(catatan);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        })
        .catch(error => {
            console.error('Group approve - Fetch error:', error);
            Swal.fire('Error', `Terjadi kesalahan saat memuat data: ${error.message}`, 'error');
        });
}

// Auto-expand first group on page load
document.addEventListener('DOMContentLoaded', function() {
    const firstGroup = document.querySelector('[id^="group-"]');
    if (firstGroup) {
        toggleGroup(firstGroup.id);
    }
});
</script>
@endpush
@endsection