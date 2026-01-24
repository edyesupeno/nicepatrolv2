@extends('perusahaan.layouts.app')

@section('title', 'Laporan Insiden')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Insiden</h1>
            <p class="text-gray-600 mt-1">Laporan insiden keamanan dari patroli mandiri</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="exportMultiplePdf()" id="exportMultipleBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-file-pdf mr-2"></i>Export PDF Terpilih
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Insiden</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_insiden']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-fire text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Prioritas Kritis</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['insiden_kritis']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Terselesaikan</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['insiden_resolved']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-percentage text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tingkat Penyelesaian</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['resolution_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Filter Laporan</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('perusahaan.laporan-patroli.insiden') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Cari lokasi, jenis kendala, atau petugas...">
                    </div>

                    <!-- Project Filter -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area Filter -->
                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700 mb-2">Area</label>
                        <select name="area_id" id="area_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" data-project="{{ $area->project_id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Kendala Filter -->
                    <div>
                        <label for="jenis_kendala" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kendala</label>
                        <select name="jenis_kendala" id="jenis_kendala" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisKendala as $jenis)
                                <option value="{{ $jenis['value'] }}" {{ request('jenis_kendala') == $jenis['value'] ? 'selected' : '' }}>
                                    {{ $jenis['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Prioritas Filter -->
                    <div>
                        <label for="prioritas" class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                        <select name="prioritas" id="prioritas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Prioritas</option>
                            <option value="kritis" {{ request('prioritas') == 'kritis' ? 'selected' : '' }}>Kritis</option>
                            <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="sedang" {{ request('prioritas') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <!-- Status Laporan Filter -->
                    <div>
                        <label for="status_laporan" class="block text-sm font-medium text-gray-700 mb-2">Status Laporan</label>
                        <select name="status_laporan" id="status_laporan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="submitted" {{ request('status_laporan') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="reviewed" {{ request('status_laporan') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ request('status_laporan') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('perusahaan.laporan-patroli.insiden') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Insiden List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Insiden</h3>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="selectAll" class="text-sm text-gray-600">Pilih Semua</label>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" disabled>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kendala</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($insiden as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="insiden_ids[]" value="{{ $item->id }}" class="insiden-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->waktu_laporan->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->nama_lokasi }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $item->project->nama ?? '-' }}
                                    @if($item->areaPatrol)
                                        • {{ $item->areaPatrol->nama }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $item->jenis_kendala)) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->prioritas_badge }}">
                                    {{ ucfirst($item->prioritas) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->status_laporan_badge }}">
                                    {{ ucfirst($item->status_laporan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->petugas->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('perusahaan.laporan-patroli.insiden.show', $item->hash_id) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('perusahaan.laporan-patroli.insiden.pdf', $item->hash_id) }}" 
                                       class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-exclamation-triangle text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500 text-lg">Tidak ada data insiden</p>
                                    <p class="text-gray-400 text-sm">Belum ada laporan insiden yang sesuai dengan filter yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($insiden->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $insiden->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Export Multiple Form -->
<form id="exportMultipleForm" action="{{ route('perusahaan.laporan-patroli.insiden.export-multiple') }}" method="POST" style="display: none;">
    @csrf
    <div id="selectedInsidenIds"></div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setupCheckboxes();
    setupAreaFilter();
});

function setupCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const insidenCheckboxes = document.querySelectorAll('.insiden-checkbox');
    const exportBtn = document.getElementById('exportMultipleBtn');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        insidenCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateExportButton();
    });

    // Individual checkbox functionality
    insidenCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateExportButton();
        });
    });

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.insiden-checkbox:checked').length;
        const totalCount = insidenCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    function updateExportButton() {
        const checkedCount = document.querySelectorAll('.insiden-checkbox:checked').length;
        exportBtn.disabled = checkedCount === 0;
        exportBtn.textContent = checkedCount > 0 ? 
            `Export PDF (${checkedCount} terpilih)` : 
            'Export PDF Terpilih';
    }
}

function setupAreaFilter() {
    const projectSelect = document.getElementById('project_id');
    const areaSelect = document.getElementById('area_id');
    const areaOptions = Array.from(areaSelect.options);

    projectSelect.addEventListener('change', function() {
        const selectedProjectId = this.value;
        
        // Clear area select
        areaSelect.innerHTML = '<option value="">Semua Area</option>';
        
        if (selectedProjectId) {
            // Filter areas by project
            const filteredAreas = areaOptions.filter(option => 
                option.value === '' || option.dataset.project === selectedProjectId
            );
            
            filteredAreas.forEach(option => {
                if (option.value !== '') {
                    areaSelect.appendChild(option.cloneNode(true));
                }
            });
        } else {
            // Show all areas
            areaOptions.forEach(option => {
                if (option.value !== '') {
                    areaSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    });
}

function exportMultiplePdf() {
    const checkedBoxes = document.querySelectorAll('.insiden-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Pilih minimal satu insiden untuk diekspor');
        return;
    }

    const form = document.getElementById('exportMultipleForm');
    const container = document.getElementById('selectedInsidenIds');
    
    // Clear previous inputs
    container.innerHTML = '';
    
    // Add selected IDs
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'insiden_ids[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });
    
    // Submit form
    form.submit();
}
</script>
@endpush