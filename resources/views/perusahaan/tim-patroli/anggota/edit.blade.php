@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Anggota Regu</h1>
        </div>
        <div>
            <p class="text-gray-600">{{ $timPatroli->nama_tim }}</p>
            <p class="text-sm text-gray-500 mt-1">Edit data: {{ $anggotaTimPatroli->user->name }}</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('perusahaan.tim-patroli.anggota.update', [$timPatroli->hash_id, $anggotaTimPatroli->hash_id]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-edit text-blue-600"></i>
                        Informasi Anggota
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- User Info (Read Only) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Karyawan
                            </label>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($anggotaTimPatroli->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $anggotaTimPatroli->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $anggotaTimPatroli->user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Role dalam Tim <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="role" value="anggota" class="mr-3" {{ old('role', $anggotaTimPatroli->role) == 'anggota' ? 'checked' : '' }} required>
                                    <div>
                                        <p class="font-semibold text-gray-900">Anggota</p>
                                        <p class="text-xs text-gray-500">Anggota biasa dalam tim patroli</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="role" value="wakil_leader" class="mr-3" {{ old('role', $anggotaTimPatroli->role) == 'wakil_leader' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-semibold text-gray-900">Wakil Leader</p>
                                        <p class="text-xs text-gray-500">Wakil dari leader tim, membantu koordinasi</p>
                                    </div>
                                </label>
                            </div>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Bergabung -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Bergabung <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_bergabung" 
                                id="tanggal_bergabung"
                                value="{{ old('tanggal_bergabung', $anggotaTimPatroli->tanggal_bergabung->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('tanggal_bergabung')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Keluar -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Keluar
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_keluar" 
                                id="tanggal_keluar"
                                value="{{ old('tanggal_keluar', $anggotaTimPatroli->tanggal_keluar?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('tanggal_keluar')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika anggota masih aktif</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="is_active" value="1" class="mr-3" {{ old('is_active', $anggotaTimPatroli->is_active ? '1' : '0') == '1' ? 'checked' : '' }} required>
                                    <div>
                                        <p class="font-semibold text-gray-900">Aktif</p>
                                        <p class="text-xs text-gray-500">Anggota aktif dalam tim patroli</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="is_active" value="0" class="mr-3" {{ old('is_active', $anggotaTimPatroli->is_active ? '1' : '0') == '0' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-semibold text-gray-900">Nonaktif</p>
                                        <p class="text-xs text-gray-500">Anggota tidak aktif dalam tim patroli</p>
                                    </div>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea 
                                name="catatan" 
                                id="catatan"
                                rows="3"
                                placeholder="Catatan tambahan tentang anggota ini..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >{{ old('catatan', $anggotaTimPatroli->catatan) }}</textarea>
                            @error('catatan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Info Tim</h3>
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm font-semibold text-blue-900">{{ $timPatroli->nama_tim }}</p>
                            <p class="text-xs text-blue-600 mt-1">{{ $timPatroli->project->nama ?? '-' }}</p>
                        </div>

                        @if($timPatroli->leader)
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <p class="text-xs font-semibold text-purple-600 uppercase">Leader</p>
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
                            <p class="text-xs font-semibold text-gray-600 uppercase">Anggota Saat Ini</p>
                            <p class="text-sm text-gray-900">{{ $timPatroli->anggotaAktif->count() }} orang</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-lg font-semibold hover:from-green-700 hover:to-green-900 transition inline-flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-save"></i>
                            Update Anggota
                        </button>
                        <a 
                            href="{{ route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id) }}"
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