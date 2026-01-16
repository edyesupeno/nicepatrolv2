@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Kantor')
@section('page-title', 'Manajemen Kantor')
@section('page-subtitle', 'Kelola kantor pusat dan cabang perusahaan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-gray-600">Total: <span class="font-bold text-sky-600">{{ $kantors->total() }}</span> kantor</p>
    </div>
    <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
        <i class="fas fa-plus mr-2"></i>Tambah Kantor
    </button>
</div>

<!-- Grid Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($kantors as $kantor)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
        <div class="p-6">
            <!-- Header with Name and Actions -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-lg font-bold text-gray-900">{{ $kantor->nama }}</h3>
                        @if($kantor->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                            </span>
                        @endif
                    </div>
                    @if($kantor->is_pusat)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                            Kantor Pusat
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            Cabang
                        </span>
                    @endif
                </div>
                <button onclick="openEditModal('{{ $kantor->hash_id }}')" class="text-sky-600 hover:text-sky-800 p-2">
                    <i class="fas fa-edit"></i>
                </button>
            </div>

            <!-- Location Info -->
            <div class="space-y-3 mb-4">
                @if($kantor->alamat)
                <div class="flex items-start text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-2 flex-shrink-0"></i>
                    <span class="line-clamp-2">{{ $kantor->alamat }}</span>
                </div>
                @endif

                @if($kantor->telepon)
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                    <span>{{ $kantor->telepon }}</span>
                </div>
                @endif

                @if($kantor->email)
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                    <span>{{ $kantor->email }}</span>
                </div>
                @endif
            </div>

            <!-- Stats -->
            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Jumlah Area:</span>
                    <span class="font-bold text-gray-900">{{ $kantor->checkpoints_count }}</span>
                </div>
            </div>
        </div>

    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Belum ada data kantor</p>
            <p class="text-gray-400 text-sm mb-6">Tambahkan kantor pertama Anda untuk memulai</p>
            <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Kantor
            </button>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $kantors->links() }}
</div>

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.kantors.store') }}" method="POST" id="formCreate">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Kantor Baru</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kantor <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="nama" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Contoh: Kantor Pusat Jakarta"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea 
                            name="alamat" 
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Alamat lengkap kantor"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                        <input 
                            type="text" 
                            name="telepon" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="021-1234567"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="kantor@example.com"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center mb-2">
                            <input 
                                type="checkbox" 
                                name="is_pusat" 
                                value="1"
                                class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Tandai sebagai Kantor Pusat</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1"
                                checked
                                class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Status Aktif</span>
                        </label>
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
                <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Kantor</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kantor <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="nama" 
                            id="edit_nama"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea 
                            name="alamat" 
                            id="edit_alamat"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                        <input 
                            type="text" 
                            name="telepon" 
                            id="edit_telepon"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="edit_email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center mb-2">
                            <input 
                                type="checkbox" 
                                name="is_pusat" 
                                id="edit_is_pusat"
                                value="1"
                                class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Tandai sebagai Kantor Pusat</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                id="edit_is_active"
                                value="1"
                                class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Status Aktif</span>
                        </label>
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
        const response = await fetch(`/perusahaan/kantors/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_alamat').value = data.alamat || '';
        document.getElementById('edit_telepon').value = data.telepon || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_is_pusat').checked = data.is_pusat || false;
        document.getElementById('edit_is_active').checked = data.is_active || false;
        document.getElementById('formEdit').action = `/perusahaan/kantors/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data kantor'
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
        text: 'Fitur detail kantor akan segera hadir'
    });
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data kantor dan area terkait akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/lokasis/${hashId}`;
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
