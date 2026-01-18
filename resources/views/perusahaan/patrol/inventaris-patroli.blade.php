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

    <!-- Tab Content: Inventaris -->
    <div id="content-inventaris" class="tab-content">
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
                                placeholder="Cari inventaris..." 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                value="{{ request('search') }}"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Filter Kategori -->
                        <select 
                            id="filterKategori"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                                    {{ $kat }}
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
                        Tambah Inventaris
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Inventaris</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dikelola Oleh Tim</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inventaris as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                @if($item->foto)
                                    <img 
                                        src="{{ asset('storage/' . $item->foto) }}" 
                                        alt="{{ $item->nama }}"
                                        class="w-16 h-16 object-cover rounded-lg"
                                    >
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $item->nama }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ $item->catatan ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->is_active)
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
                                <p class="text-sm text-gray-600">-</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        onclick="editItem('{{ $item->hash_id }}')"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        onclick="deleteItem('{{ $item->hash_id }}')"
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
                                <i class="fas fa-box text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data inventaris</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($inventaris->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $inventaris->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Tab Content: Kuesioner -->
    <div id="content-kuesioner" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1 flex flex-col md:flex-row gap-3">
                        <!-- Search -->
                        <div class="relative flex-1 max-w-md">
                            <input 
                                type="text" 
                                id="searchKuesioner"
                                placeholder="Cari kuesioner..." 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Filter Status -->
                        <select 
                            id="filterStatusKuesioner"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <button 
                        onclick="openKuesionerModal()"
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
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                                <p>Klik tab Kuesioner untuk melihat data</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Pemeriksaan -->
    <div id="content-pemeriksaan" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="fas fa-clipboard-check text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pemeriksaan</h3>
            <p class="text-gray-600">Fitur pemeriksaan akan segera hadir</p>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <form id="inventarisForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Inventaris</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Nama Inventaris -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Inventaris <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        id="nama"
                        placeholder="Contoh: Radio HT Motorola"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="kategori" 
                        id="kategori"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        <option value="">Pilih Kategori</option>
                        <option value="Komunikasi">Komunikasi</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Penerangan">Penerangan</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Alat Tulis">Alat Tulis</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- Foto Inventaris -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Inventaris
                    </label>
                    <div class="flex items-start gap-4">
                        <div id="previewContainer" class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            <i class="fas fa-box text-gray-400 text-3xl" id="previewIcon"></i>
                            <img id="previewImage" class="w-full h-full object-cover hidden" alt="Preview">
                        </div>
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="foto" 
                                id="foto"
                                accept="image/jpeg,image/png,image/jpg"
                                class="hidden"
                                onchange="previewFoto(event)"
                            >
                            <button 
                                type="button"
                                onclick="document.getElementById('foto').click()"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium"
                            >
                                Pilih File
                            </button>
                            <p class="text-xs text-gray-500 mt-2">
                                Format: JPG, PNG, max 2MB. Jika tidak diisi akan menggunakan foto default.
                            </p>
                            <p id="fileName" class="text-xs text-blue-600 mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea 
                        name="catatan" 
                        id="catatan"
                        rows="3"
                        placeholder="Catatan tambahan..."
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
// Tab Switching
function switchTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-600', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Add active class to selected button
    const activeButton = document.getElementById('tab-' + tab);
    activeButton.classList.add('active', 'border-blue-600', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Search & Filter
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

document.getElementById('filterKategori').addEventListener('change', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const kategori = document.getElementById('filterKategori').value;
    const status = document.getElementById('filterStatus').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (kategori) params.append('kategori', kategori);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("perusahaan.patrol.inventaris-patroli") }}?' + params.toString();
}

// Modal Functions
function openModal() {
    document.getElementById('formModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Inventaris';
    document.getElementById('inventarisForm').action = '{{ route("perusahaan.patrol.inventaris-patroli.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inventarisForm').reset();
    resetPreview();
}

function closeModal() {
    document.getElementById('formModal').classList.add('hidden');
    document.getElementById('inventarisForm').reset();
    resetPreview();
}

function previewFoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('previewImage').classList.remove('hidden');
            document.getElementById('previewIcon').classList.add('hidden');
        }
        reader.readAsDataURL(file);
        document.getElementById('fileName').textContent = file.name;
    }
}

function resetPreview() {
    document.getElementById('previewImage').classList.add('hidden');
    document.getElementById('previewIcon').classList.remove('hidden');
    document.getElementById('fileName').textContent = '';
}

function editItem(hashId) {
    fetch(`{{ url('perusahaan/patrol/inventaris-patroli') }}/${hashId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('formModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Inventaris';
            document.getElementById('inventarisForm').action = `{{ url('perusahaan/patrol/inventaris-patroli') }}/${hashId}`;
            document.getElementById('formMethod').value = 'PUT';
            
            document.getElementById('nama').value = data.nama;
            document.getElementById('kategori').value = data.kategori;
            document.getElementById('catatan').value = data.catatan || '';
            document.querySelector(`input[name="is_active"][value="${data.is_active ? '1' : '0'}"]`).checked = true;
            
            if (data.foto) {
                document.getElementById('previewImage').src = '{{ asset("storage") }}/' + data.foto;
                document.getElementById('previewImage').classList.remove('hidden');
                document.getElementById('previewIcon').classList.add('hidden');
            }
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
            form.action = `{{ url('perusahaan/patrol/inventaris-patroli') }}/${hashId}`;
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
