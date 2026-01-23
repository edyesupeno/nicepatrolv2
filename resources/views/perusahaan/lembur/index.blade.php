@extends('perusahaan.layouts.app')

@section('title', 'Permintaan Lembur')
@section('page-title', 'Permintaan Lembur Karyawan')
@section('page-subtitle', 'Kelola permintaan lembur dan sistem persetujuan')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div id="successAlert" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600 text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <button onclick="document.getElementById('successAlert').remove()" class="text-green-600 hover:text-green-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

@if(session('error'))
<div id="errorAlert" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    <button onclick="document.getElementById('errorAlert').remove()" class="text-red-600 hover:text-red-800 transition">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
@endif

@if(session('import_errors'))
<div id="importErrorAlert" class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-xl">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            <div>
                <span class="font-medium">Import selesai dengan beberapa error</span>
                <p class="text-sm mt-1">{{ count(session('import_errors')) }} baris gagal diimport. 
                    <a href="{{ route('perusahaan.lembur.import-errors') }}" class="underline hover:no-underline font-medium">Lihat detail error</a>
                </p>
            </div>
        </div>
        <button onclick="document.getElementById('importErrorAlert').remove()" class="text-yellow-600 hover:text-yellow-800 transition">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Permintaan -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Permintaan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-list text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Menunggu Persetujuan -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-yellow-100">
                <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Disetujui -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-100">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Ditolak -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Ditolak</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-red-100">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Actions & Filters -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <h2 class="text-xl font-semibold text-gray-900">Daftar Permintaan Lembur</h2>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="showImportModal()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 transition-all duration-200">
            <i class="fas fa-file-import mr-2"></i>
            Import Lembur dari Excel
        </button>
        <a href="{{ route('perusahaan.lembur.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg shadow-sm transition-all duration-200 hover:shadow-md" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-plus mr-2"></i>
            Tambah Permintaan Lembur
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
            <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('tanggal_mulai', now()->format('Y-m-d')) }}">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('tanggal_selesai', now()->format('Y-m-d')) }}">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Karyawan</label>
            <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nama karyawan..." value="{{ request('search') }}">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Lembur</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upah Lembur</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lemburs as $index => $lembur)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lemburs->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lembur->tanggal_lembur->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $lembur->karyawan->nama_lengkap }}</div>
                            <div class="text-sm text-gray-500">{{ $lembur->karyawan->nik_karyawan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lembur->project->nama }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lembur->jam_mulai->format('H:i') }} - {{ $lembur->jam_selesai->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $lembur->total_jam }} jam
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($lembur->total_upah_lembur)
                                <span class="font-medium text-green-600">Rp {{ number_format($lembur->total_upah_lembur, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lembur->status === 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $lembur->status_text }}
                                </span>
                            @elseif($lembur->status === 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $lembur->status_text }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $lembur->status_text }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('perusahaan.lembur.show', $lembur->hash_id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($lembur->canEdit())
                                    <a href="{{ route('perusahaan.lembur.edit', $lembur->hash_id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                @if($lembur->status === 'pending')
                                    <button type="button" class="text-green-600 hover:text-green-900 transition-colors" 
                                            onclick="approveModal('{{ $lembur->hash_id }}')" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="text-red-600 hover:text-red-900 transition-colors" 
                                            onclick="rejectModal('{{ $lembur->hash_id }}')" title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                @if($lembur->canDelete())
                                    <button type="button" class="text-red-600 hover:text-red-900 transition-colors" 
                                            onclick="deleteConfirm('{{ $lembur->hash_id }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium">Tidak ada data permintaan lembur</p>
                                <p class="text-sm">Belum ada permintaan lembur yang dibuat</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($lemburs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $lemburs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border w-full max-w-lg shadow-lg rounded-lg bg-white">
        <!-- Header -->
        <div class="bg-green-500 text-white p-6 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-excel text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Import Lembur dari Excel</h3>
                        <p class="text-sm opacity-90">Upload file Excel (.xlsx)</p>
                    </div>
                </div>
                <button onclick="closeModal('importModal')" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Template Download Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-download text-blue-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="font-medium text-blue-800 mb-2">Download Template Excel</h4>
                        <p class="text-sm text-blue-700 mb-3">
                            Buat template Excel kosong untuk diisi data lembur
                        </p>
                        
                        <!-- Project Selection for Template -->
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-blue-700 mb-1">Project</label>
                            <select id="templateProjectSelect" class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee Selection for Template -->
                        <div id="templateEmployeeSection" class="mb-3 hidden">
                            <label class="block text-xs font-medium text-blue-700 mb-1">Pilih Karyawan</label>
                            <div class="mb-2">
                                <input type="text" 
                                       id="templateEmployeeSearch" 
                                       placeholder="Cari karyawan..." 
                                       class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="selectAllTemplateEmployees()" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Pilih Semua
                                </button>
                                <button type="button" onclick="selectNoneTemplateEmployees()" class="text-xs text-gray-600 hover:text-gray-800 underline">
                                    Hapus Semua
                                </button>
                                <span id="templateSelectedCount" class="text-xs text-gray-500">0 dipilih</span>
                            </div>
                            <div id="templateEmployeeList" class="max-h-32 overflow-y-auto border border-blue-200 rounded p-2 bg-white text-sm">
                                <div class="text-center text-gray-500 py-2">Pilih project terlebih dahulu</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-blue-700 mb-1">Tanggal Mulai</label>
                                <input type="date" id="templateStartDate" class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-blue-700 mb-1">Tanggal Akhir</label>
                                <input type="date" id="templateEndDate" class="w-full px-3 py-2 text-sm border border-blue-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <button type="button" onclick="downloadTemplate()" id="downloadTemplateBtn" disabled class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-download mr-2"></i>
                            Download Template
                        </button>
                    </div>
                </div>
            </div>

            <!-- Separator -->
            <div class="flex items-center">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-3 text-sm text-gray-500 bg-white">ATAU</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            <!-- Import Section -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-upload text-green-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="font-medium text-green-800 mb-2">Import File Excel</h4>
                        <p class="text-sm text-green-700 mb-3">
                            Upload file Excel yang sudah berisi data lembur
                        </p>

                        <!-- File Upload -->
                        <div class="border-2 border-dashed border-green-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors" id="dropZone">
                            <div class="space-y-2">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-cloud-upload-alt text-green-400 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-green-600 font-medium text-sm">Klik untuk pilih file</p>
                                    <p class="text-xs text-green-500">atau drag & drop file di sini</p>
                                </div>
                                <p class="text-xs text-green-400">Format: .xlsx atau .xls (Max: 2MB)</p>
                            </div>
                            <input type="file" id="importFile" name="file" accept=".xlsx,.xls" class="hidden">
                        </div>
                        
                        <!-- Selected file info -->
                        <div id="selectedFileInfo" class="hidden mt-3 p-3 bg-green-100 border border-green-300 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-excel text-green-600"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-green-800" id="fileName"></p>
                                    <p class="text-xs text-green-600" id="fileSize"></p>
                                </div>
                                <button type="button" onclick="clearFile()" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" onclick="processImport()" id="importButton" disabled class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                <i class="fas fa-upload mr-2"></i>
                                Import Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                    <div>
                        <h4 class="font-medium text-yellow-800">Informasi Penting</h4>
                        <ul class="text-sm text-yellow-700 mt-1 space-y-1">
                            <li>• Tarif lembur dihitung otomatis berdasarkan gaji karyawan</li>
                            <li>• File Excel harus menggunakan format template yang benar</li>
                            <li>• Pastikan No Badge karyawan sesuai dengan data di sistem</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
            <button type="button" onclick="closeModal('importModal')" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" onclick="processImport()" id="importButton" disabled class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                <i class="fas fa-upload mr-2"></i>
                Import
            </button>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Setujui Permintaan Lembur</h3>
                <button onclick="closeModal('approveModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="approveForm">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menyetujui permintaan lembur ini?</p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan_approval" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tolak Permintaan Lembur</h3>
                <button onclick="closeModal('rejectModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="rejectForm">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menolak permintaan lembur ini?</p>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_approval" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentLemburId = null;

function showImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    // Set default dates to today
    const today = new Date();
    const todayString = today.toISOString().split('T')[0];
    
    document.getElementById('templateStartDate').value = todayString;
    document.getElementById('templateEndDate').value = todayString;
}

function downloadTemplate() {
    const projectId = document.getElementById('templateProjectSelect').value;
    const startDate = document.getElementById('templateStartDate').value;
    const endDate = document.getElementById('templateEndDate').value;
    
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Project Belum Dipilih',
            text: 'Silakan pilih project terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (selectedTemplateEmployees.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Karyawan Belum Dipilih',
            text: 'Silakan pilih minimal satu karyawan',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (!startDate || !endDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Tanggal Belum Dipilih',
            text: 'Silakan pilih tanggal mulai dan akhir',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        Swal.fire({
            icon: 'warning',
            title: 'Tanggal Tidak Valid',
            text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Download template with parameters
    const employeeIds = selectedTemplateEmployees.join(',');
    const url = `/perusahaan/lembur-template-download?project_id=${projectId}&employee_ids=${employeeIds}&start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}

function processImport() {
    const fileInput = document.getElementById('importFile');
    
    if (!fileInput.files.length) {
        Swal.fire({
            icon: 'warning',
            title: 'File Belum Dipilih',
            text: 'Silakan pilih file Excel terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'CSRF token tidak ditemukan',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('_token', csrfToken.getAttribute('content'));
    
    // Show loading
    Swal.fire({
        title: 'Memproses Import...',
        text: 'Mohon tunggu, sedang memproses file Excel',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit form
    fetch('/perusahaan/lembur-import', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP error! status: ${response.status} - ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        // Force close the loading modal
        Swal.close();
        
        // Close the import modal
        closeModal('importModal');
        
        if (data.success) {
            let message = data.message;
            
            // Show different message based on results
            if (data.data && data.data.has_errors) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Import Selesai dengan Peringatan',
                    html: `${message}<br><br><a href="/perusahaan/lembur-import-errors" class="text-blue-600 underline">Lihat detail error</a>`,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                // Determine icon based on results
                let icon = 'success';
                let title = 'Import Berhasil!';
                
                if (data.data && data.data.silently_skipped_count > 0 && data.data.imported_count === 0) {
                    icon = 'info';
                    title = 'Import Selesai';
                } else if (data.data && data.data.silently_skipped_count > 0) {
                    icon = 'success';
                    title = 'Import Berhasil!';
                }
                
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Import Gagal!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        // Force close the loading modal
        Swal.close();
        
        // Close the import modal
        closeModal('importModal');
        
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses import: ' + error.message,
            confirmButtonText: 'OK'
        });
    });
}

function clearFile() {
    document.getElementById('importFile').value = '';
    document.getElementById('selectedFileInfo').classList.add('hidden');
    updateImportButton();
}

function updateImportButton() {
    const fileInput = document.getElementById('importFile');
    const importButton = document.getElementById('importButton');
    
    // Import button only needs file upload
    if (importButton) {
        importButton.disabled = !fileInput.files.length;
    }
}

function updateTemplateButtons() {
    const projectId = document.getElementById('templateProjectSelect').value;
    const hasEmployees = selectedTemplateEmployees.length > 0;
    const startDate = document.getElementById('templateStartDate').value;
    const endDate = document.getElementById('templateEndDate').value;
    
    // Update download template button - requires all fields
    const downloadBtn = document.getElementById('downloadTemplateBtn');
    if (downloadBtn) {
        downloadBtn.disabled = !projectId || !hasEmployees || !startDate || !endDate;
    }
}

// File upload handling
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('importFile');
    
    // Click to select file
    if (dropZone) {
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });
        
        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-green-400', 'bg-green-50');
        });
        
        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-green-400', 'bg-green-50');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-green-400', 'bg-green-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });
    }
    
    // File input change
    if (fileInput) {
        fileInput.addEventListener('change', handleFileSelect);
    }
    
    // Template project select change
    const templateProjectSelect = document.getElementById('templateProjectSelect');
    if (templateProjectSelect) {
        templateProjectSelect.addEventListener('change', function() {
            const projectId = this.value;
            loadTemplateEmployeesByProject(projectId);
            updateTemplateButtons();
        });
    }
    
    // Date fields for template download
    const startDateField = document.getElementById('templateStartDate');
    const endDateField = document.getElementById('templateEndDate');
    if (startDateField && endDateField) {
        startDateField.addEventListener('change', updateTemplateButtons);
        endDateField.addEventListener('change', updateTemplateButtons);
    }
    
    // Template employee search
    const templateEmployeeSearch = document.getElementById('templateEmployeeSearch');
    if (templateEmployeeSearch) {
        templateEmployeeSearch.addEventListener('input', function() {
            searchTemplateEmployees();
        });
    }
    
    function searchTemplateEmployees() {
        const searchTerm = document.getElementById('templateEmployeeSearch').value.toLowerCase().trim();
        
        if (searchTerm === '') {
            filteredTemplateEmployees = [...allTemplateEmployees];
        } else {
            filteredTemplateEmployees = allTemplateEmployees.filter(employee => 
                employee.nama_lengkap.toLowerCase().includes(searchTerm) ||
                employee.nik_karyawan.toLowerCase().includes(searchTerm)
            );
        }
        
        renderTemplateEmployeeList(filteredTemplateEmployees);
    }
    
    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ];
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Valid',
                    text: 'Hanya file Excel (.xlsx, .xls) yang diperbolehkan',
                    confirmButtonText: 'OK'
                });
                clearFile();
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                    confirmButtonText: 'OK'
                });
                clearFile();
                return;
            }
            
            // Show file info
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = formatFileSize(file.size);
            document.getElementById('selectedFileInfo').classList.remove('hidden');
            
            // Enable import button when file is selected
            updateImportButton();
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});

let selectedTemplateEmployees = [];
let allTemplateEmployees = [];
let filteredTemplateEmployees = [];

// Template employee management functions
function loadTemplateEmployeesByProject(projectId) {
    if (!projectId) {
        document.getElementById('templateEmployeeSection').classList.add('hidden');
        selectedTemplateEmployees = [];
        allTemplateEmployees = [];
        filteredTemplateEmployees = [];
        updateTemplateButtons();
        return;
    }
    
    // Show loading
    document.getElementById('templateEmployeeSection').classList.remove('hidden');
    document.getElementById('templateEmployeeList').innerHTML = `
        <div class="text-center text-gray-500 py-2">
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Memuat...
        </div>
    `;
    
    // Clear search
    document.getElementById('templateEmployeeSearch').value = '';
    
    // Fetch employees
    fetch(`/perusahaan/lembur-karyawan/${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                allTemplateEmployees = data.data;
                filteredTemplateEmployees = [...allTemplateEmployees];
                renderTemplateEmployeeList(filteredTemplateEmployees);
            } else {
                allTemplateEmployees = [];
                filteredTemplateEmployees = [];
                document.getElementById('templateEmployeeList').innerHTML = `
                    <div class="text-center text-gray-500 py-2">
                        <i class="fas fa-users-slash mr-1"></i>
                        Tidak ada karyawan
                    </div>
                `;
            }
        })
        .catch(error => {
            allTemplateEmployees = [];
            filteredTemplateEmployees = [];
            document.getElementById('templateEmployeeList').innerHTML = `
                <div class="text-center text-red-500 py-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Gagal memuat
                </div>
            `;
        });
}

function renderTemplateEmployeeList(employees) {
    const employeeListEl = document.getElementById('templateEmployeeList');
    
    if (employees.length === 0) {
        employeeListEl.innerHTML = `
            <div class="text-center text-gray-500 py-2">
                <i class="fas fa-search mr-1"></i>
                Tidak ada hasil
            </div>
        `;
        return;
    }
    
    const html = employees.map(employee => {
        const isSelected = selectedTemplateEmployees.includes(employee.id);
        return `
            <div class="flex items-center p-1 hover:bg-blue-50 rounded transition-colors">
                <input type="checkbox" 
                       id="temp_emp_${employee.id}" 
                       value="${employee.id}"
                       ${isSelected ? 'checked' : ''}
                       onchange="toggleTemplateEmployee(${employee.id})"
                       class="w-3 h-3 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="temp_emp_${employee.id}" class="ml-2 flex-1 cursor-pointer text-xs">
                    <div class="font-medium text-gray-900">${employee.nama_lengkap}</div>
                    <div class="text-gray-500">${employee.nik_karyawan}</div>
                </label>
            </div>
        `;
    }).join('');
    
    employeeListEl.innerHTML = html;
    updateTemplateSelectedCount();
}

function toggleTemplateEmployee(employeeId) {
    const checkbox = document.getElementById(`temp_emp_${employeeId}`);
    if (checkbox.checked) {
        if (!selectedTemplateEmployees.includes(employeeId)) {
            selectedTemplateEmployees.push(employeeId);
        }
    } else {
        selectedTemplateEmployees = selectedTemplateEmployees.filter(id => id !== employeeId);
    }
    updateTemplateSelectedCount();
    updateTemplateButtons();
}

function selectAllTemplateEmployees() {
    selectedTemplateEmployees = [...new Set([...selectedTemplateEmployees, ...filteredTemplateEmployees.map(emp => emp.id)])];
    
    filteredTemplateEmployees.forEach(employee => {
        const checkbox = document.getElementById(`temp_emp_${employee.id}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
    
    updateTemplateSelectedCount();
    updateTemplateButtons();
}

function selectNoneTemplateEmployees() {
    const filteredIds = filteredTemplateEmployees.map(emp => emp.id);
    selectedTemplateEmployees = selectedTemplateEmployees.filter(id => !filteredIds.includes(id));
    
    filteredTemplateEmployees.forEach(employee => {
        const checkbox = document.getElementById(`temp_emp_${employee.id}`);
        if (checkbox) {
            checkbox.checked = false;
        }
    });
    
    updateTemplateSelectedCount();
    updateTemplateButtons();
}

function updateTemplateSelectedCount() {
    const count = selectedTemplateEmployees.length;
    document.getElementById('templateSelectedCount').textContent = `${count} dipilih`;
}

function approveModal(lemburId) {
    currentLemburId = lemburId;
    document.getElementById('approveModal').classList.remove('hidden');
}

function rejectModal(lemburId) {
    currentLemburId = lemburId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    
    // Reset import modal when closed
    if (modalId === 'importModal') {
        // Reset template section
        const templateProjectSelect = document.getElementById('templateProjectSelect');
        if (templateProjectSelect) {
            templateProjectSelect.value = '';
        }
        
        const templateEmployeeSection = document.getElementById('templateEmployeeSection');
        if (templateEmployeeSection) {
            templateEmployeeSection.classList.add('hidden');
        }
        
        const templateEmployeeSearchEl = document.getElementById('templateEmployeeSearch');
        if (templateEmployeeSearchEl) {
            templateEmployeeSearchEl.value = '';
        }
        
        // Reset import section
        const importFile = document.getElementById('importFile');
        if (importFile) {
            importFile.value = '';
        }
        
        const selectedFileInfo = document.getElementById('selectedFileInfo');
        if (selectedFileInfo) {
            selectedFileInfo.classList.add('hidden');
        }
        
        // Reset template employee data
        selectedTemplateEmployees = [];
        allTemplateEmployees = [];
        filteredTemplateEmployees = [];
        updateTemplateButtons();
        updateImportButton();
    }
}

function deleteConfirm(lemburId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Permintaan lembur ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/lembur/${lemburId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Handle approve form
document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/lembur/${currentLemburId}/approve`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        closeModal('approveModal');
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
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        closeModal('approveModal');
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses permintaan.',
            confirmButtonText: 'OK'
        });
    });
});

// Handle reject form
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/perusahaan/lembur/${currentLemburId}/reject`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        closeModal('rejectModal');
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
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        closeModal('rejectModal');
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses permintaan.',
            confirmButtonText: 'OK'
        });
    });
});
</script>
@endpush