<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Reimbursement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .stats-section {
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            font-size: 11px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background-color: #f8f9fa; color: #6c757d; }
        .status-submitted { background-color: #cce5ff; color: #0066cc; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-paid { background-color: #e2e3ff; color: #383d41; }
        
        .urgent-badge {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN REIMBURSEMENT</h1>
        <h2>{{ auth()->user()->perusahaan->nama ?? 'Perusahaan' }}</h2>
    </div>

    <!-- Report Info -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span>{{ now()->format('d F Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dicetak oleh:</span>
            <span>{{ auth()->user()->name }}</span>
        </div>
        @if($project)
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span>{{ $project->nama }}</span>
        </div>
        @endif
        @if(isset($validated['status']) && $validated['status'])
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span>{{ \App\Models\Reimbursement::getAvailableStatus()[$validated['status']] }}</span>
        </div>
        @endif
        @if(isset($validated['kategori']) && $validated['kategori'])
        <div class="info-row">
            <span class="info-label">Kategori:</span>
            <span>{{ \App\Models\Reimbursement::getAvailableKategori()[$validated['kategori']] }}</span>
        </div>
        @endif
        @if(isset($validated['start_date']) && isset($validated['end_date']) && $validated['start_date'] && $validated['end_date'])
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span>{{ \Carbon\Carbon::parse($validated['start_date'])->format('d F Y') }} - {{ \Carbon\Carbon::parse($validated['end_date'])->format('d F Y') }}</span>
        </div>
        @endif
    </div>

    <!-- Statistics -->
    <div class="stats-section">
        <h3 style="margin-bottom: 15px;">Ringkasan Statistik</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['total_pengajuan']) }}</div>
                <div class="stat-label">Total Pengajuan</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($stats['approved']) }}</div>
                <div class="stat-label">Disetujui</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rp {{ number_format($stats['total_amount_pengajuan'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pengajuan</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rp {{ number_format($stats['total_amount_disetujui'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Disetujui</div>
            </div>
        </div>
        
        <!-- Status Breakdown -->
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Draft</td>
                    <td class="text-center">{{ number_format($stats['draft']) }}</td>
                    <td class="text-center">{{ $stats['total_pengajuan'] > 0 ? number_format(($stats['draft'] / $stats['total_pengajuan']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Diajukan</td>
                    <td class="text-center">{{ number_format($stats['submitted']) }}</td>
                    <td class="text-center">{{ $stats['total_pengajuan'] > 0 ? number_format(($stats['submitted'] / $stats['total_pengajuan']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Disetujui</td>
                    <td class="text-center">{{ number_format($stats['approved']) }}</td>
                    <td class="text-center">{{ $stats['total_pengajuan'] > 0 ? number_format(($stats['approved'] / $stats['total_pengajuan']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Ditolak</td>
                    <td class="text-center">{{ number_format($stats['rejected']) }}</td>
                    <td class="text-center">{{ $stats['total_pengajuan'] > 0 ? number_format(($stats['rejected'] / $stats['total_pengajuan']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Dibayar</td>
                    <td class="text-center">{{ number_format($stats['paid']) }}</td>
                    <td class="text-center">{{ $stats['total_pengajuan'] > 0 ? number_format(($stats['paid'] / $stats['total_pengajuan']) * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Detail Data -->
    @if($reimbursements->count() > 0)
    <div class="page-break">
        <h3 style="margin-bottom: 15px;">Detail Reimbursement</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Nomor</th>
                        <th style="width: 15%;">Karyawan</th>
                        <th style="width: 12%;">Project</th>
                        <th style="width: 20%;">Judul</th>
                        <th style="width: 10%;">Kategori</th>
                        <th style="width: 12%;">Jumlah</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 9%;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reimbursements as $reimbursement)
                    <tr>
                        <td>
                            {{ $reimbursement->nomor_reimbursement }}
                            @if($reimbursement->is_urgent)
                                <br><span class="urgent-badge">URGENT</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $reimbursement->karyawan->nama_lengkap ?? 'N/A' }}</strong>
                            @if($reimbursement->karyawan && $reimbursement->karyawan->nik_karyawan)
                                <br><small>{{ $reimbursement->karyawan->nik_karyawan }}</small>
                            @endif
                        </td>
                        <td>{{ $reimbursement->project->nama ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ Str::limit($reimbursement->judul_pengajuan, 40) }}</strong>
                            @if($reimbursement->deskripsi)
                                <br><small>{{ Str::limit($reimbursement->deskripsi, 60) }}</small>
                            @endif
                        </td>
                        <td>{{ $reimbursement->kategori_label }}</td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($reimbursement->jumlah_pengajuan, 0, ',', '.') }}</strong>
                            @if($reimbursement->jumlah_disetujui && $reimbursement->jumlah_disetujui != $reimbursement->jumlah_pengajuan)
                                <br><small style="color: #28a745;">Disetujui: Rp {{ number_format($reimbursement->jumlah_disetujui, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $reimbursement->status }}">
                                {{ $reimbursement->status_label }}
                            </span>
                        </td>
                        <td class="text-center">{{ $reimbursement->tanggal_pengajuan->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem Nice Patrol pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Total {{ number_format($reimbursements->count()) }} reimbursement ditampilkan dalam laporan ini</p>
    </div>
</body>
</html>