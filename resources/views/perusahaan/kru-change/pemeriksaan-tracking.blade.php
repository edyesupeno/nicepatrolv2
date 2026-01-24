@extends('perusahaan.layouts.app')

@section('title', 'Tracking Pemeriksaan - Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Tracking Pemeriksaan Handover</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $kruChange->timKeluar->nama_tim }} → {{ $kruChange->timMasuk->nama_tim }} 
                    | {{ $kruChange->areaPatrol->nama }}
                </p>
            </div>
            <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="p-6">
            @if($pemeriksaans->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Pemeriksaan</h3>
                    <p class="text-gray-600">Tim {{ $kruChange->timKeluar->nama_tim }} tidak memiliki pemeriksaan yang perlu dilakukan.</p>
                    <div class="mt-6">
                        <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Kembali ke Detail Handover
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($pemeriksaans as $pemeriksaan)
                        @php
                            $pemeriksaanStatus = collect($kruChange->pemeriksaan_status ?? [])->firstWhere('id', $pemeriksaan->id);
                            $isCompleted = $pemeriksaanStatus && in_array($pemeriksaanStatus['status'], ['checked', 'failed']);
                        @endphp
                        
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $pemeriksaan->nama }}</h4>
                                        @if($pemeriksaan->deskripsi)
                                            <p class="text-sm text-gray-600 mt-1">{{ $pemeriksaan->deskripsi }}</p>
                                        @endif
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>Frekuensi: {{ ucfirst($pemeriksaan->frekuensi) }}
                                            </span>
                                            @if($pemeriksaan->estimasi_waktu)
                                                <span class="text-xs text-gray-500">
                                                    <i class="fas fa-stopwatch mr-1"></i>Estimasi: {{ $pemeriksaan->estimasi_waktu }} menit
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($isCompleted)
                                            @if($pemeriksaanStatus['status'] === 'checked')
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                                    <i class="fas fa-check mr-1"></i>Lulus
                                                </span>
                                            @else
                                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                                    <i class="fas fa-times mr-1"></i>Gagal
                                                </span>
                                            @endif
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                @if($isCompleted)
                                    <div class="bg-{{ $pemeriksaanStatus['status'] === 'checked' ? 'green' : 'red' }}-50 border border-{{ $pemeriksaanStatus['status'] === 'checked' ? 'green' : 'red' }}-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <i class="fas fa-{{ $pemeriksaanStatus['status'] === 'checked' ? 'check-circle text-green-500' : 'exclamation-triangle text-red-500' }} text-xl mr-3 mt-1"></i>
                                            <div class="flex-1">
                                                <p class="font-medium text-{{ $pemeriksaanStatus['status'] === 'checked' ? 'green' : 'red' }}-800">
                                                    Pemeriksaan {{ $pemeriksaanStatus['status'] === 'checked' ? 'Lulus' : 'Gagal' }}
                                                </p>
                                                <p class="text-sm text-{{ $pemeriksaanStatus['status'] === 'checked' ? 'green' : 'red' }}-600">
                                                    Diselesaikan pada: {{ \Carbon\Carbon::parse($pemeriksaanStatus['checked_at'])->format('d/m/Y H:i') }}
                                                </p>
                                                @if(!empty($pemeriksaanStatus['catatan']))
                                                    <div class="mt-2 p-2 bg-white rounded border">
                                                        <p class="text-sm font-medium text-gray-700">Catatan:</p>
                                                        <p class="text-sm text-gray-600">{{ $pemeriksaanStatus['catatan'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('perusahaan.kru-change.submit-pemeriksaan-tracking', $kruChange->hash_id) }}" 
                                          method="POST" id="pemeriksaan-form-{{ $pemeriksaan->id }}">
                                        @csrf
                                        <input type="hidden" name="pemeriksaan_id" value="{{ $pemeriksaan->id }}">
                                        
                                        <div class="space-y-6">
                                            @foreach($pemeriksaan->pertanyaans as $index => $pertanyaan)
                                                <div class="bg-gray-50 rounded-lg p-4">
                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium text-gray-900 mb-2">
                                                            {{ $index + 1 }}. {{ $pertanyaan->pertanyaan }}
                                                            @if($pertanyaan->is_required)
                                                                <span class="text-red-500">*</span>
                                                            @endif
                                                        </label>
                                                        
                                                        @if($pertanyaan->tipe_jawaban === 'text')
                                                            <input type="text" 
                                                                   name="jawaban[{{ $pertanyaan->id }}]"
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                                                   placeholder="Masukkan hasil pemeriksaan..."
                                                                   {{ $pertanyaan->is_required ? 'required' : '' }}>
                                                        
                                                        @elseif($pertanyaan->tipe_jawaban === 'pilihan')
                                                            @if($pertanyaan->opsi_jawaban)
                                                                <div class="space-y-2">
                                                                    @foreach($pertanyaan->opsi_jawaban as $pilihan)
                                                                        <label class="flex items-center">
                                                                            <input type="radio" 
                                                                                   name="jawaban[{{ $pertanyaan->id }}]"
                                                                                   value="{{ $pilihan }}"
                                                                                   class="mr-2 text-blue-600 focus:ring-blue-500"
                                                                                   {{ $pertanyaan->is_required ? 'required' : '' }}>
                                                                            <span class="text-sm text-gray-700">{{ $pilihan }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    
                                                    @if($pertanyaan->keterangan)
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            <i class="fas fa-info-circle mr-1"></i>{{ $pertanyaan->keterangan }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach

                                            <!-- Status Pemeriksaan -->
                                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                                <h5 class="font-medium text-blue-900 mb-3">Hasil Pemeriksaan</h5>
                                                <div class="space-y-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Status Pemeriksaan <span class="text-red-500">*</span>
                                                        </label>
                                                        <div class="space-y-2">
                                                            <label class="flex items-center">
                                                                <input type="radio" 
                                                                       name="status_pemeriksaan" 
                                                                       value="checked"
                                                                       class="mr-2 text-green-600 focus:ring-green-500"
                                                                       required>
                                                                <span class="text-sm text-gray-700">
                                                                    <i class="fas fa-check text-green-500 mr-1"></i>Lulus - Semua dalam kondisi baik
                                                                </span>
                                                            </label>
                                                            <label class="flex items-center">
                                                                <input type="radio" 
                                                                       name="status_pemeriksaan" 
                                                                       value="failed"
                                                                       class="mr-2 text-red-600 focus:ring-red-500"
                                                                       required>
                                                                <span class="text-sm text-gray-700">
                                                                    <i class="fas fa-times text-red-500 mr-1"></i>Gagal - Ada masalah yang ditemukan
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Catatan Pemeriksaan
                                                        </label>
                                                        <textarea name="catatan_pemeriksaan"
                                                                  rows="3"
                                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                                                  placeholder="Jelaskan hasil pemeriksaan, masalah yang ditemukan (jika ada), atau catatan penting lainnya..."></textarea>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            Wajib diisi jika status pemeriksaan "Gagal"
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end space-x-3">
                                            <button type="button" 
                                                    onclick="confirmSubmitPemeriksaan({{ $pemeriksaan->id }}, '{{ $pemeriksaan->nama }}')"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                                                <i class="fas fa-save mr-2"></i>Simpan Pemeriksaan
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmSubmitPemeriksaan(pemeriksaanId, nama) {
    // Validate form first
    const form = document.getElementById(`pemeriksaan-form-${pemeriksaanId}`);
    const requiredFields = form.querySelectorAll('[required]');
    const statusPemeriksaan = form.querySelector('input[name="status_pemeriksaan"]:checked');
    const catatanPemeriksaan = form.querySelector('textarea[name="catatan_pemeriksaan"]');
    
    let isValid = true;
    let emptyFields = [];

    // Check required fields
    requiredFields.forEach(field => {
        if (field.type === 'radio') {
            const radioGroup = form.querySelectorAll(`[name="${field.name}"]`);
            const isChecked = Array.from(radioGroup).some(radio => radio.checked);
            if (!isChecked) {
                isValid = false;
                const label = form.querySelector(`label[for="${field.name}"]`) || 
                             field.closest('.bg-gray-50, .bg-blue-50').querySelector('label');
                if (label) {
                    emptyFields.push(label.textContent.replace('*', '').trim());
                }
            }
        } else if (!field.value.trim()) {
            isValid = false;
            const label = form.querySelector(`label[for="${field.name}"]`) || 
                         field.closest('.bg-gray-50, .bg-blue-50').querySelector('label');
            if (label) {
                emptyFields.push(label.textContent.replace('*', '').trim());
            }
        }
    });

    // Check if catatan is required when status is failed
    if (statusPemeriksaan && statusPemeriksaan.value === 'failed' && !catatanPemeriksaan.value.trim()) {
        isValid = false;
        emptyFields.push('Catatan Pemeriksaan (wajib untuk status gagal)');
    }

    if (!isValid) {
        Swal.fire({
            title: 'Form Belum Lengkap',
            html: `
                <div class="text-left">
                    <p class="mb-3">Mohon lengkapi field berikut:</p>
                    <ul class="text-sm text-red-600 space-y-1">
                        ${emptyFields.map(field => `<li>• ${field}</li>`).join('')}
                    </ul>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    const statusText = statusPemeriksaan.value === 'checked' ? 'Lulus' : 'Gagal';
    const statusColor = statusPemeriksaan.value === 'checked' ? 'green' : 'red';

    Swal.fire({
        title: 'Simpan Hasil Pemeriksaan?',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Pemeriksaan:</strong> ${nama}</p>
                <div class="bg-${statusColor === 'green' ? 'green' : 'red'}-50 p-3 rounded mb-3">
                    <p class="text-sm text-${statusColor === 'green' ? 'green' : 'red'}-800">
                        <i class="fas fa-${statusColor === 'green' ? 'check' : 'exclamation-triangle'} mr-1"></i>
                        <strong>Status:</strong> ${statusText}
                    </p>
                    ${catatanPemeriksaan.value.trim() ? `
                        <p class="text-sm text-${statusColor === 'green' ? 'green' : 'red'}-700 mt-2">
                            <strong>Catatan:</strong> ${catatanPemeriksaan.value.trim()}
                        </p>
                    ` : ''}
                </div>
                <p class="text-sm text-gray-600">Setelah disimpan, pemeriksaan akan ditandai sebagai selesai dan tidak dapat diubah lagi.</p>
            </div>
        `,
        icon: statusPemeriksaan.value === 'checked' ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonColor: statusPemeriksaan.value === 'checked' ? '#16a34a' : '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-save mr-2"></i>Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menyimpan Pemeriksaan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}

// Auto-require catatan when status is failed
document.addEventListener('DOMContentLoaded', function() {
    const statusRadios = document.querySelectorAll('input[name="status_pemeriksaan"]');
    const catatanTextarea = document.querySelector('textarea[name="catatan_pemeriksaan"]');
    
    if (statusRadios.length && catatanTextarea) {
        statusRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'failed') {
                    catatanTextarea.setAttribute('required', 'required');
                    catatanTextarea.closest('div').querySelector('label').innerHTML = 
                        'Catatan Pemeriksaan <span class="text-red-500">*</span>';
                } else {
                    catatanTextarea.removeAttribute('required');
                    catatanTextarea.closest('div').querySelector('label').innerHTML = 'Catatan Pemeriksaan';
                }
            });
        });
    }
});
</script>
@endpush