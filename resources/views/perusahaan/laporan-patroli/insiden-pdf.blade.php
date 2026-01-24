<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Insiden - {{ $patroliMandiri->nama_lokasi }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .header h2 {
            font-size: 14px;
            margin: 0 0 5px 0;
        }

        .header .meta {
            font-size: 10px;
            color: #666;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .info-label {
            width: 30%;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
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

        .description-box {
            background-color: #f9f9f9;
            border-left: 3px solid #000;
            padding: 10px;
            margin: 8px 0;
        }

        .photo-section {
            margin: 15px 0;
        }

        .photo-table td {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }

        .photo-table img {
            max-width: 200px;
            max-height: 150px;
            border: 1px solid #ddd;
        }

        .no-photo {
            background-color: #f5f5f5;
            border: 1px dashed #ccc;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .coordinates {
            font-family: monospace;
            background-color: #f0f0f0;
            padding: 3px 6px;
            border: 1px solid #ccc;
        }

        .signature-section {
            margin-top: 30px;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            padding: 15px 5px;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 10px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN INSIDEN KEAMANAN</h1>
        <h2>{{ $patroliMandiri->project->nama ?? 'Project' }}</h2>
        <div class="meta">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | 
            ID Laporan: {{ $patroliMandiri->hash_id }}
        </div>
    </div>

    <!-- Informasi Dasar -->
    <div class="section">
        <div class="section-title">Informasi Dasar</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Waktu Laporan:</td>
                <td>{{ $patroliMandiri->waktu_laporan->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="info-label">Petugas:</td>
                <td>{{ $patroliMandiri->petugas->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Project:</td>
                <td>{{ $patroliMandiri->project->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Area Patrol:</td>
                <td>{{ $patroliMandiri->areaPatrol->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Nama Lokasi:</td>
                <td><strong>{{ $patroliMandiri->nama_lokasi }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Koordinat GPS:</td>
                <td>
                    <span class="coordinates">{{ $patroliMandiri->latitude }}, {{ $patroliMandiri->longitude }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Status dan Prioritas -->
    <div class="section">
        <div class="section-title">Status dan Prioritas</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Prioritas:</td>
                <td>
                    <span class="status-badge status-{{ $patroliMandiri->prioritas }}">
                        {{ strtoupper($patroliMandiri->prioritas) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Status Laporan:</td>
                <td>
                    <span class="status-badge status-{{ $patroliMandiri->status_laporan }}">
                        {{ strtoupper($patroliMandiri->status_laporan) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Jenis Kendala:</td>
                <td><strong>{{ ucwords(str_replace('_', ' ', $patroliMandiri->jenis_kendala)) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Detail Insiden -->
    <div class="section">
        <div class="section-title">Detail Insiden</div>
        
        @if($patroliMandiri->deskripsi_kendala)
        <div style="margin-bottom: 12px;">
            <strong>Deskripsi Kendala:</strong>
            <div class="description-box">{{ $patroliMandiri->deskripsi_kendala }}</div>
        </div>
        @endif

        @if($patroliMandiri->tindakan_yang_diambil)
        <div style="margin-bottom: 12px;">
            <strong>Tindakan yang Diambil:</strong>
            <div class="description-box">{{ $patroliMandiri->tindakan_yang_diambil }}</div>
        </div>
        @endif

        @if($patroliMandiri->catatan_petugas)
        <div style="margin-bottom: 12px;">
            <strong>Catatan Petugas:</strong>
            <div class="description-box">{{ $patroliMandiri->catatan_petugas }}</div>
        </div>
        @endif
    </div>

    <!-- Dokumentasi Foto -->
    <div class="section">
        <div class="section-title">Dokumentasi Foto</div>
        <table class="photo-table">
            <tr>
                <td>
                    <div><strong>Foto Lokasi</strong></div>
                    @if($patroliMandiri->foto_lokasi)
                        <img src="{{ public_path('storage/' . $patroliMandiri->foto_lokasi) }}" alt="Foto Lokasi">
                    @else
                        <div class="no-photo">Tidak ada foto lokasi</div>
                    @endif
                </td>
                <td>
                    <div><strong>Foto Kendala</strong></div>
                    @if($patroliMandiri->foto_kendala)
                        <img src="{{ public_path('storage/' . $patroliMandiri->foto_kendala) }}" alt="Foto Kendala">
                    @else
                        <div class="no-photo">Tidak ada foto kendala</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Review dan Tindak Lanjut -->
    @if($patroliMandiri->status_laporan !== 'submitted')
    <div class="section">
        <div class="section-title">Review dan Tindak Lanjut</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Direview Oleh:</td>
                <td>{{ $patroliMandiri->reviewer->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Waktu Review:</td>
                <td>{{ $patroliMandiri->reviewed_at ? $patroliMandiri->reviewed_at->format('d/m/Y H:i:s') : '-' }}</td>
            </tr>
        </table>
        
        @if($patroliMandiri->review_catatan)
        <div style="margin-top: 10px;">
            <strong>Catatan Review:</strong>
            <div class="description-box">{{ $patroliMandiri->review_catatan }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Tanda Tangan -->
    <table class="signature-section">
        <tr>
            <td class="signature-table">
                <div><strong>Petugas Patroli</strong></div>
                <div class="signature-line">{{ $patroliMandiri->petugas->name ?? '________________' }}</div>
            </td>
            <td class="signature-table">
                <div><strong>Supervisor</strong></div>
                <div class="signature-line">{{ $patroliMandiri->reviewer->name ?? '________________' }}</div>
            </td>
            <td class="signature-table">
                <div><strong>Manager</strong></div>
                <div class="signature-line">________________</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem Nice Patrol</p>
        <p>{{ $patroliMandiri->project->nama ?? 'Project' }} - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>