<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Karyawan - {{ $perusahaan->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3B82C8;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #3B82C8;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .info-row {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #3B82C8;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
        }
        
        th {
            background: #3B82C8;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2563A8;
        }
        
        td {
            padding: 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tr:hover {
            background: #e3f2fd;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $perusahaan->nama }}</h1>
        <h2>Data Karyawan</h2>
        @if($project)
            <p style="margin: 5px 0; font-size: 12px; color: #3B82C8; font-weight: bold;">
                Project: {{ $project->nama }}
            </p>
        @endif
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Total Data:</span> {{ $total }} karyawan
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Export:</span> {{ $generated_at }}
        </div>
        @if(!empty($filters))
            <div class="info-row">
                <span class="info-label">Filter:</span>
                @if(!empty($filters['search']))
                    Pencarian: "{{ $filters['search'] }}"
                @endif
                @if(!empty($filters['status_karyawan']))
                    Status: {{ $filters['status_karyawan'] }}
                @endif
                @if(!empty($filters['jabatan_id']))
                    @php
                        $jabatan = \App\Models\Jabatan::find($filters['jabatan_id']);
                    @endphp
                    @if($jabatan)
                        Jabatan: {{ $jabatan->nama }}
                    @endif
                @endif
                @if(isset($filters['is_active']) && $filters['is_active'] !== '')
                    Status Aktif: {{ $filters['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                @endif
            </div>
        @endif
    </div>

    @if($karyawans->count() > 0)
        <!-- Data Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">No Badge</th>
                    <th style="width: 14%;">Nama Lengkap</th>
                    <th style="width: 11%;">Email</th>
                    <th style="width: 9%;">Telepon</th>
                    <th style="width: 11%;">Project</th>
                    <th style="width: 9%;">Jabatan</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 5%;">JK</th>
                    <th style="width: 8%;">Tgl Masuk</th>
                    <th style="width: 8%;">Habis Kontrak</th>
                    <th style="width: 5%;">Aktif</th>
                </tr>
            </thead>
            <tbody>
                @foreach($karyawans as $index => $karyawan)
                    <tr>
                        <td>{{ $karyawan->nik_karyawan }}</td>
                        <td class="font-bold">{{ $karyawan->nama_lengkap }}</td>
                        <td>{{ $karyawan->user->email ?? '-' }}</td>
                        <td>{{ $karyawan->telepon ?? '-' }}</td>
                        <td>{{ $karyawan->project->nama ?? '-' }}</td>
                        <td>{{ $karyawan->jabatan->nama ?? '-' }}</td>
                        <td>{{ $karyawan->status_karyawan }}</td>
                        <td class="text-center">{{ $karyawan->jenis_kelamin ? substr($karyawan->jenis_kelamin, 0, 1) : '-' }}</td>
                        <td class="text-center">{{ $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">{{ $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            @if($karyawan->is_active)
                                <span class="status-active">Aktif</span>
                            @else
                                <span class="status-inactive">Tidak</span>
                            @endif
                        </td>
                    </tr>
                    
                    @if(($index + 1) % 35 == 0 && $index + 1 < $karyawans->count())
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        
                        <!-- Header for next page -->
                        <div class="header">
                            <h1>{{ $perusahaan->nama }}</h1>
                            <h2>Data Karyawan (Lanjutan)</h2>
                            @if($project)
                                <p style="margin: 5px 0; font-size: 12px; color: #3B82C8; font-weight: bold;">
                                    Project: {{ $project->nama }}
                                </p>
                            @endif
                        </div>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 8%;">No Badge</th>
                                    <th style="width: 14%;">Nama Lengkap</th>
                                    <th style="width: 11%;">Email</th>
                                    <th style="width: 9%;">Telepon</th>
                                    <th style="width: 11%;">Project</th>
                                    <th style="width: 9%;">Jabatan</th>
                                    <th style="width: 8%;">Status</th>
                                    <th style="width: 5%;">JK</th>
                                    <th style="width: 8%;">Tgl Masuk</th>
                                    <th style="width: 8%;">Habis Kontrak</th>
                                    <th style="width: 5%;">Aktif</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p><strong>Tidak ada data karyawan yang sesuai dengan filter yang dipilih.</strong></p>
            <p>Silakan ubah filter atau tambahkan data karyawan baru.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong>{{ $perusahaan->nama }}</strong> | 
            Dicetak pada: {{ $generated_at }} | 
            Halaman ini berisi {{ $total }} data karyawan
            @if($project)
                dari project {{ $project->nama }}
            @endif
        </p>
        <p style="margin-top: 5px; font-size: 7px;">
            Dokumen ini dibuat secara otomatis oleh sistem Nice Patrol. 
            Untuk informasi lebih lanjut, hubungi administrator sistem.
        </p>
    </div>
</body>
</html>