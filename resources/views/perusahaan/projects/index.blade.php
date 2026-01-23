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
                    <button onclick="openGuestBookModal('{{ $project->hash_id }}')" 
                            class="text-purple-600 hover:text-purple-800 p-2 rounded-lg hover:bg-purple-50 transition" 
                            title="Pengaturan Buku Tamu">
                        <i class="fas fa-book text-lg"></i>
                    </button>
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

                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2.5 w-4"></i>
                    <span class="font-medium">Batas Cuti:</span>
                    <div class="ml-2 flex items-center group">
                        <span id="batas-cuti-display-{{ $project->hash_id }}" class="cursor-pointer">{{ $project->batas_cuti_tahunan }} hari/tahun</span>
                        <button 
                            type="button" 
                            onclick="editBatasCuti('{{ $project->hash_id }}', {{ $project->batas_cuti_tahunan }})"
                            class="ml-2 text-blue-500 hover:text-blue-700 transition-colors"
                            title="Edit Batas Cuti"
                        >
                            <i class="fas fa-pencil-alt text-xs"></i>
                        </button>
                        <div id="batas-cuti-edit-{{ $project->hash_id }}" class="hidden ml-2 flex items-center">
                            <input 
                                type="number" 
                                id="batas-cuti-input-{{ $project->hash_id }}"
                                min="1" 
                                max="365" 
                                value="{{ $project->batas_cuti_tahunan }}"
                                class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            <span class="text-xs text-gray-500 ml-1">hari</span>
                            <button 
                                type="button" 
                                onclick="saveBatasCuti('{{ $project->hash_id }}')"
                                class="ml-2 text-green-600 hover:text-green-800"
                                title="Simpan"
                            >
                                <i class="fas fa-check text-xs"></i>
                            </button>
                            <button 
                                type="button" 
                                onclick="cancelEditBatasCuti('{{ $project->hash_id }}', {{ $project->batas_cuti_tahunan }})"
                                class="ml-1 text-red-600 hover:text-red-800"
                                title="Batal"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-book text-gray-400 mr-2.5 w-4"></i>
                    <span class="font-medium">Buku Tamu:</span>
                    <span class="ml-2">
                        @if($project->guest_book_mode === 'standard_migas')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                Standard MIGAS
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Simple
                            </span>
                        @endif
                        @if($project->enable_questionnaire)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 ml-1">
                                + Kuesioner
                            </span>
                        @endif
                        @if($project->enable_guest_card)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 ml-1">
                                + Kartu Tamu
                            </span>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batas Cuti Tahunan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="batas_cuti_tahunan" 
                                value="12"
                                min="1"
                                max="365"
                                required
                                class="w-full px-4 py-3 pr-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="12"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">hari/tahun</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maksimal hari cuti yang dapat diambil karyawan dalam 1 tahun</p>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batas Cuti Tahunan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="batas_cuti_tahunan" 
                                id="edit_batas_cuti_tahunan"
                                min="1"
                                max="365"
                                required
                                class="w-full px-4 py-3 pr-20 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="12"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">hari/tahun</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maksimal hari cuti yang dapat diambil karyawan dalam 1 tahun</p>
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

<!-- Modal Guest Book Settings -->
<div id="modalGuestBook" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <form id="formGuestBook" method="POST" class="flex flex-col max-h-[90vh]">
            @csrf
            @method('PUT')
            
            <!-- Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Pengaturan Buku Tamu</h3>
                        <p class="text-sm text-gray-600">Konfigurasi mode dan fitur buku tamu</p>
                    </div>
                </div>
            </div>
            
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-6">
                    <!-- Guest Book Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Mode Buku Tamu</label>
                        <div class="space-y-3">
                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="guest_book_mode" value="standard_migas" id="mode_standard" class="mt-1 mr-3 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <span class="font-semibold text-gray-900">Standard MIGAS</span>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            Lengkap
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">Form lengkap dengan semua field sesuai standar MIGAS (NIK, foto KTP, kontak darurat, dll)</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="guest_book_mode" value="simple" id="mode_simple" class="mt-1 mr-3 text-green-600 focus:ring-green-500">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <span class="font-semibold text-gray-900">Simple</span>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Sederhana
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">Form sederhana hanya dengan field dasar (nama, perusahaan, keperluan, foto)</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Enable Questionnaire -->
                    <div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Aktifkan Kuesioner</label>
                                <p class="text-sm text-gray-500 mt-1">Tampilkan kuesioner dinamis berdasarkan area patrol</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_questionnaire" id="enable_questionnaire" class="sr-only peer" value="1">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-amber-600 mt-0.5 mr-2"></i>
                                <div class="text-sm text-amber-800">
                                    <p class="font-medium mb-1">Catatan Kuesioner:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Kuesioner akan muncul jika tamu memilih area patrol</li>
                                        <li>Pertanyaan dapat dikonfigurasi di menu Patrol → Inventaris Patroli → Pertanyaan Tamu</li>
                                        <li>Jika dinonaktifkan, kuesioner tidak akan ditampilkan sama sekali</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enable Guest Card -->
                    <div>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Aktifkan Kartu Tamu</label>
                                <p class="text-sm text-gray-500 mt-1">Gunakan sistem kartu tamu fisik untuk project ini</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_guest_card" id="enable_guest_card" class="sr-only peer" value="1">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <!-- Area Selection for Guest Card -->
                        <div id="guest_card_areas_section" class="mt-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Area untuk Kartu Tamu</label>
                            <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox" name="all_areas" id="all_areas" class="mr-3 text-blue-600 focus:ring-blue-500" onchange="toggleAllAreas()">
                                    <span class="font-medium text-gray-900">Semua Area</span>
                                </label>
                                <div class="border-t border-gray-200 pt-2" id="areas_list">
                                    <!-- Areas will be loaded dynamically -->
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Pilih area mana saja yang akan menggunakan sistem kartu tamu. Area yang dipilih akan muncul di menu Kartu Tamu.</p>
                        </div>
                        
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Catatan Kartu Tamu:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Kartu tamu fisik dapat digunakan untuk identifikasi tamu</li>
                                        <li>Hanya area yang dipilih yang akan muncul di menu Kartu Tamu</li>
                                        <li>Jika dinonaktifkan, menu Kartu Tamu tidak akan ditampilkan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeGuestBookModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition"
                    >
                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
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

async function openGuestBookModal(hashId) {
    try {
        const response = await fetch(`/perusahaan/projects/${hashId}/edit`);
        const data = await response.json();
        
        // Set guest book mode
        const modeRadios = document.querySelectorAll('input[name="guest_book_mode"]');
        modeRadios.forEach(radio => {
            radio.checked = radio.value === data.guest_book_mode;
            
            // Update visual state
            const label = radio.closest('label');
            if (radio.checked) {
                label.classList.add('border-blue-500', 'bg-blue-50');
                label.classList.remove('border-gray-200');
            } else {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-200');
            }
        });
        
        // Set questionnaire toggle
        document.getElementById('enable_questionnaire').checked = data.enable_questionnaire;
        
        // Set guest card toggle
        document.getElementById('enable_guest_card').checked = data.enable_guest_card;
        
        // Load areas for guest card selection
        await loadProjectAreas(hashId, data.guest_card_area_ids || []);
        
        // Show/hide guest card areas section
        toggleGuestCardAreas();
        
        // Set form action
        document.getElementById('formGuestBook').action = `/perusahaan/projects/${hashId}/guest-book-settings`;
        
        document.getElementById('modalGuestBook').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat pengaturan buku tamu'
        });
    }
}

async function loadProjectAreas(projectId, selectedAreaIds = []) {
    try {
        const response = await fetch(`/perusahaan/projects/${projectId}/guest-card-areas`);
        const areas = await response.json();
        
        const areasList = document.getElementById('areas_list');
        areasList.innerHTML = '';
        
        if (areas.length === 0) {
            areasList.innerHTML = '<p class="text-sm text-gray-500 italic">Belum ada area di project ini</p>';
            return;
        }
        
        areas.forEach(area => {
            const isChecked = selectedAreaIds.includes(area.id);
            const areaHtml = `
                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                    <input type="checkbox" name="guest_card_area_ids[]" value="${area.id}" 
                           class="area-checkbox mr-3 text-blue-600 focus:ring-blue-500" 
                           ${isChecked ? 'checked' : ''} onchange="updateAllAreasCheckbox()">
                    <span class="text-gray-700">${area.nama}</span>
                </label>
            `;
            areasList.insertAdjacentHTML('beforeend', areaHtml);
        });
        
        // Update "Semua Area" checkbox state
        updateAllAreasCheckbox();
        
    } catch (error) {
        console.error('Error loading areas:', error);
        document.getElementById('areas_list').innerHTML = '<p class="text-sm text-red-500">Gagal memuat area</p>';
    }
}

function toggleGuestCardAreas() {
    const enableGuestCard = document.getElementById('enable_guest_card').checked;
    const areasSection = document.getElementById('guest_card_areas_section');
    
    if (enableGuestCard) {
        areasSection.classList.remove('hidden');
    } else {
        areasSection.classList.add('hidden');
        // Uncheck all areas when disabled
        document.querySelectorAll('input[name="guest_card_area_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('all_areas').checked = false;
    }
}

function toggleAllAreas() {
    const allAreasCheckbox = document.getElementById('all_areas');
    const areaCheckboxes = document.querySelectorAll('input[name="guest_card_area_ids[]"]');
    
    areaCheckboxes.forEach(checkbox => {
        checkbox.checked = allAreasCheckbox.checked;
    });
}

function updateAllAreasCheckbox() {
    const areaCheckboxes = document.querySelectorAll('input[name="guest_card_area_ids[]"]');
    const allAreasCheckbox = document.getElementById('all_areas');
    
    if (areaCheckboxes.length === 0) {
        allAreasCheckbox.checked = false;
        return;
    }
    
    const checkedCount = Array.from(areaCheckboxes).filter(cb => cb.checked).length;
    allAreasCheckbox.checked = checkedCount === areaCheckboxes.length;
}

function closeGuestBookModal() {
    document.getElementById('modalGuestBook').classList.add('hidden');
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
        document.getElementById('edit_batas_cuti_tahunan').value = data.batas_cuti_tahunan || 12;
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

document.getElementById('modalGuestBook')?.addEventListener('click', function(e) {
    if (e.target === this) closeGuestBookModal();
});

// Handle guest book mode radio button visual feedback
document.addEventListener('DOMContentLoaded', function() {
    const modeRadios = document.querySelectorAll('input[name="guest_book_mode"]');
    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Reset all labels
            modeRadios.forEach(r => {
                const label = r.closest('label');
                label.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
                label.classList.add('border-gray-200');
            });
            
            // Highlight selected
            if (this.checked) {
                const label = this.closest('label');
                label.classList.remove('border-gray-200');
                if (this.value === 'standard_migas') {
                    label.classList.add('border-blue-500', 'bg-blue-50');
                } else {
                    label.classList.add('border-green-500', 'bg-green-50');
                }
            }
        });
    });
    
    // Handle guest card toggle
    const guestCardToggle = document.getElementById('enable_guest_card');
    if (guestCardToggle) {
        guestCardToggle.addEventListener('change', toggleGuestCardAreas);
    }
});

// Inline Edit Batas Cuti Functions
function editBatasCuti(projectId, currentValue) {
    // Hide display, show edit
    document.getElementById(`batas-cuti-display-${projectId}`).classList.add('hidden');
    document.getElementById(`batas-cuti-edit-${projectId}`).classList.remove('hidden');
    
    // Focus on input
    const input = document.getElementById(`batas-cuti-input-${projectId}`);
    input.focus();
    input.select();
}

function cancelEditBatasCuti(projectId, originalValue) {
    // Reset input value
    document.getElementById(`batas-cuti-input-${projectId}`).value = originalValue;
    
    // Show display, hide edit
    document.getElementById(`batas-cuti-display-${projectId}`).classList.remove('hidden');
    document.getElementById(`batas-cuti-edit-${projectId}`).classList.add('hidden');
}

async function saveBatasCuti(projectId) {
    const input = document.getElementById(`batas-cuti-input-${projectId}`);
    const newValue = parseInt(input.value);
    
    // Validation
    if (!newValue || newValue < 1 || newValue > 365) {
        Swal.fire({
            icon: 'warning',
            title: 'Nilai Tidak Valid!',
            text: 'Batas cuti harus antara 1-365 hari',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    try {
        // Show loading
        const display = document.getElementById(`batas-cuti-display-${projectId}`);
        const originalText = display.textContent;
        display.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Menyimpan...';
        display.classList.remove('hidden');
        document.getElementById(`batas-cuti-edit-${projectId}`).classList.add('hidden');
        
        const response = await fetch(`/perusahaan/projects/${projectId}/batas-cuti`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                batas_cuti_tahunan: newValue
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update display
            display.textContent = `${newValue} hari/tahun`;
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            throw new Error(data.message || 'Gagal menyimpan');
        }
    } catch (error) {
        // Restore original display
        document.getElementById(`batas-cuti-display-${projectId}`).textContent = originalText;
        document.getElementById(`batas-cuti-display-${projectId}`).classList.remove('hidden');
        document.getElementById(`batas-cuti-edit-${projectId}`).classList.add('hidden');
        
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan saat menyimpan',
            confirmButtonText: 'OK'
        });
    }
}

// Add keyboard event listeners for inline edit
document.addEventListener('DOMContentLoaded', function() {
    // Handle keyboard events for all batas cuti inputs
    document.addEventListener('keydown', function(e) {
        if (e.target.id && e.target.id.startsWith('batas-cuti-input-')) {
            const projectId = e.target.id.replace('batas-cuti-input-', '');
            const originalValue = parseInt(e.target.getAttribute('data-original-value') || e.target.value);
            
            if (e.key === 'Enter') {
                e.preventDefault();
                saveBatasCuti(projectId);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEditBatasCuti(projectId, originalValue);
            }
        }
    });
    
    // Store original value when editing starts
    document.addEventListener('focus', function(e) {
        if (e.target.id && e.target.id.startsWith('batas-cuti-input-')) {
            e.target.setAttribute('data-original-value', e.target.value);
        }
    });
});
</script>
@endpush
