@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Detail Anggota Tim</h1>
        </div>
        <div>
            <p class="text-gray-600">{{ $timPatroli->nama_tim }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Anggota -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i>
                    Informasi Anggota
                </h3>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($anggotaTimPatroli->user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $anggotaTimPatroli->user->name }}</h4>
                        <p class="text-gray-600">{{ $anggotaTimPatroli->user->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Role</label>
                        <div>{!! $anggotaTimPatroli->role_badge !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
                        <div>{!! $anggotaTimPatroli->status_badge !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Bergabung</label>
                        <p class="text-gray-900">{{ $anggotaTimPatroli->tanggal_bergabung->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Keluar</label>
                        <p class="text-gray-900">
                            @if($anggotaTimPatroli->tanggal_keluar)
                                {{ $anggotaTimPatroli->tanggal_keluar->format('d M Y') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($anggotaTimPatroli->catatan)
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Catatan</label>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-gray-900">{{ $anggotaTimPatroli->catatan }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Riwayat Aktivitas -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i>
                    Riwayat Aktivitas
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Bergabung dengan Regu</p>
                            <p class="text-sm text-gray-600">{{ $anggotaTimPatroli->tanggal_bergabung->format('d M Y') }}</p>
                        </div>
                    </div>

                    @if($anggotaTimPatroli->tanggal_keluar)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-minus text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Keluar dari Regu</p>
                                <p class="text-sm text-gray-600">{{ $anggotaTimPatroli->tanggal_keluar->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Terakhir Diupdate</p>
                            <p class="text-sm text-gray-600">{{ $anggotaTimPatroli->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Info Tim -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Info Regu</h3>
                
                <div class="space-y-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm font-semibold text-blue-900">{{ $timPatroli->nama_tim }}</p>
                        <p class="text-xs text-blue-600 mt-1">{{ $timPatroli->project->nama ?? '-' }}</p>
                    </div>

                    @if($timPatroli->leader)
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <p class="text-xs font-semibold text-purple-600 uppercase">Danru</p>
                            <p class="text-sm text-purple-900">{{ $timPatroli->leader->name }}</p>
                        </div>
                    @endif

                    @if($timPatroli->shift)
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-xs font-semibold text-green-600 uppercase">Shift</p>
                            <p class="text-sm text-green-900">{{ $timPatroli->shift->nama_shift }}</p>
                            <p class="text-xs text-green-600">{{ $timPatroli->shift->jam_mulai }} - {{ $timPatroli->shift->jam_selesai }}</p>
                        </div>
                    @endif

                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-600 uppercase">Total Anggota</p>
                        <p class="text-sm text-gray-900">{{ $timPatroli->anggotaAktif->count() }} orang aktif</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                
                <div class="space-y-3">
                    <a 
                        href="{{ route('perusahaan.tim-patroli.anggota.edit', [$timPatroli->hash_id, $anggotaTimPatroli->hash_id]) }}"
                        class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition inline-flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-edit"></i>
                        Edit Anggota
                    </a>

                    @if($anggotaTimPatroli->is_active)
                        <button 
                            onclick="nonaktifkanAnggota()"
                            class="w-full px-4 py-2.5 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition inline-flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-user-slash"></i>
                            Nonaktifkan
                        </button>
                    @else
                        <button 
                            onclick="aktifkanAnggota()"
                            class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition inline-flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-user-check"></i>
                            Aktifkan
                        </button>
                    @endif

                    <button 
                        onclick="deleteAnggota()"
                        class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition inline-flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-trash"></i>
                        Hapus Anggota
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forms for Actions -->
<form id="nonaktifkanForm" method="POST" action="{{ route('perusahaan.tim-patroli.anggota.nonaktifkan', [$timPatroli->hash_id, $anggotaTimPatroli->hash_id]) }}" class="hidden">
    @csrf
    @method('PATCH')
</form>

<form id="aktifkanForm" method="POST" action="{{ route('perusahaan.tim-patroli.anggota.aktifkan', [$timPatroli->hash_id, $anggotaTimPatroli->hash_id]) }}" class="hidden">
    @csrf
    @method('PATCH')
</form>

<form id="deleteForm" method="POST" action="{{ route('perusahaan.tim-patroli.anggota.destroy', [$timPatroli->hash_id, $anggotaTimPatroli->hash_id]) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function nonaktifkanAnggota() {
    Swal.fire({
        title: 'Nonaktifkan Anggota?',
        text: "Anggota akan dikeluarkan dari tim patroli",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('nonaktifkanForm').submit();
        }
    });
}

function aktifkanAnggota() {
    Swal.fire({
        title: 'Aktifkan Anggota?',
        text: "Anggota akan diaktifkan kembali dalam tim patroli",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Aktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('aktifkanForm').submit();
        }
    });
}

function deleteAnggota() {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data anggota akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
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
</script>
@endpush
@endsection