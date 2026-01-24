@extends('perusahaan.layouts.app')

@section('title', 'Detail Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Detail Kru Change</h3>
            <div class="flex space-x-2">
                @if($kruChange->status === 'pending' && $kruChange->canBeStarted())
                    <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium" onclick="confirmStartHandover()">
                        <i class="fas fa-play mr-2"></i>Mulai Handover
                    </button>
                @endif
                
                @if($kruChange->status === 'in_progress' && $kruChange->canBeCompleted())
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium" onclick="confirmCompleteHandover()">
                        <i class="fas fa-check mr-2"></i>Selesaikan
                    </button>
                @endif
                
                @if($kruChange->status !== 'completed')
                    <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium" onclick="showCancelModal()">
                        <i class="fas fa-times mr-2"></i>Batalkan
                    </button>
                @endif
                
                <a href="{{ route('perusahaan.kru-change.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Dasar -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Dasar</h4>
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
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Tim</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tim Keluar -->
                        <div class="bg-white rounded-lg p-3 border border-red-200">
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
                        <div class="bg-white rounded-lg p-3 border border-green-200">
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
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Foto Tim</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Foto Tim Keluar -->
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="text-sm font-semibold text-red-700">Foto Tim Keluar</h5>
                                @if($kruChange->status === 'pending' || $kruChange->status === 'in_progress')
                                    <button type="button" onclick="uploadFoto('keluar')" 
                                            class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">
                                        <i class="fas fa-camera mr-1"></i>Upload
                                    </button>
                                @endif
                            </div>
                            
                            <div id="foto-keluar-container">
                                @if($kruChange->foto_tim_keluar)
                                    <div class="relative">
                                        <img src="{{ Storage::url($kruChange->foto_tim_keluar) }}" 
                                             alt="Foto Tim Keluar" 
                                             class="w-full h-48 object-cover rounded-lg shadow-sm">
                                        @if($kruChange->status === 'pending' || $kruChange->status === 'in_progress')
                                            <button type="button" onclick="deleteFoto('keluar')" 
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-48 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                                        <div class="text-center">
                                            <i class="fas fa-camera text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-sm text-gray-500">Belum ada foto</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Foto Tim Masuk -->
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="text-sm font-semibold text-green-700">Foto Tim Masuk</h5>
                                @if($kruChange->status === 'pending' || $kruChange->status === 'in_progress')
                                    <button type="button" onclick="uploadFoto('masuk')" 
                                            class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">
                                        <i class="fas fa-camera mr-1"></i>Upload
                                    </button>
                                @endif
                            </div>
                            
                            <div id="foto-masuk-container">
                                @if($kruChange->foto_tim_masuk)
                                    <div class="relative">
                                        <img src="{{ Storage::url($kruChange->foto_tim_masuk) }}" 
                                             alt="Foto Tim Masuk" 
                                             class="w-full h-48 object-cover rounded-lg shadow-sm">
                                        @if($kruChange->status === 'pending' || $kruChange->status === 'in_progress')
                                            <button type="button" onclick="deleteFoto('masuk')" 
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-48 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                                        <div class="text-center">
                                            <i class="fas fa-camera text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-sm text-gray-500">Belum ada foto</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Approval -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Status Approval</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                        <div class="flex items-center space-x-3">
                            @if($kruChange->approved_keluar)
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600 text-xs"></i>
                                </div>
                            @endif
                            <span class="text-sm text-gray-900">Approval Tim Keluar</span>
                        </div>
                        @if(!$kruChange->approved_keluar && $kruChange->status === 'in_progress')
                            <button type="button" 
                                    onclick="showApprovalModal('keluar')"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium">
                                Approve
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                        <div class="flex items-center space-x-3">
                            @if($kruChange->approved_masuk)
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600 text-xs"></i>
                                </div>
                            @endif
                            <span class="text-sm text-gray-900">Approval Tim Masuk</span>
                        </div>
                        @if(!$kruChange->approved_masuk && $kruChange->status === 'in_progress')
                            <button type="button" 
                                    onclick="showApprovalModal('masuk')"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium">
                                Approve
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                        <div class="flex items-center space-x-3">
                            @if($kruChange->approved_supervisor)
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600 text-xs"></i>
                                </div>
                            @endif
                            <span class="text-sm text-gray-900">Approval Supervisor</span>
                        </div>
                        @if(!$kruChange->approved_supervisor && $kruChange->status === 'in_progress')
                            <button type="button" 
                                    onclick="showApprovalModal('supervisor')"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium">
                                Approve
                            </button>
                        @endif
                    </div>
                </div>
                
                @if($kruChange->status === 'in_progress')
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Panduan Approval:</p>
                                <ul class="mt-1 space-y-1 text-xs">
                                    <li>• <strong>Tim Keluar:</strong> Konfirmasi bahwa semua inventaris dan informasi sudah diserahkan dengan benar</li>
                                    <li>• <strong>Tim Masuk:</strong> Konfirmasi bahwa semua inventaris diterima dan siap mengambil alih tugas</li>
                                    <li>• <strong>Supervisor:</strong> Validasi bahwa proses handover sudah sesuai prosedur dan lengkap</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Catatan -->
            @if($kruChange->catatan_keluar || $kruChange->catatan_masuk || $kruChange->catatan_supervisor)
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Catatan</h4>
                <div class="space-y-4">
                    @if($kruChange->catatan_keluar)
                    <div>
                        <h5 class="text-sm font-medium text-gray-700">Catatan Tim Keluar:</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $kruChange->catatan_keluar }}</p>
                    </div>
                    @endif
                    
                    @if($kruChange->catatan_masuk)
                    <div>
                        <h5 class="text-sm font-medium text-gray-700">Catatan Tim Masuk:</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $kruChange->catatan_masuk }}</p>
                    </div>
                    @endif
                    
                    @if($kruChange->catatan_supervisor)
                    <div>
                        <h5 class="text-sm font-medium text-gray-700">Catatan Supervisor:</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $kruChange->catatan_supervisor }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Tracking Status -->
            @if($kruChange->status === 'in_progress')
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-semibold text-gray-900">Status Tracking Handover</h4>
                    <button type="button" onclick="showTrackingGuide()" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-question-circle mr-1"></i>Panduan Tracking
                    </button>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Inventaris Tracking -->
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <h5 class="text-sm font-semibold text-gray-900">Inventaris</h5>
                                <button type="button" onclick="showInventarisGuide()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-info-circle text-xs"></i>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($kruChange->isInventarisComplete())
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600 font-medium">Selesai</span>
                                @else
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs text-yellow-600 font-medium">{{ $kruChange->getInventarisCompletionPercentage() }}%</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($kruChange->inventaris_status)
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($kruChange->inventaris_status as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['nama'] }}</p>
                                            <p class="text-gray-500">{{ $item['kategori'] }}</p>
                                            @if(!empty($item['catatan']))
                                                <p class="text-orange-600 italic mt-1">{{ $item['catatan'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($item['status'] === 'pending')
                                                <button onclick="updateInventaris({{ $item['id'] }}, '{{ $item['nama'] }}')" 
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                    Check
                                                </button>
                                            @elseif($item['status'] === 'checked')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">✓ OK</span>
                                            @elseif($item['status'] === 'missing')
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">✗ Hilang</span>
                                            @elseif($item['status'] === 'damaged')
                                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium">⚠ Rusak</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-box-open text-gray-300 text-2xl mb-2"></i>
                                <p class="text-xs text-gray-500">Tidak ada inventaris untuk tim ini</p>
                            </div>
                        @endif
                    </div>

                    <!-- Kuesioner Tracking -->
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <h5 class="text-sm font-semibold text-gray-900">Kuesioner</h5>
                                <button type="button" onclick="showKuesionerGuide()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-info-circle text-xs"></i>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($kruChange->isKuesionerComplete())
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600 font-medium">Selesai</span>
                                @else
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs text-yellow-600 font-medium">{{ $kruChange->getKuesionerCompletionPercentage() }}%</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($kruChange->kuesioner_status)
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($kruChange->kuesioner_status as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['judul'] }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($item['status'] === 'pending')
                                                <a href="{{ route('perusahaan.kru-change.kuesioner-tracking', $kruChange->hash_id) }}" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                    Isi Kuesioner
                                                </a>
                                            @elseif($item['status'] === 'completed')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">✓ Selesai</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list text-gray-300 text-2xl mb-2"></i>
                                <p class="text-xs text-gray-500">Tidak ada kuesioner untuk tim ini</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pemeriksaan Tracking -->
                    <div class="bg-white rounded-lg p-4 border">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <h5 class="text-sm font-semibold text-gray-900">Pemeriksaan</h5>
                                <button type="button" onclick="showPemeriksaanGuide()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-info-circle text-xs"></i>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($kruChange->isPemeriksaanComplete())
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600 font-medium">Selesai</span>
                                @else
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs text-yellow-600 font-medium">{{ $kruChange->getPemeriksaanCompletionPercentage() }}%</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($kruChange->pemeriksaan_status)
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($kruChange->pemeriksaan_status as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item['nama'] }}</p>
                                            @if(!empty($item['catatan']))
                                                <p class="text-orange-600 italic mt-1">{{ $item['catatan'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($item['status'] === 'pending')
                                                <a href="{{ route('perusahaan.kru-change.pemeriksaan-tracking', $kruChange->hash_id) }}" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                    Lakukan Pemeriksaan
                                                </a>
                                            @elseif($item['status'] === 'checked')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">✓ OK</span>
                                            @elseif($item['status'] === 'failed')
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">✗ Gagal</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-search text-gray-300 text-2xl mb-2"></i>
                                <p class="text-xs text-gray-500">Tidak ada pemeriksaan untuk tim ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Batalkan Handover</h3>
            <form action="{{ route('perusahaan.kru-change.cancel', $kruChange->hash_id) }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">Alasan Pembatalan:</label>
                    <textarea name="alasan" id="alasan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                <div class="flex justify-center space-x-4">
                    <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium" onclick="hideCancelModal()">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Batalkan Handover
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

function showApprovalModal(type) {
    const titles = {
        'keluar': 'Approval Tim Keluar',
        'masuk': 'Approval Tim Masuk', 
        'supervisor': 'Approval Supervisor'
    };
    
    const descriptions = {
        'keluar': `
            <div class="text-left">
                <p class="mb-3">Anda akan memberikan approval untuk <strong>Tim Keluar</strong>.</p>
                <div class="bg-orange-50 p-3 rounded mb-3">
                    <p class="text-sm text-orange-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Pastikan:</strong>
                    </p>
                    <ul class="text-sm text-orange-700 mt-2 space-y-1">
                        <li>• Semua inventaris sudah diserahkan dengan benar</li>
                        <li>• Kuesioner shift sudah diisi lengkap</li>
                        <li>• Tidak ada masalah atau insiden yang belum dilaporkan</li>
                        <li>• Area patroli dalam kondisi aman untuk diserahkan</li>
                    </ul>
                </div>
                <p class="text-sm text-gray-600">Approval ini mengkonfirmasi bahwa tim keluar sudah menyelesaikan tugasnya dengan baik.</p>
            </div>
        `,
        'masuk': `
            <div class="text-left">
                <p class="mb-3">Anda akan memberikan approval untuk <strong>Tim Masuk</strong>.</p>
                <div class="bg-green-50 p-3 rounded mb-3">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Pastikan:</strong>
                    </p>
                    <ul class="text-sm text-green-700 mt-2 space-y-1">
                        <li>• Semua inventaris sudah diterima dan dicek kondisinya</li>
                        <li>• Tim masuk sudah memahami kondisi area patroli</li>
                        <li>• Semua informasi penting sudah diterima dari tim keluar</li>
                        <li>• Tim masuk siap mengambil alih tugas patroli</li>
                    </ul>
                </div>
                <p class="text-sm text-gray-600">Approval ini mengkonfirmasi bahwa tim masuk siap mengambil alih tugas.</p>
            </div>
        `,
        'supervisor': `
            <div class="text-left">
                <p class="mb-3">Anda akan memberikan approval sebagai <strong>Supervisor</strong>.</p>
                <div class="bg-blue-50 p-3 rounded mb-3">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Pastikan:</strong>
                    </p>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li>• Proses handover sudah sesuai dengan prosedur</li>
                        <li>• Semua tracking (inventaris, kuesioner, pemeriksaan) sudah lengkap</li>
                        <li>• Tidak ada masalah yang perlu ditindaklanjuti</li>
                        <li>• Kedua tim sudah memberikan approval mereka</li>
                    </ul>
                </div>
                <p class="text-sm text-gray-600">Approval supervisor adalah validasi final bahwa handover sudah sesuai standar.</p>
            </div>
        `
    };
    
    Swal.fire({
        title: titles[type],
        html: descriptions[type],
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Berikan Approval',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show form for catatan
            Swal.fire({
                title: 'Catatan Approval (Opsional)',
                html: `
                    <div class="text-left">
                        <p class="mb-3 text-sm text-gray-600">Berikan catatan tambahan jika diperlukan:</p>
                    </div>
                `,
                input: 'textarea',
                inputPlaceholder: 'Masukkan catatan approval (opsional)...',
                inputAttributes: {
                    'rows': 3,
                    'maxlength': 500
                },
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-save mr-2"></i>Simpan Approval',
                cancelButtonText: 'Batal',
                preConfirm: (catatan) => {
                    return catatan || '';
                }
            }).then((catatanResult) => {
                if (catatanResult.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menyimpan Approval...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit approval
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("perusahaan.kru-change.approve", $kruChange->hash_id) }}';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const tipeInput = document.createElement('input');
                    tipeInput.type = 'hidden';
                    tipeInput.name = 'tipe_approval';
                    tipeInput.value = type;
                    
                    const catatanInput = document.createElement('input');
                    catatanInput.type = 'hidden';
                    catatanInput.name = 'catatan';
                    catatanInput.value = catatanResult.value;
                    
                    form.appendChild(csrfToken);
                    form.appendChild(tipeInput);
                    form.appendChild(catatanInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });
}



// Handover confirmation functions
function confirmStartHandover() {
    Swal.fire({
        title: 'Mulai Handover?',
        html: `
            <div class="text-left">
                <p class="mb-3">Anda akan memulai proses handover kru. Setelah dimulai, sistem akan:</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Mengaktifkan tracking inventaris, kuesioner, dan pemeriksaan</li>
                    <li>• Memerlukan approval dari tim keluar, tim masuk, dan supervisor</li>
                    <li>• Memerlukan penyelesaian semua checklist sebelum handover selesai</li>
                </ul>
                <p class="mt-3 text-sm text-orange-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Pastikan semua petugas sudah siap untuk proses handover
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-play mr-2"></i>Ya, Mulai Handover',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'text-left'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memulai Handover...',
                text: 'Mohon tunggu, sistem sedang menyiapkan tracking data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.kru-change.start", $kruChange->hash_id) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmCompleteHandover() {
    // Check if all tracking is complete
    const inventarisComplete = {{ $kruChange->isInventarisComplete() ? 'true' : 'false' }};
    const kuesionerComplete = {{ $kruChange->isKuesionerComplete() ? 'true' : 'false' }};
    const pemeriksaanComplete = {{ $kruChange->isPemeriksaanComplete() ? 'true' : 'false' }};
    const allApproved = {{ $kruChange->is_fully_approved ? 'true' : 'false' }};
    
    if (!inventarisComplete || !kuesionerComplete || !pemeriksaanComplete || !allApproved) {
        Swal.fire({
            title: 'Handover Belum Dapat Diselesaikan',
            html: `
                <div class="text-left">
                    <p class="mb-3">Untuk menyelesaikan handover, pastikan semua hal berikut sudah selesai:</p>
                    <ul class="text-sm space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-${inventarisComplete ? 'check text-green-500' : 'times text-red-500'} mr-2"></i>
                            Semua inventaris sudah dicek
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-${kuesionerComplete ? 'check text-green-500' : 'times text-red-500'} mr-2"></i>
                            Semua kuesioner sudah diisi
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-${pemeriksaanComplete ? 'check text-green-500' : 'times text-red-500'} mr-2"></i>
                            Semua pemeriksaan sudah dilakukan
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-${allApproved ? 'check text-green-500' : 'times text-red-500'} mr-2"></i>
                            Semua approval sudah diberikan
                        </li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }
    
    Swal.fire({
        title: 'Selesaikan Handover?',
        html: `
            <div class="text-left">
                <p class="mb-3">Anda akan menyelesaikan proses handover kru. Setelah diselesaikan:</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Status handover akan berubah menjadi "Selesai"</li>
                    <li>• Data tracking akan disimpan untuk audit</li>
                    <li>• Handover tidak dapat diubah lagi</li>
                </ul>
                <p class="mt-3 text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    Semua checklist sudah lengkap dan siap diselesaikan
                </p>
            </div>
        `,
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Selesaikan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.kru-change.complete", $kruChange->hash_id) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Guide functions
function showTrackingGuide() {
    Swal.fire({
        title: 'Panduan Tracking Handover',
        html: `
            <div class="text-left space-y-4">
                <div>
                    <h4 class="font-semibold text-blue-600 mb-2">
                        <i class="fas fa-box mr-2"></i>Inventaris
                    </h4>
                    <p class="text-sm text-gray-600">
                        Periksa semua peralatan dan inventaris yang digunakan tim keluar. 
                        Pastikan kondisi baik, lengkap, dan siap digunakan tim masuk.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-green-600 mb-2">
                        <i class="fas fa-clipboard-list mr-2"></i>Kuesioner
                    </h4>
                    <p class="text-sm text-gray-600">
                        Pastikan semua kuesioner shift sudah diisi lengkap oleh tim keluar. 
                        Ini termasuk laporan kondisi area, insiden, dan catatan penting.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-orange-600 mb-2">
                        <i class="fas fa-search mr-2"></i>Pemeriksaan
                    </h4>
                    <p class="text-sm text-gray-600">
                        Lakukan pemeriksaan kendaraan, peralatan khusus, dan sistem keamanan 
                        sebelum diserahkan ke tim masuk.
                    </p>
                </div>
                
                <div class="bg-blue-50 p-3 rounded">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Tips:</strong> Klik ikon info (i) di setiap section untuk panduan detail
                    </p>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#3b82f6'
    });
}

function showInventarisGuide() {
    Swal.fire({
        title: 'Panduan Tracking Inventaris',
        html: `
            <div class="text-left space-y-3">
                <div class="bg-green-50 p-3 rounded">
                    <h5 class="font-semibold text-green-700 mb-1">✓ OK - Kondisi Baik</h5>
                    <p class="text-sm text-green-600">Inventaris dalam kondisi baik dan siap digunakan</p>
                </div>
                
                <div class="bg-red-50 p-3 rounded">
                    <h5 class="font-semibold text-red-700 mb-1">✗ Hilang</h5>
                    <p class="text-sm text-red-600">Inventaris tidak ditemukan atau hilang. Wajib beri catatan detail</p>
                </div>
                
                <div class="bg-orange-50 p-3 rounded">
                    <h5 class="font-semibold text-orange-700 mb-1">⚠ Rusak</h5>
                    <p class="text-sm text-orange-600">Inventaris rusak atau tidak berfungsi. Jelaskan kerusakan di catatan</p>
                </div>
                
                <div class="bg-blue-50 p-3 rounded mt-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-lightbulb mr-1"></i>
                        <strong>Catatan:</strong> Untuk status "Hilang" atau "Rusak", sistem akan meminta catatan detail untuk keperluan tindak lanjut
                    </p>
                </div>
            </div>
        `,
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#16a34a'
    });
}

function showKuesionerGuide() {
    Swal.fire({
        title: 'Panduan Tracking Kuesioner',
        html: `
            <div class="text-left space-y-3">
                <p class="text-gray-700 mb-3">
                    Pastikan tim keluar sudah mengisi semua kuesioner yang diperlukan untuk shift mereka.
                </p>
                
                <div class="space-y-2">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <div>
                            <p class="font-medium">Yang Harus Diperiksa:</p>
                            <ul class="text-sm text-gray-600 ml-4 list-disc">
                                <li>Laporan kondisi area patroli</li>
                                <li>Catatan insiden atau kejadian khusus</li>
                                <li>Kondisi peralatan dan fasilitas</li>
                                <li>Informasi penting untuk tim selanjutnya</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-3 rounded">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Penting:</strong> Jika ada kuesioner yang belum diisi, minta tim keluar untuk melengkapi sebelum melanjutkan handover
                    </p>
                </div>
            </div>
        `,
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#059669'
    });
}

function showPemeriksaanGuide() {
    Swal.fire({
        title: 'Panduan Tracking Pemeriksaan',
        html: `
            <div class="text-left space-y-3">
                <div class="bg-green-50 p-3 rounded">
                    <h5 class="font-semibold text-green-700 mb-1">✓ OK - Lulus Pemeriksaan</h5>
                    <p class="text-sm text-green-600">Pemeriksaan berhasil dan semua dalam kondisi normal</p>
                </div>
                
                <div class="bg-red-50 p-3 rounded">
                    <h5 class="font-semibold text-red-700 mb-1">✗ Gagal Pemeriksaan</h5>
                    <p class="text-sm text-red-600">Ada masalah yang ditemukan. Wajib beri catatan detail untuk tindak lanjut</p>
                </div>
                
                <div class="mt-4">
                    <h5 class="font-semibold text-gray-700 mb-2">Contoh Pemeriksaan:</h5>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Pemeriksaan kendaraan (oli, bahan bakar, ban)</li>
                        <li>• Sistem keamanan (CCTV, alarm, akses kontrol)</li>
                        <li>• Peralatan komunikasi (radio, telepon darurat)</li>
                        <li>• Kondisi pos keamanan dan fasilitas</li>
                    </ul>
                </div>
                
                <div class="bg-orange-50 p-3 rounded">
                    <p class="text-sm text-orange-800">
                        <i class="fas fa-tools mr-1"></i>
                        <strong>Tips:</strong> Jika pemeriksaan gagal, berikan catatan detail agar tim maintenance dapat menindaklanjuti
                    </p>
                </div>
            </div>
        `,
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#ea580c'
    });
}

// Tracking functions
function updateInventaris(inventarisId, nama) {
    Swal.fire({
        title: 'Update Status Inventaris',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Inventaris:</strong> ${nama}</p>
                <p class="text-sm text-gray-600 mb-3">Pilih status berdasarkan kondisi inventaris saat ini:</p>
            </div>
        `,
        input: 'select',
        inputOptions: {
            'checked': '✓ OK - Kondisi Baik',
            'missing': '✗ Hilang - Tidak Ditemukan',
            'damaged': '⚠ Rusak - Perlu Perbaikan'
        },
        inputPlaceholder: 'Pilih status inventaris',
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3b82f6',
        inputValidator: (value) => {
            if (!value) {
                return 'Pilih status terlebih dahulu!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value === 'missing' || result.value === 'damaged') {
                const statusText = result.value === 'missing' ? 'hilang' : 'rusak';
                Swal.fire({
                    title: `Catatan Inventaris ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}`,
                    html: `
                        <div class="text-left">
                            <p class="mb-3">Berikan catatan detail untuk inventaris yang ${statusText}:</p>
                        </div>
                    `,
                    input: 'textarea',
                    inputPlaceholder: `Jelaskan kondisi inventaris yang ${statusText}...`,
                    inputAttributes: {
                        'rows': 4
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: result.value === 'missing' ? '#dc2626' : '#ea580c',
                    inputValidator: (value) => {
                        if (!value || value.trim().length < 10) {
                            return 'Catatan minimal 10 karakter untuk keperluan tindak lanjut';
                        }
                    }
                }).then((catatanResult) => {
                    if (catatanResult.isConfirmed) {
                        submitInventarisUpdate(inventarisId, result.value, catatanResult.value);
                    }
                });
            } else {
                submitInventarisUpdate(inventarisId, result.value, null);
            }
        }
    });
}

function updateKuesioner(kuesionerId, judul) {
    // Redirect to kuesioner tracking page
    window.location.href = `{{ route('perusahaan.kru-change.kuesioner-tracking', $kruChange->hash_id) }}`;
}

function updatePemeriksaan(pemeriksaanId, nama) {
    // Redirect to pemeriksaan tracking page  
    window.location.href = `{{ route('perusahaan.kru-change.pemeriksaan-tracking', $kruChange->hash_id) }}`;
}

function submitInventarisUpdate(inventarisId, status, catatan) {
    fetch(`{{ route('perusahaan.kru-change.update-inventaris', $kruChange->hash_id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            inventaris_id: inventarisId,
            status: status,
            catatan: catatan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', 'Gagal mengupdate status', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    });
}

function submitKuesionerUpdate(kuesionerId, status) {
    fetch(`{{ route('perusahaan.kru-change.update-kuesioner', $kruChange->hash_id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            kuesioner_id: kuesionerId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', 'Gagal mengupdate status', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    });
}

function submitPemeriksaanUpdate(pemeriksaanId, status, catatan) {
    fetch(`{{ route('perusahaan.kru-change.update-pemeriksaan', $kruChange->hash_id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            pemeriksaan_id: pemeriksaanId,
            status: status,
            catatan: catatan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', 'Gagal mengupdate status', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    });
}

// Photo upload functions
function uploadFoto(type) {
    Swal.fire({
        title: `Upload Foto Tim ${type === 'keluar' ? 'Keluar' : 'Masuk'}`,
        html: `
            <div class="text-left">
                <p class="mb-3">Pilih foto tim ${type === 'keluar' ? 'keluar' : 'masuk'} untuk handover ini:</p>
                <input type="file" id="foto-input-${type}" accept="image/*" class="w-full p-2 border border-gray-300 rounded">
                <p class="text-xs text-gray-500 mt-2">Format: PNG, JPG, JPEG. Maksimal 2MB</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-upload mr-2"></i>Upload',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        preConfirm: () => {
            const fileInput = document.getElementById(`foto-input-${type}`);
            const file = fileInput.files[0];
            
            if (!file) {
                Swal.showValidationMessage('Pilih file foto terlebih dahulu');
                return false;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                Swal.showValidationMessage('Ukuran file maksimal 2MB');
                return false;
            }
            
            if (!file.type.match('image.*')) {
                Swal.showValidationMessage('File harus berupa gambar');
                return false;
            }
            
            return file;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const file = result.value;
            const formData = new FormData();
            formData.append(`foto_tim_${type}`, file);
            
            // Show loading
            Swal.fire({
                title: 'Mengupload foto...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Upload photo
            const uploadUrl = type === 'keluar' 
                ? '{{ route("perusahaan.kru-change.upload-foto-tim-keluar", $kruChange->hash_id) }}'
                : '{{ route("perusahaan.kru-change.upload-foto-tim-masuk", $kruChange->hash_id) }}';
                
            fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reload page to show updated photo
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat upload foto'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat upload foto'
                });
            });
        }
    });
}

function deleteFoto(type) {
    Swal.fire({
        title: 'Hapus Foto?',
        text: `Anda yakin ingin menghapus foto tim ${type === 'keluar' ? 'keluar' : 'masuk'}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus foto...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Delete photo
            const deleteUrl = type === 'keluar' 
                ? '{{ route("perusahaan.kru-change.delete-foto-tim-keluar", $kruChange->hash_id) }}'
                : '{{ route("perusahaan.kru-change.delete-foto-tim-masuk", $kruChange->hash_id) }}';
                
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reload page to show updated display
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menghapus foto'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus foto'
                });
            });
        }
    });
}
</script>
@endpush