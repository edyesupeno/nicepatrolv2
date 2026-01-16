<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $checkpoint->nama }}</title>
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
                <h1 class="text-2xl font-bold mb-2">Nice Patrol Checkpoint</h1>
                <p class="text-blue-100 text-sm">Scan untuk verifikasi patrol</p>
            </div>

            <!-- QR Code -->
            <div class="p-8 text-center">
                <div class="bg-white border-4 border-blue-600 rounded-2xl p-6 inline-block mb-6 relative">
                    {!! QrCode::size(300)->margin(1)->errorCorrection('H')->generate(json_encode([
                        'checkpoint_id' => $checkpoint->id,
                        'qr_code' => $checkpoint->qr_code,
                        'nama' => $checkpoint->nama,
                        'rute' => $checkpoint->rutePatrol->nama,
                        'area' => $checkpoint->rutePatrol->areaPatrol->nama,
                        'project' => $checkpoint->rutePatrol->areaPatrol->project->nama,
                    ])) !!}
                    
                    <!-- Logo Overlay -->
                    @if($checkpoint->perusahaan->logo)
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div class="bg-white rounded-lg p-2 shadow-lg border-2 border-blue-600">
                                <img 
                                    src="{{ asset('storage/' . $checkpoint->perusahaan->logo) }}" 
                                    alt="{{ $checkpoint->perusahaan->nama }}" 
                                    class="w-16 h-16 object-contain"
                                >
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="space-y-4 text-left">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Nama Checkpoint</p>
                                <p class="text-base font-semibold text-gray-900">{{ $checkpoint->nama }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    @if($checkpoint->urutan)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-sort-numeric-up mr-1"></i>Urutan: {{ $checkpoint->urutan }}
                                        </p>
                                    @endif
                                    <p class="text-xs {{ $checkpoint->asets_count > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                        <i class="fas fa-box mr-1"></i>{{ $checkpoint->asets_count }} Aset
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-route text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Rute Patrol</p>
                                <p class="text-base font-semibold text-gray-900">{{ $checkpoint->rutePatrol->nama }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map text-orange-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Area Patrol</p>
                                <p class="text-base font-semibold text-gray-900">{{ $checkpoint->rutePatrol->areaPatrol->nama }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-building text-red-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Project</p>
                                <p class="text-base font-semibold text-gray-900">{{ $checkpoint->rutePatrol->areaPatrol->project->nama }}</p>
                            </div>
                        </div>
                    </div>

                    @if($checkpoint->asets->count() > 0)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-box text-teal-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-2">Aset di Checkpoint ({{ $checkpoint->asets->count() }})</p>
                                <div class="space-y-2">
                                    @foreach($checkpoint->asets as $aset)
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600 flex-shrink-0"></span>
                                            <span class="text-gray-900 font-medium">{{ $aset->nama }}</span>
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-600 text-xs">{{ $aset->kategori }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($checkpoint->alamat)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-location-dot text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Lokasi</p>
                                <p class="text-sm text-gray-700">{{ $checkpoint->alamat }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($checkpoint->deskripsi)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-info-circle text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 font-medium mb-1">Deskripsi</p>
                                <p class="text-sm text-gray-700">{{ $checkpoint->deskripsi }}</p>
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
            href="{{ route('perusahaan.patrol.checkpoint') }}"
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
                    link.download = 'QR_{{ $checkpoint->qr_code }}_{{ now()->format("YmdHis") }}.png';
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
