@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a 
                href="{{ route('perusahaan.patrol.pemeriksaan-patroli') }}"
                class="p-2 hover:bg-gray-100 rounded-lg transition"
            >
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $pemeriksaanPatroli->nama }}</h1>
                <p class="text-gray-600">{{ $pemeriksaanPatroli->deskripsi }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                <i class="fas fa-list mr-1"></i>{{ $pemeriksaanPatroli->pertanyaans->count() }} Pertanyaan
            </span>
            @if($pemeriksaanPatroli->frekuensi === 'harian')
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <i class="fas fa-calendar-day mr-1"></i>Harian
                </span>
            @elseif($pemeriksaanPatroli->frekuensi === 'mingguan')
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    <i class="fas fa-calendar-week mr-1"></i>Mingguan
                </span>
            @else
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-1"></i>Bulanan
                </span>
            @endif
            @if($pemeriksaanPatroli->is_active)
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Aktif
                </span>
            @else
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                    <i class="fas fa-times-circle mr-1"></i>Nonaktif
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- List Pertanyaan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Pertanyaan</h2>
                    <a 
                        href="{{ route('perusahaan.patrol.pemeriksaan-patroli.preview', $pemeriksaanPatroli->hash_id) }}"
                        target="_blank"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition inline-flex items-center gap-2"
                    >
                        <i class="fas fa-eye"></i>
                        Preview
                    </a>
                </div>

                <div class="p-6">
                    @if($pemeriksaanPatroli->pertanyaans->count() > 0)
                        <div id="pertanyaanList" class="space-y-4">
                            @foreach($pemeriksaanPatroli->pertanyaans as $pertanyaan)
                                <div 
                                    class="border border-gray-200 rounded-xl p-4 hover:border-blue-300 transition"
                                    data-id="{{ $pertanyaan->id }}"
                                >
                                    <div class="flex items-start gap-4">
                                        <!-- Drag Handle -->
                                        <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 pt-1">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between gap-4 mb-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold">
                                                            Pertanyaan {{ $pertanyaan->urutan }}
                                                        </span>
                                                        @if($pertanyaan->is_required)
                                                            <span class="text-red-500 text-xs">*</span>
                                                        @endif
                                                        @if($pertanyaan->tipe_jawaban === 'pilihan')
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                                                <i class="fas fa-check-circle mr-1"></i>Pilihan
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">
                                                                <i class="fas fa-keyboard mr-1"></i>Text
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-gray-900 font-medium mb-2">{{ $pertanyaan->pertanyaan }}</p>
                                                    
                                                    @if($pertanyaan->tipe_jawaban === 'pilihan' && $pertanyaan->opsi_jawaban)
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($pertanyaan->opsi_jawaban as $opsi)
                                                                <span class="px-3 py-1 bg-gray-50 border border-gray-200 text-gray-700 rounded-lg text-sm">
                                                                    {{ $opsi }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center gap-2">
                                                    <button 
                                                        onclick="editPertanyaan({{ $pertanyaan->id }})"
                                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                        title="Edit"
                                                    >
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button 
                                                        onclick="deletePertanyaan({{ $pertanyaan->id }})"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                        title="Hapus"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">Belum ada pertanyaan</p>
                            <button 
                                onclick="openModal()"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition"
                            >
                                <i class="fas fa-plus mr-2"></i>Tambah Pertanyaan Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Tambah Pertanyaan -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm sticky top-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900" id="formTitle">Tambah Pertanyaan</h2>
                </div>

                <form id="pertanyaanForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" id="pertanyaanId" value="">

                    <!-- Pertanyaan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="pertanyaan" 
                            id="pertanyaan"
                            rows="3"
                            placeholder="Tulis pertanyaan..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            required
                        ></textarea>
                    </div>

                    <!-- Tipe Jawaban -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tipe Jawaban <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="tipe_jawaban" 
                            id="tipe_jawaban"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="toggleOpsiJawaban()"
                            required
                        >
                            <option value="pilihan">Pilihan (Ya/Tidak, dll)</option>
                            <option value="text">Text Input</option>
                        </select>
                    </div>

                    <!-- Opsi Jawaban -->
                    <div id="opsiJawabanContainer">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Opsi Jawaban <span class="text-red-500">*</span>
                        </label>
                        <div id="opsiList" class="space-y-2 mb-2">
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    name="opsi_jawaban[]" 
                                    placeholder="Opsi 1 (contoh: Ya)"
                                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="Baik"
                                >
                            </div>
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    name="opsi_jawaban[]" 
                                    placeholder="Opsi 2 (contoh: Tidak)"
                                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="Tidak Baik"
                                >
                            </div>
                        </div>
                        <button 
                            type="button"
                            onclick="addOpsi()"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            <i class="fas fa-plus mr-1"></i>Tambah Opsi
                        </button>
                    </div>

                    <!-- Wajib Diisi -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" value="1" class="mr-2" checked>
                            <span class="text-sm font-medium text-gray-700">Wajib diisi</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button 
                            type="submit"
                            class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition"
                        >
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                        <button 
                            type="button"
                            onclick="resetForm()"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition"
                        >
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Sortable for drag & drop
const pertanyaanList = document.getElementById('pertanyaanList');
if (pertanyaanList) {
    new Sortable(pertanyaanList, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function(evt) {
            updateUrutan();
        }
    });
}

function updateUrutan() {
    const items = document.querySelectorAll('#pertanyaanList > div');
    const urutan = {};
    
    items.forEach((item, index) => {
        const id = item.getAttribute('data-id');
        urutan[id] = index + 1;
        
        // Update visual urutan
        const badge = item.querySelector('.bg-gray-100');
        if (badge) {
            badge.textContent = 'Pertanyaan ' + (index + 1);
        }
    });

    // Send to server
    fetch('{{ route("perusahaan.patrol.pemeriksaan-patroli.urutan", $pemeriksaanPatroli->hash_id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ urutan: urutan })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// Toggle Opsi Jawaban
function toggleOpsiJawaban() {
    const tipe = document.getElementById('tipe_jawaban').value;
    const container = document.getElementById('opsiJawabanContainer');
    
    if (tipe === 'text') {
        container.style.display = 'none';
        // Remove required from opsi inputs
        document.querySelectorAll('#opsiList input').forEach(input => {
            input.removeAttribute('required');
        });
    } else {
        container.style.display = 'block';
        // Add required to first 2 opsi inputs
        const inputs = document.querySelectorAll('#opsiList input');
        if (inputs[0]) inputs[0].setAttribute('required', 'required');
        if (inputs[1]) inputs[1].setAttribute('required', 'required');
    }
}

// Add Opsi
function addOpsi() {
    const opsiList = document.getElementById('opsiList');
    const count = opsiList.children.length + 1;
    
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input 
            type="text" 
            name="opsi_jawaban[]" 
            placeholder="Opsi ${count}"
            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
        <button 
            type="button"
            onclick="this.parentElement.remove()"
            class="px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-lg transition"
        >
            <i class="fas fa-times"></i>
        </button>
    `;
    
    opsiList.appendChild(div);
}

// Form Submit
document.getElementById('pertanyaanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const pertanyaanId = document.getElementById('pertanyaanId').value;
    const method = document.getElementById('formMethod').value;
    
    let url = '{{ route("perusahaan.patrol.pemeriksaan-patroli.pertanyaan.store", $pemeriksaanPatroli->hash_id) }}';
    
    if (method === 'PUT' && pertanyaanId) {
        url = `{{ url('perusahaan/patrol/pemeriksaan-patroli/' . $pemeriksaanPatroli->hash_id . '/pertanyaan') }}/${pertanyaanId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Terjadi kesalahan');
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: error.message
        });
    });
});

// Edit Pertanyaan
function editPertanyaan(id) {
    // Get pertanyaan data
    const pertanyaanData = @json($pemeriksaanPatroli->pertanyaans);
    const pertanyaan = pertanyaanData.find(p => p.id === id);
    
    if (!pertanyaan) return;
    
    // Update form
    document.getElementById('formTitle').textContent = 'Edit Pertanyaan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('pertanyaanId').value = id;
    document.getElementById('pertanyaan').value = pertanyaan.pertanyaan;
    document.getElementById('tipe_jawaban').value = pertanyaan.tipe_jawaban;
    document.querySelector('input[name="is_required"]').checked = pertanyaan.is_required;
    
    // Handle opsi jawaban
    if (pertanyaan.tipe_jawaban === 'pilihan' && pertanyaan.opsi_jawaban) {
        const opsiList = document.getElementById('opsiList');
        opsiList.innerHTML = '';
        
        pertanyaan.opsi_jawaban.forEach((opsi, index) => {
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input 
                    type="text" 
                    name="opsi_jawaban[]" 
                    placeholder="Opsi ${index + 1}"
                    value="${opsi}"
                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ${index < 2 ? 'required' : ''}
                >
                ${index >= 2 ? `
                    <button 
                        type="button"
                        onclick="this.parentElement.remove()"
                        class="px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                ` : ''}
            `;
            opsiList.appendChild(div);
        });
    }
    
    toggleOpsiJawaban();
    
    // Scroll to form
    document.getElementById('pertanyaanForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Delete Pertanyaan
function deletePertanyaan(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Pertanyaan ini akan dihapus dari kuesioner!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('perusahaan/patrol/pemeriksaan-patroli/' . $pemeriksaanPatroli->hash_id . '/pertanyaan') }}/${id}`;
            form.submit();
        }
    });
}

// Reset Form
function resetForm() {
    document.getElementById('formTitle').textContent = 'Tambah Pertanyaan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('pertanyaanId').value = '';
    document.getElementById('pertanyaanForm').reset();
    
    // Reset opsi list
    const opsiList = document.getElementById('opsiList');
    opsiList.innerHTML = `
        <div class="flex gap-2">
            <input 
                type="text" 
                name="opsi_jawaban[]" 
                placeholder="Opsi 1 (contoh: Ya)"
                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                value="Baik"
            >
        </div>
        <div class="flex gap-2">
            <input 
                type="text" 
                name="opsi_jawaban[]" 
                placeholder="Opsi 2 (contoh: Tidak)"
                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                value="Tidak Baik"
            >
        </div>
    `;
    
    toggleOpsiJawaban();
}

// Success/Error Messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 2000,
        showConfirmButton: false
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
    });
@endif
</script>
@endpush
@endsection
