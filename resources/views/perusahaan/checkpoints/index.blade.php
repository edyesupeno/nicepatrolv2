@extends('perusahaan.layouts.app')

@section('title', 'Checkpoint')
@section('page-title', 'Checkpoint')
@section('page-subtitle', 'Kelola checkpoint untuk patroli')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-gray-600">Total: <span class="font-bold text-sky-600">{{ $checkpoints->total() }}</span> checkpoint</p>
    </div>
    <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
        <i class="fas fa-plus mr-2"></i>Tambah Checkpoint
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-sky-500 to-blue-500 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Nama Checkpoint</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Lokasi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Kode QR</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($checkpoints as $index => $checkpoint)
                <tr class="hover:bg-sky-50 transition">
                    <td class="px-6 py-4 text-gray-600">{{ $checkpoints->firstItem() + $index }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $checkpoint->nama }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center text-gray-700">
                            <i class="fas fa-map-marker-alt text-sky-500 mr-2"></i>
                            {{ $checkpoint->kantor->nama }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <code class="bg-gray-100 px-3 py-1 rounded text-sm font-mono">{{ $checkpoint->kode_qr }}</code>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <button onclick="showQR('{{ $checkpoint->hash_id }}')" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-lg text-sm transition" title="Lihat QR">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            <button onclick="openEditModal('{{ $checkpoint->hash_id }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDelete('{{ $checkpoint->hash_id }}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada data checkpoint</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $checkpoints->links() }}
</div>

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <form action="{{ route('perusahaan.checkpoints.store') }}" method="POST" id="formCreate">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Checkpoint Baru</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <select 
                        name="lokasi_id" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    >
                        <option value="">Pilih Lokasi</option>
                        @foreach($kantors as $kantor)
                            <option value="{{ $kantor->id }}">{{ $kantor->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Checkpoint <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="nama" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        placeholder="Contoh: Pintu Masuk Utama"
                    >
                </div>

                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeCreateModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium transition"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Checkpoint</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <select 
                        name="lokasi_id" 
                        id="edit_lokasi_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    >
                        <option value="">Pilih Lokasi</option>
                        @foreach($kantors as $kantor)
                            <option value="{{ $kantor->id }}">{{ $kantor->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Checkpoint <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="nama" 
                        id="edit_nama"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    >
                </div>

                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeEditModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium transition"
                    >
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code -->
<div id="modalQR" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">QR Code Checkpoint</h3>
            
            <div class="flex flex-col items-center">
                <div id="qrCodeContainer" class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-4">
                    <!-- QR Code will be inserted here -->
                </div>
                <p class="text-sm text-gray-600 mb-2" id="qrCodeName"></p>
                <code class="bg-gray-100 px-3 py-1 rounded text-sm font-mono mb-4" id="qrCodeValue"></code>
                
                <button 
                    onclick="downloadQR()"
                    class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg font-medium transition inline-flex items-center mb-3"
                >
                    <i class="fas fa-download mr-2"></i>Download QR
                </button>
            </div>

            <button 
                type="button" 
                onclick="closeQRModal()"
                class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function openCreateModal() {
    document.getElementById('modalCreate').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('modalCreate').classList.add('hidden');
    document.getElementById('formCreate').reset();
}

async function openEditModal(hashId) {
    try {
        const response = await fetch(`/perusahaan/checkpoints/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_lokasi_id').value = data.lokasi_id;
        document.getElementById('formEdit').action = `/perusahaan/checkpoints/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data checkpoint'
        });
    }
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

async function showQR(hashId) {
    try {
        const response = await fetch(`/perusahaan/checkpoints/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('qrCodeName').textContent = data.nama;
        document.getElementById('qrCodeValue').textContent = data.kode_qr;
        
        const qrContainer = document.getElementById('qrCodeContainer');
        qrContainer.innerHTML = '';
        
        QRCode.toCanvas(data.kode_qr, { width: 256, margin: 2 }, function (error, canvas) {
            if (error) console.error(error);
            qrContainer.appendChild(canvas);
        });
        
        document.getElementById('modalQR').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat QR Code'
        });
    }
}

function closeQRModal() {
    document.getElementById('modalQR').classList.add('hidden');
}

function downloadQR() {
    const canvas = document.querySelector('#qrCodeContainer canvas');
    const link = document.createElement('a');
    link.download = 'qr-checkpoint.png';
    link.href = canvas.toDataURL();
    link.click();
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data checkpoint akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/checkpoints/${hashId}`;
            form.submit();
        }
    });
}

// Close modals when clicking outside
document.getElementById('modalCreate')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});

document.getElementById('modalEdit')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

document.getElementById('modalQR')?.addEventListener('click', function(e) {
    if (e.target === this) closeQRModal();
});
</script>
@endpush
