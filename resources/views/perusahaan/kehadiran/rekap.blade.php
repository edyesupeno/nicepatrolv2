@extends('perusahaan.layouts.app')

@section('content')
<div class="p-6">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Rekap Kehadiran</h1>
                    <p class="text-sm text-gray-600">Rekap kehadiran karyawan per periode</p>
                </div>
            </div>
            <a href="{{ route('perusahaan.kehadiran.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('perusahaan.kehadiran.rekap-kehadiran') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Project -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                <select name="project_id" id="project_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jabatan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan</label>
                <select name="jabatan_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ $jabatanId == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Karyawan</label>
                <input type="text" name="karyawan_search" value="{{ $karyawanSearch }}" placeholder="Nama atau NIK..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-white rounded-lg font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-search"></i>
                Tampilkan
            </button>
            
            @if($projectId)
            <button type="button" onclick="exportPdf()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                <i class="fas fa-file-pdf"></i>
                Export PDF
            </button>
            @endif
        </div>
    </form>

    @if($projectId && $karyawans->count() > 0)
    <!-- Rekap Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase sticky left-0 bg-gray-50 z-10" style="min-width: 200px;">Karyawan</th>
                        @foreach($dates as $date)
                        <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 border-l border-gray-200" style="min-width: 60px;">
                            <div>{{ $date->format('d') }}</div>
                            <div class="text-xs font-normal text-gray-500">{{ $date->format('D') }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($karyawans as $karyawan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 sticky left-0 bg-white z-10 border-r border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    @if($karyawan->foto)
                                    <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_lengkap }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                    <span class="text-blue-600 font-bold text-sm">{{ substr($karyawan->nama_lengkap, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 truncate">{{ $karyawan->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500">{{ $karyawan->nik_karyawan }}</div>
                                    @if($karyawan->jabatan)
                                    <div class="text-xs text-gray-500">{{ $karyawan->jabatan->nama }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @foreach($dates as $date)
                        @php
                            $key = $karyawan->id . '_' . $date->format('Y-m-d');
                            $kehadiranCollection = $kehadirans->get($key);
                            $kehadiran = $kehadiranCollection ? $kehadiranCollection->first() : null;
                        @endphp
                        <td class="px-2 py-3 text-center border-l border-gray-200">
                            @if($kehadiran)
                                @if($kehadiran->status == 'hadir')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-lg font-bold text-xs" title="Hadir">
                                    H
                                </div>
                                @elseif($kehadiran->status == 'terlambat')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-700 rounded-lg font-bold text-xs" title="Terlambat">
                                    T
                                </div>
                                @elseif($kehadiran->status == 'pulang_cepat')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-700 rounded-lg font-bold text-xs" title="Pulang Cepat">
                                    PC
                                </div>
                                @elseif($kehadiran->status == 'terlambat_pulang_cepat')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-700 rounded-lg font-bold text-xs" title="Terlambat & Pulang Cepat">
                                    TPC
                                </div>
                                @elseif($kehadiran->status == 'alpa')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-700 rounded-lg font-bold text-xs" title="Alpa">
                                    A
                                </div>
                                @elseif($kehadiran->status == 'izin')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-lg font-bold text-xs" title="Izin">
                                    I
                                </div>
                                @elseif($kehadiran->status == 'sakit')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-700 rounded-lg font-bold text-xs" title="Sakit">
                                    S
                                </div>
                                @elseif($kehadiran->status == 'cuti')
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-700 rounded-lg font-bold text-xs" title="Cuti">
                                    C
                                </div>
                                @endif
                            @else
                            <div class="text-gray-300">-</div>
                            @endif
                        </td>
                        @endforeach
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

    <!-- Legend -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Keterangan Status:</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-100 text-green-700 rounded-lg flex items-center justify-center font-bold text-xs">H</div>
                <span class="text-sm text-gray-700">Hadir</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-yellow-100 text-yellow-700 rounded-lg flex items-center justify-center font-bold text-xs">T</div>
                <span class="text-sm text-gray-700">Terlambat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-100 text-orange-700 rounded-lg flex items-center justify-center font-bold text-xs">PC</div>
                <span class="text-sm text-gray-700">Pulang Cepat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-100 text-red-700 rounded-lg flex items-center justify-center font-bold text-xs">TPC</div>
                <span class="text-sm text-gray-700">Terlambat & Pulang Cepat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-100 text-red-700 rounded-lg flex items-center justify-center font-bold text-xs">A</div>
                <span class="text-sm text-gray-700">Alpa</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center font-bold text-xs">I</div>
                <span class="text-sm text-gray-700">Izin</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-purple-100 text-purple-700 rounded-lg flex items-center justify-center font-bold text-xs">S</div>
                <span class="text-sm text-gray-700">Sakit</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center font-bold text-xs">C</div>
                <span class="text-sm text-gray-700">Cuti</span>
            </div>
        </div>
    </div>

    @elseif($projectId)
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500 font-semibold">Tidak ada data karyawan</p>
        <p class="text-gray-400 text-sm mt-1">Silakan pilih project dan periode lain</p>
    </div>
    @else
    <!-- Initial State -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-chart-bar text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500 font-semibold">Pilih project untuk melihat rekap kehadiran</p>
        <p class="text-gray-400 text-sm mt-1">Gunakan filter di atas untuk menampilkan data</p>
    </div>
    @endif

</div>

@push('scripts')
<script>
function exportPdf() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("perusahaan.kehadiran.rekap-kehadiran.export-pdf") }}';
    
    const projectId = document.getElementById('project_id').value;
    const jabatanId = document.querySelector('select[name="jabatan_id"]').value;
    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]').value;
    const tanggalAkhir = document.querySelector('input[name="tanggal_akhir"]').value;
    
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih project terlebih dahulu',
        });
        return;
    }
    
    const fields = {
        project_id: projectId,
        jabatan_id: jabatanId,
        tanggal_mulai: tanggalMulai,
        tanggal_akhir: tanggalAkhir
    };
    
    for (const [key, value] of Object.entries(fields)) {
        if (value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush

@endsection
