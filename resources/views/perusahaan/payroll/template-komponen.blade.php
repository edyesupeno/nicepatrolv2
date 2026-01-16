@extends('perusahaan.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="relative h-48" style="background: linear-gradient(135deg, #EC4899 0%, #DB2777 100%);">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-white bg-opacity-20 rounded-full mb-4 animate-pulse">
                            <i class="fas fa-file-contract text-white text-5xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-2">Template Komponen</h1>
                        <p class="text-pink-100 text-lg">Template Gaji Per Posisi</p>
                    </div>
                </div>
            </div>
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-yellow-50 rounded-full mb-6">
                    <i class="fas fa-hard-hat text-yellow-500 text-6xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Under Construction</h2>
                <p class="text-gray-600 text-lg mb-8">Fitur ini sedang dalam tahap pengembangan</p>
                <a href="{{ route('perusahaan.dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
