@extends('perusahaan.layouts.app')

@section('title', 'Detail Rekening')
@section('page-title', 'Detail Rekening')
@section('page-subtitle', $rekening->nama_rekening)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Card Header with Color -->
        <div class="h-3" style="background-color: {{ $rekening->warna_card }}"></div>
        
        <!-- Card Content -->
        <div class="p-8">
            <!-- Header Section -->
            <div class="flex items-start justify-between mb-8">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $rekening->nama_rekening }}</h1>
                        @if($rekening->is_primary)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i>Rekening Utama
                            </span>
                        @endif
                    </div>
                    <p class="text-lg text-gray-600">{{ $rekening->project->nama }}</p>
                    <p class="text-sm text-gray-500">{{ $rekening->perusahaan->nama }}</p>
                </div>
                
                <!-- Status Badge -->
                <div class="text-right">
                    @if($rekening->is_active)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i>Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Bank Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                        Informasi Bank
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" 
                                 style="background-color: {{ $rekening->warna_card }}20">
                                <i class="fas fa-university text-xl" style="color: {{ $rekening->warna_card }}"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Nama Bank</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $rekening->nama_bank }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" 
                                 style="background-color: {{ $rekening->warna_card }}20">
                                <i class="fas fa-credit-card text-xl" style="color: {{ $rekening->warna_card }}"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Nomor Rekening</p>
                                <p class="text-lg font-semibold text-gray-900 font-mono">{{ $rekening->nomor_rekening }}</p>
                                <p class="text-sm text-gray-400">Masked: {{ $rekening->formatted_nomor_rekening }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" 
                                 style="background-color: {{ $rekening->warna_card }}20">
                                <i class="fas fa-user text-xl" style="color: {{ $rekening->warna_card }}"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Nama Pemilik</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $rekening->nama_pemilik }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" 
                                 style="background-color: {{ $rekening->warna_card }}20">
                                <i class="fas fa-tag text-xl" style="color: {{ $rekening->warna_card }}"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jenis Rekening</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                                      style="background-color: {{ $rekening->warna_card }}20; color: {{ $rekening->warna_card }}">
                                    {{ $rekening->jenis_rekening_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance Information -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                        Informasi Saldo
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Saldo Awal -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Saldo Awal</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $rekening->formatted_saldo_awal }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Saldo Saat Ini -->
                        <div class="rounded-lg p-6" style="background-color: {{ $rekening->warna_card }}10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm mb-1" style="color: {{ $rekening->warna_card }}">Saldo Saat Ini</p>
                                    <p class="text-3xl font-bold" style="color: {{ $rekening->warna_card }}">{{ $rekening->formatted_saldo_saat_ini }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $rekening->mata_uang }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-lg flex items-center justify-center" 
                                     style="background-color: {{ $rekening->warna_card }}20">
                                    <i class="fas fa-wallet text-2xl" style="color: {{ $rekening->warna_card }}"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Selisih -->
                        @php
                            $selisih = $rekening->saldo_saat_ini - $rekening->saldo_awal;
                            $isPositive = $selisih >= 0;
                        @endphp
                        <div class="bg-{{ $isPositive ? 'green' : 'red' }}-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-{{ $isPositive ? 'arrow-up' : 'arrow-down' }} text-{{ $isPositive ? 'green' : 'red' }}-600 mr-2"></i>
                                <div>
                                    <p class="text-sm text-{{ $isPositive ? 'green' : 'red' }}-700">
                                        {{ $isPositive ? 'Peningkatan' : 'Penurunan' }} Saldo
                                    </p>
                                    <p class="text-lg font-semibold text-{{ $isPositive ? 'green' : 'red' }}-800">
                                        {{ $isPositive ? '+' : '' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            @if($rekening->keterangan)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Keterangan</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 leading-relaxed">{{ $rekening->keterangan }}</p>
                    </div>
                </div>
            @endif

            <!-- Metadata -->
            <div class="border-t border-gray-200 pt-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Dibuat</p>
                        <p class="text-sm font-medium text-gray-900">{{ $rekening->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $rekening->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Terakhir Diperbarui</p>
                        <p class="text-sm font-medium text-gray-900">{{ $rekening->updated_at->format('d M Y, H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $rekening->updated_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Warna Card</p>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded border border-gray-200" style="background-color: {{ $rekening->warna_card }}"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $rekening->warna_card_name }}</span>
                            <span class="text-xs text-gray-400 font-mono">{{ $rekening->warna_card }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between">
        <a href="{{ route('perusahaan.keuangan.rekening.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar
        </a>

        <div class="flex space-x-3">
            @if(!$rekening->is_primary)
                <button onclick="setPrimary('{{ $rekening->hash_id }}')" 
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-star mr-2"></i>
                    Set sebagai Primary
                </button>
            @endif
            
            <button onclick="toggleStatus('{{ $rekening->hash_id }}', {{ $rekening->is_active ? 'false' : 'true' }})" 
                    class="inline-flex items-center px-4 py-2 bg-{{ $rekening->is_active ? 'red' : 'green' }}-600 hover:bg-{{ $rekening->is_active ? 'red' : 'green' }}-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-{{ $rekening->is_active ? 'times' : 'check' }}-circle mr-2"></i>
                {{ $rekening->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
            
            <a href="{{ route('perusahaan.keuangan.rekening.edit', $rekening->hash_id) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>
                Edit Rekening
            </a>
            
            <button onclick="deleteRekening('{{ $rekening->hash_id }}', '{{ $rekening->nama_rekening }}')" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
function toggleStatus(hashId, newStatus) {
    const action = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: `Konfirmasi ${action.charAt(0).toUpperCase() + action.slice(1)}`,
        text: `Apakah Anda yakin ingin ${action} rekening ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82C8',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `Ya, ${action.charAt(0).toUpperCase() + action.slice(1)}`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/keuangan/rekening/${hashId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
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
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    });
}

function setPrimary(hashId) {
    Swal.fire({
        title: 'Set Rekening Utama',
        text: 'Apakah Anda yakin ingin menjadikan rekening ini sebagai rekening utama?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82C8',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Set Primary',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/keuangan/rekening/${hashId}/set-primary`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
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
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    });
}

function deleteRekening(hashId, namaRekening) {
    Swal.fire({
        title: 'Hapus Rekening',
        html: `Apakah Anda yakin ingin menghapus rekening <strong>${namaRekening}</strong>?<br><br><small class="text-red-600">Tindakan ini tidak dapat dibatalkan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/keuangan/rekening/${hashId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection