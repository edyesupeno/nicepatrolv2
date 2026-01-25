@extends('perusahaan.layouts.app')

@section('title', 'Medical Checkup')
@section('page-title', 'Medical Checkup')
@section('page-subtitle', 'Monitoring status medical checkup karyawan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 w-full">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_karyawan']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Valid</p>
                    <p class="text-lg font-semibold text-green-600">{{ number_format($stats['valid_checkup']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Akan Expired</p>
                    <p class="text-lg font-semibold text-yellow-600">{{ number_format($stats['expiring_soon']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Expired</p>
                    <p class="text-lg font-semibold text-red-600">{{ number_format($stats['expired_checkup']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-question-circle text-gray-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Belum Ada</p>
                    <p class="text-lg font-semibold text-gray-600">{{ number_format($stats['no_checkup']) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Karyawan</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Nama atau NIK..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
            <select name="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="expired" {{ request('status_filter') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="expiring_soon" {{ request('status_filter') == 'expiring_soon' ? 'selected' : '' }}>Akan Expired</option>
                <option value="no_checkup" {{ request('status_filter') == 'no_checkup' ? 'selected' : '' }}>Belum Ada</option>
            </select>
        </div>

        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('perusahaan.medical-checkup.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-refresh"></i>
            </a>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">
            Data Medical Checkup Karyawan 
            <span class="text-sm font-normal text-gray-500">({{ $karyawans->total() }} total)</span>
        </h3>
        <div class="flex items-center space-x-2">
            <button onclick="sendBulkReminder()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-paper-plane mr-2"></i>Kirim Reminder
            </button>
        </div>
    </div>

    @if($karyawans->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Checkup</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masa Berlaku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($karyawans as $karyawan)
                        @php
                            $latestCheckup = $karyawan->medicalCheckups->first();
                            $hasCheckup = $latestCheckup !== null;
                            
                            if ($hasCheckup) {
                                $checkupDate = \Carbon\Carbon::parse($latestCheckup->tanggal_checkup);
                                $expiredDate = $checkupDate->copy()->addYear();
                                $daysLeft = \Carbon\Carbon::now()->diffInDays($expiredDate, false);
                                $isExpired = $daysLeft < 0;
                                $isExpiringSoon = $daysLeft >= 0 && $daysLeft <= 30;
                            } else {
                                $checkupDate = null;
                                $expiredDate = null;
                                $daysLeft = null;
                                $isExpired = false;
                                $isExpiringSoon = false;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="karyawan_ids[]" value="{{ $karyawan->id }}" class="karyawan-checkbox rounded border-gray-300 text-blue-600">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                        <span class="text-sm font-medium text-blue-600">{{ substr($karyawan->nama_lengkap, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</div>
                                        <div class="text-sm text-gray-500">{{ $karyawan->nik_karyawan }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $karyawan->project->nama ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $karyawan->jabatan->nama ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($hasCheckup)
                                    <div class="text-sm text-gray-900">{{ $checkupDate->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $checkupDate->diffForHumans() }}</div>
                                @else
                                    <div class="text-sm text-gray-500">Tidak ada data</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($hasCheckup)
                                    <div class="text-sm text-gray-900">{{ $expiredDate->format('d/m/Y') }}</div>
                                @else
                                    <div class="text-sm text-gray-500">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($hasCheckup)
                                    @if($isExpired)
                                        <div class="text-sm font-medium text-red-600">Expired {{ abs($daysLeft) }} hari lalu</div>
                                    @else
                                        <div class="text-sm text-gray-900">{{ $daysLeft }} hari lagi</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-500">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(!$hasCheckup)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Belum Ada
                                    </span>
                                @elseif($isExpired)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @elseif($isExpiringSoon)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Akan Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Valid
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($karyawan->telepon)
                                        <button onclick="sendIndividualReminder('{{ $karyawan->id }}', '{{ $karyawan->nama_lengkap }}')" 
                                                class="text-orange-600 hover:text-orange-900" 
                                                title="Kirim Reminder">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('perusahaan.karyawans.show', $karyawan->hash_id) }}#medical-checkup" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination dengan info -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan {{ $karyawans->firstItem() }} sampai {{ $karyawans->lastItem() }} dari {{ $karyawans->total() }} karyawan
            </div>
            <div>
                {{ $karyawans->withQueryString()->links() }}
            </div>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-user-md text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-sm font-medium text-gray-900">Tidak ada data</h3>
            <p class="text-sm text-gray-500">Tidak ada karyawan yang sesuai dengan filter.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.karyawan-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function sendIndividualReminder(karyawanId, namaKaryawan) {
    Swal.fire({
        title: 'Kirim Reminder?',
        text: `Kirim reminder medical checkup ke ${namaKaryawan}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("perusahaan.medical-checkup.send-reminder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    karyawan_ids: [karyawanId],
                    message_type: 'expired'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success');
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat mengirim reminder', 'error');
            });
        }
    });
}

function sendBulkReminder() {
    const selectedCheckboxes = document.querySelectorAll('.karyawan-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        Swal.fire('Peringatan!', 'Pilih minimal satu karyawan untuk mengirim reminder', 'warning');
        return;
    }

    const karyawanIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    Swal.fire({
        title: 'Kirim Bulk Reminder?',
        text: `Kirim reminder medical checkup ke ${karyawanIds.length} karyawan terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("perusahaan.medical-checkup.send-reminder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    karyawan_ids: karyawanIds,
                    message_type: 'expired'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    document.querySelectorAll('.karyawan-checkbox').forEach(cb => cb.checked = false);
                    document.getElementById('selectAll').checked = false;
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat mengirim reminder', 'error');
            });
        }
    });
}
</script>
@endpush

@push('scripts')
<script>
// Select All Checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.karyawan-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Send Individual Reminder
function sendIndividualReminder(karyawanId, namaKaryawan) {
    Swal.fire({
        title: 'Kirim Reminder?',
        text: `Kirim reminder medical checkup ke ${namaKaryawan}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Determine message type based on status
            const messageType = determineMessageType(karyawanId);
            
            fetch('{{ route("perusahaan.medical-checkup.send-reminder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    karyawan_ids: [karyawanId],
                    message_type: messageType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success');
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat mengirim reminder', 'error');
            });
        }
    });
}

// Send Bulk Reminder
function sendBulkReminder() {
    const selectedCheckboxes = document.querySelectorAll('.karyawan-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        Swal.fire('Peringatan!', 'Pilih minimal satu karyawan untuk mengirim reminder', 'warning');
        return;
    }

    const karyawanIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    Swal.fire({
        title: 'Kirim Bulk Reminder?',
        text: `Kirim reminder medical checkup ke ${karyawanIds.length} karyawan terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // For bulk, use general message type
            fetch('{{ route("perusahaan.medical-checkup.send-reminder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    karyawan_ids: karyawanIds,
                    message_type: 'expired' // Default for bulk
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    // Uncheck all checkboxes
                    document.querySelectorAll('.karyawan-checkbox').forEach(cb => cb.checked = false);
                    document.getElementById('selectAll').checked = false;
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat mengirim reminder', 'error');
            });
        }
    });
}

// Export Data
function exportData() {
    Swal.fire({
        title: 'Export Data?',
        text: 'Download data medical checkup karyawan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Download',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement export functionality
            Swal.fire('Info', 'Fitur export akan segera tersedia', 'info');
        }
    });
}

// Determine message type based on karyawan status (you'll need to implement this logic)
function determineMessageType(karyawanId) {
    // This is a simplified version - you might want to pass this data from the backend
    return 'expired'; // Default
}
</script>
@endpush