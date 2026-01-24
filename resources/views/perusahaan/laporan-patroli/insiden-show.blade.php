@extends('perusahaan.layouts.app')

@section('title', 'Detail Laporan Insiden')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Laporan Insiden</h1>
            <nav class="text-sm text-gray-600 mt-1">
                <a href="{{ route('perusahaan.laporan-patroli.insiden') }}" class="hover:text-blue-600">Laporan Insiden</a>
                <span class="mx-2">/</span>
                <span>Detail</span>
            </nav>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('perusahaan.laporan-patroli.insiden') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('perusahaan.laporan-patroli.insiden.pdf', $patroliMandiri->hash_id) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Laporan</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->waktu_laporan->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Petugas</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->petugas->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->project->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Area Patrol</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->areaPatrol->nama ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->nama_lokasi }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incident Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Insiden</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendala</label>
                            <p class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $patroliMandiri->jenis_kendala)) }}</p>
                        </div>
                        
                        @if($patroliMandiri->deskripsi_kendala)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kendala</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patroliMandiri->deskripsi_kendala }}</p>
                            </div>
                        </div>
                        @endif

                        @if($patroliMandiri->tindakan_yang_diambil)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tindakan yang Diambil</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patroliMandiri->tindakan_yang_diambil }}</p>
                            </div>
                        </div>
                        @endif

                        @if($patroliMandiri->catatan_petugas)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Petugas</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patroliMandiri->catatan_petugas }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Dokumentasi Foto</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Foto Lokasi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Lokasi</label>
                            @if($patroliMandiri->foto_lokasi)
                                <div class="relative">
                                    <img src="{{ $patroliMandiri->foto_lokasi_url }}" 
                                         alt="Foto Lokasi" 
                                         class="w-full h-64 object-cover rounded-lg border cursor-pointer"
                                         onclick="openImageModal('{{ $patroliMandiri->foto_lokasi_url }}', 'Foto Lokasi')">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 hover:opacity-100 text-2xl"></i>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-gray-500">Tidak ada foto lokasi</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Foto Kendala -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kendala</label>
                            @if($patroliMandiri->foto_kendala)
                                <div class="relative">
                                    <img src="{{ $patroliMandiri->foto_kendala_url }}" 
                                         alt="Foto Kendala" 
                                         class="w-full h-64 object-cover rounded-lg border cursor-pointer"
                                         onclick="openImageModal('{{ $patroliMandiri->foto_kendala_url }}', 'Foto Kendala')">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 hover:opacity-100 text-2xl"></i>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-gray-500">Tidak ada foto kendala</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Section -->
            @if($patroliMandiri->status_laporan !== 'submitted')
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Review & Tindak Lanjut</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Direview Oleh</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->reviewer->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Review</label>
                            <p class="text-sm text-gray-900">{{ $patroliMandiri->reviewed_at ? $patroliMandiri->reviewed_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>
                        @if($patroliMandiri->review_catatan)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Review</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patroliMandiri->review_catatan }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $patroliMandiri->prioritas_badge }}">
                            {{ ucfirst($patroliMandiri->prioritas) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Laporan</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $patroliMandiri->status_laporan_badge }}">
                            {{ ucfirst($patroliMandiri->status_laporan) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Location Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Lokasi</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Koordinat GPS</label>
                        <p class="text-sm text-gray-900">{{ $patroliMandiri->latitude }}, {{ $patroliMandiri->longitude }}</p>
                    </div>
                    @if($patroliMandiri->maps_url)
                    <div>
                        <a href="{{ $patroliMandiri->maps_url }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Lihat di Google Maps
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('perusahaan.laporan-patroli.insiden.pdf', $patroliMandiri->hash_id) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Download PDF
                    </a>
                    <a href="{{ route('perusahaan.patroli-mandiri.show', $patroliMandiri->hash_id) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Kelola Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="max-w-4xl max-h-full p-4">
        <div class="relative">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl">
                <i class="fas fa-times"></i>
            </button>
            <div id="modalTitle" class="absolute bottom-4 left-4 text-white bg-black bg-opacity-50 px-3 py-1 rounded"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openImageModal(src, title) {
    document.getElementById('modalImage').src = src;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endpush