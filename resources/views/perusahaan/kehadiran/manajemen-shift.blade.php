@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Shift')
@section('page-title', 'Manajemen Shift')
@section('page-subtitle', 'Kelola shift kerja karyawan')

@section('content')
<div class="flex items-center justify-center min-h-[600px]">
    <div class="text-center">
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 shadow-2xl mb-6">
                <i class="fas fa-clock text-6xl text-white"></i>
            </div>
        </div>
        
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Under Construction</h2>
        <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto">
            Halaman <span class="font-semibold text-orange-600">Manajemen Shift</span> sedang dalam tahap pengembangan
        </p>
        
        <div class="flex items-center justify-center space-x-2 text-gray-500">
            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
        </div>
        
        <div class="mt-12">
            <a href="{{ route('perusahaan.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
