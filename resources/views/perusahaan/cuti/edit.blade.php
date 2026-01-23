@extends('perusahaan.layouts.app')

@section('title', 'Edit Permintaan Cuti')
@section('page-title', 'Edit Permintaan Cuti')
@section('page-subtitle', 'Edit permintaan cuti karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Permintaan Cuti</h3>
                    <p class="text-sm text-gray-600 mt-1">Edit form permintaan cuti karyawan</p>
                </div>
                <a href="{{ route('perusahaan.cuti.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('perusahaan.cuti.update', $cuti->hash_id) }}" method="POST" id="cutiForm">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <!-- Project Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="project_id" 
                        id="project_select"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        onchange="loadKaryawans()"
                    >
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ (old('project_id', $cuti->project_id) == $project->id) ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Karyawan Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Karyawan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="karyawan_search"
                            placeholder="Ketik nama karyawan untuk mencari..."
                            value="{{ $cuti->karyawan->nama_lengkap }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autocomplete="off"
                        >
                        <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ old('karyawan_id', $cuti->karyawan_id) }}">
                        
                        <!-- Dropdown Results -->
                        <div id="karyawan_dropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                        
                        <!-- Selected Karyawan Display -->
                        <div id="selected_karyawan" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-blue-900" id="selected_karyawan_name">{{ $cuti->karyawan->nama_lengkap }}</p>
                                    <p class="text-sm text-blue-600" id="selected_karyawan_nik">{{ $cuti->karyawan->nik_karyawan }}</p>
                                </div>
                                <button type="button" onclick="clearKaryawan()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('karyawan_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Cuti -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            id="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $cuti->tanggal_mulai->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="calculateTotalHari()"
                        >
                        @error('tanggal_mulai')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_selesai" 
                            id="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $cuti->tanggal_selesai->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="calculateTotalHari()"
                        >
                        @error('tanggal_selesai')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Total Hari Display -->
                <div id="total_hari_display">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Hari Cuti:</span>
                            <span class="text-lg font-bold text-blue-600" id="total_hari_text">{{ $cuti->total_hari }} hari</span>
                        </div>
                    </div>
                </div>

                <!-- Jenis Cuti -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Cuti <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="jenis_cuti" 
                        id="jenis_cuti"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        onchange="checkBatasCuti()"
                    >
                        <option value="">Pilih Jenis Cuti</option>
                        <option value="tahunan" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="sakit" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="melahirkan" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'melahirkan' ? 'selected' : '' }}>Melahirkan</option>
                        <option value="menikah" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="khitan" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'khitan' ? 'selected' : '' }}>Khitan</option>
                        <option value="baptis" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'baptis' ? 'selected' : '' }}>Baptis</option>
                        <option value="keluarga_meninggal" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'keluarga_meninggal' ? 'selected' : '' }}>Keluarga Meninggal</option>
                        <option value="lainnya" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_cuti')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Cuti Warning -->
                <div id="batas_cuti_warning" class="hidden">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Perhatian Batas Cuti Tahunan</h4>
                                <p class="text-sm text-yellow-700 mt-1" id="batas_cuti_text"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alasan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Cuti <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="alasan" 
                        rows="4"
                        required
                        maxlength="1000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Jelaskan alasan mengambil cuti..."
                    >{{ old('alasan', $cuti->alasan) }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('alasan')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @else
                            <p class="text-gray-500 text-sm">Maksimal 1000 karakter</p>
                        @enderror
                        <p class="text-gray-500 text-sm" id="char_count">0/1000</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('perusahaan.cuti.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i>Update Permintaan Cuti
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let karyawanData = [];
let selectedKaryawan = {
    id: {{ $cuti->karyawan_id }},
    nama: '{{ $cuti->karyawan->nama_lengkap }}',
    nik: '{{ $cuti->karyawan->nik_karyawan }}'
};

// Load karyawans when project is selected
async function loadKaryawans() {
    const projectId = document.getElementById('project_select').value;
    const dropdown = document.getElementById('karyawan_dropdown');
    
    if (!projectId) {
        karyawanData = [];
        dropdown.classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/cuti-karyawans/${projectId}`);
        karyawanData = await response.json();
        
        if (karyawanData.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Tidak ada karyawan aktif di project ini',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error loading karyawans:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal memuat data karyawan',
            confirmButtonText: 'OK'
        });
    }
}

// Search karyawan
function searchKaryawan() {
    const searchTerm = document.getElementById('karyawan_search').value.toLowerCase();
    const dropdown = document.getElementById('karyawan_dropdown');
    
    if (!searchTerm || karyawanData.length === 0) {
        dropdown.classList.add('hidden');
        return;
    }
    
    const filtered = karyawanData.filter(k => 
        k.nama_lengkap.toLowerCase().includes(searchTerm) ||
        k.nik_karyawan.toLowerCase().includes(searchTerm)
    );
    
    if (filtered.length === 0) {
        dropdown.innerHTML = '<div class="p-3 text-gray-500 text-sm">Tidak ada karyawan ditemukan</div>';
    } else {
        dropdown.innerHTML = filtered.map(k => `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="selectKaryawan(${k.id}, '${k.nama_lengkap}', '${k.nik_karyawan}')">
                <div class="font-medium text-gray-900">${k.nama_lengkap}</div>
                <div class="text-sm text-gray-500">${k.nik_karyawan}</div>
            </div>
        `).join('');
    }
    
    dropdown.classList.remove('hidden');
}

// Select karyawan
function selectKaryawan(id, nama, nik) {
    selectedKaryawan = { id, nama, nik };
    
    document.getElementById('karyawan_id').value = id;
    document.getElementById('karyawan_search').value = nama;
    document.getElementById('selected_karyawan_name').textContent = nama;
    document.getElementById('selected_karyawan_nik').textContent = nik;
    
    document.getElementById('karyawan_dropdown').classList.add('hidden');
    document.getElementById('selected_karyawan').classList.remove('hidden');
    
    checkBatasCuti();
}

// Clear karyawan selection
function clearKaryawan() {
    selectedKaryawan = null;
    document.getElementById('karyawan_id').value = '';
    document.getElementById('karyawan_search').value = '';
    document.getElementById('selected_karyawan').classList.add('hidden');
    document.getElementById('karyawan_dropdown').classList.add('hidden');
    document.getElementById('batas_cuti_warning').classList.add('hidden');
}

// Calculate total hari
function calculateTotalHari() {
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    
    if (tanggalMulai && tanggalSelesai) {
        const startDate = new Date(tanggalMulai);
        const endDate = new Date(tanggalSelesai);
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        document.getElementById('total_hari_text').textContent = `${diffDays} hari`;
        document.getElementById('total_hari_display').classList.remove('hidden');
        
        document.getElementById('tanggal_selesai').min = tanggalMulai;
        
        checkBatasCuti();
    } else {
        document.getElementById('total_hari_display').classList.add('hidden');
    }
}

// Check batas cuti tahunan
async function checkBatasCuti() {
    const jenisCuti = document.getElementById('jenis_cuti').value;
    const projectId = document.getElementById('project_select').value;
    const karyawanId = document.getElementById('karyawan_id').value;
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    
    const warningDiv = document.getElementById('batas_cuti_warning');
    
    if (jenisCuti !== 'tahunan' || !projectId || !karyawanId || !tanggalMulai || !tanggalSelesai) {
        warningDiv.classList.add('hidden');
        return;
    }
    
    try {
        const startDate = new Date(tanggalMulai);
        const endDate = new Date(tanggalSelesai);
        const totalHari = Math.ceil(Math.abs(endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        const warningText = `Permintaan cuti tahunan sebanyak ${totalHari} hari. Pastikan tidak melebihi batas cuti tahunan yang ditetapkan.`;
        
        document.getElementById('batas_cuti_text').textContent = warningText;
        warningDiv.classList.remove('hidden');
        
    } catch (error) {
        console.error('Error checking batas cuti:', error);
    }
}

// Character count for alasan
function updateCharCount() {
    const textarea = document.querySelector('textarea[name="alasan"]');
    const charCount = document.getElementById('char_count');
    charCount.textContent = `${textarea.value.length}/1000`;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Load karyawans for current project
    loadKaryawans();
    
    // Karyawan search
    document.getElementById('karyawan_search').addEventListener('input', searchKaryawan);
    
    // Character count
    document.querySelector('textarea[name="alasan"]').addEventListener('input', updateCharCount);
    
    // Form validation
    document.getElementById('cutiForm').addEventListener('submit', function(e) {
        const karyawanId = document.getElementById('karyawan_id').value;
        const tanggalMulai = document.getElementById('tanggal_mulai').value;
        const tanggalSelesai = document.getElementById('tanggal_selesai').value;
        
        if (!karyawanId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Silakan pilih karyawan terlebih dahulu',
                confirmButtonText: 'OK'
            }).then(() => {
                document.getElementById('karyawan_search').focus();
            });
            return;
        }
        
        if (tanggalMulai && tanggalSelesai && new Date(tanggalSelesai) < new Date(tanggalMulai)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal Tidak Valid!',
                text: 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
                confirmButtonText: 'OK'
            }).then(() => {
                document.getElementById('tanggal_selesai').focus();
            });
            return;
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#karyawan_search') && !e.target.closest('#karyawan_dropdown')) {
            document.getElementById('karyawan_dropdown').classList.add('hidden');
        }
    });
    
    // Initialize
    updateCharCount();
    calculateTotalHari();
    checkBatasCuti();
});
</script>
@endpush