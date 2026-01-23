@extends('perusahaan.layouts.app')

@section('title', 'Permintaan Lembur')
@section('page-title', 'Permintaan Lembur Karyawan')
@section('page-subtitle', 'Kelola permintaan lembur dan sistem persetujuan')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div id="successAlert" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600 text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <button onclick="document.getElementById('successAlert').remove()" class="text-green-600 hover:text-green-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

@if(session('error'))
<div id="errorAlert" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    <button onclick="document.getElementById('errorAlert').remove()" class="text-red-600 hover:text-red-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Permintaan -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Permintaan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-list text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Menunggu Persetujuan -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-yellow-100">
                <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Disetujui -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-100">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Ditolak -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Ditolak</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-red-100">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Actions & Filters -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <h2 class="text-xl font-semibold text-gray-900">Daftar Permintaan Lembur</h2>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('perusahaan.lembur.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg shadow-sm transition-all duration-200 hover:shadow-md" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-plus mr-2"></i>
            Tambah Permintaan Lembur
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
            <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('tanggal_mulai') }}">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('tanggal_selesai') }}">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Karyawan</label>
            <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nama karyawan..." value="{{ request('search') }}">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Lembur</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upah Lembur</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lemburs as $index => $lembur)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lemburs->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lembur->tanggal_lembur->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $lembur->karyawan->nama_lengkap }}</div>
                            <div class="text-sm text-gray-500">{{ $lembur->karyawan->nik_karyawan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lembur->project->nama }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lembur->jam_mulai->format('H:i') }} - {{ $lembur->jam_selesai->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $lembur->total_jam }} jam
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($lembur->total_upah_lembur)
                                <span class="font-medium text-green-600">Rp {{ number_format($lembur->total_upah_lembur, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lembur->status === 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $lembur->status_text }}
                                </span>
                            @elseif($lembur->status === 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $lembur->status_text }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $lembur->status_text }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('perusahaan.lembur.show', $lembur->hash_id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($lembur->canEdit())
                                    <a href="{{ route('perusahaan.lembur.edit', $lembur->hash_id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                @if($lembur->status === 'pending')
                                    <button type="button" class="text-green-600 hover:text-green-900 transition-colors" 
                                            onclick="approveModal('{{ $lembur->hash_id }}')" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="text-red-600 hover:text-red-900 transition-colors" 
                                            onclick="rejectModal('{{ $lembur->hash_id }}')" title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                @if($lembur->canDelete())
                                    <button type="button" class="text-red-600 hover:text-red-900 transition-colors" 
                                            onclick="deleteConfirm('{{ $lembur->hash_id }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">Tidak ada data permintaan lembur</p>
                                <p class="text-sm">Belum ada permintaan lembur yang dibuat</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($lemburs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $lemburs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Setujui Permintaan Lembur</h3>
                <button onclick="closeModal('approveModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="approveForm">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menyetujui permintaan lembur ini?</p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan_approval" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tolak Permintaan Lembur</h3>
                <button onclick="closeModal('rejectModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="rejectForm">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menolak permintaan lembur ini?</p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_approval" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentLemburId = null;

function approveModal(lemburId) {
    currentLemburId = lemburId;
    document.getElementById('approveModal').classList.remove('hidden');
}

function rejectModal(lemburId) {
    currentLemburId = lemburId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function deleteConfirm(lemburId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Permintaan lembur ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/lembur/${lemburId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Handle approve form
document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/lembur/${currentLemburId}/approve`, {
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
    
    fetch(`/perusahaan/lembur/${currentLemburId}/reject`, {
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
</script>
@endpush