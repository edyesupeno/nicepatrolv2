@extends('perusahaan.layouts.app')

@section('title', 'Error Import Lembur')
@section('page-title', 'Error Import Lembur')
@section('page-subtitle', 'Detail error yang terjadi saat import data lembur')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Detail Error Import</h2>
            <p class="text-sm text-gray-600 mt-1">Berikut adalah daftar baris yang gagal diimport beserta alasan errornya</p>
        </div>
        <a href="{{ route('perusahaan.lembur.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Lembur
        </a>
    </div>
</div>

<!-- Summary -->
<div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
        <div>
            <h3 class="text-lg font-medium text-red-800">{{ count($importErrors) }} Baris Gagal Diimport</h3>
            <p class="text-sm text-red-700 mt-1">Silakan perbaiki data pada baris-baris berikut dan coba import ulang</p>
        </div>
    </div>
</div>

<!-- Error Details -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Detail Error</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baris</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($importErrors as $error)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Baris {{ $error['row'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-red-600 font-medium">{{ $error['error'] }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if(isset($error['data']) && is_array($error['data']))
                                    <div class="space-y-1">
                                        @foreach($error['data'] as $key => $value)
                                            @if($value)
                                                <div><span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">Data tidak tersedia</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Help Section -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-0.5"></i>
        <div>
            <h3 class="text-lg font-medium text-blue-800 mb-2">Tips Mengatasi Error</h3>
            <div class="text-sm text-blue-700 space-y-2">
                <div><strong>No Badge tidak ditemukan:</strong> Pastikan No Badge karyawan sudah terdaftar dan aktif di sistem</div>
                <div><strong>Project tidak ditemukan:</strong> Pastikan nama project sesuai dengan yang ada di sistem</div>
                <div><strong>Karyawan tidak terdaftar di project:</strong> Pastikan karyawan sudah diassign ke project yang dimaksud</div>
                <div><strong>Format tanggal/jam tidak valid:</strong> Gunakan format YYYY-MM-DD untuk tanggal dan HH:MM untuk jam</div>
                <div><strong>Durasi lembur tidak valid:</strong> Pastikan jam selesai lebih besar dari jam mulai dan durasi minimal 30 menit</div>
                <div><strong>Data sudah ada:</strong> Cek apakah data lembur untuk karyawan, tanggal, dan jam yang sama sudah pernah diinput</div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-blue-200">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-download mr-2"></i>
                    <a href="{{ route('perusahaan.lembur.template-download') }}" class="underline hover:no-underline">Download template Excel</a> 
                    untuk memastikan format data sudah benar.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection