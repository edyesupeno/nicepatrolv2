@extends('perusahaan.layouts.app')

@section('title', 'Generate Payroll')
@section('page-title', 'Generate Payroll')
@section('page-subtitle', 'Generate slip gaji karyawan untuk periode tertentu')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Form -->
    <div class="lg:col-span-2 relative z-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('perusahaan.payroll.store') }}" id="generateForm">
                @csrf
                
                <!-- Periode -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Periode <span class="text-red-500">*</span>
                    </label>
                    @if($setting && $setting->periode_auto_generate)
                        @php
                            // Calculate periode based on cutoff date
                            $cutoffDate = $setting->periode_cutoff_tanggal;
                            $today = now();
                            $currentDay = $today->day;
                            
                            // If today is after or equal cutoff, we can generate for current month
                            // If today is before cutoff, we generate for previous month
                            if ($currentDay >= $cutoffDate) {
                                $defaultPeriode = $today->format('Y-m');
                            } else {
                                $defaultPeriode = $today->copy()->subMonth()->format('Y-m');
                            }
                        @endphp
                        <input type="month" name="periode" id="periode" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                            required value="{{ old('periode', $defaultPeriode) }}">
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-check-circle"></i>
                            Periode otomatis mengikuti pengaturan cutoff tanggal {{ $cutoffDate }}
                        </p>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                                <input type="date" name="periode_start" id="periode_start" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                    required value="{{ old('periode_start', now()->startOfMonth()->format('Y-m-d')) }}"
                                    onchange="updatePeriodeLabel()">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                                <input type="date" name="periode_end" id="periode_end" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                    required value="{{ old('periode_end', now()->endOfMonth()->format('Y-m-d')) }}"
                                    onchange="updatePeriodeLabel()">
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 mt-2" id="periode_label">
                            <i class="fas fa-info-circle"></i>
                            Periode: <span id="periode_display">{{ now()->startOfMonth()->format('d M Y') }} - {{ now()->endOfMonth()->format('d M Y') }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            Periode maksimal <strong>31 hari</strong>. Absensi dan hari kerja akan dihitung berdasarkan periode tanggal ini.
                        </p>
                    @endif
                </div>

                <!-- Pilih Project -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                        required onchange="loadKaryawans()">
                        <option value="">-- Pilih Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-yellow-600 mt-1" id="project_warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Wajib pilih project untuk mencegah data terlalu besar
                    </p>
                </div>

                <!-- Filter Jabatan (optional) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Filter Jabatan (opsional)
                    </label>
                    <select name="jabatan_id" id="jabatan_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        onchange="filterKaryawansByJabatan()" disabled>
                        <option value="">Pilih project terlebih dahulu</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1" id="jabatan_info">Pilih project terlebih dahulu</p>
                </div>

                <!-- Pilih Karyawan (optional) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center justify-between">
                        <span>Pilih Karyawan (opsional)</span>
                        <button type="button" onclick="toggleSelectAll()" class="text-blue-600 hover:text-blue-800 text-sm">
                            <span id="select_all_text">Pilih Semua</span>
                        </button>
                    </label>
                    
                    <!-- Search Box for Karyawan -->
                    <div class="mb-3" id="karyawan_search_container" style="display: none;">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="karyawan_search" 
                                   placeholder="Cari nama karyawan atau NIK..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   onkeyup="searchKaryawan()">
                        </div>
                        <div class="flex items-center justify-between mt-2 text-xs text-gray-600">
                            <span id="search_result_info"></span>
                            <div class="flex gap-2">
                                <button type="button" onclick="clearKaryawanSearch()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-times mr-1"></i>Reset Pencarian
                                </button>
                                <button type="button" onclick="clearAllFilters()" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-refresh mr-1"></i>Reset Semua
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="karyawan_container" class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                        <p class="text-gray-500 text-sm" id="karyawan_placeholder">Pilih project terlebih dahulu</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <span id="karyawan_count">0 karyawan</span> akan diproses
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3">
                    <button type="submit" id="submit_btn" disabled
                        class="flex-1 px-6 py-3 text-white rounded-lg font-medium hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed" 
                        style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-magic mr-2"></i>
                        Generate Payroll
                    </button>
                    <a href="{{ route('perusahaan.daftar-payroll.index') }}" 
                        class="px-6 py-3 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Info -->
    <div class="space-y-6 relative z-0">
        <!-- Pengaturan Periode -->
        @if($setting && $setting->periode_auto_generate)
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-check text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-green-900 mb-2">✅ Auto Generate Aktif</h3>
                    <ul class="space-y-1 text-xs text-green-800">
                        <li>• Cutoff: Tanggal <strong>{{ $setting->periode_cutoff_tanggal }}</strong> setiap bulan</li>
                        <li>• Pembayaran: Tanggal <strong>{{ $setting->periode_pembayaran_tanggal }}</strong> setiap bulan</li>
                        <li>• Periode otomatis disesuaikan dengan cutoff</li>
                        <li>• Contoh: Cutoff tgl 25 → Periode 26 bulan lalu - 25 bulan ini</li>
                    </ul>
                </div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-yellow-900 mb-2">⚠️ Pengaturan Periode Belum Aktif</h3>
                    <p class="text-xs text-yellow-800 mb-2">Periode payroll saat ini diisi <strong>manual</strong>. Periode akan diambil dari waktu saat generate payroll.</p>
                    <p class="text-xs text-yellow-800 mb-2">Untuk mengaktifkan periode otomatis (berlaku untuk semua project):</p>
                    <ol class="space-y-1 text-xs text-yellow-800 list-decimal list-inside mb-3">
                        <li>Buka <a href="{{ route('perusahaan.setting-payroll.index') }}" class="underline font-semibold">Pengaturan Payroll</a></li>
                        <li>Pilih tab <strong>Periode</strong></li>
                        <li>Centang <strong>"Aktifkan Pengaturan Periode Otomatis"</strong></li>
                        <li>Atur tanggal cutoff dan pembayaran</li>
                    </ol>
                    <div class="bg-yellow-100 rounded p-2 border border-yellow-300">
                        <p class="text-xs text-yellow-900"><strong>Catatan:</strong> Pengaturan otomatis hanya cocok jika <strong>SEMUA PROJECT</strong> memiliki tanggal gajian yang sama. Jika berbeda, gunakan input manual.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Informasi -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-blue-900 mb-2">📋 Informasi</h3>
                    <ul class="space-y-1 text-xs text-blue-800">
                        <li>• <strong>Project wajib dipilih</strong> untuk mencegah data terlalu besar</li>
                        <li>• Payroll akan di-generate dengan status <strong>draft</strong></li>
                        <li>• Perhitungan otomatis: gaji, tunjangan, BPJS, pajak</li>
                        @if(!$setting || !$setting->periode_auto_generate)
                        <li>• <strong>Hari kerja dihitung</strong> berdasarkan periode tanggal (Senin-Jumat)</li>
                        <li>• <strong>Absensi diambil</strong> dari tanggal mulai sampai tanggal akhir</li>
                        @endif
                        <li>• Lembur yang sudah approved akan dihitung</li>
                        <li>• Payroll yang sudah ada akan dilewati</li>
                        <li>• Review dulu sebelum approve</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tips untuk Data Besar -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-yellow-900 mb-2">💡 Tips untuk Data Besar</h3>
                    <ul class="space-y-1 text-xs text-yellow-800">
                        <li>• <strong>Jika lebih 50 karyawan</strong> akan diproses</li>
                        <li>• Gunakan filter Project & Jabatan untuk batch kecil</li>
                        <li>• Contoh: Generate per jabatan (Staff, Manager, Security)</li>
                        <li>• Estimasi waktu: ~2 menit</li>
                        <li>• Jangan tutup halaman saat proses berjalan</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Template Hierarchy -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-layer-group text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-green-900 mb-2">🎯 Template Hierarchy</h3>
                    <p class="text-xs text-green-800 mb-2">Sistem otomatis apply komponen dengan prioritas:</p>
                    <ol class="space-y-1 text-xs text-green-800">
                        <li><strong>1. Employee Components</strong> (tertinggi)</li>
                        <li><strong>2. Position Template</strong></li>
                        <li><strong>3. Project Template</strong> (default)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let allKaryawans = [];
let filteredKaryawans = [];
let selectedKaryawans = [];
let currentSearchTerm = '';
let projectJabatans = [];

function loadKaryawans() {
    const projectId = document.getElementById('project_id').value;
    const container = document.getElementById('karyawan_container');
    const placeholder = document.getElementById('karyawan_placeholder');
    const submitBtn = document.getElementById('submit_btn');
    const jabatanInfo = document.getElementById('jabatan_info');
    const searchContainer = document.getElementById('karyawan_search_container');
    const jabatanSelect = document.getElementById('jabatan_id');
    
    if (!projectId) {
        container.innerHTML = '<p class="text-gray-500 text-sm" id="karyawan_placeholder">Pilih project terlebih dahulu</p>';
        submitBtn.disabled = true;
        jabatanInfo.textContent = 'Pilih project terlebih dahulu';
        searchContainer.style.display = 'none';
        
        // Reset jabatan dropdown
        jabatanSelect.innerHTML = '<option value="">Pilih project terlebih dahulu</option>';
        jabatanSelect.disabled = true;
        
        updateKaryawanCount();
        return;
    }
    
    // Show loading
    container.innerHTML = '<p class="text-gray-500 text-sm"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat karyawan...</p>';
    submitBtn.disabled = true;
    searchContainer.style.display = 'none';
    
    // Show loading for jabatan
    jabatanSelect.innerHTML = '<option value="">Memuat jabatan...</option>';
    jabatanSelect.disabled = true;
    
    // Fetch karyawans and jabatans simultaneously
    Promise.all([
        fetch(`/perusahaan/karyawan/by-project/${projectId}`).then(r => r.json()),
        fetch(`/perusahaan/jabatans/by-project/${projectId}`).then(r => r.json())
    ])
    .then(([karyawansData, jabatansData]) => {
        allKaryawans = karyawansData;
        filteredKaryawans = karyawansData;
        selectedKaryawans = [];
        currentSearchTerm = '';
        projectJabatans = jabatansData;
        
        // Clear search
        document.getElementById('karyawan_search').value = '';
        
        // Load jabatan dropdown
        loadJabatanDropdown(jabatansData);
        
        if (karyawansData.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada karyawan aktif di project ini</p>';
            submitBtn.disabled = true;
            jabatanInfo.textContent = 'Tidak ada karyawan';
            searchContainer.style.display = 'none';
        } else {
            renderKaryawans(karyawansData);
            submitBtn.disabled = false;
            jabatanInfo.textContent = `Ditemukan ${karyawansData.length} karyawan, ${jabatansData.length} jabatan`;
            searchContainer.style.display = 'block';
            updateSearchInfo();
        }
        
        updateKaryawanCount();
    })
    .catch(error => {
        console.error('Error loading data:', error);
        container.innerHTML = '<p class="text-red-500 text-sm">Gagal memuat data</p>';
        submitBtn.disabled = true;
        searchContainer.style.display = 'none';
        
        jabatanSelect.innerHTML = '<option value="">Gagal memuat jabatan</option>';
        jabatanSelect.disabled = true;
    });
}

function loadJabatanDropdown(jabatans) {
    const jabatanSelect = document.getElementById('jabatan_id');
    
    // Clear existing options
    jabatanSelect.innerHTML = '<option value="">Semua Jabatan</option>';
    
    // Add jabatan options
    jabatans.forEach(jabatan => {
        const option = document.createElement('option');
        option.value = jabatan.id;
        option.textContent = `${jabatan.nama} (${jabatan.karyawan_count} karyawan)`;
        jabatanSelect.appendChild(option);
    });
    
    // Enable dropdown
    jabatanSelect.disabled = false;
}

function renderKaryawans(karyawans) {
    const container = document.getElementById('karyawan_container');
    
    if (karyawans.length === 0) {
        if (currentSearchTerm) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-search text-gray-300 text-3xl mb-2"></i>
                    <p class="text-gray-500 text-sm">Tidak ada karyawan yang ditemukan</p>
                    <p class="text-gray-400 text-xs">Coba ubah kata kunci pencarian</p>
                    <button onclick="clearKaryawanSearch()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-times mr-1"></i>Reset Pencarian
                    </button>
                </div>
            `;
        } else {
            container.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada karyawan</p>';
        }
        return;
    }
    
    let html = '<div class="space-y-2">';
    karyawans.forEach(karyawan => {
        const isChecked = selectedKaryawans.includes(karyawan.id);
        html += `
            <label class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer border border-transparent hover:border-gray-200 transition-all">
                <input type="checkbox" name="karyawan_ids[]" value="${karyawan.id}" 
                    ${isChecked ? 'checked' : ''}
                    class="karyawan-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                    onchange="updateKaryawanCount()">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-sm font-medium text-gray-900 truncate">${karyawan.nama_lengkap}</p>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                            ${karyawan.jabatan ? karyawan.jabatan.nama : 'Belum Ada Jabatan'}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600">NIK: ${karyawan.nik_karyawan}</p>
                    ${karyawan.gaji_pokok ? `<p class="text-xs text-green-600">Gaji: Rp ${new Intl.NumberFormat('id-ID').format(karyawan.gaji_pokok)}</p>` : ''}
                </div>
            </label>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function searchKaryawan() {
    const searchTerm = document.getElementById('karyawan_search').value.toLowerCase();
    currentSearchTerm = searchTerm;
    
    if (!searchTerm) {
        // No search term, apply jabatan filter if any
        filterKaryawansByJabatan();
        return;
    }
    
    // Get base data (either all karyawans or filtered by jabatan)
    const jabatanId = document.getElementById('jabatan_id').value;
    let baseData = allKaryawans;
    
    if (jabatanId) {
        baseData = allKaryawans.filter(k => k.jabatan_id == jabatanId);
    }
    
    // Apply search filter
    const searchResults = baseData.filter(karyawan => {
        const nama = karyawan.nama_lengkap.toLowerCase();
        const nik = karyawan.nik_karyawan.toLowerCase();
        const jabatan = karyawan.jabatan ? karyawan.jabatan.nama.toLowerCase() : '';
        
        return nama.includes(searchTerm) || 
               nik.includes(searchTerm) || 
               jabatan.includes(searchTerm);
    });
    
    filteredKaryawans = searchResults;
    renderKaryawans(searchResults);
    updateSearchInfo();
    updateKaryawanCount();
}

function clearKaryawanSearch() {
    document.getElementById('karyawan_search').value = '';
    currentSearchTerm = '';
    filterKaryawansByJabatan(); // This will reset to jabatan filter or show all
    updateSearchInfo();
}

function clearAllFilters() {
    // Clear search
    document.getElementById('karyawan_search').value = '';
    currentSearchTerm = '';
    
    // Clear jabatan filter
    document.getElementById('jabatan_id').value = '';
    
    // Reset to show all karyawans
    filteredKaryawans = allKaryawans;
    renderKaryawans(allKaryawans);
    updateSearchInfo();
    updateKaryawanCount();
    
    // Update jabatan info
    const jabatanInfo = document.getElementById('jabatan_info');
    jabatanInfo.textContent = `Ditemukan ${allKaryawans.length} karyawan, ${projectJabatans.length} jabatan`;
}

function updateSearchInfo() {
    const searchInfo = document.getElementById('search_result_info');
    const totalKaryawan = allKaryawans.length;
    const filteredCount = filteredKaryawans.length;
    const jabatanId = document.getElementById('jabatan_id').value;
    
    if (currentSearchTerm && jabatanId) {
        const selectedJabatan = projectJabatans.find(j => j.id == jabatanId);
        const jabatanName = selectedJabatan ? selectedJabatan.nama : 'Jabatan';
        searchInfo.textContent = `Menampilkan ${filteredCount} dari ${totalKaryawan} karyawan (pencarian + filter ${jabatanName})`;
    } else if (currentSearchTerm) {
        searchInfo.textContent = `Menampilkan ${filteredCount} dari ${totalKaryawan} karyawan (pencarian)`;
    } else if (jabatanId) {
        const selectedJabatan = projectJabatans.find(j => j.id == jabatanId);
        const jabatanName = selectedJabatan ? selectedJabatan.nama : 'Jabatan';
        searchInfo.textContent = `Menampilkan ${filteredCount} karyawan (filter ${jabatanName})`;
    } else {
        searchInfo.textContent = `${totalKaryawan} karyawan tersedia`;
    }
}

function filterKaryawansByJabatan() {
    const jabatanId = document.getElementById('jabatan_id').value;
    const searchTerm = document.getElementById('karyawan_search').value.toLowerCase();
    
    let baseData = allKaryawans;
    
    // Apply jabatan filter first
    if (jabatanId) {
        baseData = allKaryawans.filter(k => k.jabatan_id == jabatanId);
    }
    
    // Apply search filter if exists
    if (searchTerm) {
        baseData = baseData.filter(karyawan => {
            const nama = karyawan.nama_lengkap.toLowerCase();
            const nik = karyawan.nik_karyawan.toLowerCase();
            const jabatan = karyawan.jabatan ? karyawan.jabatan.nama.toLowerCase() : '';
            
            return nama.includes(searchTerm) || 
                   nik.includes(searchTerm) || 
                   jabatan.includes(searchTerm);
        });
    }
    
    filteredKaryawans = baseData;
    renderKaryawans(baseData);
    updateSearchInfo();
    updateKaryawanCount();
    
    // Update jabatan info
    const jabatanInfo = document.getElementById('jabatan_info');
    if (jabatanId) {
        const selectedJabatan = projectJabatans.find(j => j.id == jabatanId);
        if (selectedJabatan) {
            jabatanInfo.textContent = `Filter: ${selectedJabatan.nama} (${baseData.length} karyawan)`;
        }
    } else {
        jabatanInfo.textContent = `Ditemukan ${allKaryawans.length} karyawan, ${projectJabatans.length} jabatan`;
    }
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.karyawan-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    // Store current selections
    selectedKaryawans = [];
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
        if (cb.checked) {
            selectedKaryawans.push(parseInt(cb.value));
        }
    });
    
    updateKaryawanCount();
}

function updateKaryawanCount() {
    const checkboxes = document.querySelectorAll('.karyawan-checkbox:checked');
    const count = checkboxes.length;
    const countEl = document.getElementById('karyawan_count');
    const selectAllText = document.getElementById('select_all_text');
    
    // Update selected karyawans array
    selectedKaryawans = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    if (count === 0) {
        countEl.textContent = 'Semua karyawan di project';
    } else {
        countEl.textContent = `${count} karyawan dipilih`;
    }
    
    const allCheckboxes = document.querySelectorAll('.karyawan-checkbox');
    const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
    selectAllText.textContent = allChecked ? 'Batal Pilih Semua' : 'Pilih Semua';
    
    // Update select all button text based on current view
    const visibleCount = filteredKaryawans.length;
    if (currentSearchTerm || document.getElementById('jabatan_id').value) {
        selectAllText.textContent = allChecked ? 
            `Batal Pilih (${visibleCount})` : 
            `Pilih Semua (${visibleCount})`;
    }
}

// Form submit confirmation
document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const projectId = document.getElementById('project_id').value;
    const projectName = document.getElementById('project_id').options[document.getElementById('project_id').selectedIndex].text;
    const checkboxes = document.querySelectorAll('.karyawan-checkbox:checked');
    const count = checkboxes.length;
    
    // Get periode info
    let periodeText = '';
    const periodeInput = document.getElementById('periode');
    if (periodeInput) {
        periodeText = periodeInput.value;
    } else {
        const periodeStart = document.getElementById('periode_start').value;
        const periodeEnd = document.getElementById('periode_end').value;
        periodeText = formatDate(periodeStart) + ' - ' + formatDate(periodeEnd);
    }
    
    let message = `Generate payroll untuk periode <strong>${periodeText}</strong> di project <strong>${projectName}</strong>?`;
    
    if (count === 0) {
        message += '<br><br>Semua karyawan aktif akan diproses.';
    } else {
        message += `<br><br>${count} karyawan akan diproses.`;
    }
    
    Swal.fire({
        title: 'Konfirmasi Generate Payroll',
        html: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82C8',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Generate!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang generate payroll, mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            e.target.submit();
        }
    });
});

function updatePeriodeLabel() {
    const startDate = document.getElementById('periode_start').value;
    const endDate = document.getElementById('periode_end').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        // Calculate difference in days
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end date
        
        // Validate max 31 days
        if (diffDays > 31) {
            Swal.fire({
                icon: 'error',
                title: 'Periode Terlalu Panjang',
                text: `Periode maksimal 31 hari. Periode yang dipilih: ${diffDays} hari`,
                confirmButtonColor: '#EF4444'
            });
            
            // Reset end date
            document.getElementById('periode_end').value = '';
            document.getElementById('periode_display').textContent = formatDate(startDate) + ' - (pilih tanggal akhir)';
            return;
        }
        
        const startFormatted = formatDate(startDate);
        const endFormatted = formatDate(endDate);
        document.getElementById('periode_display').textContent = `${startFormatted} - ${endFormatted} (${diffDays} hari)`;
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
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

// Debounced search
let searchTimeout;
document.getElementById('karyawan_search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchKaryawan();
    }, 300);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search (when karyawan list is visible)
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        const searchContainer = document.getElementById('karyawan_search_container');
        if (searchContainer.style.display !== 'none') {
            e.preventDefault();
            document.getElementById('karyawan_search').focus();
        }
    }
});

// Focus search hint
document.getElementById('karyawan_search').setAttribute('title', 'Tekan Ctrl+F untuk fokus pencarian');
</script>
@endpush
