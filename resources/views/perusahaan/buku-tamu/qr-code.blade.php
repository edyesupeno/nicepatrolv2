<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $bukuTamu->nama_tamu }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            #qr-content {
                page-break-inside: avoid;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                padding: 15mm;
                box-sizing: border-box;
                box-shadow: none !important;
            }
        }
        
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div id="qr-content" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8 text-white text-center">
                <div class="mb-4">
                    <i class="fas fa-qrcode text-6xl"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">Nice Patrol Buku Tamu</h1>
                <p class="text-blue-100 text-sm">Scan untuk verifikasi tamu</p>
            </div>

            <!-- QR Code -->
            <div class="p-8 text-center">
                <div class="bg-white border-4 border-blue-600 rounded-2xl p-6 inline-block mb-6 relative">
                    {!! QrCode::size(300)->margin(1)->errorCorrection('H')->generate(json_encode([
                        'buku_tamu_id' => $bukuTamu->id,
                        'qr_code' => $bukuTamu->qr_code,
                        'nama_tamu' => $bukuTamu->nama_tamu,
                        'perusahaan_tamu' => $bukuTamu->perusahaan_tamu,
                        'keperluan' => $bukuTamu->keperluan,
                        'project' => $bukuTamu->project->nama,
                        'area' => $bukuTamu->area ? $bukuTamu->area->nama : null,
                        'status' => $bukuTamu->status,
                        'check_in' => $bukuTamu->check_in->toISOString(),
                    ])) !!}
                    
                    <!-- Logo Overlay -->
                    @if($bukuTamu->perusahaan->logo)
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div class="bg-white rounded-lg p-2 shadow-lg border-2 border-blue-600">
                                <img 
                                    src="{{ asset('storage/' . $bukuTamu->perusahaan->logo) }}" 
                                    alt="{{ $bukuTamu->perusahaan->nama }}" 
                                    class="w-16 h-16 object-contain"
                                >
                            </div>
                        </div>
                    @else
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div class="bg-white rounded-lg p-2 shadow-lg border-2 border-blue-600">
                                <i class="fas fa-user-shield text-blue-600 text-4xl"></i>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="space-y-4 text-left">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Nama Tamu</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->nama_tamu }}</p>
                                @if($bukuTamu->perusahaan_tamu)
                                    <p class="text-sm text-gray-600 mt-1">{{ $bukuTamu->perusahaan_tamu }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clipboard text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Keperluan</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->keperluan }}</p>
                                @if($bukuTamu->bertemu)
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-handshake mr-1"></i>Bertemu: {{ $bukuTamu->bertemu }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Project</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->project->nama }}</p>
                                @if($bukuTamu->area)
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Area: {{ $bukuTamu->area->nama }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-{{ $bukuTamu->status_color }}-100 flex items-center justify-center flex-shrink-0">
                                <i class="{{ $bukuTamu->status_icon }} text-{{ $bukuTamu->status_color }}-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Status Kunjungan</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->status_label }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-sign-in-alt mr-1"></i>Check In: {{ $bukuTamu->check_in->format('d M Y H:i') }}
                                    </p>
                                    @if($bukuTamu->check_out)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-sign-out-alt mr-1"></i>Check Out: {{ $bukuTamu->check_out->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i>Durasi: {{ $bukuTamu->duration }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($bukuTamu->no_kartu_pinjam)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-credit-card text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Kartu Pinjam</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->no_kartu_pinjam }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($bukuTamu->kontak_darurat_nama)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-red-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Kontak Darurat</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->kontak_darurat_nama }}</p>
                                @if($bukuTamu->kontak_darurat_telepon)
                                    <p class="text-sm text-gray-600 mt-1">{{ $bukuTamu->kontak_darurat_telepon }}</p>
                                @endif
                                @if($bukuTamu->kontak_darurat_hubungan)
                                    <p class="text-xs text-gray-500 mt-1">{{ $bukuTamu->kontak_darurat_hubungan }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-tie text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Diinput oleh</p>
                                <p class="text-base font-semibold text-gray-900">{{ $bukuTamu->inputBy->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $bukuTamu->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($bukuTamu->catatan)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-sticky-note text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Catatan</p>
                                <p class="text-sm text-gray-700">{{ $bukuTamu->catatan }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($bukuTamu->keterangan_tambahan)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-teal-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Keterangan Tambahan</p>
                                <p class="text-sm text-gray-700">{{ $bukuTamu->keterangan_tambahan }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 text-center border-t">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Generated on {{ now()->format('d M Y H:i') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">QR Code: {{ $bukuTamu->qr_code }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="fixed bottom-6 right-6 space-y-3 no-print">
        <button 
            onclick="downloadAsImage()"
            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center justify-center shadow-lg"
        >
            <i class="fas fa-download mr-2"></i>Download QR Code
        </button>
        
        <button 
            onclick="window.print()"
            class="w-full px-6 py-3 bg-white border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition inline-flex items-center justify-center shadow-lg"
        >
            <i class="fas fa-print mr-2"></i>Print QR Code
        </button>

        <a 
            href="{{ route('perusahaan.buku-tamu.index') }}"
            class="w-full px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition inline-flex items-center justify-center shadow-lg"
        >
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <script>
        function downloadAsImage() {
            const element = document.getElementById('qr-content');
            const buttons = document.querySelectorAll('.no-print');
            
            // Hide buttons temporarily
            buttons.forEach(btn => btn.style.display = 'none');
            
            // Configure html2canvas options
            html2canvas(element, {
                scale: 2, // Higher quality
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false,
                width: element.offsetWidth,
                height: element.offsetHeight
            }).then(canvas => {
                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    // Create download link
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = 'QR_BukuTamu_{{ $bukuTamu->qr_code }}_{{ now()->format("YmdHis") }}.png';
                    link.href = url;
                    link.click();
                    
                    // Cleanup
                    URL.revokeObjectURL(url);
                    
                    // Show buttons again
                    buttons.forEach(btn => btn.style.display = '');
                }, 'image/png');
            });
        }
    </script>
</body>
</html>