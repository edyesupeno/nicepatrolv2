@extends('perusahaan.layouts.app')

@section('title', 'Detail Patroli Area - ' . $area->nama)
@section('page-title', 'Detail Patroli Area')
@section('page-subtitle', $area->nama . ' - ' . \Carbon\Carbon::parse($startDate)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($endDate)->format('d M Y'))

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.laporan-patroli.kawasan') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    Patroli Kawasan
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $area->nama }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Area Info & Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Area Information -->
            <div class="lg:col-span-2">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Area</h4>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Area</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $area->nama }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Project</label>
                        <p class="text-sm text-gray-900">{{ $area->project->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Total Rute</label>
                        <p class="text-sm text-gray-900">{{ $area->rutePatrols->count() }} rute</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $area->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $area->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    @if($area->deskripsi)
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Deskripsi</label>
                        <p class="text-sm text-gray-900">{{ $area->deskripsi }}</p>
                    </div>
                    @endif
                    @if($area->alamat)
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Alamat</label>
                        <p class="text-sm text-gray-900">{{ $area->alamat }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['total_patroli'] }}</div>
                    <div class="text-xs text-blue-700">Total Patroli</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 mb-1">{{ $stats['completion_rate'] }}%</div>
                    <div class="text-xs text-green-700">Penyelesaian</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600 mb-1">{{ $stats['coverage_percentage'] }}%</div>
                    <div class="text-xs text-purple-700">Coverage</div>
                </div>
                <div class="text-center p-4 bg-emerald-50 rounded-lg">
                    <div class="text-xl font-bold text-emerald-600 mb-1">{{ $asetStats['total_aman'] }}/{{ $asetStats['total_checks'] }}</div>
                    <div class="text-xs text-emerald-700">Aset Aman</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-7 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-green-600">{{ $stats['patroli_selesai'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Selesai</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-yellow-600">{{ $stats['patroli_berjalan'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Berjalan</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-red-600">{{ $stats['patroli_tertunda'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Tertunda</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-green-600">{{ $asetStats['total_aman'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Aset Aman</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-red-600">{{ $asetStats['total_bermasalah'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Aset Rusak</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-orange-600">{{ $asetStats['total_hilang'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Aset Hilang</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-lg font-bold text-blue-600">
                @if(($stats['avg_duration'] ?? 0) > 0)
                    {{ $stats['avg_duration'] }}m
                @else
                    -
                @endif
            </div>
            <div class="text-xs text-gray-600">Avg Durasi</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button id="tab-patroli" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <i class="fas fa-route mr-2"></i>Riwayat Patroli ({{ $patrolis->count() }})
                </button>
                <button id="tab-checkpoint" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-map-marker-alt mr-2"></i>Coverage Checkpoint ({{ count($checkpointStats) }})
                </button>
                <button id="tab-location" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-map-marked-alt mr-2"></i>Verifikasi Lokasi
                </button>
                <button id="tab-aset" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-shield-alt mr-2"></i>Status Aset ({{ $asetStats['total_checks'] }})
                </button>
            </nav>
        </div>

        <!-- Tab Content: Riwayat Patroli -->
        <div id="content-patroli" class="tab-content p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Checkpoint</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aset Checks</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($patrolis as $patroli)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    @if($patroli->waktu_mulai)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $patroli->waktu_mulai->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $patroli->waktu_mulai->format('H:i') }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $patroli->user->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $patroli->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($patroli->waktu_mulai)
                                        <div class="text-sm text-gray-900">
                                            {{ $patroli->waktu_mulai->format('H:i') }}
                                            @if($patroli->waktu_selesai)
                                                - {{ $patroli->waktu_selesai->format('H:i') }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $patroli->details->count() }}</span>
                                    <div class="text-xs text-gray-500">checkpoint</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $totalAsetChecks = $patroli->details->sum(function($detail) {
                                            return $detail->asetChecks ? $detail->asetChecks->count() : 0;
                                        });
                                        $amanCount = $patroli->details->sum(function($detail) {
                                            return $detail->asetChecks ? $detail->asetChecks->where('status', 'aman')->count() : 0;
                                        });
                                        $bermasalahCount = $patroli->details->sum(function($detail) {
                                            return $detail->asetChecks ? $detail->asetChecks->where('status', 'bermasalah')->count() : 0;
                                        });
                                        $hilangCount = $patroli->details->sum(function($detail) {
                                            return $detail->asetChecks ? $detail->asetChecks->where('status', 'hilang')->count() : 0;
                                        });
                                    @endphp
                                    @if($totalAsetChecks > 0)
                                        <div class="text-sm font-medium text-gray-900">{{ $totalAsetChecks }}</div>
                                        <div class="text-xs">
                                            <span class="text-green-600">{{ $amanCount }} aman</span>
                                            @if($bermasalahCount > 0)
                                                <span class="text-red-600">{{ $bermasalahCount }} rusak</span>
                                            @endif
                                            @if($hilangCount > 0)
                                                <span class="text-orange-600">{{ $hilangCount }} hilang</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($patroli->status === 'selesai')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Selesai
                                        </span>
                                    @elseif($patroli->status === 'berjalan')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Berjalan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Tertunda
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-sm text-gray-900">
                                        @if($patroli->waktu_selesai && $patroli->waktu_mulai)
                                            {{ $patroli->waktu_selesai->diffInMinutes($patroli->waktu_mulai) }} menit
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-route text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Tidak ada riwayat patroli</p>
                                    <p class="text-sm">Belum ada patroli yang dilakukan di area ini dalam periode yang dipilih</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: Coverage Checkpoint -->
        <div id="content-checkpoint" class="tab-content hidden p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checkpoint</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rute</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kunjungan</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aset Checks</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terakhir Dikunjungi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas Terakhir</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Akurasi Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($checkpointStats as $stat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $stat['checkpoint']->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $stat['checkpoint']->qr_code }}</div>
                                    @if($stat['checkpoint']->deskripsi)
                                        <div class="text-xs text-gray-400 mt-1">{{ Str::limit($stat['checkpoint']->deskripsi, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $stat['rute']->nama }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $stat['visit_count'] }}</div>
                                    <div class="text-xs text-gray-500">kali</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($stat['aset_checks_count'] > 0)
                                        <div class="text-sm font-medium text-gray-900">{{ $stat['aset_checks_count'] }}</div>
                                        <div class="text-xs">
                                            <span class="text-green-600">{{ $stat['aset_aman_count'] }} aman</span>
                                            @if($stat['aset_bermasalah_count'] > 0)
                                                <span class="text-red-600">{{ $stat['aset_bermasalah_count'] }} rusak</span>
                                            @endif
                                            @if($stat['aset_hilang_count'] > 0)
                                                <span class="text-orange-600">{{ $stat['aset_hilang_count'] }} hilang</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($stat['last_visit'])
                                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($stat['last_visit'])->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($stat['last_visit'])->format('H:i') }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">Belum pernah</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $stat['last_visit_user'] ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($stat['distance_accuracy'] !== null)
                                        @if($stat['distance_accuracy'] <= 50)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>{{ $stat['distance_accuracy'] }}m
                                            </span>
                                        @elseif($stat['distance_accuracy'] <= 100)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $stat['distance_accuracy'] }}m
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>{{ $stat['distance_accuracy'] }}m
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-map-marker-alt text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Tidak ada checkpoint</p>
                                    <p class="text-sm">Belum ada checkpoint yang terdaftar di area ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: Verifikasi Lokasi -->
        <div id="content-location" class="tab-content hidden p-6">
            <!-- Maps Container -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Peta Lokasi Checkpoint</h4>
                
                <!-- Simple direct map approach -->
                <div id="map-container" style="height: 400px; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb; position: relative; overflow: hidden;">
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
                
                <div id="map-status" class="mt-2 text-sm text-gray-600">
                    <span id="map-loading" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memuat peta...
                    </span>
                </div>
            </div>

            <!-- Location Verification Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Checkpoint Locations -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-900 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Lokasi Checkpoint Terdaftar
                    </h5>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @foreach($checkpointStats as $stat)
                            @if($stat['checkpoint']->latitude && $stat['checkpoint']->longitude)
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h6 class="font-medium text-gray-900">{{ $stat['checkpoint']->nama }}</h6>
                                            <p class="text-xs text-gray-500 mb-1">{{ $stat['checkpoint']->qr_code }}</p>
                                            <p class="text-xs text-gray-600">
                                                <i class="fas fa-map-pin mr-1"></i>
                                                {{ $stat['checkpoint']->latitude }}, {{ $stat['checkpoint']->longitude }}
                                            </p>
                                            @if($stat['checkpoint']->alamat)
                                                <p class="text-xs text-gray-500 mt-1">{{ $stat['checkpoint']->alamat }}</p>
                                            @endif
                                        </div>
                                        <button data-focus-checkpoint data-lat="{{ $stat['checkpoint']->latitude }}" data-lng="{{ $stat['checkpoint']->longitude }}" data-name="{{ $stat['checkpoint']->nama }}" 
                                                class="text-blue-600 hover:text-blue-800 text-xs">
                                            <i class="fas fa-crosshairs"></i> Lihat
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Recent Patrol Locations -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-semibold text-gray-900 mb-3">
                        <i class="fas fa-route mr-2 text-green-600"></i>Lokasi Patroli Aktual
                    </h5>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @php
                            $patrolLocations = collect();
                            foreach ($patrolis as $patroli) {
                                foreach ($patroli->details as $detail) {
                                    if ($detail->latitude && $detail->longitude) {
                                        $patrolLocations->push($detail);
                                    }
                                }
                            }
                            $patrolLocations = $patrolLocations->sortByDesc('waktu_scan')->take(10);
                        @endphp
                        
                        @forelse($patrolLocations as $location)
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h6 class="font-medium text-gray-900">{{ $location->checkpoint->nama ?? 'Unknown Checkpoint' }}</h6>
                                        <p class="text-xs text-gray-500 mb-1">
                                            {{ $location->waktu_scan ? \Carbon\Carbon::parse($location->waktu_scan)->format('d M Y H:i') : '-' }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            <i class="fas fa-map-pin mr-1"></i>
                                            {{ $location->latitude }}, {{ $location->longitude }}
                                        </p>
                                        @php
                                            $checkpoint = $location->checkpoint;
                                            $distance = null;
                                            if ($checkpoint && $checkpoint->latitude && $checkpoint->longitude) {
                                                $earthRadius = 6371000; // Earth radius in meters
                                                $dLat = deg2rad($location->latitude - $checkpoint->latitude);
                                                $dLon = deg2rad($location->longitude - $checkpoint->longitude);
                                                $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($checkpoint->latitude)) * cos(deg2rad($location->latitude)) * sin($dLon/2) * sin($dLon/2);
                                                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                                                $distance = round($earthRadius * $c, 2);
                                            }
                                        @endphp
                                        @if($distance !== null)
                                            <p class="text-xs mt-1">
                                                @if($distance <= 50)
                                                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Akurat ({{ $distance }}m)</span>
                                                @elseif($distance <= 100)
                                                    <span class="text-yellow-600"><i class="fas fa-exclamation-triangle mr-1"></i>Cukup ({{ $distance }}m)</span>
                                                @else
                                                    <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Jauh ({{ $distance }}m)</span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col space-y-1">
                                        <button data-focus-patrol data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}" data-name="{{ $location->checkpoint->nama ?? 'Patrol Location' }}" 
                                                class="text-green-600 hover:text-green-800 text-xs">
                                            <i class="fas fa-crosshairs"></i> Lihat
                                        </button>
                                        @if($location->foto_verifikasi)
                                            <button data-show-photo data-url="{{ asset('storage/' . $location->foto_verifikasi) }}" 
                                                    class="text-blue-600 hover:text-blue-800 text-xs">
                                                <i class="fas fa-camera"></i> Foto
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-map-marked-alt text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">Tidak ada data lokasi patroli</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Location Statistics -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $totalWithGPS = $patrolLocations->count();
                    $accurateLocations = 0;
                    
                    foreach($patrolLocations as $location) {
                        $checkpoint = $location->checkpoint;
                        if ($checkpoint && $checkpoint->latitude && $checkpoint->longitude) {
                            $earthRadius = 6371000;
                            $dLat = deg2rad($location->latitude - $checkpoint->latitude);
                            $dLon = deg2rad($location->longitude - $checkpoint->longitude);
                            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($checkpoint->latitude)) * cos(deg2rad($location->latitude)) * sin($dLon/2) * sin($dLon/2);
                            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                            $distance = round($earthRadius * $c, 2);
                            
                            if ($distance <= 50) {
                                $accurateLocations++;
                            }
                        }
                    }
                    
                    $accuracyRate = $totalWithGPS > 0 ? round(($accurateLocations / $totalWithGPS) * 100, 1) : 0;
                @endphp
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $totalWithGPS }}</div>
                    <div class="text-xs text-gray-600">Total GPS Data</div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $accurateLocations }}</div>
                    <div class="text-xs text-gray-600">Lokasi Akurat</div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $accuracyRate }}%</div>
                    <div class="text-xs text-gray-600">Tingkat Akurasi</div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $totalWithGPS - $accurateLocations }}</div>
                    <div class="text-xs text-gray-600">Perlu Verifikasi</div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Status Aset -->
        <div id="content-aset" class="tab-content hidden p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aset</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checkpoint</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Check</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $allAsetChecks = collect();
                            foreach ($patrolis as $patroli) {
                                foreach ($patroli->details as $detail) {
                                    if ($detail->asetChecks) {
                                        foreach ($detail->asetChecks as $asetCheck) {
                                            $allAsetChecks->push($asetCheck);
                                        }
                                    }
                                }
                            }
                            $allAsetChecks = $allAsetChecks->sortByDesc('created_at');
                        @endphp
                        
                        @forelse($allAsetChecks as $asetCheck)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $asetCheck->asetKawasan->nama ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $asetCheck->asetKawasan->kode_aset ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $asetCheck->patroliDetail->checkpoint->nama ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $asetCheck->patroliDetail->checkpoint->qr_code ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($asetCheck->status === 'aman')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-shield-alt mr-1"></i>Aman
                                        </span>
                                    @elseif($asetCheck->status === 'bermasalah')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Bermasalah
                                        </span>
                                    @elseif($asetCheck->status === 'hilang')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-search mr-1"></i>Hilang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-question mr-1"></i>{{ ucfirst($asetCheck->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        {{ $asetCheck->catatan ? Str::limit($asetCheck->catatan, 50) : '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($asetCheck->created_at)
                                        <div class="text-sm text-gray-900">{{ $asetCheck->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $asetCheck->created_at->format('H:i') }}</div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($asetCheck->foto)
                                        <button data-show-aset-photo data-url="{{ $asetCheck->foto_url }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-shield-alt text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Tidak ada data aset check</p>
                                    <p class="text-sm">Belum ada pengecekan aset yang dilakukan dalam periode ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto -->
<div id="photoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Foto Aset</h3>
                    <button onclick="closePhoto()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <img id="photoImage" src="" alt="Foto Aset" class="w-full h-auto rounded-lg">
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto Verifikasi -->
<div id="verificationPhotoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-camera mr-2 text-blue-600"></i>Foto Verifikasi Lokasi
                    </h3>
                    <button onclick="closeVerificationPhoto()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <img id="verificationPhotoImage" src="" alt="Foto Verifikasi" class="w-full h-auto rounded-lg">
                <div class="mt-3 text-center">
                    <p class="text-sm text-gray-600">Foto ini diambil petugas untuk memverifikasi keberadaan di lokasi checkpoint</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- Leaflet CSS - Load first -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<!-- CRITICAL: Ultra-aggressive map containment styling -->
<style>
    /* FORCE map container to be contained - use very specific selectors */
    #content-location #map,
    div#map,
    .tab-content #map {
        height: 400px !important;
        width: 100% !important;
        max-height: 400px !important;
        max-width: 100% !important;
        position: relative !important;
        z-index: 1 !important;
        background: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        /* CRITICAL: Prevent any positioning that could cause fullscreen */
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        transform: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* FORCE Leaflet container to stay within bounds */
    #content-location .leaflet-container,
    div#map .leaflet-container,
    .tab-content .leaflet-container {
        height: 400px !important;
        width: 100% !important;
        max-height: 400px !important;
        max-width: 100% !important;
        font-family: inherit !important;
        position: relative !important;
        z-index: 1 !important;
        overflow: hidden !important;
        /* CRITICAL: Prevent any positioning that could cause fullscreen */
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        transform: none !important;
    }
    
    /* FORCE leaflet pane to stay contained */
    #content-location .leaflet-map-pane,
    div#map .leaflet-map-pane,
    .tab-content .leaflet-map-pane {
        position: relative !important;
        z-index: 1 !important;
        overflow: hidden !important;
        /* CRITICAL: Prevent any positioning that could cause fullscreen */
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        transform: none !important;
    }
    
    /* Fix leaflet controls positioning */
    #content-location .leaflet-control-container,
    div#map .leaflet-control-container,
    .tab-content .leaflet-control-container {
        position: relative !important;
        z-index: 2 !important;
    }
    
    /* Ensure tiles stay within container */
    #content-location .leaflet-tile,
    div#map .leaflet-tile,
    .tab-content .leaflet-tile {
        max-width: none !important;
    }
    
    /* Fix popup styling */
    #content-location .leaflet-popup-content-wrapper,
    div#map .leaflet-popup-content-wrapper,
    .tab-content .leaflet-popup-content-wrapper {
        border-radius: 8px !important;
    }
    
    /* CRITICAL: Ensure tab content contains the map properly */
    #content-location {
        position: relative !important;
        overflow: hidden !important;
        max-width: 100% !important;
        contain: layout style !important;
    }
    
    /* Ensure map parent container is properly sized */
    #content-location .mb-6 {
        position: relative !important;
        overflow: hidden !important;
        contain: layout style !important;
    }
    
    /* Override any potential fullscreen classes */
    .leaflet-fullscreen-on #map,
    .leaflet-pseudo-fullscreen #map {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        width: 100% !important;
        height: 400px !important;
        max-width: 100% !important;
        max-height: 400px !important;
        z-index: 1 !important;
    }
</style>
@endpush

@push('scripts')
<script>
console.log('Kawasan detail JavaScript loaded');

// Simple tab switching function
function showTab(tabName) {
    console.log('showTab called with:', tabName);
    
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById('content-' + tabName);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    // Add active class to selected tab button
    const selectedButton = document.getElementById('tab-' + tabName);
    if (selectedButton) {
        selectedButton.classList.add('active', 'border-blue-500', 'text-blue-600');
        selectedButton.classList.remove('border-transparent', 'text-gray-500');
    }
    
    console.log('Tab switched to:', tabName);
}

function showPhoto(photoUrl) {
    document.getElementById('photoImage').src = photoUrl;
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhoto() {
    document.getElementById('photoModal').classList.add('hidden');
}

function showVerificationPhoto(photoUrl) {
    document.getElementById('verificationPhotoImage').src = photoUrl;
    document.getElementById('verificationPhotoModal').classList.remove('hidden');
}

function closeVerificationPhoto() {
    document.getElementById('verificationPhotoModal').classList.add('hidden');
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing tabs');
    
    // Add click listeners to tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.id.replace('tab-', '');
            showTab(tabName);
        });
    });
    
    // Add click listeners for focus checkpoint buttons
    const focusCheckpointButtons = document.querySelectorAll('[data-focus-checkpoint]');
    focusCheckpointButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const lat = this.getAttribute('data-lat');
            const lng = this.getAttribute('data-lng');
            const name = this.getAttribute('data-name');
            focusCheckpoint(lat, lng, name);
        });
    });
    
    // Add click listeners for focus patrol buttons
    const focusPatrolButtons = document.querySelectorAll('[data-focus-patrol]');
    focusPatrolButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const lat = this.getAttribute('data-lat');
            const lng = this.getAttribute('data-lng');
            const name = this.getAttribute('data-name');
            focusPatrolLocation(lat, lng, name);
        });
    });
    
    // Add click listeners for photo buttons
    const photoButtons = document.querySelectorAll('[data-show-photo]');
    photoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            showVerificationPhoto(url);
        });
    });
    
    // Add click listeners for aset photo buttons
    const asetPhotoButtons = document.querySelectorAll('[data-show-aset-photo]');
    asetPhotoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            showPhoto(url);
        });
    });
    
    // Add modal close listeners
    const photoModal = document.getElementById('photoModal');
    if (photoModal) {
        photoModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePhoto();
            }
        });
    }
    
    const verificationModal = document.getElementById('verificationPhotoModal');
    if (verificationModal) {
        verificationModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeVerificationPhoto();
            }
        });
    }
    
    console.log('Tab system initialized');
});
</script>

<!-- Leaflet JS - Load once -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
// Ultra-simple map implementation - exactly like working test
let map;
let markers = [];

// Fix Leaflet marker icons issue - use CDN icons
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
});

        // Ultra-simple direct map implementation
        let map;
        let markers = [];

        function initSimpleDirectMap() {
            try {
                console.log('🗺️ Starting simple direct map initialization...');
                
                // Update status
                const statusElement = document.getElementById('map-loading');
                if (statusElement) {
                    statusElement.innerHTML = '<span class="text-blue-600">Initializing simple map...</span>';
                }
                
                // Check if map element exists
                const mapElement = document.getElementById('map');
                if (!mapElement) {
                    console.error('❌ Map element not found');
                    return;
                }
                
                console.log('✅ Map element found, dimensions:', mapElement.offsetWidth, 'x', mapElement.offsetHeight);
                
                // Force container styles
                mapElement.style.height = '400px';
                mapElement.style.width = '100%';
                
                // Initialize map with very basic settings
                map = L.map('map').setView([-6.2088, 106.8456], 15);
                
                console.log('✅ Leaflet map object created');
                
                // Add tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18
                }).addTo(map);
                
                console.log('✅ Tiles added');
                
                // Add simple test markers
                const testMarker1 = L.marker([-6.2088, 106.8456])
                    .addTo(map)
                    .bindPopup('<b>Test Checkpoint</b><br>Map is working!');
                
                const testMarker2 = L.marker([-6.2090, 106.8458])
                    .addTo(map)
                    .bindPopup('<b>Test Patrol</b><br>Map is working!');
                
                markers.push(testMarker1, testMarker2);
                
                console.log('✅ Test markers added');
                
                // Update status
                if (statusElement) {
                    statusElement.innerHTML = '<span class="text-green-600">✅ Simple map loaded with ' + markers.length + ' test markers</span>';
                }
                
                // Force resize
                setTimeout(function() {
                    if (map) {
                        map.invalidateSize();
                        console.log('✅ Map size invalidated');
                    }
                }, 500);
                
                console.log('✅ Simple direct map initialization complete');
                
            } catch (error) {
                console.error('❌ Error in simple direct map initialization:', error);
                const statusElement = document.getElementById('map-loading');
                if (statusElement) {
                    statusElement.innerHTML = '<span class="text-red-600">❌ Map failed to load: ' + error.message + '</span>';
                }
            }
        }

function addUltraSimpleMarkers() {
    console.log('📍 Adding ultra-simple markers...');
    
    try {
        // Add checkpoint markers (blue) - using JSON encoding for safety
        @foreach($checkpointStats as $stat)
            @if($stat['checkpoint']->latitude && $stat['checkpoint']->longitude)
                @php
                    $checkpointData = [
                        'lat' => $stat['checkpoint']->latitude,
                        'lng' => $stat['checkpoint']->longitude,
                        'nama' => $stat['checkpoint']->nama,
                        'qr_code' => $stat['checkpoint']->qr_code
                    ];
                @endphp
                const checkpointData{{ $loop->index }} = @json($checkpointData);
                const checkpointMarker{{ $loop->index }} = L.marker([checkpointData{{ $loop->index }}.lat, checkpointData{{ $loop->index }}.lng])
                    .addTo(map)
                    .bindPopup('<b>' + checkpointData{{ $loop->index }}.nama + '</b><br>' + checkpointData{{ $loop->index }}.qr_code + '<br><small>Checkpoint Terdaftar</small>');
                markers.push(checkpointMarker{{ $loop->index }});
                console.log('✅ Added checkpoint:', checkpointData{{ $loop->index }}.nama);
            @endif
        @endforeach
        
        // Add patrol markers (green) - using JSON encoding for safety
        @php
            $patrolLocations = collect();
            foreach ($patrolis as $patroli) {
                foreach ($patroli->details as $detail) {
                    if ($detail->latitude && $detail->longitude) {
                        $patrolLocations->push($detail);
                    }
                }
            }
        @endphp
        
        @foreach($patrolLocations as $location)
            @php
                $patrolData = [
                    'lat' => $location->latitude,
                    'lng' => $location->longitude,
                    'nama' => $location->checkpoint->nama ?? 'Patrol Location',
                    'waktu_scan' => $location->waktu_scan ? \Carbon\Carbon::parse($location->waktu_scan)->format('d M Y H:i') : '-'
                ];
            @endphp
            const patrolData{{ $loop->index }} = @json($patrolData);
            const patrolMarker{{ $loop->index }} = L.marker([patrolData{{ $loop->index }}.lat, patrolData{{ $loop->index }}.lng])
                .addTo(map)
                .bindPopup('<b>' + patrolData{{ $loop->index }}.nama + '</b><br>' + patrolData{{ $loop->index }}.waktu_scan + '<br><small>Lokasi Patroli</small>');
            markers.push(patrolMarker{{ $loop->index }});
            console.log('✅ Added patrol location');
        @endforeach
        
        // Fit map to show all markers
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
            console.log('🎯 Map bounds adjusted for', markers.length, 'markers');
        }
        
    } catch (error) {
        console.error('❌ Error adding markers:', error);
    }
}

        // Focus functions for buttons - using safer approach
        window.focusCheckpoint = function(lat, lng, name) {
            if (map) {
                map.setView([parseFloat(lat), parseFloat(lng)], 18);
                L.popup()
                    .setLatLng([parseFloat(lat), parseFloat(lng)])
                    .setContent('<b>' + name + '</b><br><small>Checkpoint Terdaftar</small>')
                    .openOn(map);
            }
        };

        window.focusPatrolLocation = function(lat, lng, name) {
            if (map) {
                map.setView([parseFloat(lat), parseFloat(lng)], 18);
                L.popup()
                    .setLatLng([parseFloat(lat), parseFloat(lng)])
                    .setContent('<b>' + name + '</b><br><small>Lokasi Patroli</small>')
                    .openOn(map);
            }
        };

// Initialize when location tab is clicked
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌐 DOM loaded for ultra-simple map');
    
    const locationTab = document.getElementById('tab-location');
    if (locationTab) {
        locationTab.addEventListener('click', function() {
            console.log('📍 Location tab clicked');
            
            // Wait for tab to be visible
            setTimeout(function() {
                const tabContent = document.getElementById('content-location');
                if (tabContent && !tabContent.classList.contains('hidden')) {
                    console.log('✅ Location tab is visible, initializing map...');
                    if (!map) {
                        initSimpleDirectMap();
                    } else {
                        console.log('🔄 Map already exists, refreshing...');
                        map.invalidateSize();
                    }
                } else {
                    console.log('⚠️ Location tab not visible yet');
                }
            }, 200);
        });
    }
    
    // Also check if location tab is already active
    const locationContent = document.getElementById('content-location');
    if (locationContent && !locationContent.classList.contains('hidden')) {
        console.log('🚀 Location tab already active, initializing map...');
        setTimeout(initSimpleDirectMap, 300);
    }
});

// Focus functions for buttons - direct approach
window.focusCheckpoint = function(lat, lng, name) {
    if (map) {
        map.setView([parseFloat(lat), parseFloat(lng)], 18);
        L.popup()
            .setLatLng([parseFloat(lat), parseFloat(lng)])
            .setContent('<b>' + name + '</b><br><small>Checkpoint Terdaftar</small>')
            .openOn(map);
    }
};

window.focusPatrolLocation = function(lat, lng, name) {
    if (map) {
        map.setView([parseFloat(lat), parseFloat(lng)], 18);
        L.popup()
            .setLatLng([parseFloat(lat), parseFloat(lng)])
            .setContent('<b>' + name + '</b><br><small>Lokasi Patroli</small>')
            .openOn(map);
    }
};
</script>
@endpush