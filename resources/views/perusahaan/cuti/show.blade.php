@extends('perusahaan.layouts.app')

@section('title', 'Detail Permintaan Cuti')
@section('page-title', 'Detail Permintaan Cuti')
@section('page-subtitle', 'Informasi lengkap permintaan cuti karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Actions -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('perusahaan.cuti.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Cuti
        </a>
        
        <div class="flex items-center space-x-3">
            @if($cuti->canApprove())
                <button onclick="openApproveModal('{{ $cuti->hash_id }}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-check mr-2"></i>Setujui
                </button>
                <button onclick="openRejectModal('{{ $cuti->hash_id }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-times mr-2"></i>Tolak
                </button>
            @endif
            
            @if($cuti->canEdit())
                <a href="{{ route('perusahaan.cuti.edit', $cuti->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            
            @if($cuti->canDelete())
                <button onclick="deleteConfirm('{{ $cuti->hash_id }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Cuti -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Cuti</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $cuti->tanggal_mulai->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $cuti->tanggal_mulai->format('l') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $cuti->tanggal_selesai->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $cuti->tanggal_selesai->format('l') }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Hari</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $cuti->total_hari }} hari
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Cuti</label>
                            <div class="flex items-center">
                                {!! $cuti->jenis_cuti_badge !!}
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Alasan Cuti</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $cuti->alasan }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Karyawan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Karyawan</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-blue-600">{{ substr($cuti->karyawan->nama_lengkap, 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-900">{{ $cuti->karyawan->nama_lengkap }}</h4>
                            <p class="text-gray-600 mb-3">{{ $cuti->karyawan->nik_karyawan }}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Project:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ $cuti->project->nama }}</span>
                                </div>
                                @if($cuti->karyawan->jabatan)
                                <div>
                                    <span class="text-gray-500">Jabatan:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ $cuti->karyawan->jabatan->nama }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Approval (jika ada) -->
            @if($cuti->catatan_approval)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($cuti->status === 'approved')
                            Catatan Persetujuan
                        @else
                            Alasan Penolakan
                        @endif
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $cuti->catatan_approval }}</p>
                    </div>
                    @if($cuti->approvedBy)
                    <div class="mt-4 text-sm text-gray-600">
                        <span>Oleh: {{ $cuti->approvedBy->name }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $cuti->approved_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Status Permintaan</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        {!! $cuti->status_badge !!}
                        
                        @if($cuti->status === 'pending')
                        <p class="text-sm text-gray-600 mt-2">Menunggu persetujuan dari admin</p>
                        @elseif($cuti->status === 'approved')
                        <p class="text-sm text-gray-600 mt-2">Permintaan cuti telah disetujui</p>
                        @else
                        <p class="text-sm text-gray-600 mt-2">Permintaan cuti ditolak</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Permintaan Dibuat</p>
                                <p class="text-xs text-gray-500">{{ $cuti->created_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-gray-600">oleh {{ $cuti->createdBy->name }}</p>
                            </div>
                        </div>

                        @if($cuti->status !== 'pending')
                        <!-- Approved/Rejected -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 {{ $cuti->status === 'approved' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                <i class="fas {{ $cuti->status === 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600' }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $cuti->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </p>
                                @if($cuti->approved_at)
                                <p class="text-xs text-gray-500">{{ $cuti->approved_at->format('d M Y H:i') }}</p>
                                @endif
                                @if($cuti->approvedBy)
                                <p class="text-xs text-gray-600">oleh {{ $cuti->approvedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($cuti->canApprove())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="openApproveModal('{{ $cuti->hash_id }}')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center">
                        <i class="fas fa-check mr-2"></i>Setujui Cuti
                    </button>
                    <button onclick="openRejectModal('{{ $cuti->hash_id }}')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Tolak Cuti
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

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
let currentCutiId = '{{ $cuti->hash_id }}';

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
                location.reload();
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
                location.reload();
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

// Close modals when clicking outside
document.getElementById('approveModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('approveModal');
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('rejectModal');
});
</script>
@endpush