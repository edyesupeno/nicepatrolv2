@extends('perusahaan.layouts.app')

@section('title', 'Buku Tamu')
@section('page-title', 'Buku Tamu')
@section('page-subtitle', 'Kelola data kunjungan tamu')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4 overflow-x-auto">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hari Ini</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $stats['total_today'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-500">
                    <i class="fas fa-user-check text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Sedang Berkunjung</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['visiting_now'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-500">
                    <i class="fas fa-calendar-week text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Minggu Ini</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_week'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100 min-w-max">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-purple-500">
                    <i class="fas fa-address-book text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Semua</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_all'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('perusahaan.buku-tamu.create') }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl min-w-max" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Input Tamu
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
                placeholder="Cari nama tamu, perusahaan, keperluan, atau QR code..."
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

        <!-- Filter Area -->
        <div class="lg:w-40">
            <select 
                name="area_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Area</option>
                @forelse($areas as $area)
                    <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->nama }}
                    </option>
                @empty
                    <option value="" disabled>Belum ada area</option>
                @endforelse
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
                <option value="sedang_berkunjung" {{ request('status') == 'sedang_berkunjung' ? 'selected' : '' }}>Sedang Berkunjung</option>
                <option value="sudah_keluar" {{ request('status') == 'sudah_keluar' ? 'selected' : '' }}>Sudah Keluar</option>
            </select>
        </div>

        <!-- Filter Period -->
        <div class="lg:w-40">
            <select 
                name="period"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                style="focus:ring-color: #3B82C8;"
            >
                <option value="">Semua Periode</option>
                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="visiting" {{ request('period') == 'visiting' ? 'selected' : '' }}>Sedang Berkunjung</option>
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
            @if(request('search') || request('project_id') || request('area_id') || request('status') || request('period'))
            <a 
                href="{{ route('perusahaan.buku-tamu.index') }}"
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
                        <i class="fas fa-camera mr-2" style="color: #3B82C8;"></i>Foto
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Nama Tamu
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Project
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Area
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-briefcase mr-2" style="color: #3B82C8;"></i>Perusahaan
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-clipboard mr-2" style="color: #3B82C8;"></i>Keperluan
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-handshake mr-2" style="color: #3B82C8;"></i>Bertemu
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-clock mr-2" style="color: #3B82C8;"></i>Check In
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-sign-out-alt mr-2" style="color: #3B82C8;"></i>Check Out
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
                @forelse($bukuTamus as $tamu)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                            @if($tamu->foto)
                                <img 
                                    src="{{ asset('storage/' . $tamu->foto) }}" 
                                    alt="{{ $tamu->nama_tamu }}" 
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                    <i class="fas fa-user text-gray-400 text-xl"></i>
                                </div>
                            @else
                                <i class="fas fa-user text-gray-400 text-xl"></i>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $tamu->nama_tamu }}</p>
                            <p class="text-xs text-gray-500">Input oleh: {{ $tamu->inputBy->name }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ $tamu->project->nama }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $tamu->area ? $tamu->area->nama : '-' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $tamu->perusahaan_tamu ?: '-' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ Str::limit($tamu->keperluan, 30) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $tamu->bertemu ?: '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $tamu->check_in->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $tamu->check_in->format('H:i') }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($tamu->check_out)
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $tamu->check_out->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $tamu->check_out->format('H:i') }}</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $tamu->status_color }}-100 text-{{ $tamu->status_color }}-700">
                            <i class="{{ $tamu->status_icon }} mr-2"></i>{{ $tamu->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('perusahaan.buku-tamu.show', $tamu->hash_id) }}" 
                               class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('perusahaan.buku-tamu.qr-code', $tamu->hash_id) }}" 
                               target="_blank"
                               class="px-3 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition text-sm font-medium"
                               title="Lihat QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>
                            @if($tamu->is_visiting)
                                <button onclick="checkOutGuest('{{ $tamu->hash_id }}')" 
                                        class="px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm font-medium"
                                        title="Check Out">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            @endif
                            <a href="{{ route('perusahaan.buku-tamu.edit', $tamu->hash_id) }}" 
                               class="px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition text-sm font-medium"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete('{{ $tamu->hash_id }}')" 
                                    class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-address-book text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request('search') || request('project_id') || request('area_id') || request('status') || request('period'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada data tamu
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request('search') || request('project_id') || request('area_id') || request('status') || request('period'))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Input data tamu pertama untuk memulai
                                @endif
                            </p>
                            @if(request('search') || request('project_id') || request('area_id') || request('status') || request('period'))
                                <a href="{{ route('perusahaan.buku-tamu.index') }}"
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition inline-flex items-center">
                                    <i class="fas fa-redo mr-2"></i>Reset Pencarian
                                </a>
                            @else
                                <a href="{{ route('perusahaan.buku-tamu.create') }}" 
                                   class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center text-white shadow-lg hover:shadow-xl" 
                                   style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                    <i class="fas fa-plus mr-2"></i>Input Tamu
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
@if($bukuTamus->hasPages())
<div class="mt-6">
    {{ $bukuTamus->links() }}
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
        text: "Data tamu akan dihapus dan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/buku-tamu/${hashId}`;
            form.submit();
        }
    });
}

async function checkOutGuest(hashId) {
    const { value: catatan } = await Swal.fire({
        title: 'Check Out Tamu',
        input: 'textarea',
        inputLabel: 'Catatan (opsional)',
        inputPlaceholder: 'Tambahkan catatan untuk kunjungan ini...',
        showCancelButton: true,
        confirmButtonText: 'Check Out',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
    });

    if (catatan !== undefined) {
        try {
            const response = await fetch(`/perusahaan/buku-tamu/${hashId}/check-out`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ catatan: catatan })
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
                text: error.message || 'Terjadi kesalahan saat check out'
            });
        }
    }
}
</script>
@endpush