@extends('perusahaan.layouts.app')

@section('title', 'Peminjaman Aset')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Peminjaman Aset</h1>
            <p class="text-gray-600 mt-1">Kelola peminjaman aset perusahaan</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="showExportModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>
                Export Laporan
            </button>
            <a href="{{ route('perusahaan.peminjaman-aset.jatuh-tempo') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-clock mr-2"></i>
                Jatuh Tempo
            </a>
            <a href="{{ route('perusahaan.peminjaman-aset.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Peminjaman
            </a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('perusahaan.peminjaman-aset.index') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Project</label>
                <select name="project_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status_peminjaman" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('status_peminjaman') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Aset</label>
                <select name="aset_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($asetTypeOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('aset_type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Filter Khusus</label>
                <select name="terlambat" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua</option>
                    <option value="1" {{ request('terlambat') == '1' ? 'selected' : '' }}>Hanya Terlambat</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode, aset, peminjam..." 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Per Halaman</label>
                <select name="per_page" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-200">
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-search mr-1"></i>
                    Filter
                </button>
                <a href="{{ route('perusahaan.peminjaman-aset.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-times mr-1"></i>
                    Reset
                </a>
            </div>
            <div class="text-sm text-gray-600">
                Total: {{ $peminjamans->total() }} peminjaman
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Peminjaman</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjamans as $peminjaman)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $peminjaman->kode_peminjaman }}</div>
                            <div class="text-xs text-gray-500">{{ $peminjaman->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $peminjaman->aset_nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $peminjaman->aset_kode }}</div>
                                    <div class="text-xs text-gray-400">{{ $peminjaman->aset_kategori }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $peminjaman->peminjam_nama }}</div>
                            <div class="text-xs text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($peminjaman->peminjam_tipe) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $peminjaman->project->nama ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $peminjaman->tanggal_peminjaman->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">
                                Kembali: {{ $peminjaman->tanggal_rencana_kembali->format('d/m/Y') }}
                                @if($peminjaman->is_terlambat)
                                    <span class="text-red-600 font-medium">({{ $peminjaman->keterlambatan }} hari)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($peminjaman->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                @elseif($peminjaman->status_color === 'blue') bg-blue-100 text-blue-800
                                @elseif($peminjaman->status_color === 'green') bg-green-100 text-green-800
                                @elseif($peminjaman->status_color === 'red') bg-red-100 text-red-800
                                @elseif($peminjaman->status_color === 'orange') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $peminjaman->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('perusahaan.peminjaman-aset.show', $peminjaman->hash_id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(in_array($peminjaman->status_peminjaman, ['pending', 'approved']))
                                    <a href="{{ route('perusahaan.peminjaman-aset.edit', $peminjaman->hash_id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(in_array($peminjaman->status_peminjaman, ['approved', 'dipinjam', 'dikembalikan']))
                                    <a href="{{ route('perusahaan.peminjaman-aset.export-bukti', $peminjaman->hash_id) }}" class="text-green-600 hover:text-green-900" title="Export Bukti">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                                @if(in_array($peminjaman->status_peminjaman, ['pending', 'ditolak']))
                                    <button onclick="confirmDelete('{{ $peminjaman->hash_id }}', '{{ $peminjaman->kode_peminjaman }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-handshake text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada data peminjaman</p>
                                <p class="text-sm">Klik tombol "Tambah Peminjaman" untuk menambah peminjaman baru</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($peminjamans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $peminjamans->firstItem() }} sampai {{ $peminjamans->lastItem() }} 
                    dari {{ $peminjamans->total() }} data peminjaman
                </div>
                <div>
                    {{ $peminjamans->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Total: {{ $peminjamans->total() }} data peminjaman
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus peminjaman "<span id="deleteItemName"></span>"?</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Export Laporan Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Export Laporan Peminjaman Aset</h3>
            <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="exportForm" method="GET" action="{{ route('perusahaan.peminjaman-aset.export-laporan') }}">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <select name="project_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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
                        <select name="status_peminjaman" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status_peminjaman') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset</label>
                        <select name="aset_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Semua Tipe</option>
                            @foreach($asetTypeOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('aset_type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Khusus</label>
                        <select name="terlambat" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Semua</option>
                            <option value="1" {{ request('terlambat') == '1' ? 'selected' : '' }}>Hanya Terlambat</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari</label>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kode, aset, peminjam..." 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Informasi Export:</p>
                            <ul class="text-xs space-y-1">
                                <li>• Laporan akan diexport dalam format PDF</li>
                                <li>• Filter yang sama dengan halaman ini akan diterapkan</li>
                                <li>• Laporan mencakup ringkasan statistik dan detail peminjaman</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(hashId, kodePeminjaman) {
    document.getElementById('deleteItemName').textContent = kodePeminjaman;
    document.getElementById('deleteForm').action = `/perusahaan/peminjaman-aset/${hashId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function showExportModal() {
    // Copy current filter values to export modal
    const currentForm = document.getElementById('filterForm');
    const exportForm = document.getElementById('exportForm');
    
    // Copy all form values
    const formData = new FormData(currentForm);
    for (let [key, value] of formData.entries()) {
        const exportField = exportForm.querySelector(`[name="${key}"]`);
        if (exportField) {
            if (exportField.type === 'checkbox' || exportField.type === 'radio') {
                exportField.checked = value === exportField.value;
            } else {
                exportField.value = value;
            }
        }
    }
    
    document.getElementById('exportModal').classList.remove('hidden');
    document.getElementById('exportModal').classList.add('flex');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
    document.getElementById('exportModal').classList.remove('flex');
}

// Close modals when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

document.getElementById('exportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeExportModal();
    }
});

// Handle export form submission
document.getElementById('exportForm').addEventListener('submit', function(e) {
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';
    submitBtn.disabled = true;
    
    // Re-enable button after a delay (PDF generation time)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        closeExportModal();
    }, 3000);
});
</script>
@endpush