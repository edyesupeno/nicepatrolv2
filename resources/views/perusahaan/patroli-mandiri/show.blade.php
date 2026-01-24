@extends('perusahaan.layouts.app')

@section('title', 'Detail Patroli Mandiri')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Patroli Mandiri</h1>
            <nav class="text-sm text-gray-500 mt-1">
                <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="hover:text-blue-600">Patroli Mandiri</a>
                <span class="mx-2">/</span>
                <span>Detail</span>
            </nav>
        </div>
        <div>
            <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Laporan -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Laporan</h3>
                    <div class="flex space-x-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $patroliMandiri->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $patroliMandiri->status_lokasi)) }}
                        </span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $patroliMandiri->prioritas_badge }}">
                            {{ ucfirst($patroliMandiri->prioritas) }}
                        </span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $patroliMandiri->status_laporan_badge }}">
                            {{ ucfirst($patroliMandiri->status_laporan) }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Waktu Laporan</dt>
                                <dd class="text-sm text-gray-900">{{ $patroliMandiri->waktu_laporan->format('d/m/Y H:i:s') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Petugas</dt>
                                <dd class="text-sm text-gray-900">{{ $patroliMandiri->petugas->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Project</dt>
                                <dd class="text-sm text-gray-900">{{ $patroliMandiri->project->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Area Patrol</dt>
                                <dd class="text-sm text-gray-900">{{ $patroliMandiri->areaPatrol->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Lokasi</dt>
                                <dd class="text-sm text-gray-900">{{ $patroliMandiri->nama_lokasi }}</dd>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Koordinat</dt>
                                <dd class="text-sm text-gray-900">{{ $patroliMandiri->latitude }}, {{ $patroliMandiri->longitude }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Maps</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($patroliMandiri->maps_url)
                                        <a href="{{ $patroliMandiri->maps_url }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-map-marker-alt mr-1"></i>Buka Maps
                                        </a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Lokasi</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $patroliMandiri->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $patroliMandiri->status_lokasi)) }}
                                    </span>
                                </dd>
                            </div>
                            @if($patroliMandiri->jenis_kendala)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Kendala</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $patroliMandiri->jenis_kendala)) }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Prioritas</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $patroliMandiri->prioritas_badge }}">
                                        {{ ucfirst($patroliMandiri->prioritas) }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                    </div>

                    @if($patroliMandiri->deskripsi_kendala)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi Kendala</dt>
                        <dd class="text-sm text-gray-900">{{ $patroliMandiri->deskripsi_kendala }}</dd>
                    </div>
                    @endif

                    @if($patroliMandiri->catatan_petugas)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Catatan Petugas</dt>
                        <dd class="text-sm text-gray-900">{{ $patroliMandiri->catatan_petugas }}</dd>
                    </div>
                    @endif

                    @if($patroliMandiri->tindakan_yang_diambil)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Tindakan yang Diambil</dt>
                        <dd class="text-sm text-gray-900">{{ $patroliMandiri->tindakan_yang_diambil }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Foto Dokumentasi -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Foto Dokumentasi</h3>
                </div>
                <div class="p-6">
                    @if($patroliMandiri->foto_lokasi || $patroliMandiri->foto_kendala)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($patroliMandiri->foto_lokasi)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Foto Lokasi</h4>
                                <div class="text-center">
                                    <img src="{{ $patroliMandiri->foto_lokasi_url }}" 
                                         class="w-full h-64 object-cover rounded-lg shadow cursor-pointer hover:shadow-lg transition-shadow"
                                         onclick="showImageModal('{{ $patroliMandiri->foto_lokasi_url }}', 'Foto Lokasi')">
                                </div>
                            </div>
                            @endif

                            @if($patroliMandiri->foto_kendala)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Foto Kendala</h4>
                                <div class="text-center">
                                    <img src="{{ $patroliMandiri->foto_kendala_url }}" 
                                         class="w-full h-64 object-cover rounded-lg shadow cursor-pointer hover:shadow-lg transition-shadow"
                                         onclick="showImageModal('{{ $patroliMandiri->foto_kendala_url }}', 'Foto Kendala')">
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-camera text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Tidak ada foto dokumentasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Review Section -->
            @if($patroliMandiri->status_laporan === 'submitted')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-yellow-600">Review Laporan</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Laporan ini belum direview. Silakan berikan review untuk laporan ini.</p>
                    <button type="button" onclick="showReviewModal()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-2"></i>Review Laporan
                    </button>
                </div>
            </div>
            @endif

            <!-- Review History -->
            @if($patroliMandiri->reviewed_by)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-green-600">Review History</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-check text-green-500 mr-2"></i>
                        <span class="font-medium">{{ $patroliMandiri->reviewer->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center mb-3">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-500">{{ $patroliMandiri->reviewed_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($patroliMandiri->review_catatan)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Catatan Review</h4>
                        <p class="text-sm text-gray-900">{{ $patroliMandiri->review_catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($patroliMandiri->status_laporan === 'submitted')
                    <button type="button" onclick="showReviewModal()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-2"></i>Review Laporan
                    </button>
                    <a href="{{ route('perusahaan.patroli-mandiri.edit', $patroliMandiri->hash_id) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-center transition-colors">
                        <i class="fas fa-pencil-alt mr-2"></i>Edit Laporan
                    </a>
                    @endif
                    <button type="button" onclick="confirmDelete()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Hapus Laporan
                    </button>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="border-r border-gray-200">
                            <div class="text-lg font-bold text-blue-600">
                                {{ $patroliMandiri->created_at->diffForHumans() }}
                            </div>
                            <div class="text-xs text-gray-500">Dibuat</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-green-600">
                                {{ $patroliMandiri->updated_at->diffForHumans() }}
                            </div>
                            <div class="text-xs text-gray-500">Diupdate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Review Laporan</h3>
            <form id="reviewForm">
                <div class="mb-4">
                    <label for="review_status_laporan" class="block text-sm font-medium text-gray-700 mb-2">Status Laporan</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            id="review_status_laporan" name="status_laporan" required>
                        <option value="reviewed">Reviewed</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="review_catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan Review</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              id="review_catatan" name="review_catatan" rows="3" 
                              placeholder="Masukkan catatan review..."></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeReviewModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Simpan Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="imageModalLabel">Foto</h3>
                <button type="button" onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="text-center">
                <img id="modalImage" src="" class="max-w-full max-h-96 mx-auto rounded-lg">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Show review modal
function showReviewModal() {
    document.getElementById('review_status_laporan').value = 'reviewed';
    document.getElementById('review_catatan').value = '';
    document.getElementById('reviewModal').classList.remove('hidden');
}

// Close review modal
function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

// Handle review form submission
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("perusahaan.patroli-mandiri.review", $patroliMandiri->hash_id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
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
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan review'
        });
    });
    
    closeReviewModal();
});

// Show image modal
function showImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModalLabel').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

// Close image modal
function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Confirm delete
function confirmDelete() {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Laporan patroli mandiri akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            deletePatroli();
        }
    });
}

// Delete patroli
function deletePatroli() {
    fetch('{{ route("perusahaan.patroli-mandiri.destroy", $patroliMandiri->hash_id) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("perusahaan.patroli-mandiri.index") }}';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus laporan'
        });
    });
}

// Close modal when clicking outside
document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewModal();
    }
});

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
@endpush