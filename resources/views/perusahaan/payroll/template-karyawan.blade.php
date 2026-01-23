@extends('perusahaan.layouts.app')

@section('title', 'Template Karyawan')
@section('page-title', 'Template Karyawan')
@section('page-subtitle', 'Atur komponen gaji per karyawan spesifik')

@section('content')
<!-- Info Box -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-info-circle text-white text-lg"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-bold text-blue-900 mb-2">Template Karyawan (Level 3 - Prioritas Tertinggi)</h3>
            <div class="space-y-1 text-xs text-blue-800">
                <div class="flex items-start gap-2">
                    <i class="fas fa-user text-blue-600 mt-0.5"></i>
                    <span><strong>Override untuk Karyawan Spesifik:</strong> Template ini akan meng-override template jabatan dan default untuk karyawan yang dipilih</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fas fa-crown text-yellow-600 mt-0.5"></i>
                    <span><strong>Prioritas Tertinggi:</strong> Jika karyawan punya template khusus, nilai ini yang akan digunakan</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-blue-300">
                <div class="flex items-start gap-2 text-xs text-blue-800">
                    <i class="fas fa-lightbulb text-yellow-600 mt-0.5"></i>
                    <span><strong>Contoh:</strong> Budi (Security) dapat Rp 5jt + Uang Makan Rp 50rb (berbeda dari Security lain yang dapat Rp 25rb)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('perusahaan.template-karyawan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Project Filter -->
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

            <!-- Jabatan Filter -->
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

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search text-gray-400 mr-1"></i>
                    Cari Karyawan
                </label>
                <input type="text" name="search" value="{{ $search }}" placeholder="NIK atau Nama..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg font-medium hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
                <button type="button" onclick="openModal('create')" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Template Gaji Default Section (Static Info Card) -->
<div class="mb-6">
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-md border-2 border-yellow-300 p-6">
        <div class="flex items-start gap-4">
            <!-- Left Side: Icon & Title -->
            <div class="flex-shrink-0">
                <div class="w-16 h-16 bg-yellow-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-white text-2xl"></i>
                </div>
            </div>
            
            <!-- Middle: Content -->
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <h2 class="text-xl font-bold text-gray-900">Template Gaji Default</h2>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-200 text-yellow-900">
                        <i class="fas fa-crown mr-1"></i>
                        Otomatis
                    </span>
                </div>
                
                <p class="text-sm text-gray-700 mb-3">
                    Template dasar yang berlaku untuk <strong>semua karyawan</strong>. 
                    Komponen ini akan otomatis diterapkan kecuali ada override di level jabatan atau karyawan.
                </p>
                
                <div class="bg-white/60 rounded-lg p-3 space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-700"><strong>Gaji Pokok</strong> - Dari data karyawan</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-plus-circle text-green-600"></i>
                        <span class="text-gray-700"><strong>BPJS Kesehatan</strong> - Dari setting payroll</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-plus-circle text-green-600"></i>
                        <span class="text-gray-700"><strong>BPJS Ketenagakerjaan</strong> - Dari setting payroll</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-minus-circle text-red-600"></i>
                        <span class="text-gray-700"><strong>Potongan BPJS</strong> - Dari setting payroll</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-minus-circle text-red-600"></i>
                        <span class="text-gray-700"><strong>Pajak PPh 21</strong> - Dari setting payroll</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Info Box -->
            <div class="flex-shrink-0 w-64">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-bold text-blue-900 mb-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Hierarki Template
                    </h3>
                    <div class="space-y-2 text-xs text-blue-800">
                        <div class="flex items-start gap-2">
                            <span class="font-bold text-blue-600">1.</span>
                            <span>Template Default (Gaji Pokok + BPJS)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="font-bold text-blue-600">2.</span>
                            <span>Template Jabatan (jika ada)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="font-bold text-green-600">3.</span>
                            <span><strong>Template Karyawan (prioritas tertinggi)</strong></span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-300">
                        <p class="text-xs text-blue-700">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Tip:</strong> Gunakan untuk karyawan dengan kebutuhan khusus
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates Grid -->
<div class="mb-4">
    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
        <i class="fas fa-user-cog text-blue-500"></i>
        Template Per Karyawan
    </h2>
    <p class="text-sm text-gray-600 mt-1">Template khusus untuk karyawan tertentu (override template jabatan)</p>
</div>

@forelse($groupedByProject as $projectId => $projectTemplates)
    @php
        $project = $projectTemplates->first()->project;
        $totalKaryawan = $projectTemplates->groupBy('karyawan_id')->count();
    @endphp
    
    <!-- Project Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-md p-4 mb-4 cursor-pointer hover:shadow-lg transition" onclick="toggleProject('project-{{ $projectId }}')">
        <div class="flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold">{{ $project->nama }}</h3>
                    <p class="text-sm text-blue-100">{{ $totalKaryawan }} template kustom</p>
                </div>
            </div>
            <i class="fas fa-chevron-down text-xl transition-transform" id="icon-project-{{ $projectId }}"></i>
        </div>
    </div>
    
    <!-- Project Templates -->
    <div id="project-{{ $projectId }}" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($projectTemplates->groupBy('karyawan_id') as $karyawanId => $templateList)
                @php
                    $firstTemplate = $templateList->first();
                    $karyawan = $firstTemplate->karyawan;
                    $jabatan = $firstTemplate->jabatan ?? null;
                    $totalTunjangan = $templateList->where('komponenPayroll.jenis', 'Tunjangan')->sum('nilai');
                    $totalPotongan = $templateList->where('komponenPayroll.jenis', 'Potongan')->sum('nilai');
                @endphp
        
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition">
                    <!-- Card Header -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900">{{ $karyawan->nama_lengkap }}</h3>
                                <p class="text-sm text-gray-600">NIK: {{ $karyawan->nik_karyawan }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="editTemplate('{{ $karyawanId }}')" class="text-blue-600 hover:text-blue-800" title="Edit Template">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteTemplate('{{ $karyawanId }}')" class="text-red-600 hover:text-red-800" title="Hapus Template">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if($firstTemplate->aktif)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Template Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Template Nonaktif
                            </span>
                        @endif
                        
                        @if($firstTemplate->deskripsi)
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($firstTemplate->deskripsi, 60) }}</p>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-3">
                        <!-- Jabatan Info -->
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-briefcase text-gray-400 w-4"></i>
                            <span class="text-gray-600">Jabatan:</span>
                            <span class="font-medium text-gray-900">{{ $jabatan ? $jabatan->nama : 'Tidak ada jabatan' }}</span>
                        </div>
                        
                        <!-- Project Info -->
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-building text-gray-400 w-4"></i>
                            <span class="text-gray-600">Project:</span>
                            <span class="font-medium text-gray-900">{{ $project ? Str::limit($project->nama, 20) : 'Tidak ada project' }}</span>
                        </div>

                        <hr class="my-3">

                        <!-- Info: Template Default tetap berlaku -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-2 mb-3">
                            <p class="text-xs text-blue-800 flex items-start gap-1">
                                <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                                <span><strong>Catatan:</strong> Template default + template jabatan tetap berlaku. Komponen di bawah adalah <strong>tambahan/override</strong>.</span>
                            </p>
                        </div>

                        <!-- Komponen List -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                <i class="fas fa-plus-circle text-xs"></i>
                                Komponen Kustom:
                            </h4>
                            
                            <div class="space-y-1.5">
                                @foreach($templateList as $template)
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            @if($template->komponenPayroll->jenis == 'Tunjangan')
                                                <i class="fas fa-arrow-up text-green-600 flex-shrink-0"></i>
                                            @else
                                                <i class="fas fa-arrow-down text-red-600 flex-shrink-0"></i>
                                            @endif
                                            <span class="text-gray-700 truncate">{{ $template->komponenPayroll->nama_komponen }}</span>
                                        </div>
                                        <span class="font-semibold text-gray-900 ml-2 flex-shrink-0">
                                            @if($template->komponenPayroll->tipe_perhitungan == 'Persentase')
                                                {{ number_format($template->nilai, 0, ',', '.') }}%
                                            @else
                                                {{ number_format($template->nilai, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Summary -->
                        <div class="space-y-1">
                            @if($totalTunjangan > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Total Tunjangan:</span>
                                    <span class="font-bold text-green-600">{{ number_format($totalTunjangan, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            
                            @if($totalPotongan > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Total Potongan:</span>
                                    <span class="font-bold text-red-600">{{ number_format($totalPotongan, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-cog text-gray-400 text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Template Karyawan</h3>
        <p class="text-gray-600 mb-4">Klik tombol "Tambah" untuk membuat template khusus untuk karyawan tertentu</p>
    </div>
@endforelse

<!-- Pagination -->
@if($templates->hasPages())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mt-6">
        {{ $templates->links() }}
    </div>
@endif

<!-- Modal Form -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <form id="templateForm" method="POST" action="{{ route('perusahaan.template-karyawan.store') }}">
            @csrf
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Tambah Template Karyawan</h3>
                <p class="text-sm text-gray-600 mt-1">Atur komponen gaji khusus untuk karyawan tertentu</p>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                
                <!-- Nama Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Template <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_template" id="nama_template" placeholder="Contoh: Template Khusus Budi" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="2" placeholder="Deskripsi template..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Project -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="loadJabatans()">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jabatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jabatan <span class="text-red-500">*</span>
                    </label>
                    <select name="jabatan_id" id="jabatan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="loadKaryawans()">
                        <option value="">Pilih Jabatan</option>
                    </select>
                </div>

                <!-- Karyawan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Karyawan <span class="text-red-500">*</span>
                    </label>
                    <select name="karyawan_id" id="karyawan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="loadJabatanTemplate()">
                        <option value="">Pilih Karyawan</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i>
                        Pilih karyawan - template jabatan akan otomatis dimuat
                    </p>
                </div>

                <!-- Info Template Jabatan (akan muncul setelah pilih karyawan) -->
                <div id="jabatan_template_info" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="flex-1 text-xs text-blue-800">
                            <p class="font-semibold mb-1">Template Jabatan Dimuat</p>
                            <p id="jabatan_template_text"></p>
                        </div>
                    </div>
                </div>
                <!-- Komponen Section -->
                <div class="border-t pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Komponen
                        </label>
                        <button type="button" onclick="addKomponen()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i>Tambah Komponen
                        </button>
                    </div>

                    <div id="komponenContainer" class="space-y-4">
                        <!-- Komponen items will be added here dynamically -->
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal()" 
                    class="px-5 py-2 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition">
                    Batal
                </button>
                <button type="submit" 
                    class="px-5 py-2 text-white rounded-lg font-medium hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const templatesData = @json($templates);
const komponensData = @json($komponens);
let komponenCounter = 0;

function toggleProject(projectId) {
    const content = document.getElementById(projectId);
    const icon = document.getElementById('icon-' + projectId);
    
    if (content && icon) {
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
}

function openModal(mode, karyawanId = null) {
    const modal = document.getElementById('modalForm');
    const form = document.getElementById('templateForm');
    const container = document.getElementById('komponenContainer');
    const infoBox = document.getElementById('jabatan_template_info');
    
    // Reset
    form.reset();
    container.innerHTML = '';
    komponenCounter = 0;
    infoBox.classList.add('hidden');
    
    // Don't add komponen yet - wait for karyawan selection
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('modalForm');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function addKomponen() {
    komponenCounter++;
    const container = document.getElementById('komponenContainer');
    
    const komponenHtml = `
        <div class="komponen-item border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="${komponenCounter}">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-700">Komponen ${komponenCounter}</h4>
                <button type="button" onclick="removeKomponen(${komponenCounter})" class="text-red-600 hover:text-red-800 text-sm">
                    Hapus
                </button>
            </div>
            
            <div class="space-y-3">
                <!-- Komponen Payroll -->
                <div>
                    <select name="komponens[${komponenCounter}][komponen_payroll_id]" id="komponen-select-${komponenCounter}" class="komponen-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required onchange="updateKomponenInfo(${komponenCounter})">
                        <option value="">Pilih Komponen</option>
                        ${komponensData.map(k => `
                            <option value="${k.id}" 
                                data-tipe="${k.tipe_perhitungan}" 
                                data-jenis="${k.jenis}"
                                data-nilai="${k.nilai || 0}">
                                ${k.nama_komponen} - ${k.tipe_perhitungan} (${k.jenis})
                            </option>
                        `).join('')}
                    </select>
                </div>
                
                <!-- Info Tipe -->
                <div id="info-${komponenCounter}" class="hidden bg-blue-50 border border-blue-200 rounded p-2 text-xs text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <span id="info-text-${komponenCounter}"></span>
                </div>
                
                <!-- Nilai -->
                <div>
                    <label class="block text-xs text-gray-600 mb-1">
                        <span id="label-${komponenCounter}">Jumlah Per Hari (Rp)</span> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="komponens[${komponenCounter}][nilai]" id="nilai-${komponenCounter}" placeholder="Contoh: 50.000" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required>
                    <p id="hint-${komponenCounter}" class="text-xs text-gray-500 mt-1">Akan dikalikan dengan jumlah hari kerja</p>
                </div>
                
                <!-- Catatan -->
                <div>
                    <input type="text" name="komponens[${komponenCounter}][catatan]" placeholder="Catatan (opsional)" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', komponenHtml);
    
    // Update disabled options for all selects
    updateDisabledOptions();
}

function removeKomponen(index) {
    const item = document.querySelector(`.komponen-item[data-index="${index}"]`);
    if (item) {
        item.remove();
    }
    
    // Update disabled options after removal
    updateDisabledOptions();
    
    // Renumber remaining items
    const items = document.querySelectorAll('.komponen-item');
    items.forEach((item, idx) => {
        const title = item.querySelector('h4');
        if (title) {
            title.textContent = `Komponen ${idx + 1}`;
        }
    });
}

function updateDisabledOptions() {
    // Get all selected komponen IDs
    const selectedIds = [];
    document.querySelectorAll('.komponen-select').forEach(select => {
        if (select.value) {
            selectedIds.push(select.value);
        }
    });
    
    // Update each select to disable already selected options
    document.querySelectorAll('.komponen-select').forEach(select => {
        const currentValue = select.value;
        
        Array.from(select.options).forEach(option => {
            if (option.value && option.value !== currentValue && selectedIds.includes(option.value)) {
                option.disabled = true;
                option.style.color = '#ccc';
                // Add "(Sudah dipilih)" text if not already there
                if (!option.text.includes('(Sudah dipilih)')) {
                    option.text = option.text + ' (Sudah dipilih)';
                }
            } else if (option.value) {
                option.disabled = false;
                option.style.color = '';
                // Remove "(Sudah dipilih)" text if exists
                option.text = option.text.replace(' (Sudah dipilih)', '');
            }
        });
    });
}

function updateKomponenInfo(index) {
    const select = document.querySelector(`select[name="komponens[${index}][komponen_payroll_id]"]`);
    const selectedOption = select.options[select.selectedIndex];
    const tipe = selectedOption.dataset.tipe;
    const jenis = selectedOption.dataset.jenis;
    const nilaiDefault = selectedOption.dataset.nilai || 0;
    
    const label = document.getElementById(`label-${index}`);
    const hint = document.getElementById(`hint-${index}`);
    const info = document.getElementById(`info-${index}`);
    const infoText = document.getElementById(`info-text-${index}`);
    const nilaiInput = document.getElementById(`nilai-${index}`);
    
    // Auto-fill nilai default dari komponen dengan format separator ribuan
    if (nilaiDefault && parseFloat(nilaiDefault) > 0) {
        const formatted = formatNumber(nilaiDefault);
        nilaiInput.value = formatted;
        nilaiInput.placeholder = `Default: ${formatted}`;
    } else {
        nilaiInput.value = '';
        nilaiInput.placeholder = 'Contoh: 50.000';
    }
    
    if (tipe === 'Tetap') {
        label.textContent = 'Jumlah Tetap (Rp)';
        hint.textContent = 'Nilai tetap setiap bulan';
        infoText.textContent = '💰 Tetap - Nilai tetap setiap bulan';
        info.classList.remove('hidden');
    } else if (tipe === 'Persentase') {
        label.textContent = 'Persentase (%)';
        hint.textContent = 'Persentase dari gaji pokok';
        infoText.textContent = '📊 Persentase - Dikalikan dengan gaji pokok';
        info.classList.remove('hidden');
    } else if (tipe === 'Per Hari Masuk') {
        label.textContent = 'Nilai Per Hari (Rp)';
        hint.textContent = 'Akan dikalikan dengan jumlah hari masuk kerja';
        infoText.textContent = '📅 Per Hari - Dikalikan jumlah hari masuk kerja';
        info.classList.remove('hidden');
    } else if (tipe === 'Lembur Per Hari') {
        label.textContent = 'Nilai Per Hari Lembur (Rp)';
        hint.textContent = 'Akan dikalikan dengan jumlah hari lembur';
        infoText.textContent = '⏰ Lembur Per Hari - Dikalikan jumlah hari lembur';
        info.classList.remove('hidden');
    } else {
        info.classList.add('hidden');
    }
    
    // Update disabled options after selection
    updateDisabledOptions();
}

// Format number with thousand separator
function formatNumber(num) {
    const numValue = parseFloat(num);
    if (isNaN(numValue)) return '';
    const rounded = Math.round(numValue);
    return rounded.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Parse formatted number back to plain number
function parseFormattedNumber(formatted) {
    return formatted.replace(/\./g, '');
}

// Add input event listener for auto-formatting
document.addEventListener('DOMContentLoaded', function() {
    // Delegate event for dynamically added inputs
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name*="[nilai]"]')) {
            const input = e.target;
            const cursorPosition = input.selectionStart;
            const oldLength = input.value.length;
            
            // Get raw value
            const rawValue = parseFormattedNumber(input.value);
            
            // Format with separator
            const formatted = formatNumber(rawValue);
            
            // Update input value
            input.value = formatted;
            
            // Adjust cursor position
            const newLength = formatted.length;
            const diff = newLength - oldLength;
            input.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        }
    });
    
    // Before form submit, convert formatted values back to plain numbers
    const form = document.getElementById('templateForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            document.querySelectorAll('input[name*="[nilai]"]').forEach(input => {
                input.value = parseFormattedNumber(input.value);
            });
        });
    }
});

function loadJabatans() {
    const projectId = document.getElementById('project_id').value;
    const jabatanSelect = document.getElementById('jabatan_id');
    
    if (!projectId) return Promise.resolve();
    
    return fetch(`/perusahaan/jabatans/by-project/${projectId}`)
        .then(response => response.json())
        .then(jabatans => {
            jabatanSelect.innerHTML = '<option value="">Pilih Jabatan</option>';
            jabatans.forEach(jabatan => {
                jabatanSelect.innerHTML += `<option value="${jabatan.id}">${jabatan.nama}</option>`;
            });
        });
}

function loadKaryawans() {
    const projectId = document.getElementById('project_id').value;
    const jabatanId = document.getElementById('jabatan_id').value;
    const karyawanSelect = document.getElementById('karyawan_id');
    
    if (!projectId) return Promise.resolve();
    
    const url = `/perusahaan/template-karyawan/get-karyawans?project_id=${projectId}&jabatan_id=${jabatanId}`;
    
    return fetch(url)
        .then(response => response.json())
        .then(data => {
            karyawanSelect.innerHTML = '<option value="">Pilih Karyawan</option>';
            data.forEach(karyawan => {
                karyawanSelect.innerHTML += `<option value="${karyawan.id}">${karyawan.nik_karyawan} - ${karyawan.nama_lengkap}</option>`;
            });
        });
}

function loadJabatanTemplate() {
    const karyawanId = document.getElementById('karyawan_id').value;
    const container = document.getElementById('komponenContainer');
    const infoBox = document.getElementById('jabatan_template_info');
    const infoText = document.getElementById('jabatan_template_text');
    
    if (!karyawanId) {
        infoBox.classList.add('hidden');
        return;
    }
    
    // Show loading
    infoBox.classList.remove('hidden');
    infoText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat template jabatan...';
    
    // Fetch jabatan template for this karyawan
    fetch(`/perusahaan/template-karyawan/get-jabatan-template?karyawan_id=${karyawanId}`)
        .then(response => response.json())
        .then(data => {
            // Clear existing komponens
            container.innerHTML = '';
            komponenCounter = 0;
            
            if (data.has_template && data.komponens.length > 0) {
                // Show info
                infoText.innerHTML = `Template jabatan "<strong>${data.template_info.nama_template}</strong>" dimuat dengan ${data.komponens.length} komponen. Anda bisa edit nilai atau tambah komponen baru.`;
                
                // Auto-fill nama template if empty
                const namaTemplateInput = document.getElementById('nama_template');
                if (!namaTemplateInput.value) {
                    namaTemplateInput.value = `Template ${data.karyawan.nama}`;
                }
                
                // Load komponens from jabatan template
                data.komponens.forEach(komponen => {
                    addKomponen();
                    const index = komponenCounter;
                    document.querySelector(`select[name="komponens[${index}][komponen_payroll_id]"]`).value = komponen.komponen_payroll_id;
                    document.querySelector(`input[name="komponens[${index}][nilai]"]`).value = formatNumber(komponen.nilai);
                    document.querySelector(`input[name="komponens[${index}][catatan]"]`).value = komponen.catatan || '';
                    updateKomponenInfo(index);
                });
            } else {
                // No template found
                infoText.innerHTML = 'Jabatan ini belum memiliki template. Silakan tambah komponen secara manual.';
                
                // Auto-fill nama template
                const namaTemplateInput = document.getElementById('nama_template');
                if (!namaTemplateInput.value) {
                    namaTemplateInput.value = `Template ${data.karyawan.nama}`;
                }
                
                // Add one empty komponen
                addKomponen();
            }
        })
        .catch(error => {
            console.error('Error loading jabatan template:', error);
            infoText.innerHTML = 'Gagal memuat template jabatan. Silakan tambah komponen secara manual.';
            
            // Add one empty komponen
            if (komponenCounter === 0) {
                addKomponen();
            }
        });
}

function deleteTemplate(karyawanId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        html: `Template karyawan ini dan semua komponennya akan dihapus permanen!`,
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
            form.action = '{{ route("perusahaan.template-karyawan.destroy-by-name") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const karyawanIdInput = document.createElement('input');
            karyawanIdInput.type = 'hidden';
            karyawanIdInput.name = 'karyawan_id';
            karyawanIdInput.value = karyawanId;
            
            form.appendChild(csrfToken);
            form.appendChild(karyawanIdInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function editTemplate(karyawanId) {
    // Fetch template data
    fetch(`/perusahaan/template-karyawan/get-by-karyawan?karyawan_id=${karyawanId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error
                });
                return;
            }
            
            // Open modal
            const modal = document.getElementById('modalForm');
            const form = document.getElementById('templateForm');
            const container = document.getElementById('komponenContainer');
            
            // Reset
            form.reset();
            container.innerHTML = '';
            komponenCounter = 0;
            
            // Populate form
            document.getElementById('nama_template').value = data.nama_template;
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('project_id').value = data.project_id;
            
            // Load jabatans
            loadJabatans().then(() => {
                if (data.jabatan_id) {
                    document.getElementById('jabatan_id').value = data.jabatan_id;
                }
                
                if (data.karyawan_id) {
                    loadKaryawans().then(() => {
                        document.getElementById('karyawan_id').value = data.karyawan_id;
                    });
                }
            });
            
            // Add komponens
            data.komponens.forEach(komponen => {
                addKomponen();
                const index = komponenCounter;
                document.querySelector(`select[name="komponens[${index}][komponen_payroll_id]"]`).value = komponen.komponen_payroll_id;
                document.querySelector(`input[name="komponens[${index}][nilai]"]`).value = formatNumber(komponen.nilai);
                document.querySelector(`input[name="komponens[${index}][catatan]"]`).value = komponen.catatan || '';
                updateKomponenInfo(index);
            });
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data template'
            });
        });
}

// Success/Error messages
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 2000,
    showConfirmButton: false
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
