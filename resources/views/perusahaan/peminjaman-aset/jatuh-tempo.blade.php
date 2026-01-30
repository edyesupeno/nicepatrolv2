@extends('perusahaan.layouts.app')

@section('title', 'Peminjaman Jatuh Tempo')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('perusahaan.peminjaman-aset.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Peminjaman Jatuh Tempo</h1>
                <p class="text-gray-600 mt-1">Monitor peminjaman yang akan jatuh tempo dan terlambat</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Akan Jatuh Tempo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Akan Jatuh Tempo (7 Hari)</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    {{ $akanJatuhTempo->count() }} peminjaman
                </span>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($akanJatuhTempo as $peminjaman)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-sm font-medium text-gray-900">{{ $peminjaman->kode_peminjaman }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $peminjaman->status_label }}
                                </span>
                            </div>
                            
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-box w-4 text-gray-400 mr-2"></i>
                                    <span>{{ $peminjaman->aset_nama }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user w-4 text-gray-400 mr-2"></i>
                                    <span>{{ $peminjaman->peminjam_nama }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-building w-4 text-gray-400 mr-2"></i>
                                    <span>{{ $peminjaman->project->nama ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right ml-4">
                            <div class="text-sm font-medium text-yellow-600">
                                {{ $peminjaman->tanggal_rencana_kembali->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $peminjaman->tanggal_rencana_kembali->diffInDays(now()) }} hari lagi
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('perusahaan.peminjaman-aset.show', $peminjaman->hash_id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-clock text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 font-medium">Tidak ada peminjaman yang akan jatuh tempo</p>
                    <p class="text-gray-400 text-sm">Semua peminjaman masih dalam batas waktu yang aman</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Terlambat -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Terlambat Dikembalikan</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $terlambat->count() }} peminjaman
                </span>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($terlambat as $peminjaman)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-sm font-medium text-gray-900">{{ $peminjaman->kode_peminjaman }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Terlambat
                                </span>
                            </div>
                            
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-box w-4 text-gray-400 mr-2"></i>
                                    <span>{{ $peminjaman->aset_nama }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user w-4 text-gray-400 mr-2"></i>
                                    <span>{{ $peminjaman->peminjam_nama }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-building w-4 text-gray-400 mr-2"></i>
                                    <span>{{ $peminjaman->project->nama ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right ml-4">
                            <div class="text-sm font-medium text-red-600">
                                {{ $peminjaman->tanggal_rencana_kembali->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-red-500 font-medium">
                                Lewat {{ abs($peminjaman->tanggal_rencana_kembali->diffInDays(now())) }} hari
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            @if($peminjaman->status_peminjaman === 'dipinjam')
                                <a href="{{ route('perusahaan.peminjaman-aset.return-form', $peminjaman->hash_id) }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                                    <i class="fas fa-undo mr-1"></i>
                                    Kembalikan
                                </a>
                            @endif
                        </div>
                        <a href="{{ route('perusahaan.peminjaman-aset.show', $peminjaman->hash_id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                    <p class="text-gray-500 font-medium">Tidak ada peminjaman yang terlambat</p>
                    <p class="text-gray-400 text-sm">Semua peminjaman dikembalikan tepat waktu</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Akan Jatuh Tempo</p>
                <p class="text-2xl font-bold text-gray-900">{{ $akanJatuhTempo->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Terlambat</p>
                <p class="text-2xl font-bold text-gray-900">{{ $terlambat->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-blue-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-gray-900">{{ $akanJatuhTempo->where('status_peminjaman', 'dipinjam')->count() + $terlambat->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Perlu Perhatian</p>
                <p class="text-2xl font-bold text-gray-900">{{ $akanJatuhTempo->count() + $terlambat->count() }}</p>
            </div>
        </div>
    </div>
</div>

@endsection