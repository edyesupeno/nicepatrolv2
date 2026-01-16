@extends('perusahaan.layouts.app')

@section('title', 'Status Karyawan')
@section('page-title', 'Status Karyawan')
@section('page-subtitle', 'Daftar status karyawan yang tersedia')

@section('content')
<!-- Info Card -->
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-info-circle text-white"></i>
        </div>
        <div>
            <h3 class="font-semibold text-gray-900 mb-1">Informasi Status Karyawan</h3>
            <p class="text-sm text-gray-600">Status karyawan digunakan untuk mengkategorikan jenis kepegawaian. Status ini akan digunakan saat menambahkan karyawan baru ke dalam sistem.</p>
        </div>
    </div>
</div>

<!-- Stats Card -->
<div class="mb-6">
    <div class="bg-white px-6 py-4 rounded-xl shadow-sm border border-gray-100 inline-flex items-center gap-3">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-tag text-white text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Status</p>
            <p class="text-2xl font-bold" style="color: #3B82C8;">{{ count($statuses) }}</p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-20">
                        <i class="fas fa-hashtag mr-2" style="color: #3B82C8;"></i>No
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Nama Status
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-32">
                        <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Status
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($statuses as $status)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold text-white" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                            {{ $status['id'] }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-tag text-sm" style="color: #3B82C8;"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $status['nama'] }}</p>
                                <p class="text-xs text-gray-500">Status kepegawaian</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center">
                            @if($status['is_active'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Additional Info -->
<div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-4">
    <div class="flex items-start gap-3">
        <i class="fas fa-lightbulb text-yellow-500 text-xl mt-1"></i>
        <div>
            <h4 class="font-semibold text-gray-900 mb-2">Catatan Penting:</h4>
            <ul class="space-y-1 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <i class="fas fa-circle text-xs mt-1.5" style="color: #3B82C8;"></i>
                    <span>Status karyawan ini bersifat <strong>read-only</strong> dan tidak dapat diubah</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-circle text-xs mt-1.5" style="color: #3B82C8;"></i>
                    <span>Pilih status yang sesuai saat menambahkan karyawan baru</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-circle text-xs mt-1.5" style="color: #3B82C8;"></i>
                    <span>Status akan mempengaruhi hak dan kewajiban karyawan dalam sistem</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
