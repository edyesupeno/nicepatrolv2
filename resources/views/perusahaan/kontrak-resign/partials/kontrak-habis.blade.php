<!-- Statistics Cards -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Sudah Resign</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['expired'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Lama Bekerja (2+ Tahun)</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['expiring_soon'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-sm"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['total_contract'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <input type="hidden" name="tab" value="kontrak-habis">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
            <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Karyawan</label>
            <select name="contract_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="expired" {{ request('contract_filter', 'expired') == 'expired' ? 'selected' : '' }}>Sudah Resign</option>
                <option value="expiring_soon" {{ request('contract_filter') == 'expiring_soon' ? 'selected' : '' }}>Lama Bekerja (2+ Tahun)</option>
                <option value="all" {{ request('contract_filter') == 'all' ? 'selected' : '' }}>Semua Karyawan</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Karyawan</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NIK karyawan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($karyawans as $karyawan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">{{ substr($karyawan->nama_lengkap, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500">{{ $karyawan->nik_karyawan }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $karyawan->project->nama }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $karyawan->jabatan->nama ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $karyawan->display_date->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            @if($karyawan->tanggal_keluar)
                                Tanggal Keluar
                            @else
                                Tanggal Masuk
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($karyawan->contract_status === 'expired')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-user-times mr-1"></i>
                                Sudah Resign
                            </span>
                        @elseif($karyawan->contract_status === 'expiring_soon')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Lama Bekerja
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Aktif
                            </span>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">{{ $karyawan->days_info }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('perusahaan.karyawans.show', $karyawan->hash_id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($karyawan->contract_status === 'expired')
                                <span class="text-gray-400" title="Karyawan sudah resign">
                                    <i class="fas fa-user-times"></i>
                                </span>
                            @elseif($karyawan->contract_status === 'expiring_soon' && $karyawan->is_active)
                                <button onclick="createResignFromContract('{{ $karyawan->hash_id }}')" class="text-orange-600 hover:text-orange-900 transition-colors" title="Buat Pengajuan Resign">
                                    <i class="fas fa-user-times"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-file-contract text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg mb-2">Tidak ada data kontrak karyawan</p>
                            <p class="text-gray-400 text-sm">Data akan muncul ketika ada karyawan dengan kontrak yang akan atau sudah habis</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($karyawans->hasPages())
<div class="mt-6">
    {{ $karyawans->links() }}
</div>
@endif

@push('scripts')
<script>
function createResignFromContract(karyawanHashId) {
    Swal.fire({
        title: 'Buat Pengajuan Resign?',
        text: "Apakah Anda ingin membuat pengajuan resign untuk karyawan ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Buat Pengajuan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to create resign with pre-filled data
            window.location.href = `{{ route('perusahaan.kontrak-resign.create-resign') }}?karyawan=${karyawanHashId}&jenis=resign_pribadi`;
        }
    });
}
</script>
@endpush