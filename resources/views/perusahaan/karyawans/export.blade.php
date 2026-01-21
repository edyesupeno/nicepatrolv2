@extends('perusahaan.layouts.app')

@section('title', 'Export Data Karyawan')
@section('page-title', 'Export Data Karyawan')
@section('page-subtitle', 'Export data karyawan ke Excel atau PDF')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('perusahaan.karyawans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Karyawan
        </a>
    </div>

    <!-- Export Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('perusahaan.karyawans.export-excel') }}" method="POST" id="exportForm">
            @csrf
            
            <!-- Header -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="fas fa-file-export text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Export Data Karyawan</h3>
                    <p class="text-sm text-gray-500">Pilih format dan filter data yang akan di-export</p>
                </div>
            </div>

            <!-- Format Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-file-alt mr-2" style="color: #10B981;"></i>Format Export <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="format" value="excel" class="sr-only" checked onchange="updateFormatInfo()">
                        <div class="format-option border-2 border-green-200 bg-green-50 rounded-xl p-4 transition hover:border-green-300">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center">
                                    <i class="fas fa-file-excel text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Excel (.xlsx)</p>
                                    <p class="text-sm text-gray-600">Format spreadsheet dengan semua kolom</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="format" value="pdf" class="sr-only" onchange="updateFormatInfo()">
                        <div class="format-option border-2 border-gray-200 bg-gray-50 rounded-xl p-4 transition hover:border-gray-300">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-500 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">PDF (.pdf)</p>
                                    <p class="text-sm text-gray-600">Format dokumen untuk cetak</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-filter mr-2" style="color: #10B981;"></i>Filter Data (Opsional)
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Project Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Project
                        </label>
                        <select name="project_id" id="projectSelect" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="updatePreview()">
                            <option value="">Semua Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jabatan Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tie mr-2" style="color: #3B82C8;"></i>Jabatan
                        </label>
                        <select name="jabatan_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="updatePreview()">
                            <option value="">Semua Jabatan</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Karyawan Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Status Karyawan
                        </label>
                        <select name="status_karyawan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="updatePreview()">
                            <option value="">Semua Status</option>
                            @foreach($statusKaryawans as $status)
                                <option value="{{ $status->nama }}">{{ $status->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Aktif Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on mr-2" style="color: #3B82C8;"></i>Status Aktif
                        </label>
                        <select name="is_active" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="updatePreview()">
                            <option value="">Semua</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Search Filter -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2" style="color: #3B82C8;"></i>Pencarian
                    </label>
                    <input type="text" name="search" placeholder="Cari nama, email, No Badge, NIK..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" onkeyup="updatePreview()">
                </div>
            </div>

            <!-- Preview Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-blue-900 mb-2">Preview Export</h4>
                        <div id="previewInfo" class="text-sm text-blue-800">
                            <p><strong>Format:</strong> <span id="formatInfo">Excel (.xlsx)</span></p>
                            <p><strong>Data:</strong> <span id="dataInfo">Memuat preview...</span></p>
                            <p><strong>Kolom:</strong> <span id="columnInfo">Excel: 23 kolom | PDF: 12 kolom</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Format Info -->
            <div class="mb-6">
                <div id="excelInfo" class="format-info p-4 bg-green-50 border border-green-200 rounded-xl">
                    <h4 class="font-semibold text-green-900 mb-2">
                        <i class="fas fa-file-excel mr-2"></i>Format Excel
                    </h4>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• Semua kolom data karyawan (22 kolom)</li>
                        <li>• Format yang dapat diedit dan dianalisis</li>
                        <li>• Cocok untuk pengolahan data lebih lanjut</li>
                        <li>• Ukuran file relatif kecil</li>
                    </ul>
                </div>
                <div id="pdfInfo" class="format-info hidden p-4 bg-red-50 border border-red-200 rounded-xl">
                    <h4 class="font-semibold text-red-900 mb-2">
                        <i class="fas fa-file-pdf mr-2"></i>Format PDF
                    </h4>
                    <ul class="text-sm text-red-800 space-y-1">
                        <li>• Format landscape untuk data lengkap</li>
                        <li>• Siap untuk dicetak atau dibagikan</li>
                        <li>• Tampilan profesional dengan header perusahaan</li>
                        <li>• Tidak dapat diedit, hanya untuk viewing</li>
                        <li>• Kolom: No Badge, Nama, Email, Telepon, Project, Jabatan, Jenis Regu, Status, JK, Tgl Masuk, Habis Kontrak, Status Aktif</li>
                    </ul>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 px-6 py-3 text-white rounded-xl font-medium hover:shadow-lg transition" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="fas fa-download mr-2"></i>
                    <span id="exportButtonText">Export Excel</span>
                </button>
                <a href="{{ route('perusahaan.karyawans.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl font-medium hover:bg-gray-600 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateFormatInfo() {
    const format = document.querySelector('input[name="format"]:checked').value;
    const formatInfo = document.getElementById('formatInfo');
    const exportButtonText = document.getElementById('exportButtonText');
    const excelInfo = document.getElementById('excelInfo');
    const pdfInfo = document.getElementById('pdfInfo');
    
    // Update format options styling
    document.querySelectorAll('.format-option').forEach(option => {
        option.classList.remove('border-green-200', 'bg-green-50', 'border-red-200', 'bg-red-50');
        option.classList.add('border-gray-200', 'bg-gray-50');
    });
    
    if (format === 'excel') {
        formatInfo.textContent = 'Excel (.xlsx)';
        exportButtonText.innerHTML = '<i class="fas fa-download mr-2"></i>Export Excel';
        excelInfo.classList.remove('hidden');
        pdfInfo.classList.add('hidden');
        
        // Style excel option
        const excelOption = document.querySelector('input[value="excel"]').closest('label').querySelector('.format-option');
        excelOption.classList.remove('border-gray-200', 'bg-gray-50');
        excelOption.classList.add('border-green-200', 'bg-green-50');
    } else {
        formatInfo.textContent = 'PDF (.pdf)';
        exportButtonText.innerHTML = '<i class="fas fa-download mr-2"></i>Export PDF';
        excelInfo.classList.add('hidden');
        pdfInfo.classList.remove('hidden');
        
        // Style pdf option
        const pdfOption = document.querySelector('input[value="pdf"]').closest('label').querySelector('.format-option');
        pdfOption.classList.remove('border-gray-200', 'bg-gray-50');
        pdfOption.classList.add('border-red-200', 'bg-red-50');
    }
}

function updatePreview() {
    const projectId = document.querySelector('select[name="project_id"]').value;
    const jabatanId = document.querySelector('select[name="jabatan_id"]').value;
    const statusKaryawan = document.querySelector('select[name="status_karyawan"]').value;
    const isActive = document.querySelector('select[name="is_active"]').value;
    const search = document.querySelector('input[name="search"]').value;
    
    // Build preview text
    let filters = [];
    if (projectId) {
        const projectName = document.querySelector(`select[name="project_id"] option[value="${projectId}"]`).textContent;
        filters.push(`Project: ${projectName}`);
    }
    if (jabatanId) {
        const jabatanName = document.querySelector(`select[name="jabatan_id"] option[value="${jabatanId}"]`).textContent;
        filters.push(`Jabatan: ${jabatanName}`);
    }
    if (statusKaryawan) {
        filters.push(`Status: ${statusKaryawan}`);
    }
    if (isActive !== '') {
        const statusText = isActive === '1' ? 'Aktif' : 'Tidak Aktif';
        filters.push(`Status Aktif: ${statusText}`);
    }
    if (search) {
        filters.push(`Pencarian: "${search}"`);
    }
    
    const dataInfo = document.getElementById('dataInfo');
    if (filters.length > 0) {
        dataInfo.textContent = `Data dengan filter: ${filters.join(', ')}`;
    } else {
        dataInfo.textContent = 'Semua data karyawan';
    }
}

// Form submission with loading
document.getElementById('exportForm').addEventListener('submit', function(e) {
    const format = document.querySelector('input[name="format"]:checked').value;
    const formatText = format === 'excel' ? 'Excel' : 'PDF';
    
    Swal.fire({
        title: `Memproses Export ${formatText}...`,
        html: 'Mohon tunggu, sedang menyiapkan file untuk diunduh.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Close loading after a delay (file download should start)
    setTimeout(() => {
        Swal.close();
    }, 3000);
});

// Initialize
updateFormatInfo();
updatePreview();
</script>
@endpush