@extends('perusahaan.layouts.app')

@section('title', 'Detail Kartu Tamu')
@section('page-title', 'Detail Kartu Tamu')
@section('page-subtitle', $project->nama . ' - ' . $area->nama)

@section('content')
<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.kartu-tamu.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-id-card mr-2"></i>
                    Kartu Tamu
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $project->nama }}</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $area->nama }}</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4 overflow-x-auto">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-id-card text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $stats['total'] }}</p>
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
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-yellow-500">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Rusak</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['rusak'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-red-500">
                    <i class="fas fa-times-circle text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hilang</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['hilang'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('perusahaan.kartu-tamu.create', ['project_id' => $projectId, 'area_id' => $areaId]) }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl min-w-max" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Kartu
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <input type="hidden" name="project_id" value="{{ $projectId }}">
        <input type="hidden" name="area_id" value="{{ $areaId }}">
        
        <!-- Search Input -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Cari nomor kartu, NFC, atau keterangan..."
                class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
        </div>

        <!-- Filter Status -->
        <div class="lg:w-40">
            <select 
                name="status"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="hilang" {{ request('status') == 'hilang' ? 'selected' : '' }}>Hilang</option>
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
            @if(request('search') || request('status'))
            <a 
                href="{{ route('perusahaan.kartu-tamu.detail', ['project_id' => $projectId, 'area_id' => $areaId]) }}"
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
                        <i class="fas fa-hashtag mr-2" style="color: #3B82C8;"></i>No Kartu
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-wifi mr-2" style="color: #3B82C8;"></i>NFC Kartu
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-info-circle mr-2" style="color: #3B82C8;"></i>Status
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Tamu Saat Ini
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-sticky-note mr-2" style="color: #3B82C8;"></i>Keterangan
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($kartuTamus as $kartu)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-900">{{ $kartu->no_kartu }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $kartu->nfc_kartu ?: '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $kartu->status_color }}-100 text-{{ $kartu->status_color }}-700">
                            <i class="{{ $kartu->status_icon }} mr-2"></i>{{ $kartu->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($kartu->currentGuest)
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $kartu->currentGuest->nama_tamu }}</p>
                                <p class="text-xs text-gray-500">{{ $kartu->currentGuest->perusahaan_tamu }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ Str::limit($kartu->keterangan, 50) ?: '-' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            @if($kartu->is_assigned)
                                <button onclick="returnCard('{{ $kartu->hash_id }}')" 
                                        class="px-3 py-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition text-sm font-medium"
                                        title="Kembalikan Kartu">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                            <a href="{{ route('perusahaan.kartu-tamu.edit', $kartu->hash_id) }}" 
                               class="px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition text-sm font-medium"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete('{{ $kartu->hash_id }}')" 
                                    class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                    title="Hapus">
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
                                <i class="fas fa-id-card text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('status'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada kartu tamu
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('status'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan kartu tamu untuk area ini
                                @endif
                            </p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('perusahaan.kartu-tamu.detail', ['project_id' => $projectId, 'area_id' => $areaId]) }}"
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition inline-flex items-center">
                                    <i class="fas fa-redo mr-2"></i>Reset Pencarian
                                </a>
                            @else
                                <a href="{{ route('perusahaan.kartu-tamu.create', ['project_id' => $projectId, 'area_id' => $areaId]) }}" 
                                   class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center text-white shadow-lg hover:shadow-xl" 
                                   style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                    <i class="fas fa-plus mr-2"></i>Tambah Kartu
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
@if($kartuTamus->hasPages())
<div class="mt-6">
    {{ $kartuTamus->appends(request()->query())->links() }}
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
        text: "Kartu tamu akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/kartu-tamu/${hashId}`;
            form.submit();
        }
    });
}

async function returnCard(hashId) {
    const result = await Swal.fire({
        title: 'Kembalikan Kartu',
        text: 'Yakin ingin mengembalikan kartu dari tamu?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/kartu-tamu/${hashId}/return`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Terjadi kesalahan saat mengembalikan kartu'
            });
        }
    }
}
</script>
@endpush