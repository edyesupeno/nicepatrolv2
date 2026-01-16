<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Jadwal Shift - {{ $project->nama }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            line-height: 1.2;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 14px;
            margin-bottom: 3px;
            color: #333;
        }
        
        .header .info {
            font-size: 9px;
            color: #666;
        }
        
        .legend {
            margin-bottom: 6px;
            padding: 4px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .legend-title {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 3px;
        }
        
        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .legend-item {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            color: white;
            font-size: 7px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 2px 3px;
            text-align: center;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
        }
        
        .col-no {
            width: 20px;
        }
        
        .col-nik {
            width: 50px;
        }
        
        .col-nama {
            width: 80px;
            text-align: left;
        }
        
        .col-date {
            width: auto;
            min-width: 18px;
        }
        
        .shift-badge {
            display: inline-block;
            padding: 1px 3px;
            border-radius: 2px;
            color: white;
            font-weight: bold;
            font-size: 6px;
        }
        
        .total-row {
            background-color: #e8f4f8;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 8px;
            text-align: right;
            font-size: 7px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REKAP JADWAL SHIFT</h1>
        <div class="info">
            <strong>{{ $project->nama }}</strong> | 
            Periode: {{ $tanggalMulai->format('d M Y') }} - {{ $tanggalAkhir->format('d M Y') }} | 
            Total Karyawan: {{ $karyawans->count() }}
        </div>
    </div>

    <!-- Legend -->
    @if($shifts->isNotEmpty())
    <div class="legend">
        <div class="legend-title">Keterangan Shift:</div>
        <div class="legend-items">
            @foreach($shifts as $shift)
            <span class="legend-item" style="background-color: {{ $shift->warna }};">
                {{ $shift->kode_shift }} - {{ $shift->nama_shift }} ({{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }})
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nik">NIK</th>
                <th class="col-nama">Nama Karyawan</th>
                @foreach($dates as $date)
                <th class="col-date">
                    {{ $date->format('d') }}<br>
                    <small>{{ $date->format('D') }}</small>
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($karyawans as $index => $karyawan)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-nik">{{ $karyawan->nik_karyawan }}</td>
                <td class="col-nama">
                    {{ $karyawan->nama_lengkap }}
                    @if($karyawan->jabatan)
                    <br><small style="color: #666;">{{ $karyawan->jabatan->nama }}</small>
                    @endif
                </td>
                @foreach($dates as $date)
                @php
                    $key = $karyawan->id . '_' . $date->format('Y-m-d');
                    $jadwal = $jadwalShifts->get($key);
                @endphp
                <td class="col-date">
                    @if($jadwal && $jadwal->first())
                        <span class="shift-badge" style="background-color: {{ $jadwal->first()->shift->warna }};">
                            {{ $jadwal->first()->shift->kode_shift }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
            
            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                @foreach($dates as $date)
                @php
                    $totalPerDate = 0;
                    foreach($karyawans as $karyawan) {
                        $key = $karyawan->id . '_' . $date->format('Y-m-d');
                        $jadwal = $jadwalShifts->get($key);
                        if($jadwal && $jadwal->first()) {
                            $totalPerDate++;
                        }
                    }
                @endphp
                <td>{{ $totalPerDate }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
