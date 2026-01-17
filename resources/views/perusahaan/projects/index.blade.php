@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Project')
@section('page-title', 'Manajemen Project')
@section('page-subtitle', 'Kelola project di setiap area')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center gap-4">
        <p class="text-gray-600">Total: <span class="font-bold text-sky-600">{{ $projects->total() }}</span> project</p>
        
        <!-- Filter Kantor -->
        <form method="GET" class="flex items-center gap-2">
            <select 
                name="kantor_id" 
                onchange="this.form.submit()"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm"
            >
                <option value="">Semua Kantor</option>
                @foreach($kantors as $kantor)
                    <option value="{{ $kantor->id }}" {{ request('kantor_id') == $kantor->id ? 'selected' : '' }}>
                        {{ $kantor->nama }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
        <i class="fas fa-plus mr-2"></i>Tambah Project
    </button>
</div>

<!-- Grid Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($projects as $project)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $project->nama }}</h3>
                    @if($project->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <i class="fas fa-check-circle mr-1"></i>Project Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            Project Selesai
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('perusahaan.projects.contacts.index', $project->hash_id) }}" 
                       class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition" 
                       title="Kontak Penting">
                        <i class="fas fa-address-book text-lg"></i>
                    </a>
                    <button onclick="openEditModal('{{ $project->hash_id }}')" class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition" title="Edit Project">
                        <i class="fas fa-edit text-lg"></i>
                    </button>
                </div>
            </div>

            @if($project->deskripsi)
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $project->deskripsi }}</p>
            @endif

            <div class="border-t border-gray-200 my-4"></div>

            <!-- Info -->
            <div class="space-y-2.5 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-building text-gray-400 mr-2.5 w-4"></i>
                    <span class="font-medium">Kantor:</span>
                    <span class="ml-2">{{ $project->kantor->nama }}</span>
                </div>

                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-globe text-gray-400 mr-2.5 w-4"></i>
                    <span class="font-medium">Timezone:</span>
                    <span class="ml-2">{{ $project->timezone }}</span>
                </div>

                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-map-marked-alt text-gray-400 mr-2.5 w-4"></i>
                    <span class="font-medium">Areas:</span>
                    <span class="ml-2">0 area</span>
                </div>

                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-calendar text-gray-400 mr-2.5 w-4"></i>
                    <span class="font-medium">Durasi:</span>
                    <span class="ml-2">
                        {{ $project->tanggal_mulai->format('d M Y') }} 
                        @if($project->tanggal_selesai)
                            → {{ $project->tanggal_selesai->format('d M Y') }}
                        @else
                            → Sekarang
                        @endif
                    </span>
                </div>
            </div>

            <!-- Struktur Jabatan -->
            <div class="border-t border-gray-200 pt-4 mb-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-users text-gray-600 mr-2"></i>
                    <h4 class="font-semibold text-gray-700">Struktur Jabatan:</h4>
                </div>
                @if(count($project->struktur_jabatan) > 0)
                    <div class="space-y-1.5 text-sm">
                        @foreach($project->struktur_jabatan as $item)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">• {{ $item['jabatan'] }}:</span>
                            <span class="font-semibold text-gray-900">{{ $item['jumlah'] }} orang</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-slash text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada jabatan di project ini</p>
                        <p class="text-xs text-gray-400 mt-1">Tambahkan jabatan ke project untuk melihat struktur</p>
                    </div>
                @endif
            </div>

            <!-- Total Karyawan -->
            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-700">Total Karyawan:</span>
                    <span class="font-bold text-gray-900 text-lg">{{ $project->total_karyawan ?? 0 }} orang</span>
                </div>
            </div>
        </div>

    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-project-diagram text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Belum ada data project</p>
            <p class="text-gray-400 text-sm mb-6">Tambahkan project pertama Anda untuk memulai</p>
            <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Project
            </button>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $projects->links() }}
</div>

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.projects.store') }}" method="POST" id="formCreate">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Project</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kantor <span class="text-red-500">*</span></label>
                        <select 
                            name="kantor_id" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Kantor</option>
                            @foreach($kantors as $kantor)
                                <option value="{{ $kantor->id }}">{{ $kantor->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Project <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="nama" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Nama project"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone <span class="text-red-500">*</span></label>
                        <select 
                            name="timezone" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="Asia/Jakarta">WIB - Waktu Indonesia Barat (UTC+7)</option>
                            <option value="Asia/Makassar">WITA - Waktu Indonesia Tengah (UTC+8)</option>
                            <option value="Asia/Jayapura">WIT - Waktu Indonesia Timur (UTC+9)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih timezone sesuai lokasi project untuk perhitungan waktu absensi yang akurat</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                name="tanggal_mulai" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input 
                                type="date" 
                                name="tanggal_selesai"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea 
                            name="deskripsi" 
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Deskripsi project"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select 
                            name="is_active" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button 
                        type="button" 
                        onclick="closeCreateModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium transition"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Project</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kantor <span class="text-red-500">*</span></label>
                        <select 
                            name="kantor_id" 
                            id="edit_kantor_id"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Kantor</option>
                            @foreach($kantors as $kantor)
                                <option value="{{ $kantor->id }}">{{ $kantor->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Project <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="nama" 
                            id="edit_nama"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone <span class="text-red-500">*</span></label>
                        <select 
                            name="timezone" 
                            id="edit_timezone"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="Asia/Jakarta">WIB - Waktu Indonesia Barat (UTC+7)</option>
                            <option value="Asia/Makassar">WITA - Waktu Indonesia Tengah (UTC+8)</option>
                            <option value="Asia/Jayapura">WIT - Waktu Indonesia Timur (UTC+9)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                name="tanggal_mulai" 
                                id="edit_tanggal_mulai"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input 
                                type="date" 
                                name="tanggal_selesai"
                                id="edit_tanggal_selesai"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea 
                            name="deskripsi" 
                            id="edit_deskripsi"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select 
                            name="is_active" 
                            id="edit_is_active"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button 
                        type="button" 
                        onclick="closeEditModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium transition"
                    >
                        Update
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

async function openEditModal(hashId) {
    try {
        const response = await fetch(`/perusahaan/projects/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_kantor_id').value = data.kantor_id;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_timezone').value = data.timezone;
        
        // Format tanggal ke YYYY-MM-DD untuk input date
        if (data.tanggal_mulai) {
            const tanggalMulai = new Date(data.tanggal_mulai);
            document.getElementById('edit_tanggal_mulai').value = tanggalMulai.toISOString().split('T')[0];
        }
        
        if (data.tanggal_selesai) {
            const tanggalSelesai = new Date(data.tanggal_selesai);
            document.getElementById('edit_tanggal_selesai').value = tanggalSelesai.toISOString().split('T')[0];
        } else {
            document.getElementById('edit_tanggal_selesai').value = '';
        }
        
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
        document.getElementById('edit_is_active').value = data.is_active ? '1' : '0';
        document.getElementById('formEdit').action = `/perusahaan/projects/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data project'
        });
    }
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function viewDetail(hashId) {
    // TODO: Implement detail view
    Swal.fire({
        icon: 'info',
        title: 'Coming Soon',
        text: 'Fitur detail project akan segera hadir'
    });
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data project akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/projects/${hashId}`;
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
</script>
@endpush
