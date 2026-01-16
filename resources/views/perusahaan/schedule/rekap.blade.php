@extends('perusahaan.layouts.app')

@section('content')
<div class="min-h-screen" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('perusahaan.kehadiran.index') }}" class="inline-flex items-center text-white hover:text-blue-100 transition mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Rekap Jadwal Shift</h1>
                    <p class="text-blue-100">Laporan jadwal shift karyawan per periode</p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-filter text-blue-600"></i>
                <h3 class="text-lg font-semibold text-gray-800">Filter Rekap</h3>
            </div>
            
            <form method="GET" action="{{ route('perusahaan.kehadiran.rekap') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Project -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Jabatan Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jabatan (Opsional)
                        </label>
                        <select name="jabatan_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Jabatan</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" {{ $jabatanId == $jabatan->id ? 'selected' : '' }}>
                                    {{ $jabatan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Search Karyawan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Cari Karyawan (Opsional)
                        </label>
                        <input type="text" name="karyawan_search" value="{{ $karyawanSearch }}" placeholder="Nama atau NIK..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Tanggal Akhir -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Akhir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-search mr-2"></i>Tampilkan Rekap
                    </button>
                    <a href="{{ route('perusahaan.kehadiran.rekap') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 flex items-start gap-2">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                    <p class="text-sm text-yellow-800">Maksimal periode 31 hari. Contoh: 20 Jan - 19 Feb atau 1 Jan - 31 Jan</p>
                </div>
            </form>
        </div>

        @if($projectId && $karyawans->isNotEmpty())
        <!-- Rekap Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header Info -->
            <div class="p-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-2">
                            {{ $tanggalMulai->format('d M Y') }} - {{ $tanggalAkhir->format('d M Y') }}
                        </h2>
                        <div class="flex items-center gap-6 text-white">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users"></i>
                                <span>Total Karyawan: <strong>{{ $karyawans->total() }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-alt"></i>
                                <span>Halaman: <strong>{{ $karyawans->currentPage() }} dari {{ $karyawans->lastPage() }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-day"></i>
                                <span>Total Hari: <strong>{{ count($dates) }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('perusahaan.kehadiran.rekap.export-pdf', [
                        'project_id' => $projectId,
                        'jabatan_id' => $jabatanId,
                        'karyawan_search' => $karyawanSearch,
                        'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                        'tanggal_akhir' => $tanggalAkhir->format('Y-m-d')
                    ]) }}" class="px-6 py-3 bg-white text-purple-600 rounded-xl font-semibold hover:shadow-lg transition">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                </div>
            </div>

            <!-- Legend -->
            @if($shifts->isNotEmpty())
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-info-circle text-gray-600"></i>
                    <span class="text-sm font-semibold text-gray-700">Keterangan Shift:</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($shifts as $shift)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-white" style="background-color: {{ $shift->warna }};">
                        <span class="font-bold">{{ $shift->kode_shift }}</span>
                        <span>-</span>
                        <span>{{ $shift->nama_shift }} ({{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }})</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200">
                            <th class="sticky-col-1 px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200" style="min-width: 50px;">No</th>
                            <th class="sticky-col-2 px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200" style="min-width: 120px;">NIK</th>
                            <th class="sticky-col-3 px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200" style="min-width: 200px;">Nama Karyawan</th>
                            
                            @foreach($dates as $date)
                            <th class="px-3 py-3 text-center font-semibold text-gray-700 border-r border-gray-200" style="min-width: 60px;">
                                <div class="text-xs">{{ $date->format('d') }}</div>
                                <div class="text-xs text-gray-500">{{ $date->format('D') }}</div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karyawans as $index => $karyawan)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="sticky-col-1 px-4 py-3 border-r border-gray-200">{{ $index + 1 }}</td>
                            <td class="sticky-col-2 px-4 py-3 border-r border-gray-200 font-medium text-gray-700">{{ $karyawan->nik_karyawan }}</td>
                            <td class="sticky-col-3 px-4 py-3 border-r border-gray-200">
                                <div class="font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</div>
                                <div class="text-xs text-gray-500">{{ $karyawan->jabatan->nama ?? '-' }}</div>
                            </td>
                            
                            @foreach($dates as $date)
                            @php
                                $key = $karyawan->id . '_' . $date->format('Y-m-d');
                                $jadwal = $jadwalShifts->get($key);
                            @endphp
                            <td class="px-3 py-3 text-center border-r border-gray-200">
                                @if($jadwal && $jadwal->first())
                                    <span class="inline-block px-2 py-1 rounded text-xs font-bold text-white" style="background-color: {{ $jadwal->first()->shift->warna }};">
                                        {{ $jadwal->first()->shift->kode_shift }}
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        
                        <!-- Total Row -->
                        <tr class="bg-blue-50 font-semibold">
                            <td colspan="3" class="sticky-col-total px-4 py-3 border-r border-gray-200 text-gray-700">
                                <i class="fas fa-calculator mr-2"></i>TOTAL
                            </td>
                            @foreach($dates as $date)
                            @php
                                $totalPerDate = 0;
                                foreach($karyawans as $karyawan) {
                                    $key = $karyawan->id . '_' . $date->format('Y-m-d');
                                    $jadwal = $jadwalShifts->get($key);
                                    if($jadwal && $jadwal->first()) {
                                        $totalPerDate++;
                                    }
                                }
                            @endphp
                            <td class="px-3 py-3 text-center border-r border-gray-200 text-blue-700">
                                {{ $totalPerDate }}
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($karyawans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $karyawans->links() }}
            </div>
            @endif
        </div>
        @elseif($projectId)
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <i class="fas fa-users-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data Karyawan</h3>
            <p class="text-gray-500">Project ini belum memiliki karyawan aktif</p>
        </div>
        @else
        <!-- Initial State -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <i class="fas fa-filter text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Pilih Filter untuk Melihat Rekap</h3>
            <p class="text-gray-500">Pilih project dan periode tanggal untuk menampilkan rekap jadwal shift</p>
        </div>
        @endif

    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .bg-white.rounded-2xl.shadow-xl, .bg-white.rounded-2xl.shadow-xl * {
        visibility: visible;
    }
    .bg-white.rounded-2xl.shadow-xl {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    button {
        display: none !important;
    }
}

/* Sticky columns with proper z-index and shadow */
.sticky-col-1 {
    position: sticky !important;
    left: 0;
    z-index: 20;
    background-color: inherit;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sticky-col-2 {
    position: sticky !important;
    left: 50px;
    z-index: 20;
    background-color: inherit;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sticky-col-3 {
    position: sticky !important;
    left: 170px;
    z-index: 20;
    background-color: inherit;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sticky-col-total {
    position: sticky !important;
    left: 0;
    z-index: 20;
    background-color: #EFF6FF !important;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

/* Ensure thead sticky columns have correct background */
thead .sticky-col-1,
thead .sticky-col-2,
thead .sticky-col-3 {
    background-color: #F3F4F6 !important;
}

/* Ensure tbody sticky columns have correct background */
tbody tr .sticky-col-1,
tbody tr .sticky-col-2,
tbody tr .sticky-col-3 {
    background-color: white !important;
}

tbody tr:hover .sticky-col-1,
tbody tr:hover .sticky-col-2,
tbody tr:hover .sticky-col-3 {
    background-color: #F9FAFB !important;
}
</style>
@endsection
