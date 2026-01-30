<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Peminjaman Aset - {{ $peminjamanAset->kode_peminjaman }}</title>
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
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .document-subtitle {
            font-size: 12px;
            color: #6b7280;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding: 8px 12px;
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
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
            width: 35%;
            padding: 6px 12px 6px 0;
            font-weight: bold;
            color: #4b5563;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            padding: 6px 0;
            color: #1f2937;
            vertical-align: top;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #dbeafe; color: #1e40af; }
        .status-dipinjam { background-color: #d1fae5; color: #065f46; }
        .status-dikembalikan { background-color: #f3f4f6; color: #374151; }
        .status-terlambat { background-color: #fee2e2; color: #991b1b; }
        .status-hilang { background-color: #fee2e2; color: #991b1b; }
        .status-rusak { background-color: #fed7aa; color: #9a3412; }
        .status-ditolak { background-color: #fee2e2; color: #991b1b; }
        
        .kondisi-baik { color: #065f46; font-weight: bold; }
        .kondisi-rusak-ringan { color: #d97706; font-weight: bold; }
        .kondisi-rusak-berat { color: #dc2626; font-weight: bold; }
        .kondisi-hilang { color: #991b1b; font-weight: bold; }
        
        .notes-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-top: 10px;
        }
        
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-grid {
            display: table;
            width: 100%;
        }
        
        .signature-row {
            display: table-row;
        }
        
        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 20px 10px;
            vertical-align: top;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            color: #1f2937;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 1px;
        }
        
        .signature-name {
            font-size: 10px;
            color: #6b7280;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        
        .important-note {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 12px;
            margin: 15px 0;
        }
        
        .important-note-title {
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        
        .important-note-text {
            color: #7f1d1d;
            font-size: 11px;
        }
        
        @media print {
            body { margin: 0; }
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $peminjamanAset->perusahaan->nama }}</div>
            @if($peminjamanAset->perusahaan->alamat || $peminjamanAset->perusahaan->telepon)
                <div class="company-info">
                    @if($peminjamanAset->perusahaan->alamat)
                        {{ $peminjamanAset->perusahaan->alamat }}
                    @endif
                    @if($peminjamanAset->perusahaan->alamat && $peminjamanAset->perusahaan->telepon) | @endif
                    @if($peminjamanAset->perusahaan->telepon)
                        Tel: {{ $peminjamanAset->perusahaan->telepon }}
                    @endif
                    @if($peminjamanAset->perusahaan->email)
                        | Email: {{ $peminjamanAset->perusahaan->email }}
                    @endif
                </div>
            @endif
            <div class="document-title">BUKTI PEMINJAMAN ASET</div>
            <div class="document-subtitle">{{ $peminjamanAset->kode_peminjaman }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Informasi Peminjaman -->
            <div class="section">
                <div class="section-title">Informasi Peminjaman</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Kode Peminjaman:</div>
                        <div class="info-value">{{ $peminjamanAset->kode_peminjaman }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Project:</div>
                        <div class="info-value">{{ $peminjamanAset->project->nama ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Peminjaman:</div>
                        <div class="info-value">{{ $peminjamanAset->tanggal_peminjaman->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Rencana Kembali:</div>
                        <div class="info-value">{{ $peminjamanAset->tanggal_rencana_kembali->format('d F Y') }}</div>
                    </div>
                    @if($peminjamanAset->tanggal_kembali_aktual)
                        <div class="info-row">
                            <div class="info-label">Tanggal Kembali Aktual:</div>
                            <div class="info-value">{{ $peminjamanAset->tanggal_kembali_aktual->format('d F Y') }}</div>
                        </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ str_replace('_', '-', $peminjamanAset->status_peminjaman) }}">
                                {{ $peminjamanAset->status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Aset -->
            <div class="section">
                <div class="section-title">Informasi Aset</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Kode Aset:</div>
                        <div class="info-value">{{ $peminjamanAset->aset_kode }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nama Aset:</div>
                        <div class="info-value">{{ $peminjamanAset->aset_nama }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kategori:</div>
                        <div class="info-value">{{ $peminjamanAset->aset_kategori }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jumlah Dipinjam:</div>
                        <div class="info-value">{{ $peminjamanAset->jumlah_dipinjam }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">PIC Penanggung Jawab:</div>
                        <div class="info-value">{{ $peminjamanAset->aset_info->pic_penanggung_jawab ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Informasi Peminjam -->
            <div class="section">
                <div class="section-title">Informasi Peminjam</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nama Peminjam:</div>
                        <div class="info-value">{{ $peminjamanAset->peminjam_nama }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tipe Peminjam:</div>
                        <div class="info-value">{{ ucfirst($peminjamanAset->peminjam_tipe) }}</div>
                    </div>
                    @if($peminjamanAset->peminjamKaryawan)
                        <div class="info-row">
                            <div class="info-label">NIK Karyawan:</div>
                            <div class="info-value">{{ $peminjamanAset->peminjamKaryawan->nik_karyawan }}</div>
                        </div>
                    @elseif($peminjamanAset->peminjamUser)
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value">{{ $peminjamanAset->peminjamUser->email }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kondisi Aset -->
            <div class="section">
                <div class="section-title">Kondisi Aset</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Kondisi Saat Dipinjam:</div>
                        <div class="info-value">
                            <span class="kondisi-{{ str_replace('_', '-', $peminjamanAset->kondisi_saat_dipinjam) }}">
                                {{ $peminjamanAset->kondisi_saat_dipinjam_label }}
                            </span>
                        </div>
                    </div>
                    @if($peminjamanAset->kondisi_saat_dikembalikan)
                        <div class="info-row">
                            <div class="info-label">Kondisi Saat Dikembalikan:</div>
                            <div class="info-value">
                                <span class="kondisi-{{ str_replace('_', '-', $peminjamanAset->kondisi_saat_dikembalikan) }}">
                                    {{ $peminjamanAset->kondisi_saat_dikembalikan_label }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Keperluan -->
            @if($peminjamanAset->keperluan)
                <div class="section">
                    <div class="section-title">Keperluan Peminjaman</div>
                    <div class="notes-box">
                        {{ $peminjamanAset->keperluan }}
                    </div>
                </div>
            @endif

            <!-- Catatan -->
            @if($peminjamanAset->catatan_peminjaman || $peminjamanAset->catatan_pengembalian)
                <div class="section">
                    <div class="section-title">Catatan</div>
                    @if($peminjamanAset->catatan_peminjaman)
                        <div style="margin-bottom: 10px;">
                            <strong>Catatan Peminjaman:</strong>
                            <div class="notes-box">{{ $peminjamanAset->catatan_peminjaman }}</div>
                        </div>
                    @endif
                    @if($peminjamanAset->catatan_pengembalian)
                        <div>
                            <strong>Catatan Pengembalian:</strong>
                            <div class="notes-box">{{ $peminjamanAset->catatan_pengembalian }}</div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Timeline -->
            <div class="section">
                <div class="section-title">Timeline Peminjaman</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Dibuat:</div>
                        <div class="info-value">
                            {{ $peminjamanAset->created_at->format('d F Y H:i') }} 
                            oleh {{ $peminjamanAset->createdBy->name }}
                        </div>
                    </div>
                    @if($peminjamanAset->approved_at)
                        <div class="info-row">
                            <div class="info-label">{{ $peminjamanAset->status_peminjaman === 'ditolak' ? 'Ditolak:' : 'Disetujui:' }}</div>
                            <div class="info-value">
                                {{ $peminjamanAset->approved_at->format('d F Y H:i') }}
                                @if($peminjamanAset->approvedBy)
                                    oleh {{ $peminjamanAset->approvedBy->name }}
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($peminjamanAset->borrowed_at)
                        <div class="info-row">
                            <div class="info-label">Dipinjam:</div>
                            <div class="info-value">{{ $peminjamanAset->borrowed_at->format('d F Y H:i') }}</div>
                        </div>
                    @endif
                    @if($peminjamanAset->returned_at)
                        <div class="info-row">
                            <div class="info-label">Dikembalikan:</div>
                            <div class="info-value">
                                {{ $peminjamanAset->returned_at->format('d F Y H:i') }}
                                @if($peminjamanAset->returnedBy)
                                    oleh {{ $peminjamanAset->returnedBy->name }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Important Note -->
            @if($peminjamanAset->status_peminjaman === 'dipinjam')
                <div class="important-note">
                    <div class="important-note-title">PENTING:</div>
                    <div class="important-note-text">
                        Peminjam bertanggung jawab penuh atas aset yang dipinjam dan wajib mengembalikan dalam kondisi baik sesuai tanggal yang telah ditentukan. 
                        Kerusakan atau kehilangan aset menjadi tanggung jawab peminjam.
                    </div>
                </div>
            @endif
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-grid">
                <div class="signature-row">
                    <div class="signature-cell">
                        <div class="signature-title">Peminjam</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $peminjamanAset->peminjam_nama }}</div>
                    </div>
                    <div class="signature-cell">
                        <div class="signature-title">PIC Aset</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $peminjamanAset->aset_info->pic_penanggung_jawab ?? '(.........................)' }}</div>
                    </div>
                    <div class="signature-cell">
                        <div class="signature-title">{{ $peminjamanAset->approvedBy ? 'Disetujui oleh' : 'Menyetujui' }}</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $peminjamanAset->approvedBy->name ?? '(.......................)' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y H:i') }}</div>
            <div>{{ $peminjamanAset->kode_peminjaman }} - {{ $peminjamanAset->perusahaan->nama }}</div>
        </div>
    </div>
</body>
</html>