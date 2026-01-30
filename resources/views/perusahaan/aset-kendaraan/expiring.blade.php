@extends('perusahaan.layouts.app')

@section('title', 'Dokumen Kendaraan yang Akan Expired')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.aset-kendaraan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Aset Kendaraan
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Dokumen Kendaraan yang Akan Expired</h1>
                <p class="text-gray-600 mt-1">Dokumen yang akan expired dalam 30 hari ke depan</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ $expiringSoon->count() }} Dokumen
                </span>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if($expiringSoon->count() > 0)
            <div class="space-y-4">
                @foreach($expiringSoon as $kendaraan)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $kendaraan->kode_kendaraan }}</h3>
                                    <span class="text-sm text-gray-500">{{ $kendaraan->merk }} {{ $kendaraan->model }}</span>
                                    <span class="text-sm font-mono text-gray-600">{{ $kendaraan->nomor_polisi }}</span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-building mr-1"></i>{{ $kendaraan->project->nama ?? 'Tidak ada project' }}
                                </div>

                                <!-- Expiring Documents -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- STNK -->
                                    @if($kendaraan->tanggal_berlaku_stnk)
                                        @php
                                            $stnkDaysLeft = floor(now()->diffInDays($kendaraan->tanggal_berlaku_stnk, false));
                                        @endphp
                                        
                                        @if($stnkDaysLeft <= 30)
                                            <div class="bg-blue-50 p-3 rounded-lg">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-blue-900">STNK</span>
                                                    @if($stnkDaysLeft < 0)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Lewat {{ abs($stnkDaysLeft) }} hari
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ $stnkDaysLeft }} hari lagi
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600">{{ $kendaraan->tanggal_berlaku_stnk->format('d/m/Y') }}</p>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Asuransi -->
                                    @if($kendaraan->tanggal_berlaku_asuransi)
                                        @php
                                            $asuransiDaysLeft = floor(now()->diffInDays($kendaraan->tanggal_berlaku_asuransi, false));
                                        @endphp
                                        
                                        @if($asuransiDaysLeft <= 30)
                                            <div class="bg-purple-50 p-3 rounded-lg">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-purple-900">Asuransi</span>
                                                    @if($asuransiDaysLeft < 0)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Lewat {{ abs($asuransiDaysLeft) }} hari
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ $asuransiDaysLeft }} hari lagi
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600">{{ $kendaraan->tanggal_berlaku_asuransi->format('d/m/Y') }}</p>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Pajak -->
                                    @if($kendaraan->jatuh_tempo_pajak)
                                        @php
                                            $pajakDaysLeft = floor(now()->diffInDays($kendaraan->jatuh_tempo_pajak, false));
                                        @endphp
                                        
                                        @if($pajakDaysLeft <= 30)
                                            <div class="bg-orange-50 p-3 rounded-lg">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-orange-900">Pajak</span>
                                                    @if($pajakDaysLeft < 0)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Lewat {{ abs($pajakDaysLeft) }} hari
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ $pajakDaysLeft }} hari lagi
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600">{{ $kendaraan->jatuh_tempo_pajak->format('d/m/Y') }}</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('perusahaan.aset-kendaraan.show', $kendaraan->hash_id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors text-center">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                <a href="{{ route('perusahaan.aset-kendaraan.edit', $kendaraan->hash_id) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors text-center">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Statistics -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $stnkExpiring = $expiringSoon->filter(function($k) {
                        return $k->tanggal_berlaku_stnk && floor(now()->diffInDays($k->tanggal_berlaku_stnk, false)) <= 30;
                    })->count();
                    
                    $asuransiExpiring = $expiringSoon->filter(function($k) {
                        return $k->tanggal_berlaku_asuransi && floor(now()->diffInDays($k->tanggal_berlaku_asuransi, false)) <= 30;
                    })->count();
                    
                    $pajakExpiring = $expiringSoon->filter(function($k) {
                        return $k->jatuh_tempo_pajak && floor(now()->diffInDays($k->jatuh_tempo_pajak, false)) <= 30;
                    })->count();
                @endphp

                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stnkExpiring }}</div>
                    <div class="text-sm text-blue-800">STNK Akan Expired</div>
                </div>

                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $asuransiExpiring }}</div>
                    <div class="text-sm text-purple-800">Asuransi Akan Expired</div>
                </div>

                <div class="bg-orange-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $pajakExpiring }}</div>
                    <div class="text-sm text-orange-800">Pajak Akan Expired</div>
                </div>
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-4xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Semua Dokumen Masih Valid</h3>
                <p class="text-gray-600 mb-6">Tidak ada dokumen kendaraan yang akan expired dalam 30 hari ke depan.</p>
                <a href="{{ route('perusahaan.aset-kendaraan.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Kendaraan
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Auto refresh setiap 5 menit untuk update status
setInterval(function() {
    location.reload();
}, 300000); // 5 minutes
</script>
@endpush