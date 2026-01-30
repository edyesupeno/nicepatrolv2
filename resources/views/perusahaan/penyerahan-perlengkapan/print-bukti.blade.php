<div class="print-bukti">
    <div class="header">
        <h2>BUKTI PENYERAHAN PERLENGKAPAN</h2>
        <p>{{ auth()->user()->perusahaan->nama }}</p>
    </div>
    
    <div class="info-grid">
        <div class="info-section">
            <h4>Informasi Penyerahan</h4>
            <table>
                <tr>
                    <td>Project</td>
                    <td>: {{ $penyerahan->project->nama }}</td>
                </tr>
                <tr>
                    <td>Tanggal Mulai</td>
                    <td>: {{ $penyerahan->tanggal_mulai->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Selesai</td>
                    <td>: {{ $penyerahan->tanggal_selesai->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Cetak</td>
                    <td>: {{ now()->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="info-section">
            <h4>Informasi Penerima</h4>
            <table>
                <tr>
                    <td>Nama</td>
                    <td>: {{ $karyawan->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td>NIK</td>
                    <td>: {{ $karyawan->nik_karyawan }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: {{ $karyawan->jabatan->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="items-section">
        <h4>Daftar Item yang Diserahkan</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Item</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Tanggal Diserahkan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item->nama_item }}</td>
                    <td>{{ $item->item->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $item->jumlah_diserahkan }}</td>
                    <td>{{ $item->item->satuan }}</td>
                    <td>{{ $item->tanggal_diserahkan->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="signature-section">
        <div class="signature-box">
            <p>Yang Menyerahkan</p>
            <br><br><br>
            <p>(_____________________)</p>
            <p>Tanggal: _______________</p>
        </div>
        
        <div class="signature-box">
            <p>Yang Menerima</p>
            <br><br><br>
            <p>{{ $karyawan->nama_lengkap }}</p>
            <p>Tanggal: {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>
    
    <div class="footer">
        <p><small>Dokumen ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</small></p>
    </div>
</div>

<style>
.print-bukti {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.4;
}

.header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 15px;
}

.header h2 {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: bold;
}

.header p {
    margin: 0;
    font-size: 14px;
    color: #666;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.info-section h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.info-section table {
    width: 100%;
    font-size: 12px;
}

.info-section table td {
    padding: 3px 0;
    vertical-align: top;
}

.info-section table td:first-child {
    width: 40%;
}

.items-section {
    margin-bottom: 40px;
}

.items-section h4 {
    margin: 0 0 15px 0;
    font-size: 14px;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}

.items-table th,
.items-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.items-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.items-table td:first-child {
    text-align: center;
    width: 40px;
}

.items-table td:nth-child(4) {
    text-align: center;
    width: 60px;
}

.signature-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-top: 50px;
}

.signature-box {
    text-align: center;
    font-size: 12px;
}

.signature-box p {
    margin: 5px 0;
}

.footer {
    margin-top: 30px;
    text-align: center;
    border-top: 1px solid #ddd;
    padding-top: 10px;
}

.footer small {
    color: #666;
    font-size: 10px;
}

@media print {
    .print-bukti {
        margin: 0;
        padding: 15px;
    }
    
    .info-grid {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .signature-section {
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
}
</style>