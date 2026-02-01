@extends('perusahaan.layouts.app')

@section('title', $featureData['title'])
@section('page-title', $featureData['title'])
@section('page-subtitle', 'Fitur sedang dalam pengembangan')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 -m-8">
    <div class="max-w-2xl mx-auto text-center px-6">
        <!-- Main Icon -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-white rounded-full shadow-2xl mb-6">
                <i class="{{ $featureData['icon'] }} text-6xl text-{{ $featureData['color'] }}-500"></i>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            {{ $featureData['title'] }}
        </h1>

        <!-- Description -->
        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
            {{ $featureData['description'] }}
        </p>

        <!-- Under Construction Message -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="flex items-center justify-center mb-6">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-tools text-3xl text-orange-500 animate-bounce"></i>
                    <span class="text-2xl font-bold text-gray-800">Sedang Dikembangkan</span>
                </div>
            </div>
            
            <p class="text-gray-600 mb-6">
                Tim developer kami sedang bekerja keras untuk menghadirkan fitur terbaik untuk kebutuhan keuangan perusahaan Anda.
            </p>

            <!-- Features Coming Soon -->
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <i class="fas fa-check-circle text-blue-500 mb-2"></i>
                    <h4 class="font-semibold text-gray-800 mb-1">User-Friendly Interface</h4>
                    <p class="text-sm text-gray-600">Antarmuka yang mudah digunakan</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <i class="fas fa-shield-alt text-green-500 mb-2"></i>
                    <h4 class="font-semibold text-gray-800 mb-1">Keamanan Tinggi</h4>
                    <p class="text-sm text-gray-600">Sistem keamanan berlapis</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <i class="fas fa-chart-bar text-purple-500 mb-2"></i>
                    <h4 class="font-semibold text-gray-800 mb-1">Laporan Lengkap</h4>
                    <p class="text-sm text-gray-600">Analisis dan laporan detail</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <i class="fas fa-mobile-alt text-yellow-500 mb-2"></i>
                    <h4 class="font-semibold text-gray-800 mb-1">Mobile Responsive</h4>
                    <p class="text-sm text-gray-600">Akses dari berbagai perangkat</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress Pengembangan</span>
                    <span class="text-sm font-medium text-{{ $featureData['color'] }}-600">25%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-{{ $featureData['color'] }}-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: 25%"></div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-{{ $featureData['color'] }}-500"></i>
                    Estimasi Timeline
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Analisis & Desain</span>
                        <span class="text-green-600 font-medium">✓ Selesai</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Development Backend</span>
                        <span class="text-blue-600 font-medium">🔄 Sedang Berjalan</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Development Frontend</span>
                        <span class="text-gray-400">⏳ Menunggu</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Testing & Launch</span>
                        <span class="text-gray-400">⏳ Menunggu</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('perusahaan.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-{{ $featureData['color'] }}-600 hover:bg-{{ $featureData['color'] }}-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-home mr-2"></i>
                Kembali ke Dashboard
            </a>
            <button onclick="showNotificationRequest()" class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors duration-200">
                <i class="fas fa-bell mr-2"></i>
                Beritahu Saat Siap
            </button>
        </div>

        <!-- Contact Info -->
        <div class="mt-8 text-sm text-gray-500">
            <p>Ada pertanyaan? Hubungi tim support kami</p>
            <p class="mt-1">
                <i class="fas fa-envelope mr-1"></i>
                support@nicepatrol.com
            </p>
        </div>
    </div>
</div>

<script>
function showNotificationRequest() {
    Swal.fire({
        title: 'Notifikasi Fitur Baru',
        html: `
            <div class="text-left">
                <p class="mb-4">Kami akan memberitahu Anda ketika fitur <strong>{{ $featureData['title'] }}</strong> sudah siap digunakan.</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Notifikasi akan dikirim melalui email dan sistem internal.
                    </p>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, Beritahu Saya',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3B82C8'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Anda akan mendapat notifikasi ketika fitur ini siap.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Animate progress bar on load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const progressBar = document.querySelector('.bg-{{ $featureData["color"] }}-500');
        if (progressBar) {
            progressBar.style.width = '25%';
        }
    }, 500);
});
</script>
@endsection