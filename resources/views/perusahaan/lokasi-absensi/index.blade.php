@extends('perusahaan.layouts.app')

@section('title', 'Lokasi Absensi')
@section('page-title', 'Lokasi Absensi')
@section('page-subtitle', 'Kelola lokasi absensi karyawan per project')

@section('content')
<!-- Stats & Actions Bar -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-map-marker-alt text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Lokasi</p>
                    <p class="text-2xl font-bold" style="color: #3B82C8;">{{ $lokasis->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <button onclick="openTambahModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white hover:shadow-xl" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
        <i class="fas fa-plus mr-2"></i>Tambah Lokasi
    </button>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lokasi atau alamat..." class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent text-sm" style="focus:ring-color: #3B82C8;">
        </div>
        <select name="project_id" class="lg:w-64 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 text-sm">
            <option value="">Semua Project</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-3 rounded-xl font-medium transition text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request()->hasAny(['search', 'project_id']))
            <a href="{{ route('perusahaan.kehadiran.lokasi-absensi') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt mr-2" style="color: #3B82C8;"></i>Nama Lokasi
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-map-marked-alt mr-2" style="color: #3B82C8;"></i>Alamat
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-crosshairs mr-2" style="color: #3B82C8;"></i>Koordinat
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-circle-notch mr-2" style="color: #3B82C8;"></i>Radius
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-cog mr-2" style="color: #3B82C8;"></i>Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lokasis as $lokasi)
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                <i class="fas fa-project-diagram text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $lokasi->project->nama }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-900">{{ $lokasi->nama_lokasi }}</p>
                        @if($lokasi->deskripsi)
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($lokasi->deskripsi, 50) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-700">{{ Str::limit($lokasi->alamat, 50) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-gray-600">
                            <p class="font-mono">{{ $lokasi->latitude }}, {{ $lokasi->longitude }}</p>
                            <a href="https://www.google.com/maps?q={{ $lokasi->latitude }},{{ $lokasi->longitude }}" target="_blank" class="text-blue-600 hover:text-blue-800 mt-1 inline-flex items-center">
                                <i class="fas fa-external-link-alt mr-1"></i>Lihat di Maps
                            </a>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-700">{{ $lokasi->radius }} meter</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditModal('{{ $lokasi->hash_id }}')" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDelete('{{ $lokasi->hash_id }}')" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                                <i class="fas fa-map-marker-alt text-5xl" style="color: #3B82C8;"></i>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold mb-2">
                                @if(request()->hasAny(['search', 'project_id']))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada lokasi absensi
                                @endif
                            </p>
                            <p class="text-gray-500 text-sm mb-6">
                                @if(request()->hasAny(['search', 'project_id']))
                                    Coba ubah kata kunci atau filter pencarian Anda
                                @else
                                    Tambahkan lokasi absensi pertama untuk memulai
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Lokasi -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Tambah Lokasi</h3>
                <button onclick="closeTambahModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ route('perusahaan.lokasi-absensi.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Lokasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lokasi" required maxlength="255" placeholder="Contoh: Gedung Utama" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" required rows="3" placeholder="Alamat lengkap lokasi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>

                <!-- Koordinat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Koordinat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="koordinat" required placeholder="Paste koordinat: 0.4439257712249158, 101.45440" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">
                        Paste koordinat dari Google Maps atau format: "latitude, longitude"
                    </p>
                    <div class="flex gap-2 mt-2">
                        <a href="https://www.google.com/maps" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition">
                            <i class="fas fa-map-marked-alt mr-1"></i>Contoh PHR
                        </a>
                        <a href="https://www.google.com/maps/place/Jakarta" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-lg text-xs font-medium hover:bg-green-100 transition">
                            <i class="fas fa-map-marker-alt mr-1"></i>Contoh Jakarta
                        </a>
                    </div>
                </div>

                <!-- Radius -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Radius (meter) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="radius" required min="10" max="1000" value="100" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">
                        Jarak maksimal karyawan dapat melakukan absensi (10-1000 meter)
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi lokasi (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeTambahModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Lokasi -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Edit Lokasi</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formEdit" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="edit_project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Lokasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lokasi" id="edit_nama_lokasi" required maxlength="255" placeholder="Contoh: Gedung Utama" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="edit_alamat" required rows="3" placeholder="Alamat lengkap lokasi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                </div>

                <!-- Koordinat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Koordinat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="koordinat" id="edit_koordinat" required placeholder="Paste koordinat: 0.4439257712249158, 101.45440" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">
                        Paste koordinat dari Google Maps atau format: "latitude, longitude"
                    </p>
                </div>

                <!-- Radius -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Radius (meter) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="radius" id="edit_radius" required min="10" max="1000" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">
                        Jarak maksimal karyawan dapat melakukan absensi (10-1000 meter)
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" placeholder="Deskripsi lokasi (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-200">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete (Hidden) -->
<form id="formDelete" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    const lokasisData = @json($lokasis);

    function openTambahModal() {
        document.getElementById('modalTambah').classList.remove('hidden');
    }

    function closeTambahModal() {
        document.getElementById('modalTambah').classList.add('hidden');
    }

    function openEditModal(hashId) {
        const lokasi = lokasisData.find(l => l.hash_id === hashId);
        if (!lokasi) return;

        document.getElementById('formEdit').action = `/perusahaan/lokasi-absensi/${hashId}`;
        document.getElementById('edit_project_id').value = lokasi.project_id;
        document.getElementById('edit_nama_lokasi').value = lokasi.nama_lokasi;
        document.getElementById('edit_alamat').value = lokasi.alamat;
        document.getElementById('edit_koordinat').value = `${lokasi.latitude}, ${lokasi.longitude}`;
        document.getElementById('edit_radius').value = lokasi.radius;
        document.getElementById('edit_deskripsi').value = lokasi.deskripsi || '';

        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }

    function confirmDelete(hashId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Lokasi absensi ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formDelete');
                form.action = `/perusahaan/lokasi-absensi/${hashId}`;
                form.submit();
            }
        });
    }

    // Close modal when clicking outside
    document.getElementById('modalTambah').addEventListener('click', function(e) {
        if (e.target === this) closeTambahModal();
    });

    document.getElementById('modalEdit').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endpush
@endsection
