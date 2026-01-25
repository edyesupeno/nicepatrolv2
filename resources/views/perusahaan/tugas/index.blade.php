@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Tugas')
@section('page-title', 'Manajemen Tugas')
@section('page-subtitle', 'Kelola dan pantau tugas untuk tim')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4 overflow-x-auto">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-tasks text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Tugas</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $tugas->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-500">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $tugas->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-yellow-500">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Aktif</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $tugas->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-500">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Terlambat</p>
                    <p class="text-2xl font-bold text-red-600">{{ $tugas->where('is_overdue', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('perusahaan.tugas.create') }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl min-w-max" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Buat Tugas
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
                placeholder="Cari judul tugas, deskripsi, atau lokasi..."
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
                <option value="high" {{ request('prioritas') == 'high' ? 'selected' : '' }}>Tinggi</option>
                <option value="medium" {{ request('prioritas') == 'medium' ? 'selected' : '' }}>Sedang</option>
                <option value="low" {{ request('prioritas') == 'low' ? 'selected' : '' }}>Rendah</option>
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
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                <option value="due_soon" {{ request('status') == 'due_soon' ? 'selected' : '' }}>Segera Berakhir</option>
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
                href="{{ route('perusahaan.tugas.index') }}"
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
                        <i class="fas fa-tasks mr-2" style="color: #3B82C8;"></i>Tugas
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project & Area
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-flag mr-2" style="color: #3B82C8;"></i>Prioritas
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-calendar mr-2" style="color: #3B82C8;"></i>Batas Waktu
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-chart-pie mr-2" style="color: #3B82C8;"></i>Progress
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-info-circle mr-2" style="color: #3B82C8;"></i>Status
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tugas as $task)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 bg-{{ $task->prioritas_color }}-500">
                                <i class="{{ $task->prioritas_icon }} text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $task->judul }}</p>
                                    @if($task->is_urgent)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Mendesak
                                        </span>
                                    @endif
                                    @if($task->is_overdue)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i class="fas fa-clock mr-1"></i>Terlambat
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mb-2">{{ Str::limit($task->deskripsi, 80) }}</p>
                                @if($task->detail_lokasi)
                                    <p class="text-xs text-gray-400">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($task->detail_lokasi, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $task->project->nama }}</p>
                            @if($task->area)
                                <p class="text-xs text-gray-500">{{ $task->area->nama }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $task->target_type_label }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $task->prioritas_color }}-100 text-{{ $task->prioritas_color }}-700">
                            <i class="{{ $task->prioritas_icon }} mr-2"></i>{{ $task->prioritas_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $task->batas_pengerjaan->format('d M Y') }}</p>
                            @if($task->days_remaining >= 0)
                                <p class="text-xs text-gray-500">{{ $task->days_remaining }} hari lagi</p>
                            @else
                                <p class="text-xs text-red-500">{{ abs($task->days_remaining) }} hari terlambat</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-center">
                            <!-- Completion Progress -->
                            <div class="mb-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">Selesai</span>
                                    <span class="text-xs font-medium text-green-600">{{ $task->assignments_count > 0 ? round(($task->completed_assignments_count / $task->assignments_count) * 100, 1) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $task->assignments_count > 0 ? round(($task->completed_assignments_count / $task->assignments_count) * 100, 1) : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <!-- In Progress -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">Dikerjakan</span>
                                    <span class="text-xs font-medium text-blue-600">{{ $task->assignments_count > 0 ? round(($task->in_progress_assignments_count / $task->assignments_count) * 100, 1) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $task->assignments_count > 0 ? round(($task->in_progress_assignments_count / $task->assignments_count) * 100, 1) : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-2">{{ $task->assignments_count }} orang</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-700">
                            {{ $task->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('perusahaan.tugas.show', ['tugas' => $task->hash_id]) }}" 
                               class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('perusahaan.tugas.edit', ['tugas' => $task->hash_id]) }}" 
                               class="px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition text-sm font-medium"
                               title="Edit Tugas">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete('{{ $task->hash_id }}')" 
                                    class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                    title="Hapus Tugas">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-tasks text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('project_id') || request('prioritas') || request('status'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada tugas
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('project_id') || request('prioritas') || request('status'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Buat tugas pertama Anda untuk memulai
                                @endif
                            </p>
                            @if(request('search') || request('project_id') || request('prioritas') || request('status'))
                                <a href="{{ route('perusahaan.tugas.index') }}"
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition inline-flex items-center">
                                    <i class="fas fa-redo mr-2"></i>Reset Pencarian
                                </a>
                            @else
                                <a href="{{ route('perusahaan.tugas.create') }}" 
                                   class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center text-white shadow-lg hover:shadow-xl" 
                                   style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                    <i class="fas fa-plus mr-2"></i>Buat Tugas
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
@if($tugas->hasPages())
<div class="mt-6">
    {{ $tugas->links() }}
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
        text: "Tugas akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('perusahaan.tugas.destroy', ['tugas' => '__HASH_ID__']) }}`.replace('__HASH_ID__', hashId);
            form.submit();
        }
    });
}
</script>
@endpush