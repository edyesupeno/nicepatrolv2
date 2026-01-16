@extends('perusahaan.layouts.app')

@section('title', 'Manajemen Gaji Pokok')
@section('page-title', 'Manajemen Gaji Pokok')
@section('page-subtitle', 'Kelola gaji pokok karyawan berdasarkan jabatan')

@section('content')
<!-- Filters & Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('perusahaan.manajemen-gaji.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search text-gray-400 mr-1"></i>
                    Cari nama atau ID karyawan...
                </label>
                <input type="text" name="search" value="{{ $search }}" 
                    placeholder="Cari nama atau ID karyawan..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Project Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building text-gray-400 mr-1"></i>
                    Semua Project
                </label>
                <select name="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jabatan Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-briefcase text-gray-400 mr-1"></i>
                    Semua Jabatan
                </label>
                <select name="jabatan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ $jabatanId == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg font-medium hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
                <button type="button" onclick="showUpdateMassalModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">
                    <i class="fas fa-edit mr-2"></i>
                    Update Massal
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Karyawan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalKaryawan }} karyawan</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Gaji Pokok</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalGajiPokok, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Rata-rata Gaji</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($rataRataGaji, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Expand/Collapse All Button -->
@if($groupedKaryawans->count() > 0)
<div class="mb-4 flex justify-end">
    <button type="button" onclick="toggleAllProjects()" 
        class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition">
        <i class="fas fa-expand-alt mr-2"></i>
        <span id="toggleAllText">Buka Semua</span>
    </button>
</div>
@endif

<!-- Karyawan List by Project -->
@forelse($groupedKaryawans as $projectId => $karyawanList)
    @php
        $project = $karyawanList->first()->project;
        $projectTotal = $karyawanList->sum('gaji_pokok');
        $projectAvg = $karyawanList->count() > 0 ? $projectTotal / $karyawanList->count() : 0;
    @endphp
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 overflow-hidden">
        <!-- Project Header (Collapsible) -->
        <button type="button" 
            onclick="toggleProject('project-{{ $projectId }}')"
            class="w-full px-6 py-4 text-left hover:bg-gray-50 transition" 
            style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $project->nama }}</h3>
                        <p class="text-blue-100 text-sm">{{ $karyawanList->count() }} karyawan • Rata-rata: Rp {{ number_format($projectAvg, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-blue-100 text-sm">Total Gaji Pokok</p>
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($projectTotal, 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-white text-xl transition-transform duration-300" id="icon-project-{{ $projectId }}"></i>
                </div>
            </div>
        </button>

        <!-- Karyawan Table (Collapsible) -->
        <div id="project-{{ $projectId }}" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JABATAN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GAJI POKOK</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($karyawanList as $karyawan)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $karyawan->nik_karyawan }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($karyawan->foto)
                                            <img src="{{ asset('storage/' . $karyawan->foto) }}" 
                                                alt="{{ $karyawan->nama_lengkap }}"
                                                class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);">
                                                {{ strtoupper(substr($karyawan->nama_lengkap, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">{{ $karyawan->nik_karyawan }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-briefcase mr-1"></i>
                                        {{ $karyawan->jabatan->nama }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="inline-edit-container" data-karyawan-id="{{ $karyawan->hash_id }}">
                                        <div class="view-mode">
                                            <span class="gaji-value text-lg font-bold text-gray-900">
                                                Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="edit-mode hidden">
                                            <input type="text" 
                                                class="gaji-input w-48 px-3 py-2 border border-blue-500 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                                value="{{ $karyawan->gaji_pokok }}"
                                                data-original="{{ $karyawan->gaji_pokok }}">
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-edit-actions" data-karyawan-id="{{ $karyawan->hash_id }}">
                                        <div class="view-mode">
                                            <button onclick="enableEdit('{{ $karyawan->hash_id }}')" 
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </button>
                                        </div>
                                        <div class="edit-mode hidden flex items-center justify-center gap-2">
                                            <button onclick="saveEdit('{{ $karyawan->hash_id }}')" 
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                                <i class="fas fa-check mr-1"></i>
                                                Simpan
                                            </button>
                                            <button onclick="cancelEdit('{{ $karyawan->hash_id }}')" 
                                                class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-medium hover:bg-gray-600 transition">
                                                <i class="fas fa-times mr-1"></i>
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-gray-400 text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Data</h3>
        <p class="text-gray-600">Tidak ada karyawan yang sesuai dengan filter yang dipilih</p>
    </div>
@endforelse

<!-- Pagination -->
@if($karyawans->hasPages())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mt-6">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Menampilkan {{ $karyawans->firstItem() }} - {{ $karyawans->lastItem() }} dari {{ $karyawans->total() }} karyawan
            </div>
            <div>
                {{ $karyawans->links() }}
            </div>
        </div>
    </div>
@endif

<!-- Update Massal Modal -->
<div id="updateMassalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Update Gaji Pokok Massal</h3>
            <p class="text-sm text-gray-600 mt-1">Update gaji pokok untuk beberapa karyawan sekaligus</p>
        </div>
        
        <form id="updateMassalForm" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building text-gray-400 mr-1"></i>
                    Project (Opsional)
                </label>
                <select name="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-briefcase text-gray-400 mr-1"></i>
                    Jabatan (Opsional)
                </label>
                <select name="jabatan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                    Gaji Pokok Baru <span class="text-red-500">*</span>
                </label>
                <input type="text" id="gajiPokokMassal" name="gaji_pokok" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 5.000.000">
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Perhatian!</p>
                        <p>Gaji pokok akan diupdate untuk semua karyawan yang sesuai dengan filter yang dipilih.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeUpdateMassalModal()" 
                    class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 px-4 py-2 text-white rounded-lg font-medium hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-save mr-2"></i>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let allExpanded = false;

function toggleAllProjects() {
    const projectContents = document.querySelectorAll('[id^="project-"]');
    const projectIcons = document.querySelectorAll('[id^="icon-project-"]');
    const toggleText = document.getElementById('toggleAllText');
    const toggleButton = event.currentTarget;
    const icon = toggleButton.querySelector('i');
    
    allExpanded = !allExpanded;
    
    projectContents.forEach(content => {
        if (allExpanded) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
    
    projectIcons.forEach(icon => {
        if (allExpanded) {
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = 'rotate(0deg)';
        }
    });
    
    if (allExpanded) {
        toggleText.textContent = 'Tutup Semua';
        icon.className = 'fas fa-compress-alt mr-2';
    } else {
        toggleText.textContent = 'Buka Semua';
        icon.className = 'fas fa-expand-alt mr-2';
    }
}

function toggleProject(projectId) {
    const content = document.getElementById(projectId);
    const icon = document.getElementById('icon-' + projectId);
    
    if (content && icon) {
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
}

function enableEdit(karyawanId) {
    const container = document.querySelector(`.inline-edit-container[data-karyawan-id="${karyawanId}"]`);
    const actions = document.querySelector(`.inline-edit-actions[data-karyawan-id="${karyawanId}"]`);
    
    if (!container || !actions) return;
    
    const viewMode = container.querySelector('.view-mode');
    const editMode = container.querySelector('.edit-mode');
    const actionsView = actions.querySelector('.view-mode');
    const actionsEdit = actions.querySelector('.edit-mode');
    const input = container.querySelector('.gaji-input');
    
    if (!viewMode || !editMode || !actionsView || !actionsEdit || !input) return;
    
    viewMode.classList.add('hidden');
    editMode.classList.remove('hidden');
    actionsView.classList.add('hidden');
    actionsEdit.classList.remove('hidden');
    
    setTimeout(() => {
        input.focus();
        input.select();
    }, 100);
}

function cancelEdit(karyawanId) {
    const container = document.querySelector(`.inline-edit-container[data-karyawan-id="${karyawanId}"]`);
    const actions = document.querySelector(`.inline-edit-actions[data-karyawan-id="${karyawanId}"]`);
    
    if (!container || !actions) return;
    
    const viewMode = container.querySelector('.view-mode');
    const editMode = container.querySelector('.edit-mode');
    const actionsView = actions.querySelector('.view-mode');
    const actionsEdit = actions.querySelector('.edit-mode');
    const input = container.querySelector('.gaji-input');
    
    if (!viewMode || !editMode || !actionsView || !actionsEdit || !input) return;
    
    input.value = input.dataset.original;
    
    viewMode.classList.remove('hidden');
    editMode.classList.add('hidden');
    actionsView.classList.remove('hidden');
    actionsEdit.classList.add('hidden');
}

function saveEdit(karyawanId) {
    const container = document.querySelector(`.inline-edit-container[data-karyawan-id="${karyawanId}"]`);
    if (!container) return;
    
    const input = container.querySelector('.gaji-input');
    if (!input) return;
    
    const gajiPokok = input.value.replace(/\./g, '').replace(/,/g, '');
    
    if (!gajiPokok || parseInt(gajiPokok) < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gaji pokok tidak valid',
        });
        return;
    }
    
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    fetch(`/perusahaan/manajemen-gaji/${karyawanId}/update-gaji-pokok`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
        },
        body: JSON.stringify({ gaji_pokok: gajiPokok })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update display
            const gajiValue = container.querySelector('.gaji-value');
            if (gajiValue) {
                gajiValue.textContent = data.gaji_pokok_formatted;
            }
            input.dataset.original = data.gaji_pokok;
            
            // Switch back to view mode
            cancelEdit(karyawanId);
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Gagal update gaji pokok',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan data',
        });
    });
}

function showUpdateMassalModal() {
    const modal = document.getElementById('updateMassalModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeUpdateMassalModal() {
    const modal = document.getElementById('updateMassalModal');
    const form = document.getElementById('updateMassalForm');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    if (form) {
        form.reset();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const updateMassalForm = document.getElementById('updateMassalForm');
    const updateMassalModal = document.getElementById('updateMassalModal');
    
    if (updateMassalForm) {
        updateMassalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Remove separator if any
            if (data.gaji_pokok) {
                data.gaji_pokok = data.gaji_pokok.replace(/\./g, '').replace(/,/g, '');
            }
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin update gaji pokok secara massal?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3B82C8',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    
                    fetch('/perusahaan/manajemen-gaji/update-massal', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Gagal update gaji pokok',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data',
                        });
                    });
                }
            });
        });
    }
    
    if (updateMassalModal) {
        updateMassalModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeUpdateMassalModal();
            }
        });
    }
});
</script>
@endpush
