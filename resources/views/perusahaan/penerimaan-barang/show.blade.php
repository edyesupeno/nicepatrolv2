@extends('perusahaan.layouts.app')

@section('title', 'Detail Penerimaan Barang')
@section('page-title', 'Detail Penerimaan Barang')
@section('page-subtitle', 'Informasi lengkap penerimaan barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.penerimaan-barang.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-box mr-2"></i>
                    Penerimaan Barang
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">{{ $penerimaanBarang->nama_barang }}</h1>
                        <p class="text-sm text-gray-600">{{ $penerimaanBarang->nomor_penerimaan }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('perusahaan.penerimaan-barang.edit', $penerimaanBarang->hash_id) }}" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <button onclick="deleteItem('{{ $penerimaanBarang->hash_id }}')" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Status Badge -->
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $kondisiClass = match($penerimaanBarang->kondisi_barang) {
                            'Baik' => 'bg-green-100 text-green-800',
                            'Rusak' => 'bg-red-100 text-red-800',
                            'Segel Terbuka' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $kondisiIcon = match($penerimaanBarang->kondisi_barang) {
                            'Baik' => 'fas fa-check-circle',
                            'Rusak' => 'fas fa-times-circle',
                            'Segel Terbuka' => 'fas fa-exclamation-circle',
                            default => 'fas fa-question-circle'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kondisiClass }}">
                        <i class="{{ $kondisiIcon }} mr-2"></i>
                        {{ $penerimaanBarang->kondisi_barang }}
                    </span>
                    
                    @php
                        $kategoriClass = match($penerimaanBarang->kategori_barang) {
                            'Dokumen' => 'bg-blue-100 text-blue-800',
                            'Material' => 'bg-green-100 text-green-800',
                            'Elektronik' => 'bg-purple-100 text-purple-800',
                            'Logistik' => 'bg-orange-100 text-orange-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kategoriClass }}">
                        {{ $penerimaanBarang->kategori_barang }}
                    </span>
                </div>
                
                <div class="text-sm text-gray-600">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ $penerimaanBarang->formatted_tanggal_terima }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Detail Barang -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Barang</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->nama_barang }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->kategori_barang }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->jumlah_barang }} {{ $penerimaanBarang->satuan }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->kondisi_barang }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->pengirim }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Departemen</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->tujuan_departemen }}</p>
                        </div>
                        
                        @if($penerimaanBarang->project)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->project->nama }}</p>
                        </div>
                        @endif
                        
                        @if($penerimaanBarang->area)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Area Penyimpanan</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->area->nama }}{{ $penerimaanBarang->area->alamat ? ' - ' . $penerimaanBarang->area->alamat : '' }}</p>
                        </div>
                        @endif
                        
                        @if($penerimaanBarang->pos)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">POS (Pos Jaga Security)</label>
                            <p class="text-gray-900">{{ $penerimaanBarang->pos }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if($penerimaanBarang->keterangan)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <p class="text-gray-900">{{ $penerimaanBarang->keterangan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Foto Barang -->
            @if($penerimaanBarang->foto_barang)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Foto Barang</h2>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <img 
                            src="{{ Storage::url($penerimaanBarang->foto_barang) }}" 
                            alt="Foto {{ $penerimaanBarang->nama_barang }}"
                            class="w-full max-w-md mx-auto rounded-lg shadow-sm border border-gray-200"
                            onclick="openImageModal(this.src)"
                        >
                        <button 
                            onclick="openImageModal('{{ Storage::url($penerimaanBarang->foto_barang) }}')"
                            class="absolute top-2 right-2 bg-black bg-opacity-50 text-white p-2 rounded-lg hover:bg-opacity-70 transition"
                        >
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Info Penerimaan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Info Penerimaan</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Penerimaan</label>
                        <p class="text-gray-900 font-mono">{{ $penerimaanBarang->nomor_penerimaan }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terima</label>
                        <p class="text-gray-900">{{ $penerimaanBarang->formatted_tanggal_terima }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Petugas Penerima</label>
                        <p class="text-gray-900">{{ $penerimaanBarang->petugas_penerima }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            {{ $penerimaanBarang->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Aksi</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('perusahaan.penerimaan-barang.edit', $penerimaanBarang->hash_id) }}" class="block w-full px-4 py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-left">
                        <i class="fas fa-edit mr-3"></i>Edit Data Barang
                    </a>
                    
                    <button onclick="printDetail()" class="block w-full px-4 py-3 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition font-medium text-left">
                        <i class="fas fa-print mr-3"></i>Cetak Detail
                    </button>
                    
                    <button onclick="deleteItem('{{ $penerimaanBarang->hash_id }}')" class="block w-full px-4 py-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-left">
                        <i class="fas fa-trash mr-3"></i>Hapus Data
                    </button>
                    
                    <a href="{{ route('perusahaan.penerimaan-barang.index') }}" class="block w-full px-4 py-3 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium text-left">
                        <i class="fas fa-arrow-left mr-3"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-full rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 bg-black bg-opacity-50 text-white p-2 rounded-lg hover:bg-opacity-70 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function deleteItem(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data penerimaan barang akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('perusahaan.penerimaan-barang.index') }}/${hashId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                        window.location.href = '{{ route('perusahaan.penerimaan-barang.index') }}';
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus data'
                });
            });
        }
    });
}

function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function printDetail() {
    window.print();
}

// Close modal when clicking outside
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

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-white {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
@endpush