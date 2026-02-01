<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Cash Advance - {{ $cashAdvance->perusahaan->nama ?? 'Perusahaan' }} - {{ $cashAdvance->project->nama }} - {{ $cashAdvance->nomor_ca }}</title>
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
            background: white;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .header .company-name {
            font-size: 16px;
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header .project-name {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        
        .header .ca-number {
            font-size: 12px;
            color: #777;
            font-weight: normal;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .info-left, .info-right {
            width: 48%;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 15px;
        }
        
        .summary-card {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        
        .summary-card h3 {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .summary-card .amount {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .transactions-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        
        .transactions-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .transactions-table .text-center {
            text-align: center;
        }
        
        .transactions-table .text-right {
            text-align: right;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-green {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-red {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-blue {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        .print-info {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                max-width: none;
                margin: 0;
                padding: 15px;
            }
            
            .print-info {
                display: none;
            }
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-active {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>LAPORAN CASH ADVANCE</h1>
            <div class="company-name">{{ $cashAdvance->perusahaan->nama ?? 'Nama Perusahaan' }}</div>
            <div class="project-name">{{ $cashAdvance->project->nama }}</div>
            <div class="ca-number">{{ $cashAdvance->nomor_ca }}</div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-row">
                    <span class="info-label">Nama Karyawan:</span>
                    <span class="info-value">{{ $cashAdvance->karyawan->nama_lengkap }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NIK:</span>
                    <span class="info-value">{{ $cashAdvance->karyawan->nik_karyawan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Project:</span>
                    <span class="info-value">{{ $cashAdvance->project->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Keperluan:</span>
                    <span class="info-value">{{ $cashAdvance->keperluan }}</span>
                </div>
            </div>
            <div class="info-right">
                <div class="info-row">
                    <span class="info-label">Tanggal Pengajuan:</span>
                    <span class="info-value">{{ $cashAdvance->tanggal_pengajuan->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Batas Pertanggungjawaban:</span>
                    <span class="info-value">{{ $cashAdvance->batas_pertanggungjawaban->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        @php
                            $statusLabels = [
                                'pending' => 'Menunggu Approval',
                                'approved' => 'Disetujui',
                                'active' => 'Aktif',
                                'completed' => 'Selesai',
                                'rejected' => 'Ditolak',
                                'need_report' => 'Perlu Laporan',
                            ];
                        @endphp
                        <span class="status-badge status-{{ $cashAdvance->status }}">
                            {{ $statusLabels[$cashAdvance->status] ?? $cashAdvance->status }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rekening Sumber:</span>
                    <span class="info-value">{{ $cashAdvance->rekening->nama_rekening }}</span>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Saldo Tersedia</h3>
                <div class="amount">Rp {{ number_format($cashAdvance->saldo_tersedia ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h3>Total Terpakai</h3>
                <div class="amount">Rp {{ number_format($cashAdvance->total_terpakai ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h3>Sisa Saldo</h3>
                <div class="amount">Rp {{ number_format($cashAdvance->sisa_saldo ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h3>Persentase Terpakai</h3>
                <div class="amount">
                    @if($cashAdvance->saldo_tersedia > 0)
                        {{ number_format(($cashAdvance->total_terpakai / $cashAdvance->saldo_tersedia) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </div>
            </div>
        </div>

        <!-- Transactions Section -->
        @if($cashAdvance->transactions->count() > 0)
            <div class="transactions-section">
                <h3 class="section-title">Riwayat Transaksi</h3>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">No. Transaksi</th>
                            <th width="12%">Tipe</th>
                            <th width="15%">Tanggal & Waktu</th>
                            <th width="15%">Jumlah</th>
                            <th width="25%">Keterangan</th>
                            <th width="13%">Saldo Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cashAdvance->transactions as $index => $transaction)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $transaction->nomor_transaksi }}</td>
                                <td class="text-center">
                                    @php
                                        $tipeClasses = [
                                            'pencairan' => 'badge-green',
                                            'pengeluaran' => 'badge-red',
                                            'pengembalian' => 'badge-blue',
                                        ];
                                        $tipeLabels = [
                                            'pencairan' => 'Pencairan',
                                            'pengeluaran' => 'Pengeluaran',
                                            'pengembalian' => 'Pengembalian',
                                        ];
                                    @endphp
                                    <span class="badge {{ $tipeClasses[$transaction->tipe] ?? 'badge-gray' }}">
                                        {{ $tipeLabels[$transaction->tipe] ?? $transaction->tipe }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $transaction->tanggal_transaksi->format('d/m/Y') }}</div>
                                    <div style="font-size: 10px; color: #666;">{{ $transaction->created_at->format('H:i:s') }} WIB</div>
                                </td>
                                <td class="text-right">
                                    <span style="color: {{ $transaction->tipe === 'pengeluaran' ? '#dc3545' : '#28a745' }};">
                                        {{ $transaction->tipe === 'pengeluaran' ? '-' : '+' }}
                                        Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>{{ $transaction->keterangan }}</td>
                                <td class="text-right">Rp {{ number_format($transaction->saldo_sesudah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Dibuat oleh,</div>
                <div class="signature-line">
                    <strong>{{ $cashAdvance->karyawan->nama_lengkap }}</strong><br>
                    <small>Pemegang Cash Advance</small>
                </div>
            </div>
            
            @if($cashAdvance->approvedBy)
                <div class="signature-box">
                    <div>Disetujui oleh,</div>
                    <div class="signature-line">
                        <strong>{{ $cashAdvance->approvedBy->name }}</strong><br>
                        <small>{{ $cashAdvance->tanggal_approved ? $cashAdvance->tanggal_approved->format('d/m/Y') : '' }}</small>
                    </div>
                </div>
            @else
                <div class="signature-box">
                    <div>Disetujui oleh,</div>
                    <div class="signature-line">
                        <strong>_____________________</strong><br>
                        <small>Pejabat yang Berwenang</small>
                    </div>
                </div>
            @endif
        </div>

        <!-- Print Info -->
        <div class="print-info">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB | 
            Sistem: {{ config('app.name') }}
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>