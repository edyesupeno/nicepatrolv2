@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Superadmin')
@section('page-subtitle', 'Selamat datang di panel manajemen NicePatrol SaaS')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @if(auth()->user()->isSuperAdmin())
    <!-- Total Perusahaan -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Perusahaan</p>
                <p class="text-4xl font-bold">{{ $stats['total_perusahaan'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-building text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Perusahaan Aktif -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Perusahaan Aktif</p>
                <p class="text-4xl font-bold">{{ $stats['total_perusahaan'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Pendapatan -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Pendapatan</p>
                <p class="text-4xl font-bold">Rp 0</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-dollar-sign text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Tagihan Pending -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Tagihan Pending</p>
                <p class="text-4xl font-bold">0</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-clock text-3xl"></i>
            </div>
        </div>
    </div>
    @else
    <!-- Stats untuk Admin/Petugas -->
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

    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
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

    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Project</p>
                <p class="text-4xl font-bold">{{ $stats['total_project'] }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-project-diagram text-3xl"></i>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Quick Actions -->
@if(auth()->user()->isSuperAdmin())
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Tambah Perusahaan -->
        <a href="{{ route('admin.perusahaans.create') }}" class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
            <div class="flex items-start">
                <div class="bg-purple-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-plus text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Tambah Perusahaan</h4>
                    <p class="text-sm text-gray-500">Daftarkan mitra baru</p>
                </div>
            </div>
        </a>

        <!-- Kelola Paket -->
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
            <div class="flex items-start">
                <div class="bg-blue-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Kelola Paket</h4>
                    <p class="text-sm text-gray-500">Atur paket langganan</p>
                </div>
            </div>
        </a>

        <!-- Lihat Tagihan -->
        <a href="{{ route('admin.lokasis.index') }}" class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
            <div class="flex items-start">
                <div class="bg-green-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-file-invoice text-green-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Lihat Tagihan</h4>
                    <p class="text-sm text-gray-500">Monitor pembayaran</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endif

<!-- Recent Activity / Patroli Terbaru -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-900">
            @if(auth()->user()->isSuperAdmin())
                Aktivitas Terbaru
            @else
                Patroli Terbaru
            @endif
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentPatrolis as $patroli)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <span class="text-white font-semibold text-xs">{{ substr($patroli->user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $patroli->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $patroli->waktu_mulai ? $patroli->waktu_mulai->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $patroli->waktu_selesai ? $patroli->waktu_selesai->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($patroli->status === 'berlangsung')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Berlangsung</span>
                        @elseif($patroli->status === 'selesai')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Dibatalkan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center">
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
