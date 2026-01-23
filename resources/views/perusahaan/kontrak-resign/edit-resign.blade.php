@extends('perusahaan.layouts.app')

@section('title', 'Edit Pengajuan Resign')
@section('page-title', 'Edit Pengajuan Resign')
@section('page-subtitle', 'Edit pengajuan resign karyawan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Pengajuan Resign</h3>
                    <p class="text-sm text-gray-600 mt-1">Edit form pengajuan resign untuk {{ $resign->karyawan->nama_lengkap }}</p>
                </div>
                <a href="{{ route('perusahaan.kontrak-resign.index', ['tab' => 'resign']) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('perusahaan.kontrak-resign.update-resign', $resign->hash_id) }}" method="POST" id="resignForm">
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
                            <option value="{{ $project->id }}" {{ old('project_id', $resign->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Karyawan Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Karyawan <span class="text-red-500">*</span>
                    </label>
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-blue-900">{{ $resign->karyawan->nama_lengkap }}</p>
                                <p class="text-sm text-blue-600">{{ $resign->karyawan->nik_karyawan }}</p>
                                <p class="text-sm text-blue-600">
                                    @if($resign->karyawan->tanggal_keluar)
                                        Keluar: {{ $resign->karyawan->tanggal_keluar->format('d M Y') }}
                                    @else
                                        Karyawan aktif
                                    @endif
                                </p>
                            </div>
                            <button type="button" onclick="changeKaryawan()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>Ganti Karyawan
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ old('karyawan_id', $resign->karyawan_id) }}">
                    @error('karyawan_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Karyawan Selection (Hidden by default) -->
                <div id="karyawan_selection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Karyawan Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="karyawan_search"
                            placeholder="Ketik nama karyawan untuk mencari..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autocomplete="off"
                        >
                        
                        <!-- Dropdown Results -->
                        <div id="karyawan_dropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <button type="button" onclick="cancelChangeKaryawan()" class="px-3 py-1 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_pengajuan" 
                            id="tanggal_pengajuan"
                            value="{{ old('tanggal_pengajuan', $resign->tanggal_pengajuan->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="calculateNoticePeriod()"
                        >
                        @error('tanggal_pengajuan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Resign Efektif <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_resign_efektif" 
                            id="tanggal_resign_efektif"
                            value="{{ old('tanggal_resign_efektif', $resign->tanggal_resign_efektif->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="calculateNoticePeriod()"
                        >
                        @error('tanggal_resign_efektif')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notice Period Display -->
                <div id="notice_period_display">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Masa Pemberitahuan:</span>
                            <span class="text-lg font-bold text-blue-600" id="notice_period_text">{{ $resign->calculateNoticePeriod() }} hari</span>
                        </div>
                    </div>
                </div>

                <!-- Jenis Resign -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Resign <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="jenis_resign" 
                        id="jenis_resign"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Pilih Jenis Resign</option>
                        <option value="resign_pribadi" {{ old('jenis_resign', $resign->jenis_resign) == 'resign_pribadi' ? 'selected' : '' }}>Resign Pribadi</option>
                        <option value="kontrak_habis" {{ old('jenis_resign', $resign->jenis_resign) == 'kontrak_habis' ? 'selected' : '' }}>Kontrak Habis</option>
                        <option value="phk" {{ old('jenis_resign', $resign->jenis_resign) == 'phk' ? 'selected' : '' }}>PHK</option>
                        <option value="pensiun" {{ old('jenis_resign', $resign->jenis_resign) == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                        <option value="meninggal_dunia" {{ old('jenis_resign', $resign->jenis_resign) == 'meninggal_dunia' ? 'selected' : '' }}>Meninggal Dunia</option>
                        <option value="lainnya" {{ old('jenis_resign', $resign->jenis_resign) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_resign')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alasan Resign -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Resign <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="alasan_resign" 
                        rows="4"
                        required
                        maxlength="2000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Jelaskan alasan resign secara detail..."
                    >{{ old('alasan_resign', $resign->alasan_resign) }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('alasan_resign')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @else
                            <p class="text-gray-500 text-sm">Maksimal 2000 karakter</p>
                        @enderror
                        <p class="text-gray-500 text-sm" id="char_count_alasan">0/2000</p>
                    </div>
                </div>

                <!-- Handover Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Serah Terima (Opsional)
                    </label>
                    <textarea 
                        name="handover_notes" 
                        rows="3"
                        maxlength="2000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Catatan serah terima tugas, dokumen, atau aset..."
                    >{{ old('handover_notes', $resign->handover_notes) }}</textarea>
                    <div class="flex justify-between mt-1">
                        <p class="text-gray-500 text-sm">Maksimal 2000 karakter</p>
                        <p class="text-gray-500 text-sm" id="char_count_handover">0/2000</p>
                    </div>
                </div>

                <!-- Handover Items -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Item Serah Terima (Opsional)
                    </label>
                    <div id="handover_items_container">
                        @if(old('handover_items', $resign->handover_items))
                            @foreach(old('handover_items', $resign->handover_items) as $item)
                                <div class="handover-item flex items-center space-x-2 mb-2">
                                    <input 
                                        type="text" 
                                        name="handover_items[]" 
                                        value="{{ $item }}"
                                        placeholder="Contoh: Laptop Dell Latitude 5520"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                    >
                                    <button type="button" onclick="removeHandoverItem(this)" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="handover-item flex items-center space-x-2 mb-2">
                                <input 
                                    type="text" 
                                    name="handover_items[]" 
                                    placeholder="Contoh: Laptop Dell Latitude 5520"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                >
                                <button type="button" onclick="removeHandoverItem(this)" class="text-red-600 hover:text-red-800 p-2">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addHandoverItem()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i>Tambah Item
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('perusahaan.kontrak-resign.index', ['tab' => 'resign']) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i>Update Pengajuan Resign
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let karyawanData = [];
let originalKaryawanId = {{ $resign->karyawan_id }};

// Load karyawans when project is selected
async function loadKaryawans() {
    const projectId = document.getElementById('project_select').value;
    
    if (!projectId) {
        karyawanData = [];
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/kontrak-resign-karyawans/${projectId}`);
        karyawanData = await response.json();
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

// Show karyawan selection
function changeKaryawan() {
    document.getElementById('karyawan_selection').classList.remove('hidden');
    loadKaryawans();
}

// Cancel karyawan change
function cancelChangeKaryawan() {
    document.getElementById('karyawan_selection').classList.add('hidden');
    document.getElementById('karyawan_id').value = originalKaryawanId;
    document.getElementById('karyawan_search').value = '';
    document.getElementById('karyawan_dropdown').classList.add('hidden');
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
        dropdown.innerHTML = filtered.map(k => {
            const contractInfo = k.tanggal_keluar ? 
                `<div class="text-xs text-gray-400">Keluar: ${new Date(k.tanggal_keluar).toLocaleDateString('id-ID')}</div>` : 
                '<div class="text-xs text-gray-400">Karyawan aktif</div>';
            
            return `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" onclick="selectKaryawan(${k.id}, '${k.nama_lengkap}', '${k.nik_karyawan}')">
                    <div class="font-medium text-gray-900">${k.nama_lengkap}</div>
                    <div class="text-sm text-gray-500">${k.nik_karyawan}</div>
                    ${contractInfo}
                </div>
            `;
        }).join('');
    }
    
    dropdown.classList.remove('hidden');
}

// Select karyawan
function selectKaryawan(id, nama, nik) {
    document.getElementById('karyawan_id').value = id;
    document.getElementById('karyawan_search').value = nama;
    document.getElementById('karyawan_dropdown').classList.add('hidden');
    
    Swal.fire({
        icon: 'success',
        title: 'Karyawan Dipilih',
        text: `${nama} telah dipilih`,
        timer: 1500,
        showConfirmButton: false
    });
}

// Calculate notice period
function calculateNoticePeriod() {
    const tanggalPengajuan = document.getElementById('tanggal_pengajuan').value;
    const tanggalResign = document.getElementById('tanggal_resign_efektif').value;
    
    if (tanggalPengajuan && tanggalResign) {
        const startDate = new Date(tanggalPengajuan);
        const endDate = new Date(tanggalResign);
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        document.getElementById('notice_period_text').textContent = `${diffDays} hari`;
        
        // Update min date for tanggal_resign_efektif
        const minDate = new Date(tanggalPengajuan);
        minDate.setDate(minDate.getDate() + 1);
        document.getElementById('tanggal_resign_efektif').min = minDate.toISOString().split('T')[0];
    }
}

// Add handover item
function addHandoverItem() {
    const container = document.getElementById('handover_items_container');
    const newItem = document.createElement('div');
    newItem.className = 'handover-item flex items-center space-x-2 mb-2';
    newItem.innerHTML = `
        <input 
            type="text" 
            name="handover_items[]" 
            placeholder="Contoh: ID Card, Seragam, dll"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        >
        <button type="button" onclick="removeHandoverItem(this)" class="text-red-600 hover:text-red-800 p-2">
            <i class="fas fa-trash text-sm"></i>
        </button>
    `;
    container.appendChild(newItem);
}

// Remove handover item
function removeHandoverItem(button) {
    const container = document.getElementById('handover_items_container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

// Character count for textareas
function updateCharCount(textarea, counterId) {
    const charCount = document.getElementById(counterId);
    const maxLength = textarea.getAttribute('maxlength');
    charCount.textContent = `${textarea.value.length}/${maxLength}`;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Karyawan search
    document.getElementById('karyawan_search').addEventListener('input', searchKaryawan);
    
    // Character count
    document.querySelector('textarea[name="alasan_resign"]').addEventListener('input', function() {
        updateCharCount(this, 'char_count_alasan');
    });
    
    document.querySelector('textarea[name="handover_notes"]').addEventListener('input', function() {
        updateCharCount(this, 'char_count_handover');
    });
    
    // Form validation
    document.getElementById('resignForm').addEventListener('submit', function(e) {
        const tanggalPengajuan = document.getElementById('tanggal_pengajuan').value;
        const tanggalResign = document.getElementById('tanggal_resign_efektif').value;
        
        if (tanggalPengajuan && tanggalResign && new Date(tanggalResign) <= new Date(tanggalPengajuan)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal Tidak Valid!',
                text: 'Tanggal resign efektif harus setelah tanggal pengajuan',
                confirmButtonText: 'OK'
            }).then(() => {
                document.getElementById('tanggal_resign_efektif').focus();
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
    
    // Initialize character counts
    updateCharCount(document.querySelector('textarea[name="alasan_resign"]'), 'char_count_alasan');
    updateCharCount(document.querySelector('textarea[name="handover_notes"]'), 'char_count_handover');
    
    // Load karyawans for current project
    loadKaryawans();
});
</script>
@endpush