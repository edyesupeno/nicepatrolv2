@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Karyawan')
@section('page-title', 'Manajemen Karyawan')
@section('page-subtitle', 'Kelola data karyawan perusahaan')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div id="successAlert" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600 text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <button onclick="document.getElementById('successAlert').remove()" class="text-green-600 hover:text-green-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

@if(session('error'))
<div id="errorAlert" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    <button onclick="document.getElementById('errorAlert').remove()" class="text-red-600 hover:text-red-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

@if(session('warning'))
<div id="warningAlert" class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
        <span class="font-medium">{{ session('warning') }}</span>
    </div>
    <button onclick="document.getElementById('warningAlert').remove()" class="text-yellow-600 hover:text-yellow-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Karyawan</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $karyawans->total() }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('perusahaan.karyawans.export-page') }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-sm text-white hover:shadow-lg" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
            <i class="fas fa-file-export mr-2"></i>Export Data
        </a>
        <button onclick="openImportModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-sm text-white hover:shadow-lg" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <i class="fas fa-file-excel mr-2"></i>Import Excel
        </button>
        <button onclick="openWizardModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-plus mr-2"></i>Tambah Karyawan
        </button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, No Badge..." class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm" style="focus:ring-color: #3B82C8;">
        </div>
        <select name="project_id" class="lg:w-48 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            <option value="">Semua Project</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
            @endforeach
        </select>
        <select name="status_karyawan" class="lg:w-48 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            <option value="">Semua Status</option>
            @foreach($statusKaryawans as $status)
                <option value="{{ $status->nama }}" {{ request('status_karyawan') == $status->nama ? 'selected' : '' }}>{{ $status->nama }}</option>
            @endforeach
        </select>
        <select name="jabatan_id" class="lg:w-48 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            <option value="">Semua Jabatan</option>
            @foreach($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}" {{ request('jabatan_id') == $jabatan->id ? 'selected' : '' }}>{{ $jabatan->nama }}</option>
            @endforeach
        </select>
        <select name="is_active" class="lg:w-40 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            <option value="">Semua</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-3 rounded-xl font-medium transition text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request()->hasAny(['search', 'project_id', 'status_karyawan', 'jabatan_id', 'is_active']))
            <a href="{{ route('perusahaan.karyawans.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
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
                        <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Karyawan
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-id-card mr-2" style="color: #3B82C8;"></i>No Badge & NIK
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-user-tie mr-2" style="color: #3B82C8;"></i>Jabatan
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Status
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Aktif
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($karyawans as $karyawan)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <span class="text-white font-bold text-sm">{{ substr($karyawan->nama_lengkap, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $karyawan->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $karyawan->user->email ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $karyawan->nik_karyawan }}</p>
                            <p class="text-xs text-gray-500">KTP: {{ $karyawan->nik_ktp }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-700">{{ $karyawan->jabatan->nama ?? '-' }}</p>
                        @if($karyawan->project)
                            <span class="px-2 py-0.5 rounded text-xs font-medium text-white mt-1 inline-block" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                {{ $karyawan->project->nama }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <span class="px-2 py-1 rounded-lg text-xs font-medium text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                {{ $karyawan->status_karyawan }}
                            </span>
                            @if(str_contains(strtolower($karyawan->status_karyawan), 'kontrak') && $karyawan->tanggal_keluar)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-calendar-times mr-1"></i>
                                    Berakhir: {{ $karyawan->tanggal_keluar->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($karyawan->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('perusahaan.karyawans.show', $karyawan->hash_id) }}" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="confirmDelete('{{ $karyawan->hash_id }}')" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium" title="Hapus Karyawan">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-users text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request()->hasAny(['search', 'status_karyawan', 'jabatan_id', 'is_active']))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada data karyawan
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request()->hasAny(['search', 'status_karyawan', 'jabatan_id', 'is_active']))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan karyawan pertama Anda untuk memulai
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($karyawans->hasPages())
<div class="mt-6">{{ $karyawans->links() }}</div>
@endif

<!-- Wizard Modal -->
<div id="wizardModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.karyawans.store') }}" method="POST" id="wizardForm" novalidate>
            @csrf
            <!-- Modal Header with Progress -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-user-plus text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tambah Karyawan Baru</h3>
                            <p class="text-sm text-gray-500">Lengkapi data karyawan dalam 3 langkah</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeWizardModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Step Indicator -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="flex items-center">
                            <div id="step1-indicator" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-900">Step 1</p>
                                <p class="text-xs text-gray-500">Data Dasar</p>
                            </div>
                        </div>
                        <div id="line1" class="flex-1 h-1 mx-4 bg-gray-200 rounded transition"></div>
                    </div>
                    <div class="flex items-center flex-1">
                        <div class="flex items-center">
                            <div id="step2-indicator" class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-gray-200 text-gray-400 transition">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-semibold text-gray-500">Step 2</p>
                                <p class="text-xs text-gray-400">Data Pribadi</p>
                            </div>
                        </div>
                        <div id="line2" class="flex-1 h-1 mx-4 bg-gray-200 rounded transition"></div>
                    </div>
                    <div class="flex items-center">
                        <div id="step3-indicator" class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-gray-200 text-gray-400 transition">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-semibold text-gray-500">Step 3</p>
                            <p class="text-xs text-gray-400">Akun Login</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Data Dasar -->
            <div id="step1" class="wizard-step p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-badge mr-2" style="color: #3B82C8;"></i>No Badge <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nik_karyawan" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="Contoh: NK001">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                        </label>
                        <select name="project_id" id="projectSelectCreate" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" onchange="loadJabatansByProjectCreate()">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-tie mr-2" style="color: #3B82C8;"></i>Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select name="jabatan_id" id="jabatanSelectCreate" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="">Pilih project terlebih dahulu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Status Karyawan <span class="text-red-500">*</span>
                        </label>
                        <select name="status_karyawan" id="statusKaryawanCreate" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="">Pilih Status</option>
                            @foreach($statusKaryawans as $status)
                                <option value="{{ $status->nama }}">{{ $status->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2" style="color: #3B82C8;"></i>Tanggal Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_masuk" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Status Aktif <span class="text-red-500">*</span>
                        </label>
                        <select name="is_active" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    <div id="tanggalKeluarWrapperCreate" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-times mr-2" style="color: #3B82C8;"></i>Tanggal Berakhir Kontrak <span class="text-red-500" id="requiredStarCreate">*</span>
                        </label>
                        <input type="date" name="tanggal_keluar" id="tanggalKeluarCreate" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                        <p class="mt-1 text-xs text-gray-500">Wajib diisi untuk karyawan kontrak</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Data Pribadi -->
            <div id="step2" class="wizard-step hidden p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_lengkap" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="Nama lengkap sesuai KTP">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2" style="color: #3B82C8;"></i>NIK KTP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nik_ktp" required maxlength="16" pattern="[0-9]{16}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="16 digit NIK KTP">
                        <p class="mt-1 text-xs text-gray-500">16 digit angka</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2" style="color: #3B82C8;"></i>Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="telepon" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tempat_lahir" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="Kota tempat lahir">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-2" style="color: #3B82C8;"></i>Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-venus-mars mr-2" style="color: #3B82C8;"></i>Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_kelamin" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-ring mr-2" style="color: #3B82C8;"></i>Status Perkawinan <span class="text-red-500">*</span>
                        </label>
                        <select name="status_perkawinan" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="TK">Tidak Kawin (TK)</option>
                            <option value="K">Kawin (K)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-users mr-2" style="color: #3B82C8;"></i>Jumlah Tanggungan <span class="text-red-500">*</span>
                        </label>
                        <select name="jumlah_tanggungan" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="0">0 (Tidak ada tanggungan)</option>
                            <option value="1">1 (Satu tanggungan)</option>
                            <option value="2">2 (Dua tanggungan)</option>
                            <option value="3">3 (Tiga tanggungan)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-home mr-2" style="color: #3B82C8;"></i>Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" required rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition resize-none" placeholder="Alamat lengkap sesuai KTP"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-city mr-2" style="color: #3B82C8;"></i>Kota <span class="text-red-500">*</span>
                        </label>
                        <select name="kota" id="kotaSelect" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="">Ketik untuk mencari atau tambah kota baru...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Ketik nama kota, jika tidak ada akan otomatis ditambahkan</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map mr-2" style="color: #3B82C8;"></i>Provinsi <span class="text-red-500">*</span>
                        </label>
                        <select name="provinsi" id="provinsiSelect" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="">Ketik untuk mencari atau tambah provinsi baru...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Ketik nama provinsi, jika tidak ada akan otomatis ditambahkan</p>
                    </div>
                </div>
            </div>

            <!-- Step 3: Akun Login -->
            <div id="step3" class="wizard-step hidden p-6 space-y-5">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 mb-1">Informasi Akun Login</p>
                            <p class="text-xs text-blue-700">Akun ini akan digunakan karyawan untuk login ke sistem patroli</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2" style="color: #3B82C8;"></i>Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="email@example.com">
                        <p class="mt-1 text-xs text-gray-500">Email akan digunakan untuk login</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-shield mr-2" style="color: #3B82C8;"></i>Role / Hak Akses <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition">
                            <option value="">Pilih Role</option>
                            <option value="security_officer" selected>Security Officer</option>
                            <option value="office_employee">Office Employee</option>
                            <option value="manager_project">Manager Project</option>
                            <option value="admin_project">Admin Project</option>
                            <option value="admin_branch">Admin Branch</option>
                            <option value="finance_branch">Finance Branch</option>
                            <option value="admin_hsse">Admin HSSE</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Tentukan hak akses karyawan di sistem</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2" style="color: #3B82C8;"></i>Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="Minimal 8 karakter">
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2" style="color: #3B82C8;"></i>Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required minlength="8" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition" placeholder="Ulangi password">
                        <p class="mt-1 text-xs text-gray-500">Masukkan password yang sama</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer with Navigation -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 rounded-b-2xl border-t border-gray-200">
                <div class="flex gap-3">
                    <button type="button" onclick="closeWizardModal()" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="button" id="btnPrev" onclick="prevStep()" class="hidden px-6 py-3 border-2 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition" style="border-color: #3B82C8; color: #3B82C8;">
                        <i class="fas fa-arrow-left mr-2"></i>Sebelumnya
                    </button>
                    <button type="button" id="btnNext" onclick="nextStep()" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        Selanjutnya<i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="btnSubmit" class="hidden flex-1 px-6 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                        <i class="fas fa-save mr-2"></i>Simpan Karyawan
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

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="fas fa-file-excel text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Import Karyawan dari Excel</h3>
                    <p class="text-sm text-gray-600">Upload file Excel untuk import data karyawan</p>
                </div>
            </div>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form id="importForm" action="{{ route('perusahaan.karyawans.import-excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Step 1: Pilih Project & Role -->
            <div id="importStep1">
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Pilih Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="import_project_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">-- Pilih Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Semua karyawan yang diimport akan ditambahkan ke project ini
                    </p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-shield mr-2" style="color: #3B82C8;"></i>Role / Hak Akses <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="import_role" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="security_officer" selected>Security Officer</option>
                        <option value="office_employee">Office Employee</option>
                        <option value="manager_project">Manager Project</option>
                        <option value="admin_project">Admin Project</option>
                        <option value="admin_branch">Admin Branch</option>
                        <option value="finance_branch">Finance Branch</option>
                        <option value="admin_hsse">Admin HSSE</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Semua karyawan yang diimport akan memiliki role yang sama
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeImportModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="button" onclick="goToImportStep2()" class="px-6 py-3 text-white rounded-lg font-semibold transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        Lanjut <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Download Template & Upload -->
            <div id="importStep2" class="hidden">
                <!-- Download Template -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-download text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-1">Langkah 1: Download Template</h4>
                            <p class="text-sm text-gray-600 mb-3">Download template Excel terlebih dahulu, isi data karyawan sesuai format</p>
                            <button type="button" onclick="downloadTemplate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                <i class="fas fa-download mr-2"></i>Download Template Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload File -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-upload mr-2" style="color: #3B82C8;"></i>Langkah 2: Upload File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                        <input type="file" name="file" id="import_file" accept=".xlsx,.xls" class="hidden" required onchange="updateFileName(this)">
                        <label for="import_file" class="cursor-pointer">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-cloud-upload-alt text-3xl text-blue-600"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 mb-1">Klik untuk upload file</p>
                            <p class="text-xs text-gray-500">atau drag and drop file Excel di sini</p>
                            <p class="text-xs text-gray-400 mt-2">Format: .xlsx atau .xls (Max: 10MB)</p>
                        </label>
                    </div>
                    <p id="fileName" class="mt-2 text-sm text-gray-600 hidden"></p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold mb-2">Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Pastikan format file sesuai dengan template</li>
                                <li>No Badge dan Email harus unik</li>
                                <li>Jabatan harus sesuai dengan yang ada di sistem</li>
                                <li>Password default untuk semua user: <strong>nicepatrol</strong></li>
                                <li>Semua karyawan akan memiliki <strong>role yang sama</strong> sesuai pilihan di Step 1</li>
                                <li>Area kerja akan <strong>otomatis di-assign</strong> berdasarkan project</li>
                                <li>Hapus baris contoh sebelum import</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between gap-3">
                    <button type="button" onclick="goToImportStep1()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeImportModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" id="importButton" class="px-6 py-3 text-white rounded-lg font-semibold transition" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                            <i class="fas fa-upload mr-2"></i>Import Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Progress -->
            <div id="importStep3" class="hidden">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-cog fa-spin text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Import Sedang Berjalan</h4>
                    <p class="text-sm text-gray-600">Mohon tunggu, proses import sedang berjalan di background...</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span id="progressPercentage" class="text-sm font-medium text-blue-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="progressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="progressMessage" class="text-sm text-gray-600 mt-2">Memulai import...</p>
                </div>

                <!-- Progress Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div id="successCount" class="text-2xl font-bold text-green-600">0</div>
                        <div class="text-sm text-gray-600">Berhasil</div>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <div id="skippedCount" class="text-2xl font-bold text-red-600">0</div>
                        <div class="text-sm text-gray-600">Di-skip</div>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div id="totalProcessed" class="text-2xl font-bold text-blue-600">0</div>
                        <div class="text-sm text-gray-600">Total</div>
                    </div>
                </div>

                <!-- Recent Errors -->
                <div id="recentErrors" class="hidden mb-6">
                    <h5 class="text-sm font-semibold text-gray-700 mb-2">Error Terbaru:</h5>
                    <div id="errorList" class="bg-red-50 border border-red-200 rounded-lg p-3 max-h-32 overflow-y-auto">
                        <!-- Errors will be populated here -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div id="progressActions" class="flex justify-center">
                    <button type="button" onclick="cancelImport()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Tutup
                    </button>
                </div>

                <!-- Completion Actions -->
                <div id="completionActions" class="hidden flex justify-center gap-3">
                    <button type="button" onclick="closeImportModal(); location.reload();" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>Selesai
                    </button>
                    <button type="button" onclick="startNewImport()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Import Lagi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Progress Polling Variables -->
<script>
let importJobId = null;
let progressInterval = null;
</script>

@push('scripts')
<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importStep1').classList.remove('hidden');
    document.getElementById('importStep2').classList.add('hidden');
    document.getElementById('importForm').reset();
    document.getElementById('fileName').classList.add('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

function goToImportStep2() {
    const projectId = document.getElementById('import_project_id').value;
    const role = document.getElementById('import_role').value;
    
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih project terlebih dahulu',
        });
        return;
    }
    
    if (!role) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih role terlebih dahulu',
        });
        return;
    }
    
    document.getElementById('importStep1').classList.add('hidden');
    document.getElementById('importStep2').classList.remove('hidden');
}

function goToImportStep1() {
    document.getElementById('importStep2').classList.add('hidden');
    document.getElementById('importStep1').classList.remove('hidden');
}

function downloadTemplate() {
    const projectId = document.getElementById('import_project_id').value;
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih project terlebih dahulu',
        });
        return;
    }
    
    window.location.href = '{{ route("perusahaan.karyawans.download-template") }}?project_id=' + projectId;
}

function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = 'File dipilih: ' + input.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
}

// Handle form submission with AJAX
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('import_file');
    if (!fileInput.files || !fileInput.files[0]) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih file Excel terlebih dahulu',
        });
        return;
    }
    
    // Show progress step
    document.getElementById('importStep2').classList.add('hidden');
    document.getElementById('importStep3').classList.remove('hidden');
    
    // Reset progress
    resetProgress();
    
    // Submit form via AJAX
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            importJobId = data.job_id;
            startProgressPolling();
        } else {
            showError('Gagal memulai import: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Terjadi kesalahan saat memulai import');
    });
});

function resetProgress() {
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('progressPercentage').textContent = '0%';
    document.getElementById('progressMessage').textContent = 'Memulai import...';
    document.getElementById('successCount').textContent = '0';
    document.getElementById('skippedCount').textContent = '0';
    document.getElementById('totalProcessed').textContent = '0';
    document.getElementById('recentErrors').classList.add('hidden');
    document.getElementById('progressActions').classList.remove('hidden');
    document.getElementById('completionActions').classList.add('hidden');
}

function startProgressPolling() {
    if (progressInterval) {
        clearInterval(progressInterval);
    }
    
    progressInterval = setInterval(checkProgress, 2000); // Check every 2 seconds
    checkProgress(); // Check immediately
}

function checkProgress() {
    if (!importJobId) return;
    
    fetch(`{{ route('perusahaan.karyawans.import-progress') }}?job_id=${importJobId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProgress(data.data);
            } else {
                console.error('Progress check failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Progress check error:', error);
        });
}

function updateProgress(progress) {
    // Update progress bar
    document.getElementById('progressBar').style.width = progress.percentage + '%';
    document.getElementById('progressPercentage').textContent = progress.percentage + '%';
    document.getElementById('progressMessage').textContent = progress.message;
    
    // Update stats
    document.getElementById('successCount').textContent = progress.success_count;
    document.getElementById('skippedCount').textContent = progress.skipped_count;
    document.getElementById('totalProcessed').textContent = progress.success_count + progress.skipped_count;
    
    // Show errors if any
    if (progress.errors && progress.errors.length > 0) {
        document.getElementById('recentErrors').classList.remove('hidden');
        const errorList = document.getElementById('errorList');
        errorList.innerHTML = progress.errors.map(error => 
            `<div class="text-sm text-red-700 mb-1">• ${error}</div>`
        ).join('');
    }
    
    // Check if completed
    if (progress.completed) {
        clearInterval(progressInterval);
        progressInterval = null;
        
        document.getElementById('progressActions').classList.add('hidden');
        document.getElementById('completionActions').classList.remove('hidden');
        
        // Show completion message
        if (progress.success_count > 0) {
            Swal.fire({
                icon: 'success',
                title: 'Import Selesai!',
                text: `Berhasil import ${progress.success_count} karyawan${progress.skipped_count > 0 ? `, ${progress.skipped_count} data di-skip` : ''}`,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Import Gagal',
                text: 'Tidak ada data yang berhasil diimport. Periksa format file dan data.',
            });
        }
    }
}

function cancelImport() {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    closeImportModal();
}

function startNewImport() {
    // Reset form and go back to step 1
    document.getElementById('importForm').reset();
    document.getElementById('fileName').classList.add('hidden');
    document.getElementById('importStep3').classList.add('hidden');
    document.getElementById('importStep1').classList.remove('hidden');
    
    // Clear progress data
    importJobId = null;
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
}

function showError(message) {
    document.getElementById('importStep3').classList.add('hidden');
    document.getElementById('importStep2').classList.remove('hidden');
    
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
    });
}

// Initialize Select2 for Kota and Provinsi with tagging (add new option)
$(document).ready(function() {
    // Data kota dan provinsi Indonesia (sample data)
    const kotaData = [
        'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 
        'Tangerang', 'Depok', 'Bekasi', 'Bogor', 'Batam', 'Pekanbaru', 'Bandar Lampung',
        'Padang', 'Malang', 'Denpasar', 'Samarinda', 'Banjarmasin', 'Pontianak', 'Manado',
        'Balikpapan', 'Jambi', 'Cimahi', 'Surakarta', 'Yogyakarta', 'Serang', 'Mataram',
        'Kupang', 'Ambon', 'Jayapura', 'Sorong', 'Ternate', 'Bengkulu', 'Palu', 'Kendari'
    ];
    
    const provinsiData = [
        'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi',
        'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung',
        'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
        'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat',
        'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
        'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan',
        'Sulawesi Tenggara', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Tengah',
        'Papua Pegunungan', 'Papua Selatan', 'Papua Barat Daya'
    ];
    
    // Initialize Kota Select2
    $('#kotaSelect').select2({
        tags: true,
        data: kotaData,
        placeholder: 'Ketik untuk mencari atau tambah kota baru...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#wizardModal'),
        language: {
            noResults: function() {
                return "Tidak ditemukan. Ketik dan tekan Enter untuk menambahkan.";
            },
            searching: function() {
                return "Mencari...";
            }
        },
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        templateResult: function(data) {
            if (data.loading) {
                return 'Mencari...';
            }
            if (data.newTag) {
                return $('<div class="flex items-center"><i class="fas fa-plus-circle mr-2 text-green-600"></i><span>Tambah: <strong>' + data.text + '</strong></span></div>');
            }
            return $('<div>' + data.text + '</div>');
        },
        templateSelection: function(data) {
            return data.text;
        }
    });
    
    // Initialize Provinsi Select2
    $('#provinsiSelect').select2({
        tags: true,
        data: provinsiData,
        placeholder: 'Ketik untuk mencari atau tambah provinsi baru...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#wizardModal'),
        language: {
            noResults: function() {
                return "Tidak ditemukan. Ketik dan tekan Enter untuk menambahkan.";
            },
            searching: function() {
                return "Mencari...";
            }
        },
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        templateResult: function(data) {
            if (data.loading) {
                return 'Mencari...';
            }
            if (data.newTag) {
                return $('<div class="flex items-center"><i class="fas fa-plus-circle mr-2 text-green-600"></i><span>Tambah: <strong>' + data.text + '</strong></span></div>');
            }
            return $('<div>' + data.text + '</div>');
        },
        templateSelection: function(data) {
            return data.text;
        }
    });
    
    // Reset Select2 when modal closes
    $('#wizardModal').on('hidden', function() {
        $('#kotaSelect').val(null).trigger('change');
        $('#provinsiSelect').val(null).trigger('change');
    });
});
</script>
@endpush

@endsection



@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 3;

function openWizardModal() {
    document.getElementById('wizardModal').classList.remove('hidden');
    currentStep = 1;
    showStep(1);
    
    // Attach event listener after modal is opened
    setTimeout(() => {
        const statusSelect = document.getElementById('statusKaryawanCreate');
        if (statusSelect) {
            // Remove existing listener to avoid duplicates
            statusSelect.removeEventListener('change', toggleTanggalKeluarCreate);
            // Add new listener
            statusSelect.addEventListener('change', toggleTanggalKeluarCreate);
        }
    }, 100);
}

function closeWizardModal() {
    document.getElementById('wizardModal').classList.add('hidden');
    document.getElementById('wizardForm').reset();
    currentStep = 1;
    showStep(1);
    
    // Reset tanggal keluar field
    const tanggalKeluarWrapper = document.getElementById('tanggalKeluarWrapperCreate');
    const tanggalKeluarInput = document.getElementById('tanggalKeluarCreate');
    if (tanggalKeluarWrapper) tanggalKeluarWrapper.classList.add('hidden');
    if (tanggalKeluarInput) {
        tanggalKeluarInput.removeAttribute('required');
        tanggalKeluarInput.value = '';
    }
}

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
    
    // Show current step
    document.getElementById('step' + step).classList.remove('hidden');
    
    // Update step indicators
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById('step' + i + '-indicator');
        const line = document.getElementById('line' + i);
        
        if (!indicator) continue;
        
        if (i < step) {
            // Completed step
            indicator.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
            indicator.innerHTML = '<i class="fas fa-check"></i>';
            indicator.classList.remove('bg-gray-200', 'text-gray-400');
            indicator.classList.add('text-white');
            if (line) {
                line.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
            }
        } else if (i === step) {
            // Current step
            indicator.style.background = 'linear-gradient(135deg, #3B82C8 0%, #2563A8 100%)';
            indicator.classList.remove('bg-gray-200', 'text-gray-400');
            indicator.classList.add('text-white');
            
            // Update icon based on step
            if (i === 1) indicator.innerHTML = '<i class="fas fa-briefcase"></i>';
            else if (i === 2) indicator.innerHTML = '<i class="fas fa-user"></i>';
            else if (i === 3) indicator.innerHTML = '<i class="fas fa-key"></i>';
            
            if (line) {
                line.style.background = '#E5E7EB';
            }
        } else {
            // Future step
            indicator.style.background = '#E5E7EB';
            indicator.classList.add('bg-gray-200', 'text-gray-400');
            indicator.classList.remove('text-white');
            
            if (i === 1) indicator.innerHTML = '<i class="fas fa-briefcase"></i>';
            else if (i === 2) indicator.innerHTML = '<i class="fas fa-user"></i>';
            else if (i === 3) indicator.innerHTML = '<i class="fas fa-key"></i>';
            
            if (line) {
                line.style.background = '#E5E7EB';
            }
        }
        
        // Update step text colors
        const stepTextContainer = indicator.parentElement?.nextElementSibling;
        if (stepTextContainer) {
            const title = stepTextContainer.querySelector('p:first-child');
            const subtitle = stepTextContainer.querySelector('p:last-child');
            
            if (title && subtitle) {
                if (i <= step) {
                    title.classList.remove('text-gray-500');
                    title.classList.add('text-gray-900');
                    subtitle.classList.remove('text-gray-400');
                    subtitle.classList.add('text-gray-500');
                } else {
                    title.classList.remove('text-gray-900');
                    title.classList.add('text-gray-500');
                    subtitle.classList.remove('text-gray-500');
                    subtitle.classList.add('text-gray-400');
                }
            }
        }
    }
    
    // Update buttons
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (step === 1) {
        btnPrev.classList.add('hidden');
        btnNext.classList.remove('hidden');
        btnSubmit.classList.add('hidden');
    } else if (step === totalSteps) {
        btnPrev.classList.remove('hidden');
        btnNext.classList.add('hidden');
        btnSubmit.classList.remove('hidden');
    } else {
        btnPrev.classList.remove('hidden');
        btnNext.classList.remove('hidden');
        btnSubmit.classList.add('hidden');
    }
}

function validateStep(step) {
    const stepElement = document.getElementById('step' + step);
    const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
    
    let isValid = true;
    let firstInvalidInput = null;
    
    inputs.forEach(input => {
        // Remove previous error styling
        input.classList.remove('border-red-500');
        
        // Check if empty
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('border-red-500');
            if (!firstInvalidInput) firstInvalidInput = input;
        }
        
        // Check NIK KTP length
        if (input.name === 'nik_ktp' && input.value.trim() && input.value.length !== 16) {
            isValid = false;
            input.classList.add('border-red-500');
            if (!firstInvalidInput) firstInvalidInput = input;
        }
        
        // Check email format
        if (input.type === 'email' && input.value.trim() && !input.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            isValid = false;
            input.classList.add('border-red-500');
            if (!firstInvalidInput) firstInvalidInput = input;
        }
        
        // Check password confirmation
        if (input.name === 'password_confirmation' && input.value.trim()) {
            const password = document.querySelector('[name="password"]').value;
            if (input.value !== password) {
                isValid = false;
                input.classList.add('border-red-500');
                if (!firstInvalidInput) firstInvalidInput = input;
            }
        }
    });
    
    if (!isValid) {
        if (firstInvalidInput) {
            firstInvalidInput.focus();
        }
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Mohon lengkapi semua field yang wajib diisi dengan benar',
            confirmButtonColor: '#3B82C8'
        });
    }
    
    return isValid;
}

function nextStep() {
    if (validateStep(currentStep)) {
        currentStep++;
        showStep(currentStep);
    }
}

function prevStep() {
    currentStep--;
    showStep(currentStep);
}

// Handle form submit
document.getElementById('wizardForm')?.addEventListener('submit', function(e) {
    // Validate current step before submit
    if (!validateStep(currentStep)) {
        e.preventDefault();
        return false;
    }
});

async function openEditWizard(hashId) {
    try {
        const response = await fetch(`/perusahaan/karyawans/${hashId}/edit`);
        const data = await response.json();
        
        // Populate form fields
        document.querySelector('[name="nik_karyawan"]').value = data.nik_karyawan || '';
        document.querySelector('[name="status_karyawan"]').value = data.status_karyawan || '';
        document.querySelector('[name="jabatan_id"]').value = data.jabatan_id || '';
        document.querySelector('[name="tanggal_masuk"]').value = data.tanggal_masuk || '';
        document.querySelector('[name="is_active"]').value = data.is_active ? '1' : '0';
        document.querySelector('[name="nama_lengkap"]').value = data.nama_lengkap || '';
        document.querySelector('[name="nik_ktp"]').value = data.nik_ktp || '';
        document.querySelector('[name="tempat_lahir"]').value = data.tempat_lahir || '';
        document.querySelector('[name="tanggal_lahir"]').value = data.tanggal_lahir || '';
        document.querySelector('[name="jenis_kelamin"]').value = data.jenis_kelamin || '';
        document.querySelector('[name="telepon"]').value = data.telepon || '';
        document.querySelector('[name="alamat"]').value = data.alamat || '';
        document.querySelector('[name="kota"]').value = data.kota || '';
        document.querySelector('[name="provinsi"]').value = data.provinsi || '';
        document.querySelector('[name="email"]').value = data.email || '';
        
        // Update form action and method
        const form = document.getElementById('wizardForm');
        form.action = `/perusahaan/karyawans/${hashId}`;
        
        // Add PUT method
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        // Make password optional for edit
        document.querySelector('[name="password"]').removeAttribute('required');
        document.querySelector('[name="password_confirmation"]').removeAttribute('required');
        
        // Update modal title
        document.querySelector('#wizardModal h3').textContent = 'Edit Data Karyawan';
        
        openWizardModal();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data karyawan',
            confirmButtonColor: '#3B82C8'
        });
    }
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data karyawan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/karyawans/${hashId}`;
            form.submit();
        }
    });
}

// Close modal when clicking outside
document.getElementById('wizardModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeWizardModal();
});

// Validate NIK KTP (16 digits)
document.querySelector('[name="nik_ktp"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').substring(0, 16);
});

// Toggle tanggal keluar based on status karyawan in create form
function toggleTanggalKeluarCreate() {
    const statusSelect = document.getElementById('statusKaryawanCreate');
    const tanggalKeluarWrapper = document.getElementById('tanggalKeluarWrapperCreate');
    const tanggalKeluarInput = document.getElementById('tanggalKeluarCreate');
    
    if (!statusSelect || !tanggalKeluarWrapper || !tanggalKeluarInput) return;
    
    const selectedStatus = statusSelect.value.toLowerCase();
    const isKontrak = selectedStatus.includes('kontrak');
    
    if (isKontrak) {
        tanggalKeluarWrapper.classList.remove('hidden');
        tanggalKeluarInput.setAttribute('required', 'required');
    } else {
        tanggalKeluarWrapper.classList.add('hidden');
        tanggalKeluarInput.removeAttribute('required');
        tanggalKeluarInput.value = '';
    }
}

// Load jabatan based on selected project in create form
function loadJabatansByProjectCreate() {
    const projectSelect = document.getElementById('projectSelectCreate');
    const jabatanSelect = document.getElementById('jabatanSelectCreate');
    const projectId = projectSelect.value;
    
    if (!projectId) {
        jabatanSelect.innerHTML = '<option value="">Pilih project terlebih dahulu</option>';
        jabatanSelect.disabled = true;
        return;
    }
    
    // Show loading
    jabatanSelect.innerHTML = '<option value="">Loading...</option>';
    jabatanSelect.disabled = true;
    
    // Fetch jabatan by project
    fetch(`/perusahaan/projects/${projectId}/jabatans`)
        .then(response => response.json())
        .then(data => {
            jabatanSelect.innerHTML = '<option value="">Pilih Jabatan</option>';
            data.forEach(jabatan => {
                const option = document.createElement('option');
                option.value = jabatan.id;
                option.textContent = jabatan.nama;
                jabatanSelect.appendChild(option);
            });
            jabatanSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading jabatan:', error);
            jabatanSelect.innerHTML = '<option value="">Error loading jabatan</option>';
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal memuat data jabatan',
                confirmButtonColor: '#3B82C8'
            });
        });
}
</script>
@endpush
