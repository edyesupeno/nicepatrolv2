@extends('perusahaan.layouts.app')

@section('title', 'Rekening')
@section('page-title', 'Rekening')
@section('page-subtitle', 'Kelola rekening bank per project')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-university text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Rekening</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rekening Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-wallet text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Saldo</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_saldo'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Project Terdaftar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['by_project']->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <form method="GET" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <!-- Project Filter -->
                    <select name="project_id" class="rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>

                    <!-- Jenis Filter -->
                    <select name="jenis" class="rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="operasional" {{ request('jenis') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="payroll" {{ request('jenis') == 'payroll' ? 'selected' : '' }}>Payroll</option>
                        <option value="investasi" {{ request('jenis') == 'investasi' ? 'selected' : '' }}>Investasi</option>
                        <option value="emergency" {{ request('jenis') == 'emergency' ? 'selected' : '' }}>Emergency Fund</option>
                        <option value="lainnya" {{ request('jenis') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>

                    <!-- Search -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari rekening..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </form>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <a href="{{ route('perusahaan.keuangan.rekening.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Rekening
                </a>
            </div>
        </div>
    </div>

    <!-- Rekening Cards -->
    @if($rekenings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rekenings as $rekening)
                <div class="relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Card Header with Color -->
                    <div class="h-2" style="background-color: {{ $rekening->warna_card }}"></div>
                    
                    <!-- Card Content -->
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $rekening->nama_rekening }}</h3>
                                    @if($rekening->is_primary)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>Primary
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">{{ $rekening->project->nama }}</p>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex items-center space-x-2">
                                @if($rekening->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Bank Info -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-university w-4 mr-2"></i>
                                <span>{{ $rekening->nama_bank }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-credit-card w-4 mr-2"></i>
                                <span>{{ $rekening->formatted_nomor_rekening }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user w-4 mr-2"></i>
                                <span>{{ $rekening->nama_pemilik }}</span>
                            </div>
                        </div>

                        <!-- Jenis & Saldo -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                  style="background-color: {{ $rekening->warna_card }}20; color: {{ $rekening->warna_card }}">
                                {{ $rekening->jenis_rekening_label }}
                            </span>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Saldo Saat Ini</p>
                                <p class="text-lg font-bold" style="color: {{ $rekening->warna_card }}">
                                    {{ $rekening->formatted_saldo_saat_ini }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex space-x-2">
                                <a href="{{ route('perusahaan.keuangan.rekening.show', $rekening->hash_id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                <a href="{{ route('perusahaan.keuangan.rekening.edit', $rekening->hash_id) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                            </div>
                            
                            <div class="flex space-x-2">
                                @if(!$rekening->is_primary)
                                    <button onclick="setPrimary('{{ $rekening->hash_id }}')" 
                                            class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                        <i class="fas fa-star mr-1"></i>Set Primary
                                    </button>
                                @endif
                                
                                <button onclick="toggleStatus('{{ $rekening->hash_id }}', {{ $rekening->is_active ? 'false' : 'true' }})" 
                                        class="text-{{ $rekening->is_active ? 'red' : 'green' }}-600 hover:text-{{ $rekening->is_active ? 'red' : 'green' }}-800 text-sm font-medium">
                                    <i class="fas fa-{{ $rekening->is_active ? 'times' : 'check' }}-circle mr-1"></i>
                                    {{ $rekening->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                                
                                <button onclick="deleteRekening('{{ $rekening->hash_id }}', '{{ $rekening->nama_rekening }}')" 
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            {{ $rekenings->withQueryString()->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-university text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Rekening</h3>
            <p class="text-gray-500 mb-6">Mulai dengan menambahkan rekening bank untuk project Anda.</p>
            <a href="{{ route('perusahaan.keuangan.rekening.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Rekening Pertama
            </a>
        </div>
    @endif
</div>

<script>
function toggleStatus(hashId, newStatus) {
    const action = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: `Konfirmasi ${action.charAt(0).toUpperCase() + action.slice(1)}`,
        text: `Apakah Anda yakin ingin ${action} rekening ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82C8',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `Ya, ${action.charAt(0).toUpperCase() + action.slice(1)}`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/keuangan/rekening/${hashId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
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
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    });
}

function setPrimary(hashId) {
    Swal.fire({
        title: 'Set Rekening Utama',
        text: 'Apakah Anda yakin ingin menjadikan rekening ini sebagai rekening utama?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82C8',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Set Primary',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/perusahaan/keuangan/rekening/${hashId}/set-primary`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
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
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        }
    });
}

function deleteRekening(hashId, namaRekening) {
    Swal.fire({
        title: 'Hapus Rekening',
        html: `Apakah Anda yakin ingin menghapus rekening <strong>${namaRekening}</strong>?<br><br><small class="text-red-600">Tindakan ini tidak dapat dibatalkan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/keuangan/rekening/${hashId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection