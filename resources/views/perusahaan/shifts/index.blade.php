@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Shift')
@section('page-title', 'Manajemen Shift')
@section('page-subtitle', 'Kelola shift kerja karyawan per project')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Shift</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $shifts->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <button onclick="openTambahModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Shift
    </button>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode shift atau nama shift..." class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm" style="focus:ring-color: #3B82C8;">
        </div>
        <select name="project_id" class="lg:w-64 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            <option value="">Semua Project</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-3 rounded-xl font-medium transition text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request()->hasAny(['search', 'project_id']))
            <a href="{{ route('perusahaan.kehadiran.manajemen-shift') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
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
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-code mr-2" style="color: #3B82C8;"></i>Kode Shift
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Nama Shift
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-clock mr-2" style="color: #3B82C8;"></i>Jam Kerja
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-coffee mr-2" style="color: #3B82C8;"></i>Istirahat
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-hourglass-half mr-2" style="color: #3B82C8;"></i>Toleransi
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($shifts as $shift)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <i class="fas fa-project-diagram text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $shift->project->nama }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-lg font-bold text-sm text-white" style="background: {{ $shift->warna }};">
                            {{ $shift->kode_shift }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $shift->nama_shift }}</p>
                            @if($shift->deskripsi)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($shift->deskripsi, 50) }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-clock mr-2" style="color: #3B82C8;"></i>
                            <span class="text-sm font-medium">{{ date('H:i', strtotime($shift->jam_mulai)) }} - {{ date('H:i', strtotime($shift->jam_selesai)) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-700">{{ $shift->durasi_istirahat }} menit</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-700">{{ $shift->toleransi_keterlambatan }} menit</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditModal('{{ $shift->hash_id }}')" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDelete('{{ $shift->hash_id }}')" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium" title="Hapus">
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
                                <i class="fas fa-clock text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request()->hasAny(['search', 'project_id']))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada shift
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request()->hasAny(['search', 'project_id']))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan shift pertama untuk memulai
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

<!-- Modal Tambah Shift -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Tambah Shift</h3>
                <button onclick="closeTambahModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ route('perusahaan.shifts.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Info Box untuk HL dan OFF -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 mb-1">Informasi Penting</p>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Untuk <strong>Hari Libur (HL)</strong> dan <strong>Off Duty (OFF)</strong>, isi jam shift <strong>00:00 - 00:00</strong></li>
                                <li>• Durasi istirahat dan toleransi untuk HL/OFF bisa diisi <strong>0 menit</strong></li>
                                <li>• Kode shift dalam 1 project yang sama harus berbeda</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kode Shift -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode Shift <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_shift" required maxlength="10" placeholder="Contoh: SP, SS, SM, HL, OFF" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Singkatan untuk rekap. Contoh: SP (Shift Pagi), SS (Shift Siang), SM (Shift Malam), HL (Hari Libur), OFF (Off Duty)
                    </p>
                </div>

                <!-- Nama Shift -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Shift <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_shift" required maxlength="100" placeholder="Contoh: Shift Pagi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Jam Mulai & Selesai -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="jam_mulai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="jam_selesai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Durasi Istirahat & Toleransi -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Durasi Istirahat (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="durasi_istirahat" required min="0" value="60" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Toleransi Keterlambatan (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="toleransi_keterlambatan" required min="0" value="15" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi shift (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Warna -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Warna <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3 items-center">
                        <input type="color" name="warna" id="warna_tambah" required value="#3B82C8" class="w-16 h-12 border border-gray-300 rounded-xl cursor-pointer">
                        <div class="flex-1">
                            <p class="text-xs text-gray-600 mb-2">Pilih warna untuk shift ini atau gunakan preset:</p>
                            <div class="flex gap-2 flex-wrap">
                                <button type="button" onclick="setColor('#3B82F6', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #3B82F6;" title="Blue"></button>
                                <button type="button" onclick="setColor('#F59E0B', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #F59E0B;" title="Orange"></button>
                                <button type="button" onclick="setColor('#8B5CF6', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #8B5CF6;" title="Purple"></button>
                                <button type="button" onclick="setColor('#EF4444', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #EF4444;" title="Red"></button>
                                <button type="button" onclick="setColor('#10B981', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #10B981;" title="Green"></button>
                                <button type="button" onclick="setColor('#6B7280', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #6B7280;" title="Gray"></button>
                                <button type="button" onclick="setColor('#EC4899', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #EC4899;" title="Pink"></button>
                                <button type="button" onclick="setColor('#14B8A6', 'warna_tambah')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #14B8A6;" title="Teal"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeTambahModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Shift -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Edit Shift</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formEdit" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="edit_project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kode Shift -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode Shift <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_shift" id="edit_kode_shift" required maxlength="10" placeholder="Contoh: SP, SS, SM" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Singkatan untuk rekap. Contoh: SP (Shift Pagi), SS (Shift Siang), SM (Shift Malam)
                    </p>
                </div>

                <!-- Nama Shift -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Shift <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_shift" id="edit_nama_shift" required maxlength="100" placeholder="Contoh: Shift Pagi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Jam Mulai & Selesai -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Durasi Istirahat & Toleransi -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Durasi Istirahat (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="durasi_istirahat" id="edit_durasi_istirahat" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Toleransi Keterlambatan (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="toleransi_keterlambatan" id="edit_toleransi_keterlambatan" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" placeholder="Deskripsi shift (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                </div>

                <!-- Warna -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Warna <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3 items-center">
                        <input type="color" name="warna" id="edit_warna" required value="#3B82C8" class="w-16 h-12 border border-gray-300 rounded-xl cursor-pointer">
                        <div class="flex-1">
                            <p class="text-xs text-gray-600 mb-2">Pilih warna untuk shift ini atau gunakan preset:</p>
                            <div class="flex gap-2 flex-wrap">
                                <button type="button" onclick="setColor('#3B82F6', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #3B82F6;" title="Blue"></button>
                                <button type="button" onclick="setColor('#F59E0B', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #F59E0B;" title="Orange"></button>
                                <button type="button" onclick="setColor('#8B5CF6', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #8B5CF6;" title="Purple"></button>
                                <button type="button" onclick="setColor('#EF4444', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #EF4444;" title="Red"></button>
                                <button type="button" onclick="setColor('#10B981', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #10B981;" title="Green"></button>
                                <button type="button" onclick="setColor('#6B7280', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #6B7280;" title="Gray"></button>
                                <button type="button" onclick="setColor('#EC4899', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #EC4899;" title="Pink"></button>
                                <button type="button" onclick="setColor('#14B8A6', 'edit_warna')" class="w-8 h-8 rounded-lg border-2 border-gray-300 hover:border-gray-500" style="background: #14B8A6;" title="Teal"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete (Hidden) -->
<form id="formDelete" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    const shiftsData = @json($shifts);

    function setColor(color, inputId) {
        document.getElementById(inputId).value = color;
    }

    function openTambahModal() {
        document.getElementById('modalTambah').classList.remove('hidden');
    }

    function closeTambahModal() {
        document.getElementById('modalTambah').classList.add('hidden');
    }

    function openEditModal(hashId) {
        const shift = shiftsData.find(s => s.hash_id === hashId);
        if (!shift) return;

        document.getElementById('formEdit').action = `/perusahaan/shifts/${hashId}`;
        document.getElementById('edit_project_id').value = shift.project_id;
        document.getElementById('edit_kode_shift').value = shift.kode_shift;
        document.getElementById('edit_nama_shift').value = shift.nama_shift;
        
        // Convert HH:MM:SS to HH:MM for time input
        document.getElementById('edit_jam_mulai').value = shift.jam_mulai.substring(0, 5);
        document.getElementById('edit_jam_selesai').value = shift.jam_selesai.substring(0, 5);
        
        document.getElementById('edit_durasi_istirahat').value = shift.durasi_istirahat;
        document.getElementById('edit_toleransi_keterlambatan').value = shift.toleransi_keterlambatan;
        document.getElementById('edit_deskripsi').value = shift.deskripsi || '';
        document.getElementById('edit_warna').value = shift.warna || '#3B82C8';

        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }

    function confirmDelete(hashId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Shift ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formDelete');
                form.action = `/perusahaan/shifts/${hashId}`;
                form.submit();
            }
        });
    }

    // Close modal when clicking outside
    document.getElementById('modalTambah').addEventListener('click', function(e) {
        if (e.target === this) closeTambahModal();
    });

    document.getElementById('modalEdit').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endpush
@endsection
