@extends('perusahaan.layouts.app')

@section('title', 'Detail Maintenance - ' . $maintenanceAset->nomor_maintenance)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Maintenance</h1>
            <p class="text-gray-600 mt-1">{{ $maintenanceAset->nomor_maintenance }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('perusahaan.maintenance-aset.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if($maintenanceAset->status !== 'completed')
                <a href="{{ route('perusahaan.maintenance-aset.edit', $maintenanceAset->hash_id) }}" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Informasi Dasar
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Maintenance</label>
                            <p class="text-gray-900 font-medium">{{ $maintenanceAset->nomor_maintenance }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <p class="text-gray-900">{{ $maintenanceAset->project->nama ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset</label>
                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $maintenanceAset->asset_type)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset</label>
                            <p class="text-gray-900">{{ $maintenanceAset->asset_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Maintenance</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $maintenanceAset->jenis_maintenance == 'preventive' ? 'bg-green-100 text-green-800' : 
                                   ($maintenanceAset->jenis_maintenance == 'corrective' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ ucfirst($maintenanceAset->jenis_maintenance) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                            {!! $maintenanceAset->prioritas_badge !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            {!! $maintenanceAset->status_badge !!}
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
                            <p class="text-gray-900">{{ $maintenanceAset->createdBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar text-blue-600"></i>
                        Jadwal Maintenance
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Maintenance</label>
                            <p class="text-gray-900 font-medium">{{ $maintenanceAset->tanggal_maintenance->format('d F Y') }}</p>
                            @if($maintenanceAset->is_overdue)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mt-1">
                                    Terlambat
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                            <p class="text-gray-900">{{ $maintenanceAset->waktu_mulai ?? 'Tidak ditentukan' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                            <p class="text-gray-900">{{ $maintenanceAset->waktu_selesai ?? 'Belum selesai' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Durasi</label>
                            <p class="text-gray-900">{{ $maintenanceAset->estimasi_durasi ? $maintenanceAset->estimasi_durasi . ' menit' : 'Tidak ditentukan' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Interval Maintenance</label>
                            <p class="text-gray-900">{{ $maintenanceAset->interval_maintenance ? $maintenanceAset->interval_maintenance . ' hari' : 'Tidak berulang' }}</p>
                        </div>
                        @if($maintenanceAset->tanggal_maintenance_berikutnya)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Maintenance Berikutnya</label>
                            <p class="text-gray-900">{{ $maintenanceAset->tanggal_maintenance_berikutnya->format('d F Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Work Description -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        Deskripsi Pekerjaan
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-line">{{ $maintenanceAset->deskripsi_pekerjaan }}</p>
                            </div>
                        </div>
                        
                        @if($maintenanceAset->catatan_sebelum)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Sebelum Maintenance</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-line">{{ $maintenanceAset->catatan_sebelum }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceAset->catatan_sesudah)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Setelah Maintenance</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-line">{{ $maintenanceAset->catatan_sesudah }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personnel Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-users text-blue-600"></i>
                        Petugas Maintenance
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teknisi Internal</label>
                            <p class="text-gray-900">{{ $maintenanceAset->teknisi_internal ?? 'Tidak ada' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Eksternal</label>
                            <p class="text-gray-900">{{ $maintenanceAset->vendor_eksternal ?? 'Tidak ada' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Vendor</label>
                            <p class="text-gray-900">{{ $maintenanceAset->kontak_vendor ?? 'Tidak ada' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results (if completed) -->
            @if($maintenanceAset->status === 'completed')
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600"></i>
                        Hasil Maintenance
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hasil Maintenance</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $maintenanceAset->hasil_maintenance == 'berhasil' ? 'bg-green-100 text-green-800' : 
                                       ($maintenanceAset->hasil_maintenance == 'sebagian' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($maintenanceAset->hasil_maintenance) }}
                                </span>
                            </div>
                        </div>
                        
                        @if($maintenanceAset->masalah_ditemukan)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Masalah Ditemukan</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-line">{{ $maintenanceAset->masalah_ditemukan }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceAset->tindakan_dilakukan)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tindakan Dilakukan</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-line">{{ $maintenanceAset->tindakan_dilakukan }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceAset->rekomendasi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi</label>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-line">{{ $maintenanceAset->rekomendasi }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Photos -->
            @if($maintenanceAset->foto_sebelum || $maintenanceAset->foto_sesudah)
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-camera text-blue-600"></i>
                        Foto Maintenance
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($maintenanceAset->foto_sebelum)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Sebelum</label>
                            <div class="border rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($maintenanceAset->foto_sebelum) }}" 
                                     alt="Foto Sebelum" 
                                     class="w-full h-48 object-cover cursor-pointer"
                                     onclick="showImageModal('{{ Storage::url($maintenanceAset->foto_sebelum) }}', 'Foto Sebelum Maintenance')">
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceAset->foto_sesudah)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Sesudah</label>
                            <div class="border rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($maintenanceAset->foto_sesudah) }}" 
                                     alt="Foto Sesudah" 
                                     class="w-full h-48 object-cover cursor-pointer"
                                     onclick="showImageModal('{{ Storage::url($maintenanceAset->foto_sesudah) }}', 'Foto Sesudah Maintenance')">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Actions -->
            @if($maintenanceAset->status !== 'completed' && $maintenanceAset->status !== 'cancelled')
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Status</h3>
                    
                    <div class="space-y-3">
                        @if($maintenanceAset->status === 'scheduled')
                            <button onclick="updateStatus('{{ $maintenanceAset->hash_id }}', 'in_progress')" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-play"></i> Mulai Maintenance
                            </button>
                        @elseif($maintenanceAset->status === 'in_progress')
                            <button onclick="completeMaintenanceModal('{{ $maintenanceAset->hash_id }}')" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i> Selesaikan
                            </button>
                        @endif
                        
                        <button onclick="updateStatus('{{ $maintenanceAset->hash_id }}', 'cancelled')" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i> Batalkan
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cost Summary -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-money-bill text-green-600"></i>
                        Ringkasan Biaya
                    </h3>
                    
                    @if($maintenanceAset->status === 'completed')
                        <!-- Biaya Real (setelah selesai) -->
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                Biaya Real (Setelah Maintenance)
                            </h4>
                            <div class="space-y-2 bg-green-50 p-3 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Biaya Sparepart:</span>
                                    <span class="font-medium text-green-700">Rp {{ number_format($maintenanceAset->biaya_sparepart, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Biaya Jasa:</span>
                                    <span class="font-medium text-green-700">Rp {{ number_format($maintenanceAset->biaya_jasa, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Biaya Lainnya:</span>
                                    <span class="font-medium text-green-700">Rp {{ number_format($maintenanceAset->biaya_lainnya, 0, ',', '.') }}</span>
                                </div>
                                <hr class="border-green-200">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total Biaya Real:</span>
                                    <span class="text-green-600">{{ $maintenanceAset->formatted_total_biaya }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Estimasi Biaya (sebelum selesai) -->
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-calculator text-blue-500"></i>
                                Estimasi Biaya
                            </h4>
                            <div class="space-y-2 bg-blue-50 p-3 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Estimasi Sparepart:</span>
                                    <span class="font-medium text-blue-700">Rp {{ number_format($maintenanceAset->biaya_sparepart, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Estimasi Jasa:</span>
                                    <span class="font-medium text-blue-700">Rp {{ number_format($maintenanceAset->biaya_jasa, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Estimasi Lainnya:</span>
                                    <span class="font-medium text-blue-700">Rp {{ number_format($maintenanceAset->biaya_lainnya, 0, ',', '.') }}</span>
                                </div>
                                <hr class="border-blue-200">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total Estimasi:</span>
                                    <span class="text-blue-600">{{ $maintenanceAset->formatted_total_biaya }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle"></i>
                                Biaya real akan diinput saat maintenance selesai
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reminder Settings -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-bell text-yellow-600"></i>
                        Pengaturan Reminder
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status Reminder:</span>
                            <span class="font-medium">
                                @if($maintenanceAset->reminder_aktif)
                                    <span class="text-green-600">Aktif</span>
                                @else
                                    <span class="text-gray-500">Tidak Aktif</span>
                                @endif
                            </span>
                        </div>
                        
                        @if($maintenanceAset->reminder_aktif)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reminder:</span>
                            <span class="font-medium">{{ $maintenanceAset->reminder_hari }} hari sebelum</span>
                        </div>
                        @endif
                        
                        @if($maintenanceAset->reminder_terakhir)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reminder Terakhir:</span>
                            <span class="font-medium">{{ $maintenanceAset->reminder_terakhir->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Documents -->
            @if($maintenanceAset->dokumen_pendukung || $maintenanceAset->invoice_pembayaran)
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-file text-blue-600"></i>
                        Dokumen
                    </h3>
                    
                    <div class="space-y-3">
                        @if($maintenanceAset->dokumen_pendukung)
                        <div>
                            <a href="{{ Storage::url($maintenanceAset->dokumen_pendukung) }}" 
                               target="_blank"
                               class="flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-file-alt"></i>
                                <span>Dokumen Pendukung</span>
                            </a>
                        </div>
                        @endif
                        
                        @if($maintenanceAset->invoice_pembayaran)
                        <div>
                            <a href="{{ Storage::url($maintenanceAset->invoice_pembayaran) }}" 
                               target="_blank"
                               class="flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-receipt"></i>
                                <span>Invoice Pembayaran</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Complete Maintenance Modal -->
<div id="completeMaintenanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Selesaikan Maintenance</h3>
                <form id="completeMaintenanceForm" enctype="multipart/form-data">
                    <input type="hidden" id="maintenanceId" name="maintenance_id">
                    <input type="hidden" name="status" value="completed">
                    
                    <!-- Hasil Maintenance -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            Hasil Maintenance
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Maintenance <span class="text-red-500">*</span></label>
                                <select name="hasil_maintenance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="berhasil">Berhasil</option>
                                    <option value="sebagian">Sebagian Berhasil</option>
                                    <option value="gagal">Gagal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Biaya Real -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-green-600"></i>
                            Biaya Real Maintenance
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Input biaya yang sebenarnya dikeluarkan untuk maintenance ini.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Sparepart (Rp)</label>
                                <input type="number" name="biaya_sparepart_real" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       min="0" step="1000" placeholder="0">
                                <p class="text-xs text-gray-500 mt-1">Biaya sparepart yang benar-benar digunakan</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Jasa (Rp)</label>
                                <input type="number" name="biaya_jasa_real" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       min="0" step="1000" placeholder="0">
                                <p class="text-xs text-gray-500 mt-1">Biaya jasa teknisi/vendor yang dibayar</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Lainnya (Rp)</label>
                                <input type="number" name="biaya_lainnya_real" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       min="0" step="1000" placeholder="0">
                                <p class="text-xs text-gray-500 mt-1">Biaya lain-lain (transport, makan, dll)</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Catatan dan Detail -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                            Detail Pekerjaan
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Hasil</label>
                                <textarea name="catatan_sesudah" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan hasil maintenance..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Masalah Ditemukan</label>
                                <textarea name="masalah_ditemukan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masalah yang ditemukan selama maintenance..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tindakan Dilakukan</label>
                                <textarea name="tindakan_dilakukan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tindakan yang dilakukan..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rekomendasi</label>
                                <textarea name="rekomendasi" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Rekomendasi untuk maintenance selanjutnya..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-paperclip text-purple-600"></i>
                            Lampiran
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Sesudah</label>
                                <input type="file" name="foto_sesudah" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Foto kondisi aset setelah maintenance</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Pembayaran</label>
                                <input type="file" name="invoice_pembayaran" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Invoice atau bukti pembayaran</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeCompleteModal()" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                            <i class="fas fa-check"></i> Selesaikan Maintenance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="imageModalTitle" class="text-lg font-medium text-gray-900"></h3>
                    <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="text-center">
                    <img id="imageModalImg" src="" alt="" class="max-w-full max-h-96 mx-auto">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(maintenanceId, status) {
    let title = 'Konfirmasi';
    let text = 'Apakah Anda yakin ingin mengubah status maintenance?';
    
    if (status === 'cancelled') {
        title = 'Batalkan Maintenance';
        text = 'Apakah Anda yakin ingin membatalkan maintenance ini?';
    }
    
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/maintenance-aset/${maintenanceId}/update-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
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
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengubah status'
                });
            });
        }
    });
}

function completeMaintenanceModal(maintenanceId) {
    document.getElementById('maintenanceId').value = maintenanceId;
    document.getElementById('completeMaintenanceModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeMaintenanceModal').classList.add('hidden');
}

function showImageModal(src, title) {
    document.getElementById('imageModalImg').src = src;
    document.getElementById('imageModalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

document.getElementById('completeMaintenanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const maintenanceId = formData.get('maintenance_id');
    
    fetch(`/perusahaan/maintenance-aset/${maintenanceId}/update-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCompleteModal();
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
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyelesaikan maintenance'
        });
    });
});
</script>
@endpush