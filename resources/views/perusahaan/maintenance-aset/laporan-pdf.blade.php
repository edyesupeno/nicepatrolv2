<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Maintenance & Servis</title>
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
            margin: 20mm 15mm 20mm 15mm; /* top right bottom left */
        }
        
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: A4 landscape;
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
            width: 20%;
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
        
        .table-container {
            margin-bottom: 20px;
        }
        
        .table-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        td {
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
        
        .status-scheduled {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-in-progress {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status-completed {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .type-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .type-preventive {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .type-corrective {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .type-predictive {
            background-color: #f3e8ff;
            color: #9333ea;
        }
        
        .breakdown {
            margin-top: 20px;
        }
        
        .breakdown-grid {
            display: table;
            width: 100%;
        }
        
        .breakdown-item {
            display: table-cell;
            width: 50%;
            padding: 0 8px;
        }
        
        .breakdown-table {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .breakdown-table th {
            background-color: #f9f9f9;
            font-size: 11px;
        }
        
        .breakdown-table td {
            font-size: 11px;
        }
        
        .footer {
            margin-top: 25px;
            padding-top: 12px;
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
        
        .page-break {
            page-break-before: always;
        }
        
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
            
            .footer {
                position: relative;
                page-break-inside: avoid;
            }
        }
        
        /* Ensure proper spacing for content */
        .content-wrapper {
            min-height: calc(100vh - 80mm); /* Account for margins and footer */
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>LAPORAN MAINTENANCE & SERVIS ASET</h1>
            <h2>{{ auth()->user()->perusahaan->nama ?? 'Perusahaan' }}</h2>
            <div class="info">
                Dicetak pada: {{ now()->format('d F Y H:i') }} WIB
                @if(request('tanggal_dari') || request('tanggal_sampai'))
                    <br>
                    Periode: 
                    @if(request('tanggal_dari'))
                        {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d F Y') }}
                    @else
                        Awal
                    @endif
                    s/d
                    @if(request('tanggal_sampai'))
                        {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d F Y') }}
                    @else
                        Sekarang
                    @endif
                @endif
            </div>
        </div>

    @if($maintenances->count() > 0)
        <!-- Summary -->
        @php
            $totalBiaya = $maintenances->sum('total_biaya');
            $totalCompleted = $maintenances->where('status', 'completed')->count();
            $totalScheduled = $maintenances->where('status', 'scheduled')->count();
            $totalInProgress = $maintenances->where('status', 'in_progress')->count();
            $avgBiaya = $maintenances->count() > 0 ? $totalBiaya / $maintenances->count() : 0;
        @endphp

        <div class="summary">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Total Maintenance</div>
                    <div class="value">{{ $maintenances->count() }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Selesai</div>
                    <div class="value">{{ $totalCompleted }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Terjadwal</div>
                    <div class="value">{{ $totalScheduled }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Biaya</div>
                    <div class="value">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Rata-rata Biaya</div>
                    <div class="value">Rp {{ number_format($avgBiaya, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="table-container">
            <div class="table-title">Detail Maintenance</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="18%">Nomor Maintenance</th>
                        <th width="12%">Project</th>
                        <th width="18%">Aset</th>
                        <th width="10%">Tanggal</th>
                        <th width="10%">Jenis</th>
                        <th width="8%">Status</th>
                        <th width="12%">Teknisi</th>
                        <th width="8%">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenances as $index => $maintenance)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $maintenance->nomor_maintenance }}</strong>
                        </td>
                        <td>
                            <strong>{{ $maintenance->project->nama ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <strong>{{ Str::limit($maintenance->asset_name, 30) }}</strong><br>
                            <small>{{ ucfirst(str_replace('_', ' ', $maintenance->asset_type)) }}</small>
                        </td>
                        <td class="text-center">
                            {{ $maintenance->tanggal_maintenance->format('d/m/Y') }}
                            @if($maintenance->waktu_mulai)
                                <br><small>{{ $maintenance->waktu_mulai }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="type-badge type-{{ $maintenance->jenis_maintenance }}">
                                {{ ucfirst($maintenance->jenis_maintenance) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $maintenance->status }}">
                                @switch($maintenance->status)
                                    @case('scheduled') Terjadwal @break
                                    @case('in_progress') Dikerjakan @break
                                    @case('completed') Selesai @break
                                    @case('cancelled') Dibatalkan @break
                                    @default {{ ucfirst($maintenance->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            {{ $maintenance->teknisi_internal ?? $maintenance->vendor_eksternal ?? 'Belum ditentukan' }}
                        </td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($maintenance->total_biaya, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f5f5f5;">
                        <td colspan="8" class="text-right"><strong>Total Biaya:</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Cost Breakdown -->
        <div class="breakdown">
            <div class="breakdown-grid">
                <!-- Cost by Type -->
                <div class="breakdown-item">
                    <div class="table-title">Biaya per Jenis Maintenance</div>
                    @php
                        $costByType = $maintenances->groupBy('jenis_maintenance')->map(function($items) {
                            return $items->sum('total_biaya');
                        });
                    @endphp
                    
                    <table class="breakdown-table">
                        <thead>
                            <tr>
                                <th>Jenis Maintenance</th>
                                <th class="text-right">Biaya</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($costByType as $type => $cost)
                            <tr>
                                <td>{{ ucfirst($type) }}</td>
                                <td class="text-right">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $totalBiaya > 0 ? number_format(($cost / $totalBiaya) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Cost by Status -->
                <div class="breakdown-item">
                    <div class="table-title">Biaya per Status</div>
                    @php
                        $costByStatus = $maintenances->groupBy('status')->map(function($items) {
                            return $items->sum('total_biaya');
                        });
                    @endphp
                    
                    <table class="breakdown-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-right">Biaya</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($costByStatus as $status => $cost)
                            <tr>
                                <td>
                                    @switch($status)
                                        @case('scheduled') Terjadwal @break
                                        @case('in_progress') Sedang Dikerjakan @break
                                        @case('completed') Selesai @break
                                        @case('cancelled') Dibatalkan @break
                                        @default {{ ucfirst($status) }}
                                    @endswitch
                                </td>
                                <td class="text-right">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $totalBiaya > 0 ? number_format(($cost / $totalBiaya) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown by Asset Type -->
        <div class="breakdown">
            <div class="table-title">Rincian Biaya per Komponen</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="20%">Nomor Maintenance</th>
                        <th width="12%">Biaya Sparepart</th>
                        <th width="12%">Biaya Jasa</th>
                        <th width="12%">Biaya Lainnya</th>
                        <th width="12%">Total Biaya</th>
                        <th width="28%">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenances->where('status', 'completed') as $index => $maintenance)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $maintenance->nomor_maintenance }}</td>
                        <td class="text-right">Rp {{ number_format($maintenance->biaya_sparepart, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($maintenance->biaya_jasa, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($maintenance->biaya_lainnya, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($maintenance->total_biaya, 0, ',', '.') }}</strong></td>
                        <td class="text-center">
                            @if($maintenance->hasil_maintenance)
                                <span class="status-badge {{ $maintenance->hasil_maintenance == 'berhasil' ? 'status-completed' : ($maintenance->hasil_maintenance == 'sebagian' ? 'status-in-progress' : 'status-cancelled') }}">
                                    {{ ucfirst($maintenance->hasil_maintenance) }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f5f5f5;">
                        <td colspan="2" class="text-right"><strong>Total:</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($maintenances->where('status', 'completed')->sum('biaya_sparepart'), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($maintenances->where('status', 'completed')->sum('biaya_jasa'), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($maintenances->where('status', 'completed')->sum('biaya_lainnya'), 0, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($maintenances->where('status', 'completed')->sum('total_biaya'), 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @else
        <div class="content-wrapper">
            <div class="no-data">
                <h3>Tidak ada data maintenance</h3>
                <p>Tidak ada data maintenance yang sesuai dengan filter yang dipilih.</p>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="float: left;">
            <strong>{{ auth()->user()->perusahaan->nama ?? 'Perusahaan' }}</strong><br>
            Laporan Maintenance & Servis Aset
        </div>
        <div style="float: right;">
            Halaman 1 dari 1<br>
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>