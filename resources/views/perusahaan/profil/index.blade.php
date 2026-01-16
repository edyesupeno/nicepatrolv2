@extends('perusahaan.layouts.app')

@section('title', 'Profil Perusahaan')
@section('page-title', 'Profil Perusahaan')
@section('page-subtitle', 'Kelola informasi perusahaan Anda')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Company Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Informasi Perusahaan</h3>
                <button onclick="openEditModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit
                </button>
            </div>

            <div class="space-y-4">
                <!-- Nama Perusahaan -->
                <div>
                    <label class="text-sm text-gray-500 block mb-1">Nama Perusahaan</label>
                    <p class="text-gray-900 font-medium">{{ $perusahaan->nama }}</p>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="text-sm text-gray-500 block mb-1">Alamat</label>
                    <p class="text-gray-900">{{ $perusahaan->alamat ?? '-' }}</p>
                </div>

                <!-- No. Telepon -->
                <div>
                    <label class="text-sm text-gray-500 block mb-1">No. Telepon</label>
                    <p class="text-gray-900">{{ $perusahaan->telepon ?? '-' }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-500 block mb-1">Email</label>
                    <p class="text-gray-900">{{ $perusahaan->email ?? '-' }}</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="text-sm text-gray-500 block mb-1">Status</label>
                    @if($perusahaan->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i>Nonaktif
                        </span>
                    @endif
                </div>

                <!-- Terdaftar Sejak -->
                <div>
                    <label class="text-sm text-gray-500 block mb-1">Terdaftar Sejak</label>
                    <p class="text-gray-900">{{ $perusahaan->created_at->format('d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Logo & Stats -->
    <div class="space-y-6">
        <!-- Logo Perusahaan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Logo Perusahaan</h3>
            
            <div class="flex flex-col items-center">
                <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                    @if($perusahaan->logo)
                        <img src="{{ asset('storage/' . $perusahaan->logo) }}" alt="Logo" class="w-full h-full object-cover rounded-lg">
                    @else
                        <i class="fas fa-building text-gray-400 text-4xl"></i>
                    @endif
                </div>
                
                <button onclick="openLogoModal()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Pilih File
                </button>
                <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, GIF. Maksimal 5MB</p>
            </div>
        </div>

        <!-- Statistik -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Statistik</h3>
                <i class="fas fa-chart-bar text-2xl opacity-50"></i>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm opacity-90">Total User</span>
                    <span class="text-2xl font-bold">{{ $stats['total_users'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm opacity-90">Total Kantor</span>
                    <span class="text-2xl font-bold">{{ $stats['total_kantor'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm opacity-90">Sesi Patroli</span>
                    <span class="text-2xl font-bold">{{ $stats['total_patroli'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Perusahaan -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <form action="{{ route('perusahaan.profil.update') }}" method="POST" id="formEdit">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Informasi Perusahaan</h3>
                
                <!-- Nama Perusahaan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan</label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ $perusahaan->nama }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea 
                        name="alamat" 
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ $perusahaan->alamat }}</textarea>
                </div>

                <!-- No. Telepon -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <input 
                        type="text" 
                        name="telepon" 
                        value="{{ $perusahaan->telepon }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ $perusahaan->email }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Buttons -->
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
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Upload Logo -->
<div id="modalLogo" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <form action="{{ route('perusahaan.profil.upload-logo') }}" method="POST" enctype="multipart/form-data" id="formLogo">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Upload Logo Perusahaan</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                    <input 
                        type="file" 
                        name="logo" 
                        accept="image/*"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, GIF. Maksimal 5MB</p>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeLogoModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                    >
                        Upload
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal() {
    document.getElementById('modalEdit').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function openLogoModal() {
    document.getElementById('modalLogo').classList.remove('hidden');
}

function closeLogoModal() {
    document.getElementById('modalLogo').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('modalEdit')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

document.getElementById('modalLogo')?.addEventListener('click', function(e) {
    if (e.target === this) closeLogoModal();
});
</script>
@endpush
