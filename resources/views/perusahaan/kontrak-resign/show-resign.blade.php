@extends('perusahaan.layouts.app')

@section('title', 'Detail Pengajuan Resign')
@section('page-title', 'Detail Pengajuan Resign')
@section('page-subtitle', 'Informasi lengkap pengajuan resign karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Actions -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('perusahaan.kontrak-resign.index', ['tab' => 'resign']) }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Resign
        </a>
        
        <div class="flex items-center space-x-3">
            @if($resign->canApprove())
                <button onclick="openApproveModal('{{ $resign->hash_id }}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-check mr-2"></i>Setujui
                </button>
                <button onclick="openRejectModal('{{ $resign->hash_id }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-times mr-2"></i>Tolak
                </button>
            @endif
            
            @if($resign->canEdit())
                <a href="{{ route('perusahaan.kontrak-resign.edit-resign', $resign->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            
            @if($resign->canDelete())
                <button onclick="deleteConfirm('{{ $resign->hash_id }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Resign -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Resign</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Pengajuan</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $resign->tanggal_pengajuan->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $resign->tanggal_pengajuan->format('l') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Resign Efektif</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $resign->tanggal_resign_efektif->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $resign->tanggal_resign_efektif->format('l') }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Masa Pemberitahuan</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $resign->calculateNoticePeriod() }} hari
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Resign</label>
                            <div class="flex items-center">
                                {!! $resign->jenis_resign_badge !!}
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Alasan Resign</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $resign->alasan_resign }}</p>
                        </div>
                    </div>

                    @if($resign->handover_notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Catatan Serah Terima</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $resign->handover_notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($resign->handover_items && count($resign->handover_items) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Item Serah Terima</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($resign->handover_items as $item)
                                    <li class="text-gray-900">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
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
                                <span class="text-xl font-bold text-blue-600">{{ substr($resign->karyawan->nama_lengkap, 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-900">{{ $resign->karyawan->nama_lengkap }}</h4>
                            <p class="text-gray-600 mb-3">{{ $resign->karyawan->nik_karyawan }}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Project:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ $resign->project->nama }}</span>
                                </div>
                                @if($resign->karyawan->jabatan)
                                <div>
                                    <span class="text-gray-500">Jabatan:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ $resign->karyawan->jabatan->nama }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-500">Tanggal Masuk:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ $resign->karyawan->tanggal_masuk->format('d M Y') }}</span>
                                </div>
                                @if($resign->karyawan->tanggal_keluar)
                                <div>
                                    <span class="text-gray-500">Tanggal Keluar:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ $resign->karyawan->tanggal_keluar->format('d M Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Approval (jika ada) -->
            @if($resign->catatan_approval)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($resign->status === 'approved')
                            Catatan Persetujuan
                        @else
                            Alasan Penolakan
                        @endif
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $resign->catatan_approval }}</p>
                    </div>
                    @if($resign->approvedBy)
                    <div class="mt-4 text-sm text-gray-600">
                        <span>Oleh: {{ $resign->approvedBy->name }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $resign->approved_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif

                    @if($resign->status === 'approved' && $resign->is_blacklist)
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="font-medium text-red-800">Karyawan dimasukkan ke blacklist</span>
                        </div>
                        @if($resign->blacklist_reason)
                        <p class="text-red-700 text-sm mt-2">{{ $resign->blacklist_reason }}</p>
                        @endif
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
                    <h3 class="text-lg font-semibold text-gray-900">Status Pengajuan</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        {!! $resign->status_badge !!}
                        
                        @if($resign->status === 'pending')
                        <p class="text-sm text-gray-600 mt-2">Menunggu persetujuan dari admin</p>
                        @elseif($resign->status === 'approved')
                        <p class="text-sm text-gray-600 mt-2">Pengajuan resign telah disetujui</p>
                        @else
                        <p class="text-sm text-gray-600 mt-2">Pengajuan resign ditolak</p>
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
                                <p class="text-sm font-medium text-gray-900">Pengajuan Dibuat</p>
                                <p class="text-xs text-gray-500">{{ $resign->created_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-gray-600">oleh {{ $resign->createdBy->name }}</p>
                            </div>
                        </div>

                        @if($resign->status !== 'pending')
                        <!-- Approved/Rejected -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 {{ $resign->status === 'approved' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                <i class="fas {{ $resign->status === 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600' }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $resign->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </p>
                                @if($resign->approved_at)
                                <p class="text-xs text-gray-500">{{ $resign->approved_at->format('d M Y H:i') }}</p>
                                @endif
                                @if($resign->approvedBy)
                                <p class="text-xs text-gray-600">oleh {{ $resign->approvedBy->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($resign->status === 'approved')
                        <!-- Effective Date -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-purple-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Resign Efektif</p>
                                <p class="text-xs text-gray-500">{{ $resign->tanggal_resign_efektif->format('d M Y') }}</p>
                                @if($resign->tanggal_resign_efektif->isPast())
                                <p class="text-xs text-gray-600">Karyawan sudah tidak aktif</p>
                                @else
                                <p class="text-xs text-gray-600">{{ $resign->tanggal_resign_efektif->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($resign->canApprove())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="openApproveModal('{{ $resign->hash_id }}')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center">
                        <i class="fas fa-check mr-2"></i>Setujui Resign
                    </button>
                    <button onclick="openRejectModal('{{ $resign->hash_id }}')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Tolak Resign
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Include the same modals from resign partial -->
@include('perusahaan.kontrak-resign.partials.resign-modals')

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
let currentResignId = '{{ $resign->hash_id }}';

function openApproveModal(resignId) {
    currentResignId = resignId;
    document.getElementById('approveModal').classList.remove('hidden');
}

function openRejectModal(resignId) {
    currentResignId = resignId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    if (modalId === 'approveModal') {
        document.getElementById('approveForm').reset();
        document.getElementById('blacklistReasonDiv').classList.add('hidden');
    } else if (modalId === 'rejectModal') {
        document.getElementById('rejectForm').reset();
    }
}

function deleteConfirm(resignId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Pengajuan resign ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/kontrak-resign/resign/${resignId}`;
            form.submit();
        }
    });
}

// Handle blacklist checkbox
document.querySelector('input[name="is_blacklist"]')?.addEventListener('change', function() {
    const blacklistDiv = document.getElementById('blacklistReasonDiv');
    if (this.checked) {
        blacklistDiv.classList.remove('hidden');
        blacklistDiv.querySelector('textarea').required = true;
    } else {
        blacklistDiv.classList.add('hidden');
        blacklistDiv.querySelector('textarea').required = false;
    }
});

// Handle approve form
document.getElementById('approveForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/kontrak-resign/resign/${currentResignId}/approve`, {
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
document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/kontrak-resign/resign/${currentResignId}/reject`, {
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