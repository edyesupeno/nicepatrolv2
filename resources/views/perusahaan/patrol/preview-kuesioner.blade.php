<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kuesionerPatroli->judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8 text-white text-center rounded-t-2xl">
                <div class="mb-4">
                    <i class="fas fa-clipboard-list text-6xl"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">{{ $kuesionerPatroli->judul }}</h1>
                <p class="text-blue-100 text-sm">{{ $kuesionerPatroli->deskripsi }}</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                @if($kuesionerPatroli->pertanyaans->count() > 0)
                    <div class="space-y-6">
                        @foreach($kuesionerPatroli->pertanyaans as $pertanyaan)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="mb-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                                        Pertanyaan {{ $pertanyaan->urutan }}
                                        @if($pertanyaan->is_required)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <p class="text-gray-900">{{ $pertanyaan->pertanyaan }}</p>
                                </div>

                                @if($pertanyaan->tipe_jawaban === 'pilihan')
                                    <!-- Pilihan Jawaban -->
                                    <div class="space-y-2">
                                        @foreach($pertanyaan->opsi_jawaban as $index => $opsi)
                                            <button 
                                                type="button"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg text-left hover:border-blue-500 hover:bg-blue-50 transition flex items-center justify-between group"
                                            >
                                                <span class="text-gray-700 group-hover:text-blue-700 font-medium">
                                                    @if($index === 0)
                                                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                                    @else
                                                        <i class="fas fa-times-circle mr-2 text-red-500"></i>
                                                    @endif
                                                    {{ $opsi }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Text Input -->
                                    <textarea 
                                        rows="3"
                                        placeholder="Petugas akan mengisi jawaban di sini..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        disabled
                                    ></textarea>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button 
                            type="button"
                            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-900 transition"
                            disabled
                        >
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Laporan
                        </button>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada pertanyaan dalam kuesioner ini</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 text-center border-t rounded-b-2xl">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ini adalah preview kuesioner. Tombol tidak berfungsi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
