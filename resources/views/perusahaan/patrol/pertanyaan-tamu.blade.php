@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Inventaris Patroli</h1>
        <p class="text-gray-600">Kelola inventaris dan peralatan tim patroli</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a 
                    href="{{ route('perusahaan.patrol.inventaris-patroli') }}"
                    class="tab-button {{ request()->routeIs('perusahaan.patrol.inventaris-patroli') ? 'active border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} px-6 py-4 text-sm font-semibold border-b-2 flex items-center gap-2"
                >
                    <i class="fas fa-box"></i>
                    Inventaris
                </a>
                <a 
                    href="{{ route('perusahaan.patrol.kuesioner-patroli') }}"
                    class="tab-button {{ request()->routeIs('perusahaan.patrol.kuesioner-patroli*') ? 'active border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} px-6 py-4 text-sm font-semibold border-b-2 flex items-center gap-2"
                >
                    <i class="fas fa-clipboard-list"></i>
                    Kuesioner
                </a>
                <a 
                    href="{{ route('perusahaan.patrol.pemeriksaan-patroli') }}"
                    class="tab-button {{ request()->routeIs('perusahaan.patrol.pemeriksaan-patroli*') ? 'active border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} px-6 py-4 text-sm font-semibold border-b-2 flex items-center gap-2"
                >
                    <i class="fas fa-clipboard-check"></i>
                    Pemeriksaan
                </a>
                <a 
                    href="{{ route('perusahaan.patrol.pertanyaan-tamu') }}"
                    class="tab-button {{ request()->routeIs('perusahaan.patrol.pertanyaan-tamu*') ? 'active border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} px-6 py-4 text-sm font-semibold border-b-2 flex items-center gap-2"
                >
                    <i class="fas fa-users"></i>
                    Pertanyaan Tamu
                </a>
            </nav>
        </div>
    </div>

    <!-- Tab Content: Pertanyaan Tamu -->
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Toolbar -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 flex flex-col md:flex-row gap-3">
                    <!-- Search -->
                    <div class="relative flex-1 max-w-md">
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Cari kuesioner tamu..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ request('search') }}"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <!-- Filter Project -->
                    <select 
                        id="filterProject"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Area -->
                    <select 
                        id="filterArea"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" data-project="{{ $area->project_id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->nama }} ({{ $area->project->nama }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Status -->
                    <select 
                        id="filterStatus"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <button 
                    onclick="openModal()"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center justify-center gap-2"
                >
                    <i class="fas fa-plus"></i>
                    Tambah Kuesioner Tamu
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul Kuesioner</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Area</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Pertanyaan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kuesionerTamus as $kuesioner)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $kuesioner->judul }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                {{ $kuesioner->project->nama }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    {{ $kuesioner->area->nama }}
                                </span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-lock mr-1"></i>Terkunci
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ Str::limit($kuesioner->deskripsi, 50) ?: '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                {{ $kuesioner->pertanyaans_count }} pertanyaan
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($kuesioner->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-times-circle mr-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a 
                                    href="{{ route('perusahaan.patrol.pertanyaan-tamu.kelola', $kuesioner->hash_id) }}"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                    title="Kelola Pertanyaan"
                                >
                                    <i class="fas fa-cogs"></i>
                                </a>
                                <a 
                                    href="{{ route('perusahaan.patrol.pertanyaan-tamu.preview', $kuesioner->hash_id) }}"
                                    class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                    title="Preview"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button 
                                    onclick="editItem('{{ $kuesioner->hash_id }}')"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button 
                                    onclick="deleteItem('{{ $kuesioner->hash_id }}')"
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
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada kuesioner tamu</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kuesionerTamus->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $kuesionerTamus->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <form id="kuesionerForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Kuesioner Tamu</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="project_id" 
                        id="project_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                        onchange="loadAreas()"
                    >
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Area -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Area <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="area_id" 
                        id="area_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                        disabled
                    >
                        <option value="">Pilih Area</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Setiap area hanya boleh memiliki 1 kuesioner tamu
                    </p>
                </div>

                <!-- Judul Kuesioner -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Kuesioner <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        id="judul"
                        placeholder="Contoh: Kuesioner Kepuasan Tamu"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        name="deskripsi" 
                        id="deskripsi"
                        rows="3"
                        placeholder="Deskripsi kuesioner..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    ></textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="is_active" value="1" class="mr-2" checked>
                            <span class="text-sm">Aktif</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="is_active" value="0" class="mr-2">
                            <span class="text-sm">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="closeModal()"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Search & Filter
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

document.getElementById('filterProject').addEventListener('change', function() {
    filterAreasByProject();
    applyFilters();
});
document.getElementById('filterArea').addEventListener('change', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const project = document.getElementById('filterProject').value;
    const area = document.getElementById('filterArea').value;
    const status = document.getElementById('filterStatus').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (project) params.append('project_id', project);
    if (area) params.append('area_id', area);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("perusahaan.patrol.pertanyaan-tamu") }}?' + params.toString();
}

function filterAreasByProject() {
    const projectId = document.getElementById('filterProject').value;
    const areaSelect = document.getElementById('filterArea');
    const options = areaSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const optionProject = option.getAttribute('data-project');
            option.style.display = (!projectId || optionProject === projectId) ? 'block' : 'none';
        }
    });
    
    // Reset area selection if current selection is not visible
    const currentArea = areaSelect.value;
    if (currentArea) {
        const currentOption = areaSelect.querySelector(`option[value="${currentArea}"]`);
        if (currentOption && currentOption.style.display === 'none') {
            areaSelect.value = '';
        }
    }
}

// Modal Functions
function openModal() {
    document.getElementById('formModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Kuesioner Tamu';
    document.getElementById('kuesionerForm').action = '{{ route("perusahaan.patrol.pertanyaan-tamu.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('kuesionerForm').reset();
    document.getElementById('area_id').disabled = true;
    
    // Clear edit variables
    window.editAreaId = null;
    window.editKuesionerTamuId = null;
}

function closeModal() {
    document.getElementById('formModal').classList.add('hidden');
    document.getElementById('kuesionerForm').reset();
    document.getElementById('area_id').disabled = true;
    
    // Clear edit variables
    window.editAreaId = null;
    window.editKuesionerTamuId = null;
}

function loadAreas() {
    const projectId = document.getElementById('project_id').value;
    const areaSelect = document.getElementById('area_id');
    const kuesionerTamuId = document.getElementById('formMethod').value === 'PUT' ? 
        window.editKuesionerTamuId : null;
    
    if (!projectId) {
        areaSelect.innerHTML = '<option value="">Pilih Area</option>';
        areaSelect.disabled = true;
        return;
    }
    
    let url = `{{ route('perusahaan.patrol.get-areas-by-project') }}?project_id=${projectId}`;
    if (kuesionerTamuId) {
        url += `&kuesioner_tamu_id=${kuesionerTamuId}`;
    }
    
    console.log('Loading areas from URL:', url);
    
    // Show loading state
    areaSelect.innerHTML = '<option value="">Loading areas...</option>';
    areaSelect.disabled = true;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Areas loaded:', data);
            
            // Check if response has error
            if (data.error) {
                throw new Error(data.error);
            }
            
            const areas = Array.isArray(data) ? data : [];
            
            areaSelect.innerHTML = '<option value="">Pilih Area</option>';
            if (areas.length === 0) {
                areaSelect.innerHTML += '<option value="" disabled>Semua area sudah memiliki kuesioner tamu</option>';
            } else {
                areas.forEach(area => {
                    areaSelect.innerHTML += `<option value="${area.id}">${area.nama}</option>`;
                });
            }
            areaSelect.disabled = false;
            
            // Set the area value if we're in edit mode
            if (window.editAreaId) {
                console.log('Setting area value to:', window.editAreaId);
                areaSelect.value = window.editAreaId;
                window.editAreaId = null; // Clear after use
            }
        })
        .catch(error => {
            console.error('Error loading areas:', error);
            areaSelect.innerHTML = '<option value="">Error loading areas</option>';
            areaSelect.disabled = true;
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Gagal memuat data area: ${error.message}`
            });
        });
}

function editItem(hashId) {
    fetch(`{{ url('perusahaan/patrol/pertanyaan-tamu') }}/${hashId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('formModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Kuesioner Tamu';
            document.getElementById('kuesionerForm').action = `{{ url('perusahaan/patrol/pertanyaan-tamu') }}/${hashId}`;
            document.getElementById('formMethod').value = 'PUT';
            
            document.getElementById('project_id').value = data.project_id;
            
            // Store the area_id to set after areas are loaded
            window.editAreaId = data.area_id;
            window.editKuesionerTamuId = hashId;
            
            loadAreas();
            
            document.getElementById('judul').value = data.judul;
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.querySelector(`input[name="is_active"][value="${data.is_active ? '1' : '0'}"]`).checked = true;
        })
        .catch(error => {
            console.error('Error loading kuesioner data:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data kuesioner'
            });
        });
}

function deleteItem(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('perusahaan/patrol/pertanyaan-tamu') }}/${hashId}`;
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

// Initialize filter on page load
document.addEventListener('DOMContentLoaded', function() {
    filterAreasByProject();
});
</script>
@endpush
@endsection