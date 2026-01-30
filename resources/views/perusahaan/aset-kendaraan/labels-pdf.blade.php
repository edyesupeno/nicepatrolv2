<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Label Kendaraan - {{ $perusahaan }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 portrait;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #666;
        }
        
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm;
            justify-content: space-between;
        }
        
        .label {
            width: 85mm;
            height: 35mm;
            border: 2px solid #333;
            border-radius: 3mm;
            padding: 3mm;
            margin-bottom: 5mm;
            box-sizing: border-box;
            page-break-inside: avoid;
            display: inline-block;
            vertical-align: top;
        }
        
        .label-header {
            text-align: center;
            border-bottom: 1px solid #666;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        
        .company-name {
            font-size: 8px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .label-title {
            font-size: 7px;
            color: #666;
            margin: 1px 0 0 0;
        }
        
        .vehicle-code {
            text-align: center;
            margin: 2mm 0;
        }
        
        .vehicle-code-text {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .barcode-container {
            text-align: center;
            margin: 2mm 0;
        }
        
        .barcode {
            max-width: 70mm;
            height: 15mm;
            object-fit: contain;
        }
        
        /* Print optimizations */
        @media print {
            .label {
                break-inside: avoid;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        /* Page break after every 10 labels */
        .label:nth-child(10n) {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LABEL KENDARAAN - {{ strtoupper($perusahaan) }}</h1>
        <p>
            @if($type === 'single')
                Label Kendaraan Individual
            @elseif($type === 'multiple')
                Label Kendaraan Terpilih ({{ $totalLabels }} unit)
            @else
                Label Semua Kendaraan ({{ $totalLabels }} unit)
            @endif
            | Digenerate: {{ $generatedAt }}
        </p>
    </div>

    <div class="labels-container">
        @foreach($barcodeData as $data)
            <div class="label">
                <div class="label-header">
                    <p class="company-name">{{ strtoupper($perusahaan) }}</p>
                    <p class="label-title">VEHICLE LABEL</p>
                </div>
                
                <div class="vehicle-code">
                    <p class="vehicle-code-text">{{ $data['kendaraan']->kode_kendaraan }}</p>
                </div>
                
                <div class="barcode-container">
                    <img src="data:image/png;base64,{{ $data['barcode'] }}" alt="Barcode" class="barcode">
                </div>
                
                <div style="text-align: center; font-size: 7px; color: #666; margin-top: 2mm;">
                    Scan untuk info lengkap
                </div>
            </div>
        @endforeach
    </div>
    
    @if($totalLabels > 10)
        <div style="page-break-before: always; text-align: center; padding-top: 20mm; font-size: 12px; color: #666;">
            <p>Total {{ $totalLabels }} label kendaraan telah digenerate</p>
            <p>{{ $perusahaan }} - {{ $generatedAt }}</p>
        </div>
    @endif
</body>
</html>