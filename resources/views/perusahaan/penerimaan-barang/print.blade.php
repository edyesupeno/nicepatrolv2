<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyaluran - {{ $penerimaanBarang->nomor_penerimaan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 2px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .header h2 {
            font-size: 18px;
            color: #374151;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .header h3 {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .header .report-number {
            font-size: 14px;
            color: #9ca3af;
            font-weight: 500;
            margin-top: 8px;
            padding: 4px 12px;
            background: #f3f4f6;
            border-radius: 20px;
            display: inline-block;
        }

        /* Info Cards */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #f9fafb;
        }

        .info-card h4 {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            align-items: flex-start;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            min-width: 120px;
        }

        .info-value {
            color: #374151;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }

        /* Photo Section */
        .photo-section {
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        .photo-section h4 {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .photo-container {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #f9fafb;
            display: inline-block;
            max-width: 400px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .photo-container::after {
            content: 'LAPORAN RESMI';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            color: rgba(37, 99, 235, 0.1);
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            pointer-events: none;
            z-index: 1;
        }

        .photo-container img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        .no-photo {
            width: 300px;
            height: 200px;
            border: 2px dashed #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-style: italic;
            background: #f3f4f6;
            position: relative;
            z-index: 2;
        }

        /* Details Section */
        .details-section {
            margin-bottom: 30px;
        }

        .details-section h4 {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-item {
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .detail-value {
            color: #374151;
            font-weight: 500;
            font-size: 13px;
        }

        .detail-value.full-width {
            grid-column: 1 / -1;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-baik { background: #dcfce7; color: #166534; }
        .status-rusak { background: #fee2e2; color: #dc2626; }
        .status-segel-terbuka { background: #fef3c7; color: #d97706; }

        .kategori-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kategori-dokumen { background: #dbeafe; color: #1d4ed8; }
        .kategori-material { background: #dcfce7; color: #166534; }
        .kategori-elektronik { background: #f3e8ff; color: #7c3aed; }
        .kategori-logistik { background: #fed7aa; color: #ea580c; }

        /* Description Section */
        .description-section {
            margin-bottom: 30px;
        }

        .description-section h4 {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .description-content {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #f9fafb;
            min-height: 80px;
            color: #374151;
            line-height: 1.6;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
        }

        .signature-box {
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px 15px;
            background: #f9fafb;
        }

        .signature-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 60px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .signature-line {
            border-top: 1px solid #374151;
            margin-bottom: 5px;
        }

        .signature-name {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .container {
                max-width: none;
                margin: 0;
                padding: 15px;
            }
            
            .info-section {
                break-inside: avoid;
            }
            
            .photo-section {
                break-inside: avoid;
            }
            
            .signature-section {
                break-inside: avoid;
                margin-top: 30px;
            }
            
            @page {
                margin: 1cm;
                size: A4;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .info-section {
                grid-template-columns: 1fr;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .signature-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laporan Penyaluran</h1>
            @if($penerimaanBarang->perusahaan)
                <h2>{{ $penerimaanBarang->perusahaan->nama }}</h2>
            @endif
            @if($penerimaanBarang->project)
                <h3>{{ $penerimaanBarang->project->nama }}</h3>
            @endif
            <div class="report-number">{{ $penerimaanBarang->nomor_penerimaan }}</div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-card">
                <h4>Informasi Penerimaan</h4>
                <div class="info-row">
                    <span class="info-label">Nomor:</span>
                    <span class="info-value">{{ $penerimaanBarang->nomor_penerimaan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ $penerimaanBarang->tanggal_terima ? $penerimaanBarang->tanggal_terima->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Petugas:</span>
                    <span class="info-value">{{ $penerimaanBarang->petugas_penerima }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ $penerimaanBarang->status }}</span>
                </div>
            </div>

            <div class="info-card">
                <h4>Informasi Penerima</h4>
                <div class="info-row">
                    <span class="info-label">Departemen:</span>
                    <span class="info-value">{{ $penerimaanBarang->tujuan_departemen }}</span>
                </div>
                @if($penerimaanBarang->project)
                <div class="info-row">
                    <span class="info-label">Project:</span>
                    <span class="info-value">{{ $penerimaanBarang->project->nama }}</span>
                </div>
                @endif
                @if($penerimaanBarang->area)
                <div class="info-row">
                    <span class="info-label">Area:</span>
                    <span class="info-value">{{ $penerimaanBarang->area->nama }}</span>
                </div>
                @endif
                @if($penerimaanBarang->pos)
                <div class="info-row">
                    <span class="info-label">POS:</span>
                    <span class="info-value">{{ $penerimaanBarang->pos }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Photo Section -->
        @if($penerimaanBarang->foto_barang)
        <div class="photo-section">
            <h4>Dokumentasi Barang</h4>
            <div class="photo-container">
                <img src="{{ Storage::url($penerimaanBarang->foto_barang) }}" alt="Foto {{ $penerimaanBarang->nama_barang }}">
            </div>
        </div>
        @else
        <div class="photo-section">
            <h4>Dokumentasi Barang</h4>
            <div class="photo-container">
                <div class="no-photo">Tidak ada foto</div>
            </div>
        </div>
        @endif

        <!-- Details Section -->
        <div class="details-section">
            <h4>Detail Barang</h4>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Nama Barang</div>
                    <div class="detail-value">{{ $penerimaanBarang->nama_barang }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Kategori</div>
                    <div class="detail-value">
                        @php
                            $kategoriClass = match($penerimaanBarang->kategori_barang) {
                                'Dokumen' => 'kategori-dokumen',
                                'Material' => 'kategori-material',
                                'Elektronik' => 'kategori-elektronik',
                                'Logistik' => 'kategori-logistik',
                                default => 'kategori-dokumen'
                            };
                        @endphp
                        <span class="kategori-badge {{ $kategoriClass }}">{{ $penerimaanBarang->kategori_barang }}</span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Jumlah</div>
                    <div class="detail-value">{{ $penerimaanBarang->jumlah_barang }} {{ $penerimaanBarang->satuan }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Kondisi</div>
                    <div class="detail-value">
                        @php
                            $kondisiClass = match($penerimaanBarang->kondisi_barang) {
                                'Baik' => 'status-baik',
                                'Rusak' => 'status-rusak',
                                'Segel Terbuka' => 'status-segel-terbuka',
                                default => 'status-baik'
                            };
                        @endphp
                        <span class="status-badge {{ $kondisiClass }}">{{ $penerimaanBarang->kondisi_barang }}</span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Pengirim</div>
                    <div class="detail-value">{{ $penerimaanBarang->pengirim }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Dicatat Oleh</div>
                    <div class="detail-value">{{ $penerimaanBarang->createdBy->name ?? 'System' }}</div>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        @if($penerimaanBarang->keterangan)
        <div class="description-section">
            <h4>Keterangan & Berita</h4>
            <div class="description-content">
                {{ $penerimaanBarang->keterangan }}
            </div>
        </div>
        @else
        <div class="description-section">
            <h4>Keterangan & Berita</h4>
            <div class="description-content">
                <em>Tidak ada keterangan khusus untuk penyaluran ini.</em>
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Pengirim</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $penerimaanBarang->pengirim }}</div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Petugas Penerima</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $penerimaanBarang->petugas_penerima }}</div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Penerima Akhir</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $penerimaanBarang->tujuan_departemen }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dicetak pada {{ now()->format('d/m/Y H:i:s') }} WIB</p>
            <p>{{ $penerimaanBarang->perusahaan->nama ?? 'Nice Patrol System' }} - Sistem Manajemen Penerimaan Barang</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>