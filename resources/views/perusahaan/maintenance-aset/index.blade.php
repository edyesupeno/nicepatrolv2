@extends('perusahaan.layouts.app')

@section('title', 'Maintenance & Servis Aset')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Maintenance & Servis Aset</h1>
            <p class="text-gray-600 mt-1">Kelola jadwal maintenance dan servis aset perusahaan</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('perusahaan.maintenance-aset.dashboard') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-chart-bar"></i> Dashboard
            </a>
            <a href="{{ route('perusahaan.maintenance-aset.laporan') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-file-alt"></i> Laporan
            </a>
            <a href="{{ route('perusahaan.maintenance-aset.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-plus"></i> Jadwal Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-wrench text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Terjadwal</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Dikerjakan</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-cog text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Terlambat</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Mendatang</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-clock text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-7 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" placeholder="Cari maintenance..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           value="{{ request('search') }}">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Aset</label>
                    <select name="asset_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="data_aset" {{ request('asset_type') == 'data_aset' ? 'selected' : '' }}>Data Aset</option>
                        <option value="aset_kendaraan" {{ request('asset_type') == 'aset_kendaraan' ? 'selected' : '' }}>Aset Kendaraan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Maintenance</label>
                    <select name="jenis_maintenance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="preventive" {{ request('jenis_maintenance') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                        <option value="corrective" {{ request('jenis_maintenance') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                        <option value="predictive" {{ request('jenis_maintenance') == 'predictive' ? 'selected' : '' }}>Predictive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                    <select name="prioritas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Prioritas</option>
                        <option value="low" {{ request('prioritas') == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ request('prioritas') == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ request('prioritas') == 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="urgent" {{ request('prioritas') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($maintenances as $maintenance)
                    <tr class="hover:bg-gray-50 {{ $maintenance->is_overdue ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $maintenance->nomor_maintenance }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($maintenance->deskripsi_pekerjaan, 40) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $maintenance->project->nama ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($maintenance->asset_type == 'data_aset')
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-car text-green-600"></i>
                                        </div>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($maintenance->asset_name, 25) }}</div>
                                    <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $maintenance->asset_type)) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $maintenance->tanggal_maintenance->format('d/m/Y') }}</div>
                            @if($maintenance->waktu_mulai)
                                <div class="text-sm text-gray-500">{{ $maintenance->waktu_mulai }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $maintenance->jenis_maintenance == 'preventive' ? 'bg-green-100 text-green-800' : 
                                   ($maintenance->jenis_maintenance == 'corrective' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ ucfirst($maintenance->jenis_maintenance) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{!! $maintenance->prioritas_badge !!}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{!! $maintenance->status_badge !!}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $maintenance->formatted_total_biaya }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('perusahaan.maintenance-aset.show', $maintenance->hash_id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($maintenance->status !== 'completed')
                                    <a href="{{ route('perusahaan.maintenance-aset.edit', $maintenance->hash_id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if($maintenance->status === 'scheduled')
                                    <button onclick="updateStatus('{{ $maintenance->hash_id }}', 'in_progress')" 
                                            class="text-green-600 hover:text-green-900" title="Mulai Maintenance">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @elseif($maintenance->status === 'in_progress')
                                    <button onclick="completeMaintenanceModal('{{ $maintenance->hash_id }}')" 
                                            class="text-green-600 hover:text-green-900" title="Selesaikan Maintenance">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center py-8">
                                <i class="fas fa-wrench text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">Belum ada jadwal maintenance</p>
                                <p class="text-sm">Buat jadwal maintenance pertama untuk aset Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($maintenances->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Complete Maintenance Modal -->
<div id="completeMaintenanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Selesaikan Maintenance</h3>
                <form id="completeMaintenanceForm">
                    <input type="hidden" id="maintenanceId" name="maintenance_id">
                    
                    <!-- Hasil Maintenance -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Maintenance</label>
                        <select name="hasil_maintenance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="berhasil">Berhasil</option>
                            <option value="sebagian">Sebagian Berhasil</option>
                            <option value="gagal">Gagal</option>
                        </select>
                    </div>

                    <!-- Biaya Real -->
                    <div class="mb-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-green-600"></i>
                            Biaya Real Maintenance
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Sparepart (Rp)</label>
                                <input type="number" name="biaya_sparepart_real" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       min="0" step="1000" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Jasa (Rp)</label>
                                <input type="number" name="biaya_jasa_real" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       min="0" step="1000" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Lainnya (Rp)</label>
                                <input type="number" name="biaya_lainnya_real" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       min="0" step="1000" placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Hasil</label>
                        <textarea name="catatan_sesudah" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan hasil maintenance..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeCompleteModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(maintenanceId, status) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin mengubah status maintenance?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/maintenance-aset/${maintenanceId}/update-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status
                })
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengubah status'
                });
            });
        }
    });
}

function completeMaintenanceModal(maintenanceId) {
    document.getElementById('maintenanceId').value = maintenanceId;
    document.getElementById('completeMaintenanceModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeMaintenanceModal').classList.add('hidden');
}

document.getElementById('completeMaintenanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const maintenanceId = formData.get('maintenance_id');
    
    fetch(`/perusahaan/maintenance-aset/${maintenanceId}/update-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: new FormData(document.getElementById('completeMaintenanceForm'))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCompleteModal();
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
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyelesaikan maintenance'
        });
    });
});
</script>
@endpush