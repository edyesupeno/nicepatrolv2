@extends('perusahaan.layouts.app')

@section('title', 'Laporan Mutasi Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Mutasi Aset</h1>
            <nav class="text-sm text-gray-600 mt-1">
                <a href="{{ route('perusahaan.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">›</span>
                <a href="{{ route('perusahaan.mutasi-aset.index') }}" class="hover:text-blue-600">Mutasi Aset</a>
                <span class="mx-2">›</span>
                <span>Laporan</span>
            </nav>
        </div>
        <div>
            <a href="{{ route('perusahaan.mutasi-aset.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <!-- Filter Form -->
            <form method="GET" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                        <input type="date" name="tanggal_dari" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ request('tanggal_dari') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                        <input type="date" name="tanggal_sampai" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="submit" name="export" value="pdf" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-600 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $mutasiAsets->count() }}</h3>
                            <p class="text-blue-100">Total Mutasi</p>
                        </div>
                        <div class="text-blue-200">
                            <i class="fas fa-exchange-alt text-3xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-500 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $mutasiAsets->where('status', 'pending')->count() }}</h3>
                            <p class="text-yellow-100">Pending</p>
                        </div>
                        <div class="text-yellow-200">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-green-600 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $mutasiAsets->where('status', 'disetujui')->count() }}</h3>
                            <p class="text-green-100">Disetujui</p>
                        </div>
                        <div class="text-green-200">
                            <i class="fas fa-check text-3xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-indigo-600 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $mutasiAsets->where('status', 'selesai')->count() }}</h3>
                            <p class="text-indigo-100">Selesai</p>
                        </div>
                        <div class="text-indigo-200">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Mutasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Asal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mutasiAsets as $mutasi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $mutasi->nomor_mutasi }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $mutasi->tanggal_mutasi->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($mutasi->asset_type == 'data_aset')
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-box text-blue-600"></i>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-car text-green-600"></i>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($mutasi->asset_name, 25) }}</div>
                                        <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $mutasi->asset_type)) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $mutasi->karyawan->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500">{{ $mutasi->karyawan->nik_karyawan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $mutasi->projectAsal->nama ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $mutasi->projectTujuan->nama ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{!! $mutasi->status_badge !!}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $mutasi->disetujuiOleh->name ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">Tidak ada data mutasi aset</p>
                                    <p class="text-sm">Coba ubah filter atau tambah data mutasi baru</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($mutasiAsets->isNotEmpty())
                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Berdasarkan Tipe Aset</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe Aset</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $dataAsetCount = $mutasiAsets->where('asset_type', 'data_aset')->count();
                                        $kendaraanCount = $mutasiAsets->where('asset_type', 'aset_kendaraan')->count();
                                        $total = $mutasiAsets->count();
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">Data Aset</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $dataAsetCount }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $total > 0 ? round(($dataAsetCount / $total) * 100, 1) : 0 }}%</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">Aset Kendaraan</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $kendaraanCount }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $total > 0 ? round(($kendaraanCount / $total) * 100, 1) : 0 }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Berdasarkan Status</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach(['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                                        @php
                                            $count = $mutasiAsets->where('status', $status)->count();
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ ucfirst($status) }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $count }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection