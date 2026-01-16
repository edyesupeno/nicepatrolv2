@extends('perusahaan.layouts.app')

@section('title', 'Patroli')
@section('page-title', 'Riwayat Patroli')
@section('page-subtitle', 'Lihat riwayat patroli petugas')

@section('content')
<div class="mb-6">
    <div class="bg-gradient-to-r from-sky-500 to-blue-500 rounded-xl shadow-lg p-6 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Patroli</p>
                <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
            </div>
            <div>
                <p class="text-sm opacity-90 mb-1">Hari Ini</p>
                <p class="text-3xl font-bold">{{ $stats['today'] }}</p>
            </div>
            <div>
                <p class="text-sm opacity-90 mb-1">Minggu Ini</p>
                <p class="text-3xl font-bold">{{ $stats['week'] }}</p>
            </div>
            <div>
                <p class="text-sm opacity-90 mb-1">Bulan Ini</p>
                <p class="text-3xl font-bold">{{ $stats['month'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input 
                type="date" 
                name="start_date" 
                value="{{ request('start_date') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            >
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input 
                type="date" 
                name="end_date" 
                value="{{ request('end_date') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            >
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Petugas</label>
            <select 
                name="user_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
            >
                <option value="">Semua Petugas</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('perusahaan.patrolis.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-sky-500 to-blue-500 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal & Waktu</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Petugas</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Lokasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Checkpoint</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($patrolis as $index => $patroli)
                <tr class="hover:bg-sky-50 transition">
                    <td class="px-6 py-4 text-gray-600">{{ $patrolis->firstItem() + $index }}</td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $patroli->tanggal->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $patroli->waktu_mulai ? $patroli->waktu_mulai->format('H:i') : '-' }} - 
                                {{ $patroli->waktu_selesai ? $patroli->waktu_selesai->format('H:i') : 'Belum selesai' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white font-bold text-xs">{{ substr($patroli->user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-gray-900">{{ $patroli->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center text-gray-700">
                            <i class="fas fa-map-marker-alt text-sky-500 mr-2"></i>
                            {{ $patroli->lokasi->nama }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>{{ $patroli->details_count }} checkpoint
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($patroli->status === 'selesai')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-2"></i>Berlangsung
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('perusahaan.patrolis.show', $patroli->hash_id) }}" class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm transition inline-flex items-center">
                                <i class="fas fa-eye mr-2"></i>Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada data patroli</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $patrolis->links() }}
</div>
@endsection
