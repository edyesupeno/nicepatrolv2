@extends('perusahaan.layouts.app')

@section('title', 'Patroli Kawasan')
@section('page-title', 'Patroli Kawasan')
@section('page-subtitle', 'Laporan kegiatan patroli berdasarkan kawasan/area')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Area</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('perusahaan.laporan-patroli.kawasan') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Patroli</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalStats['total_patroli']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-route text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ $totalStats['completion_rate'] }}%</span>
                    <span class="text-gray-500 ml-1">tingkat penyelesaian</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Patroli Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($totalStats['patroli_selesai']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500">dari {{ number_format($totalStats['total_patroli']) }} total patroli</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Aset Bermasalah</p>
                    <p class="text-2xl font-bold text-red-600">{{ $totalStats['total_aset_bermasalah'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500">dari {{ $totalStats['total_aset_checks'] ?? 0 }} total pengecekan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Areas List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Area Patroli</h3>
            <p class="text-sm text-gray-600 mt-1">Statistik patroli per kawasan dalam periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Patroli</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aset Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Durasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($areas as $area)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $area->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($area->deskripsi, 50) }}</div>
                                    @if($area->alamat)
                                        <div class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($area->alamat, 40) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $area->project->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-medium text-gray-900">{{ $area->patroli_stats['total_patroli'] }}</div>
                                <div class="text-xs text-gray-500">
                                    <span class="text-green-600">{{ $area->patroli_stats['patroli_selesai'] }}</span> /
                                    <span class="text-yellow-600">{{ $area->patroli_stats['patroli_berjalan'] }}</span> /
                                    <span class="text-red-600">{{ $area->patroli_stats['patroli_tertunda'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-medium text-green-600">{{ $area->patroli_stats['completion_rate'] }}%</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-medium text-purple-600">{{ $area->patroli_stats['coverage_percentage'] }}%</div>
                                <div class="text-xs text-gray-500">{{ $area->patroli_stats['checkpoints_visited'] }}/{{ $area->patroli_stats['total_checkpoints'] }} checkpoint</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $asetAman = $area->patroli_stats['aset_aman'] ?? 0;
                                    $asetBermasalah = $area->patroli_stats['aset_bermasalah'] ?? 0;
                                    $asetHilang = $area->patroli_stats['aset_hilang'] ?? 0;
                                    $totalAset = $asetAman + $asetBermasalah + $asetHilang;
                                @endphp
                                @if($totalAset > 0)
                                    <div class="text-xs">
                                        <span class="text-green-600 font-medium">{{ $asetAman }} aman</span>
                                        @if($asetBermasalah > 0)
                                            <br><span class="text-red-600 font-medium">{{ $asetBermasalah }} rusak</span>
                                        @endif
                                        @if($asetHilang > 0)
                                            <br><span class="text-orange-600 font-medium">{{ $asetHilang }} hilang</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-gray-900">
                                    @if($area->patroli_stats['avg_duration'] > 0)
                                        {{ $area->patroli_stats['avg_duration'] }} menit
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($area->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('perusahaan.laporan-patroli.kawasan.detail', ['area' => $area->hash_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-map-marked-alt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Tidak ada data area patroli</p>
                                    <p class="text-sm">Silakan tambahkan area patroli terlebih dahulu</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($areas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $areas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
