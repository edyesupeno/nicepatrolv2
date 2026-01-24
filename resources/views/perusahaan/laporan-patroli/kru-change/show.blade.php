@extends('perusahaan.layouts.app')

@section('title', 'Detail Laporan Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Detail Laporan Kru Change</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $kruChange->areaPatrol->nama }} - {{ $kruChange->project->nama }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('perusahaan.laporan-patroli.kru-change.pdf', $kruChange->hash_id) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-file-pdf mr-2"></i>Download PDF
                </a>
                <a href="{{ route('perusahaan.laporan-patroli.kru-change.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Header Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Informasi Dasar -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Project:</span>
                            <span class="text-sm text-gray-900">{{ $kruChange->project->nama }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Area:</span>
                            <span class="text-sm text-gray-900">{{ $kruChange->areaPatrol->nama }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Status:</span>
                            <span>{!! $kruChange->status_badge !!}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Waktu Mulai:</span>
                            <span class="text-sm text-gray-900">{{ $kruChange->waktu_mulai_handover->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($kruChange->waktu_selesai_handover)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Waktu Selesai:</span>
                            <span class="text-sm text-gray-900">{{ $kruChange->waktu_selesai_handover->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Durasi:</span>
                            <span class="text-sm text-gray-900">{{ $kruChange->durasi_handover }}</span>
                        </div>
                        @endif
                        @if($kruChange->supervisor)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Supervisor:</span>
                            <span class="text-sm text-gray-900">{{ $kruChange->supervisor->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Tim -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tim</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tim Keluar -->
                        <div class="bg-white rounded-lg p-4 border border-red-200">
                            <h5 class="text-sm font-semibold text-red-700 mb-2">Tim Keluar</h5>
                            <p class="text-sm font-medium text-gray-900">{{ $kruChange->timKeluar->nama_tim }}</p>
                            <p class="text-xs text-gray-600">{{ $kruChange->timKeluar->jenis_regu }}</p>
                            <p class="text-xs text-gray-600">Shift: {{ $kruChange->shiftKeluar->nama_shift }}</p>
                            
                            <div class="mt-3">
                                <p class="text-xs font-medium text-gray-700 mb-2">Petugas Keluar:</p>
                                @if($kruChange->petugas_keluar_ids && count($kruChange->petugas_keluar_ids) > 0)
                                    <div class="space-y-1">
                                        @foreach($kruChange->petugasKeluarWithRoles() as $anggota)
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-900">{{ $anggota->user->name }}</span>
                                                <span class="text-xs">{!! $anggota->role_badge !!}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($kruChange->petugasKeluar)
                                    <p class="text-xs text-gray-600">{{ $kruChange->petugasKeluar->name }}</p>
                                @else
                                    <p class="text-xs text-yellow-600">Belum ditentukan</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tim Masuk -->
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <h5 class="text-sm font-semibold text-green-700 mb-2">Tim Masuk</h5>
                            <p class="text-sm font-medium text-gray-900">{{ $kruChange->timMasuk->nama_tim }}</p>
                            <p class="text-xs text-gray-600">{{ $kruChange->timMasuk->jenis_regu }}</p>
                            <p class="text-xs text-gray-600">Shift: {{ $kruChange->shiftMasuk->nama_shift }}</p>
                            
                            <div class="mt-3">
                                <p class="text-xs font-medium text-gray-700 mb-2">Petugas Masuk:</p>
                                @if($kruChange->petugas_masuk_ids && count($kruChange->petugas_masuk_ids) > 0)
                                    <div class="space-y-1">
                                        @foreach($kruChange->petugasMasukWithRoles() as $anggota)
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-900">{{ $anggota->user->name }}</span>
                                                <span class="text-xs">{!! $anggota->role_badge !!}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($kruChange->petugasMasuk)
                                    <p class="text-xs text-gray-600">{{ $kruChange->petugasMasuk->name }}</p>
                                @else
                                    <p class="text-xs text-yellow-600">Belum ditentukan</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foto Tim -->
                @if($kruChange->foto_tim_keluar || $kruChange->foto_tim_masuk)
                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Foto Tim</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Foto Tim Keluar -->
                        @if($kruChange->foto_tim_keluar)
                        <div class="bg-white rounded-lg p-4 border">
                            <h5 class="text-sm font-semibold text-red-700 mb-3">Foto Tim Keluar</h5>
                            <img src="{{ Storage::url($kruChange->foto_tim_keluar) }}" 
                                 alt="Foto Tim Keluar" 
                                 class="w-full h-64 object-cover rounded-lg shadow-sm">
                        </div>
                        @endif

                        <!-- Foto Tim Masuk -->
                        @if($kruChange->foto_tim_masuk)
                        <div class="bg-white rounded-lg p-4 border">
                            <h5 class="text-sm font-semibold text-green-700 mb-3">Foto Tim Masuk</h5>
                            <img src="{{ Storage::url($kruChange->foto_tim_masuk) }}" 
                                 alt="Foto Tim Masuk" 
                                 class="w-full h-64 object-cover rounded-lg shadow-sm">
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Status Approval -->
            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Status Approval</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                        <div class="flex items-center space-x-3">
                            @if($kruChange->approved_keluar)
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">Approval Tim Keluar</p>
                                <p class="text-xs text-gray-500">
                                    {{ $kruChange->approved_keluar ? 'Approved' : 'Pending' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                        <div class="flex items-center space-x-3">
                            @if($kruChange->approved_masuk)
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">Approval Tim Masuk</p>
                                <p class="text-xs text-gray-500">
                                    {{ $kruChange->approved_masuk ? 'Approved' : 'Pending' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                        <div class="flex items-center space-x-3">
                            @if($kruChange->approved_supervisor)
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">Approval Supervisor</p>
                                <p class="text-xs text-gray-500">
                                    {{ $kruChange->approved_supervisor ? 'Approved' : 'Pending' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking Status -->
            @if($kruChange->status === 'in_progress' || $kruChange->status === 'completed')
            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Status Tracking Handover</h4>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Inventaris Tracking -->
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-sm font-semibold text-gray-900">Inventaris</h5>
                            <div class="flex items-center space-x-2">
                                @if($kruChange->isInventarisComplete())
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600 font-medium">Selesai</span>
                                @else
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                    <span class="text-xs text-yellow-600 font-medium">{{ $kruChange->getInventarisCompletionPercentage() }}%</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($kruChange->inventaris_status)
                            <div class="space-y-2">
                                @foreach($kruChange->inventaris_status as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['nama'] }}</p>
                                            <p class="text-gray-500">{{ $item['kategori'] }}</p>
                                            @if(!empty($item['catatan']))
                                                <p class="text-orange-600 italic mt-1">{{ $item['catatan'] }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            @if($item['status'] === 'checked')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">✓ OK</span>
                                            @elseif($item['status'] === 'missing')
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">✗ Hilang</span>
                                            @elseif($item['status'] === 'damaged')
                                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium">⚠ Rusak</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada inventaris</p>
                        @endif
                    </div>

                    <!-- Kuesioner Tracking -->
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-sm font-semibold text-gray-900">Kuesioner</h5>
                            <div class="flex items-center space-x-2">
                                @if($kruChange->isKuesionerComplete())
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600 font-medium">Selesai</span>
                                @else
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                    <span class="text-xs text-yellow-600 font-medium">{{ $kruChange->getKuesionerCompletionPercentage() }}%</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($kruChange->kuesioner_status)
                            <div class="space-y-2">
                                @foreach($kruChange->kuesioner_status as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['judul'] }}</p>
                                        </div>
                                        <div>
                                            @if($item['status'] === 'completed')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">✓ Selesai</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada kuesioner</p>
                        @endif
                    </div>

                    <!-- Pemeriksaan Tracking -->
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-sm font-semibold text-gray-900">Pemeriksaan</h5>
                            <div class="flex items-center space-x-2">
                                @if($kruChange->isPemeriksaanComplete())
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600 font-medium">Selesai</span>
                                @else
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                    <span class="text-xs text-yellow-600 font-medium">{{ $kruChange->getPemeriksaanCompletionPercentage() }}%</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($kruChange->pemeriksaan_status)
                            <div class="space-y-2">
                                @foreach($kruChange->pemeriksaan_status as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['nama'] }}</p>
                                            @if(!empty($item['catatan']))
                                                <p class="text-orange-600 italic mt-1">{{ $item['catatan'] }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            @if($item['status'] === 'checked')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">✓ OK</span>
                                            @elseif($item['status'] === 'failed')
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">✗ Gagal</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500 text-center py-4">Tidak ada pemeriksaan</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Jawaban Kuesioner -->
            @if($kuesionerAnswers->isNotEmpty())
            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Jawaban Kuesioner</h4>
                @foreach($kuesionerAnswers as $kuesionerData)
                    <div class="mb-6 bg-white rounded-lg p-4 border">
                        <h5 class="font-medium text-gray-900 mb-3">{{ $kuesionerData['nama'] }}</h5>
                        <div class="space-y-3">
                            @foreach($kuesionerData['answers'] as $answer)
                                <div class="border-l-4 border-blue-200 pl-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $answer->pertanyaanKuesioner->pertanyaan }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $answer->jawaban }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Jawaban Pemeriksaan -->
            @if($pemeriksaanAnswers->isNotEmpty())
            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Jawaban Pemeriksaan</h4>
                @foreach($pemeriksaanAnswers as $pemeriksaanData)
                    <div class="mb-6 bg-white rounded-lg p-4 border">
                        <h5 class="font-medium text-gray-900 mb-3">{{ $pemeriksaanData['nama'] }}</h5>
                        <div class="space-y-3">
                            @foreach($pemeriksaanData['answers'] as $answer)
                                <div class="border-l-4 border-orange-200 pl-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $answer->pertanyaanPemeriksaan->pertanyaan }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $answer->jawaban }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Catatan -->
            @if($kruChange->catatan_keluar || $kruChange->catatan_masuk || $kruChange->catatan_supervisor)
            <div class="bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Catatan</h4>
                <div class="space-y-4">
                    @if($kruChange->catatan_keluar)
                    <div class="bg-white rounded-lg p-4 border">
                        <h5 class="text-sm font-medium text-gray-700">Catatan Tim Keluar:</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $kruChange->catatan_keluar }}</p>
                    </div>
                    @endif
                    
                    @if($kruChange->catatan_masuk)
                    <div class="bg-white rounded-lg p-4 border">
                        <h5 class="text-sm font-medium text-gray-700">Catatan Tim Masuk:</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $kruChange->catatan_masuk }}</p>
                    </div>
                    @endif
                    
                    @if($kruChange->catatan_supervisor)
                    <div class="bg-white rounded-lg p-4 border">
                        <h5 class="text-sm font-medium text-gray-700">Catatan Supervisor:</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $kruChange->catatan_supervisor }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection