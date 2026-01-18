@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a 
                href="{{ route('perusahaan.patrol.pertanyaan-tamu') }}"
                class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition"
            >
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $kuesionerTamu->judul }}</h1>
        </div>
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
            <span class="flex items-center gap-2">
                <i class="fas fa-building text-blue-600"></i>
                {{ $kuesionerTamu->project->nama }}
            </span>
            <span class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-green-600"></i>
                {{ $kuesionerTamu->areaPatrol->nama }}
            </span>
            <span class="flex items-center gap-2">
                <i class="fas fa-question-circle text-purple-600"></i>
                {{ $kuesionerTamu->pertanyaans->count() }} Pertanyaan
            </span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <button 
            onclick="openModal()"
            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center gap-2"
        >
            <i class="fas fa-plus"></i>
            Tambah Pertanyaan
        </button>
        <a 
            href="{{ route('perusahaan.patrol.pertanyaan-tamu.preview', $kuesionerTamu->hash_id) }}"
            class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-purple-900 transition inline-flex items-center gap-2"
        >
            <i class="fas fa-eye"></i>
            Preview
        </a>
    </div>

    <!-- Pertanyaan List -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Pertanyaan</h3>
            <p class="text-sm text-gray-600 mt-1">Drag & drop untuk mengubah urutan pertanyaan</p>
        </div>

        <div class="p-6">
            @if($kuesionerTamu->pertanyaans->count() > 0)
                <div id="sortable-list" class="space-y-4">
                    @foreach($kuesionerTamu->pertanyaans as $pertanyaan)
                    <div class="pertanyaan-item bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-move" data-id="{{ $pertanyaan->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold">
                                        {{ $pertanyaan->urutan }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-grip-vertical text-gray-400"></i>
                                        @if($pertanyaan->is_required)
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">Wajib</span>
                                        @endif
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">
                                            {{ $pertanyaan->tipe_jawaban === 'pilihan' ? 'Pilihan Ganda' : 'Text' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $pertanyaan->pertanyaan }}</h4>
                                
                                @if($pertanyaan->tipe_jawaban === 'pilihan' && $pertanyaan->opsi_jawaban)
                                    <div class="space-y-1">
                                        @foreach($pertanyaan->opsi_jawaban as $index => $opsi)
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <span class="w-4 h-4 border border-gray-300 rounded-full flex items-center justify-center text-xs">
                                                    {{ chr(65 + $index) }}
                                                </span>
                                                {{ $opsi }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2 ml-4">
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
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-question-circle text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pertanyaan</h3>
                    <p class="text-gray-600 mb-6">Mulai dengan menambahkan pertanyaan pertama untuk kuesioner ini</p>
                    <button 
                        onclick="openModal()"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center gap-2"
                    >
                        <i class="fas fa-plus"></i>
                        Tambah Pertanyaan
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <form id="pertanyaanForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" id="pertanyaanId" value="">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Pertanyaan</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
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
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="tipe_jawaban" value="pilihan" class="mr-2" onchange="toggleOpsiJawaban()" checked>
                            <span class="text-sm">Pilihan Ganda</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="tipe_jawaban" value="text" class="mr-2" onchange="toggleOpsiJawaban()">
                            <span class="text-sm">Text</span>
                        </label>
                    </div>
                </div>

                <!-- Opsi Jawaban -->
                <div id="opsiJawabanContainer">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Opsi Jawaban <span class="text-red-500">*</span>
                    </label>
                    <div id="opsiList" class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                name="opsi_jawaban[]" 
                                placeholder="Opsi 1"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            <button type="button" onclick="removeOpsi(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                name="opsi_jawaban[]" 
                                placeholder="Opsi 2"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            <button type="button" onclick="removeOpsi(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        onclick="addOpsi()"
                        class="mt-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm"
                    >
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Opsi
                    </button>
                </div>

                <!-- Wajib Diisi -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_required" value="1" class="mr-2">
                        <span class="text-sm font-semibold text-gray-700">Wajib diisi</span>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="closeModal()"
                    class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition"
                >
                    Simpan
                </button>
            </div>
        </form>
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
// Initialize Sortable
let sortable;
if (document.getElementById('sortable-list')) {
    sortable = Sortable.create(document.getElementById('sortable-list'), {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function (evt) {
            updateUrutan();
        }
    });
}

// Modal Functions
function openModal() {
    document.getElementById('formModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Pertanyaan';
    document.getElementById('pertanyaanForm').action = '{{ route("perusahaan.patrol.pertanyaan-tamu.pertanyaan.store", $kuesionerTamu->hash_id) }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('pertanyaanId').value = '';
    document.getElementById('pertanyaanForm').reset();
    
    // Reset to pilihan ganda
    document.querySelector('input[name="tipe_jawaban"][value="pilihan"]').checked = true;
    toggleOpsiJawaban();
    
    // Reset opsi list
    const opsiList = document.getElementById('opsiList');
    opsiList.innerHTML = `
        <div class="flex items-center gap-2">
            <input type="text" name="opsi_jawaban[]" placeholder="Opsi 1" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            <button type="button" onclick="removeOpsi(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" name="opsi_jawaban[]" placeholder="Opsi 2" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            <button type="button" onclick="removeOpsi(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

function closeModal() {
    document.getElementById('formModal').classList.add('hidden');
    document.getElementById('pertanyaanForm').reset();
}

function toggleOpsiJawaban() {
    const tipeJawaban = document.querySelector('input[name="tipe_jawaban"]:checked').value;
    const opsiContainer = document.getElementById('opsiJawabanContainer');
    
    if (tipeJawaban === 'pilihan') {
        opsiContainer.style.display = 'block';
        // Make opsi inputs required
        document.querySelectorAll('input[name="opsi_jawaban[]"]').forEach(input => {
            input.required = true;
        });
    } else {
        opsiContainer.style.display = 'none';
        // Remove required from opsi inputs
        document.querySelectorAll('input[name="opsi_jawaban[]"]').forEach(input => {
            input.required = false;
        });
    }
}

function addOpsi() {
    const opsiList = document.getElementById('opsiList');
    const opsiCount = opsiList.children.length + 1;
    
    const newOpsi = document.createElement('div');
    newOpsi.className = 'flex items-center gap-2';
    newOpsi.innerHTML = `
        <input 
            type="text" 
            name="opsi_jawaban[]" 
            placeholder="Opsi ${opsiCount}"
            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
        >
        <button type="button" onclick="removeOpsi(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    opsiList.appendChild(newOpsi);
}

function removeOpsi(button) {
    const opsiList = document.getElementById('opsiList');
    if (opsiList.children.length > 2) {
        button.parentElement.remove();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Minimal harus ada 2 opsi jawaban',
        });
    }
}

function editPertanyaan(id) {
    // Find pertanyaan data from the page
    const pertanyaanItems = @json($kuesionerTamu->pertanyaans);
    const pertanyaan = pertanyaanItems.find(p => p.id === id);
    
    if (!pertanyaan) return;
    
    document.getElementById('formModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Edit Pertanyaan';
    document.getElementById('pertanyaanForm').action = `{{ url('perusahaan/patrol/pertanyaan-tamu/' . $kuesionerTamu->hash_id . '/pertanyaan') }}/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('pertanyaanId').value = id;
    
    // Fill form
    document.getElementById('pertanyaan').value = pertanyaan.pertanyaan;
    document.querySelector(`input[name="tipe_jawaban"][value="${pertanyaan.tipe_jawaban}"]`).checked = true;
    document.querySelector('input[name="is_required"]').checked = pertanyaan.is_required;
    
    toggleOpsiJawaban();
    
    // Fill opsi jawaban if pilihan
    if (pertanyaan.tipe_jawaban === 'pilihan' && pertanyaan.opsi_jawaban) {
        const opsiList = document.getElementById('opsiList');
        opsiList.innerHTML = '';
        
        pertanyaan.opsi_jawaban.forEach((opsi, index) => {
            const opsiDiv = document.createElement('div');
            opsiDiv.className = 'flex items-center gap-2';
            opsiDiv.innerHTML = `
                <input 
                    type="text" 
                    name="opsi_jawaban[]" 
                    placeholder="Opsi ${index + 1}"
                    value="${opsi}"
                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                <button type="button" onclick="removeOpsi(this)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            opsiList.appendChild(opsiDiv);
        });
    }
}

function deletePertanyaan(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Pertanyaan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('perusahaan/patrol/pertanyaan-tamu/' . $kuesionerTamu->hash_id . '/pertanyaan') }}/${id}`;
            form.submit();
        }
    });
}

function updateUrutan() {
    const items = document.querySelectorAll('.pertanyaan-item');
    const urutan = {};
    
    items.forEach((item, index) => {
        const id = item.getAttribute('data-id');
        urutan[id] = index + 1;
        
        // Update visual urutan
        const urutanSpan = item.querySelector('.bg-blue-100');
        urutanSpan.textContent = index + 1;
    });
    
    fetch('{{ route("perusahaan.patrol.pertanyaan-tamu.urutan", $kuesionerTamu->hash_id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal mengupdate urutan'
        });
    });
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleOpsiJawaban();
});
</script>

<style>
.sortable-ghost {
    opacity: 0.4;
}

.pertanyaan-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endpush
@endsection