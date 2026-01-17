@extends('perusahaan.layouts.app')

@section('title', 'Atensi')
@section('page-title', 'Atensi')
@section('page-subtitle', 'Kelola pengumuman dan perintah untuk tim')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-bullhorn text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Atensi</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $atensis->total() }}</p>
                </div>
            </div>
        </div>
        
        @php
            $urgentCount = \App\Models\Atensi::urgent()->active()->current()->count();
        @endphp
        @if($urgentCount > 0)
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-500">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Mendesak</p>
                    <p class="text-2xl font-bold text-red-600">{{ $urgentCount }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    <a href="{{ route('perusahaan.atensi.create') }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Atensi
    </a>
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
                placeholder="Cari judul atensi atau deskripsi..."
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

        <!-- Filter Prioritas -->
        <div class="lg:w-40">
            <select 
                name="prioritas"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Prioritas</option>
                <option value="high" {{ request('prioritas') == 'high' ? 'selected' : '' }}>🔴 Tinggi</option>
                <option value="medium" {{ request('prioritas') == 'medium' ? 'selected' : '' }}>🟡 Sedang</option>
                <option value="low" {{ request('prioritas') == 'low' ? 'selected' : '' }}>🟢 Rendah</option>
            </select>
        </div>

        <!-- Filter Status -->
        <div class="lg:w-40">
            <select 
                name="status"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Berakhir</option>
                <option value="urgent" {{ request('status') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
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
            @if(request('search') || request('project_id') || request('prioritas') || request('status'))
            <a 
                href="{{ route('perusahaan.atensi.index') }}"
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
                        <i class="fas fa-bullhorn mr-2" style="color: #3B82C8;"></i>Atensi
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project & Area
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-users mr-2" style="color: #3B82C8;"></i>Target & Status
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-calendar mr-2" style="color: #3B82C8;"></i>Periode
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-chart-bar mr-2" style="color: #3B82C8;"></i>Progress
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($atensis as $atensi)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 bg-{{ $atensi->prioritas_color }}-500">
                                <i class="{{ $atensi->prioritas_icon }} text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $atensi->judul }}</p>
                                    @if($atensi->is_urgent)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Mendesak
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 line-clamp-2">{{ Str::limit($atensi->deskripsi, 80) }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $atensi->prioritas_color }}-100 text-{{ $atensi->prioritas_color }}-700">
                                        {{ $atensi->prioritas_label }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $atensi->status_color }}-100 text-{{ $atensi->status_color }}-700">
                                        {{ $atensi->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $atensi->project->nama }}</p>
                            @if($atensi->area)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $atensi->area->nama }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">Semua area</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $atensi->target_type_label }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $atensi->recipients->count() }} penerima</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm text-gray-900">{{ $atensi->tanggal_mulai->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">s/d {{ $atensi->tanggal_selesai->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400 mt-1">oleh {{ $atensi->creator->name }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="space-y-2">
                            <!-- Read Progress -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">Dibaca</span>
                                    <span class="text-xs font-medium text-blue-600">{{ $atensi->recipients_count > 0 ? round(($atensi->read_recipients_count / $atensi->recipients_count) * 100, 1) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $atensi->recipients_count > 0 ? round(($atensi->read_recipients_count / $atensi->recipients_count) * 100, 1) : 0 }}%"></div>
                                </div>
                            </div>
                            <!-- Acknowledgment Progress -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">Konfirmasi</span>
                                    <span class="text-xs font-medium text-green-600">{{ $atensi->recipients_count > 0 ? round(($atensi->acknowledged_recipients_count / $atensi->recipients_count) * 100, 1) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $atensi->recipients_count > 0 ? round(($atensi->acknowledged_recipients_count / $atensi->recipients_count) * 100, 1) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a 
                                href="{{ route('perusahaan.atensi.show', $atensi->hash_id) }}" 
                                class="px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm font-medium"
                                title="Lihat Detail"
                            >
                                <i class="fas fa-eye"></i>
                            </a>
                            <a 
                                href="{{ route('perusahaan.atensi.edit', $atensi->hash_id) }}" 
                                class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                                title="Edit Atensi"
                            >
                                <i class="fas fa-edit"></i>
                            </a>
                            <button 
                                onclick="confirmDelete('{{ $atensi->hash_id }}')" 
                                class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                title="Hapus Atensi"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-bullhorn text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('project_id') || request('prioritas') || request('status'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada atensi
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('project_id') || request('prioritas') || request('status'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Buat atensi pertama untuk memberikan pengumuman atau perintah kepada tim
                                @endif
                            </p>
                            @if(request('search') || request('project_id') || request('prioritas') || request('status'))
                                <a 
                                    href="{{ route('perusahaan.atensi.index') }}"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition inline-flex items-center"
                                >
                                    <i class="fas fa-redo mr-2"></i>Reset Pencarian
                                </a>
                            @else
                                <a 
                                    href="{{ route('perusahaan.atensi.create') }}" 
                                    class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center text-white shadow-lg hover:shadow-xl" 
                                    style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
                                >
                                    <i class="fas fa-plus mr-2"></i>Tambah Atensi
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
@if($atensis->hasPages())
<div class="mt-6">
    {{ $atensis->links() }}
</div>
@endif

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Atensi akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/atensi/${hashId}`;
            form.submit();
        }
    });
}
</script>
@endpush