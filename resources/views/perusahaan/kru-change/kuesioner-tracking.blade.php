@extends('perusahaan.layouts.app')

@section('title', 'Tracking Kuesioner - Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Tracking Kuesioner Handover</h3>
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
            @if($kuesioners->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Kuesioner</h3>
                    <p class="text-gray-600">Tim {{ $kruChange->timKeluar->nama_tim }} tidak memiliki kuesioner yang perlu diisi.</p>
                    <div class="mt-6">
                        <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Kembali ke Detail Handover
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($kuesioners as $kuesioner)
                        @php
                            $kuesionerStatus = collect($kruChange->kuesioner_status ?? [])->firstWhere('id', $kuesioner->id);
                            $isCompleted = $kuesionerStatus && $kuesionerStatus['status'] === 'completed';
                        @endphp
                        
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $kuesioner->judul }}</h4>
                                        @if($kuesioner->deskripsi)
                                            <p class="text-sm text-gray-600 mt-1">{{ $kuesioner->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($isCompleted)
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                                <i class="fas fa-check mr-1"></i>Selesai
                                            </span>
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
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                            <div>
                                                <p class="font-medium text-green-800">Kuesioner Sudah Diisi</p>
                                                <p class="text-sm text-green-600">
                                                    Diselesaikan pada: {{ \Carbon\Carbon::parse($kuesionerStatus['completed_at'])->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('perusahaan.kru-change.submit-kuesioner-tracking', $kruChange->hash_id) }}" 
                                          method="POST" id="kuesioner-form-{{ $kuesioner->id }}">
                                        @csrf
                                        <input type="hidden" name="kuesioner_id" value="{{ $kuesioner->id }}">
                                        
                                        <div class="space-y-6">
                                            @foreach($kuesioner->pertanyaans as $index => $pertanyaan)
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
                                                                   placeholder="Masukkan jawaban..."
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
                                        </div>

                                        <div class="mt-6 flex justify-end space-x-3">
                                            <button type="button" 
                                                    onclick="confirmSubmitKuesioner({{ $kuesioner->id }}, '{{ $kuesioner->judul }}')"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                                                <i class="fas fa-check mr-2"></i>Simpan Kuesioner
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
function confirmSubmitKuesioner(kuesionerId, judul) {
    // Validate form first
    const form = document.getElementById(`kuesioner-form-${kuesionerId}`);
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    let emptyFields = [];

    requiredFields.forEach(field => {
        if (field.type === 'radio') {
            const radioGroup = form.querySelectorAll(`[name="${field.name}"]`);
            const isChecked = Array.from(radioGroup).some(radio => radio.checked);
            if (!isChecked) {
                isValid = false;
                const label = form.querySelector(`label[for="${field.name}"]`) || 
                             field.closest('.bg-gray-50').querySelector('label');
                if (label) {
                    emptyFields.push(label.textContent.replace('*', '').trim());
                }
            }
        } else if (!field.value.trim()) {
            isValid = false;
            const label = form.querySelector(`label[for="${field.name}"]`) || 
                         field.closest('.bg-gray-50').querySelector('label');
            if (label) {
                emptyFields.push(label.textContent.replace('*', '').trim());
            }
        }
    });

    if (!isValid) {
        Swal.fire({
            title: 'Form Belum Lengkap',
            html: `
                <div class="text-left">
                    <p class="mb-3">Mohon lengkapi pertanyaan wajib berikut:</p>
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

    Swal.fire({
        title: 'Simpan Kuesioner?',
        html: `
            <div class="text-left">
                <p class="mb-3"><strong>Kuesioner:</strong> ${judul}</p>
                <div class="bg-blue-50 p-3 rounded mb-3">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pastikan semua jawaban sudah benar sebelum menyimpan
                    </p>
                </div>
                <p class="text-sm text-gray-600">Setelah disimpan, kuesioner akan ditandai sebagai selesai dan tidak dapat diubah lagi.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-save mr-2"></i>Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menyimpan Kuesioner...',
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
</script>
@endpush