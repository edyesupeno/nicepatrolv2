@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('perusahaan.tim-patroli.master') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Tim Patroli</h1>
        </div>
        <p class="text-gray-600">Buat tim patroli baru dengan area dan rute tanggung jawab</p>
    </div>

    <!-- Form -->
    <form action="{{ route('perusahaan.tim-patroli.store') }}" method="POST" id="timPatroliForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Informasi Dasar
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Project -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Project <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="project_id" 
                                id="project_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
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

                        <!-- Nama Tim -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Tim <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nama_tim" 
                                id="nama_tim"
                                value="{{ old('nama_tim') }}"
                                placeholder="Contoh: Tim Alpha Shift Pagi"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('nama_tim')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shift -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Shift <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="shift" 
                                id="shift"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="">Pilih Shift</option>
                                <option value="pagi" {{ old('shift') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="siang" {{ old('shift') == 'siang' ? 'selected' : '' }}>Siang</option>
                                <option value="malam" {{ old('shift') == 'malam' ? 'selected' : '' }}>Malam</option>
                            </select>
                            @error('shift')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Leader -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Leader Tim
                            </label>
                            <select 
                                name="leader_id" 
                                id="leader_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Pilih Leader</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('leader_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Area Tanggung Jawab -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marked-alt text-blue-600"></i>
                        Area Tanggung Jawab
                    </h3>
                    
                    <div id="areasContainer" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>
                    </div>
                </div>

                <!-- Rute Patroli -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-route text-blue-600"></i>
                        Rute Patroli
                    </h3>
                    
                    <div id="rutesContainer" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>
                    </div>
                </div>

                <!-- Checkpoint -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                        Checkpoint yang Harus Dikunjungi
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">Pilih checkpoint yang harus dikunjungi oleh tim patroli ini</p>
                    
                    <div id="checkpointsContainer" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>
                    </div>
                </div>

                <!-- Inventaris Patroli -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-box text-blue-600"></i>
                        Inventaris Patroli
                    </h3>
                    
                    <div id="inventarisContainer" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>
                    </div>
                </div>

                <!-- Kuesioner Patroli -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        Kuesioner Patroli
                    </h3>
                    
                    <div id="kuesionersContainer" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>
                    </div>
                </div>

                <!-- Pemeriksaan Patroli -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-blue-600"></i>
                        Pemeriksaan Patroli
                    </h3>
                    
                    <div id="pemeriksaansContainer" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Status</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="is_active" value="1" class="mr-3" {{ old('is_active', '1') == '1' ? 'checked' : '' }} required>
                            <div>
                                <p class="font-semibold text-gray-900">Aktif</p>
                                <p class="text-xs text-gray-500">Tim dapat melakukan patroli</p>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="is_active" value="0" class="mr-3" {{ old('is_active') == '0' ? 'checked' : '' }}>
                            <div>
                                <p class="font-semibold text-gray-900">Nonaktif</p>
                                <p class="text-xs text-gray-500">Tim tidak dapat melakukan patroli</p>
                            </div>
                        </label>
                    </div>

                    @error('is_active')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror

                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-save"></i>
                            Simpan Tim Patroli
                        </button>
                        <a 
                            href="{{ route('perusahaan.tim-patroli.master') }}"
                            class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition inline-flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('project_id').addEventListener('change', function() {
    const projectId = this.value;
    
    if (!projectId) {
        resetContainers();
        return;
    }

    // Show loading
    showLoading();

    // Fetch data
    fetch(`{{ url('perusahaan/tim-patroli/get-data-by-project') }}/${projectId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
            populateAreas(data.areas);
            populateRutes(data.rutes);
            populateCheckpoints(data.checkpoints);
            populateInventaris(data.inventaris);
            populateKuesioners(data.kuesioners);
            populatePemeriksaans(data.pemeriksaans);
        })
        .catch(error => {
            console.error('Error:', error);
            resetContainers();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal memuat data. Silakan coba lagi.',
            });
        });
});

function showLoading() {
    const containers = ['areasContainer', 'rutesContainer', 'checkpointsContainer', 'inventarisContainer', 'kuesionersContainer', 'pemeriksaansContainer'];
    containers.forEach(id => {
        document.getElementById(id).innerHTML = '<p class="text-sm text-gray-500 italic"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</p>';
    });
}

function resetContainers() {
    const containers = ['areasContainer', 'rutesContainer', 'checkpointsContainer', 'inventarisContainer', 'kuesionersContainer', 'pemeriksaansContainer'];
    containers.forEach(id => {
        document.getElementById(id).innerHTML = '<p class="text-sm text-gray-500 italic">Pilih project terlebih dahulu</p>';
    });
}

function populateAreas(areas) {
    const container = document.getElementById('areasContainer');
    if (areas.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada area tersedia</p>';
        return;
    }

    let html = '';
    areas.forEach(area => {
        html += `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="areas[]" value="${area.id}" class="mr-3">
                <span class="text-sm font-medium text-gray-900">${area.nama}</span>
            </label>
        `;
    });
    container.innerHTML = html;
}

function populateRutes(rutes) {
    const container = document.getElementById('rutesContainer');
    if (rutes.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada rute tersedia</p>';
        return;
    }

    let html = '';
    rutes.forEach(rute => {
        html += `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="rutes[]" value="${rute.id}" class="mr-3">
                <span class="text-sm font-medium text-gray-900">${rute.nama}</span>
            </label>
        `;
    });
    container.innerHTML = html;
}

function populateCheckpoints(checkpoints) {
    const container = document.getElementById('checkpointsContainer');
    if (checkpoints.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada checkpoint tersedia</p>';
        return;
    }

    let html = '';
    let currentRute = '';
    checkpoints.forEach(checkpoint => {
        if (currentRute !== checkpoint.rute_nama) {
            if (currentRute !== '') {
                html += '</div>';
            }
            currentRute = checkpoint.rute_nama;
            html += `<div class="mb-3"><p class="text-xs font-semibold text-gray-600 uppercase mb-2">${currentRute}</p>`;
        }
        html += `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition mb-2">
                <input type="checkbox" name="checkpoints[]" value="${checkpoint.id}" class="mr-3">
                <span class="text-sm font-medium text-gray-900">${checkpoint.nama}</span>
            </label>
        `;
    });
    if (currentRute !== '') {
        html += '</div>';
    }
    container.innerHTML = html;
}

function populateInventaris(inventaris) {
    const container = document.getElementById('inventarisContainer');
    if (inventaris.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada inventaris tersedia</p>';
        return;
    }

    let html = '';
    inventaris.forEach(item => {
        html += `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="inventaris[]" value="${item.id}" class="mr-3">
                <div class="flex-1">
                    <span class="text-sm font-medium text-gray-900">${item.nama}</span>
                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">${item.kategori}</span>
                </div>
            </label>
        `;
    });
    container.innerHTML = html;
}

function populateKuesioners(kuesioners) {
    const container = document.getElementById('kuesionersContainer');
    if (kuesioners.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada kuesioner tersedia</p>';
        return;
    }

    let html = '';
    kuesioners.forEach(kuesioner => {
        html += `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="kuesioners[]" value="${kuesioner.id}" class="mr-3">
                <span class="text-sm font-medium text-gray-900">${kuesioner.judul}</span>
            </label>
        `;
    });
    container.innerHTML = html;
}

function populatePemeriksaans(pemeriksaans) {
    const container = document.getElementById('pemeriksaansContainer');
    if (pemeriksaans.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada pemeriksaan tersedia</p>';
        return;
    }

    let html = '';
    pemeriksaans.forEach(pemeriksaan => {
        const badgeColor = pemeriksaan.frekuensi === 'harian' ? 'bg-green-100 text-green-800' : 
                          pemeriksaan.frekuensi === 'mingguan' ? 'bg-blue-100 text-blue-800' : 
                          'bg-purple-100 text-purple-800';
        html += `
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="pemeriksaans[]" value="${pemeriksaan.id}" class="mr-3">
                <div class="flex-1">
                    <span class="text-sm font-medium text-gray-900">${pemeriksaan.nama}</span>
                    <span class="ml-2 px-2 py-0.5 ${badgeColor} rounded text-xs capitalize">${pemeriksaan.frekuensi}</span>
                </div>
            </label>
        `;
    });
    container.innerHTML = html;
}

// Error messages
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
