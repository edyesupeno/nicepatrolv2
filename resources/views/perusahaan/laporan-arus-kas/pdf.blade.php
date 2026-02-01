<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Kas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4a5568;
        }
        
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: bold;
            color: #2d3748;
        }
        
        .header .subtitle {
            font-size: 12px;
            margin: 5px 0;
            color: #4a5568;
        }
        
        .header .print-info {
            font-size: 9px;
            margin: 5px 0 0 0;
            color: #718096;
            font-style: italic;
        }
        
        /* Filter Info */
        .filter-info {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .filter-info h3 {
            margin: 0 0 8px 0;
            color: #2d3748;
            font-size: 11px;
            font-weight: bold;
        }
        
        .filter-row {
            margin-bottom: 3px;
            font-size: 9px;
        }
        
        .filter-label {
            font-weight: bold;
            color: #4a5568;
        }
        
        /* Statistics - Simple Table Layout */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .stats-table td {
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background: #f8f9fa;
        }
        
        .stats-table .stat-label {
            font-size: 8px;
            color: #718096;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .stats-table .stat-value {
            font-size: 11px;
            font-weight: bold;
            color: #2d3748;
        }
        
        .stats-table .stat-positive {
            color: #38a169;
        }
        
        .stats-table .stat-negative {
            color: #e53e3e;
        }
        
        /* Section Headers */
        .section-header {
            background: #f1f5f9;
            padding: 8px 10px;
            margin: 15px 0 8px 0;
            border-left: 3px solid #4a5568;
        }
        
        .section-header h3 {
            margin: 0;
            color: #2d3748;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Saldo Table */
        .saldo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .saldo-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        
        .saldo-table .saldo-name {
            font-weight: bold;
            color: #2d3748;
            width: 70%;
        }
        
        .saldo-table .saldo-amount {
            font-weight: bold;
            text-align: right;
            width: 30%;
        }
        
        .saldo-positive {
            color: #38a169;
        }
        
        .saldo-negative {
            color: #e53e3e;
        }
        
        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .data-table th {
            background: #4a5568;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #2d3748;
        }
        
        .data-table td {
            padding: 4px;
            border: 1px solid #e2e8f0;
            font-size: 8px;
            vertical-align: top;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        /* Simple Badges */
        .badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .amount-positive {
            color: #38a169;
            font-weight: bold;
        }
        
        .amount-negative {
            color: #e53e3e;
            font-weight: bold;
        }
        
        .amount-neutral {
            color: #718096;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 8px;
        }
        
        .footer .company-info {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 2px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-style: italic;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN ARUS KAS</h1>
        <p class="subtitle">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        <p class="print-info">Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
    </div>

    <!-- Filter Info -->
    @if($rekeningId || $projectId)
    <div class="filter-info">
        <h3>Filter Laporan</h3>
        @if($rekeningId)
            <div class="filter-row">
                <span class="filter-label">Rekening:</span>
                <span>{{ $transaksis->first()->rekening->nama_rekening ?? 'Semua Rekening' }}</span>
            </div>
        @endif
        @if($projectId)
            <div class="filter-row">
                <span class="filter-label">Project:</span>
                <span>{{ $projects->where('id', $projectId)->first()->nama ?? 'Semua Project' }}</span>
            </div>
        @endif
    </div>
    @endif

    <!-- Statistics -->
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-label">Total Debit</div>
                <div class="stat-value stat-positive">Rp {{ number_format($stats['total_debit'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="stat-label">Total Kredit</div>
                <div class="stat-value stat-negative">Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="stat-label">Net Cash Flow</div>
                <div class="stat-value {{ $stats['net_cash_flow'] >= 0 ? 'stat-positive' : 'stat-negative' }}">
                    Rp {{ number_format($stats['net_cash_flow'], 0, ',', '.') }}
                </div>
            </td>
            <td>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">{{ number_format($stats['total_transaksi'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Saldo Rekening -->
    @if($saldoRekenings->isNotEmpty())
    <div class="section-header">
        <h3>Saldo Rekening Saat Ini</h3>
    </div>
    <table class="saldo-table">
        @foreach($saldoRekenings as $rekening)
        <tr>
            <td class="saldo-name">{{ $rekening->nama_rekening }}</td>
            <td class="saldo-amount {{ $rekening->saldo_saat_ini >= 0 ? 'saldo-positive' : 'saldo-negative' }}">
                Rp {{ number_format($rekening->saldo_saat_ini, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- Transaction Table -->
    <div class="section-header">
        <h3>Detail Transaksi</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="12%">No. Transaksi</th>
                <th width="15%">Rekening</th>
                <th width="8%">Jenis</th>
                <th width="12%">Kategori</th>
                <th width="12%">Jumlah</th>
                <th width="31%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $transaksi)
            <tr>
                <td>
                    <div style="font-weight: bold;">
                        {{ $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d/m/Y') : '-' }}
                    </div>
                    <div style="font-size: 7px; color: #666;">
                        {{ $transaksi->created_at ? $transaksi->created_at->format('H:i') : '-' }}
                    </div>
                </td>
                <td>
                    <span class="badge badge-secondary">{{ $transaksi->nomor_transaksi }}</span>
                </td>
                <td>
                    <div style="font-weight: bold;">
                        {{ $transaksi->rekening->nama_rekening ?? 'N/A' }}
                    </div>
                    @if($transaksi->rekening && $transaksi->rekening->project)
                    <div style="font-size: 7px; color: #666;">
                        {{ $transaksi->rekening->project->nama }}
                    </div>
                    @endif
                </td>
                <td class="text-center">
                    @if($transaksi->jenis_transaksi === 'debit')
                    <span class="badge badge-success">Debit</span>
                    @else
                    <span class="badge badge-danger">Kredit</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">{{ $transaksi->kategori_transaksi_label }}</span>
                </td>
                <td class="text-right">
                    <span class="{{ $transaksi->jenis_transaksi === 'debit' ? 'amount-positive' : 'amount-negative' }}">
                        {{ $transaksi->jenis_transaksi === 'debit' ? '+' : '-' }}Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                    </span>
                </td>
                <td>
                    <div>{{ Str::limit($transaksi->keterangan, 40) }}</div>
                    @if($transaksi->referensi)
                    <div style="font-size: 7px; color: #666; margin-top: 1px;">
                        Ref: {{ $transaksi->referensi }}
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="empty-state">
                    <div>
                        <strong>Tidak ada transaksi dalam periode ini</strong>
                        <br>
                        <small>Silakan pilih periode yang berbeda atau tambah transaksi baru</small>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary by Category -->
    @if($transaksiPerKategori->isNotEmpty())
    <div class="section-header">
        <h3>Ringkasan per Kategori</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="30%">Kategori</th>
                <th width="15%" class="text-center">Jenis</th>
                <th width="15%" class="text-center">Jumlah Transaksi</th>
                <th width="20%" class="text-right">Total Amount</th>
                <th width="20%" class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = $stats['total_debit'] + $stats['total_kredit'];
            @endphp
            @foreach($transaksiPerKategori as $kategori)
            <tr>
                <td>
                    <div style="font-weight: bold;">
                        {{ $kategori->kategori_transaksi_label }}
                    </div>
                </td>
                <td class="text-center">
                    @if($kategori->jenis_transaksi === 'debit')
                    <span class="badge badge-success">Debit</span>
                    @else
                    <span class="badge badge-danger">Kredit</span>
                    @endif
                </td>
                <td class="text-center">
                    <span style="font-weight: bold;">{{ number_format($kategori->jumlah_transaksi, 0, ',', '.') }}</span>
                </td>
                <td class="text-right">
                    <span class="{{ $kategori->jenis_transaksi === 'debit' ? 'amount-positive' : 'amount-negative' }}">
                        Rp {{ number_format($kategori->total, 0, ',', '.') }}
                    </span>
                </td>
                <td class="text-right">
                    @if($totalAmount > 0)
                    <span class="amount-neutral">
                        {{ number_format(($kategori->total / $totalAmount) * 100, 1) }}%
                    </span>
                    @else
                    <span class="amount-neutral">0%</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="company-info">{{ config('app.name', 'Nice Patrol System') }}</div>
        <div>Laporan ini digenerate secara otomatis pada {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</div>
        <div>© {{ \Carbon\Carbon::now()->format('Y') }} - Semua hak cipta dilindungi</div>
    </div>
</body>
</html>