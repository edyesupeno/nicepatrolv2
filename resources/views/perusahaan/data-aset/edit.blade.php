@extends('perusahaan.layouts.app')

@section('title', 'Edit Data Aset')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.data-aset.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Data Aset
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-xl font-semibold text-gray-900">Edit Data Aset</h1>
        <p class="text-gray-600 mt-1">Kode Aset: <span class="font-medium">{{ $dataAset->kode_aset }}</span></p>
    </div>
    
    <form action="{{ route('perusahaan.data-aset.update', $dataAset->hash_id) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $dataAset->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_aset" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Aset <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_aset" id="nama_aset" value="{{ old('nama_aset', $dataAset->nama_aset) }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_aset') border-red-500 @enderror"
                           placeholder="Masukkan nama aset">
                    @error('nama_aset')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $dataAset->kategori) }}" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kategori') border-red-500 @enderror"
                               placeholder="Ketik untuk mencari atau buat kategori baru..."
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400" id="search-icon"></i>
                            <i class="fas fa-spinner fa-spin text-blue-500 hidden" id="loading-icon"></i>
                        </div>
                        
                        <!-- Dropdown suggestions -->
                        <div id="kategori-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                            <div id="kategori-suggestions" class="py-1">
                                <!-- Suggestions will be populated here -->
                            </div>
                            <div id="kategori-create-new" class="hidden border-t border-gray-200 p-3 bg-gray-50">
                                <button type="button" onclick="createNewKategori()" class="w-full text-left text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-plus mr-2"></i>Buat kategori baru: "<span id="new-kategori-name"></span>"
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Ketik untuk mencari kategori yang sudah ada atau buat kategori baru</p>
                    @error('kategori')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_beli" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Beli <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli', $dataAset->tanggal_beli->format('Y-m-d')) }}" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_beli') border-red-500 @enderror">
                        @error('tanggal_beli')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $dataAset->status) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Beli <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', $dataAset->harga_beli) }}" required min="0" step="0.01"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('harga_beli') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('harga_beli')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nilai_penyusutan" class="block text-sm font-medium text-gray-700 mb-2">
                            Nilai Penyusutan
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="nilai_penyusutan" id="nilai_penyusutan" value="{{ old('nilai_penyusutan', $dataAset->nilai_penyusutan) }}" min="0" step="0.01"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nilai_penyusutan') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Kosongkan jika belum ada penyusutan</p>
                        @error('nilai_penyusutan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <div>
                    <label for="pic_penanggung_jawab" class="block text-sm font-medium text-gray-700 mb-2">
                        PIC / Penanggung Jawab <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pic_penanggung_jawab" id="pic_penanggung_jawab" value="{{ old('pic_penanggung_jawab', $dataAset->pic_penanggung_jawab) }}" required 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pic_penanggung_jawab') border-red-500 @enderror"
                           placeholder="Nama penanggung jawab aset">
                    @error('pic_penanggung_jawab')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="foto_aset" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Aset
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <input type="file" name="foto_aset" id="foto_aset" accept="image/*" class="hidden" onchange="previewImage(this)">
                        
                        @if($dataAset->foto_aset)
                            <div id="current-image">
                                <img src="{{ $dataAset->foto_url }}" alt="Current Image" class="max-w-full h-48 object-cover rounded-lg mx-auto mb-3">
                                <p class="text-sm text-gray-600 mb-2">Foto saat ini</p>
                                <button type="button" onclick="changeImage()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Ganti Foto
                                </button>
                            </div>
                        @else
                            <div id="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-2">Klik untuk upload foto aset</p>
                                <p class="text-sm text-gray-500">Format: JPG, PNG (Max: 2MB)</p>
                                <button type="button" onclick="document.getElementById('foto_aset').click()" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Pilih File
                                </button>
                            </div>
                        @endif
                        
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-lg mx-auto mb-3">
                            <p id="file-name" class="text-sm text-gray-600 mb-2"></p>
                            <button type="button" onclick="removeImage()" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i>Hapus Foto
                            </button>
                        </div>
                    </div>
                    @error('foto_aset')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="catatan_tambahan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Tambahan
                    </label>
                    <textarea name="catatan_tambahan" id="catatan_tambahan" rows="4" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan_tambahan') border-red-500 @enderror"
                              placeholder="Catatan atau keterangan tambahan tentang aset">{{ old('catatan_tambahan', $dataAset->catatan_tambahan) }}</textarea>
                    @error('catatan_tambahan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('perusahaan.data-aset.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Update Aset
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Hide current image and upload placeholder
            const currentImage = document.getElementById('current-image');
            const uploadPlaceholder = document.getElementById('upload-placeholder');
            if (currentImage) currentImage.classList.add('hidden');
            if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
            
            // Show preview
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('file-name').textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function changeImage() {
    document.getElementById('foto_aset').click();
}

function removeImage() {
    document.getElementById('foto_aset').value = '';
    
    // Show current image or upload placeholder
    const currentImage = document.getElementById('current-image');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    
    if (currentImage) {
        currentImage.classList.remove('hidden');
    } else if (uploadPlaceholder) {
        uploadPlaceholder.classList.remove('hidden');
    }
    
    // Hide preview
    document.getElementById('image-preview').classList.add('hidden');
}

// Auto-suggest kategori dengan advanced search
let kategoriTimeout;
let selectedKategoriIndex = -1;
let availableKategori = [];

document.getElementById('kategori').addEventListener('input', function() {
    const query = this.value.trim();
    
    // Clear previous timeout
    clearTimeout(kategoriTimeout);
    
    if (query.length >= 1) {
        // Show loading
        document.getElementById('search-icon').classList.add('hidden');
        document.getElementById('loading-icon').classList.remove('hidden');
        
        // Debounce search
        kategoriTimeout = setTimeout(() => {
            searchKategori(query);
        }, 300);
    } else {
        hideKategoriDropdown();
    }
});

// Handle keyboard navigation
document.getElementById('kategori').addEventListener('keydown', function(e) {
    const dropdown = document.getElementById('kategori-dropdown');
    const suggestions = dropdown.querySelectorAll('.kategori-suggestion');
    
    if (!dropdown.classList.contains('hidden')) {
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedKategoriIndex = Math.min(selectedKategoriIndex + 1, suggestions.length - 1);
                updateKategoriSelection(suggestions);
                break;
            case 'ArrowUp':
                e.preventDefault();
                selectedKategoriIndex = Math.max(selectedKategoriIndex - 1, -1);
                updateKategoriSelection(suggestions);
                break;
            case 'Enter':
                e.preventDefault();
                if (selectedKategoriIndex >= 0 && suggestions[selectedKategoriIndex]) {
                    selectKategori(suggestions[selectedKategoriIndex].textContent);
                } else {
                    // Create new kategori if no selection
                    const query = this.value.trim();
                    if (query && !availableKategori.includes(query)) {
                        createNewKategori();
                    }
                }
                break;
            case 'Escape':
                hideKategoriDropdown();
                break;
        }
    }
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#kategori') && !e.target.closest('#kategori-dropdown')) {
        hideKategoriDropdown();
    }
});

async function searchKategori(query) {
    try {
        const response = await fetch(`/perusahaan/data-aset-kategori-suggestions?q=${encodeURIComponent(query)}`);
        const suggestions = await response.json();
        
        availableKategori = suggestions;
        displayKategoriSuggestions(suggestions, query);
        
    } catch (error) {
        console.error('Error fetching kategori suggestions:', error);
    } finally {
        // Hide loading
        document.getElementById('loading-icon').classList.add('hidden');
        document.getElementById('search-icon').classList.remove('hidden');
    }
}

function displayKategoriSuggestions(suggestions, query) {
    const dropdown = document.getElementById('kategori-dropdown');
    const suggestionsContainer = document.getElementById('kategori-suggestions');
    const createNewContainer = document.getElementById('kategori-create-new');
    const newKategoriName = document.getElementById('new-kategori-name');
    
    // Clear previous suggestions
    suggestionsContainer.innerHTML = '';
    selectedKategoriIndex = -1;
    
    if (suggestions.length > 0) {
        suggestions.forEach((kategori, index) => {
            const item = document.createElement('div');
            item.className = 'kategori-suggestion px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm';
            item.textContent = kategori;
            item.onclick = () => selectKategori(kategori);
            suggestionsContainer.appendChild(item);
        });
    }
    
    // Show "create new" option if query doesn't match any existing kategori
    const exactMatch = suggestions.some(k => k.toLowerCase() === query.toLowerCase());
    if (query && !exactMatch) {
        newKategoriName.textContent = query;
        createNewContainer.classList.remove('hidden');
    } else {
        createNewContainer.classList.add('hidden');
    }
    
    // Show dropdown if there are suggestions or create new option
    if (suggestions.length > 0 || !exactMatch) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

function updateKategoriSelection(suggestions) {
    suggestions.forEach((item, index) => {
        if (index === selectedKategoriIndex) {
            item.classList.add('bg-blue-100');
        } else {
            item.classList.remove('bg-blue-100');
        }
    });
}

function selectKategori(kategori) {
    document.getElementById('kategori').value = kategori;
    hideKategoriDropdown();
}

function hideKategoriDropdown() {
    document.getElementById('kategori-dropdown').classList.add('hidden');
    selectedKategoriIndex = -1;
}

async function createNewKategori() {
    const kategoriInput = document.getElementById('kategori');
    const newKategori = kategoriInput.value.trim();
    
    if (!newKategori) {
        return;
    }
    
    try {
        const response = await fetch('/perusahaan/data-aset-create-kategori', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kategori: newKategori })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Set the value and hide dropdown
            kategoriInput.value = newKategori;
            hideKategoriDropdown();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Kategori Baru',
                text: `Kategori "${newKategori}" akan ditambahkan`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    } catch (error) {
        console.error('Error creating kategori:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal membuat kategori baru'
        });
    }
}
</script>
@endpush