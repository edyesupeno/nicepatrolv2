@extends('perusahaan.layouts.app')

@section('title', 'Laporan Aset Bermasalah')
@section('page-title', 'Laporan Aset Bermasalah')
@section('page-subtitle', 'Rekap aset bermasalah dan hilang dari hasil patroli')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="bermasalah" {{ request('status_filter') == 'bermasalah' ? 'selected' : '' }}>Bermasalah</option>
                    <option value="hilang" {{ request('status_filter') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('perusahaan.laporan-patroli.aset-bermasalah') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bermasalah</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_bermasalah']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-red-600 font-medium">{{ $stats['percentage_bermasalah'] }}%</span>
                    <span class="text-gray-500 ml-1">dari total pengecekan</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Aset Rusak</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_bermasalah_only']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Aset Hilang</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_hilang']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pengecekan</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_checks']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset Bermasalah List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Aset Bermasalah</h3>
            <p class="text-sm text-gray-600 mt-1">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }} ({{ $asetBermasalah->total() }} item)</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($asetBermasalah as $aset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $aset->asetKawasan->nama ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $aset->asetKawasan->kode_aset ?? '-' }}</div>
                                    @if($aset->asetKawasan->merk || $aset->asetKawasan->model)
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $aset->asetKawasan->merk }} {{ $aset->asetKawasan->model }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $aset->patroliDetail->checkpoint->nama ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $aset->patroliDetail->checkpoint->rutePatrol->nama ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $aset->patroliDetail->checkpoint->rutePatrol->areaPatrol->nama ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($aset->status === 'bermasalah')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Bermasalah
                                    </span>
                                @elseif($aset->status === 'hilang')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-search mr-1"></i>Hilang
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $aset->catatan ? Str::limit($aset->catatan, 60) : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $aset->patroliDetail->patroli->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($aset->patroliDetail->patroli->waktu_mulai)
                                        {{ $aset->patroliDetail->patroli->waktu_mulai->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($aset->created_at)
                                    <div class="text-sm text-gray-900">{{ $aset->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $aset->created_at->format('H:i') }}</div>
                                @else
                                    <div class="text-sm text-gray-400">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($aset->foto)
                                    <button onclick="showPhoto('{{ asset('storage/' . $aset->foto) }}')" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-image"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-shield-alt text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">Tidak ada aset bermasalah</p>
                                    <p class="text-sm">Semua aset dalam kondisi baik dalam periode yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($asetBermasalah->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $asetBermasalah->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Foto -->
<div id="photoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Foto Aset</h3>
                    <button onclick="closePhoto()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <img id="photoImage" src="" alt="Foto Aset" class="w-full h-auto rounded-lg">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showPhoto(photoUrl) {
    document.getElementById('photoImage').src = photoUrl;
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhoto() {
    document.getElementById('photoModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhoto();
    }
});
</script>
@endpush