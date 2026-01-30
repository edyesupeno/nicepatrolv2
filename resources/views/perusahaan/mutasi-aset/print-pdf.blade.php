<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mutasi Aset - {{ $mutasiAset->nomor_mutasi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .asset-info {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-disetujui { background-color: #28a745; }
        .status-ditolak { background-color: #dc3545; }
        .status-selesai { background-color: #007bff; }
        .project-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .project-asal { background-color: #6c757d; }
        .project-tujuan { background-color: #007bff; }
        .text-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-box h4 {
            margin: 0 0 50px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA MUTASI ASET</h1>
        <h2>{{ $mutasiAset->perusahaan->nama ?? 'PT. Nice Patrol' }}</h2>
    </div>

    <div class="info-section">
        <h3>Informasi Mutasi</h3>
        <table class="info-table">
            <tr>
                <td>Nomor Mutasi</td>
                <td>: {{ $mutasiAset->nomor_mutasi }}</td>
            </tr>
            <tr>
                <td>Tanggal Mutasi</td>
                <td>: {{ $mutasiAset->tanggal_mutasi->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: <span class="status-badge status-{{ $mutasiAset->status }}">{{ strtoupper($mutasiAset->status) }}</span></td>
            </tr>
            <tr>
                <td>Karyawan Penanggung Jawab</td>
                <td>: {{ $mutasiAset->karyawan->nama_lengkap }} ({{ $mutasiAset->karyawan->nik_karyawan }})</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h3>Informasi Pemindahan</h3>
        <table class="info-table">
            <tr>
                <td>Project Asal</td>
                <td>: <span class="project-badge project-asal">{{ $mutasiAset->projectAsal->nama ?? 'N/A' }}</span></td>
            </tr>
            <tr>
                <td>Project Tujuan</td>
                <td>: <span class="project-badge project-tujuan">{{ $mutasiAset->projectTujuan->nama ?? 'N/A' }}</span></td>
            </tr>
        </table>
    </div>

    <div class="asset-info">
        <h3>Detail Aset</h3>
        <table class="info-table">
            <tr>
                <td>Nama Aset</td>
                <td>: {{ $mutasiAset->asset_name }}</td>
            </tr>
            <tr>
                <td>Tipe Aset</td>
                <td>: {{ ucfirst(str_replace('_', ' ', $mutasiAset->asset_type)) }}</td>
            </tr>
            @if($mutasiAset->asset_type == 'data_aset' && $mutasiAset->dataAset)
                <tr>
                    <td>Kode Aset</td>
                    <td>: {{ $mutasiAset->dataAset->kode_aset }}</td>
                </tr>
                <tr>
                    <td>Kategori</td>
                    <td>: {{ $mutasiAset->dataAset->kategori }}</td>
                </tr>
                <tr>
                    <td>Kondisi</td>
                    <td>: {{ ucfirst($mutasiAset->dataAset->kondisi) }}</td>
                </tr>
                @if($mutasiAset->dataAset->nilai_perolehan)
                <tr>
                    <td>Nilai Perolehan</td>
                    <td>: Rp {{ number_format($mutasiAset->dataAset->nilai_perolehan, 0, ',', '.') }}</td>
                </tr>
                @endif
            @elseif($mutasiAset->asset_type == 'aset_kendaraan' && $mutasiAset->asetKendaraan)
                <tr>
                    <td>Nomor Polisi</td>
                    <td>: {{ $mutasiAset->asetKendaraan->nomor_polisi }}</td>
                </tr>
                <tr>
                    <td>Merk/Model</td>
                    <td>: {{ $mutasiAset->asetKendaraan->merk }} {{ $mutasiAset->asetKendaraan->model }}</td>
                </tr>
                <tr>
                    <td>Tahun</td>
                    <td>: {{ $mutasiAset->asetKendaraan->tahun_pembuatan ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Warna</td>
                    <td>: {{ $mutasiAset->asetKendaraan->warna ?? 'N/A' }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="info-section">
        <h3>Alasan Mutasi</h3>
        <div class="text-section">
            {{ $mutasiAset->alasan_mutasi }}
        </div>
    </div>

    @if($mutasiAset->keterangan)
    <div class="info-section">
        <h3>Keterangan</h3>
        <div class="text-section">
            {{ $mutasiAset->keterangan }}
        </div>
    </div>
    @endif

    @if($mutasiAset->status != 'pending')
    <div class="info-section">
        <h3>Informasi Persetujuan</h3>
        <table class="info-table">
            <tr>
                <td>Disetujui Oleh</td>
                <td>: {{ $mutasiAset->disetujuiOleh->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Tanggal Persetujuan</td>
                <td>: {{ $mutasiAset->tanggal_persetujuan ? $mutasiAset->tanggal_persetujuan->format('d F Y H:i') : 'N/A' }}</td>
            </tr>
            @if($mutasiAset->catatan_persetujuan)
            <tr>
                <td>Catatan Persetujuan</td>
                <td>: {{ $mutasiAset->catatan_persetujuan }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <h4>Pemohon</h4>
            <div class="signature-line">
                {{ $mutasiAset->karyawan->nama_lengkap }}
            </div>
        </div>
        <div class="signature-box">
            <h4>Penyetuju</h4>
            <div class="signature-line">
                {{ $mutasiAset->disetujuiOleh->name ?? '(..........................)' }}
            </div>
        </div>
        <div class="signature-box">
            <h4>Penerima</h4>
            <div class="signature-line">
                (..........................)
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i') }} WIB</p>
        <p>{{ $mutasiAset->perusahaan->nama ?? 'PT. Nice Patrol' }} - Sistem Manajemen Aset</p>
    </div>
</body>
</html>