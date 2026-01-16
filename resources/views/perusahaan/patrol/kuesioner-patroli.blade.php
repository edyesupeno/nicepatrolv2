@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Inventaris Patroli</h1>
        <p class="text-gray-600">Kelola template kuesioner untuk tim patroli</p>
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
            </nav>
        </div>
    </div>

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
                            placeholder="Cari kuesioner..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ request('search') }}"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

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
                    Tambah Kuesioner
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul Kuesioner</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Pertanyaan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kuesioners as $kuesioner)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $kuesioner->judul }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $kuesioner->deskripsi ?: '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a 
                                href="{{ route('perusahaan.patrol.kuesioner-patroli.pertanyaan', $kuesioner->hash_id) }}"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium hover:bg-blue-200 transition"
                            >
                                <i class="fas fa-list"></i>
                                {{ $kuesioner->pertanyaans_count }} pertanyaan
                            </a>
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
                                    href="{{ route('perusahaan.patrol.kuesioner-patroli.pertanyaan', $kuesioner->hash_id) }}"
                                    class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                    title="Kelola Pertanyaan"
                                >
                                    <i class="fas fa-tasks"></i>
                                </a>
                                <a 
                                    href="{{ route('perusahaan.patrol.kuesioner-patroli.preview', $kuesioner->hash_id) }}"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                    title="Preview"
                                    target="_blank"
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada data kuesioner</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kuesioners->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $kuesioners->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <form id="kuesionerForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <!-- Modal Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Kuesioner</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Judul -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Kuesioner <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        id="judul"
                        placeholder="Contoh: Kuesioner Keamanan Harian"
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
                        placeholder="Checklist keamanan yang harus diisi setiap shift"
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
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200 rounded-b-2xl">
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

document.getElementById('filterStatus').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('filterStatus').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("perusahaan.patrol.kuesioner-patroli") }}?' + params.toString();
}

// Modal Functions
function openModal() {
    document.getElementById('formModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Kuesioner';
    document.getElementById('kuesionerForm').action = '{{ route("perusahaan.patrol.kuesioner-patroli.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('kuesionerForm').reset();
}

function closeModal() {
    document.getElementById('formModal').classList.add('hidden');
    document.getElementById('kuesionerForm').reset();
}

function editItem(hashId) {
    fetch(`{{ url('perusahaan/patrol/kuesioner-patroli') }}/${hashId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('formModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Kuesioner';
            document.getElementById('kuesionerForm').action = `{{ url('perusahaan/patrol/kuesioner-patroli') }}/${hashId}`;
            document.getElementById('formMethod').value = 'PUT';
            
            document.getElementById('judul').value = data.judul;
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.querySelector(`input[name="is_active"][value="${data.is_active ? '1' : '0'}"]`).checked = true;
        });
}

function deleteItem(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Semua pertanyaan dalam kuesioner ini akan ikut terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('perusahaan/patrol/kuesioner-patroli') }}/${hashId}`;
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
