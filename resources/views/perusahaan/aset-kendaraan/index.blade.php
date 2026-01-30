@extends('perusahaan.layouts.app')

@section('title', 'Aset Kendaraan')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Aset Kendaraan</h1>
            <p class="text-gray-600 mt-1">Kelola data kendaraan operasional perusahaan</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('perusahaan.aset-kendaraan.expiring-documents') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Dokumen Expired
            </a>
            <div class="relative">
                <button onclick="toggleExportDropdown()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-barcode mr-2"></i>
                    Export Label
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-2">
                        <button onclick="exportSelected()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-check-square mr-3 text-blue-500"></i>
                            Export Terpilih
                        </button>
                        <a href="{{ route('perusahaan.aset-kendaraan.export-all-labels', request()->query()) }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-list mr-3 text-green-500"></i>
                            Export Semua (Filter)
                        </a>
                    </div>
                </div>
            </div>
            <a href="{{ route('perusahaan.aset-kendaraan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kendaraan
            </a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('perusahaan.aset-kendaraan.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
        <div>
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
            <select name="jenis_kendaraan" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Jenis</option>
                @foreach($jenisOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('jenis_kendaraan') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <select name="status_kendaraan" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('status_kendaraan') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="lg:col-span-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari kode, nopol, merk..." 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        
        <div>
            <select name="per_page" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kendaraan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Polisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kendaraans as $kendaraan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="selected_ids[]" value="{{ $kendaraan->hash_id }}" class="vehicle-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="updateSelectAllState()">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($kendaraan->foto_kendaraan)
                                        <img class="h-12 w-12 rounded-lg object-cover" src="{{ $kendaraan->foto_url }}" alt="Foto Kendaraan">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-{{ $kendaraan->jenis_kendaraan === 'mobil' ? 'car' : 'motorcycle' }} text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $kendaraan->kode_kendaraan }}</div>
                                    <div class="text-sm text-gray-500">{{ $kendaraan->merk }} {{ $kendaraan->model }}</div>
                                    <div class="text-xs text-gray-400">{{ $kendaraan->tahun_pembuatan }} • {{ $kendaraan->warna }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $kendaraan->nomor_polisi }}</div>
                            <div class="text-xs text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $kendaraan->jenis_label }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $kendaraan->project->nama ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $kendaraan->driver_utama ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $kendaraan->formatted_harga_pembelian }}</div>
                            @if($kendaraan->nilai_penyusutan > 0)
                                <div class="text-xs text-green-600">Sekarang: {{ $kendaraan->formatted_nilai_sekarang }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                <!-- STNK Status -->
                                <div class="flex items-center text-xs">
                                    <span class="w-16 flex-shrink-0">STNK:</span>
                                    @if($kendaraan->tanggal_berlaku_stnk)
                                        @php
                                            $stnkDaysLeft = floor(now()->diffInDays($kendaraan->tanggal_berlaku_stnk, false));
                                        @endphp
                                        @if($stnkDaysLeft < 0)
                                            <span class="text-red-600 font-medium">Lewat {{ abs($stnkDaysLeft) }} hari</span>
                                        @elseif($stnkDaysLeft <= 30)
                                            <span class="text-orange-600 font-medium">{{ $stnkDaysLeft }} hari lagi</span>
                                        @else
                                            <span class="text-green-600">{{ $kendaraan->tanggal_berlaku_stnk->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                                
                                <!-- Asuransi Status -->
                                <div class="flex items-center text-xs">
                                    <span class="w-16 flex-shrink-0">Asuransi:</span>
                                    @if($kendaraan->tanggal_berlaku_asuransi)
                                        @php
                                            $asuransiDaysLeft = floor(now()->diffInDays($kendaraan->tanggal_berlaku_asuransi, false));
                                        @endphp
                                        @if($asuransiDaysLeft < 0)
                                            <span class="text-red-600 font-medium">Lewat {{ abs($asuransiDaysLeft) }} hari</span>
                                        @elseif($asuransiDaysLeft <= 30)
                                            <span class="text-orange-600 font-medium">{{ $asuransiDaysLeft }} hari lagi</span>
                                        @else
                                            <span class="text-green-600">{{ $kendaraan->tanggal_berlaku_asuransi->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($kendaraan->status_kendaraan === 'aktif') bg-green-100 text-green-800
                                @elseif($kendaraan->status_kendaraan === 'maintenance') bg-yellow-100 text-yellow-800
                                @elseif($kendaraan->status_kendaraan === 'rusak') bg-red-100 text-red-800
                                @elseif($kendaraan->status_kendaraan === 'dijual') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $kendaraan->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('perusahaan.aset-kendaraan.show', $kendaraan->hash_id) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('perusahaan.aset-kendaraan.edit', $kendaraan->hash_id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('perusahaan.aset-kendaraan.export-label', $kendaraan->hash_id) }}" class="text-green-600 hover:text-green-900" title="Export Label">
                                    <i class="fas fa-barcode"></i>
                                </a>
                                @if($kendaraan->status_kendaraan === 'aktif')
                                    <a href="{{ route('perusahaan.disposal-aset.create', ['asset_type' => 'aset_kendaraan', 'asset_id' => $kendaraan->id]) }}" class="text-orange-600 hover:text-orange-900" title="Disposal Aset">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                @endif
                                <button onclick="confirmDelete('{{ $kendaraan->hash_id }}', '{{ $kendaraan->nomor_polisi }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-car text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada data kendaraan</p>
                                <p class="text-sm">Klik tombol "Tambah Kendaraan" untuk menambah data kendaraan baru</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($kendaraans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $kendaraans->firstItem() }} sampai {{ $kendaraans->lastItem() }} 
                    dari {{ $kendaraans->total() }} data kendaraan
                </div>
                <div>
                    {{ $kendaraans->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Total: {{ $kendaraans->total() }} data kendaraan
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus kendaraan "<span id="deleteItemName"></span>"?</p>
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
<script>
function confirmDelete(hashId, nomorPolisi) {
    document.getElementById('deleteItemName').textContent = nomorPolisi;
    document.getElementById('deleteForm').action = `/perusahaan/aset-kendaraan/${hashId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Export dropdown functionality
function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('exportDropdown');
    const button = e.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().indexOf('toggleExportDropdown') === -1) {
        dropdown.classList.add('hidden');
    }
});

// Checkbox functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateExportButtonState();
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const selectAll = document.getElementById('selectAll');
    const checkedBoxes = document.querySelectorAll('.vehicle-checkbox:checked');
    
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
    
    updateExportButtonState();
}

function updateExportButtonState() {
    const checkedBoxes = document.querySelectorAll('.vehicle-checkbox:checked');
    const exportButton = document.querySelector('button[onclick="exportSelected()"]');
    
    if (exportButton) {
        if (checkedBoxes.length > 0) {
            exportButton.classList.remove('text-gray-400');
            exportButton.classList.add('text-gray-700');
            exportButton.disabled = false;
        } else {
            exportButton.classList.remove('text-gray-700');
            exportButton.classList.add('text-gray-400');
            exportButton.disabled = true;
        }
    }
}

// Export selected vehicles
function exportSelected() {
    const checkedBoxes = document.querySelectorAll('.vehicle-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Kendaraan',
            text: 'Silakan pilih minimal satu kendaraan untuk diekspor',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("perusahaan.aset-kendaraan.export-labels") }}';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add selected IDs
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Close dropdown
    document.getElementById('exportDropdown').classList.add('hidden');
}

// Initialize export button state
document.addEventListener('DOMContentLoaded', function() {
    updateExportButtonState();
});
</script>
@endpush