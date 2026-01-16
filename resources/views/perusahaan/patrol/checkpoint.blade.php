@extends('perusahaan.layouts.app')

@section('title', 'Checkpoint')
@section('page-title', 'Checkpoint')
@section('page-subtitle', 'Kelola checkpoint untuk rute patroli')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-map-marker-alt text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Checkpoint</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $checkpoints->total() }}</p>
                </div>
            </div>
        </div>
    </div>
    <button onclick="openCreateModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Checkpoint
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
                placeholder="Cari nama checkpoint, alamat, atau QR code..."
                class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
        </div>

        <!-- Filter Rute -->
        <div class="lg:w-64">
            <select 
                name="rute_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Rute</option>
                @foreach($rutePatrols as $rute)
                    <option value="{{ $rute->id }}" {{ request('rute_id') == $rute->id ? 'selected' : '' }}>
                        {{ $rute->nama }}
                    </option>
                @endforeach
            </select>
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
            @if(request('search') || request('rute_id') || request('status'))
            <a 
                href="{{ route('perusahaan.patrol.checkpoint') }}"
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
                        <i class="fas fa-sort-numeric-up mr-2" style="color: #3B82C8;"></i>Urutan
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Nama Checkpoint
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-route mr-2" style="color: #3B82C8;"></i>Rute Patrol
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-box mr-2" style="color: #3B82C8;"></i>Jumlah Aset
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
                @forelse($checkpoints as $checkpoint)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg font-bold text-white" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                            {{ $checkpoint->urutan ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <i class="fas fa-map-pin text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $checkpoint->nama }}</p>
                                @if($checkpoint->alamat)
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($checkpoint->alamat, 40) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">{{ $checkpoint->rutePatrol->nama }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-map mr-1"></i>{{ $checkpoint->rutePatrol->areaPatrol->nama }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $checkpoint->asets_count > 0 ? 'bg-green-100' : 'bg-gray-100' }}">
                            <i class="fas fa-box text-sm {{ $checkpoint->asets_count > 0 ? 'text-green-600' : 'text-gray-400' }}"></i>
                            <span class="font-semibold text-sm {{ $checkpoint->asets_count > 0 ? 'text-green-700' : 'text-gray-500' }}">
                                {{ $checkpoint->asets_count }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($checkpoint->is_active)
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
                            @if($checkpoint->qr_code)
                                <a 
                                    href="{{ route('perusahaan.patrol.checkpoint.qr', $checkpoint->hash_id) }}" 
                                    target="_blank"
                                    class="px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition text-sm font-medium"
                                    title="Lihat QR Code"
                                >
                                    <i class="fas fa-qrcode mr-1"></i>QR
                                </a>
                            @endif
                            <a 
                                href="{{ route('perusahaan.patrol.checkpoint.aset', $checkpoint->hash_id) }}" 
                                class="px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm font-medium"
                                title="Kelola Aset"
                            >
                                <i class="fas fa-box mr-1"></i>Aset
                            </a>
                            <button 
                                onclick="openEditModal('{{ $checkpoint->hash_id }}')" 
                                class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                                title="Edit Checkpoint"
                            >
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button 
                                onclick="confirmDelete('{{ $checkpoint->hash_id }}')" 
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                title="Hapus Checkpoint"
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
                                <i class="fas fa-map-marker-alt text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('rute_id') || request('status'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada checkpoint
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('rute_id') || request('status'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan checkpoint pertama Anda untuk memulai
                                @endif
                            </p>
                            @if(request('search') || request('rute_id') || request('status'))
                                <a 
                                    href="{{ route('perusahaan.patrol.checkpoint') }}"
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
                                    <i class="fas fa-plus mr-2"></i>Tambah Checkpoint
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
@if($checkpoints->hasPages())
<div class="mt-6">
    {{ $checkpoints->links() }}
</div>
@endif

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.patrol.checkpoint.store') }}" method="POST" id="formCreate">
            @csrf
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tambah Checkpoint Baru</h3>
                            <p class="text-sm text-gray-500">Isi form di bawah untuk menambah checkpoint</p>
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
                        <i class="fas fa-route mr-2" style="color: #3B82C8;"></i>Rute Patrol <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="rute_patrol_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        required
                    >
                        <option value="">Pilih Rute Patrol</option>
                        @foreach($rutePatrols as $rute)
                            <option value="{{ $rute->id }}">{{ $rute->nama }} ({{ $rute->areaPatrol->nama }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Pilih rute patrol untuk checkpoint ini</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-pin mr-2" style="color: #3B82C8;"></i>Nama Checkpoint <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        placeholder="Contoh: Checkpoint Gerbang Utama"
                    >
                    <p class="mt-1 text-xs text-gray-500">Masukkan nama checkpoint yang jelas</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up mr-2" style="color: #3B82C8;"></i>Urutan (Sequence)
                        </label>
                        <input 
                            type="number" 
                            name="urutan" 
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Auto jika kosong"
                        >
                        <p class="mt-1 text-xs text-gray-500">Urutan checkpoint dalam rute (otomatis jika kosong)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-qrcode mr-2" style="color: #3B82C8;"></i>QR Code
                        </label>
                        <input 
                            type="text" 
                            name="qr_code"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Auto generate jika kosong"
                        >
                        <p class="mt-1 text-xs text-gray-500">Kode unik untuk QR Code (otomatis jika kosong)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Alamat/Lokasi
                    </label>
                    <input 
                        type="text" 
                        name="alamat"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        placeholder="Contoh: Dekat pintu masuk utama"
                    >
                    <p class="mt-1 text-xs text-gray-500">Deskripsi lokasi checkpoint</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-compass mr-2" style="color: #3B82C8;"></i>Latitude
                        </label>
                        <input 
                            type="text" 
                            name="latitude"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Contoh: -0.123456"
                        >
                        <p class="mt-1 text-xs text-gray-500">Koordinat latitude (-90 sampai 90)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-compass mr-2" style="color: #3B82C8;"></i>Longitude
                        </label>
                        <input 
                            type="text" 
                            name="longitude"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                            placeholder="Contoh: 101.123456"
                        >
                        <p class="mt-1 text-xs text-gray-500">Koordinat longitude (-180 sampai 180)</p>
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
                        placeholder="Deskripsi checkpoint..."
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500">Deskripsi singkat tentang checkpoint ini</p>
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

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Info:</p>
                            <p>Setelah checkpoint dibuat, Anda bisa menambahkan aset yang harus dicek di checkpoint ini.</p>
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
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
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
                            <h3 class="text-xl font-bold text-gray-900">Edit Checkpoint</h3>
                            <p class="text-sm text-gray-500">Update informasi checkpoint</p>
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
                        <i class="fas fa-route mr-2" style="color: #3B82C8;"></i>Rute Patrol <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="rute_patrol_id"
                        id="edit_rute_patrol_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                        required
                    >
                        <option value="">Pilih Rute Patrol</option>
                        @foreach($rutePatrols as $rute)
                            <option value="{{ $rute->id }}">{{ $rute->nama }} ({{ $rute->areaPatrol->nama }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-pin mr-2" style="color: #3B82C8;"></i>Nama Checkpoint <span class="text-red-500">*</span>
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
                            <i class="fas fa-sort-numeric-up mr-2" style="color: #3B82C8;"></i>Urutan (Sequence)
                        </label>
                        <input 
                            type="number" 
                            name="urutan" 
                            id="edit_urutan"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-qrcode mr-2" style="color: #3B82C8;"></i>QR Code
                        </label>
                        <input 
                            type="text" 
                            name="qr_code"
                            id="edit_qr_code"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Alamat/Lokasi
                    </label>
                    <input 
                        type="text" 
                        name="alamat"
                        id="edit_alamat"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                        style="focus:ring-color: #3B82C8;"
                    >
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-compass mr-2" style="color: #3B82C8;"></i>Latitude
                        </label>
                        <input 
                            type="text" 
                            name="latitude"
                            id="edit_latitude"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-compass mr-2" style="color: #3B82C8;"></i>Longitude
                        </label>
                        <input 
                            type="text" 
                            name="longitude"
                            id="edit_longitude"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;"
                        >
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
function openCreateModal() {
    document.getElementById('modalCreate').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('modalCreate').classList.add('hidden');
    document.getElementById('formCreate').reset();
}

async function openEditModal(hashId) {
    try {
        const response = await fetch(`/perusahaan/patrol/checkpoint/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_rute_patrol_id').value = data.rute_patrol_id;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_urutan').value = data.urutan || '';
        document.getElementById('edit_qr_code').value = data.qr_code || '';
        document.getElementById('edit_alamat').value = data.alamat || '';
        document.getElementById('edit_latitude').value = data.latitude || '';
        document.getElementById('edit_longitude').value = data.longitude || '';
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
        document.getElementById('edit_is_active').value = data.is_active ? '1' : '0';
        document.getElementById('formEdit').action = `/perusahaan/patrol/checkpoint/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data checkpoint'
        });
    }
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data checkpoint akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/patrol/checkpoint/${hashId}`;
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
