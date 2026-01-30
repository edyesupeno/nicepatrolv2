@extends('perusahaan.layouts.app')

@section('page-title', 'Laporan Penyerahan Perlengkapan')
@section('page-subtitle', 'Progress dan statistik penyerahan item')

@push('styles')
<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endpush

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Penyerahan
    </a>
</div>

<!-- Informasi Penyerahan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Penyerahan</h3>
            <button onclick="printLaporan()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-print mr-2"></i>Print Laporan
            </button>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Project</label>
                <p class="text-gray-900 font-medium">{{ $penyerahanRecord->project->nama }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                <p class="text-gray-900">{{ $penyerahanRecord->tanggal_mulai->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                <p class="text-gray-900">{{ $penyerahanRecord->tanggal_selesai->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span class="px-2 py-1 text-xs font-medium rounded-full 
                    {{ $penyerahanRecord->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $penyerahanRecord->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $penyerahanRecord->status === 'diserahkan' ? 'bg-green-100 text-green-800' : '' }}">
                    {{ ucfirst($penyerahanRecord->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Overall Progress -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Progress Keseluruhan</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $totalKaryawan }}</div>
                <div class="text-sm text-gray-500">Total Karyawan</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $totalItems }}</div>
                <div class="text-sm text-gray-500">Total Item</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $totalDiserahkan }}</div>
                <div class="text-sm text-gray-500">Item Diserahkan</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">{{ $overallPersentase }}%</div>
                <div class="text-sm text-gray-500">Progress</div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progress Penyerahan</span>
                <span class="text-sm text-gray-500">{{ $totalDiserahkan }}/{{ $totalItems }} item</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-300" 
                     style="width: {{ $overallPersentase }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $statusBreakdown['belum_diserahkan'] }}</div>
                    <div class="text-sm text-gray-500">Belum Diserahkan</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $statusBreakdown['sebagian_diserahkan'] }}</div>
                    <div class="text-sm text-gray-500">Sebagian Diserahkan</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $statusBreakdown['sudah_diserahkan'] }}</div>
                    <div class="text-sm text-gray-500">Sudah Diserahkan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress by Category -->
@if($itemsByCategory->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Progress per Kategori</h3>
        <p class="text-sm text-gray-500 mt-1">Klik kategori untuk melihat detail item</p>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($itemsByCategory as $index => $category)
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <!-- Category Header (Clickable) -->
                <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors" 
                     onclick="toggleCategory({{ $index }})">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-3">{{ $category['kategori'] }}</span>
                                <i id="icon-{{ $index }}" class="fas fa-chevron-down text-gray-400 transition-transform duration-200"></i>
                            </div>
                            <span class="text-sm text-gray-500">{{ $category['diserahkan'] }}/{{ $category['total'] }} item ({{ $category['persentase'] }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ $category['persentase'] }}%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Category Items (Collapsible) -->
                <div id="category-{{ $index }}" class="hidden border-t border-gray-200">
                    <div class="p-4 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($category['items'] as $item)
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg {{ $item['is_diserahkan'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                @if($item['foto_item'])
                                    <img src="{{ asset('storage/' . $item['foto_item']) }}" 
                                         alt="{{ $item['nama_item'] }}" 
                                         class="w-12 h-12 rounded-lg object-cover mr-3">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $item['nama_item'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['satuan'] }}</div>
                                    <div class="flex items-center mt-1">
                                        @if($item['is_diserahkan'])
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Diserahkan
                                            </span>
                                            @if($item['tanggal_diserahkan'])
                                                <span class="ml-2 text-xs text-gray-500">{{ $item['tanggal_diserahkan']->format('d/m/Y') }}</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @endif
                                        <span class="ml-2 text-xs text-gray-500">{{ $item['jumlah_diserahkan'] }}x</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($category['items']->count() === 0)
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-box-open text-3xl mb-2"></i>
                            <p>Tidak ada item dalam kategori ini</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Detail per Karyawan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Detail per Karyawan</h3>
        <p class="text-sm text-gray-500 mt-1">Progress penyerahan untuk setiap karyawan</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diserahkan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Diserahkan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($karyawans as $karyawan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $karyawan->karyawan->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500">{{ $karyawan->karyawan->nik_karyawan }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $karyawan->total_items }} item</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $karyawan->diserahkan_items }} item</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" 
                                     style="width: {{ $karyawan->persentase }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $karyawan->persentase }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $karyawan->status_penyerahan === 'belum_diserahkan' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $karyawan->status_penyerahan === 'sebagian_diserahkan' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $karyawan->status_penyerahan === 'sudah_diserahkan' ? 'bg-green-100 text-green-800' : '' }}">
                            @switch($karyawan->status_penyerahan)
                                @case('belum_diserahkan')
                                    <i class="fas fa-clock mr-1"></i>Belum Diserahkan
                                    @break
                                @case('sebagian_diserahkan')
                                    <i class="fas fa-hourglass-half mr-1"></i>Sebagian
                                    @break
                                @case('sudah_diserahkan')
                                    <i class="fas fa-check mr-1"></i>Selesai
                                    @break
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($karyawan->tanggal_terakhir_diserahkan)
                                {{ $karyawan->tanggal_terakhir_diserahkan->format('d/m/Y H:i') }}
                                <div class="text-xs text-gray-500">
                                    {{ $karyawan->tanggal_terakhir_diserahkan->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-gray-400 italic">Belum ada penyerahan</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('perusahaan.penyerahan-perlengkapan.serahkan-karyawan-page', [$penyerahanRecord->hash_id, $karyawan->karyawan->id]) }}" 
                           class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                            <i class="fas fa-edit mr-1"></i>Kelola
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Action Buttons -->
<div class="mt-6 flex justify-between items-center">
    <div class="text-sm text-gray-500">
        Laporan dibuat pada {{ now()->format('d/m/Y H:i') }}
    </div>
    <div class="flex gap-3">
        <a href="{{ route('perusahaan.penyerahan-perlengkapan.serahkan-item-page', $penyerahanRecord->hash_id) }}" 
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
            <i class="fas fa-hand-holding mr-2"></i>Kelola Penyerahan
        </a>
    </div>
</div>

@push('scripts')
<script>
function toggleCategory(index) {
    const categoryDiv = document.getElementById(`category-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    
    if (categoryDiv.classList.contains('hidden')) {
        categoryDiv.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        categoryDiv.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function printLaporan() {
    // Create print content
    const printContent = `
        <html>
            <head>
                <title>Laporan Penyerahan Perlengkapan</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
                    .header h1 { margin: 0; font-size: 20px; }
                    .header p { margin: 5px 0; color: #666; }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
                    .info-section h3 { margin: 0 0 10px 0; font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
                    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; text-align: center; }
                    .stat-box { padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
                    .stat-number { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                    .stat-label { font-size: 12px; color: #666; }
                    .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 11px; }
                    .table th { background-color: #f5f5f5; font-weight: bold; }
                    .progress-bar { width: 50px; height: 8px; background-color: #e5e5e5; border-radius: 4px; display: inline-block; position: relative; }
                    .progress-fill { height: 100%; background: linear-gradient(to right, #3b82f6, #10b981); border-radius: 4px; }
                    .footer { margin-top: 30px; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #666; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>LAPORAN PENYERAHAN PERLENGKAPAN</h1>
                    <p>{{ auth()->user()->perusahaan->nama }}</p>
                    <p>Project: {{ $penyerahanRecord->project->nama }}</p>
                </div>
                
                <div class="info-grid">
                    <div class="info-section">
                        <h3>Informasi Penyerahan</h3>
                        <p><strong>Tanggal Mulai:</strong> {{ $penyerahanRecord->tanggal_mulai->format('d/m/Y') }}</p>
                        <p><strong>Tanggal Selesai:</strong> {{ $penyerahanRecord->tanggal_selesai->format('d/m/Y') }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($penyerahanRecord->status) }}</p>
                    </div>
                    <div class="info-section">
                        <h3>Statistik</h3>
                        <p><strong>Total Karyawan:</strong> {{ $totalKaryawan }} orang</p>
                        <p><strong>Total Item:</strong> {{ $totalItems }} item</p>
                        <p><strong>Progress:</strong> {{ $overallPersentase }}%</p>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-number">{{ $totalKaryawan }}</div>
                        <div class="stat-label">Total Karyawan</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $totalItems }}</div>
                        <div class="stat-label">Total Item</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $totalDiserahkan }}</div>
                        <div class="stat-label">Item Diserahkan</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">{{ $overallPersentase }}%</div>
                        <div class="stat-label">Progress</div>
                    </div>
                </div>
                
                <h3>Detail per Karyawan</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>NIK</th>
                            <th>Total Item</th>
                            <th>Diserahkan</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Terakhir Diserahkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karyawans as $index => $karyawan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $karyawan->karyawan->nama_lengkap }}</td>
                            <td>{{ $karyawan->karyawan->nik_karyawan }}</td>
                            <td>{{ $karyawan->total_items }}</td>
                            <td>{{ $karyawan->diserahkan_items }}</td>
                            <td>{{ $karyawan->persentase }}%</td>
                            <td>
                                @switch($karyawan->status_penyerahan)
                                    @case('belum_diserahkan')
                                        Belum Diserahkan
                                        @break
                                    @case('sebagian_diserahkan')
                                        Sebagian Diserahkan
                                        @break
                                    @case('sudah_diserahkan')
                                        Sudah Diserahkan
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($karyawan->tanggal_terakhir_diserahkan)
                                    {{ $karyawan->tanggal_terakhir_diserahkan->format('d/m/Y H:i') }}
                                @else
                                    Belum ada
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="footer">
                    <p>Laporan dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </body>
        </html>
    `;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush
@endsection