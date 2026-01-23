<!-- Statistics Cards -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-blue-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
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
                <p class="text-lg font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
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
                <p class="text-lg font-semibold text-gray-900">{{ $stats['approved'] ?? 0 }}</p>
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
                <p class="text-lg font-semibold text-gray-900">{{ $stats['rejected'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="mb-6 flex justify-end">
    <a href="{{ route('perusahaan.kontrak-resign.create-resign') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
        <i class="fas fa-plus mr-2"></i>Tambah Pengajuan Resign
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <input type="hidden" name="tab" value="resign">
        
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Resign</label>
            <select name="jenis_resign" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Jenis</option>
                <option value="resign_pribadi" {{ request('jenis_resign') == 'resign_pribadi' ? 'selected' : '' }}>Resign Pribadi</option>
                <option value="kontrak_habis" {{ request('jenis_resign') == 'kontrak_habis' ? 'selected' : '' }}>Kontrak Habis</option>
                <option value="phk" {{ request('jenis_resign') == 'phk' ? 'selected' : '' }}>PHK</option>
                <option value="pensiun" {{ request('jenis_resign') == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                <option value="meninggal_dunia" {{ request('jenis_resign') == 'meninggal_dunia' ? 'selected' : '' }}>Meninggal Dunia</option>
                <option value="lainnya" {{ request('jenis_resign') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Resign</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Resign</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($resigns as $resign)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">{{ substr($resign->karyawan->nama_lengkap, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $resign->karyawan->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500">{{ $resign->karyawan->nik_karyawan }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $resign->project->nama }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $resign->tanggal_resign_efektif->format('d M Y') }}</div>
                        <div class="text-sm text-gray-500">Pengajuan: {{ $resign->tanggal_pengajuan->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {!! $resign->jenis_resign_badge !!}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {!! $resign->status_badge !!}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('perusahaan.kontrak-resign.show-resign', $resign->hash_id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($resign->canApprove())
                                <button onclick="openApproveModal('{{ $resign->hash_id }}')" class="text-green-600 hover:text-green-900 transition-colors" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="openRejectModal('{{ $resign->hash_id }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                            
                            @if($resign->canEdit())
                                <a href="{{ route('perusahaan.kontrak-resign.edit-resign', $resign->hash_id) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            
                            @if($resign->canDelete())
                                <button onclick="deleteConfirm('{{ $resign->hash_id }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-user-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Belum ada pengajuan resign</p>
                            <p class="text-gray-400 text-sm mb-6">Tambahkan pengajuan resign pertama untuk memulai</p>
                            <a href="{{ route('perusahaan.kontrak-resign.create-resign') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Tambah Pengajuan Resign
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
@if(isset($resigns) && $resigns->hasPages())
<div class="mt-6">
    {{ $resigns->links() }}
</div>
@endif

<!-- Include modals -->
@include('perusahaan.kontrak-resign.partials.resign-modals')

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
let currentResignId = null;

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
document.querySelector('input[name="is_blacklist"]').addEventListener('change', function() {
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
document.getElementById('approveForm').addEventListener('submit', function(e) {
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
document.getElementById('rejectForm').addEventListener('submit', function(e) {
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