@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('perusahaan.tim-patroli.master') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Anggota Regu Patroli</h1>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600">{{ $timPatroli->nama_tim }}</p>
                <div class="flex items-center gap-4 mt-1">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-building mr-1"></i>{{ $timPatroli->project->nama ?? '-' }}
                    </span>
                    @if($timPatroli->shift)
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-clock mr-1"></i>{{ $timPatroli->shift->nama_shift }}
                        </span>
                    @endif
                    @if($timPatroli->leader)
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-user-tie mr-1"></i>Danru: {{ $timPatroli->leader->name }}
                        </span>
                    @endif
                </div>
            </div>
            <a 
                href="{{ route('perusahaan.tim-patroli.anggota.create', $timPatroli->hash_id) }}"
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center justify-center gap-2"
            >
                <i class="fas fa-user-plus"></i>
                Tambah Anggota
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <!-- Stats -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Anggota</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $anggota->total() }}</p>
                        </div>
                        <i class="fas fa-users text-2xl text-blue-400"></i>
                    </div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Anggota Aktif</p>
                            <p class="text-2xl font-bold text-green-900">{{ $timPatroli->anggotaAktif->count() }}</p>
                        </div>
                        <i class="fas fa-user-check text-2xl text-green-400"></i>
                    </div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600">Wakil Leader</p>
                            <p class="text-2xl font-bold text-purple-900">
                                {{ $timPatroli->anggotaAktif->where('role', 'wakil_leader')->count() }}
                            </p>
                        </div>
                        <i class="fas fa-user-tie text-2xl text-purple-400"></i>
                    </div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600">Nonaktif</p>
                            <p class="text-2xl font-bold text-red-900">
                                {{ $anggota->where('is_active', false)->count() }}
                            </p>
                        </div>
                        <i class="fas fa-user-times text-2xl text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Anggota</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($anggota as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $member->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $member->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {!! $member->role_badge !!}
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900">{{ $member->tanggal_bergabung->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($member->tanggal_keluar)
                                <p class="text-sm text-gray-900">{{ $member->tanggal_keluar->format('d M Y') }}</p>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {!! $member->status_badge !!}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a 
                                    href="{{ route('perusahaan.tim-patroli.anggota.show', [$timPatroli->hash_id, $member->hash_id]) }}"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                    title="Detail"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a 
                                    href="{{ route('perusahaan.tim-patroli.anggota.edit', [$timPatroli->hash_id, $member->hash_id]) }}"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($member->is_active)
                                    <button 
                                        onclick="nonaktifkanAnggota('{{ $member->hash_id }}')"
                                        class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition"
                                        title="Nonaktifkan"
                                    >
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                @else
                                    <button 
                                        onclick="aktifkanAnggota('{{ $member->hash_id }}')"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="Aktifkan"
                                    >
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                @endif
                                <button 
                                    onclick="deleteAnggota('{{ $member->hash_id }}')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                    title="Hapus"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada anggota tim</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($anggota->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $anggota->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Forms for Actions -->
<form id="nonaktifkanForm" method="POST" class="hidden">
    @csrf
    @method('PATCH')
</form>

<form id="aktifkanForm" method="POST" class="hidden">
    @csrf
    @method('PATCH')
</form>

<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function nonaktifkanAnggota(hashId) {
    Swal.fire({
        title: 'Nonaktifkan Anggota?',
        text: "Anggota akan dikeluarkan dari regu patroli",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Nonaktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('nonaktifkanForm');
            form.action = `{{ url('perusahaan/tim-patroli/' . $timPatroli->hash_id . '/anggota') }}/${hashId}/nonaktifkan`;
            form.submit();
        }
    });
}

function aktifkanAnggota(hashId) {
    Swal.fire({
        title: 'Aktifkan Anggota?',
        text: "Anggota akan diaktifkan kembali dalam regu patroli",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Aktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('aktifkanForm');
            form.action = `{{ url('perusahaan/tim-patroli/' . $timPatroli->hash_id . '/anggota') }}/${hashId}/aktifkan`;
            form.submit();
        }
    });
}

function deleteAnggota(hashId) {
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
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('perusahaan/tim-patroli/' . $timPatroli->hash_id . '/anggota') }}/${hashId}`;
            form.submit();
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