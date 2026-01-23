@extends('perusahaan.layouts.app')

@section('title', 'Detail Permintaan Lembur')
@section('page-title', 'Detail Permintaan Lembur')
@section('page-subtitle', 'Informasi lengkap permintaan lembur karyawan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Detail Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $lembur->karyawan->nama_lengkap }}</h2>
                        <p class="text-sm text-gray-600">NIK: {{ $lembur->karyawan->nik_karyawan }}</p>
                        <p class="text-sm text-gray-600">{{ $lembur->project->nama }}</p>
                    </div>
                </div>
                <div class="text-right">
                    @if($lembur->status == 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>
                            Menunggu Persetujuan
                        </span>
                    @elseif($lembur->status == 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Disetujui
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>
                            Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Detail Waktu Lembur -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Tanggal Lembur</p>
                    <p class="text-lg font-bold text-blue-600">{{ $lembur->tanggal_lembur->format('d M Y') }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Jam Mulai</p>
                    <p class="text-lg font-bold text-green-600">{{ $lembur->jam_mulai->format('H:i') }}</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Jam Selesai</p>
                    <p class="text-lg font-bold text-orange-600">{{ $lembur->jam_selesai->format('H:i') }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Total Jam</p>
                    <p class="text-lg font-bold text-purple-600">{{ $lembur->total_jam }} jam</p>
                </div>
            </div>
        </div>

        <!-- Alasan dan Deskripsi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-question-circle text-blue-600"></i>
                Alasan Lembur
            </h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700">{{ $lembur->alasan_lembur }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tasks text-blue-600"></i>
                Deskripsi Pekerjaan
            </h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700">{{ $lembur->deskripsi_pekerjaan }}</p>
            </div>
        </div>

        <!-- Perhitungan Upah -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-calculator text-green-600"></i>
                Perhitungan Upah Lembur
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Tarif per Jam</span>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($lembur->tarif_lembur_per_jam, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Total Jam</span>
                    <span class="text-sm font-bold text-gray-900">{{ $lembur->total_jam }} jam</span>
                </div>
                <div class="flex items-center justify-between py-4 bg-gradient-to-r from-green-500 to-green-600 rounded-lg px-4">
                    <span class="text-base font-bold text-white">Total Upah Lembur</span>
                    @if($lembur->total_upah_lembur)
                        <span class="text-xl font-bold text-white">Rp {{ number_format($lembur->total_upah_lembur, 0, ',', '.') }}</span>
                    @else
                        <span class="text-white opacity-75">Belum dihitung</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Approval -->
        @if($lembur->status !== 'pending')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-{{ $lembur->status === 'approved' ? 'check-circle text-green-600' : 'times-circle text-red-600' }}"></i>
                    Status Persetujuan
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Status</p>
                            @if($lembur->status == 'approved')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i>
                                    Ditolak
                                </span>
                            @endif
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Diproses oleh</p>
                            <p class="text-sm font-medium text-gray-900">{{ $lembur->approvedBy->name ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-1">Tanggal Diproses</p>
                            <p class="text-sm font-medium text-gray-900">{{ $lembur->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    @if($lembur->catatan_approval)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-600 mb-2">Catatan</p>
                            <p class="text-sm text-gray-700">{{ $lembur->catatan_approval }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Right: Actions -->
    <div class="space-y-6">
        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
            <div class="space-y-3">
                @if($lembur->status === 'pending')
                    <button onclick="approveModal()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>
                        Setujui Permintaan
                    </button>
                    <button onclick="rejectModal()" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                        <i class="fas fa-times-circle mr-2"></i>
                        Tolak Permintaan
                    </button>
                @endif
                @if($lembur->canEdit())
                    <a href="{{ route('perusahaan.lembur.edit', $lembur->hash_id) }}" class="block w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Permintaan
                    </a>
                @endif
                <a href="{{ route('perusahaan.lembur.index') }}" class="block w-full px-4 py-3 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition text-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Info -->
        @if($lembur->status === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-yellow-900 mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Menunggu Persetujuan
                </h3>
                <p class="text-xs text-yellow-800">Permintaan lembur ini masih menunggu persetujuan dari atasan. Silakan setujui atau tolak permintaan ini.</p>
            </div>
        @endif

        @if($lembur->status === 'approved')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-green-900 mb-2">
                    <i class="fas fa-check-circle mr-1"></i>
                    Disetujui
                </h3>
                <div class="text-xs text-green-800 space-y-1">
                    <p><strong>Oleh:</strong> {{ $lembur->approvedBy->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $lembur->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
        @endif

        @if($lembur->status === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-red-900 mb-2">
                    <i class="fas fa-times-circle mr-1"></i>
                    Ditolak
                </h3>
                <div class="text-xs text-red-800 space-y-1">
                    <p><strong>Oleh:</strong> {{ $lembur->approvedBy->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $lembur->approved_at?->format('d M Y H:i') ?? '-' }}</p>
                    @if($lembur->catatan_approval)
                        <p><strong>Alasan:</strong> {{ $lembur->catatan_approval }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Setujui Permintaan</h3>
                    <p class="text-sm text-gray-600">Konfirmasi persetujuan lembur</p>
                </div>
            </div>
            
            <form id="approveForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan_approval" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" rows="3" placeholder="Tambahkan catatan persetujuan..."></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeApproveModal()" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tolak Permintaan</h3>
                    <p class="text-sm text-gray-600">Berikan alasan penolakan</p>
                </div>
            </div>
            
            <form id="rejectForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_approval" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function approveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').classList.add('flex');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('flex');
}

function rejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

// Handle approve form
document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/lembur/{{ $lembur->hash_id }}/approve`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        closeApproveModal();
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
                title: 'Error!',
                text: data.message
            });
        }
    })
    .catch(error => {
        closeApproveModal();
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses permintaan.'
        });
    });
});

// Handle reject form
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/lembur/{{ $lembur->hash_id }}/reject`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        closeRejectModal();
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
                title: 'Error!',
                text: data.message
            });
        }
    })
    .catch(error => {
        closeRejectModal();
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses permintaan.'
        });
    });
});

// Close modal when clicking outside
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endpush