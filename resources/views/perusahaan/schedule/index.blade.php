@extends('perusahaan.layouts.app')

@section('title', 'Schedule Kehadiran')
@section('page-title', 'Schedule Kehadiran')
@section('page-subtitle', 'Kelola jadwal shift karyawan dengan mudah')

@section('content')
<!-- Alert Messages -->
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <i class="fas fa-check-circle text-green-600 text-xl"></i>
    <p class="text-green-800 font-medium">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
    <p class="text-red-800 font-medium">{{ session('error') }}</p>
</div>
@endif

@if(session('warning'))
<div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
    <div class="flex-1">
        <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Karyawan</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $karyawans->total() }}</p>
                </div>
            </div>
        </div>
        
        @if($projectId)
        <!-- Quick Action Buttons -->
        <div class="flex gap-2">
            <form method="GET" class="inline">
                <input type="hidden" name="project_id" value="{{ $projectId }}">
                <input type="hidden" name="quick_action" value="minggu_ini">
                @if($karyawanSearch)<input type="hidden" name="karyawan_search" value="{{ $karyawanSearch }}">@endif
                @if($shiftFilter)<input type="hidden" name="shift_id" value="{{ $shiftFilter }}">@endif
                <button type="submit" class="px-4 py-2 bg-white border-2 border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 transition text-sm font-medium">
                    <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
                </button>
            </form>
            <form method="GET" class="inline">
                <input type="hidden" name="project_id" value="{{ $projectId }}">
                <input type="hidden" name="quick_action" value="minggu_depan">
                @if($karyawanSearch)<input type="hidden" name="karyawan_search" value="{{ $karyawanSearch }}">@endif
                @if($shiftFilter)<input type="hidden" name="shift_id" value="{{ $shiftFilter }}">@endif
                <button type="submit" class="px-4 py-2 bg-white border-2 border-green-500 text-green-600 rounded-lg hover:bg-green-50 transition text-sm font-medium">
                    <i class="fas fa-forward mr-1"></i>Minggu Depan
                </button>
            </form>
            <form method="GET" class="inline">
                <input type="hidden" name="project_id" value="{{ $projectId }}">
                <input type="hidden" name="quick_action" value="bulan_ini">
                @if($karyawanSearch)<input type="hidden" name="karyawan_search" value="{{ $karyawanSearch }}">@endif
                @if($shiftFilter)<input type="hidden" name="shift_id" value="{{ $shiftFilter }}">@endif
                <button type="submit" class="px-4 py-2 bg-white border-2 border-purple-500 text-purple-600 rounded-lg hover:bg-purple-50 transition text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-1"></i>Bulan Ini
                </button>
            </form>
        </div>
        @endif
    </div>
    <div class="flex gap-2">
        <a href="{{ route('perusahaan.kehadiran.rekap') }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-chart-bar mr-2"></i>Rekap
        </a>
        <button onclick="openImportExcelModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <i class="fas fa-file-excel mr-2"></i>Import Excel
        </button>
        <button onclick="openGenerateByJabatanModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-users-cog mr-2"></i>Generate per Jabatan
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="space-y-4">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-filter" style="color: #3B82C8;"></i>
            <span class="font-semibold text-gray-700">Filter</span>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3">
            <!-- Project -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    <i class="fas fa-project-diagram mr-1"></i>Project <span class="text-red-500">*</span>
                </label>
                <select name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
                    <option value="">Pilih Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    <i class="fas fa-calendar mr-1"></i>Tanggal Mulai
                </label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            </div>
            
            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    <i class="fas fa-calendar mr-1"></i>Tanggal Akhir
                </label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            </div>
            
            <!-- Karyawan Search -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    <i class="fas fa-search mr-1"></i>Cari Karyawan
                </label>
                <input type="text" name="karyawan_search" value="{{ $karyawanSearch }}" placeholder="Nama atau NIK..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            </div>
            
            <!-- Shift -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    <i class="fas fa-clock mr-1"></i>Shift
                </label>
                <select name="shift_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
                    <option value="">Semua Shift</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ $shiftFilter == $shift->id ? 'selected' : '' }}>{{ $shift->kode_shift }} - {{ $shift->nama_shift }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl font-medium transition text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-search mr-2"></i>Tampilkan
            </button>
            @if(request()->hasAny(['project_id', 'tanggal_mulai', 'tanggal_akhir', 'karyawan_search', 'shift_id']))
            <a href="{{ route('perusahaan.kehadiran.schedule') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
            @endif
        </div>
    </form>
</div>

@if($projectId && $karyawans->count() > 0)
<!-- Navigation Buttons -->
<div class="mb-4 flex justify-between items-center">
    <form method="GET" class="inline">
        <input type="hidden" name="project_id" value="{{ $projectId }}">
        <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai->copy()->subWeek()->format('Y-m-d') }}">
        <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir->copy()->subWeek()->format('Y-m-d') }}">
        @if($karyawanSearch)<input type="hidden" name="karyawan_search" value="{{ $karyawanSearch }}">@endif
        @if($shiftFilter)<input type="hidden" name="shift_id" value="{{ $shiftFilter }}">@endif
        <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
            <i class="fas fa-chevron-left mr-2"></i>Minggu Sebelumnya
        </button>
    </form>
    
    <div class="text-center">
        <p class="text-sm text-gray-500">Menampilkan</p>
        <p class="text-lg font-bold" style="color: #3B82C8;">{{ $tanggalMulai->format('d M Y') }} - {{ $tanggalAkhir->format('d M Y') }}</p>
    </div>
    
    <form method="GET" class="inline">
        <input type="hidden" name="project_id" value="{{ $projectId }}">
        <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai->copy()->addWeek()->format('Y-m-d') }}">
        <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir->copy()->addWeek()->format('Y-m-d') }}">
        @if($karyawanSearch)<input type="hidden" name="karyawan_search" value="{{ $karyawanSearch }}">@endif
        @if($shiftFilter)<input type="hidden" name="shift_id" value="{{ $shiftFilter }}">@endif
        <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
            Minggu Berikutnya<i class="fas fa-chevron-right ml-2"></i>
        </button>
    </form>
</div>
<!-- Schedule Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky left-0 bg-gradient-to-r from-gray-50 to-gray-100 z-10" style="min-width: 200px;">
                        <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Karyawan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider" style="min-width: 150px;">
                        <i class="fas fa-user-tie mr-2" style="color: #3B82C8;"></i>Jabatan
                    </th>
                    @foreach($dates as $date)
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider" style="min-width: 100px;">
                        <div>{{ $date->format('D') }}</div>
                        <div class="text-lg font-bold" style="color: #3B82C8;">{{ $date->format('d') }}</div>
                        <div>{{ $date->format('M') }}</div>
                    </th>
                    @endforeach
                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider" style="min-width: 120px;">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($karyawans as $karyawan)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-4 py-3 sticky left-0 bg-white z-10">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-2" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <span class="text-white font-bold text-xs">{{ substr($karyawan->nama_lengkap, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $karyawan->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $karyawan->nik_karyawan }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-700">{{ $karyawan->jabatan ? $karyawan->jabatan->nama : '-' }}</p>
                    </td>
                    @foreach($dates as $date)
                    @php
                        $key = $karyawan->id . '_' . $date->format('Y-m-d');
                        $jadwal = $jadwalShifts->get($key);
                        $jadwalFirst = $jadwal ? $jadwal->first() : null;
                        $shiftKode = $jadwalFirst && $jadwalFirst->shift ? $jadwalFirst->shift->kode_shift : '';
                    @endphp
                    <td class="px-4 py-3 text-center">
                        <select 
                            class="shift-select w-full px-2 py-1 border border-gray-300 rounded-lg text-xs font-bold text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            data-karyawan-id="{{ $karyawan->id }}"
                            data-tanggal="{{ $date->format('Y-m-d') }}"
                            style="background: {{ $jadwalFirst && $jadwalFirst->shift ? $jadwalFirst->shift->warna : '#FFFFFF' }}; color: {{ $jadwalFirst && $jadwalFirst->shift ? '#FFFFFF' : '#000000' }};"
                            onchange="updateShift(this)">
                            <option value="" style="background: #FFFFFF; color: #000000;">-</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" 
                                    style="background: {{ $shift->warna }}; color: #FFFFFF;"
                                    {{ $jadwalFirst && $jadwalFirst->shift_id == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->kode_shift }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    @endforeach
                    <td class="px-4 py-3 text-center">
                        <div class="flex gap-1 justify-center">
                            <button onclick="copyLastWeek({{ $karyawan->id }})" class="px-2 py-1 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-xs font-medium" title="Copy Jadwal Periode Sebelumnya">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button onclick="openSetMonthModal({{ $karyawan->id }}, '{{ $karyawan->nama_lengkap }}')" class="px-2 py-1 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition text-xs font-medium" title="Atur Jadwal Bulan">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $karyawans->links() }}
</div>

@elseif($projectId && $karyawans->count() == 0)
<!-- Empty State -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
    <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4 mx-auto" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
        <i class="fas fa-users text-5xl" style="color: #3B82C8;"></i>
    </div>
    <p class="text-gray-900 text-lg font-semibold mb-2">Tidak ada karyawan</p>
    <p class="text-gray-500 text-sm">Tidak ada karyawan aktif di project ini</p>
</div>

@else
<!-- No Project Selected -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
    <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4 mx-auto" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
        <i class="fas fa-calendar-alt text-5xl" style="color: #3B82C8;"></i>
    </div>
    <p class="text-gray-900 text-lg font-semibold mb-2">Pilih Project</p>
    <p class="text-gray-500 text-sm">Silakan pilih project untuk melihat jadwal shift karyawan</p>
</div>
@endif

<!-- Modal: Atur Jadwal Bulan -->
<div id="modalSetMonth" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Atur Jadwal Bulan</h3>
                        <p class="text-purple-100 text-sm" id="modalKaryawanName"></p>
                    </div>
                </div>
                <button onclick="closeSetMonthModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formSetMonth" class="p-6 space-y-4">
            <input type="hidden" id="setMonthKaryawanId" name="karyawan_id">
            
            <!-- Bulan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2" style="color: #8B5CF6;"></i>Pilih Bulan
                </label>
                <input type="month" name="bulan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <!-- Shift -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clock mr-2" style="color: #8B5CF6;"></i>Pilih Shift
                </label>
                <select name="shift_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih Shift</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->kode_shift }} - {{ $shift->nama_shift }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Hari Kerja -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check mr-2" style="color: #8B5CF6;"></i>Hari Kerja
                </label>
                <div class="grid grid-cols-4 gap-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="1" checked class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Senin</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="2" checked class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Selasa</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="3" checked class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Rabu</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="4" checked class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Kamis</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="5" checked class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Jumat</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="6" class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Sabtu</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hari_kerja[]" value="0" class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Minggu</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">Default: Senin - Jumat</p>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                    <i class="fas fa-save mr-2"></i>Simpan Jadwal
                </button>
                <button type="button" onclick="closeSetMonthModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Generate per Jabatan -->
<div id="modalGenerateByJabatan" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users-cog text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Generate Jadwal per Jabatan</h3>
                        <p class="text-blue-100 text-sm">Set jadwal untuk semua karyawan</p>
                    </div>
                </div>
                <button onclick="closeGenerateByJabatanModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formGenerateByJabatan" class="p-6 space-y-4">
            <!-- Project -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                </label>
                <select id="generateProjectId" name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadJabatansAndShifts()">
                    <option value="">Pilih Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih project terlebih dahulu</p>
            </div>
            
            <!-- Jabatan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user-tie mr-2" style="color: #3B82C8;"></i>Jabatan <span class="text-red-500">*</span>
                </label>
                <select id="generateJabatanId" name="jabatan_id" required disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                    <option value="">Pilih Jabatan</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih project terlebih dahulu</p>
            </div>
            
            <!-- Shift -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clock mr-2" style="color: #3B82C8;"></i>Shift <span class="text-red-500">*</span>
                </label>
                <select id="generateShiftId" name="shift_id" required disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                    <option value="">Pilih Shift</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih project terlebih dahulu</p>
            </div>
            
            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2" style="color: #3B82C8;"></i>Tanggal Mulai <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_mulai" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2" style="color: #3B82C8;"></i>Tanggal Akhir <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_akhir" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-magic mr-2"></i>Generate
                </button>
                <button type="button" onclick="closeGenerateByJabatanModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Import Excel -->
<div id="modalImportExcel" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-100" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Import Jadwal dari Excel</h3>
                        <p class="text-green-100 text-sm">Upload file Excel (.xlsx)</p>
                    </div>
                </div>
                <button onclick="closeImportExcelModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formImportExcel" action="{{ route('perusahaan.schedule.import-excel') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <!-- Project -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-project-diagram mr-2" style="color: #10B981;"></i>Project <span class="text-red-500">*</span>
                </label>
                <select id="importProjectId" name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Pilih Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih project yang sesuai dengan template Excel</p>
            </div>
            
            <!-- Info: Tanggal di Excel -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-yellow-600 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-900 mb-1">Tanggal Otomatis dari Excel</p>
                        <p class="text-xs text-yellow-700">Tanggal jadwal akan dibaca otomatis dari header Excel. Pastikan format template tidak diubah.</p>
                    </div>
                </div>
            </div>
            
            <!-- Download Template -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Belum punya template?</p>
                        <p class="text-xs text-blue-700 mb-3">Download template Excel dengan periode tanggal yang diinginkan, isi jadwal shift, lalu upload kembali.</p>
                        
                        <!-- Tanggal Range untuk Download Template -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-blue-800 mb-1">Tanggal Mulai</label>
                                <input type="date" id="templateTanggalMulai" value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-1.5 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-blue-800 mb-1">Tanggal Akhir</label>
                                <input type="date" id="templateTanggalAkhir" value="{{ now()->addDays(6)->format('Y-m-d') }}" class="w-full px-3 py-1.5 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <button type="button" onclick="downloadTemplate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </button>
                        <p id="templateInfo" class="text-xs text-blue-600 mt-2 hidden"></p>
                    </div>
                </div>
            </div>
            
            <!-- File Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-upload mr-2" style="color: #10B981;"></i>Upload File Excel <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-500 transition">
                    <input type="file" name="file" id="excelFile" accept=".xlsx,.xls" required class="hidden" onchange="updateFileName(this)">
                    <label for="excelFile" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Klik untuk pilih file</p>
                        <p class="text-xs text-gray-500 mt-1">atau drag & drop file di sini</p>
                        <p class="text-xs text-gray-400 mt-2">Format: .xlsx atau .xls (Max: 2MB)</p>
                    </label>
                </div>
                <p id="fileName" class="text-sm text-green-600 font-medium mt-2 hidden"></p>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="fas fa-upload mr-2"></i>Import
                </button>
                <button type="button" onclick="closeImportExcelModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateShift(selectElement) {
    const karyawanId = selectElement.dataset.karyawanId;
    const tanggal = selectElement.dataset.tanggal;
    const shiftId = selectElement.value;
    
    // Update background color based on selected shift
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    if (shiftId) {
        selectElement.style.background = selectedOption.style.background;
        selectElement.style.color = '#FFFFFF';
    } else {
        selectElement.style.background = '#FFFFFF';
        selectElement.style.color = '#000000';
    }
    
    // Show loading state
    selectElement.disabled = true;
    selectElement.style.opacity = '0.7';
    
    fetch('{{ route("perusahaan.schedule.update-shift") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            karyawan_id: karyawanId,
            tanggal: tanggal,
            shift_id: shiftId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success feedback
            selectElement.style.borderColor = '#10B981';
            setTimeout(() => {
                selectElement.style.borderColor = '';
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mengupdate shift',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .finally(() => {
        selectElement.disabled = false;
        selectElement.style.opacity = '1';
    });
}

function copyLastWeek(karyawanId) {
    Swal.fire({
        title: 'Copy Jadwal Periode Sebelumnya?',
        text: "Jadwal periode sebelumnya akan disalin ke periode ini",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Copy!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("perusahaan.schedule.copy-last-week") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    karyawan_id: karyawanId,
                    tanggal_mulai: '{{ $tanggalMulai->format("Y-m-d") }}',
                    tanggal_akhir: '{{ $tanggalAkhir->format("Y-m-d") }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat copy jadwal',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    });
}

function openSetMonthModal(karyawanId, karyawanName) {
    document.getElementById('setMonthKaryawanId').value = karyawanId;
    document.getElementById('modalKaryawanName').textContent = karyawanName;
    document.getElementById('modalSetMonth').classList.remove('hidden');
}

function closeSetMonthModal() {
    document.getElementById('modalSetMonth').classList.add('hidden');
    document.getElementById('formSetMonth').reset();
}

document.getElementById('formSetMonth').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        karyawan_id: formData.get('karyawan_id'),
        shift_id: formData.get('shift_id'),
        bulan: formData.get('bulan'),
        hari_kerja: formData.getAll('hari_kerja[]')
    };
    
    fetch('{{ route("perusahaan.schedule.set-month") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeSetMonthModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mengatur jadwal',
            timer: 2000,
            showConfirmButton: false
        });
    });
});

// Close modal when clicking outside
document.getElementById('modalSetMonth').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSetMonthModal();
    }
});

// Generate by Jabatan functions
function openGenerateByJabatanModal() {
    document.getElementById('modalGenerateByJabatan').classList.remove('hidden');
}

function closeGenerateByJabatanModal() {
    document.getElementById('modalGenerateByJabatan').classList.add('hidden');
    document.getElementById('formGenerateByJabatan').reset();
    document.getElementById('generateJabatanId').disabled = true;
    document.getElementById('generateShiftId').disabled = true;
}

function loadJabatansAndShifts() {
    const projectId = document.getElementById('generateProjectId').value;
    const jabatanSelect = document.getElementById('generateJabatanId');
    const shiftSelect = document.getElementById('generateShiftId');
    
    if (!projectId) {
        jabatanSelect.disabled = true;
        shiftSelect.disabled = true;
        jabatanSelect.innerHTML = '<option value="">Pilih Jabatan</option>';
        shiftSelect.innerHTML = '<option value="">Pilih Shift</option>';
        return;
    }
    
    // Load all jabatans
    jabatanSelect.innerHTML = '<option value="">Pilih Jabatan</option>';
    const jabatans = @json($jabatans);
    jabatans.forEach(jabatan => {
        jabatanSelect.innerHTML += `<option value="${jabatan.id}">${jabatan.nama}</option>`;
    });
    jabatanSelect.disabled = false;
    
    // Load shifts for selected project
    shiftSelect.innerHTML = '<option value="">Pilih Shift</option>';
    const allShifts = @json($allShifts ?? []);
    allShifts.forEach(shift => {
        if (shift.project_id == projectId) {
            shiftSelect.innerHTML += `<option value="${shift.id}">${shift.kode_shift} - ${shift.nama_shift}</option>`;
        }
    });
    shiftSelect.disabled = false;
}

document.getElementById('formGenerateByJabatan').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        project_id: formData.get('project_id'),
        jabatan_id: formData.get('jabatan_id'),
        shift_id: formData.get('shift_id'),
        tanggal_mulai: formData.get('tanggal_mulai'),
        tanggal_akhir: formData.get('tanggal_akhir')
    };
    
    Swal.fire({
        title: 'Generate Jadwal?',
        text: "Jadwal akan dibuat untuk semua karyawan dengan jabatan ini",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82C8',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Generate!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("perusahaan.schedule.generate-by-jabatan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeGenerateByJabatanModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat generate jadwal',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    });
});

// Close modal when clicking outside
document.getElementById('modalGenerateByJabatan').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateByJabatanModal();
    }
});

// Import Excel functions
function openImportExcelModal() {
    document.getElementById('modalImportExcel').classList.remove('hidden');
}

function closeImportExcelModal() {
    document.getElementById('modalImportExcel').classList.add('hidden');
    document.getElementById('formImportExcel').reset();
    document.getElementById('fileName').classList.add('hidden');
}

function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = '✓ File dipilih: ' + input.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
}

function downloadTemplate() {
    const projectId = document.getElementById('importProjectId').value;
    const tanggalMulai = document.getElementById('templateTanggalMulai').value;
    const tanggalAkhir = document.getElementById('templateTanggalAkhir').value;
    
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih project terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (!tanggalMulai || !tanggalAkhir) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Isi tanggal mulai dan tanggal akhir terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show info
    const templateInfo = document.getElementById('templateInfo');
    templateInfo.textContent = '✓ Template akan didownload untuk periode ' + tanggalMulai + ' s/d ' + tanggalAkhir;
    templateInfo.classList.remove('hidden');
    
    window.location.href = '{{ route("perusahaan.schedule.download-template") }}?project_id=' + projectId + '&tanggal_mulai=' + tanggalMulai + '&tanggal_akhir=' + tanggalAkhir;
}

// Close modal when clicking outside
document.getElementById('modalImportExcel').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportExcelModal();
    }
});
</script>
@endpush
@endsection
