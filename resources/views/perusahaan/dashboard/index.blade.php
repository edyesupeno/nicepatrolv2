@extends('perusahaan.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di panel admin ' . auth()->user()->perusahaan->nama)

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Patroli -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Patroli</p>
                <p class="text-4xl font-bold">{{ $stats['total_patroli'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-clipboard-list text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Patroli Hari Ini -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Patroli Hari Ini</p>
                <p class="text-4xl font-bold">{{ $stats['patroli_hari_ini'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-calendar-day text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Patroli Berlangsung -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Sedang Berlangsung</p>
                <p class="text-4xl font-bold">{{ $stats['patroli_berlangsung'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-running text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Kantor -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Kantor</p>
                <p class="text-4xl font-bold">{{ $stats['total_kantor'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-building text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Checkpoint -->
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Checkpoint</p>
                <p class="text-4xl font-bold">{{ $stats['total_checkpoint'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Petugas -->
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Petugas</p>
                <p class="text-4xl font-bold">{{ $stats['total_petugas'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-users text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Patroli -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-900">Patroli Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentPatrolis as $patroli)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-sky-600 font-semibold text-xs">{{ substr($patroli->user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $patroli->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $patroli->lokasi->nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $patroli->waktu_mulai->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($patroli->status === 'berlangsung')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Berlangsung</span>
                        @elseif($patroli->status === 'selesai')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('perusahaan.patrolis.show', $patroli->hash_id) }}" class="text-sky-600 hover:text-sky-800 font-medium text-sm">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada data patroli</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
