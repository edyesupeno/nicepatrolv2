<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Aset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 12px;
            font-weight: normal;
        }
        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        .filter-info h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .summary-card h4 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .summary-card p {
            margin: 5px 0 0 0;
            font-size: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
        }
        .table td {
            font-size: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            color: white;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-disetujui { background-color: #28a745; }
        .status-ditolak { background-color: #dc3545; }
        .status-selesai { background-color: #007bff; }
        .project-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            color: white;
        }
        .project-asal { background-color: #6c757d; }
        .project-tujuan { background-color: #007bff; }
        .statistics {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .stat-section {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }
        .stat-section h4 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MUTASI ASET</h1>
        <h2>{{ auth()->user()->perusahaan->nama ?? 'PT. Nice Patrol' }}</h2>
    </div>

    <div class="filter-info">
        <h3>Filter Laporan:</h3>
        <p>
            <strong>Periode:</strong> 
            @if(request('tanggal_dari') && request('tanggal_sampai'))
                {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') }}
            @elseif(request('tanggal_dari'))
                Dari {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') }}
            @elseif(request('tanggal_sampai'))
                Sampai {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
            |
            <strong>Status:</strong> {{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}
        </p>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <h4>{{ $mutasiAsets->count() }}</h4>
            <p>Total Mutasi</p>
        </div>
        <div class="summary-card">
            <h4>{{ $mutasiAsets->where('status', 'pending')->count() }}</h4>
            <p>Pending</p>
        </div>
        <div class="summary-card">
            <h4>{{ $mutasiAsets->where('status', 'disetujui')->count() }}</h4>
            <p>Disetujui</p>
        </div>
        <div class="summary-card">
            <h4>{{ $mutasiAsets->where('status', 'selesai')->count() }}</h4>
            <p>Selesai</p>
        </div>
    </div>

    @if($mutasiAsets->isNotEmpty())
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Nomor Mutasi</th>
                    <th width="8%">Tanggal</th>
                    <th width="20%">Aset</th>
                    <th width="15%">Karyawan</th>
                    <th width="12%">Project Asal</th>
                    <th width="12%">Project Tujuan</th>
                    <th width="8%">Status</th>
                    <th width="8%">Disetujui</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasiAsets as $mutasi)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $mutasi->nomor_mutasi }}</strong></td>
                    <td>{{ $mutasi->tanggal_mutasi->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $mutasi->asset_name }}</strong><br>
                        <small>{{ ucfirst(str_replace('_', ' ', $mutasi->asset_type)) }}</small>
                    </td>
                    <td>
                        <strong>{{ $mutasi->karyawan->nama_lengkap }}</strong><br>
                        <small>{{ $mutasi->karyawan->nik_karyawan }}</small>
                    </td>
                    <td>
                        <span class="project-badge project-asal">{{ $mutasi->projectAsal->nama ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="project-badge project-tujuan">{{ $mutasi->projectTujuan->nama ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $mutasi->status }}">{{ strtoupper($mutasi->status) }}</span>
                    </td>
                    <td>{{ $mutasi->disetujuiOleh->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="statistics">
            <div class="stat-section">
                <h4>Statistik Berdasarkan Tipe Aset</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipe Aset</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dataAsetCount = $mutasiAsets->where('asset_type', 'data_aset')->count();
                            $kendaraanCount = $mutasiAsets->where('asset_type', 'aset_kendaraan')->count();
                            $total = $mutasiAsets->count();
                        @endphp
                        <tr>
                            <td>Data Aset</td>
                            <td>{{ $dataAsetCount }}</td>
                            <td>{{ $total > 0 ? round(($dataAsetCount / $total) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Aset Kendaraan</td>
                            <td>{{ $kendaraanCount }}</td>
                            <td>{{ $total > 0 ? round(($kendaraanCount / $total) * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="stat-section">
                <h4>Statistik Berdasarkan Status</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                            @php
                                $count = $mutasiAsets->where('status', $status)->count();
                            @endphp
                            <tr>
                                <td>{{ ucfirst($status) }}</td>
                                <td>{{ $count }}</td>
                                <td>{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="no-data">
            <p>Tidak ada data mutasi aset untuk periode yang dipilih</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan dicetak pada {{ now()->format('d F Y H:i') }} WIB</p>
        <p>{{ auth()->user()->perusahaan->nama ?? 'PT. Nice Patrol' }} - Sistem Manajemen Aset</p>
    </div>
</body>
</html>