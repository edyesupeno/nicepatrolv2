@extends('perusahaan.layouts.app')

@section('title', 'Disposal Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Disposal Aset</h1>
            <p class="text-gray-600 mt-1">Kelola disposal dan penghapusan aset perusahaan</p>
        </div>
        <div class="flex gap-3">
            <button onclick="exportPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <a href="{{ route('perusahaan.disposal-aset.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-plus"></i> Ajukan Disposal
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Disposal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-full">
                    <i class="fas fa-trash-alt text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="bg-yellow-100 p-2 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Approved</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['approved']) }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-full">
                    <i class="fas fa-check text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Completed</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['completed']) }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Nilai</p>
                    <p class="text-lg font-bold text-green-600">Rp {{ number_format($stats['total_nilai_disposal'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset</label>
                <select name="asset_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="data_aset" {{ request('asset_type') == 'data_aset' ? 'selected' : '' }}>Data Aset</option>
                    <option value="aset_kendaraan" {{ request('asset_type') == 'aset_kendaraan' ? 'selected' : '' }}>Aset Kendaraan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Disposal</label>
                <select name="jenis_disposal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="dijual" {{ request('jenis_disposal') == 'dijual' ? 'selected' : '' }}>Dijual</option>
                    <option value="rusak" {{ request('jenis_disposal') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="hilang" {{ request('jenis_disposal') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="tidak_layak" {{ request('jenis_disposal') == 'tidak_layak' ? 'selected' : '' }}>Tidak Layak</option>
                    <option value="expired" {{ request('jenis_disposal') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor, kode aset, nama..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('perusahaan.disposal-aset.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Disposal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($disposalAsets as $disposal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $disposal->nomor_disposal }}</div>
                                <div class="text-sm text-gray-500">{{ $disposal->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $disposal->asset_name }}</div>
                                <div class="text-sm text-gray-500">{{ $disposal->asset_code }}</div>
                                <div class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $disposal->asset_type)) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $disposal->project->nama ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $disposal->tanggal_disposal->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $disposal->jenis_disposal_badge }}">
                                    {{ ucfirst($disposal->jenis_disposal) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $disposal->formatted_nilai_buku }}</div>
                                @if($disposal->nilai_disposal)
                                    <div class="text-sm text-green-600">{{ $disposal->formatted_nilai_disposal }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $disposal->status_badge }}">
                                    {{ ucfirst($disposal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('perusahaan.disposal-aset.show', $disposal->hash_id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($disposal->status === 'pending')
                                        <a href="{{ route('perusahaan.disposal-aset.edit', $disposal->hash_id) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button onclick="approveDisposal('{{ $disposal->hash_id }}', 'approve')" class="text-green-600 hover:text-green-900" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        
                                        <button onclick="approveDisposal('{{ $disposal->hash_id }}', 'reject')" class="text-red-600 hover:text-red-900" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    @if($disposal->status === 'approved')
                                        <button onclick="completeDisposal('{{ $disposal->hash_id }}')" class="text-purple-600 hover:text-purple-900" title="Selesaikan">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    
                                    @if(in_array($disposal->status, ['pending', 'rejected']))
                                        <button onclick="deleteDisposal('{{ $disposal->hash_id }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-trash-alt text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Belum ada data disposal aset</p>
                                <p class="text-sm">Klik "Ajukan Disposal" untuk menambah data baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($disposalAsets->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $disposalAsets->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="approvalTitle">Konfirmasi Approval</h3>
                <form id="approvalForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="catatan_approval" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan approval..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" id="approvalSubmitBtn" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentDisposalId = null;
let currentAction = null;

function exportPDF() {
    const params = new URLSearchParams(window.location.search);
    const url = new URL('{{ route("perusahaan.disposal-aset.export-pdf") }}');
    
    // Add current filters to export URL
    for (const [key, value] of params) {
        if (value) url.searchParams.set(key, value);
    }
    
    Swal.fire({
        title: 'Export PDF',
        text: 'Sedang memproses laporan PDF...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            window.location.href = url.toString();
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    });
}

function approveDisposal(disposalId, action) {
    currentDisposalId = disposalId;
    currentAction = action;
    
    const title = action === 'approve' ? 'Setujui Disposal Aset' : 'Tolak Disposal Aset';
    const btnText = action === 'approve' ? 'Setujui' : 'Tolak';
    const btnClass = action === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700';
    
    document.getElementById('approvalTitle').textContent = title;
    document.getElementById('approvalSubmitBtn').textContent = btnText;
    document.getElementById('approvalSubmitBtn').className = `px-4 py-2 text-white rounded-lg ${btnClass}`;
    
    document.getElementById('approvalModal').classList.remove('hidden');
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('approvalForm').reset();
    currentDisposalId = null;
    currentAction = null;
}

document.getElementById('approvalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentDisposalId || !currentAction) return;
    
    const formData = new FormData(this);
    formData.append('action', currentAction);
    
    try {
        const response = await fetch(`/perusahaan/disposal-aset/${currentDisposalId}/approve`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
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
            text: 'Terjadi kesalahan sistem'
        });
    }
    
    closeApprovalModal();
});

async function completeDisposal(disposalId) {
    const result = await Swal.fire({
        title: 'Selesaikan Disposal?',
        text: 'Aset akan dihapus dari daftar aktif setelah disposal diselesaikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Selesaikan!',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/disposal-aset/${disposalId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem'
            });
        }
    }
}

async function deleteDisposal(disposalId) {
    const result = await Swal.fire({
        title: 'Hapus Disposal?',
        text: 'Data disposal akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/disposal-aset/${disposalId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Disposal aset berhasil dihapus',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error('Failed to delete');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem'
            });
        }
    }
}
</script>
@endpush