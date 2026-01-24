@extends('perusahaan.layouts.app')

@section('title', 'Laporan Insiden')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Insiden</h1>
            <p class="text-gray-600 mt-1">Laporan insiden keamanan dari patroli mandiri</p>
        </div>
    </div>

    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-exclamation-triangle text-gray-400 text-6xl mb-6"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Fitur Laporan Insiden Belum Tersedia</h3>
                
                @if(session('info'))
                    <p class="text-gray-600 mb-6 max-w-md">{{ session('info') }}</p>
                @elseif(session('error'))
                    <p class="text-red-600 mb-6 max-w-md">{{ session('error') }}</p>
                @else
                    <p class="text-gray-600 mb-6 max-w-md">
                        Fitur laporan insiden memerlukan modul Patroli Mandiri untuk dapat berfungsi. 
                        Silakan hubungi administrator untuk mengaktifkan fitur ini.
                    </p>
                @endif

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-lg">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 mr-3 mt-0.5"></i>
                        <div class="text-left">
                            <p class="text-sm font-medium text-blue-800 mb-2">Fitur yang akan tersedia:</p>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Daftar laporan insiden keamanan</li>
                                <li>• Detail insiden dengan foto dan lokasi GPS</li>
                                <li>• Filter berdasarkan prioritas dan status</li>
                                <li>• Export laporan ke PDF</li>
                                <li>• Tracking status penanganan insiden</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('perusahaan.dashboard') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection