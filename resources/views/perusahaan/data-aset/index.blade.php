@extends('perusahaan.layouts.app')

@section('title', 'Data Aset')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Aset</h1>
            <p class="text-gray-600 mt-1">Kelola data aset perusahaan</p>
        </div>
        <div class="flex space-x-2">
            <!-- Export Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Label
                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>
                
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-1">
                        <button onclick="exportSelected()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-check-square mr-3 text-blue-500"></i>
                            Export Terpilih
                        </button>
                        <button onclick="exportAll()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-list mr-3 text-green-500"></i>
                            Export Semua (Filter)
                        </button>
                        <div class="border-t border-gray-100 my-1"></div>
                        <div class="px-4 py-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Label dengan barcode untuk ditempel
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('perusahaan.data-aset.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Aset
            </a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('perusahaan.data-aset.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
            <select name="project_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select name="kategori" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                        {{ $kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, PIC..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Per Halaman</label>
            <select name="per_page" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Aset</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Aset</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Sekarang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dataAsets as $aset)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_assets[]" value="{{ $aset->hash_id }}" class="asset-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="updateSelectAllState()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $aset->kode_aset }}</div>
                            <div class="text-sm text-gray-500">{{ $aset->tanggal_beli->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $aset->nama_aset }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $aset->kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $aset->project->nama }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $aset->formatted_harga_beli }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-green-600">{{ $aset->formatted_nilai_sekarang }}</div>
                            @if($aset->nilai_penyusutan > 0)
                                <div class="text-xs text-gray-500">Penyusutan: {{ $aset->formatted_nilai_penyusutan }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $aset->pic_penanggung_jawab }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($aset->status === 'ada') bg-green-100 text-green-800
                                @elseif($aset->status === 'rusak') bg-red-100 text-red-800
                                @elseif($aset->status === 'dijual') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $aset->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('perusahaan.data-aset.export-label', $aset->hash_id) }}" class="text-green-600 hover:text-green-900" title="Export Label">
                                    <i class="fas fa-barcode"></i>
                                </a>
                                <a href="{{ route('perusahaan.data-aset.show', $aset->hash_id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('perusahaan.data-aset.edit', $aset->hash_id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($aset->status === 'ada')
                                    <a href="{{ route('perusahaan.disposal-aset.create', ['asset_type' => 'data_aset', 'asset_id' => $aset->id]) }}" class="text-orange-600 hover:text-orange-900" title="Disposal Aset">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                @endif
                                <button onclick="confirmDelete('{{ $aset->hash_id }}', '{{ $aset->nama_aset }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada data aset</p>
                                <p class="text-sm">Klik tombol "Tambah Aset" untuk menambah data aset baru</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($dataAsets->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $dataAsets->firstItem() }} sampai {{ $dataAsets->lastItem() }} 
                    dari {{ $dataAsets->total() }} data aset
                </div>
                <div>
                    {{ $dataAsets->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Total: {{ $dataAsets->total() }} data aset
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus aset "<span id="deleteItemName"></span>"?</p>
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

@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function confirmDelete(hashId, namaAset) {
    document.getElementById('deleteItemName').textContent = namaAset;
    document.getElementById('deleteForm').action = `/perusahaan/data-aset/${hashId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Select All functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    const selectAll = document.getElementById('selectAll');
    const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
    }
}

// Export functions
function exportSelected() {
    const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Aset',
            text: 'Silakan pilih minimal satu aset untuk diekspor',
        });
        return;
    }
    
    const assetIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    Swal.fire({
        title: 'Export Label Terpilih?',
        text: `Akan mengexport ${assetIds.length} label aset ke PDF`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Export!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.data-aset.export-labels") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add asset IDs
            assetIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'asset_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function exportAll() {
    Swal.fire({
        title: 'Export Semua Label?',
        text: 'Akan mengexport semua label aset (sesuai filter) ke PDF',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Export Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get current filter parameters
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.data-aset.export-all-labels") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add current filter parameters
            const urlParams = new URLSearchParams(window.location.search);
            for (const [key, value] of urlParams) {
                if (key !== 'page') { // Exclude page parameter
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
            }
            
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