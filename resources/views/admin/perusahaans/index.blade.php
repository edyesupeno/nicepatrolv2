@extends('layouts.app')

@section('title', 'Perusahaan Mitra')
@section('page-title', 'Perusahaan Mitra')
@section('page-subtitle', 'Kelola semua perusahaan yang menggunakan sistem SaaS')

@section('content')
<div class="mb-6 flex justify-end">
    <button onclick="openModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-md">
        <i class="fas fa-plus mr-2"></i>Tambah Perusahaan
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Perusahaan</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Dibuat</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($perusahaans as $perusahaan)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-building text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $perusahaan->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $perusahaan->kode }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    @if($perusahaan->is_active)
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                    @else
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Nonaktif</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $perusahaan->created_at->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.perusahaans.edit', $perusahaan->hash_id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Edit
                        </a>
                        <button onclick="confirmDelete('{{ $perusahaan->hash_id }}', '{{ $perusahaan->nama }}')" class="text-red-600 hover:text-red-800 font-medium text-sm">
                            Hapus
                        </button>
                        <form id="delete-form-{{ $perusahaan->hash_id }}" action="{{ route('admin.perusahaans.destroy', $perusahaan->hash_id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <div class="text-gray-400">
                        <i class="fas fa-building text-5xl mb-3"></i>
                        <p class="text-sm font-medium">Belum ada perusahaan mitra</p>
                        <p class="text-xs mt-1">Klik tombol "Tambah Perusahaan" untuk menambahkan mitra baru</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah Perusahaan -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <form action="{{ route('admin.perusahaans.store') }}" method="POST" id="formTambah">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Perusahaan</h3>
                
                <!-- Nama Perusahaan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan</label>
                    <input 
                        type="text" 
                        name="nama" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="PT. Nama Perusahaan"
                    >
                </div>

                <!-- Kode Perusahaan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Perusahaan</label>
                    <input 
                        type="text" 
                        name="kode" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="KODE"
                    >
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select 
                        name="is_active" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <!-- Data Admin Perusahaan -->
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Data Admin Perusahaan</h4>
                    
                    <!-- Nama Admin -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Admin <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="admin_name" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Nama lengkap admin"
                        >
                    </div>

                    <!-- Email Admin -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Admin <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="admin_email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="admin@perusahaan.com"
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="admin_password" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Minimal 6 karakter"
                        >
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Password untuk login admin perusahaan</p>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal() {
    document.getElementById('modalTambah').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modalTambah').classList.add('hidden');
    document.getElementById('formTambah').reset();
}

// Close modal when clicking outside
document.getElementById('modalTambah')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

function confirmDelete(hashId, nama) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        html: `Perusahaan <strong>${nama}</strong> akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + hashId).submit();
        }
    });
}
</script>
@endpush
