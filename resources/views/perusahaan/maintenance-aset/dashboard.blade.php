@extends('perusahaan.layouts.app')

@section('title', 'Dashboard Maintenance & Servis')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Maintenance & Servis</h1>
            <p class="text-gray-600 mt-1">Ringkasan dan analisis maintenance aset perusahaan</p>
        </div>
        <div class="flex gap-3">
            <!-- Project Filter -->
            <select id="projectFilter" onchange="filterByProject()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->nama }}</option>
                @endforeach
            </select>
            <a href="{{ route('perusahaan.maintenance-aset.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-list"></i> Daftar Maintenance
            </a>
            <a href="{{ route('perusahaan.maintenance-aset.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-plus"></i> Jadwal Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Maintenance</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-wrench text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Terjadwal</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['scheduled'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sedang Dikerjakan</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-cog text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Selesai</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Terlambat</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Mendatang (7 hari)</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-clock text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Cost Chart -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-600"></i>
                    Biaya Maintenance Bulanan ({{ date('Y') }})
                </h3>
                <div class="h-64">
                    <canvas id="monthlyCostChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Maintenance Type Distribution -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                    Distribusi Jenis Maintenance
                </h3>
                <div class="h-64">
                    <canvas id="maintenanceTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Maintenances -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                        Maintenance Mendatang
                    </h3>
                    <a href="{{ route('perusahaan.maintenance-aset.index', ['status' => 'scheduled']) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        Lihat Semua
                    </a>
                </div>
                
                @if($upcomingMaintenances->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingMaintenances as $maintenance)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    @if($maintenance->asset_type == 'data_aset')
                                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-car text-green-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $maintenance->nomor_maintenance }}</p>
                                        <p class="text-sm text-gray-600">{{ Str::limit($maintenance->asset_name, 30) }}</p>
                                        <p class="text-xs text-blue-600">{{ $maintenance->project->nama ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $maintenance->tanggal_maintenance->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $maintenance->tanggal_maintenance->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Tidak ada maintenance mendatang dalam 7 hari ke depan</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Overdue Maintenances -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        Maintenance Terlambat
                    </h3>
                    <a href="{{ route('perusahaan.maintenance-aset.index') }}?overdue=1" 
                       class="text-red-600 hover:text-red-800 text-sm">
                        Lihat Semua
                    </a>
                </div>
                
                @if($overdueMaintenances->count() > 0)
                    <div class="space-y-4">
                        @foreach($overdueMaintenances as $maintenance)
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    @if($maintenance->asset_type == 'data_aset')
                                        <div class="flex-shrink-0 h-8 w-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-box text-red-600"></i>
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-8 w-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-car text-red-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $maintenance->nomor_maintenance }}</p>
                                        <p class="text-sm text-gray-600">{{ Str::limit($maintenance->asset_name, 30) }}</p>
                                        <p class="text-xs text-red-600">{{ $maintenance->project->nama ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-red-600">{{ $maintenance->tanggal_maintenance->format('d/m/Y') }}</p>
                                <p class="text-xs text-red-500">Terlambat {{ $maintenance->tanggal_maintenance->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                        <p class="text-gray-500">Tidak ada maintenance yang terlambat</p>
                    </div>
                @endif
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

document.addEventListener('DOMContentLoaded', function() {
    // Set selected project filter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('project_id');
    if (projectId) {
        document.getElementById('projectFilter').value = projectId;
    }

    // Monthly Cost Chart
    const monthlyCostCtx = document.getElementById('monthlyCostChart').getContext('2d');
    const monthlyCostData = @json($monthlyCosts);
    
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const costLabels = [];
    const costValues = [];
    
    // Initialize all months with 0
    for (let i = 1; i <= 12; i++) {
        costLabels.push(monthNames[i - 1]);
        costValues.push(0);
    }
    
    // Fill actual data
    monthlyCostData.forEach(item => {
        costValues[item.month - 1] = item.total;
    });
    
    new Chart(monthlyCostCtx, {
        type: 'line',
        data: {
            labels: costLabels,
            datasets: [{
                label: 'Biaya Maintenance (Rp)',
                data: costValues,
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Biaya: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Maintenance Type Chart
    const maintenanceTypeCtx = document.getElementById('maintenanceTypeChart').getContext('2d');
    
    // Get maintenance type data (you might want to pass this from controller)
    const typeData = {
        preventive: {{ \App\Models\MaintenanceAset::where('jenis_maintenance', 'preventive')->count() }},
        corrective: {{ \App\Models\MaintenanceAset::where('jenis_maintenance', 'corrective')->count() }},
        predictive: {{ \App\Models\MaintenanceAset::where('jenis_maintenance', 'predictive')->count() }}
    };
    
    new Chart(maintenanceTypeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Preventive', 'Corrective', 'Predictive'],
            datasets: [{
                data: [typeData.preventive, typeData.corrective, typeData.predictive],
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(147, 51, 234)'
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
});
</script>
@endpush