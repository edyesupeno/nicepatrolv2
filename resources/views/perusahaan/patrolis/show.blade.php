@extends('perusahaan.layouts.app')

@section('title', 'Detail Patroli')
@section('page-title', 'Detail Patroli')
@section('page-subtitle', 'Informasi lengkap patroli')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.patrolis.index') }}" class="inline-flex items-center text-sky-600 hover:text-sky-800 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Patroli
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Informasi Patroli -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Info Umum -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-info-circle text-sky-500 mr-2"></i>
                Informasi Patroli
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500 block mb-1">Tanggal</label>
                    <p class="text-gray-900 font-medium">{{ $patroli->tanggal->format('d F Y') }}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-500 block mb-1">Status</label>
                    @if($patroli->status === 'selesai')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-2"></i>Berlangsung
                        </span>
                    @endif
                </div>

                <div>
                    <label class="text-sm text-gray-500 block mb-1">Waktu Mulai</label>
                    <p class="text-gray-900 font-medium">{{ $patroli->waktu_mulai ? $patroli->waktu_mulai->format('H:i:s') : '-' }}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-500 block mb-1">Waktu Selesai</label>
                    <p class="text-gray-900 font-medium">{{ $patroli->waktu_selesai ? $patroli->waktu_selesai->format('H:i:s') : '-' }}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-500 block mb-1">Lokasi</label>
                    <p class="text-gray-900 font-medium flex items-center">
                        <i class="fas fa-map-marker-alt text-sky-500 mr-2"></i>
                        {{ $patroli->lokasi->nama }}
                    </p>
                </div>

                <div>
                    <label class="text-sm text-gray-500 block mb-1">Total Checkpoint</label>
                    <p class="text-gray-900 font-medium">{{ $patroli->details->count() }} checkpoint</p>
                </div>
            </div>

            @if($patroli->catatan)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <label class="text-sm text-gray-500 block mb-1">Catatan</label>
                <p class="text-gray-900">{{ $patroli->catatan }}</p>
            </div>
            @endif
        </div>

        <!-- Detail Checkpoint -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Detail Checkpoint
            </h3>

            <div class="space-y-4">
                @forelse($patroli->details as $index => $detail)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-sky-300 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $detail->checkpoint->nama }}</p>
                                <p class="text-sm text-gray-500">{{ $detail->waktu_scan->format('H:i:s') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Terscan
                        </span>
                    </div>

                    @if($detail->foto)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $detail->foto) }}" alt="Foto" class="w-full h-48 object-cover rounded-lg">
                    </div>
                    @endif

                    @if($detail->catatan)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-sticky-note text-gray-400 mr-2"></i>
                            {{ $detail->catatan }}
                        </p>
                    </div>
                    @endif

                    @if($detail->latitude && $detail->longitude)
                    <div class="mt-3 flex items-center text-sm text-gray-600">
                        <i class="fas fa-map-pin text-sky-500 mr-2"></i>
                        <span>{{ $detail->latitude }}, {{ $detail->longitude }}</span>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                    <p>Belum ada checkpoint yang terscan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column - Info Petugas & Timeline -->
    <div class="space-y-6">
        <!-- Info Petugas -->
        <div class="bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Petugas</h3>
                <i class="fas fa-user-shield text-2xl opacity-50"></i>
            </div>

            <div class="flex items-center mb-4">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mr-4">
                    <span class="text-sky-600 font-bold text-2xl">{{ substr($patroli->user->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-bold text-lg">{{ $patroli->user->name }}</p>
                    <p class="text-sm opacity-90">{{ $patroli->user->email }}</p>
                </div>
            </div>

            <div class="border-t border-sky-400 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm opacity-90">Role</span>
                    <span class="font-semibold">{{ ucfirst($patroli->user->role) }}</span>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Checkpoint</span>
                    <span class="text-2xl font-bold text-sky-600">{{ $patroli->details->count() }}</span>
                </div>

                @if($patroli->waktu_mulai && $patroli->waktu_selesai)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Durasi</span>
                    <span class="text-2xl font-bold text-green-600">
                        {{ $patroli->waktu_mulai->diffInMinutes($patroli->waktu_selesai) }} menit
                    </span>
                </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Dengan Foto</span>
                    <span class="text-2xl font-bold text-purple-600">
                        {{ $patroli->details->whereNotNull('foto')->count() }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Dengan Catatan</span>
                    <span class="text-2xl font-bold text-orange-600">
                        {{ $patroli->details->whereNotNull('catatan')->count() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Export -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Export</h3>
            
            <div class="space-y-2">
                <button class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-medium transition inline-flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
                <button class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-medium transition inline-flex items-center justify-center">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
