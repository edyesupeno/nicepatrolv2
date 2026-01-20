@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Area')
@section('page-title', 'Manajemen Area')
@section('page-subtitle', 'Kelola area di setiap project')

@section('content')
<!-- CRITICAL ALERT: Project Tanpa Area -->
@if($projectsWithoutAreas->count() > 0)
<div class="mb-6 bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-500 rounded-xl shadow-lg">
    <div class="p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-red-800">
                        <i class="fas fa-warning mr-2"></i>Perhatian: Project Tanpa Area Kerja
                    </h3>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                        {{ $projectsWithoutAreas->count() }} Project
                    </span>
                </div>
                <p class="text-red-700 text-sm mb-4 leading-relaxed">
                    <strong>{{ $projectsWithoutAreas->count() }} project</strong> belum memiliki area kerja yang akan <strong>berpengaruh pada {{ $affectedKaryawanCount }} karyawan aktif</strong>. 
                    @if($affectedKaryawanCount > 0)
                        Karyawan tidak akan memiliki area tugas dan tidak dapat menggunakan fitur penerimaan barang dengan benar.
                    @else
                        Meskipun belum ada karyawan yang terpengaruh, sebaiknya area ditambahkan sebelum ada penugasan karyawan.
                    @endif
                </p>
                
                <div class="bg-white rounded-lg p-4 mb-4 border border-red-200">
                    <h4 class="font-semibold text-red-800 mb-2">
                        <i class="fas fa-list mr-2"></i>Project yang perlu ditambahkan area:
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($projectsWithoutAreas as $project)
                        @php
                            $karyawanCount = \App\Models\Karyawan::where('project_id', $project->id)->where('is_active', true)->count();
                        @endphp
                        <div class="flex items-center justify-between bg-red-50 px-3 py-2 rounded-lg border border-red-200">
                            <div class="flex items-center">
                                <i class="fas fa-project-diagram text-red-600 mr-2"></i>
                                <div>
                                    <span class="text-sm font-medium text-red-800 block">{{ $project->nama }}</span>
                                    @if($karyawanCount > 0)
                                        <span class="text-xs text-red-600">{{ $karyawanCount }} karyawan terpengaruh</span>
                                    @else
                                        <span class="text-xs text-gray-500">Belum ada karyawan</span>
                                    @endif
                                </div>
                            </div>
                            <button 
                                onclick="addAreaForProject({{ $project->id }}, '{{ $project->nama }}')"
                                class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition"
                                title="Tambah Area untuk {{ $project->nama }}"
                            >
                                <i class="fas fa-plus mr-1"></i>Tambah
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        onclick="openCreateModal()"
                        class="px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition shadow-lg inline-flex items-center justify-center"
                    >
                        <i class="fas fa-plus mr-2"></i>Tambah Area Sekarang
                    </button>
                    <div class="text-xs text-red-600 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Setiap project harus memiliki minimal 1 area untuk operasional yang optimal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-map-marked-alt text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Area</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $areas->total() }}</p>
                </div>
            </div>
        </div>
    </div>
    <button onclick="openCreateModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Area
    </button>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <!-- Search Input -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Cari nama area, alamat, atau project..."
                class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
        </div>

        <!-- Filter Project -->
        <div class="lg:w-64">
            <select 
                name="project_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button 
                type="submit"
                class="px-6 py-3 rounded-xl font-medium transition text-white"
                style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
            >
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request('search') || request('project_id'))
            <a 
                href="{{ route('perusahaan.areas.index') }}"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition"
            >
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Nama Area
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marked-alt mr-2" style="color: #3B82C8;"></i>Alamat
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($areas as $area)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <i class="fas fa-map-marker-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $area->nama }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $area->hash_id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="px-3 py-1 rounded-lg text-xs font-medium text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                {{ $area->project->nama }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $area->alamat ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button 
                                onclick="openEditModal('{{ $area->hash_id }}')" 
                                class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                                title="Edit Area"
                            >
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-map-marked-alt text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('project_id'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada data area
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('project_id'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan area pertama Anda untuk memulai
                                @endif
                            </p>
                            @if(request('search') || request('project_id'))
                                <a 
                                    href="{{ route('perusahaan.areas.index') }}"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition inline-flex items-center"
                                >
                                    <i class="fas fa-redo mr-2"></i>Reset Pencarian
                                </a>
                            @else
                                <button 
                                    onclick="openCreateModal()" 
                                    class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center text-white shadow-lg hover:shadow-xl" 
                                    style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
                                >
                                    <i class="fas fa-plus mr-2"></i>Tambah Area
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($areas->hasPages())
<div class="mt-6">
    {{ $areas->links() }}
</div>
@endif

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.areas.store') }}" method="POST" id="formCreate">
            @csrf
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-map-marked-alt text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tambah Area Baru</h3>
                            <p class="text-sm text-gray-500">Isi form di bawah untuk menambah area</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="project_id" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                    >
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Pilih project untuk area ini</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Nama Area <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        placeholder="Contoh: Lobby Utama, Parkir Basement, dll"
                    >
                    <p class="mt-1 text-xs text-gray-500">Masukkan nama area yang jelas dan deskriptif</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marked-alt mr-2" style="color: #3B82C8;"></i>Alamat
                    </label>
                    <textarea 
                        name="alamat" 
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition resize-none"
                        style="focus:ring-color: #3B82C8;"
                        placeholder="Masukkan alamat lengkap area (opsional)"
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500">Alamat detail untuk memudahkan identifikasi lokasi</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 rounded-b-2xl border-t border-gray-200">
                <div class="flex gap-3">
                    <button 
                        type="button" 
                        onclick="closeCreateModal()"
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition"
                    >
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl"
                        style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
                    >
                        <i class="fas fa-save mr-2"></i>Simpan Area
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Edit Area</h3>
                            <p class="text-sm text-gray-500">Update informasi area</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="project_id" 
                        id="edit_project_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                    >
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Nama Area <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        id="edit_nama"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marked-alt mr-2" style="color: #3B82C8;"></i>Alamat
                    </label>
                    <textarea 
                        name="alamat" 
                        id="edit_alamat"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition resize-none"
                        style="focus:ring-color: #3B82C8;"
                    ></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 rounded-b-2xl border-t border-gray-200">
                <div class="flex gap-3">
                    <button 
                        type="button" 
                        onclick="closeEditModal()"
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition"
                    >
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl"
                        style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
                    >
                        <i class="fas fa-save mr-2"></i>Update Area
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function openCreateModal() {
    document.getElementById('modalCreate').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('modalCreate').classList.add('hidden');
    document.getElementById('formCreate').reset();
}

// NEW: Function untuk menambah area untuk project tertentu
function addAreaForProject(projectId, projectName) {
    // Buka modal create
    openCreateModal();
    
    // Set project yang dipilih
    const projectSelect = document.querySelector('#formCreate select[name="project_id"]');
    projectSelect.value = projectId;
    
    // Highlight project yang dipilih
    projectSelect.style.borderColor = '#ef4444';
    projectSelect.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
    
    // Focus ke nama area
    const namaInput = document.querySelector('#formCreate input[name="nama"]');
    setTimeout(() => {
        namaInput.focus();
        namaInput.placeholder = `Contoh: Area Utama ${projectName}, Lobby ${projectName}, dll`;
    }, 100);
    
    // Show notification
    Swal.fire({
        icon: 'info',
        title: 'Tambah Area',
        text: `Silakan isi nama area untuk project "${projectName}"`,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

async function openEditModal(hashId) {
    try {
        const response = await fetch(`/perusahaan/areas/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_project_id').value = data.project_id;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_alamat').value = data.alamat || '';
        document.getElementById('formEdit').action = `/perusahaan/areas/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data area'
        });
    }
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data area akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/areas/${hashId}`;
            form.submit();
        }
    });
}

// Close modals when clicking outside
document.getElementById('modalCreate')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});

document.getElementById('modalEdit')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Reset project select styling when changed
document.querySelector('#formCreate select[name="project_id"]')?.addEventListener('change', function() {
    this.style.borderColor = '';
    this.style.boxShadow = '';
});
</script>
@endpush
