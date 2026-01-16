@extends('perusahaan.layouts.app')

@section('title', 'Kelola Aset Checkpoint')
@section('page-title', 'Kelola Aset Checkpoint')
@section('page-subtitle', 'Pilih aset yang harus dicek di checkpoint ini')

@section('content')
<!-- Checkpoint Info -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-start gap-4">
        <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-map-marker-alt text-white text-2xl"></i>
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $checkpoint->nama }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Rute Patrol</p>
                    <p class="font-semibold text-gray-900">{{ $checkpoint->rutePatrol->nama }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Area Patrol</p>
                    <p class="font-semibold text-gray-900">{{ $checkpoint->rutePatrol->areaPatrol->nama }}</p>
                </div>
                <div>
                    <p class="text-gray-500">QR Code</p>
                    <p class="font-semibold text-gray-900">{{ $checkpoint->qr_code }}</p>
                </div>
            </div>
        </div>
        <a 
            href="{{ route('perusahaan.patrol.checkpoint') }}"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
        >
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
</div>

<form action="{{ route('perusahaan.patrol.checkpoint.aset.store', $checkpoint->hash_id) }}" method="POST">
    @csrf
    
    <!-- Selected Asets Summary -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            <div class="flex-1">
                <p class="font-semibold text-blue-900">Pilih aset yang harus dicek di checkpoint ini</p>
                <p class="text-sm text-blue-700">Aset yang dipilih akan muncul saat petugas melakukan patrol di checkpoint ini</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-600">Terpilih:</p>
                <p class="text-2xl font-bold text-blue-900" id="selected-count">{{ $checkpoint->asets->count() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: All Assets -->
        <div>
            <!-- Search Bar -->
            <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        id="search-aset"
                        placeholder="Cari aset..."
                        class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm"
                        style="focus:ring-color: #3B82C8;"
                        onkeyup="searchAset()"
                    >
                </div>
            </div>

            <!-- Aset List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-base font-bold text-gray-900">
                        <i class="fas fa-box mr-2" style="color: #3B82C8;"></i>Daftar Aset Kawasan
                    </h3>
                    <p class="text-xs text-gray-500 mt-1" id="aset-count">Menampilkan {{ $asetKawasans->count() }} aset</p>
                </div>

                <div class="p-4 max-h-[600px] overflow-y-auto">
                    @if($asetKawasans->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 mx-auto" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-box text-4xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 font-semibold mb-2">Belum ada aset kawasan</p>
                            <p class="text-gray-500 text-sm mb-4">Tambahkan aset kawasan terlebih dahulu</p>
                            <a 
                                href="{{ route('perusahaan.patrol.aset-kawasan') }}"
                                class="px-4 py-2 rounded-xl font-medium transition inline-flex items-center text-white shadow-lg hover:shadow-xl text-sm" 
                                style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
                            >
                                <i class="fas fa-plus mr-2"></i>Tambah Aset
                            </a>
                        </div>
                    @else
                        <div class="space-y-2" id="aset-list">
                            @foreach($asetKawasans as $aset)
                                @php
                                    $isSelected = $checkpoint->asets->contains($aset->id);
                                @endphp
                                <div class="border rounded-lg p-3 transition hover:shadow-md cursor-pointer aset-item {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" 
                                     data-aset-id="{{ $aset->id }}"
                                     data-search="{{ strtolower($aset->nama . ' ' . $aset->kode_aset . ' ' . $aset->kategori . ' ' . ($aset->merk ?? '') . ' ' . ($aset->model ?? '')) }}"
                                     onclick="toggleAset({{ $aset->id }}, '{{ $aset->nama }}', '{{ $aset->kode_aset }}', '{{ $aset->kategori }}', '{{ $aset->foto }}', '{{ $aset->merk }}', '{{ $aset->model }}')">
                                    <div class="flex items-center gap-3">
                                        <input 
                                            type="checkbox" 
                                            name="aset_ids[]" 
                                            value="{{ $aset->id }}"
                                            id="aset-{{ $aset->id }}"
                                            {{ $isSelected ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 aset-checkbox"
                                            onclick="event.stopPropagation()"
                                            onchange="toggleAsetFromCheckbox(this)"
                                        >
                                        @if($aset->foto)
                                            <img src="{{ asset('storage/' . $aset->foto) }}" alt="{{ $aset->nama }}" class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $aset->nama }}</p>
                                            <p class="text-xs text-gray-500">{{ $aset->kode_aset }}</p>
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                {{ $aset->kategori }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- No Results -->
                        <div id="no-results" class="hidden text-center py-12">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 mx-auto" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-search text-4xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 font-semibold mb-2">Tidak ada hasil</p>
                            <p class="text-gray-500 text-sm">Coba kata kunci lain</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Selected Assets -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-4">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                    <h3 class="text-base font-bold text-gray-900">
                        <i class="fas fa-check-circle mr-2" style="color: #3B82C8;"></i>Aset Terpilih
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">Aset yang akan dicek di checkpoint ini</p>
                </div>

                <div class="p-4 max-h-[600px] overflow-y-auto" id="selected-asets">
                    @if($checkpoint->asets->isEmpty())
                        <div class="text-center py-12" id="empty-selected">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 mx-auto" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-hand-pointer text-4xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 font-semibold mb-2">Belum ada aset dipilih</p>
                            <p class="text-gray-500 text-sm">Pilih aset dari daftar di sebelah kiri</p>
                        </div>
                    @else
                        @foreach($checkpoint->asets as $aset)
                            <div class="border border-blue-200 rounded-lg p-3 mb-3 bg-blue-50 selected-aset-card" data-aset-id="{{ $aset->id }}">
                                <div class="flex items-start gap-3 mb-2">
                                    @if($aset->foto)
                                        <img src="{{ asset('storage/' . $aset->foto) }}" alt="{{ $aset->nama }}" class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm">{{ $aset->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ $aset->kode_aset }}</p>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ $aset->kategori }}
                                        </span>
                                    </div>
                                    <button 
                                        type="button"
                                        onclick="removeAset({{ $aset->id }})"
                                        class="text-red-500 hover:text-red-700 transition"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        <i class="fas fa-sticky-note mr-1"></i>Catatan
                                    </label>
                                    <textarea 
                                        name="catatan[{{ $aset->id }}]"
                                        rows="2"
                                        class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent text-xs resize-none"
                                        style="focus:ring-color: #3B82C8;"
                                        placeholder="Catatan untuk aset ini..."
                                    >{{ $aset->pivot->catatan }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($asetKawasans->isNotEmpty())
    <div class="mt-6 flex gap-3">
        <button 
            type="submit"
            class="flex-1 px-6 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl"
            style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
        >
            <i class="fas fa-save mr-2"></i>Simpan Perubahan
        </button>
        <a 
            href="{{ route('perusahaan.patrol.checkpoint') }}"
            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition"
        >
            <i class="fas fa-times mr-2"></i>Batal
        </a>
    </div>
    @endif
</form>
@endsection

@push('scripts')
<script>
const selectedAsets = new Map();

// Initialize selected asets from server
document.addEventListener('DOMContentLoaded', function() {
    @foreach($checkpoint->asets as $aset)
        selectedAsets.set({{ $aset->id }}, {
            nama: '{{ $aset->nama }}',
            kode: '{{ $aset->kode_aset }}',
            kategori: '{{ $aset->kategori }}',
            foto: '{{ $aset->foto }}',
            merk: '{{ $aset->merk }}',
            model: '{{ $aset->model }}',
            catatan: '{{ $aset->pivot->catatan }}'
        });
    @endforeach
    updateSelectedCount();
});

function toggleAset(id, nama, kode, kategori, foto, merk, model) {
    const checkbox = document.getElementById(`aset-${id}`);
    checkbox.checked = !checkbox.checked;
    toggleAsetFromCheckbox(checkbox);
}

function toggleAsetFromCheckbox(checkbox) {
    const asetItem = checkbox.closest('.aset-item');
    const asetId = parseInt(checkbox.value);
    
    if (checkbox.checked) {
        // Add to selected
        asetItem.classList.add('border-blue-500', 'bg-blue-50');
        asetItem.classList.remove('border-gray-200');
        
        // Get aset data from DOM
        const img = asetItem.querySelector('img');
        const nama = asetItem.querySelector('.font-semibold').textContent;
        const kode = asetItem.querySelector('.text-xs').textContent;
        const kategori = asetItem.querySelector('.bg-blue-100').textContent.trim();
        
        selectedAsets.set(asetId, {
            nama: nama,
            kode: kode,
            kategori: kategori,
            foto: img ? img.src : '',
            catatan: ''
        });
        
        addToSelectedList(asetId);
    } else {
        // Remove from selected
        asetItem.classList.remove('border-blue-500', 'bg-blue-50');
        asetItem.classList.add('border-gray-200');
        
        selectedAsets.delete(asetId);
        removeFromSelectedList(asetId);
    }
    
    updateSelectedCount();
}

function addToSelectedList(asetId) {
    const aset = selectedAsets.get(asetId);
    const selectedContainer = document.getElementById('selected-asets');
    const emptyMessage = document.getElementById('empty-selected');
    
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    const fotoHtml = aset.foto ? 
        `<img src="${aset.foto}" alt="${aset.nama}" class="w-12 h-12 object-cover rounded-lg">` :
        `<div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100"><i class="fas fa-image text-gray-400"></i></div>`;
    
    const card = document.createElement('div');
    card.className = 'border border-blue-200 rounded-lg p-3 mb-3 bg-blue-50 selected-aset-card';
    card.setAttribute('data-aset-id', asetId);
    card.innerHTML = `
        <div class="flex items-start gap-3 mb-2">
            ${fotoHtml}
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm">${aset.nama}</p>
                <p class="text-xs text-gray-500">${aset.kode}</p>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    ${aset.kategori}
                </span>
            </div>
            <button 
                type="button"
                onclick="removeAset(${asetId})"
                class="text-red-500 hover:text-red-700 transition"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                <i class="fas fa-sticky-note mr-1"></i>Catatan
            </label>
            <textarea 
                name="catatan[${asetId}]"
                rows="2"
                class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent text-xs resize-none"
                style="focus:ring-color: #3B82C8;"
                placeholder="Catatan untuk aset ini..."
            >${aset.catatan || ''}</textarea>
        </div>
    `;
    
    selectedContainer.appendChild(card);
}

function removeFromSelectedList(asetId) {
    const card = document.querySelector(`.selected-aset-card[data-aset-id="${asetId}"]`);
    if (card) {
        card.remove();
    }
    
    // Show empty message if no asets selected
    const selectedContainer = document.getElementById('selected-asets');
    if (selectedContainer.children.length === 0) {
        selectedContainer.innerHTML = `
            <div class="text-center py-12" id="empty-selected">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 mx-auto" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                    <i class="fas fa-hand-pointer text-4xl" style="color: #3B82C8;"></i>
                </div>
                <p class="text-gray-900 font-semibold mb-2">Belum ada aset dipilih</p>
                <p class="text-gray-500 text-sm">Pilih aset dari daftar di sebelah kiri</p>
            </div>
        `;
    }
}

function removeAset(asetId) {
    const checkbox = document.getElementById(`aset-${asetId}`);
    if (checkbox) {
        checkbox.checked = false;
        toggleAsetFromCheckbox(checkbox);
    }
}

function updateSelectedCount() {
    const count = selectedAsets.size;
    document.getElementById('selected-count').textContent = count;
}

function searchAset() {
    const searchInput = document.getElementById('search-aset');
    const searchTerm = searchInput.value.toLowerCase();
    const asetItems = document.querySelectorAll('.aset-item');
    const noResults = document.getElementById('no-results');
    const asetList = document.getElementById('aset-list');
    const asetCount = document.getElementById('aset-count');
    
    let visibleCount = 0;
    
    asetItems.forEach(item => {
        const searchData = item.getAttribute('data-search');
        
        if (searchData.includes(searchTerm)) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Update count
    if (asetCount) {
        asetCount.textContent = `Menampilkan ${visibleCount} aset`;
    }
    
    // Show/hide no results message
    if (visibleCount === 0) {
        if (asetList) asetList.classList.add('hidden');
        if (noResults) noResults.classList.remove('hidden');
    } else {
        if (asetList) asetList.classList.remove('hidden');
        if (noResults) noResults.classList.add('hidden');
    }
}
</script>
@endpush
