@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Master Regu Patroli</h1>
            <p class="text-gray-600">Kelola regu patroli berdasarkan project dan shift kerja</p>
        </div>
        <a 
            href="{{ route('perusahaan.tim-patroli.create') }}"
            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center justify-center gap-2"
        >
            <i class="fas fa-plus"></i>
            Tambah Regu
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <!-- Filters -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Filter Project -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Filter Project</label>
                    <select 
                        id="filterProject"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">📦 Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jenis Regu -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Filter Jenis Regu</label>
                    <select 
                        id="filterJenisRegu"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">🚶 Semua Jenis</option>
                        <option value="POS JAGA" {{ request('jenis_regu') == 'POS JAGA' ? 'selected' : '' }}>🏢 POS JAGA</option>
                        <option value="PATROLI MOBIL" {{ request('jenis_regu') == 'PATROLI MOBIL' ? 'selected' : '' }}>🚗 PATROLI MOBIL</option>
                        <option value="PATROLI MOTOR" {{ request('jenis_regu') == 'PATROLI MOTOR' ? 'selected' : '' }}>🏍️ PATROLI MOTOR</option>
                    </select>
                </div>

                <!-- Filter Shift -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Filter Shift</label>
                    <select 
                        id="filterShift"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">🕐 Semua Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->nama_shift }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Filter Status</label>
                    <select 
                        id="filterStatus"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">📊 Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="flex items-end">
                    <button 
                        onclick="resetFilters()"
                        class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition inline-flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-redo"></i>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container relative">
            <div class="scroll-indicator" id="scrollIndicator">
                <i class="fas fa-arrows-alt-h mr-1"></i>
                Scroll untuk melihat lebih banyak
            </div>
            <div class="overflow-x-auto" id="tableContainer">
                <div class="min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nama Regu</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jenis Regu</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Shift</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Project</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Area</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Rute</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Checkpoint</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Inventaris</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Kuesioner</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Pemeriksaan</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap sticky right-0 bg-gray-50">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($timPatrolis as $tim)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $tim->nama_tim }}</p>
                                            @if($tim->leader)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-user-tie mr-1"></i>Danru: {{ $tim->leader->name }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $jenisReguConfig = [
                                                'POS JAGA' => ['icon' => '🏢', 'color' => 'bg-blue-100 text-blue-800'],
                                                'PATROLI MOBIL' => ['icon' => '🚗', 'color' => 'bg-green-100 text-green-800'],
                                                'PATROLI MOTOR' => ['icon' => '🏍️', 'color' => 'bg-purple-100 text-purple-800']
                                            ];
                                            $config = $jenisReguConfig[$tim->jenis_regu] ?? ['icon' => '❓', 'color' => 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="px-3 py-1 {{ $config['color'] }} rounded-full text-xs font-medium">
                                            {{ $config['icon'] }} {{ $tim->jenis_regu }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap shift-info">
                                        @if($tim->shift)
                                            <div class="text-sm">
                                                <p class="font-medium text-gray-900">{{ $tim->shift->nama_shift }}</p>
                                                <p class="text-xs text-gray-500">{{ $tim->shift->jam_mulai }} - {{ $tim->shift->jam_selesai }}</p>
                                            </div>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                                Tidak ada shift
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm text-gray-900">{{ $tim->project->nama ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full text-xs font-bold cursor-help"
                                            data-tippy-content="<div class='text-left'><strong class='block mb-2 text-blue-600'>📍 Area Patroli:</strong>@if($tim->areas->count() > 0)@foreach($tim->areas as $area)<div class='py-1'>• {{ $area->nama }}</div>@endforeach @else<div class='text-gray-500 italic'>Belum ada area</div>@endif</div>"
                                            data-tippy-allowHTML="true"
                                        >
                                            {{ $tim->areas_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-800 rounded-full text-xs font-bold cursor-help"
                                            data-tippy-content="<div class='text-left'><strong class='block mb-2 text-green-600'>🛣️ Rute Patroli:</strong>@if($tim->rutes->count() > 0)@foreach($tim->rutes as $rute)<div class='py-1'>• {{ $rute->nama }}</div>@endforeach @else<div class='text-gray-500 italic'>Belum ada rute</div>@endif</div>"
                                            data-tippy-allowHTML="true"
                                        >
                                            {{ $tim->rutes_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-800 rounded-full text-xs font-bold cursor-help"
                                            data-tippy-content="<div class='text-left'><strong class='block mb-2 text-purple-600'>📌 Checkpoint:</strong>@if($tim->checkpoints->count() > 0)@foreach($tim->checkpoints as $checkpoint)<div class='py-1'>• {{ $checkpoint->nama }}</div>@endforeach @else<div class='text-gray-500 italic'>Belum ada checkpoint</div>@endif</div>"
                                            data-tippy-allowHTML="true"
                                        >
                                            {{ $tim->checkpoints_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-cyan-100 text-cyan-800 rounded-full text-xs font-bold cursor-help"
                                            data-tippy-content="<div class='text-left'><strong class='block mb-2 text-cyan-600'>📦 Inventaris:</strong>@if($tim->inventaris->count() > 0)@foreach($tim->inventaris as $item)<div class='py-1'>• {{ $item->nama }}</div>@endforeach @else<div class='text-gray-500 italic'>Belum ada inventaris</div>@endif</div>"
                                            data-tippy-allowHTML="true"
                                        >
                                            {{ $tim->inventaris_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-pink-100 text-pink-800 rounded-full text-xs font-bold cursor-help"
                                            data-tippy-content="<div class='text-left'><strong class='block mb-2 text-pink-600'>📋 Kuesioner:</strong>@if($tim->kuesioners->count() > 0)@foreach($tim->kuesioners as $kuesioner)<div class='py-1'>• {{ $kuesioner->judul }}</div>@endforeach @else<div class='text-gray-500 italic'>Belum ada kuesioner</div>@endif</div>"
                                            data-tippy-allowHTML="true"
                                        >
                                            {{ $tim->kuesioners_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center justify-center w-8 h-8 bg-amber-100 text-amber-800 rounded-full text-xs font-bold cursor-help"
                                            data-tippy-content="<div class='text-left'><strong class='block mb-2 text-amber-600'>🔍 Pemeriksaan:</strong>@if($tim->pemeriksaans->count() > 0)@foreach($tim->pemeriksaans as $pemeriksaan)<div class='py-1'>• {{ $pemeriksaan->nama }}</div>@endforeach @else<div class='text-gray-500 italic'>Belum ada pemeriksaan</div>@endif</div>"
                                            data-tippy-allowHTML="true"
                                        >
                                            {{ $tim->pemeriksaans_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($tim->is_active)
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                <i class="fas fa-check-circle mr-1"></i>Aktif
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                <i class="fas fa-times-circle mr-1"></i>Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap sticky right-0 bg-white">
                                        <div class="flex items-center justify-center gap-2">
                                            <a 
                                                href="{{ route('perusahaan.tim-patroli.anggota.index', $tim->hash_id) }}"
                                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                                title="Anggota Regu"
                                            >
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <a 
                                                href="{{ route('perusahaan.tim-patroli.edit', $tim->hash_id) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button 
                                                onclick="deleteItem('{{ $tim->hash_id }}')"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                title="Hapus"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                        <p>Belum ada regu patroli</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($timPatrolis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $timPatrolis->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<style>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Sticky action column shadow */
.sticky.right-0 {
    box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
}

/* Table responsive improvements */
.min-w-full {
    min-width: 1200px;
}

/* Scroll indicator */
.table-container {
    position: relative;
}

.scroll-indicator {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    background: rgba(59, 130, 246, 0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 10;
}

.scroll-indicator.show {
    opacity: 1;
}

/* Improved shift display */
.shift-info {
    min-width: 120px;
}
</style>

<script>
// Initialize Tippy.js tooltips
document.addEventListener('DOMContentLoaded', function() {
    tippy('[data-tippy-content]', {
        theme: 'light-border',
        placement: 'top',
        arrow: true,
        animation: 'scale',
        duration: [200, 150],
        maxWidth: 350,
        interactive: true,
    });

    // Handle scroll indicator
    const tableContainer = document.getElementById('tableContainer');
    const scrollIndicator = document.getElementById('scrollIndicator');
    
    function checkScroll() {
        const canScrollRight = tableContainer.scrollLeft < (tableContainer.scrollWidth - tableContainer.clientWidth);
        const canScrollLeft = tableContainer.scrollLeft > 0;
        
        if (canScrollRight || canScrollLeft) {
            scrollIndicator.classList.add('show');
        } else {
            scrollIndicator.classList.remove('show');
        }
    }
    
    // Check scroll on load
    setTimeout(checkScroll, 100);
    
    // Check scroll on resize
    window.addEventListener('resize', checkScroll);
    
    // Hide indicator when scrolling
    tableContainer.addEventListener('scroll', function() {
        scrollIndicator.classList.remove('show');
        
        // Show again after scroll stops
        clearTimeout(this.scrollTimeout);
        this.scrollTimeout = setTimeout(checkScroll, 1000);
    });
});

// Filter handlers
document.getElementById('filterProject').addEventListener('change', applyFilters);
document.getElementById('filterJenisRegu').addEventListener('change', applyFilters);
document.getElementById('filterShift').addEventListener('change', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);

function applyFilters() {
    const project = document.getElementById('filterProject').value;
    const jenisRegu = document.getElementById('filterJenisRegu').value;
    const shift = document.getElementById('filterShift').value;
    const status = document.getElementById('filterStatus').value;
    
    const params = new URLSearchParams();
    if (project) params.append('project_id', project);
    if (jenisRegu) params.append('jenis_regu', jenisRegu);
    if (shift) params.append('shift_id', shift);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("perusahaan.tim-patroli.master") }}?' + params.toString();
}

function resetFilters() {
    window.location.href = '{{ route("perusahaan.tim-patroli.master") }}';
}

function deleteItem(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data tim patroli dan semua assignment akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('perusahaan/tim-patroli') }}/${hashId}`;
            form.submit();
        }
    });
}

// Success/Error Messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 2000,
        showConfirmButton: false
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
    });
@endif
</script>
@endpush
@endsection
