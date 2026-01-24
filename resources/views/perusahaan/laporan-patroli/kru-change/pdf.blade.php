<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kru Change - {{ $kruChange->areaPatrol->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 30px;
            padding: 25px;
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
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 4px 0;
            font-weight: bold;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 4px 0;
            vertical-align: top;
        }
        
        .team-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .team-column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 10px;
            border: 1px solid #ddd;
            margin-right: 2%;
        }
        
        .team-column:last-child {
            margin-right: 0;
        }
        
        .team-title {
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
            padding: 5px;
            background-color: #f5f5f5;
        }
        
        .team-keluar .team-title {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .team-masuk .team-title {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-section {
            margin-bottom: 20px;
        }
        
        .status-grid {
            display: table;
            width: 100%;
        }
        
        .status-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .tracking-section {
            margin-bottom: 20px;
        }
        
        .tracking-grid {
            display: table;
            width: 100%;
        }
        
        .tracking-column {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .tracking-title {
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
            padding: 5px;
            background-color: #f3f4f6;
        }
        
        .tracking-item {
            margin-bottom: 8px;
            padding: 5px;
            border-left: 3px solid #ddd;
            padding-left: 8px;
        }
        
        .tracking-item.completed {
            border-left-color: #10b981;
            background-color: #f0fdf4;
        }
        
        .tracking-item.failed {
            border-left-color: #ef4444;
            background-color: #fef2f2;
        }
        
        .tracking-item.pending {
            border-left-color: #f59e0b;
            background-color: #fffbeb;
        }
        
        .answers-section {
            margin-bottom: 20px;
        }
        
        .answer-item {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        
        .answer-question {
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .answer-text {
            color: #666;
        }
        
        .notes-section {
            margin-bottom: 20px;
        }
        
        .note-item {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
        }
        
        .note-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                font-size: 11px;
                margin: 20mm;
                padding: 15mm;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN KRU CHANGE</h1>
        <h2>{{ $kruChange->project->nama }} - {{ $kruChange->areaPatrol->nama }}</h2>
        <p>Tanggal: {{ $kruChange->waktu_mulai_handover->format('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Informasi Dasar -->
    <div class="info-section">
        <div class="info-title">INFORMASI DASAR</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Project:</div>
                <div class="info-value">{{ $kruChange->project->nama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Area Patroli:</div>
                <div class="info-value">{{ $kruChange->areaPatrol->nama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ $kruChange->status_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Waktu Mulai:</div>
                <div class="info-value">{{ $kruChange->waktu_mulai_handover->format('d F Y, H:i') }} WIB</div>
            </div>
            @if($kruChange->waktu_selesai_handover)
            <div class="info-row">
                <div class="info-label">Waktu Selesai:</div>
                <div class="info-value">{{ $kruChange->waktu_selesai_handover->format('d F Y, H:i') }} WIB</div>
            </div>
            <div class="info-row">
                <div class="info-label">Durasi Handover:</div>
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
    </div>

    <!-- Informasi Tim -->
    <div class="info-section">
        <div class="info-title">INFORMASI TIM</div>
        <div class="team-section">
            <div class="team-column team-keluar">
                <div class="team-title">TIM KELUAR</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nama Tim:</div>
                        <div class="info-value">{{ $kruChange->timKeluar->nama_tim }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jenis Regu:</div>
                        <div class="info-value">{{ $kruChange->timKeluar->jenis_regu }}</div>
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
                                    {{ $anggota->user->name }} ({{ $anggota->role === 'leader' ? 'Danru (Komandan Regu)' : ucfirst($anggota->role) }})<br>
                                @endforeach
                            @elseif($kruChange->petugasKeluar)
                                {{ $kruChange->petugasKeluar->name }}
                            @else
                                Belum ditentukan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="team-column team-masuk">
                <div class="team-title">TIM MASUK</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nama Tim:</div>
                        <div class="info-value">{{ $kruChange->timMasuk->nama_tim }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jenis Regu:</div>
                        <div class="info-value">{{ $kruChange->timMasuk->jenis_regu }}</div>
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
                                    {{ $anggota->user->name }} ({{ $anggota->role === 'leader' ? 'Danru (Komandan Regu)' : ucfirst($anggota->role) }})<br>
                                @endforeach
                            @elseif($kruChange->petugasMasuk)
                                {{ $kruChange->petugasMasuk->name }}
                            @else
                                Belum ditentukan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Foto Tim -->
    @if($kruChange->foto_tim_keluar || $kruChange->foto_tim_masuk)
    <div class="info-section">
        <div class="info-title">FOTO TIM</div>
        <div class="team-section">
            @if($kruChange->foto_tim_keluar)
            <div class="team-column">
                <div class="team-title" style="background-color: #fee2e2; color: #991b1b;">FOTO TIM KELUAR</div>
                <div style="text-align: center; padding: 10px;">
                    <img src="{{ public_path('storage/' . $kruChange->foto_tim_keluar) }}" 
                         alt="Foto Tim Keluar" 
                         style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>
            @endif
            
            @if($kruChange->foto_tim_masuk)
            <div class="team-column">
                <div class="team-title" style="background-color: #dcfce7; color: #166534;">FOTO TIM MASUK</div>
                <div style="text-align: center; padding: 10px;">
                    <img src="{{ public_path('storage/' . $kruChange->foto_tim_masuk) }}" 
                         alt="Foto Tim Masuk" 
                         style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Status Approval -->
    <div class="info-section">
        <div class="info-title">STATUS APPROVAL</div>
        <div class="status-grid">
            <div class="status-item {{ $kruChange->approved_keluar ? 'status-approved' : 'status-pending' }}">
                <strong>Tim Keluar</strong><br>
                {{ $kruChange->approved_keluar ? '✓ Approved' : '⏳ Pending' }}
            </div>
            <div class="status-item {{ $kruChange->approved_masuk ? 'status-approved' : 'status-pending' }}">
                <strong>Tim Masuk</strong><br>
                {{ $kruChange->approved_masuk ? '✓ Approved' : '⏳ Pending' }}
            </div>
            <div class="status-item {{ $kruChange->approved_supervisor ? 'status-approved' : 'status-pending' }}">
                <strong>Supervisor</strong><br>
                {{ $kruChange->approved_supervisor ? '✓ Approved' : '⏳ Pending' }}
            </div>
        </div>
    </div>

    <!-- Status Tracking -->
    @if($kruChange->status === 'in_progress' || $kruChange->status === 'completed')
    <div class="info-section">
        <div class="info-title">STATUS TRACKING HANDOVER</div>
        <div class="tracking-grid">
            <!-- Inventaris -->
            <div class="tracking-column">
                <div class="tracking-title">INVENTARIS</div>
                @if($kruChange->inventaris_status)
                    @foreach($kruChange->inventaris_status as $item)
                        <div class="tracking-item {{ $item['status'] === 'checked' ? 'completed' : ($item['status'] === 'pending' ? 'pending' : 'failed') }}">
                            <strong>{{ $item['nama'] }}</strong><br>
                            <small>{{ $item['kategori'] }}</small><br>
                            Status: 
                            @if($item['status'] === 'checked')
                                ✓ OK
                            @elseif($item['status'] === 'missing')
                                ✗ Hilang
                            @elseif($item['status'] === 'damaged')
                                ⚠ Rusak
                            @else
                                ⏳ Pending
                            @endif
                            @if(!empty($item['catatan']))
                                <br><em>{{ $item['catatan'] }}</em>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="tracking-item">Tidak ada inventaris</div>
                @endif
            </div>

            <!-- Kuesioner -->
            <div class="tracking-column">
                <div class="tracking-title">KUESIONER</div>
                @if($kruChange->kuesioner_status)
                    @foreach($kruChange->kuesioner_status as $item)
                        <div class="tracking-item {{ $item['status'] === 'completed' ? 'completed' : 'pending' }}">
                            <strong>{{ $item['judul'] }}</strong><br>
                            Status: {{ $item['status'] === 'completed' ? '✓ Selesai' : '⏳ Pending' }}
                        </div>
                    @endforeach
                @else
                    <div class="tracking-item">Tidak ada kuesioner</div>
                @endif
            </div>

            <!-- Pemeriksaan -->
            <div class="tracking-column">
                <div class="tracking-title">PEMERIKSAAN</div>
                @if($kruChange->pemeriksaan_status)
                    @foreach($kruChange->pemeriksaan_status as $item)
                        <div class="tracking-item {{ $item['status'] === 'checked' ? 'completed' : ($item['status'] === 'pending' ? 'pending' : 'failed') }}">
                            <strong>{{ $item['nama'] }}</strong><br>
                            Status: 
                            @if($item['status'] === 'checked')
                                ✓ OK
                            @elseif($item['status'] === 'failed')
                                ✗ Gagal
                            @else
                                ⏳ Pending
                            @endif
                            @if(!empty($item['catatan']))
                                <br><em>{{ $item['catatan'] }}</em>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="tracking-item">Tidak ada pemeriksaan</div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Jawaban Kuesioner -->
    @if($kuesionerAnswers->isNotEmpty())
    <div class="page-break"></div>
    <div class="info-section">
        <div class="info-title">JAWABAN KUESIONER</div>
        @foreach($kuesionerAnswers as $kuesionerData)
            <div class="answers-section">
                <h4 style="margin-bottom: 10px; font-weight: bold;">{{ $kuesionerData['nama'] }}</h4>
                @foreach($kuesionerData['answers'] as $answer)
                    <div class="answer-item">
                        <div class="answer-question">{{ $answer->pertanyaanKuesioner->pertanyaan }}</div>
                        <div class="answer-text">{{ $answer->jawaban }}</div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    @endif

    <!-- Jawaban Pemeriksaan -->
    @if($pemeriksaanAnswers->isNotEmpty())
    <div class="info-section">
        <div class="info-title">JAWABAN PEMERIKSAAN</div>
        @foreach($pemeriksaanAnswers as $pemeriksaanData)
            <div class="answers-section">
                <h4 style="margin-bottom: 10px; font-weight: bold;">{{ $pemeriksaanData['nama'] }}</h4>
                @foreach($pemeriksaanData['answers'] as $answer)
                    <div class="answer-item">
                        <div class="answer-question">{{ $answer->pertanyaanPemeriksaan->pertanyaan }}</div>
                        <div class="answer-text">{{ $answer->jawaban }}</div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    @endif

    <!-- Catatan -->
    @if($kruChange->catatan_keluar || $kruChange->catatan_masuk || $kruChange->catatan_supervisor)
    <div class="info-section">
        <div class="info-title">CATATAN</div>
        @if($kruChange->catatan_keluar)
        <div class="note-item">
            <div class="note-title">Catatan Tim Keluar:</div>
            <div>{{ $kruChange->catatan_keluar }}</div>
        </div>
        @endif
        
        @if($kruChange->catatan_masuk)
        <div class="note-item">
            <div class="note-title">Catatan Tim Masuk:</div>
            <div>{{ $kruChange->catatan_masuk }}</div>
        </div>
        @endif
        
        @if($kruChange->catatan_supervisor)
        <div class="note-item">
            <div class="note-title">Catatan Supervisor:</div>
            <div>{{ $kruChange->catatan_supervisor }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</p>
        <p>Nice Patrol System - {{ $kruChange->project->nama }}</p>
    </div>
</body>
</html>