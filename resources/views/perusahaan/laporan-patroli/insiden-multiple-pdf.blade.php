<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Insiden Multiple</title>
    <style>
        @page {
            margin: 15mm 10mm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .header .meta {
            font-size: 9px;
            color: #666;
        }

        .summary-section {
            margin-bottom: 15px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            padding: 8px;
        }

        .summary-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            width: 25%;
            text-align: center;
            padding: 5px;
        }

        .summary-number {
            font-size: 14px;
            font-weight: bold;
            display: block;
        }

        .summary-label {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }

        .incident-item {
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 8px;
            background-color: #fff;
        }

        .incident-header {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
        }

        .incident-date {
            float: right;
            font-size: 9px;
            color: #666;
            font-weight: normal;
        }

        .info-table td {
            padding: 2px 5px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        .info-label {
            width: 25%;
            font-weight: bold;
            font-size: 8px;
            background-color: #f8f8f8;
        }

        .info-value {
            font-size: 8px;
        }

        .status-badge {
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-kritis { background-color: #ffebee; color: #c62828; }
        .status-tinggi { background-color: #fff3e0; color: #ef6c00; }
        .status-sedang { background-color: #fffde7; color: #f57f17; }
        .status-rendah { background-color: #e3f2fd; color: #1565c0; }
        .status-submitted { background-color: #e3f2fd; color: #1565c0; }
        .status-reviewed { background-color: #fffde7; color: #f57f17; }
        .status-resolved { background-color: #e8f5e8; color: #2e7d32; }

        .description {
            background-color: #f9f9f9;
            border-left: 2px solid #000;
            padding: 5px;
            margin: 3px 0;
            font-size: 8px;
        }

        .coordinates {
            font-family: monospace;
            background-color: #f0f0f0;
            padding: 1px 3px;
            font-size: 7px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN INSIDEN KEAMANAN - MULTIPLE</h1>
        <div class="meta">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | 
            Total Insiden: {{ $insidenList->count() }}
        </div>
    </div>

    <!-- Summary -->
    <div class="summary-section">
        <div class="summary-title">Ringkasan Insiden</div>
        <table class="summary-table">
            <tr>
                <td>
                    <span class="summary-number">{{ $insidenList->count() }}</span>
                    <div class="summary-label">Total Insiden</div>
                </td>
                <td>
                    <span class="summary-number">{{ $insidenList->where('prioritas', 'kritis')->count() }}</span>
                    <div class="summary-label">Prioritas Kritis</div>
                </td>
                <td>
                    <span class="summary-number">{{ $insidenList->where('status_laporan', 'resolved')->count() }}</span>
                    <div class="summary-label">Terselesaikan</div>
                </td>
                <td>
                    <span class="summary-number">{{ $insidenList->count() > 0 ? round(($insidenList->where('status_laporan', 'resolved')->count() / $insidenList->count()) * 100, 1) : 0 }}%</span>
                    <div class="summary-label">Tingkat Penyelesaian</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Incident List -->
    @forelse($insidenList as $index => $insiden)
        <div class="incident-item">
            <div class="incident-header">
                {{ $index + 1 }}. {{ $insiden->nama_lokasi }}
                <span class="incident-date">{{ $insiden->waktu_laporan->format('d/m/Y H:i') }}</span>
                <div style="clear: both;"></div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="info-label">Project:</td>
                    <td class="info-value">{{ $insiden->project->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Area:</td>
                    <td class="info-value">{{ $insiden->areaPatrol->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Petugas:</td>
                    <td class="info-value">{{ $insiden->petugas->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Jenis Kendala:</td>
                    <td class="info-value"><strong>{{ ucwords(str_replace('_', ' ', $insiden->jenis_kendala)) }}</strong></td>
                </tr>
                <tr>
                    <td class="info-label">Prioritas:</td>
                    <td class="info-value">
                        <span class="status-badge status-{{ $insiden->prioritas }}">
                            {{ strtoupper($insiden->prioritas) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Status:</td>
                    <td class="info-value">
                        <span class="status-badge status-{{ $insiden->status_laporan }}">
                            {{ strtoupper($insiden->status_laporan) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Koordinat:</td>
                    <td class="info-value">
                        <span class="coordinates">{{ $insiden->latitude }}, {{ $insiden->longitude }}</span>
                    </td>
                </tr>
            </table>

            @if($insiden->deskripsi_kendala)
            <div>
                <strong style="font-size: 8px;">Deskripsi Kendala:</strong>
                <div class="description">{{ $insiden->deskripsi_kendala }}</div>
            </div>
            @endif

            @if($insiden->tindakan_yang_diambil)
            <div>
                <strong style="font-size: 8px;">Tindakan yang Diambil:</strong>
                <div class="description">{{ $insiden->tindakan_yang_diambil }}</div>
            </div>
            @endif

            @if($insiden->catatan_petugas)
            <div>
                <strong style="font-size: 8px;">Catatan Petugas:</strong>
                <div class="description">{{ $insiden->catatan_petugas }}</div>
            </div>
            @endif

            @if($insiden->status_laporan !== 'submitted' && $insiden->review_catatan)
            <div>
                <strong style="font-size: 8px;">Review ({{ $insiden->reviewer->name ?? '-' }}):</strong>
                <div class="description">{{ $insiden->review_catatan }}</div>
            </div>
            @endif
        </div>

        @if(($index + 1) % 6 == 0 && $index + 1 < $insidenList->count())
            <div class="page-break"></div>
        @endif
    @empty
        <div class="no-data">
            <p>Tidak ada data insiden untuk diekspor</p>
        </div>
    @endforelse

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem Nice Patrol</p>
        <p>Periode: {{ $insidenList->min('waktu_laporan')?->format('d/m/Y') }} - {{ $insidenList->max('waktu_laporan')?->format('d/m/Y') }} | {{ now()->format('Y') }}</p>
    </div>
</body>
</html>