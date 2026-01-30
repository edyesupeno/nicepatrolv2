@extends('perusahaan.layouts.app')

@section('title', 'Laporan Maintenance & Servis')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Maintenance & Servis</h1>
            <p class="text-gray-600 mt-1">Laporan lengkap maintenance dan servis aset perusahaan</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('perusahaan.maintenance-aset.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button onclick="exportPDF()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Laporan</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Project</option>
                        @foreach(\App\Models\Project::select('id', 'nama')->orderBy('nama')->get() as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
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
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Maintenance</label>
                    <select name="jenis_maintenance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="preventive" {{ request('jenis_maintenance') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                        <option value="corrective" {{ request('jenis_maintenance') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                        <option value="predictive" {{ request('jenis_maintenance') == 'predictive' ? 'selected' : '' }}>Predictive</option>
                    </select>
                </div>
                <div class="md:col-span-5 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-filter"></i> Filter Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @php
        $totalBiaya = $maintenances->sum('total_biaya');
        $totalCompleted = $maintenances->where('status', 'completed')->count();
        $totalScheduled = $maintenances->where('status', 'scheduled')->count();
        $totalInProgress = $maintenances->where('status', 'in_progress')->count();
        $avgBiaya = $maintenances->count() > 0 ? $totalBiaya / $maintenances->count() : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Maintenance</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $maintenances->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-wrench text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $totalCompleted }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Terjadwal</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalScheduled }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Biaya</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-money-bill text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Rata-rata Biaya</p>
                    <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($avgBiaya, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calculator text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Maintenance</h3>
            
            @if($maintenances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Maintenance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teknisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($maintenances as $index => $maintenance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $maintenance->nomor_maintenance }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $maintenance->project->nama ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($maintenance->asset_type == 'data_aset')
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
                                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($maintenance->asset_name, 25) }}</div>
                                            <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $maintenance->asset_type)) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $maintenance->tanggal_maintenance->format('d/m/Y') }}</div>
                                    @if($maintenance->waktu_mulai)
                                        <div class="text-sm text-gray-500">{{ $maintenance->waktu_mulai }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $maintenance->jenis_maintenance == 'preventive' ? 'bg-green-100 text-green-800' : 
                                           ($maintenance->jenis_maintenance == 'corrective' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ ucfirst($maintenance->jenis_maintenance) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{!! $maintenance->status_badge !!}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $maintenance->teknisi_internal ?? $maintenance->vendor_eksternal ?? 'Belum ditentukan' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $maintenance->formatted_total_biaya }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    Total Biaya:
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600">
                                    Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                    <p class="text-lg font-medium text-gray-500 mb-2">Tidak ada data maintenance</p>
                    <p class="text-gray-400">Sesuaikan filter untuk melihat data maintenance</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Cost Breakdown by Type -->
    @if($maintenances->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Cost by Maintenance Type -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Biaya per Jenis Maintenance</h3>
                @php
                    $costByType = $maintenances->groupBy('jenis_maintenance')->map(function($items) {
                        return $items->sum('total_biaya');
                    });
                @endphp
                
                <div class="space-y-4">
                    @foreach($costByType as $type => $cost)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full 
                                {{ $type == 'preventive' ? 'bg-green-500' : 
                                   ($type == 'corrective' ? 'bg-yellow-500' : 'bg-purple-500') }}">
                            </div>
                            <span class="font-medium text-gray-900">{{ ucfirst($type) }}</span>
                        </div>
                        <span class="font-bold text-gray-900">Rp {{ number_format($cost, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cost by Status -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Biaya per Status</h3>
                @php
                    $costByStatus = $maintenances->groupBy('status')->map(function($items) {
                        return $items->sum('total_biaya');
                    });
                @endphp
                
                <div class="space-y-4">
                    @foreach($costByStatus as $status => $cost)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full 
                                {{ $status == 'completed' ? 'bg-green-500' : 
                                   ($status == 'in_progress' ? 'bg-yellow-500' : 
                                   ($status == 'scheduled' ? 'bg-blue-500' : 'bg-red-500')) }}">
                            </div>
                            <span class="font-medium text-gray-900">
                                @switch($status)
                                    @case('scheduled') Terjadwal @break
                                    @case('in_progress') Sedang Dikerjakan @break
                                    @case('completed') Selesai @break
                                    @case('cancelled') Dibatalkan @break
                                    @default {{ ucfirst($status) }}
                                @endswitch
                            </span>
                        </div>
                        <span class="font-bold text-gray-900">Rp {{ number_format($cost, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportPDF() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('export', 'pdf');
    
    Swal.fire({
        title: 'Export PDF',
        text: 'Sedang memproses laporan PDF...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            
            // Redirect to PDF export
            window.location.href = currentUrl.toString();
            
            // Close loading after a delay
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    });
}
</script>
@endpush