@extends('perusahaan.layouts.app')

@section('title', 'Kondisi Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kondisi Aset</h1>
            <p class="text-gray-600 mt-1">Dashboard statistik dan kondisi aset perusahaan</p>
        </div>
        <div class="flex gap-3">
            <!-- Project Filter -->
            <select id="projectFilter" onchange="filterByProject()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama }}
                    </option>
                @endforeach
            </select>
            <button onclick="exportPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Assets -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Aset</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($dataAsetStats['total'] + $asetKendaraanStats['total']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Data Aset: {{ $dataAsetStats['total'] }} | Kendaraan: {{ $asetKendaraanStats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-cube text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Nilai Aset</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($dataAsetValue['total_value'] + $asetKendaraanValue['total_value'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Rata-rata: Rp {{ number_format(($dataAsetValue['avg_value'] + $asetKendaraanValue['avg_value']) / 2, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Assets -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aset Aktif</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($dataAsetStats['ada'] + $asetKendaraanStats['aktif']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format((($dataAsetStats['ada'] + $asetKendaraanStats['aktif']) / ($dataAsetStats['total'] + $asetKendaraanStats['total'])) * 100, 1) }}% dari total</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Problem Assets -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aset Bermasalah</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($dataAsetStats['rusak'] + $asetKendaraanStats['rusak'] + $asetKendaraanStats['maintenance']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Rusak & Maintenance</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Data Aset Status Chart -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    Status Data Aset
                </h3>
                <div class="h-64">
                    <canvas id="dataAsetStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Aset Kendaraan Status Chart -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-car text-green-600"></i>
                    Status Aset Kendaraan
                </h3>
                <div class="h-64">
                    <canvas id="asetKendaraanStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category & Type Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Data Aset by Category -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-tags text-purple-600"></i>
                    Data Aset per Kategori
                </h3>
                <div class="h-64">
                    <canvas id="dataAsetCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Aset Kendaraan by Type -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-truck text-orange-600"></i>
                    Kendaraan per Jenis
                </h3>
                <div class="h-64">
                    <canvas id="asetKendaraanTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Data Aset by Project -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-project-diagram text-indigo-600"></i>
                    Data Aset per Project (Top 10)
                </h3>
                <div class="space-y-3">
                    @forelse($dataAsetByProject as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                <span class="font-medium text-gray-900">{{ $item->project->nama ?? 'Tidak ada project' }}</span>
                            </div>
                            <span class="font-bold text-indigo-600">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Aset Kendaraan by Project -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-project-diagram text-teal-600"></i>
                    Kendaraan per Project (Top 10)
                </h3>
                <div class="space-y-3">
                    @forelse($asetKendaraanByProject as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                                <span class="font-medium text-gray-900">{{ $item->project->nama ?? 'Tidak ada project' }}</span>
                            </div>
                            <span class="font-bold text-teal-600">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-blue-600"></i>
                Aktivitas Terbaru (30 Hari Terakhir)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $recentDataAset }}</div>
                    <div class="text-sm text-gray-600">Data Aset Baru</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $recentAsetKendaraan }}</div>
                    <div class="text-sm text-gray-600">Kendaraan Baru</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function filterByProject() {
    const projectId = document.getElementById('projectFilter').value;
    const url = new URL(window.location.href);
    
    if (projectId) {
        url.searchParams.set('project_id', projectId);
    } else {
        url.searchParams.delete('project_id');
    }
    
    window.location.href = url.toString();
}

function exportPDF() {
    const projectId = document.getElementById('projectFilter').value;
    const url = new URL('{{ route("perusahaan.kondisi-aset.export-pdf") }}');
    
    if (projectId) {
        url.searchParams.set('project_id', projectId);
    }
    
    Swal.fire({
        title: 'Export PDF',
        text: 'Sedang memproses laporan PDF...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            window.location.href = url.toString();
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Data Aset Status Chart
    const dataAsetStatusCtx = document.getElementById('dataAsetStatusChart').getContext('2d');
    new Chart(dataAsetStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Ada', 'Rusak', 'Dijual', 'Dihapus'],
            datasets: [{
                data: [
                    {{ $dataAsetStats['ada'] }},
                    {{ $dataAsetStats['rusak'] }},
                    {{ $dataAsetStats['dijual'] }},
                    {{ $dataAsetStats['dihapus'] }}
                ],
                backgroundColor: [
                    '#10b981',
                    '#ef4444',
                    '#3b82f6',
                    '#6b7280'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Aset Kendaraan Status Chart
    const asetKendaraanStatusCtx = document.getElementById('asetKendaraanStatusChart').getContext('2d');
    new Chart(asetKendaraanStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Maintenance', 'Rusak', 'Dijual', 'Hilang'],
            datasets: [{
                data: [
                    {{ $asetKendaraanStats['aktif'] }},
                    {{ $asetKendaraanStats['maintenance'] }},
                    {{ $asetKendaraanStats['rusak'] }},
                    {{ $asetKendaraanStats['dijual'] }},
                    {{ $asetKendaraanStats['hilang'] }}
                ],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#3b82f6',
                    '#6b7280'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Data Aset Category Chart
    const dataAsetCategoryCtx = document.getElementById('dataAsetCategoryChart').getContext('2d');
    new Chart(dataAsetCategoryCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($dataAsetByCategory as $item)
                    '{{ $item->kategori }}',
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Aset',
                data: [
                    @foreach($dataAsetByCategory as $item)
                        {{ $item->total }},
                    @endforeach
                ],
                backgroundColor: '#8b5cf6',
                borderColor: '#7c3aed',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Aset Kendaraan Type Chart
    const asetKendaraanTypeCtx = document.getElementById('asetKendaraanTypeChart').getContext('2d');
    new Chart(asetKendaraanTypeCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($asetKendaraanByType as $item)
                    '{{ ucfirst($item->jenis_kendaraan) }}',
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Kendaraan',
                data: [
                    @foreach($asetKendaraanByType as $item)
                        {{ $item->total }},
                    @endforeach
                ],
                backgroundColor: '#f97316',
                borderColor: '#ea580c',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush