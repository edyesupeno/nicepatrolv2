@extends('perusahaan.layouts.app')

@section('title', $title ?? 'Under Construction')
@section('page-title', $pageTitle ?? 'Under Construction')
@section('page-subtitle', $pageSubtitle ?? 'Fitur ini sedang dalam tahap pengembangan')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="text-center">
        <!-- Construction Icon -->
        <div class="mb-8 inline-block">
            <div class="relative">
                <div class="w-32 h-32 mx-auto rounded-full flex items-center justify-center" 
                     style="background: linear-gradient(135deg, #FCD34D 0%, #F59E0B 100%);">
                    <i class="fas fa-hard-hat text-6xl text-white"></i>
                </div>
                <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-tools text-xl text-white"></i>
                </div>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Under Construction</h2>
        
        <!-- Description -->
        <p class="text-gray-600 text-lg mb-2">Fitur <strong>{{ $featureName ?? 'ini' }}</strong> sedang dalam tahap pengembangan</p>
        <p class="text-gray-500 text-sm mb-8">Kami sedang bekerja keras untuk menghadirkan fitur terbaik untuk Anda</p>

        <!-- Features Coming Soon -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8 max-w-md mx-auto">
            <h3 class="text-sm font-bold text-blue-900 mb-3 flex items-center justify-center">
                <i class="fas fa-rocket mr-2"></i>
                Yang Akan Datang
            </h3>
            <ul class="space-y-2 text-sm text-blue-800 text-left">
                @if(isset($features) && is_array($features))
                    @foreach($features as $feature)
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                @else
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                        <span>Interface yang user-friendly</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                        <span>Fitur lengkap dan mudah digunakan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mr-2 mt-0.5"></i>
                        <span>Performa optimal dan cepat</span>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 justify-center">
            <a href="{{ route('perusahaan.dashboard') }}" 
               class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" 
               style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            
            @if(isset($backUrl))
                <a href="{{ $backUrl }}" 
                   class="inline-flex items-center gap-2 px-8 py-3 bg-gray-500 text-white rounded-xl font-semibold hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            @endif
        </div>

        <!-- Progress Indicator -->
        <div class="mt-12 max-w-md mx-auto">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span>Progress Pengembangan</span>
                <span class="font-semibold">{{ $progress ?? '25' }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                     style="width: {{ $progress ?? '25' }}%"></div>
            </div>
        </div>
    </div>
</div>
@endsection
