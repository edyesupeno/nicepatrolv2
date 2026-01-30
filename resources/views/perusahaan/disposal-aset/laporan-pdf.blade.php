<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Disposal Aset</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px 0;
            border-bottom: 2px solid #2563eb;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        
        .info-section {
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 8px 12px;
            vertical-align: top;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 3px;
            font-size: 10px;
        }
        
        .info-value {
            color: #6b7280;
            font-size: 10px;
        }
        
        .stats-container {
            margin-bottom: 25px;
            padding: 20px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .stats-title {
            text-align: center;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 8px;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-card {
            display: table-cell;
            width: 20%;
            background: #ffffff;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        
        .stat-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            line-height: 1.1;
        }
        
        .table-container {
            margin-top: 20px;
            padding: 0 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
            border: 1px solid #d1d5db;
        }
        
        th {
            background: #1e40af;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            line-height: 1.1;
            border-right: 1px solid #1e3a8a;
        }
        
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #f3f4f6;
            vertical-align: top;
            line-height: 1.2;
        }
        
        tr:nth-child(even) {
            background: #f9fafb;
        }
        
        tr:hover {
            background: #f3f4f6;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            line-height: 1;
            display: inline-block;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .jenis-dijual {
            background: #d1fae5;
            color: #065f46;
        }
        
        .jenis-rusak {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .jenis-hilang {
            background: #ede9fe;
            color: #7c3aed;
        }
        
        .jenis-tidak_layak {
            background: #fed7aa;
            color: #ea580c;
        }
        
        .jenis-expired {
            background: #f3f4f6;
            color: #374151;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-mono {
            font-family: 'Courier New', monospace;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .text-green {
            color: #059669;
        }
        
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
            font-style: italic;
            background: #f9fafb;
            border-radius: 6px;
        }
        
        .footer {
            margin-top: 25px;
            padding: 15px 0;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
        }
        
        .footer-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .footer-row {
            display: table-row;
        }
        
        .footer-cell {
            display: table-cell;
            width: 50%;
            padding: 5px 10px;
            vertical-align: top;
        }
        
        .signature-box {
            text-align: center;
            margin-top: 40px;
            padding: 20px 0;
        }
        
        .signature-line {
            border-top: 1px solid #374151;
            margin-top: 50px;
            padding-top: 8px;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DISPOSAL ASET</h1>
        <p>{{ auth()->user()->perusahaan->nama ?? 'PT. Nice Patrol Indonesia' }}</p>
        @if($selectedProject)
            <p>Project: {{ $selectedProject->nama }}</p>
        @endif
        <p>Periode: {{ now()->format('d F Y') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-container">
        <div class="stats-title">RINGKASAN DISPOSAL ASET</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['total']) }}</div>
                    <div class="stat-label">Total<br>Disposal</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['pending']) }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['approved']) }}</div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['completed']) }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">Rp {{ number_format($stats['total_nilai_disposal'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total<br>Nilai</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($disposalAsets->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%">Nomor</th>
                        <th style="width: 20%">Aset</th>
                        <th style="width: 15%">Project</th>
                        <th style="width: 10%">Tanggal</th>
                        <th style="width: 10%">Jenis</th>
                        <th style="width: 13%">Nilai Buku</th>
                        <th style="width: 13%">Nilai Disposal</th>
                        <th style="width: 7%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disposalAsets as $disposal)
                        <tr>
                            <td style="padding: 8px 6px;">
                                <div class="font-mono font-semibold">{{ $disposal->nomor_disposal }}</div>
                                <div style="font-size: 8px; color: #6b7280; margin-top: 2px;">{{ $disposal->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td style="padding: 8px 6px;">
                                <div class="font-semibold">{{ $disposal->asset_name }}</div>
                                <div style="font-size: 8px; color: #6b7280; margin-top: 1px;">{{ $disposal->asset_code }}</div>
                                <div style="font-size: 7px; color: #9ca3af; margin-top: 1px;">{{ ucfirst(str_replace('_', ' ', $disposal->asset_type)) }}</div>
                            </td>
                            <td style="padding: 8px 6px;">{{ $disposal->project->nama ?? '-' }}</td>
                            <td style="padding: 8px 6px;">{{ $disposal->tanggal_disposal->format('d/m/Y') }}</td>
                            <td style="padding: 8px 6px;">
                                <span class="status-badge jenis-{{ $disposal->jenis_disposal }}">
                                    {{ ucfirst($disposal->jenis_disposal) }}
                                </span>
                            </td>
                            <td class="text-right" style="padding: 8px 6px;">
                                Rp {{ number_format($disposal->nilai_buku, 0, ',', '.') }}
                            </td>
                            <td class="text-right" style="padding: 8px 6px;">
                                @if($disposal->nilai_disposal)
                                    <span class="text-green">Rp {{ number_format($disposal->nilai_disposal, 0, ',', '.') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center" style="padding: 8px 6px;">
                                <span class="status-badge status-{{ $disposal->status }}">
                                    {{ ucfirst($disposal->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data disposal aset untuk ditampilkan</p>
            </div>
        @endif
    </div>

    <!-- Summary by Jenis Disposal -->
    @if($disposalAsets->count() > 0)
        <div class="info-section">
            <div class="info-label" style="margin-bottom: 12px; font-size: 11px;">Ringkasan per Jenis Disposal:</div>
            <div class="info-grid">
                @php
                    $jenisStats = $disposalAsets->groupBy('jenis_disposal')->map(function($items, $jenis) {
                        return [
                            'count' => $items->count(),
                            'total_nilai' => $items->sum('nilai_disposal')
                        ];
                    });
                @endphp
                
                <div class="info-row">
                    @php $counter = 0; @endphp
                    @foreach($jenisStats as $jenis => $stat)
                        @if($counter % 2 == 0 && $counter > 0)
                            </div><div class="info-row">
                        @endif
                        <div class="info-cell">
                            <div class="info-item">
                                <div class="info-label">{{ ucfirst($jenis) }}:</div>
                                <div class="info-value">
                                    {{ $stat['count'] }} aset
                                    @if($stat['total_nilai'] > 0)
                                        <br>(Rp {{ number_format($stat['total_nilai'], 0, ',', '.') }})
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php $counter++; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="footer">
        <div class="footer-grid">
            <div class="footer-row">
                <div class="footer-cell">
                    <div class="info-label">Dicetak pada:</div>
                    <div class="info-value">{{ now()->format('d F Y H:i:s') }}</div>
                </div>
                <div class="footer-cell">
                    <div class="info-label">Dicetak oleh:</div>
                    <div class="info-value">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
        
        <div class="signature-box">
            <div class="info-label">Mengetahui,</div>
            <div class="signature-line">
                <div class="info-label">Manager Aset</div>
            </div>
        </div>
    </div>
</body>
</html>