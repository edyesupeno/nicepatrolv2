<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kru Change - Multiple</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 25px;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .summary-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
        }
        
        .summary-table td {
            font-size: 9px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-progress {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .detail-section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        
        .detail-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-bottom: 1px solid #ddd;
        }
        
        .detail-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .detail-subtitle {
            font-size: 10px;
            color: #666;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 25%;
            padding: 3px 0;
            font-weight: bold;
            vertical-align: top;
            font-size: 9px;
        }
        
        .info-value {
            display: table-cell;
            width: 75%;
            padding: 3px 0;
            vertical-align: top;
            font-size: 9px;
        }
        
        .team-info {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .team-column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 8px;
            border: 1px solid #ddd;
            margin-right: 2%;
        }
        
        .team-column:last-child {
            margin-right: 0;
        }
        
        .team-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
            padding: 3px;
            background-color: #f5f5f5;
            font-size: 9px;
        }
        
        .approval-status {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .approval-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
            font-size: 8px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN KRU CHANGE - RINGKASAN</h1>
        <p>Tanggal Generate: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</p>
        <p>Total Laporan: {{ $kruChanges->count() }} Kru Change</p>
    </div>

    <!-- Summary Table -->
    <table class="summary-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Project</th>
                <th>Area</th>
                <th>Tim Keluar</th>
                <th>Tim Masuk</th>
                <th>Waktu Handover</th>
                <th>Status</th>
                <th>Approval</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kruChanges as $index => $kruChange)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $kruChange->project->nama }}</td>
                <td>{{ $kruChange->areaPatrol->nama }}</td>
                <td>
                    {{ $kruChange->timKeluar->nama_tim }}<br>
                    <small>{{ $kruChange->timKeluar->jenis_regu }}</small>
                </td>
                <td>
                    {{ $kruChange->timMasuk->nama_tim }}<br>
                    <small>{{ $kruChange->timMasuk->jenis_regu }}</small>
                </td>
                <td>{{ $kruChange->waktu_mulai_handover->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="status-badge status-{{ $kruChange->status }}">
                        {{ $kruChange->status_name }}
                    </span>
                </td>
                <td>
                    {{ $kruChange->approved_keluar ? '✓' : '✗' }} Keluar<br>
                    {{ $kruChange->approved_masuk ? '✓' : '✗' }} Masuk<br>
                    {{ $kruChange->approved_supervisor ? '✓' : '✗' }} Supervisor
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Detail Sections -->
    @foreach($kruChanges as $index => $kruChange)
    <div class="page-break"></div>
    <div class="detail-section">
        <div class="detail-header">
            <div class="detail-title">{{ $index + 1 }}. {{ $kruChange->areaPatrol->nama }} - {{ $kruChange->project->nama }}</div>
            <div class="detail-subtitle">{{ $kruChange->waktu_mulai_handover->format('d F Y, H:i') }} WIB</div>
        </div>

        <!-- Basic Info -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ $kruChange->status_name }}</div>
            </div>
            @if($kruChange->waktu_selesai_handover)
            <div class="info-row">
                <div class="info-label">Durasi:</div>
                <div class="info-value">{{ $kruChange->durasi_handover }}</div>
            </div>
            @endif
            @if($kruChange->supervisor)
            <div class="info-row">
                <div class="info-label">Supervisor:</div>
                <div class="info-value">{{ $kruChange->supervisor->name }}</div>
            </div>
            @endif
        </div>

        <!-- Team Info -->
        <div class="team-info">
            <div class="team-column">
                <div class="team-title">TIM KELUAR</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Tim:</div>
                        <div class="info-value">{{ $kruChange->timKeluar->nama_tim }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Shift:</div>
                        <div class="info-value">{{ $kruChange->shiftKeluar->nama_shift }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Petugas:</div>
                        <div class="info-value">
                            @if($kruChange->petugas_keluar_ids && count($kruChange->petugas_keluar_ids) > 0)
                                @foreach($kruChange->petugasKeluarWithRoles() as $anggota)
                                    {{ $anggota->user->name }} ({{ $anggota->role === 'leader' ? 'Danru' : ucfirst($anggota->role) }})<br>
                                @endforeach
                            @elseif($kruChange->petugasKeluar)
                                {{ $kruChange->petugasKeluar->name }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="team-column">
                <div class="team-title">TIM MASUK</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Tim:</div>
                        <div class="info-value">{{ $kruChange->timMasuk->nama_tim }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Shift:</div>
                        <div class="info-value">{{ $kruChange->shiftMasuk->nama_shift }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Petugas:</div>
                        <div class="info-value">
                            @if($kruChange->petugas_masuk_ids && count($kruChange->petugas_masuk_ids) > 0)
                                @foreach($kruChange->petugasMasukWithRoles() as $anggota)
                                    {{ $anggota->user->name }} ({{ $anggota->role === 'leader' ? 'Danru' : ucfirst($anggota->role) }})<br>
                                @endforeach
                            @elseif($kruChange->petugasMasuk)
                                {{ $kruChange->petugasMasuk->name }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Status -->
        <div class="approval-status">
            <div class="approval-item {{ $kruChange->approved_keluar ? 'status-completed' : 'status-pending' }}">
                <strong>Tim Keluar</strong><br>
                {{ $kruChange->approved_keluar ? '✓ Approved' : '⏳ Pending' }}
            </div>
            <div class="approval-item {{ $kruChange->approved_masuk ? 'status-completed' : 'status-pending' }}">
                <strong>Tim Masuk</strong><br>
                {{ $kruChange->approved_masuk ? '✓ Approved' : '⏳ Pending' }}
            </div>
            <div class="approval-item {{ $kruChange->approved_supervisor ? 'status-completed' : 'status-pending' }}">
                <strong>Supervisor</strong><br>
                {{ $kruChange->approved_supervisor ? '✓ Approved' : '⏳ Pending' }}
            </div>
        </div>

        <!-- Tracking Summary -->
        @if($kruChange->status === 'in_progress' || $kruChange->status === 'completed')
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Inventaris:</div>
                <div class="info-value">
                    @if($kruChange->isInventarisComplete())
                        ✓ Selesai ({{ $kruChange->getInventarisCompletionPercentage() }}%)
                    @else
                        ⏳ {{ $kruChange->getInventarisCompletionPercentage() }}%
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Kuesioner:</div>
                <div class="info-value">
                    @if($kruChange->isKuesionerComplete())
                        ✓ Selesai ({{ $kruChange->getKuesionerCompletionPercentage() }}%)
                    @else
                        ⏳ {{ $kruChange->getKuesionerCompletionPercentage() }}%
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Pemeriksaan:</div>
                <div class="info-value">
                    @if($kruChange->isPemeriksaanComplete())
                        ✓ Selesai ({{ $kruChange->getPemeriksaanCompletionPercentage() }}%)
                    @else
                        ⏳ {{ $kruChange->getPemeriksaanCompletionPercentage() }}%
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($kruChange->catatan_keluar || $kruChange->catatan_masuk || $kruChange->catatan_supervisor)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
            <strong style="font-size: 10px;">Catatan:</strong>
            @if($kruChange->catatan_keluar)
                <div style="font-size: 9px; margin-top: 3px;"><strong>Tim Keluar:</strong> {{ $kruChange->catatan_keluar }}</div>
            @endif
            @if($kruChange->catatan_masuk)
                <div style="font-size: 9px; margin-top: 3px;"><strong>Tim Masuk:</strong> {{ $kruChange->catatan_masuk }}</div>
            @endif
            @if($kruChange->catatan_supervisor)
                <div style="font-size: 9px; margin-top: 3px;"><strong>Supervisor:</strong> {{ $kruChange->catatan_supervisor }}</div>
            @endif
        </div>
        @endif
    </div>
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</p>
        <p>Nice Patrol System</p>
    </div>
</body>
</html>