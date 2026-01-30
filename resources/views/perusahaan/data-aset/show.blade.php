@extends('perusahaan.layouts.app')

@section('title', 'Detail Data Aset')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('perusahaan.data-aset.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Data Aset
        </a>
        <div class="flex space-x-2">
            <a href="{{ route('perusahaan.data-aset.export-label', $dataAset->hash_id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-barcode mr-2"></i>Export Label
            </a>
            <a href="{{ route('perusahaan.data-aset.edit', $dataAset->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <button onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-trash mr-2"></i>Hapus
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">{{ $dataAset->nama_aset }}</h1>
                        <p class="text-gray-600 mt-1">{{ $dataAset->kode_aset }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($dataAset->status === 'ada') bg-green-100 text-green-800
                        @elseif($dataAset->status === 'rusak') bg-red-100 text-red-800
                        @elseif($dataAset->status === 'dijual') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $dataAset->status_label }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Informasi Dasar</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Kategori</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $dataAset->kategori }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Project</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $dataAset->project->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Tanggal Beli</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $dataAset->tanggal_beli->format('d F Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Umur Aset</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($dataAset->umur_aset)
                                        {{ $dataAset->umur_aset }} tahun
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Informasi Finansial</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Harga Beli</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $dataAset->formatted_harga_beli }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Nilai Penyusutan</dt>
                                <dd class="mt-1 text-sm text-red-600">{{ $dataAset->formatted_nilai_penyusutan }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-700">Nilai Sekarang</dt>
                                <dd class="mt-1 text-lg font-bold text-green-600">{{ $dataAset->formatted_nilai_sekarang }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Penanggung Jawab</h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $dataAset->pic_penanggung_jawab }}</p>
                            <p class="text-sm text-gray-500">PIC Aset</p>
                        </div>
                    </div>
                </div>
                
                @if($dataAset->catatan_tambahan)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Catatan Tambahan</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $dataAset->catatan_tambahan }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Foto Aset -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Foto Aset</h3>
            </div>
            <div class="p-6">
                @if($dataAset->foto_aset)
                    <div class="text-center">
                        <img src="{{ $dataAset->foto_url }}" alt="{{ $dataAset->nama_aset }}" class="w-full h-48 object-cover rounded-lg mb-3">
                        <a href="{{ $dataAset->foto_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>Lihat Ukuran Penuh
                        </a>
                    </div>
                @else
                    <div class="text-center text-gray-500">
                        <i class="fas fa-image text-4xl mb-3"></i>
                        <p class="text-sm">Tidak ada foto</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Informasi Sistem -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Sistem</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-700">Dibuat Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataAset->createdBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">Tanggal Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataAset->created_at->format('d F Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">Terakhir Diupdate</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataAset->updated_at->format('d F Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('perusahaan.data-aset.export-label', $dataAset->hash_id) }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-barcode mr-2"></i>Export Label
                </a>
                <button onclick="printAset()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-print mr-2"></i>Cetak Detail
                </button>
                <button onclick="exportAset()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-file-export mr-2"></i>Export Data
                </button>
                @if($dataAset->status === 'ada')
                    <button onclick="changeStatus('rusak')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Tandai Rusak
                    </button>
                @elseif($dataAset->status === 'rusak')
                    <button onclick="changeStatus('ada')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Tandai Baik
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus aset "{{ $dataAset->nama_aset }}"? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </button>
            <form action="{{ route('perusahaan.data-aset.destroy', $dataAset->hash_id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function printAset() {
    window.print();
}

function exportAset() {
    // Implement export functionality
    Swal.fire({
        icon: 'info',
        title: 'Export Data',
        text: 'Fitur export akan segera tersedia',
        timer: 2000,
        showConfirmButton: false
    });
}

function changeStatus(newStatus) {
    Swal.fire({
        title: 'Ubah Status Aset?',
        text: `Apakah Anda yakin ingin mengubah status aset menjadi "${newStatus}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.data-aset.update", $dataAset->hash_id) }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            form.appendChild(method);
            
            // Add all current data
            const fields = {
                'project_id': '{{ $dataAset->project_id }}',
                'nama_aset': '{{ $dataAset->nama_aset }}',
                'kategori': '{{ $dataAset->kategori }}',
                'tanggal_beli': '{{ $dataAset->tanggal_beli->format("Y-m-d") }}',
                'harga_beli': '{{ $dataAset->harga_beli }}',
                'nilai_penyusutan': '{{ $dataAset->nilai_penyusutan }}',
                'pic_penanggung_jawab': '{{ $dataAset->pic_penanggung_jawab }}',
                'catatan_tambahan': '{{ $dataAset->catatan_tambahan }}',
                'status': newStatus
            };
            
            Object.keys(fields).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-white {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
@endpush