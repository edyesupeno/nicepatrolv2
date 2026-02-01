@extends('perusahaan.layouts.app')

@section('title', 'Laporan Arus Kas')
@section('page-title', 'Laporan Arus Kas')
@section('page-subtitle', 'Pantau aliran kas masuk dan keluar per rekening')

@section('content')
<div class="space-y-6">
    @if($rekenings->isEmpty())
    <!-- Empty State - No Rekening -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-6">
                <i class="fas fa-university text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Rekening</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Anda perlu membuat rekening terlebih dahulu sebelum dapat melihat laporan arus kas. 
                Rekening digunakan untuk mencatat semua transaksi keuangan perusahaan.
            </p>
            <div class="flex justify-center space-x-3">
                <a href="{{ route('perusahaan.keuangan.rekening.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Rekening Pertama
                </a>
                <a href="{{ route('perusahaan.keuangan.rekening.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-university mr-2"></i>
                    Kelola Rekening
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Header Actions -->
    <div class="flex justify-end space-x-3">
        <button type="button" class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="exportPdf()">
            <i class="fas fa-file-pdf mr-2"></i>
            Export PDF
        </button>
        <button type="button" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="exportExcel()">
            <i class="fas fa-file-excel mr-2"></i>
            Export Excel
        </button>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('perusahaan.keuangan.laporan-arus-kas.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Tanggal Mulai
                    </label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="start_date" value="{{ $startDate }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Tanggal Selesai
                    </label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="end_date" value="{{ $endDate }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-blue-500 mr-2"></i>
                        Project
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="project_id">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-university text-blue-500 mr-2"></i>
                        Rekening
                    </label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="rekening_id">
                        <option value="">Semua Rekening</option>
                        @foreach($rekenings as $rekening)
                            <option value="{{ $rekening->id }}" {{ $rekeningId == $rekening->id ? 'selected' : '' }}>
                                {{ $rekening->nama_rekening }} - {{ $rekening->project->nama ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex space-x-3 mt-6">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('perusahaan.keuangan.laporan-arus-kas.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-undo mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Debit (Masuk)</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_debit'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Kredit (Keluar)</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full {{ $stats['net_cash_flow'] >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                    <i class="fas fa-{{ $stats['net_cash_flow'] >= 0 ? 'chart-line' : 'chart-line-down' }} {{ $stats['net_cash_flow'] >= 0 ? 'text-green-600' : 'text-red-600' }} text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Net Cash Flow</p>
                    <p class="text-2xl font-bold {{ $stats['net_cash_flow'] >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($stats['net_cash_flow'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_transaksi']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Rekening Cards -->
    @if($saldoRekenings->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-university mr-2 text-blue-500"></i>
                Saldo Rekening Saat Ini
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($saldoRekenings as $rekening)
                <div class="bg-white rounded-xl border-l-4 shadow-sm p-4" style="border-left-color: {{ $rekening->warna_card }};">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $rekening->nama_rekening }}</h4>
                            <p class="text-sm text-gray-500">{{ $rekening->project->nama ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold {{ $rekening->saldo_saat_ini >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $rekening->mata_uang }} {{ number_format($rekening->saldo_saat_ini, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Transaksi Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list mr-2 text-blue-500"></i>
                Riwayat Transaksi
            </h3>
            <div class="text-sm text-gray-500">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>
        <div class="p-6">
            @if($transaksis->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekening</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kredit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaksis as $transaksi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d/m/Y') : '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $transaksi->created_at ? $transaksi->created_at->format('H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $transaksi->nomor_transaksi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($transaksi->rekening)
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $transaksi->rekening->warna_card }};"></div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $transaksi->rekening->nama_rekening }}</div>
                                            <div class="text-sm text-gray-500">{{ $transaksi->rekening->project->nama ?? $transaksi->rekening->nomor_rekening ?? 'N/A' }}</div>
                                        </div>
                                    @else
                                        <div class="w-3 h-3 rounded-full mr-2 bg-gray-400"></div>
                                        <div>
                                            <div class="font-medium text-red-600">Rekening Dihapus</div>
                                            <div class="text-sm text-gray-500">ID: {{ $transaksi->rekening_id }}</div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $transaksi->kategori_transaksi_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $transaksi->keterangan }}">
                                    {{ $transaksi->keterangan }}
                                </div>
                                @if($transaksi->referensi)
                                <div class="text-xs text-gray-500">Ref: {{ $transaksi->referensi }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($transaksi->jenis_transaksi === 'debit')
                                <span class="font-medium text-green-600">
                                    +{{ $transaksi->formatted_jumlah }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($transaksi->jenis_transaksi === 'kredit')
                                <span class="font-medium text-red-600">
                                    -{{ $transaksi->formatted_jumlah }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="font-medium {{ $transaksi->saldo_sesudah >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaksi->formatted_saldo_sesudah }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaksi->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Verified
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.show', $transaksi->hash_id) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-lg text-sm text-blue-700 hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $transaksis->firstItem() ?? 0 }} - {{ $transaksis->lastItem() ?? 0 }} 
                    dari {{ $transaksis->total() }} transaksi
                </div>
                <div>
                    {{ $transaksis->appends(request()->query())->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-chart-line text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada transaksi</h3>
                <p class="text-gray-500 mb-6">Belum ada transaksi pada periode yang dipilih</p>
                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Transaksi
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
function exportPdf() {
    const form = document.getElementById('filterForm');
    const url = new URL('{{ route("perusahaan.keuangan.laporan-arus-kas.export-pdf") }}');
    
    // Add current filter parameters
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        if (value) {
            url.searchParams.append(key, value);
        }
    }
    
    window.open(url.toString(), '_blank');
}

function exportExcel() {
    const form = document.getElementById('filterForm');
    const url = new URL('{{ route("perusahaan.keuangan.laporan-arus-kas.export-excel") }}');
    
    // Add current filter parameters
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        if (value) {
            url.searchParams.append(key, value);
        }
    }
    
    window.open(url.toString(), '_blank');
}
</script>
@endsection