@extends('perusahaan.layouts.app')

@section('title', 'Permintaan Cuti')
@section('page-title', 'Permintaan Cuti')
@section('page-subtitle', 'Kelola permintaan cuti karyawan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full sm:w-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Disetujui</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times text-red-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Ditolak</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['rejected'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('perusahaan.cuti.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
        <i class="fas fa-plus mr-2"></i>Tambah Permintaan Cuti
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Cuti</label>
            <select name="jenis_cuti" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Jenis</option>
                <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Melahirkan</option>
                <option value="menikah" {{ request('jenis_cuti') == 'menikah' ? 'selected' : '' }}>Menikah</option>
                <option value="khitan" {{ request('jenis_cuti') == 'khitan' ? 'selected' : '' }}>Khitan</option>
                <option value="baptis" {{ request('jenis_cuti') == 'baptis' ? 'selected' : '' }}>Baptis</option>
                <option value="keluarga_meninggal" {{ request('jenis_cuti') == 'keluarga_meninggal' ? 'selected' : '' }}>Keluarga Meninggal</option>
                <option value="lainnya" {{ request('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Karyawan</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NIK karyawan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Cuti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hari</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Cuti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Cuti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cutis as $cuti)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">{{ substr($cuti->karyawan->nama_lengkap, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $cuti->karyawan->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500">{{ $cuti->karyawan->nik_karyawan }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $cuti->project->nama }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $cuti->tanggal_mulai->format('d M Y') }}</div>
                        <div class="text-sm text-gray-500">s/d {{ $cuti->tanggal_selesai->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $cuti->total_hari }} hari
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cuti->jenis_cuti === 'tahunan' && $cuti->sisa_cuti_tahunan !== null)
                            <div class="text-sm">
                                <div class="text-gray-900 font-medium flex items-center">
                                    @if($cuti->sisa_cuti_tahunan <= 2)
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-1 text-xs" title="Sisa cuti hampir habis"></i>
                                    @elseif($cuti->sisa_cuti_tahunan <= 5)
                                        <i class="fas fa-exclamation-circle text-yellow-500 mr-1 text-xs" title="Sisa cuti terbatas"></i>
                                    @endif
                                    Sisa: 
                                    <span class="ml-1 {{ $cuti->sisa_cuti_tahunan <= 2 ? 'text-red-600 font-bold' : ($cuti->sisa_cuti_tahunan <= 5 ? 'text-yellow-600 font-semibold' : 'text-green-600') }}">
                                        {{ $cuti->sisa_cuti_tahunan }} hari
                                    </span>
                                </div>
                                <div class="text-gray-500 text-xs">
                                    Terpakai: {{ $cuti->cuti_terpakai }}/{{ $cuti->batas_cuti_tahunan }} hari
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {!! $cuti->jenis_cuti_badge !!}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {!! $cuti->status_badge !!}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('perusahaan.cuti.show', $cuti->hash_id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($cuti->canApprove())
                                <button onclick="openApproveModal('{{ $cuti->hash_id }}')" class="text-green-600 hover:text-green-900 transition-colors" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="openRejectModal('{{ $cuti->hash_id }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                            
                            @if($cuti->canEdit())
                                <a href="{{ route('perusahaan.cuti.edit', $cuti->hash_id) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            
                            @if($cuti->canDelete())
                                <button onclick="deleteConfirm('{{ $cuti->hash_id }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Belum ada data permintaan cuti</p>
                            <p class="text-gray-400 text-sm mb-6">Tambahkan permintaan cuti pertama untuk memulai</p>
                            <a href="{{ route('perusahaan.cuti.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Tambah Permintaan Cuti
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
@if($cutis->hasPages())
<div class="mt-6">
    {{ $cutis->links() }}
</div>
@endif

<!-- Modal Approve -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <form id="approveForm">
            @csrf
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Setujui Permintaan Cuti</h3>
                        <p class="text-sm text-gray-600">Apakah Anda yakin ingin menyetujui permintaan cuti ini?</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea 
                        name="catatan_approval" 
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Tambahkan catatan persetujuan..."
                    ></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('approveModal')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                        Setujui
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <form id="rejectForm">
            @csrf
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Tolak Permintaan Cuti</h3>
                        <p class="text-sm text-gray-600">Berikan alasan penolakan permintaan cuti</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea 
                        name="catatan_approval" 
                        rows="3"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        placeholder="Jelaskan alasan penolakan..."
                    ></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal('rejectModal')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        Tolak
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
let currentCutiId = null;

function openApproveModal(cutiId) {
    currentCutiId = cutiId;
    document.getElementById('approveModal').classList.remove('hidden');
}

function openRejectModal(cutiId) {
    currentCutiId = cutiId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    if (modalId === 'approveModal') {
        document.getElementById('approveForm').reset();
    } else if (modalId === 'rejectModal') {
        document.getElementById('rejectForm').reset();
    }
}

function deleteConfirm(cutiId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Permintaan cuti ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/cuti/${cutiId}`;
            form.submit();
        }
    });
}

// Handle approve form
document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/cuti/${currentCutiId}/approve`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        closeModal('approveModal');
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Update the table row instead of full page reload
                updateTableRowAfterApproval(currentCutiId, data.remaining_leave);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        closeModal('approveModal');
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses permintaan.',
            confirmButtonText: 'OK'
        });
    });
});

// Function to update table row after approval
function updateTableRowAfterApproval(cutiId, remainingLeave) {
    // Find the table row for this cuti
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const approveButton = row.querySelector(`button[onclick="openApproveModal('${cutiId}')"]`);
        if (approveButton) {
            // Update status badge
            const statusCell = row.cells[6]; // Status column (0-indexed)
            statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>';
            
            // Update remaining leave info if it's annual leave
            if (remainingLeave) {
                const sisaCutiCell = row.cells[4]; // Sisa Cuti column
                const sisaCuti = remainingLeave.sisa_cuti;
                const cutiTerpakai = remainingLeave.cuti_terpakai;
                const batasCuti = remainingLeave.batas_cuti;
                
                let colorClass = 'text-green-600';
                let iconHtml = '';
                
                if (sisaCuti <= 2) {
                    colorClass = 'text-red-600 font-bold';
                    iconHtml = '<i class="fas fa-exclamation-triangle text-red-500 mr-1 text-xs" title="Sisa cuti hampir habis"></i>';
                } else if (sisaCuti <= 5) {
                    colorClass = 'text-yellow-600 font-semibold';
                    iconHtml = '<i class="fas fa-exclamation-circle text-yellow-500 mr-1 text-xs" title="Sisa cuti terbatas"></i>';
                }
                
                sisaCutiCell.innerHTML = `
                    <div class="text-sm">
                        <div class="text-gray-900 font-medium flex items-center">
                            ${iconHtml}
                            Sisa: 
                            <span class="ml-1 ${colorClass}">
                                ${sisaCuti} hari
                            </span>
                        </div>
                        <div class="text-gray-500 text-xs">
                            Terpakai: ${cutiTerpakai}/${batasCuti} hari
                        </div>
                    </div>
                `;
            }
            
            // Remove approve/reject buttons
            const actionCell = row.cells[7]; // Action column
            const actionDiv = actionCell.querySelector('.flex.items-center.space-x-2');
            
            // Remove approve and reject buttons
            const approveBtn = actionDiv.querySelector(`button[onclick="openApproveModal('${cutiId}')"]`);
            const rejectBtn = actionDiv.querySelector(`button[onclick="openRejectModal('${cutiId}')"]`);
            
            if (approveBtn) approveBtn.remove();
            if (rejectBtn) rejectBtn.remove();
            
            return; // Exit loop once found
        }
    });
}

// Handle reject form
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/cuti/${currentCutiId}/reject`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        closeModal('rejectModal');
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Update the table row instead of full page reload
                updateTableRowAfterRejection(currentCutiId);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        closeModal('rejectModal');
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses permintaan.',
            confirmButtonText: 'OK'
        });
    });
});

// Function to update table row after rejection
function updateTableRowAfterRejection(cutiId) {
    // Find the table row for this cuti
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const approveButton = row.querySelector(`button[onclick="openApproveModal('${cutiId}')"]`);
        if (approveButton) {
            // Update status badge
            const statusCell = row.cells[6]; // Status column (0-indexed)
            statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>';
            
            // Remove approve/reject buttons
            const actionCell = row.cells[7]; // Action column
            const actionDiv = actionCell.querySelector('.flex.items-center.space-x-2');
            
            // Remove approve and reject buttons
            const approveBtn = actionDiv.querySelector(`button[onclick="openApproveModal('${cutiId}')"]`);
            const rejectBtn = actionDiv.querySelector(`button[onclick="openRejectModal('${cutiId}')"]`);
            
            if (approveBtn) approveBtn.remove();
            if (rejectBtn) rejectBtn.remove();
            
            return; // Exit loop once found
        }
    });
}

// Close modals when clicking outside
document.getElementById('approveModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('approveModal');
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('rejectModal');
});
</script>
@endpush