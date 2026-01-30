@extends('perusahaan.layouts.app')

@section('title', 'Detail Disposal Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Disposal Aset</h1>
            <p class="text-gray-600 mt-1">{{ $disposalAset->nomor_disposal }}</p>
        </div>
        <div class="flex gap-3">
            @if($disposalAset->status === 'pending')
                <a href="{{ route('perusahaan.disposal-aset.edit', $disposalAset->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('perusahaan.disposal-aset.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Informasi Disposal
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Disposal</label>
                            <p class="text-gray-900 font-medium">{{ $disposalAset->nomor_disposal }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $disposalAset->status_badge }}">
                                {{ ucfirst($disposalAset->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <p class="text-gray-900">{{ $disposalAset->project->nama ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Disposal</label>
                            <p class="text-gray-900">{{ $disposalAset->tanggal_disposal->format('d F Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Disposal</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $disposalAset->jenis_disposal_badge }}">
                                {{ ucfirst($disposalAset->jenis_disposal) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diajukan Oleh</label>
                            <p class="text-gray-900">{{ $disposalAset->diajukanOleh->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cube text-green-600"></i>
                        Informasi Aset
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset</label>
                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $disposalAset->asset_type)) }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Aset</label>
                            <p class="text-gray-900 font-mono">{{ $disposalAset->asset_code }}</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset</label>
                            <p class="text-gray-900">{{ $disposalAset->asset_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Buku</label>
                            <p class="text-gray-900 font-semibold">{{ $disposalAset->formatted_nilai_buku }}</p>
                        </div>
                        
                        @if($disposalAset->nilai_disposal)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Disposal</label>
                            <p class="text-green-600 font-semibold">{{ $disposalAset->formatted_nilai_disposal }}</p>
                        </div>
                        @endif
                        
                        @if($disposalAset->pembeli)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pembeli</label>
                            <p class="text-gray-900">{{ $disposalAset->pembeli }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Disposal Details -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-purple-600"></i>
                        Detail Disposal
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Disposal</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $disposalAset->alasan_disposal }}</p>
                            </div>
                        </div>
                        
                        @if($disposalAset->catatan)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $disposalAset->catatan }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($disposalAset->foto_kondisi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kondisi Aset</label>
                            <div class="mt-2">
                                <img src="{{ Storage::url($disposalAset->foto_kondisi) }}" alt="Foto Kondisi" class="max-w-md rounded-lg shadow-sm border">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Information -->
            @if($disposalAset->status !== 'pending')
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-600"></i>
                        Informasi Approval
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Disetujui Oleh</label>
                            <p class="text-gray-900">{{ $disposalAset->disetujuiOleh->name ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Approval</label>
                            <p class="text-gray-900">{{ $disposalAset->tanggal_disetujui ? $disposalAset->tanggal_disetujui->format('d F Y H:i') : '-' }}</p>
                        </div>
                        
                        @if($disposalAset->catatan_approval)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Approval</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $disposalAset->catatan_approval }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h3>
                    
                    <div class="space-y-3">
                        @if($disposalAset->status === 'pending')
                            <button onclick="approveDisposal('{{ $disposalAset->hash_id }}', 'approve')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            
                            <button onclick="approveDisposal('{{ $disposalAset->hash_id }}', 'reject')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        @endif
                        
                        @if($disposalAset->status === 'approved')
                            <button onclick="completeDisposal('{{ $disposalAset->hash_id }}')" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i> Selesaikan Disposal
                            </button>
                        @endif
                        
                        @if(in_array($disposalAset->status, ['pending', 'rejected']))
                            <button onclick="deleteDisposal('{{ $disposalAset->hash_id }}')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-plus text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Disposal Diajukan</p>
                                <p class="text-sm text-gray-500">{{ $disposalAset->created_at->format('d F Y H:i') }}</p>
                                <p class="text-sm text-gray-600">Oleh: {{ $disposalAset->diajukanOleh->name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        @if($disposalAset->status !== 'pending')
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 {{ $disposalAset->status === 'approved' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $disposalAset->status === 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600' }} text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $disposalAset->status === 'approved' ? 'Disposal Disetujui' : 'Disposal Ditolak' }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $disposalAset->tanggal_disetujui ? $disposalAset->tanggal_disetujui->format('d F Y H:i') : '-' }}</p>
                                <p class="text-sm text-gray-600">Oleh: {{ $disposalAset->disetujuiOleh->name ?? '-' }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($disposalAset->status === 'completed')
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check-circle text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Disposal Selesai</p>
                                <p class="text-sm text-gray-500">{{ $disposalAset->updated_at->format('d F Y H:i') }}</p>
                                <p class="text-sm text-gray-600">Aset telah dihapus dari daftar aktif</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
                    window.location.href = '{{ route("perusahaan.disposal-aset.index") }}';
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