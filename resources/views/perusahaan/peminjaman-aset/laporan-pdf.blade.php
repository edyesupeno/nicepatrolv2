<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Aset</title>
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
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 11px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .report-date {
            font-size: 11px;
            color: #666;
        }
        
        .filter-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .filter-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .filter-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .filter-label {
            width: 120px;
            font-weight: bold;
            color: #6c757d;
        }
        
        .filter-value {
            color: #495057;
        }
        
        .summary {
            background-color: #e3f2fd;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #bbdefb;
            border-radius: 5px;
        }
        
        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #1976d2;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #cce5ff; color: #004085; }
        .status-dipinjam { background-color: #d4edda; color: #155724; }
        .status-dikembalikan { background-color: #e2e3e5; color: #383d41; }
        .status-terlambat { background-color: #f8d7da; color: #721c24; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-nowrap { white-space: nowrap; }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $perusahaan->nama }}</div>
        @if($perusahaan->alamat || $perusahaan->telepon || $perusahaan->email)
        <div class="company-info">
            @if($perusahaan->alamat){{ $perusahaan->alamat }}@endif
            @if($perusahaan->telepon) | Tel: {{ $perusahaan->telepon }}@endif
            @if($perusahaan->email) | Email: {{ $perusahaan->email }}@endif
        </div>
        @endif
        <div class="report-title">LAPORAN PEMINJAMAN ASET</div>
        <div class="report-date">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <!-- Filter Information -->
    <div class="filter-info">
        <div class="filter-title">Filter Laporan:</div>
        <div class="filter-row">
            <div class="filter-label">Project:</div>
            <div class="filter-value">{{ $filterInfo['project'] }}</div>
        </div>
        <div class="filter-row">
            <div class="filter-label">Status:</div>
            <div class="filter-value">{{ $filterInfo['status'] }}</div>
        </div>
        <div class="filter-row">
            <div class="filter-label">Tipe Aset:</div>
            <div class="filter-value">{{ $filterInfo['aset_type'] }}</div>
        </div>
        @if($filterInfo['tanggal_dari'] || $filterInfo['tanggal_sampai'])
        <div class="filter-row">
            <div class="filter-label">Periode:</div>
            <div class="filter-value">
                @if($filterInfo['tanggal_dari']){{ \Carbon\Carbon::parse($filterInfo['tanggal_dari'])->format('d/m/Y') }}@else-@endif
                s/d 
                @if($filterInfo['tanggal_sampai']){{ \Carbon\Carbon::parse($filterInfo['tanggal_sampai'])->format('d/m/Y') }}@else-@endif
            </div>
        </div>
        @endif
        @if($filterInfo['search'])
        <div class="filter-row">
            <div class="filter-label">Pencarian:</div>
            <div class="filter-value">{{ $filterInfo['search'] }}</div>
        </div>
        @endif
        <div class="filter-row">
            <div class="filter-label">Hanya Terlambat:</div>
            <div class="filter-value">{{ $filterInfo['terlambat'] }}</div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-title">Ringkasan Data:</div>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $peminjamans->count() }}</div>
                <div class="stat-label">Total Peminjaman</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $peminjamans->where('status_peminjaman', 'pending')->count() }}</div>
                <div class="stat-label">Menunggu Persetujuan</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $peminjamans->where('status_peminjaman', 'dipinjam')->count() }}</div>
                <div class="stat-label">Sedang Dipinjam</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $peminjamans->where('status_peminjaman', 'dikembalikan')->count() }}</div>
                <div class="stat-label">Sudah Dikembalikan</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $peminjamans->filter(function($p) { return $p->is_terlambat; })->count() }}</div>
                <div class="stat-label">Terlambat</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    @if($peminjamans->count() > 0)
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Kode</th>
                <th width="10%">Project</th>
                <th width="12%">Aset</th>
                <th width="12%">Peminjam</th>
                <th width="8%">Tgl Pinjam</th>
                <th width="8%">Tgl Kembali</th>
                <th width="5%">Jumlah</th>
                <th width="8%">Status</th>
                <th width="8%">Kondisi</th>
                <th width="18%">Keperluan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjamans as $index => $peminjaman)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-nowrap">{{ $peminjaman->kode_peminjaman }}</td>
                <td>{{ $peminjaman->project->nama ?? '-' }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $peminjaman->aset_kode }}</div>
                    <div style="font-size: 9px; color: #666;">{{ $peminjaman->aset_nama }}</div>
                    <div style="font-size: 8px; color: #999;">{{ $peminjaman->aset_kategori }}</div>
                </td>
                <td>
                    <div style="font-weight: bold;">{{ $peminjaman->peminjam_nama }}</div>
                    @if($peminjaman->peminjamKaryawan)
                    <div style="font-size: 9px; color: #666;">NIK: {{ $peminjaman->peminjamKaryawan->nik_karyawan }}</div>
                    @endif
                </td>
                <td class="text-center text-nowrap">{{ $peminjaman->tanggal_peminjaman->format('d/m/Y') }}</td>
                <td class="text-center text-nowrap">
                    {{ $peminjaman->tanggal_rencana_kembali->format('d/m/Y') }}
                    @if($peminjaman->tanggal_kembali_aktual)
                    <div style="font-size: 9px; color: #666;">Aktual: {{ $peminjaman->tanggal_kembali_aktual->format('d/m/Y') }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $peminjaman->jumlah_dipinjam }}</td>
                <td class="text-center">
                    <span class="status-badge status-{{ $peminjaman->status_peminjaman }}">
                        {{ $peminjaman->status_label }}
                    </span>
                    @if($peminjaman->is_terlambat)
                    <div style="font-size: 8px; color: #dc3545; margin-top: 2px;">
                        Terlambat {{ $peminjaman->keterlambatan }} hari
                    </div>
                    @endif
                </td>
                <td class="text-center">
                    <div style="font-size: 9px;">Pinjam: {{ $peminjaman->kondisi_saat_dipinjam_label }}</div>
                    @if($peminjaman->kondisi_saat_dikembalikan)
                    <div style="font-size: 9px; margin-top: 2px;">Kembali: {{ $peminjaman->kondisi_saat_dikembalikan_label }}</div>
                    @endif
                </td>
                <td style="font-size: 9px;">{{ Str::limit($peminjaman->keperluan, 80) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        <div style="font-size: 16px; margin-bottom: 10px;">Tidak ada data peminjaman</div>
        <div style="font-size: 12px;">Sesuai dengan filter yang dipilih</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div>Laporan ini dibuat secara otomatis oleh sistem</div>
        <div>{{ config('app.name') }} - {{ now()->format('Y') }}</div>
    </div>
</body>
</html>