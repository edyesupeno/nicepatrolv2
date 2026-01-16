@extends('perusahaan.layouts.app')

@section('title', 'Aset Kawasan')
@section('page-title', 'Aset Kawasan')
@section('page-subtitle', 'Kelola aset yang ada di kawasan patrol')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-box text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Aset</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $asetKawasans->total() }}</p>
                </div>
            </div>
        </div>
    </div>
    <button onclick="openCreateModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Aset
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
                placeholder="Cari nama aset, kode, kategori, merk, atau model..."
                class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
        </div>

        <!-- Filter Kategori -->
        <div class="lg:w-48">
            <input 
                type="text"
                name="kategori"
                value="{{ request('kategori') }}"
                placeholder="Filter Kategori"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
        </div>

        <!-- Filter Status -->
        <div class="lg:w-48">
            <select 
                name="status"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
            @if(request('search') || request('kategori') || request('status'))
            <a 
                href="{{ route('perusahaan.patrol.aset-kawasan') }}"
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
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-image mr-2" style="color: #3B82C8;"></i>Foto
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-box mr-2" style="color: #3B82C8;"></i>Nama Aset
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Kategori
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-info-circle mr-2" style="color: #3B82C8;"></i>Detail
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Status
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($asetKawasans as $aset)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        @if($aset->foto)
                            <img src="{{ asset('storage/' . $aset->foto) }}" alt="{{ $aset->nama }}" class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 rounded-lg flex items-center justify-center bg-gray-100">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $aset->nama }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-barcode mr-1"></i>{{ $aset->kode_aset }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ $aset->kategori }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm space-y-1">
                            @if($aset->merk)
                                <p class="text-gray-600"><span class="font-medium">Merk:</span> {{ $aset->merk }}</p>
                            @endif
                            @if($aset->model)
                                <p class="text-gray-600"><span class="font-medium">Model:</span> {{ $aset->model }}</p>
                            @endif
                            @if($aset->serial_number)
                                <p class="text-gray-600"><span class="font-medium">SN:</span> {{ $aset->serial_number }}</p>
                            @endif
                            @if(!$aset->merk && !$aset->model && !$aset->serial_number)
                                <span class="text-xs text-gray-400">Tidak ada detail</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($aset->is_active)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                <i class="fas fa-times-circle mr-1"></i>Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button 
                                onclick="openEditModal('{{ $aset->hash_id }}')" 
                                class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                                title="Edit Aset"
                            >
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button 
                                onclick="confirmDelete('{{ $aset->hash_id }}')" 
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                title="Hapus Aset"
                            >
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-box text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('kategori') || request('status'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada aset kawasan
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('kategori') || request('status'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan aset kawasan pertama Anda untuk memulai
                                @endif
                            </p>
                            @if(request('search') || request('kategori') || request('status'))
                                <a 
                                    href="{{ route('perusahaan.patrol.aset-kawasan') }}"
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
                                    <i class="fas fa-plus mr-2"></i>Tambah Aset
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
@if($asetKawasans->hasPages())
<div class="mt-6">
    {{ $asetKawasans->links() }}
</div>
@endif

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.patrol.aset-kawasan.store') }}" method="POST" id="formCreate" enctype="multipart/form-data">
            @csrf
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-box text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tambah Aset Baru</h3>
                            <p class="text-sm text-gray-500">Isi form di bawah untuk menambah aset</p>
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
                        <i class="fas fa-box mr-2" style="color: #3B82C8;"></i>Nama Aset <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        placeholder="Contoh: CCTV Indoor"
                    >
                    <p class="mt-1 text-xs text-gray-500">Kode aset akan di-generate otomatis berdasarkan nama</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Kategori <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="kategori" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Contoh: Elektronik"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-copyright mr-2" style="color: #3B82C8;"></i>Merk
                        </label>
                        <input 
                            type="text" 
                            name="merk"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Contoh: Hikvision"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-cube mr-2" style="color: #3B82C8;"></i>Model
                        </label>
                        <input 
                            type="text" 
                            name="model"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Contoh: DS-2CE16D0T"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-2" style="color: #3B82C8;"></i>Serial Number
                        </label>
                        <input 
                            type="text" 
                            name="serial_number"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Contoh: SN123456789"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Status
                    </label>
                    <select 
                        name="is_active"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        required
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-image mr-2" style="color: #3B82C8;"></i>Foto Aset
                    </label>
                    <div class="flex items-start gap-4">
                        <div id="preview-create" class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="foto"
                                id="foto-create"
                                accept="image/jpeg,image/png,image/jpg"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition text-sm"
                                style="focus:ring-color: #3B82C8;"
                                onchange="previewImage(this, 'preview-create')"
                            >
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, max 2MB. Jika tidak diisi akan menggunakan foto default.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2" style="color: #3B82C8;"></i>Deskripsi
                    </label>
                    <textarea 
                        name="deskripsi" 
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition resize-none"
                        style="focus:ring-color: #3B82C8;"
                        placeholder="Deskripsi detail aset..."
                    ></textarea>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Catatan:</p>
                            <p>Kondisi aset akan di-update otomatis saat patrol. Aset ini bisa digunakan di multiple checkpoint.</p>
                        </div>
                    </div>
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
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form id="formEdit" method="POST" enctype="multipart/form-data">
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
                            <h3 class="text-xl font-bold text-gray-900">Edit Aset</h3>
                            <p class="text-sm text-gray-500">Update informasi aset</p>
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
                        <i class="fas fa-barcode mr-2" style="color: #3B82C8;"></i>Kode Aset
                    </label>
                    <input 
                        type="text" 
                        id="edit_kode_aset"
                        disabled
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-600"
                    >
                    <p class="mt-1 text-xs text-gray-500">Kode aset tidak dapat diubah</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box mr-2" style="color: #3B82C8;"></i>Nama Aset <span class="text-red-500">*</span>
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Kategori <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="kategori" 
                            id="edit_kategori"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-copyright mr-2" style="color: #3B82C8;"></i>Merk
                        </label>
                        <input 
                            type="text" 
                            name="merk"
                            id="edit_merk"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-cube mr-2" style="color: #3B82C8;"></i>Model
                        </label>
                        <input 
                            type="text" 
                            name="model"
                            id="edit_model"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-2" style="color: #3B82C8;"></i>Serial Number
                        </label>
                        <input 
                            type="text" 
                            name="serial_number"
                            id="edit_serial_number"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Status
                    </label>
                    <select 
                        name="is_active"
                        id="edit_is_active"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        required
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-image mr-2" style="color: #3B82C8;"></i>Foto Aset
                    </label>
                    <div class="flex items-start gap-4">
                        <div id="preview-edit" class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="foto"
                                id="foto-edit"
                                accept="image/jpeg,image/png,image/jpg"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition text-sm"
                                style="focus:ring-color: #3B82C8;"
                                onchange="previewImage(this, 'preview-edit')"
                            >
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, max 2MB. Kosongkan jika tidak ingin mengubah foto.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2" style="color: #3B82C8;"></i>Deskripsi
                    </label>
                    <textarea 
                        name="deskripsi" 
                        id="edit_deskripsi"
                        rows="3"
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
                        <i class="fas fa-save mr-2"></i>Update
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
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function openCreateModal() {
    document.getElementById('modalCreate').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('modalCreate').classList.add('hidden');
    document.getElementById('formCreate').reset();
    document.getElementById('preview-create').innerHTML = '<i class="fas fa-image text-gray-400 text-2xl"></i>';
}

async function openEditModal(hashId) {
    try {
        const response = await fetch(`/perusahaan/patrol/aset-kawasan/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_kode_aset').value = data.kode_aset;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_kategori').value = data.kategori;
        document.getElementById('edit_merk').value = data.merk || '';
        document.getElementById('edit_model').value = data.model || '';
        document.getElementById('edit_serial_number').value = data.serial_number || '';
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
        document.getElementById('edit_is_active').value = data.is_active ? '1' : '0';
        
        // Preview existing foto
        const previewEdit = document.getElementById('preview-edit');
        if (data.foto) {
            previewEdit.innerHTML = `<img src="/storage/${data.foto}" class="w-full h-full object-cover rounded-lg">`;
        } else {
            previewEdit.innerHTML = '<i class="fas fa-image text-gray-400 text-2xl"></i>';
        }
        
        document.getElementById('formEdit').action = `/perusahaan/patrol/aset-kawasan/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data aset'
        });
    }
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data aset akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/patrol/aset-kawasan/${hashId}`;
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
