@extends('perusahaan.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
        <!-- Under Construction Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header with Gradient -->
            <div class="relative h-48" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-white bg-opacity-20 rounded-full mb-4 animate-pulse">
                            <i class="fas fa-money-bill-wave text-white text-5xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-2">Payroll</h1>
                        <p class="text-purple-100 text-lg">Sistem Penggajian Karyawan</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-12 text-center">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-32 h-32 bg-yellow-50 rounded-full mb-6">
                        <i class="fas fa-hard-hat text-yellow-500 text-6xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Under Construction</h2>
                    <p class="text-gray-600 text-lg mb-2">Fitur ini sedang dalam tahap pengembangan</p>
                    <p class="text-gray-500">Kami sedang membangun sesuatu yang luar biasa untuk Anda!</p>
                </div>
                
                <!-- Features Preview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-left">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-rocket text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-blue-900 mb-1">Generate Payroll</h3>
                                <p class="text-sm text-blue-700">Otomatis hitung gaji berdasarkan kehadiran</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-left">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-green-900 mb-1">Daftar Payroll</h3>
                                <p class="text-sm text-green-700">Kelola dan review slip gaji karyawan</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-left">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-cog text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-purple-900 mb-1">Setting Payroll</h3>
                                <p class="text-sm text-purple-700">Konfigurasi komponen dan template</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 text-left">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-orange-900 mb-1">Template Karyawan</h3>
                                <p class="text-sm text-orange-700">Atur template gaji per karyawan</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <a href="{{ route('perusahaan.dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900 mb-1">Informasi Pengembangan</p>
                    <p class="text-xs text-blue-700">Fitur Payroll akan segera hadir dengan kemampuan untuk mengelola penggajian karyawan secara otomatis berdasarkan data kehadiran, komponen gaji, dan template yang dapat dikustomisasi.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
