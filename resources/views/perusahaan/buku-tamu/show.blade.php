@extends('perusahaan.layouts.app')

@section('title', 'Detail Tamu - ' . $bukuTamu->nama_tamu)
@section('page-title', 'Detail Tamu')
@section('page-subtitle', $bukuTamu->nama_tamu)

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.buku-tamu.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-sky-600">
                    <i class="fas fa-address-book mr-2"></i>
                    Buku Tamu
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Guest Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <!-- Photo -->
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                                @if($bukuTamu->foto)
                                    <img 
                                        src="{{ Storage::url($bukuTamu->foto) }}" 
                                        alt="{{ $bukuTamu->nama_tamu }}" 
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                                    </div>
                                @else
                                    <i class="fas fa-user text-gray-400 text-3xl"></i>
                                @endif
                            </div>
                            
                            <!-- Basic Info -->
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $bukuTamu->nama_tamu }}</h1>
                                @if($bukuTamu->perusahaan_tamu)
                                    <p class="text-sm text-gray-600 mb-2">{{ $bukuTamu->perusahaan_tamu }}</p>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $bukuTamu->status_color }}-100 text-{{ $bukuTamu->status_color }}-700">
                                        <i class="{{ $bukuTamu->status_icon }} mr-2"></i>{{ $bukuTamu->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            @if($bukuTamu->is_visiting)
                                <button onclick="checkOutGuest('{{ $bukuTamu->hash_id }}')" class="px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition font-medium">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Check Out
                                </button>
                            @endif
                            <a href="{{ route('perusahaan.buku-tamu.edit', $bukuTamu->hash_id) }}" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                            <button onclick="confirmDelete('{{ $bukuTamu->hash_id }}')" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                                <i class="fas fa-trash mr-2"></i>Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pribadi</h3>
                            <div class="space-y-4">
                                @if($bukuTamu->nik)
                                <div class="flex items-start">
                                    <i class="fas fa-id-card text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->nik }}</p>
                                        <p class="text-xs text-gray-500">NIK</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->tanggal_lahir)
                                <div class="flex items-start">
                                    <i class="fas fa-birthday-cake text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->tanggal_lahir->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->domisili)
                                <div class="flex items-start">
                                    <i class="fas fa-home text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->domisili }}</p>
                                        <p class="text-xs text-gray-500">Domisili</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->jabatan)
                                <div class="flex items-start">
                                    <i class="fas fa-user-tie text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->jabatan }}</p>
                                        <p class="text-xs text-gray-500">Jabatan</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->email)
                                <div class="flex items-start">
                                    <i class="fas fa-envelope text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->email }}</p>
                                        <p class="text-xs text-gray-500">Email</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->no_whatsapp)
                                <div class="flex items-start">
                                    <i class="fab fa-whatsapp text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->no_whatsapp }}</p>
                                        <p class="text-xs text-gray-500">WhatsApp</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kunjungan</h3>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-building text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->project->nama }}</p>
                                        <p class="text-xs text-gray-500">Project</p>
                                    </div>
                                </div>
                                
                                @if($bukuTamu->area)
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->area->nama }}</p>
                                        <p class="text-xs text-gray-500">Area</p>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="flex items-start">
                                    <i class="fas fa-clipboard text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->keperluan }}</p>
                                        <p class="text-xs text-gray-500">Keperluan</p>
                                    </div>
                                </div>
                                
                                @if($bukuTamu->lokasi_dituju)
                                <div class="flex items-start">
                                    <i class="fas fa-map-pin text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->lokasi_dituju }}</p>
                                        <p class="text-xs text-gray-500">Lokasi Dituju</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->bertemu)
                                <div class="flex items-start">
                                    <i class="fas fa-handshake text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->bertemu }}</p>
                                        <p class="text-xs text-gray-500">Bertemu dengan</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->no_kartu_pinjam)
                                <div class="flex items-start">
                                    <i class="fas fa-credit-card text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->no_kartu_pinjam }}</p>
                                        <p class="text-xs text-gray-500">No Kartu Pinjam</p>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="flex items-start">
                                    <i class="fas fa-user text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->inputBy->name }}</p>
                                        <p class="text-xs text-gray-500">Diinput oleh</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Waktu Kunjungan -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Waktu Kunjungan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-sign-in-alt text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->check_in->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Check In</p>
                                    </div>
                                </div>
                                
                                @if($bukuTamu->mulai_kunjungan)
                                <div class="flex items-start">
                                    <i class="fas fa-play text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->mulai_kunjungan->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Mulai Kunjungan</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="space-y-4">
                                @if($bukuTamu->check_out)
                                <div class="flex items-start">
                                    <i class="fas fa-sign-out-alt text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->check_out->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Check Out</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->selesai_kunjungan)
                                <div class="flex items-start">
                                    <i class="fas fa-stop text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->selesai_kunjungan->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Selesai Kunjungan</p>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->lama_kunjungan)
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->lama_kunjungan }}</p>
                                        <p class="text-xs text-gray-500">Lama Kunjungan</p>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="flex items-start">
                                    <i class="fas fa-calendar text-gray-400 mr-3 mt-1 w-5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->created_at->format('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">Data Dibuat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kuesioner Dinamis -->
                    @if($bukuTamu->jawabanKuesioner->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jawaban Kuesioner</h3>
                        @if($bukuTamu->areaPatrol)
                            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                    <span class="text-sm font-medium text-blue-800">Area Patrol: {{ $bukuTamu->areaPatrol->nama }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="space-y-4">
                            @foreach($bukuTamu->jawabanKuesioner as $jawaban)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <span class="flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 rounded-full text-sm font-semibold flex-shrink-0">
                                        {{ $jawaban->pertanyaanTamu->urutan }}
                                    </span>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-2">
                                            {{ $jawaban->pertanyaanTamu->pertanyaan }}
                                            @if($jawaban->pertanyaanTamu->is_required)
                                                <span class="text-red-500 ml-1">*</span>
                                            @endif
                                        </h4>
                                        
                                        @if($jawaban->pertanyaanTamu->tipe_jawaban === 'pilihan')
                                            <div class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                {{ $jawaban->jawaban }}
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-lg p-3 mt-2">
                                                <p class="text-gray-700 leading-relaxed">{{ $jawaban->jawaban }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2 text-xs text-gray-500">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Tipe: {{ $jawaban->pertanyaanTamu->tipe_jawaban === 'pilihan' ? 'Pilihan Ganda' : 'Text Bebas' }}
                                            @if($jawaban->pertanyaanTamu->is_required)
                                                • Wajib diisi
                                            @else
                                                • Opsional
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Kuesioner Statis Lama (Fallback) -->
                    @if($bukuTamu->jawabanKuesioner->count() == 0 && ($bukuTamu->pertanyaan_1 || $bukuTamu->pertanyaan_2 || $bukuTamu->pertanyaan_3))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Kuesioner (Versi Lama)</h3>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="space-y-4">
                                @if($bukuTamu->pertanyaan_1)
                                <div>
                                    <p class="text-sm font-medium text-gray-700">1. Pertanyaan 1</p>
                                    <p class="text-sm text-purple-700 mt-1">{{ $bukuTamu->pertanyaan_1 }}</p>
                                </div>
                                @endif
                                
                                @if($bukuTamu->pertanyaan_2 && count($bukuTamu->pertanyaan_2) > 0)
                                <div>
                                    <p class="text-sm font-medium text-gray-700">2. Pertanyaan 2</p>
                                    <div class="mt-1">
                                        @foreach($bukuTamu->pertanyaan_2 as $jawaban)
                                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full mr-2 mb-1">{{ $jawaban }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                @if($bukuTamu->pertanyaan_3)
                                <div>
                                    <p class="text-sm font-medium text-gray-700">3. Pertanyaan 3</p>
                                    <p class="text-sm text-purple-700 mt-1">{{ $bukuTamu->pertanyaan_3 }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($bukuTamu->catatan)
                    <!-- Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Catatan</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed">{{ $bukuTamu->catatan }}</p>
                        </div>
                    </div>
                    @endif

                    @if($bukuTamu->keterangan_tambahan)
                    <!-- Additional Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Keterangan Tambahan</h3>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-blue-700 leading-relaxed">{{ $bukuTamu->keterangan_tambahan }}</p>
                        </div>
                    </div>
                    @endif

                    @if($bukuTamu->kontak_darurat_nama || $bukuTamu->kontak_darurat_telepon)
                    <!-- Emergency Contact -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Kontak Darurat</h3>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if($bukuTamu->kontak_darurat_nama)
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Nama</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->kontak_darurat_nama }}</p>
                                </div>
                                @endif
                                
                                @if($bukuTamu->kontak_darurat_telepon)
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Telepon</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->kontak_darurat_telepon }}</p>
                                </div>
                                @endif
                                
                                @if($bukuTamu->kontak_darurat_hubungan)
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Hubungan</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $bukuTamu->kontak_darurat_hubungan }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($bukuTamu->foto_identitas)
                    <!-- Identity Photo -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Foto Identitas</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="w-48 h-32 mx-auto rounded-lg overflow-hidden bg-white shadow-sm flex items-center justify-center">
                                <img 
                                    src="{{ Storage::url($bukuTamu->foto_identitas) }}" 
                                    alt="Foto Identitas" 
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="w-full h-full flex items-center justify-center bg-gray-200 hidden">
                                    <div class="text-center">
                                        <i class="fas fa-id-card text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-xs text-gray-500">Foto tidak tersedia</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 text-center mt-2">KTP/SIM</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- QR Code Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code</h3>
                
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto bg-white border-2 border-blue-600 rounded-lg flex items-center justify-center mb-4 p-2 relative overflow-hidden">
                        {!! QrCode::size(120)->margin(0)->errorCorrection('H')->generate(json_encode([
                            'buku_tamu_id' => $bukuTamu->id,
                            'qr_code' => $bukuTamu->qr_code,
                            'nama_tamu' => $bukuTamu->nama_tamu,
                            'status' => $bukuTamu->status,
                        ])) !!}
                        
                        <!-- Small logo overlay -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div class="bg-white rounded p-1 shadow-sm border border-blue-600">
                                <i class="fas fa-user-shield text-blue-600 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm font-mono bg-gray-100 px-3 py-2 rounded-lg">{{ $bukuTamu->qr_code }}</p>
                    <p class="text-xs text-gray-500 mt-2">Scan untuk akses cepat</p>
                    
                    <div class="mt-4 space-y-2">
                        <a 
                            href="{{ route('perusahaan.buku-tamu.qr-code', $bukuTamu->hash_id) }}" 
                            target="_blank"
                            class="block w-full px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition font-medium text-sm"
                        >
                            <i class="fas fa-qrcode mr-2"></i>Lihat QR Code Lengkap
                        </a>
                        
                        <button 
                            onclick="downloadQrCode('{{ $bukuTamu->hash_id }}')"
                            class="w-full px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-sm"
                        >
                            <i class="fas fa-download mr-2"></i>Download QR Code
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Kunjungan</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status Saat Ini</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $bukuTamu->status_color }}-100 text-{{ $bukuTamu->status_color }}-700">
                            <i class="{{ $bukuTamu->status_icon }} mr-2"></i>{{ $bukuTamu->status_label }}
                        </span>
                    </div>
                    
                    @if($bukuTamu->is_visiting)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-green-500 mr-2"></i>
                            <span class="text-sm text-green-700">Tamu sedang berada di lokasi</span>
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-gray-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Kunjungan telah selesai</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                
                <div class="space-y-3">
                    @if($bukuTamu->is_visiting)
                    <button onclick="checkOutGuest('{{ $bukuTamu->hash_id }}')" class="w-full px-4 py-3 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition font-medium text-left">
                        <i class="fas fa-sign-out-alt mr-3"></i>Check Out Tamu
                    </button>
                    @endif
                    
                    <a href="{{ route('perusahaan.buku-tamu.edit', $bukuTamu->hash_id) }}" class="block w-full px-4 py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-left">
                        <i class="fas fa-edit mr-3"></i>Edit Data Tamu
                    </a>
                    
                    <a 
                        href="{{ route('perusahaan.buku-tamu.qr-code', $bukuTamu->hash_id) }}" 
                        target="_blank"
                        class="block w-full px-4 py-3 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition font-medium text-left"
                    >
                        <i class="fas fa-qrcode mr-3"></i>Lihat QR Code
                    </a>
                    
                    <button onclick="window.print()" class="w-full px-4 py-3 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition font-medium text-left">
                        <i class="fas fa-print mr-3"></i>Cetak Detail
                    </button>
                    
                    <a href="{{ route('perusahaan.buku-tamu.index') }}" class="block w-full px-4 py-3 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium text-left">
                        <i class="fas fa-arrow-left mr-3"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data tamu akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/buku-tamu/${hashId}`;
            form.submit();
        }
    });
}

async function checkOutGuest(hashId) {
    const { value: catatan } = await Swal.fire({
        title: 'Check Out Tamu',
        input: 'textarea',
        inputLabel: 'Catatan (opsional)',
        inputPlaceholder: 'Tambahkan catatan untuk kunjungan ini...',
        showCancelButton: true,
        confirmButtonText: 'Check Out',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
    });

    if (catatan !== undefined) {
        try {
            const response = await fetch(`/perusahaan/buku-tamu/${hashId}/check-out`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ catatan: catatan })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Terjadi kesalahan saat check out'
            });
        }
    }
}

function downloadQrCode(hashId) {
    // Open QR code page in new window for download
    window.open(`/perusahaan/buku-tamu-qr/${hashId}`, '_blank');
}
</script>
@endpush