@extends('perusahaan.layouts.app')

@section('title', 'Edit Reimbursement - ' . $reimbursement->nomor_reimbursement)
@section('page-title', 'Edit Reimbursement')
@section('page-subtitle', $reimbursement->nomor_reimbursement . ' - ' . $reimbursement->judul_pengajuan)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center">
        <a href="{{ route('perusahaan.keuangan.reimbursement.show', $reimbursement->hash_id) }}" 
           class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

        <form action="{{ route('perusahaan.keuangan.reimbursement.update', $reimbursement->hash_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Informasi Dasar</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-project-diagram text-blue-500 mr-2"></i>
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select name="project_id" id="project_id" required
                                class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('project_id') border-red-500 @enderror">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $reimbursement->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Karyawan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-user text-green-500 mr-2"></i>
                            Karyawan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="karyawan_search" placeholder="Cari nama karyawan..." autocomplete="off"
                                   class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('karyawan_id') border-red-500 @enderror">
                            <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ old('karyawan_id', $reimbursement->karyawan_id) }}" required>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <!-- Dropdown Results -->
                            <div id="karyawan_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                <!-- Results will be populated here -->
                            </div>
                        </div>
                        @error('karyawan_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Judul Pengajuan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-heading text-purple-500 mr-2"></i>
                            Judul Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="judul_pengajuan" value="{{ old('judul_pengajuan', $reimbursement->judul_pengajuan) }}" required
                               placeholder="Contoh: Reimbursement transportasi ke kantor pusat"
                               class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('judul_pengajuan') border-red-500 @enderror">
                        @error('judul_pengajuan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-align-left text-indigo-500 mr-2"></i>
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="deskripsi" rows="4" required
                                  placeholder="Jelaskan detail pengajuan reimbursement Anda..."
                                  class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $reimbursement->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Detail Keuangan</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jumlah Pengajuan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-calculator text-green-500 mr-2"></i>
                            Jumlah Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="text" id="jumlah_display" placeholder="0"
                                   class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('jumlah_pengajuan') border-red-500 @enderror">
                            <input type="hidden" name="jumlah_pengajuan" id="jumlah_pengajuan" value="{{ old('jumlah_pengajuan', $reimbursement->jumlah_pengajuan) }}">
                        </div>
                        @error('jumlah_pengajuan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-tags text-orange-500 mr-2"></i>
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="kategori_search" placeholder="Cari atau ketik kategori baru..." autocomplete="off"
                                   class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('kategori') border-red-500 @enderror">
                            <input type="hidden" name="kategori" id="kategori_value" value="{{ old('kategori', $reimbursement->kategori) }}" required>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <!-- Dropdown Results -->
                            <div id="kategori_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                <!-- Results will be populated here -->
                            </div>
                        </div>
                        @error('kategori')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pengajuan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>
                            Tanggal Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', $reimbursement->tanggal_pengajuan->format('Y-m-d')) }}" required
                               class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('tanggal_pengajuan') border-red-500 @enderror">
                        @error('tanggal_pengajuan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Kejadian -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-calendar-day text-red-500 mr-2"></i>
                            Tanggal Kejadian <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $reimbursement->tanggal_kejadian->format('Y-m-d')) }}" required
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('tanggal_kejadian') border-red-500 @enderror">
                        @error('tanggal_kejadian')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prioritas -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-flag text-yellow-500 mr-2"></i>
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="prioritas" required
                                class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('prioritas') border-red-500 @enderror">
                            @foreach(\App\Models\Reimbursement::getAvailablePrioritas() as $key => $label)
                                <option value="{{ $key }}" {{ old('prioritas', $reimbursement->prioritas) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('prioritas')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Urgent -->
                    <div class="flex items-center">
                        <div class="flex items-center h-full">
                            <input type="checkbox" name="is_urgent" id="is_urgent" value="1" {{ old('is_urgent', $reimbursement->is_urgent) ? 'checked' : '' }}
                                   class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="is_urgent" class="ml-3 text-sm font-semibold text-gray-700">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                Tandai sebagai Urgent
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-paperclip text-purple-600"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Bukti Dokumen</h2>
                </div>

                <div class="space-y-6">
                    <!-- Existing Files -->
                    @if($reimbursement->bukti_dokumen && count($reimbursement->bukti_dokumen) > 0)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">File yang Ada</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="existing-files">
                                @foreach($reimbursement->bukti_dokumen as $index => $dokumen)
                                    <div class="border border-gray-200 rounded-lg p-4" id="file-{{ $index }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                    @if(in_array(pathinfo($dokumen['filename'], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                        <i class="fas fa-image text-blue-600"></i>
                                                    @elseif(pathinfo($dokumen['filename'], PATHINFO_EXTENSION) === 'pdf')
                                                        <i class="fas fa-file-pdf text-red-600"></i>
                                                    @else
                                                        <i class="fas fa-file text-gray-600"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $dokumen['filename'] }}</p>
                                                    <p class="text-sm text-gray-500">{{ number_format($dokumen['size'] / 1024, 1) }} KB</p>
                                                </div>
                                            </div>
                                            <button type="button" onclick="removeExistingFile({{ $index }})" 
                                                    class="text-red-600 hover:text-red-800 transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Upload New Files -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-upload text-purple-500 mr-2"></i>
                            Upload File Baru
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                            <input type="file" name="bukti_dokumen[]" id="bukti_dokumen" multiple
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                   class="hidden">
                            <label for="bukti_dokumen" class="cursor-pointer">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-700 mb-2">Klik untuk upload file</p>
                                <p class="text-sm text-gray-500">atau drag & drop file di sini</p>
                                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, PDF, DOC, DOCX (Max: 5MB per file)</p>
                            </label>
                        </div>
                        <div id="file-list" class="mt-4 space-y-2"></div>
                        @error('bukti_dokumen.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan Pengaju -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>
                            Catatan Tambahan
                        </label>
                        <textarea name="catatan_pengaju" rows="3"
                                  placeholder="Catatan atau informasi tambahan yang perlu diketahui..."
                                  class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('catatan_pengaju') border-red-500 @enderror">{{ old('catatan_pengaju', $reimbursement->catatan_pengaju) }}</textarea>
                        @error('catatan_pengaju')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda dapat menyimpan perubahan atau langsung submit untuk review
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                        @if($reimbursement->status === 'draft')
                            <button type="submit" name="submit" value="1"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit untuk Review
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hidden input for files to remove -->
            <input type="hidden" name="remove_files" id="remove_files" value="">
        </form>
    </div>
</div>

@push('scripts')
<script>
// Karyawan data for search
const karyawanData = {!! json_encode($karyawans->map(function($k) {
    return [
        'id' => $k->id,
        'nama' => $k->nama_lengkap,
        'project_id' => $k->project_id,
        'project_nama' => $k->project->nama ?? 'No Project',
        'nik' => $k->nik_karyawan ?? ''
    ];
})) !!};

// Kategori data for search
const kategoriData = {!! json_encode(\App\Models\Reimbursement::getAvailableKategori()) !!};
let availableKategori = Object.keys(kategoriData).map(key => ({
    key: key,
    label: kategoriData[key]
}));

let filteredKaryawan = karyawanData;
let filteredKategori = availableKategori;

// Current selected karyawan
const currentKaryawanId = {{ old('karyawan_id', $reimbursement->karyawan_id) }};
const currentKaryawan = karyawanData.find(k => k.id == currentKaryawanId);

// Current selected kategori
const currentKategori = '{{ old('kategori', $reimbursement->kategori) }}';

// Format number with thousand separator
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Remove thousand separator
function unformatNumber(str) {
    return str.replace(/\./g, '');
}

// Handle amount input formatting
document.getElementById('jumlah_display').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
    if (value) {
        e.target.value = formatNumber(value);
        document.getElementById('jumlah_pengajuan').value = value;
    } else {
        e.target.value = '';
        document.getElementById('jumlah_pengajuan').value = '';
    }
});

// Set initial values
document.addEventListener('DOMContentLoaded', function() {
    const initialValue = document.getElementById('jumlah_pengajuan').value;
    if (initialValue) {
        document.getElementById('jumlah_display').value = formatNumber(initialValue);
    }
    
    // Set current karyawan in search field
    if (currentKaryawan) {
        document.getElementById('karyawan_search').value = `${currentKaryawan.nama} - ${currentKaryawan.project_nama}`;
    }
    
    // Set current kategori in search field
    if (currentKategori && kategoriData[currentKategori]) {
        document.getElementById('kategori_search').value = kategoriData[currentKategori];
    }
});

// Karyawan search functionality
const karyawanSearch = document.getElementById('karyawan_search');
const karyawanDropdown = document.getElementById('karyawan_dropdown');
const karyawanIdInput = document.getElementById('karyawan_id');

function filterKaryawan() {
    const projectId = document.getElementById('project_id').value;
    const searchTerm = karyawanSearch.value.toLowerCase();
    
    filteredKaryawan = karyawanData.filter(karyawan => {
        const matchesProject = !projectId || karyawan.project_id == projectId;
        const matchesSearch = !searchTerm || 
            karyawan.nama.toLowerCase().includes(searchTerm) ||
            karyawan.nik.toLowerCase().includes(searchTerm) ||
            karyawan.project_nama.toLowerCase().includes(searchTerm);
        
        return matchesProject && matchesSearch;
    });
    
    renderKaryawanDropdown();
}

function renderKaryawanDropdown() {
    if (filteredKaryawan.length === 0) {
        karyawanDropdown.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm">Tidak ada karyawan ditemukan</div>';
    } else {
        karyawanDropdown.innerHTML = filteredKaryawan.map(karyawan => `
            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                 onclick="selectKaryawan(${karyawan.id}, '${karyawan.nama}', '${karyawan.project_nama}')">
                <div class="font-medium text-gray-900">${karyawan.nama}</div>
                <div class="text-sm text-gray-500">${karyawan.nik ? karyawan.nik + ' - ' : ''}${karyawan.project_nama}</div>
            </div>
        `).join('');
    }
    
    karyawanDropdown.classList.remove('hidden');
}

function selectKaryawan(id, nama, projectNama) {
    karyawanSearch.value = `${nama} - ${projectNama}`;
    karyawanIdInput.value = id;
    karyawanDropdown.classList.add('hidden');
}

// Kategori search functionality
const kategoriSearch = document.getElementById('kategori_search');
const kategoriDropdown = document.getElementById('kategori_dropdown');
const kategoriValueInput = document.getElementById('kategori_value');

function filterKategori() {
    const searchTerm = kategoriSearch.value.toLowerCase();
    
    filteredKategori = availableKategori.filter(kategori => 
        kategori.label.toLowerCase().includes(searchTerm)
    );
    
    renderKategoriDropdown();
}

function renderKategoriDropdown() {
    let html = '';
    
    if (filteredKategori.length === 0) {
        // Show option to add new category
        const searchValue = kategoriSearch.value.trim();
        if (searchValue) {
            html = `
                <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100" 
                     onclick="addNewKategori('${searchValue}')">
                    <div class="flex items-center text-blue-600">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Tambah "${searchValue}"</span>
                    </div>
                    <div class="text-sm text-gray-500">Klik untuk menambah kategori baru</div>
                </div>
            `;
        } else {
            html = '<div class="px-4 py-3 text-gray-500 text-sm">Tidak ada kategori ditemukan</div>';
        }
    } else {
        html = filteredKategori.map(kategori => `
            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                 onclick="selectKategori('${kategori.key}', '${kategori.label}')">
                <div class="font-medium text-gray-900">${kategori.label}</div>
            </div>
        `).join('');
        
        // Add option to create new category if search doesn't match exactly
        const searchValue = kategoriSearch.value.trim();
        const exactMatch = filteredKategori.some(k => k.label.toLowerCase() === searchValue.toLowerCase());
        if (searchValue && !exactMatch) {
            html += `
                <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-t border-gray-200" 
                     onclick="addNewKategori('${searchValue}')">
                    <div class="flex items-center text-blue-600">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Tambah "${searchValue}"</span>
                    </div>
                    <div class="text-sm text-gray-500">Klik untuk menambah kategori baru</div>
                </div>
            `;
        }
    }
    
    kategoriDropdown.innerHTML = html;
    kategoriDropdown.classList.remove('hidden');
}

function selectKategori(key, label) {
    kategoriSearch.value = label;
    kategoriValueInput.value = key;
    kategoriDropdown.classList.add('hidden');
}

function addNewKategori(newKategori) {
    // Create a snake_case key from the label
    const key = newKategori.toLowerCase()
        .replace(/[^a-z0-9\s]/g, '') // Remove special characters
        .replace(/\s+/g, '_') // Replace spaces with underscores
        .replace(/_+/g, '_') // Replace multiple underscores with single
        .replace(/^_|_$/g, ''); // Remove leading/trailing underscores
    
    // Add to available categories
    availableKategori.push({
        key: key,
        label: newKategori
    });
    
    // Select the new category
    selectKategori(key, newKategori);
    
    // Show success message
    const successMsg = document.createElement('div');
    successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    successMsg.innerHTML = `<i class="fas fa-check mr-2"></i>Kategori "${newKategori}" berhasil ditambahkan`;
    document.body.appendChild(successMsg);
    
    setTimeout(() => {
        successMsg.remove();
    }, 3000);
}

// Event listeners for karyawan
karyawanSearch.addEventListener('input', function() {
    if (this.value.length > 0) {
        filterKaryawan();
    } else {
        karyawanDropdown.classList.add('hidden');
        karyawanIdInput.value = '';
    }
});

karyawanSearch.addEventListener('focus', function() {
    if (this.value.length > 0) {
        filterKaryawan();
    }
});

// Event listeners for kategori
kategoriSearch.addEventListener('input', function() {
    if (this.value.length > 0) {
        filterKategori();
    } else {
        kategoriDropdown.classList.add('hidden');
        kategoriValueInput.value = '';
    }
});

kategoriSearch.addEventListener('focus', function() {
    if (this.value.length > 0) {
        filterKategori();
    }
});

// Hide dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!karyawanSearch.contains(e.target) && !karyawanDropdown.contains(e.target)) {
        karyawanDropdown.classList.add('hidden');
    }
    if (!kategoriSearch.contains(e.target) && !kategoriDropdown.contains(e.target)) {
        kategoriDropdown.classList.add('hidden');
    }
});

// Filter karyawan by project
document.getElementById('project_id').addEventListener('change', function() {
    // Clear karyawan selection when project changes
    karyawanSearch.value = '';
    karyawanIdInput.value = '';
    karyawanDropdown.classList.add('hidden');
    
    // Update filtered list
    filterKaryawan();
});

// File upload handling
document.getElementById('bukti_dokumen').addEventListener('change', function(e) {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
        fileItem.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-file text-gray-400 mr-3"></i>
                <div>
                    <div class="font-medium text-gray-900">${file.name}</div>
                    <div class="text-sm text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                </div>
            </div>
            <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        `;
        fileList.appendChild(fileItem);
    });
});

function removeFile(index) {
    const input = document.getElementById('bukti_dokumen');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

// Remove existing file
let filesToRemove = [];

function removeExistingFile(index) {
    if (confirm('Yakin ingin menghapus file ini?')) {
        filesToRemove.push(index);
        document.getElementById('remove_files').value = JSON.stringify(filesToRemove);
        document.getElementById('file-' + index).style.display = 'none';
    }
}

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');
dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = e.dataTransfer.files;
    document.getElementById('bukti_dokumen').files = files;
    document.getElementById('bukti_dokumen').dispatchEvent(new Event('change'));
});
</script>
@endpush
@endsection