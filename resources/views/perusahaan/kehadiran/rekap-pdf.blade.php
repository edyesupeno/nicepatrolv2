<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran - {{ $project->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3B82C8;
        }
        
        .header h1 {
            font-size: 14px;
            color: #3B82C8;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 9px;
            color: #666;
        }
        
        .info {
            margin-bottom: 10px;
            font-size: 8px;
        }
        
        .info table {
            width: 100%;
        }
        
        .info td {
            padding: 1px 0;
        }
        
        .info td:first-child {
            width: 80px;
            font-weight: bold;
        }
        
        table.rekap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table.rekap th,
        table.rekap td {
            border: 1px solid #ddd;
            padding: 3px 2px;
            text-align: center;
            font-size: 7px;
        }
        
        table.rekap th {
            background-color: #3B82C8;
            color: white;
            font-weight: bold;
            font-size: 7px;
            padding: 4px 2px;
        }
        
        table.rekap td.karyawan {
            text-align: left;
            font-size: 7px;
            max-width: 100px;
            padding: 3px 4px;
        }
        
        .karyawan-name {
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .karyawan-nik {
            color: #666;
            font-size: 6px;
        }
        
        .karyawan-jabatan {
            color: #666;
            font-size: 6px;
        }
        
        .status {
            font-weight: bold;
            padding: 2px 3px;
            border-radius: 2px;
            display: inline-block;
            font-size: 7px;
        }
        
        .status-h {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-t {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-pc {
            background-color: #FED7AA;
            color: #9A3412;
        }
        
        .status-tpc {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .status-a {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .status-i {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .status-s {
            background-color: #E9D5FF;
            color: #6B21A8;
        }
        
        .status-c {
            background-color: #E0E7FF;
            color: #3730A3;
        }
        
        .legend {
            margin-top: 10px;
            padding: 8px;
            background-color: #f9fafb;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .legend h3 {
            font-size: 9px;
            margin-bottom: 6px;
            color: #333;
            font-weight: bold;
        }
        
        .legend-items {
            width: 100%;
        }
        
        .legend-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 3px;
            font-size: 7px;
            white-space: nowrap;
        }
        
        .legend-box {
            display: inline-block;
            width: 16px;
            height: 16px;
            text-align: center;
            line-height: 16px;
            margin-right: 4px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 7px;
            vertical-align: middle;
        }
        
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 7px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REKAP KEHADIRAN KARYAWAN</h1>
        <p>{{ $project->nama }}</p>
    </div>
    
    <!-- Info -->
    <div class="info">
        <table>
            <tr>
                <td>Periode</td>
                <td>: {{ $tanggalMulai->format('d F Y') }} - {{ $tanggalAkhir->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Total Karyawan</td>
                <td>: {{ $karyawans->count() }} orang</td>
            </tr>
            <tr>
                <td>Dicetak</td>
                <td>: {{ now()->format('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Rekap Table -->
    <table class="rekap">
        <thead>
            <tr>
                <th style="width: 100px;">Karyawan</th>
                @foreach($dates as $date)
                <th style="width: 20px;">
                    {{ $date->format('d') }}<br>
                    <span style="font-size: 6px;">{{ $date->format('D') }}</span>
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($karyawans as $karyawan)
            <tr>
                <td class="karyawan">
                    <div class="karyawan-name">{{ $karyawan->nama_lengkap }}</div>
                    <div class="karyawan-nik">{{ $karyawan->nik_karyawan }}</div>
                    @if($karyawan->jabatan)
                    <div class="karyawan-jabatan">{{ $karyawan->jabatan->nama }}</div>
                    @endif
                </td>
                @foreach($dates as $date)
                @php
                    $key = $karyawan->id . '_' . $date->format('Y-m-d');
                    $kehadiranCollection = $kehadirans->get($key);
                    $kehadiran = $kehadiranCollection ? $kehadiranCollection->first() : null;
                @endphp
                <td>
                    @if($kehadiran)
                        @if($kehadiran->status == 'hadir')
                        <span class="status status-h">H</span>
                        @elseif($kehadiran->status == 'terlambat')
                        <span class="status status-t">T</span>
                        @elseif($kehadiran->status == 'pulang_cepat')
                        <span class="status status-pc">PC</span>
                        @elseif($kehadiran->status == 'terlambat_pulang_cepat')
                        <span class="status status-tpc">TPC</span>
                        @elseif($kehadiran->status == 'alpa')
                        <span class="status status-a">A</span>
                        @elseif($kehadiran->status == 'izin')
                        <span class="status status-i">I</span>
                        @elseif($kehadiran->status == 'sakit')
                        <span class="status status-s">S</span>
                        @elseif($kehadiran->status == 'cuti')
                        <span class="status status-c">C</span>
                        @endif
                    @else
                    -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Legend -->
    <div class="legend">
        <h3>Keterangan Status:</h3>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-box status-h">H</span>
                <span>Hadir</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-t">T</span>
                <span>Terlambat</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-pc">PC</span>
                <span>Pulang Cepat</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-tpc">TPC</span>
                <span>Terlambat & Pulang Cepat</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-a">A</span>
                <span>Alpa</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-i">I</span>
                <span>Izin</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-s">S</span>
                <span>Sakit</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-c">C</span>
                <span>Cuti</span>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dicetak dari Nice Patrol System - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
