@extends('perusahaan.layouts.app')

@section('title', 'Export Payroll')
@section('page-title', 'Export Payroll')
@section('page-subtitle', 'Export Data Payroll untuk Import ke iBanking')

@section('content')
<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('perusahaan.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="{{ route('perusahaan.daftar-payroll.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                    Daftar Payroll
                </a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500">Export Payroll</span>
            </div>
        </li>
    </ol>
</nav>

<div class="max-w-2xl mx-auto">
    <!-- Export Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-download text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Export Payroll ke Excel</h3>
                <p class="text-sm text-gray-600">Export data payroll untuk import ke sistem iBanking</p>
            </div>
        </div>

        <form id="exportForm" class="space-y-6">
            @csrf
            
            <!-- Periode (WAJIB) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-1"></i>
                    Periode Gaji <span class="text-red-500">*</span>
                </label>
                <input type="month" id="periode" name="periode" required
                    value="{{ now()->format('Y-m') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Pilih bulan dan tahun periode gaji yang akan di-export</p>
            </div>

            <!-- Project (WAJIB) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building text-gray-400 mr-1"></i>
                    Project <span class="text-red-500">*</span>
                </label>
                <select id="project_id" name="project_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih project yang akan di-export payrollnya</p>
            </div>

            <!-- Jabatan (Opsional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-briefcase text-gray-400 mr-1"></i>
                    Filter Jabatan (Opsional)
                </label>
                <select id="jabatan_id" name="jabatan_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika ingin export semua jabatan</p>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter text-gray-400 mr-1"></i>
                    Status Payroll
                </label>
                <select id="status" name="status"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="approved" selected>Approved (Direkomendasikan)</option>
                    <option value="paid">Paid</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Disarankan pilih "Approved" untuk payroll yang sudah final</p>
            </div>

            <!-- Info Export -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-2">File Excel akan berisi kolom berikut:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 text-xs">
                            <div>• No Badge (NIK Karyawan)</div>
                            <div>• Nama Karyawan</div>
                            <div>• Nama Project</div>
                            <div>• Nama Bank</div>
                            <div>• No Rekening</div>
                            <div>• Nama Pemilik Rekening</div>
                            <div class="md:col-span-2">• Jumlah Gaji Netto (Take Home Pay)</div>
                        </div>
                        <p class="mt-2 text-xs">Format file siap untuk di-import ke sistem iBanking untuk transfer gaji.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i>
                    Export ke Excel
                </button>
                <a href="{{ route('perusahaan.daftar-payroll.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <!-- Preview Info -->
    <div id="previewInfo" class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4 hidden">
        <div class="flex items-start">
            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
            <div class="text-sm text-green-700">
                <p class="font-medium mb-1">Preview Export:</p>
                <div id="previewDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Handle Export Form Submit
document.getElementById('exportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const periode = document.getElementById('periode').value;
    const projectId = document.getElementById('project_id').value;
    const jabatanId = document.getElementById('jabatan_id').value;
    const status = document.getElementById('status').value;
    
    // Validasi WAJIB
    if (!periode) {
        Swal.fire({
            icon: 'error',
            title: 'Periode Wajib Dipilih',
            text: 'Silakan pilih periode terlebih dahulu'
        });
        document.getElementById('periode').focus();
        return;
    }
    
    if (!projectId) {
        Swal.fire({
            icon: 'error',
            title: 'Project Wajib Dipilih',
            text: 'Silakan pilih project terlebih dahulu'
        });
        document.getElementById('project_id').focus();
        return;
    }
    
    // Build export URL
    const params = new URLSearchParams();
    params.append('periode', periode);
    params.append('project_id', projectId);
    if (jabatanId) params.append('jabatan_id', jabatanId);
    if (status && status !== 'all') params.append('status', status);
    
    const exportUrl = '{{ route("perusahaan.daftar-payroll.export") }}?' + params.toString();
    
    // Show confirmation with details
    const projectName = document.getElementById('project_id').selectedOptions[0].text;
    const jabatanName = jabatanId ? document.getElementById('jabatan_id').selectedOptions[0].text : 'Semua Jabatan';
    const statusName = document.getElementById('status').selectedOptions[0].text;
    const periodeFormatted = new Date(periode + '-01').toLocaleDateString('id-ID', {year: 'numeric', month: 'long'});
    
    Swal.fire({
        title: 'Konfirmasi Export',
        html: `
            <div class="text-left">
                <p class="mb-3">Akan export payroll dengan detail:</p>
                <ul class="text-sm space-y-1">
                    <li><strong>Periode:</strong> ${periodeFormatted}</li>
                    <li><strong>Project:</strong> ${projectName}</li>
                    <li><strong>Jabatan:</strong> ${jabatanName}</li>
                    <li><strong>Status:</strong> ${statusName}</li>
                </ul>
                <p class="mt-3 text-xs text-gray-600">File Excel akan didownload otomatis setelah proses selesai.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-download mr-2"></i>Ya, Export!',
        cancelButtonText: 'Batal',
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses Export...',
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
                        </div>
                        <p>Sedang memproses data payroll...</p>
                        <p class="text-sm text-gray-600 mt-2">Mohon tunggu, jangan tutup halaman ini</p>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });
            
            // Start export with fetch for better error handling
            fetch(exportUrl)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Export gagal');
                        });
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `Payroll_Export_${projectName}_${periodeFormatted}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    // Show success
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Berhasil!',
                        html: `
                            <div class="text-center">
                                <p>File Excel telah berhasil didownload</p>
                                <p class="text-sm text-gray-600 mt-2">Silakan cek folder Download Anda</p>
                            </div>
                        `,
                        timer: 3000,
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Gagal!',
                        text: error.message || 'Terjadi kesalahan saat export',
                        confirmButtonText: 'OK'
                    });
                });
        }
    });
});

// Show preview when form changes
function updatePreview() {
    const periode = document.getElementById('periode').value;
    const projectId = document.getElementById('project_id').value;
    
    if (periode && projectId) {
        const projectName = document.getElementById('project_id').selectedOptions[0].text;
        const jabatanName = document.getElementById('jabatan_id').value ? 
            document.getElementById('jabatan_id').selectedOptions[0].text : 'Semua Jabatan';
        const statusName = document.getElementById('status').selectedOptions[0].text;
        const periodeFormatted = new Date(periode + '-01').toLocaleDateString('id-ID', {year: 'numeric', month: 'long'});
        
        document.getElementById('previewDetails').innerHTML = `
            <div class="text-xs space-y-1">
                <div><strong>Periode:</strong> ${periodeFormatted}</div>
                <div><strong>Project:</strong> ${projectName}</div>
                <div><strong>Jabatan:</strong> ${jabatanName}</div>
                <div><strong>Status:</strong> ${statusName}</div>
            </div>
        `;
        document.getElementById('previewInfo').classList.remove('hidden');
    } else {
        document.getElementById('previewInfo').classList.add('hidden');
    }
}

// Add event listeners for preview
document.getElementById('periode').addEventListener('change', updatePreview);
document.getElementById('project_id').addEventListener('change', updatePreview);
document.getElementById('jabatan_id').addEventListener('change', updatePreview);
document.getElementById('status').addEventListener('change', updatePreview);

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