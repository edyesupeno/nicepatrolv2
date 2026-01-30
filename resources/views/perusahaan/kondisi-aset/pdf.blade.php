<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kondisi Aset</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20mm 15mm 20mm 15mm;
        }
        
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: A4 portrait;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .header .info {
            font-size: 11px;
            color: #888;
        }
        
        .summary {
            margin-bottom: 20px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        
        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .stats-item {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .stats-table th,
        .stats-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .stats-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        .stats-table td {
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-ada { background-color: #dcfce7; color: #16a34a; }
        .status-rusak { background-color: #fee2e2; color: #dc2626; }
        .status-dijual { background-color: #dbeafe; color: #2563eb; }
        .status-dihapus { background-color: #f3f4f6; color: #6b7280; }
        .status-aktif { background-color: #dcfce7; color: #16a34a; }
        .status-maintenance { background-color: #fef3c7; color: #d97706; }
        .status-hilang { background-color: #f3f4f6; color: #6b7280; }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            clear: both;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        
        /* Print optimizations */
        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN KONDISI ASET</h1>
        <h2>{{ auth()->user()->perusahaan->nama ?? 'Perusahaan' }}</h2>
        <div class="info">
            Dicetak pada: {{ now()->format('d F Y H:i') }} WIB
            @if($selectedProject)
                <br>Project: {{ $selectedProject->nama }}
            @else
                <br>Semua Project
            @endif
        </div>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Data Aset</div>
                <div class="value">{{ number_format($dataAsetStats['total']) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Kendaraan</div>
                <div class="value">{{ number_format($asetKendaraanStats['total']) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Nilai Data Aset</div>
                <div class="value">Rp {{ number_format($dataAsetValue['total_value'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Nilai Kendaraan</div>
                <div class="value">Rp {{ number_format($asetKendaraanValue['total_value'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Data Aset & Kendaraan Statistics -->
    <div class="section">
        <div class="stats-grid">
            <!-- Data Aset Statistics -->
            <div class="stats-item">
                <div class="section-title">Statistik Data Aset</div>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="status-badge status-ada">Ada</span>
                            </td>
                            <td class="text-center">{{ $dataAsetStats['ada'] }}</td>
                            <td class="text-center">{{ $dataAsetStats['total'] > 0 ? number_format(($dataAsetStats['ada'] / $dataAsetStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-rusak">Rusak</span>
                            </td>
                            <td class="text-center">{{ $dataAsetStats['rusak'] }}</td>
                            <td class="text-center">{{ $dataAsetStats['total'] > 0 ? number_format(($dataAsetStats['rusak'] / $dataAsetStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-dijual">Dijual</span>
                            </td>
                            <td class="text-center">{{ $dataAsetStats['dijual'] }}</td>
                            <td class="text-center">{{ $dataAsetStats['total'] > 0 ? number_format(($dataAsetStats['dijual'] / $dataAsetStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-dihapus">Dihapus</span>
                            </td>
                            <td class="text-center">{{ $dataAsetStats['dihapus'] }}</td>
                            <td class="text-center">{{ $dataAsetStats['total'] > 0 ? number_format(($dataAsetStats['dihapus'] / $dataAsetStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f5f5f5;">
                            <td><strong>Total</strong></td>
                            <td class="text-center"><strong>{{ $dataAsetStats['total'] }}</strong></td>
                            <td class="text-center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Aset Kendaraan Statistics -->
            <div class="stats-item">
                <div class="section-title">Statistik Aset Kendaraan</div>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="status-badge status-aktif">Aktif</span>
                            </td>
                            <td class="text-center">{{ $asetKendaraanStats['aktif'] }}</td>
                            <td class="text-center">{{ $asetKendaraanStats['total'] > 0 ? number_format(($asetKendaraanStats['aktif'] / $asetKendaraanStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-maintenance">Maintenance</span>
                            </td>
                            <td class="text-center">{{ $asetKendaraanStats['maintenance'] }}</td>
                            <td class="text-center">{{ $asetKendaraanStats['total'] > 0 ? number_format(($asetKendaraanStats['maintenance'] / $asetKendaraanStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-rusak">Rusak</span>
                            </td>
                            <td class="text-center">{{ $asetKendaraanStats['rusak'] }}</td>
                            <td class="text-center">{{ $asetKendaraanStats['total'] > 0 ? number_format(($asetKendaraanStats['rusak'] / $asetKendaraanStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-dijual">Dijual</span>
                            </td>
                            <td class="text-center">{{ $asetKendaraanStats['dijual'] }}</td>
                            <td class="text-center">{{ $asetKendaraanStats['total'] > 0 ? number_format(($asetKendaraanStats['dijual'] / $asetKendaraanStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="status-badge status-hilang">Hilang</span>
                            </td>
                            <td class="text-center">{{ $asetKendaraanStats['hilang'] }}</td>
                            <td class="text-center">{{ $asetKendaraanStats['total'] > 0 ? number_format(($asetKendaraanStats['hilang'] / $asetKendaraanStats['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f5f5f5;">
                            <td><strong>Total</strong></td>
                            <td class="text-center"><strong>{{ $asetKendaraanStats['total'] }}</strong></td>
                            <td class="text-center"><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Category & Type Distribution -->
    <div class="section">
        <div class="stats-grid">
            <!-- Data Aset by Category -->
            <div class="stats-item">
                <div class="section-title">Data Aset per Kategori</div>
                @if($dataAsetByCategory->count() > 0)
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataAsetByCategory as $item)
                            <tr>
                                <td>{{ $item->kategori }}</td>
                                <td class="text-center">{{ $item->total }}</td>
                                <td class="text-center">{{ $dataAsetStats['total'] > 0 ? number_format(($item->total / $dataAsetStats['total']) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">Tidak ada data kategori</div>
                @endif
            </div>

            <!-- Aset Kendaraan by Type -->
            <div class="stats-item">
                <div class="section-title">Kendaraan per Jenis</div>
                @if($asetKendaraanByType->count() > 0)
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Jenis Kendaraan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asetKendaraanByType as $item)
                            <tr>
                                <td>{{ ucfirst($item->jenis_kendaraan) }}</td>
                                <td class="text-center">{{ $item->total }}</td>
                                <td class="text-center">{{ $asetKendaraanStats['total'] > 0 ? number_format(($item->total / $asetKendaraanStats['total']) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">Tidak ada data jenis kendaraan</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Value Analysis -->
    <div class="section">
        <div class="section-title">Analisis Nilai Aset</div>
        <div class="stats-grid">
            <div class="stats-item">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Data Aset</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Nilai</td>
                            <td class="text-right">Rp {{ number_format($dataAsetValue['total_value'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Rata-rata Nilai</td>
                            <td class="text-right">Rp {{ number_format($dataAsetValue['avg_value'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="stats-item">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Aset Kendaraan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Nilai</td>
                            <td class="text-right">Rp {{ number_format($asetKendaraanValue['total_value'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Rata-rata Nilai</td>
                            <td class="text-right">Rp {{ number_format($asetKendaraanValue['avg_value'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>{{ auth()->user()->perusahaan->nama ?? 'Perusahaan' }}</strong><br>
            Laporan Kondisi Aset
        </div>
        <div style="float: right;">
            Halaman 1 dari 1<br>
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>