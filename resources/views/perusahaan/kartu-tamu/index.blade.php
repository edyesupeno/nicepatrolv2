@extends('perusahaan.layouts.app')

@section('title', 'Kartu Tamu')
@section('page-title', 'Kartu Tamu')
@section('page-subtitle', 'Kelola kartu tamu untuk project')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4 overflow-x-auto">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-id-card text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Kartu</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $stats['total_kartu'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-500">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['tersedia'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-orange-500">
                    <i class="fas fa-user-tag text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Terpakai</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['terpakai'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-500">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Rusak/Hilang</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rusak_hilang'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <!-- Search Input -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Cari project atau area..."
                class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
        </div>

        <!-- Filter Project -->
        <div class="lg:w-48">
            <select 
                name="project_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button 
                type="submit"
                class="px-6 py-3 rounded-xl font-medium transition text-white"
                style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
            >
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request('search') || request('project_id'))
            <a 
                href="{{ route('perusahaan.kartu-tamu.index') }}"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition"
            >
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Project
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Area
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-id-card mr-2" style="color: #3B82C8;"></i>Jumlah Kartu
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-user-tag mr-2" style="color: #3B82C8;"></i>Terpakai
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-check-circle mr-2" style="color: #3B82C8;"></i>Tersedia
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($kartuSummary as $summary)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-900">{{ $summary->project_nama }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $summary->area_nama }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                            {{ $summary->total_kartu }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-700">
                            {{ $summary->terpakai }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                            {{ $summary->tersedia }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('perusahaan.kartu-tamu.detail', ['project_id' => $summary->project_id, 'area_id' => $summary->area_id]) }}" 
                               class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('perusahaan.kartu-tamu.create', ['project_id' => $summary->project_id, 'area_id' => $summary->area_id]) }}" 
                               class="px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm font-medium"
                               title="Tambah Kartu">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-id-card text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('project_id'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada kartu tamu
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('project_id'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan kartu tamu untuk project dan area tertentu
                                @endif
                            </p>
                            @if(request('search') || request('project_id'))
                                <a href="{{ route('perusahaan.kartu-tamu.index') }}"
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition inline-flex items-center">
                                    <i class="fas fa-redo mr-2"></i>Reset Pencarian
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($kartuSummary->hasPages())
<div class="mt-6">
    {{ $kartuSummary->links() }}
</div>
@endif
@endsection