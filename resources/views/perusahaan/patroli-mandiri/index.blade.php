@extends('perusahaan.layouts.app')

@section('title', 'Patroli Mandiri')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Patroli Mandiri</h1>
            <p class="text-gray-600 mt-1">Laporan patroli mandiri dari petugas security</p>
        </div>
        <div>
            <a href="{{ route('perusahaan.patroli-mandiri.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Laporan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="statisticsCards">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Laporan</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalLaporan">-</p>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Lokasi Aman</p>
                    <p class="text-2xl font-bold text-green-600" id="lokasiAman">-</p>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-shield-alt text-3xl text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Lokasi Tidak Aman</p>
                    <p class="text-2xl font-bold text-red-600" id="lokasiTidakAman">-</p>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Belum Direview</p>
                    <p class="text-2xl font-bold text-yellow-600" id="belumDireview">-</p>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-3xl text-yellow-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('perusahaan.patroli-mandiri.index') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Nama lokasi, petugas...">
                    </div>
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                id="project_id" name="project_id">
                            <option value="">Semua Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status_lokasi" class="block text-sm font-medium text-gray-700 mb-1">Status Lokasi</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                id="status_lokasi" name="status_lokasi">
                            <option value="">Semua Status</option>
                            <option value="aman" {{ request('status_lokasi') == 'aman' ? 'selected' : '' }}>Aman</option>
                            <option value="tidak_aman" {{ request('status_lokasi') == 'tidak_aman' ? 'selected' : '' }}>Tidak Aman</option>
                        </select>
                    </div>
                    <div>
                        <label for="prioritas" class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                id="prioritas" name="prioritas">
                            <option value="">Semua Prioritas</option>
                            <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sedang" {{ request('prioritas') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="kritis" {{ request('prioritas') == 'kritis' ? 'selected' : '' }}>Kritis</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_laporan" class="block text-sm font-medium text-gray-700 mb-1">Status Laporan</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                id="status_laporan" name="status_laporan">
                            <option value="">Semua Status</option>
                            <option value="submitted" {{ request('status_laporan') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="reviewed" {{ request('status_laporan') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ request('status_laporan') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <a href="{{ route('perusahaan.patroli-mandiri.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Laporan Patroli Mandiri</h3>
        </div>
        <div class="overflow-x-auto">
            @if($patroliMandiri->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Laporan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kendala</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Laporan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($patroliMandiri as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->waktu_laporan->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->petugas->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->project->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->nama_lokasi }}</div>
                                    @if($item->maps_url)
                                        <div class="text-xs">
                                            <a href="{{ $item->maps_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-map-marker-alt mr-1"></i>Lihat Maps
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->status_lokasi)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->prioritas_badge }}">
                                        {{ ucfirst($item->prioritas) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->jenis_kendala)
                                        {{ ucfirst(str_replace('_', ' ', $item->jenis_kendala)) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->status_laporan_badge }}">
                                        {{ ucfirst($item->status_laporan) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('perusahaan.patroli-mandiri.show', $item->hash_id) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($item->status_laporan === 'submitted')
                                            <a href="{{ route('perusahaan.patroli-mandiri.edit', $item->hash_id) }}" 
                                               class="text-green-600 hover:text-green-900" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button type="button" onclick="showReviewModal('{{ $item->hash_id }}')" 
                                                    class="text-yellow-600 hover:text-yellow-900" title="Review">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button type="button" onclick="confirmDelete('{{ $item->hash_id }}')" 
                                                class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ $patroliMandiri->firstItem() }} sampai {{ $patroliMandiri->lastItem() }} 
                        dari {{ $patroliMandiri->total() }} data
                    </div>
                    <div>
                        {{ $patroliMandiri->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada laporan patroli mandiri</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Review Laporan</h3>
            <form id="reviewForm">
                <div class="mb-4">
                    <label for="review_status_laporan" class="block text-sm font-medium text-gray-700 mb-2">Status Laporan</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            id="review_status_laporan" name="status_laporan" required>
                        <option value="reviewed">Reviewed</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="review_catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan Review</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              id="review_catatan" name="review_catatan" rows="3" 
                              placeholder="Masukkan catatan review..."></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeReviewModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Simpan Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPatroliId = null;

// Load statistics
function loadStatistics() {
    fetch('{{ route("perusahaan.patroli-mandiri.statistics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalLaporan').textContent = data.data.total_laporan;
                document.getElementById('lokasiAman').textContent = data.data.lokasi_aman;
                document.getElementById('lokasiTidakAman').textContent = data.data.lokasi_tidak_aman;
                document.getElementById('belumDireview').textContent = data.data.belum_direview;
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

// Show review modal
function showReviewModal(patroliId) {
    currentPatroliId = patroliId;
    document.getElementById('review_status_laporan').value = 'reviewed';
    document.getElementById('review_catatan').value = '';
    document.getElementById('reviewModal').classList.remove('hidden');
}

// Close review modal
function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    currentPatroliId = null;
}

// Handle review form submission
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentPatroliId) return;
    
    const formData = new FormData(this);
    
    fetch(`{{ url('perusahaan/patroli-mandiri') }}/${currentPatroliId}/review`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
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
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan review'
        });
    });
    
    closeReviewModal();
});

// Confirm delete
function confirmDelete(patroliId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Laporan patroli mandiri akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            deletePatroli(patroliId);
        }
    });
}

// Delete patroli
function deletePatroli(patroliId) {
    fetch(`{{ url('perusahaan/patroli-mandiri') }}/${patroliId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
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
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus laporan'
        });
    });
}

// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});
</script>
@endpush