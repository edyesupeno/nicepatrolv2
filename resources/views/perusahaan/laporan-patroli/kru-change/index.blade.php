@extends('perusahaan.layouts.app')

@section('title', 'Laporan Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Laporan Kru Change</h3>
                <p class="text-sm text-gray-600 mt-1">Laporan lengkap handover kru patroli</p>
            </div>
            <div class="flex space-x-2">
                <button type="button" 
                        onclick="exportSelected()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
                        id="export-selected-btn" 
                        style="display: none;">
                    <i class="fas fa-file-pdf mr-2"></i>Export Selected PDF
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari area, tim..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                    <select name="area_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" 
                           name="tanggal_mulai" 
                           value="{{ request('tanggal_mulai') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" 
                           name="tanggal_selesai" 
                           value="{{ request('tanggal_selesai') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div class="lg:col-span-6 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('perusahaan.laporan-patroli.kru-change.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim Keluar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Handover</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kruChanges as $index => $kruChange)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       name="selected_kru_changes[]" 
                                       value="{{ $kruChange->id }}" 
                                       class="kru-change-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $kruChanges->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $kruChange->project->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $kruChange->areaPatrol->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $kruChange->timKeluar->nama_tim }}</div>
                                <div class="text-xs text-gray-500">{{ $kruChange->timKeluar->jenis_regu }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $kruChange->timMasuk->nama_tim }}</div>
                                <div class="text-xs text-gray-500">{{ $kruChange->timMasuk->jenis_regu }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $kruChange->waktu_mulai_handover->format('d/m/Y H:i') }}
                                @if($kruChange->waktu_selesai_handover)
                                    <div class="text-xs text-gray-500">
                                        Selesai: {{ $kruChange->waktu_selesai_handover->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $kruChange->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('perusahaan.laporan-patroli.kru-change.show', $kruChange->hash_id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('perusahaan.laporan-patroli.kru-change.pdf', $kruChange->hash_id) }}" 
                                   class="text-red-600 hover:text-red-900"
                                   title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>Tidak ada data kru change ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kruChanges->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $kruChanges->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Export Multiple Form -->
<form id="export-multiple-form" action="{{ route('perusahaan.laporan-patroli.kru-change.export-multiple') }}" method="POST" style="display: none;">
    @csrf
    <div id="selected-ids-container"></div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const kruChangeCheckboxes = document.querySelectorAll('.kru-change-checkbox');
    const exportSelectedBtn = document.getElementById('export-selected-btn');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        kruChangeCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleExportButton();
    });

    // Individual checkbox functionality
    kruChangeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.kru-change-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === kruChangeCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < kruChangeCheckboxes.length;
            toggleExportButton();
        });
    });

    function toggleExportButton() {
        const checkedCount = document.querySelectorAll('.kru-change-checkbox:checked').length;
        exportSelectedBtn.style.display = checkedCount > 0 ? 'inline-flex' : 'none';
    }
});

function exportSelected() {
    const checkedBoxes = document.querySelectorAll('.kru-change-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        Swal.fire('Peringatan', 'Pilih minimal satu kru change untuk di-export', 'warning');
        return;
    }

    Swal.fire({
        title: 'Export PDF',
        text: `Export ${checkedBoxes.length} laporan kru change ke PDF?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-download mr-2"></i>Export PDF',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Generating PDF...',
                text: 'Mohon tunggu, sedang membuat file PDF',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepare form
            const form = document.getElementById('export-multiple-form');
            const container = document.getElementById('selected-ids-container');
            container.innerHTML = '';

            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'kru_change_ids[]';
                input.value = checkbox.value;
                container.appendChild(input);
            });

            form.submit();
            
            // Close loading after a delay
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    });
}
</script>
@endpush