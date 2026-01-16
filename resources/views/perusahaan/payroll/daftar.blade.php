@extends('perusahaan.layouts.app')

@section('title', 'Daftar Payroll')
@section('page-title', 'Daftar Payroll')
@section('page-subtitle', 'Kelola Slip Gaji Karyawan')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Payroll -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Payroll</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Draft -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Draft</p>
                <h3 class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-edit text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Approved -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Approved</p>
                <h3 class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Gaji -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Gaji Netto</p>
                <h3 class="text-xl font-bold text-gray-900">Rp {{ number_format($stats['total_gaji_netto'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('perusahaan.daftar-payroll.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Periode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-1"></i>
                    Periode
                </label>
                <input type="month" name="periode" value="{{ $periode }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building text-gray-400 mr-1"></i>
                    Project
                </label>
                <select name="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jabatan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-briefcase text-gray-400 mr-1"></i>
                    Jabatan
                </label>
                <select name="jabatan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ $jabatanId == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter text-gray-400 mr-1"></i>
                    Status
                </label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search text-gray-400 mr-1"></i>
                    Cari
                </label>
                <input type="text" name="search" value="{{ $search }}" placeholder="NIK atau Nama..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2 text-white rounded-lg font-medium hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            <a href="{{ route('perusahaan.payroll.generate') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>
                Generate Payroll
            </a>
            @if($stats['draft'] > 0)
                <button type="button" onclick="bulkApprove()" class="px-6 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">
                    <i class="fas fa-check-double mr-2"></i>
                    Bulk Approve
                </button>
            @endif
        </div>
    </form>
</div>

<!-- Payroll Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($payrolls->isEmpty())
        <div class="p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-invoice-dollar text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Payroll</h3>
            <p class="text-gray-600 mb-4">Klik tombol "Generate Payroll" untuk membuat slip gaji</p>
            <a href="{{ route('perusahaan.payroll.generate') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                <i class="fas fa-plus"></i>
                Generate Payroll
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select_all" class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" onchange="toggleSelectAll()">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Bruto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Netto</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @if($payroll->status == 'draft')
                                    <input type="checkbox" class="payroll-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" value="{{ $payroll->id }}">
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $payroll->karyawan->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">{{ $payroll->karyawan->nik_karyawan }} • {{ $payroll->karyawan->jabatan->nama ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $payroll->project->nama }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600">Masuk:</span>
                                        <span class="font-medium text-green-600">{{ $payroll->hari_masuk }}/{{ $payroll->hari_kerja }}</span>
                                    </div>
                                    @if($payroll->hari_alpha > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600">Alpha:</span>
                                            <span class="font-medium text-red-600">{{ $payroll->hari_alpha }}</span>
                                        </div>
                                    @endif
                                    @if($payroll->hari_lembur > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600">Lembur:</span>
                                            <span class="font-medium text-purple-600">{{ $payroll->hari_lembur }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($payroll->gaji_bruto, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold text-green-600">Rp {{ number_format($payroll->gaji_netto, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($payroll->status == 'draft')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-edit mr-1"></i>
                                        Draft
                                    </span>
                                @elseif($payroll->status == 'approved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-money-check-alt mr-1"></i>
                                        Paid
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('perusahaan.daftar-payroll.show', $payroll->hash_id) }}" 
                                        class="text-blue-600 hover:text-blue-800" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($payroll->status == 'draft')
                                        <button onclick="approvePayroll('{{ $payroll->hash_id }}')" 
                                            class="text-green-600 hover:text-green-800" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="deletePayroll('{{ $payroll->hash_id }}')" 
                                            class="text-red-600 hover:text-red-800" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payrolls->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payrolls->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('select_all');
    const checkboxes = document.querySelectorAll('.payroll-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
    });
}

function bulkApprove() {
    const checkboxes = document.querySelectorAll('.payroll-checkbox:checked');
    
    if (checkboxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Payroll Dipilih',
            text: 'Pilih minimal 1 payroll untuk di-approve'
        });
        return;
    }
    
    const payrollIds = Array.from(checkboxes).map(cb => cb.value);
    
    Swal.fire({
        title: 'Konfirmasi Bulk Approve',
        html: `Approve <strong>${payrollIds.length} payroll</strong> sekaligus?<br><br>Payroll yang sudah di-approve tidak bisa diubah lagi.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.daftar-payroll.bulk-approve") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            payrollIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'payroll_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function approvePayroll(hashId) {
    Swal.fire({
        title: 'Konfirmasi Approve',
        text: 'Approve payroll ini? Payroll yang sudah di-approve tidak bisa diubah lagi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/daftar-payroll/${hashId}/approve`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function deletePayroll(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Payroll ini akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/daftar-payroll/${hashId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Success/Error messages
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: true
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '{{ session('error') }}'
});
@endif
</script>
@endpush
