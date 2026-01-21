@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a 
                href="{{ route('perusahaan.patrol.pertanyaan-tamu') }}"
                class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition"
            >
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Preview Kuesioner</h1>
        </div>
        <p class="text-gray-600">Preview tampilan kuesioner tamu seperti yang akan dilihat oleh tamu</p>
    </div>

    <!-- Preview Card -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $kuesionerTamu->judul }}</h2>
                        <div class="flex items-center gap-4 mt-2 text-blue-100">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-building text-sm"></i>
                                {{ $kuesionerTamu->project->nama }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-map-marker-alt text-sm"></i>
                                {{ $kuesionerTamu->area->nama }}
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($kuesionerTamu->deskripsi)
                <div class="mt-4 p-4 bg-white bg-opacity-10 rounded-lg">
                    <p class="text-blue-50">{{ $kuesionerTamu->deskripsi }}</p>
                </div>
                @endif
            </div>

            <!-- Form Preview -->
            <div class="p-6">
                @if($kuesionerTamu->pertanyaans->count() > 0)
                    <form class="space-y-6">
                        @foreach($kuesionerTamu->pertanyaans as $pertanyaan)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start gap-3 mb-3">
                                <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold flex-shrink-0">
                                    {{ $pertanyaan->urutan }}
                                </span>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">
                                        {{ $pertanyaan->pertanyaan }}
                                        @if($pertanyaan->is_required)
                                            <span class="text-red-500 ml-1">*</span>
                                        @endif
                                    </h4>
                                    @if($pertanyaan->is_required)
                                        <p class="text-xs text-red-600">Wajib diisi</p>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-11">
                                @if($pertanyaan->tipe_jawaban === 'pilihan')
                                    <div class="space-y-2">
                                        @foreach($pertanyaan->opsi_jawaban as $index => $opsi)
                                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                            <input 
                                                type="radio" 
                                                name="pertanyaan_{{ $pertanyaan->id }}" 
                                                value="{{ $opsi }}"
                                                class="text-blue-600 focus:ring-blue-500"
                                                disabled
                                            >
                                            <span class="text-gray-700">{{ $opsi }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                @else
                                    <textarea 
                                        name="pertanyaan_{{ $pertanyaan->id }}"
                                        rows="3"
                                        placeholder="Tulis jawaban Anda..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        disabled
                                    ></textarea>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        <!-- Submit Button Preview -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $kuesionerTamu->pertanyaans->where('is_required', true)->count() }} pertanyaan wajib diisi
                            </div>
                            <button 
                                type="button"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition disabled:opacity-50"
                                disabled
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim Jawaban
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-question-circle text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pertanyaan</h3>
                        <p class="text-gray-600 mb-6">Tambahkan pertanyaan untuk melihat preview kuesioner</p>
                        <a 
                            href="{{ route('perusahaan.patrol.pertanyaan-tamu.kelola', $kuesionerTamu->hash_id) }}"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center gap-2"
                        >
                            <i class="fas fa-plus"></i>
                            Tambah Pertanyaan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Footer Info -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-question-circle"></i>
                            {{ $kuesionerTamu->pertanyaans->count() }} Pertanyaan
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                            {{ $kuesionerTamu->pertanyaans->where('is_required', true)->count() }} Wajib
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock"></i>
                            Estimasi {{ ceil($kuesionerTamu->pertanyaans->count() * 0.5) }} menit
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('perusahaan.patrol.pertanyaan-tamu.kelola', $kuesionerTamu->hash_id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                        >
                            <i class="fas fa-edit mr-1"></i>
                            Edit Pertanyaan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-center gap-4 mt-6">
            <a 
                href="{{ route('perusahaan.patrol.pertanyaan-tamu') }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
            <a 
                href="{{ route('perusahaan.patrol.pertanyaan-tamu.kelola', $kuesionerTamu->hash_id) }}"
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition"
            >
                <i class="fas fa-cogs mr-2"></i>
                Kelola Pertanyaan
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom radio button styling for preview */
input[type="radio"]:disabled {
    opacity: 0.6;
}

.preview-form label:hover {
    background-color: #f9fafb;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .bg-gradient-to-r.from-blue-600.to-blue-800 .flex.items-center.gap-4 {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .ml-11 {
        margin-left: 0;
        margin-top: 0.75rem;
    }
}
</style>
@endpush
@endsection