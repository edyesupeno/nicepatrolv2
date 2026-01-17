@extends('perusahaan.layouts.app')

@section('title', 'Kontak Penting - ' . $project->nama)
@section('page-title', 'Kontak Penting')
@section('page-subtitle', 'Kelola kontak penting untuk project: ' . $project->nama)

@section('content')
<div class="mb-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.projects.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-sky-600">
                    <i class="fas fa-project-diagram mr-2"></i>
                    Projects
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $project->nama }}</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Kontak Penting</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <p class="text-gray-600">Total: <span class="font-bold text-sky-600">{{ $contacts->count() }}</span> kontak</p>
        </div>
        <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center shadow-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Kontak
        </button>
    </div>
</div>

<!-- Project Info Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $project->nama }}</h3>
            <div class="flex items-center gap-6 text-sm text-gray-600">
                <div class="flex items-center">
                    <i class="fas fa-building text-gray-400 mr-2"></i>
                    <span>{{ $project->kantor->nama }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-globe text-gray-400 mr-2"></i>
                    <span>{{ $project->timezone }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                    <span>{{ $project->tanggal_mulai->format('d M Y') }} - {{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d M Y') : 'Sekarang' }}</span>
                </div>
            </div>
        </div>
        @if($project->is_active)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                <i class="fas fa-check-circle mr-1"></i>Project Aktif
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                Project Selesai
            </span>
        @endif
    </div>
</div>

<!-- Contacts Grid -->
@if($contacts->count() > 0)
    <!-- Group by jenis_kontak -->
    @php
        $groupedContacts = $contacts->groupBy('jenis_kontak');
        $jenisOrder = ['polisi', 'pemadam_kebakaran', 'ambulans', 'security', 'manager_project', 'supervisor', 'teknisi', 'lainnya'];
    @endphp

    @foreach($jenisOrder as $jenis)
        @if($groupedContacts->has($jenis))
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="{{ $groupedContacts[$jenis]->first()->jenis_kontak_icon }} text-{{ $groupedContacts[$jenis]->first()->jenis_kontak_color }}-600 mr-3"></i>
                    {{ $groupedContacts[$jenis]->first()->jenis_kontak_label }}
                    <span class="ml-2 bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $groupedContacts[$jenis]->count() }}</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groupedContacts[$jenis] as $contact)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                            <div class="p-6">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <h4 class="text-lg font-bold text-gray-900">{{ $contact->nama_kontak }}</h4>
                                            @if($contact->is_primary)
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    <i class="fas fa-star mr-1"></i>Primary
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-1">{{ $contact->jabatan_kontak }}</p>
                                        @if(!$contact->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="openEditModal('{{ $contact->hash_id }}')" class="text-blue-600 hover:text-blue-800 p-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete('{{ $contact->hash_id }}')" class="text-red-600 hover:text-red-800 p-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-3 w-4"></i>
                                        <a href="tel:{{ $contact->nomor_telepon }}" class="text-sky-600 hover:text-sky-800 font-medium">
                                            {{ $contact->nomor_telepon }}
                                        </a>
                                    </div>
                                    
                                    @if($contact->email)
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-400 mr-3 w-4"></i>
                                            <a href="mailto:{{ $contact->email }}" class="text-sky-600 hover:text-sky-800">
                                                {{ $contact->email }}
                                            </a>
                                        </div>
                                    @endif

                                    @if($contact->keterangan)
                                        <div class="flex items-start">
                                            <i class="fas fa-info-circle text-gray-400 mr-3 w-4 mt-0.5"></i>
                                            <p class="text-sm text-gray-600">{{ $contact->keterangan }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Quick Actions -->
                                <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                                    <a href="tel:{{ $contact->nomor_telepon }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
                                        <i class="fas fa-phone mr-1"></i>Call
                                    </a>
                                    <a href="sms:{{ $contact->nomor_telepon }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
                                        <i class="fas fa-sms mr-1"></i>SMS
                                    </a>
                                    @if($contact->email)
                                        <a href="mailto:{{ $contact->email }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition text-center">
                                            <i class="fas fa-envelope mr-1"></i>Email
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-address-book text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg mb-2">Belum ada kontak penting</p>
        <p class="text-gray-400 text-sm mb-6">Tambahkan kontak penting seperti polisi, pemadam kebakaran, dan lainnya</p>
        <button onclick="openCreateModal()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Tambah Kontak
        </button>
    </div>
@endif

<!-- Modal Create -->
<div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('perusahaan.projects.contacts.store', $project->hash_id) }}" method="POST" id="formCreate">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Kontak Penting</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kontak <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="nama_kontak" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="Nama lengkap kontak"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="jabatan_kontak" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="Jabatan/posisi"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input 
                                type="tel" 
                                name="nomor_telepon" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="08xxxxxxxxxx"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input 
                                type="email" 
                                name="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                                placeholder="email@example.com"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kontak <span class="text-red-500">*</span></label>
                        <select 
                            name="jenis_kontak" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Jenis Kontak</option>
                            <option value="polisi">🚔 Polisi</option>
                            <option value="pemadam_kebakaran">🚒 Pemadam Kebakaran</option>
                            <option value="ambulans">🚑 Ambulans/Medis</option>
                            <option value="security">🛡️ Security</option>
                            <option value="manager_project">👔 Manager Project</option>
                            <option value="supervisor">👷 Supervisor</option>
                            <option value="teknisi">🔧 Teknisi</option>
                            <option value="lainnya">📞 Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea 
                            name="keterangan" 
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Keterangan tambahan (opsional)"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="hidden" name="is_primary" value="0">
                            <input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Kontak Utama</span>
                        </label>

                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
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
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Kontak Penting</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kontak <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="nama_kontak" 
                                id="edit_nama_kontak"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                name="jabatan_kontak" 
                                id="edit_jabatan_kontak"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input 
                                type="tel" 
                                name="nomor_telepon" 
                                id="edit_nomor_telepon"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input 
                                type="email" 
                                name="email"
                                id="edit_email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kontak <span class="text-red-500">*</span></label>
                        <select 
                            name="jenis_kontak" 
                            id="edit_jenis_kontak"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="">Pilih Jenis Kontak</option>
                            <option value="polisi">🚔 Polisi</option>
                            <option value="pemadam_kebakaran">🚒 Pemadam Kebakaran</option>
                            <option value="ambulans">🚑 Ambulans/Medis</option>
                            <option value="security">🛡️ Security</option>
                            <option value="manager_project">👔 Manager Project</option>
                            <option value="supervisor">👷 Supervisor</option>
                            <option value="teknisi">🔧 Teknisi</option>
                            <option value="lainnya">📞 Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea 
                            name="keterangan" 
                            id="edit_keterangan"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="hidden" name="is_primary" value="0">
                            <input type="checkbox" name="is_primary" value="1" id="edit_is_primary" class="rounded border-gray-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Kontak Utama</span>
                        </label>

                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="edit_is_active" class="rounded border-gray-300 text-sky-600 shadow-sm focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
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

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
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
        const response = await fetch(`/perusahaan/projects/{{ $project->hash_id }}/contacts/${hashId}/edit`);
        const data = await response.json();
        
        document.getElementById('edit_nama_kontak').value = data.nama_kontak;
        document.getElementById('edit_jabatan_kontak').value = data.jabatan_kontak;
        document.getElementById('edit_nomor_telepon').value = data.nomor_telepon;
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_jenis_kontak').value = data.jenis_kontak;
        document.getElementById('edit_keterangan').value = data.keterangan || '';
        document.getElementById('edit_is_primary').checked = data.is_primary;
        document.getElementById('edit_is_active').checked = data.is_active;
        
        document.getElementById('formEdit').action = `/perusahaan/projects/{{ $project->hash_id }}/contacts/${hashId}`;
        
        document.getElementById('modalEdit').classList.remove('hidden');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data kontak'
        });
    }
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function confirmDelete(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data kontak akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/perusahaan/projects/{{ $project->hash_id }}/contacts/${hashId}`;
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
</script>
@endpush