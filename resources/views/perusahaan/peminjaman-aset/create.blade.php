@extends('perusahaan.layouts.app')

@section('title', 'Tambah Peminjaman Aset')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('perusahaan.peminjaman-aset.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Peminjaman Aset</h1>
            <p class="text-gray-600 mt-1">Buat peminjaman aset baru</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <form action="{{ route('perusahaan.peminjaman-aset.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Aset -->
                <div>
                    <label for="aset_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Aset <span class="text-red-500">*</span>
                    </label>
                    <select name="aset_type" id="aset_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Tipe Aset</option>
                        @foreach($asetTypeOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('aset_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('aset_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Aset yang Dipinjam -->
                <div id="aset-selection">
                    <label for="aset_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Aset yang Dipinjam <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" 
                               id="aset_search_input" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Pilih tipe aset terlebih dahulu..."
                               disabled
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="aset_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                    
                    <!-- Hidden inputs for actual IDs -->
                    <input type="hidden" name="data_aset_id" id="data_aset_id" value="{{ old('data_aset_id') }}">
                    <input type="hidden" name="aset_kendaraan_id" id="aset_kendaraan_id" value="{{ old('aset_kendaraan_id') }}">
                    
                    @error('data_aset_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('aset_kendaraan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Aset Info Display -->
                    <div id="aset-info" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Kode:</span>
                                <span id="aset-kode" class="text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Kategori:</span>
                                <span id="aset-kategori" class="text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Karyawan Peminjam -->
                <div>
                    <label for="peminjam_karyawan_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Karyawan Peminjam <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" 
                               id="peminjam_karyawan_search" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Cari karyawan berdasarkan nama atau NIK..."
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="karyawan_search_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                    
                    <!-- Hidden input for actual ID -->
                    <input type="hidden" name="peminjam_karyawan_id" id="peminjam_karyawan_id" value="{{ old('peminjam_karyawan_id') }}">
                    
                    @error('peminjam_karyawan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Dipinjam -->
                <div>
                    <label for="jumlah_dipinjam" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Dipinjam <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_dipinjam" id="jumlah_dipinjam" value="{{ old('jumlah_dipinjam', 1) }}" min="1" max="100" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('jumlah_dipinjam')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Tanggal Peminjaman -->
                <div>
                    <label for="tanggal_peminjaman" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Peminjaman <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_peminjaman" id="tanggal_peminjaman" value="{{ old('tanggal_peminjaman', date('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal_peminjaman')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Rencana Kembali -->
                <div>
                    <label for="tanggal_rencana_kembali" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Rencana Kembali <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_rencana_kembali" id="tanggal_rencana_kembali" value="{{ old('tanggal_rencana_kembali') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal_rencana_kembali')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kondisi Saat Dipinjam -->
                <div>
                    <label for="kondisi_saat_dipinjam" class="block text-sm font-medium text-gray-700 mb-2">
                        Kondisi Saat Dipinjam <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi_saat_dipinjam" id="kondisi_saat_dipinjam" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($kondisiOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('kondisi_saat_dipinjam', 'baik') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('kondisi_saat_dipinjam')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keperluan -->
                <div>
                    <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keperluan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keperluan" id="keperluan" rows="3" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan keperluan peminjaman aset...">{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Peminjaman -->
                <div>
                    <label for="catatan_peminjaman" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Peminjaman
                    </label>
                    <textarea name="catatan_peminjaman" id="catatan_peminjaman" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan tambahan (opsional)...">{{ old('catatan_peminjaman') }}</textarea>
                    @error('catatan_peminjaman')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Bukti Peminjaman -->
                <div>
                    <label for="file_bukti_peminjaman" class="block text-sm font-medium text-gray-700 mb-2">
                        File Bukti Peminjaman
                    </label>
                    <input type="file" name="file_bukti_peminjaman" id="file_bukti_peminjaman" accept=".jpg,.jpeg,.png,.pdf" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Maksimal 5MB</p>
                    @error('file_bukti_peminjaman')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('perusahaan.peminjaman-aset.index') }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Simpan Peminjaman
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Asset type and search elements
    const asetTypeSelect = document.getElementById('aset_type');
    const asetSearchInput = document.getElementById('aset_search_input');
    const asetSearchResults = document.getElementById('aset_search_results');
    const dataAsetIdInput = document.getElementById('data_aset_id');
    const asetKendaraanIdInput = document.getElementById('aset_kendaraan_id');
    const asetInfo = document.getElementById('aset-info');
    const asetKode = document.getElementById('aset-kode');
    const asetKategori = document.getElementById('aset-kategori');

    // Project selection element
    const projectSelect = document.getElementById('project_id');

    // Employee search elements
    const karyawanSearchInput = document.getElementById('peminjam_karyawan_search');
    const karyawanSearchResults = document.getElementById('karyawan_search_results');
    const karyawanIdInput = document.getElementById('peminjam_karyawan_id');

    // Project change handler - reset asset search when project changes
    projectSelect.addEventListener('change', function() {
        // Reset asset selection when project changes
        asetSearchInput.value = '';
        asetSearchResults.classList.add('hidden');
        dataAsetIdInput.value = '';
        asetKendaraanIdInput.value = '';
        asetInfo.classList.add('hidden');
        
        // Update placeholder based on project selection
        if (this.value && asetTypeSelect.value) {
            asetSearchInput.disabled = false;
            asetSearchInput.placeholder = `Cari ${asetTypeSelect.value === 'data_aset' ? 'aset' : 'kendaraan'}...`;
        } else if (!this.value) {
            asetSearchInput.disabled = true;
            asetSearchInput.placeholder = 'Pilih project terlebih dahulu...';
        }
    });

    // Asset type change handler
    asetTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Reset asset selection
        asetSearchInput.value = '';
        asetSearchResults.classList.add('hidden');
        dataAsetIdInput.value = '';
        asetKendaraanIdInput.value = '';
        asetInfo.classList.add('hidden');
        
        if (selectedType && projectSelect.value) {
            asetSearchInput.disabled = false;
            asetSearchInput.placeholder = `Cari ${selectedType === 'data_aset' ? 'aset' : 'kendaraan'}...`;
        } else if (!projectSelect.value) {
            asetSearchInput.disabled = true;
            asetSearchInput.placeholder = 'Pilih project terlebih dahulu...';
        } else {
            asetSearchInput.disabled = true;
            asetSearchInput.placeholder = 'Pilih tipe aset terlebih dahulu...';
        }
    });

    // Asset search functionality
    let asetSearchTimeout;
    asetSearchInput.addEventListener('input', function() {
        clearTimeout(asetSearchTimeout);
        const searchTerm = this.value.trim();
        const asetType = asetTypeSelect.value;
        const projectId = projectSelect.value;
        
        if (!projectId) {
            asetSearchResults.innerHTML = '<div class="p-3 text-orange-600 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Pilih project terlebih dahulu</div>';
            asetSearchResults.classList.remove('hidden');
            return;
        }
        
        if (!asetType) {
            asetSearchResults.innerHTML = '<div class="p-3 text-orange-600 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Pilih tipe aset terlebih dahulu</div>';
            asetSearchResults.classList.remove('hidden');
            return;
        }
        
        if (searchTerm.length >= 2) {
            asetSearchTimeout = setTimeout(() => {
                searchAsets(asetType, searchTerm);
            }, 300);
        } else if (searchTerm.length === 0) {
            asetSearchResults.classList.add('hidden');
        } else {
            asetSearchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">Ketik minimal 2 karakter untuk mencari</div>';
            asetSearchResults.classList.remove('hidden');
        }
    });

    function searchAsets(type, search) {
        const projectId = document.getElementById('project_id').value;
        
        console.log('Searching assets:', { type, search, projectId }); // Debug log
        
        if (!projectId) {
            asetSearchResults.innerHTML = '<div class="p-3 text-orange-600 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Pilih project terlebih dahulu</div>';
            asetSearchResults.classList.remove('hidden');
            return;
        }
        
        // Show loading state
        asetSearchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</div>';
        asetSearchResults.classList.remove('hidden');
        
        const url = new URL('{{ route("perusahaan.peminjaman-aset.search-asets") }}');
        url.searchParams.append('type', type);
        url.searchParams.append('search', search);
        url.searchParams.append('project_id', projectId);

        console.log('Search URL:', url.toString()); // Debug log

        fetch(url)
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data); // Debug log
                displayAsetResults(data);
            })
            .catch(error => {
                console.error('Error searching assets:', error);
                asetSearchResults.innerHTML = '<div class="p-3 text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan saat mencari aset</div>';
            });
    }

    function displayAsetResults(results) {
        console.log('Displaying results:', results); // Debug log
        asetSearchResults.innerHTML = '';
        
        if (results.length === 0) {
            asetSearchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm"><i class="fas fa-search mr-2"></i>Tidak ada hasil ditemukan</div>';
        } else {
            results.forEach(result => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors';
                div.innerHTML = `
                    <div class="font-medium text-sm text-gray-900">${result.text}</div>
                    <div class="text-xs text-gray-500 mt-1">${result.kategori}</div>
                `;
                div.addEventListener('click', () => selectAset(result));
                asetSearchResults.appendChild(div);
            });
        }
        
        asetSearchResults.classList.remove('hidden');
    }

    function selectAset(aset) {
        const asetType = asetTypeSelect.value;
        
        asetSearchInput.value = aset.text;
        asetSearchResults.classList.add('hidden');
        
        // Set the appropriate hidden input
        if (asetType === 'data_aset') {
            dataAsetIdInput.value = aset.id;
            asetKendaraanIdInput.value = '';
        } else {
            asetKendaraanIdInput.value = aset.id;
            dataAsetIdInput.value = '';
        }
        
        // Show asset info
        asetKode.textContent = aset.kode;
        asetKategori.textContent = aset.kategori;
        asetInfo.classList.remove('hidden');
    }

    // Employee search functionality
    let karyawanSearchTimeout;
    karyawanSearchInput.addEventListener('input', function() {
        clearTimeout(karyawanSearchTimeout);
        const searchTerm = this.value.trim();
        
        if (searchTerm.length >= 2) {
            karyawanSearchTimeout = setTimeout(() => {
                searchKaryawan(searchTerm);
            }, 300);
        } else if (searchTerm.length === 0) {
            karyawanSearchResults.classList.add('hidden');
        } else {
            karyawanSearchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">Ketik minimal 2 karakter untuk mencari</div>';
            karyawanSearchResults.classList.remove('hidden');
        }
    });

    function searchKaryawan(search) {
        // Show loading state
        karyawanSearchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari karyawan...</div>';
        karyawanSearchResults.classList.remove('hidden');
        
        const url = new URL('{{ route("perusahaan.peminjaman-aset.search-karyawan") }}');
        url.searchParams.append('search', search);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                displayKaryawanResults(data);
            })
            .catch(error => {
                console.error('Error searching karyawan:', error);
                karyawanSearchResults.innerHTML = '<div class="p-3 text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan saat mencari karyawan</div>';
            });
    }

    function displayKaryawanResults(results) {
        karyawanSearchResults.innerHTML = '';
        
        if (results.length === 0) {
            karyawanSearchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm"><i class="fas fa-search mr-2"></i>Tidak ada karyawan ditemukan</div>';
        } else {
            results.forEach(result => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors';
                div.innerHTML = `
                    <div class="font-medium text-sm text-gray-900">${result.nama}</div>
                    <div class="text-xs text-gray-500 mt-1">NIK: ${result.nik}</div>
                `;
                div.addEventListener('click', () => selectKaryawan(result));
                karyawanSearchResults.appendChild(div);
            });
        }
        
        karyawanSearchResults.classList.remove('hidden');
    }

    function selectKaryawan(karyawan) {
        karyawanSearchInput.value = karyawan.text;
        karyawanSearchResults.classList.add('hidden');
        karyawanIdInput.value = karyawan.id;
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!asetSearchInput.contains(e.target) && !asetSearchResults.contains(e.target)) {
            asetSearchResults.classList.add('hidden');
        }
        if (!karyawanSearchInput.contains(e.target) && !karyawanSearchResults.contains(e.target)) {
            karyawanSearchResults.classList.add('hidden');
        }
    });

    // Set minimum date for tanggal_rencana_kembali
    const tanggalPeminjaman = document.getElementById('tanggal_peminjaman');
    const tanggalRencanaKembali = document.getElementById('tanggal_rencana_kembali');

    function updateMinReturnDate() {
        const peminjamanDate = new Date(tanggalPeminjaman.value);
        peminjamanDate.setDate(peminjamanDate.getDate() + 1);
        tanggalRencanaKembali.min = peminjamanDate.toISOString().split('T')[0];
    }

    tanggalPeminjaman.addEventListener('change', updateMinReturnDate);
    
    // Initialize
    updateMinReturnDate();
});
</script>

<style>
/* Custom styles for search dropdowns */
#aset_search_results, #karyawan_search_results {
    position: absolute;
    z-index: 1000;
    width: 100%;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    margin-top: 0.25rem;
    max-height: 15rem;
    overflow-y: auto;
}

/* Make parent containers relative for absolute positioning */
#aset-selection, .relative {
    position: relative;
}

/* Hover effects for search results */
.search-result-item:hover {
    background-color: #f9fafb;
}
</style>
@endpush