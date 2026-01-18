@extends('perusahaan.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Anggota Regu</h1>
        </div>
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
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('perusahaan.tim-patroli.anggota.store', $timPatroli->hash_id) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-plus text-blue-600"></i>
                        Informasi Anggota
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- User Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Karyawan <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="user_id" 
                                id="user_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="">Pilih Karyawan</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @if($availableUsers->isEmpty())
                                <p class="text-amber-600 text-xs mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Tidak ada security officer yang tersedia. Semua sudah menjadi anggota atau danru regu patroli aktif.
                                </p>
                            @else
                                <p class="text-blue-600 text-xs mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Menampilkan security officer yang belum menjadi anggota atau danru regu patroli lain
                                </p>
                            @endif
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Role dalam Regu <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="role" value="anggota" class="mr-3" {{ old('role', 'anggota') == 'anggota' ? 'checked' : '' }} required>
                                    <div>
                                        <p class="font-semibold text-gray-900">Anggota</p>
                                        <p class="text-xs text-gray-500">Anggota biasa dalam regu patroli</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="role" value="wakil_leader" class="mr-3" {{ old('role') == 'wakil_leader' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-semibold text-gray-900">Wakil Danru</p>
                                        <p class="text-xs text-gray-500">Wakil dari Danru, membantu koordinasi</p>
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
                                value="{{ old('tanggal_bergabung', date('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('tanggal_bergabung')
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
                            >{{ old('catatan') }}</textarea>
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
                            <p class="text-xs font-semibold text-gray-600 uppercase">Anggota Saat Ini</p>
                            <p class="text-sm text-gray-900">{{ $timPatroli->anggotaAktif->count() }} orang</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-flex items-center justify-center gap-2"
                            @if($availableUsers->isEmpty()) disabled @endif
                        >
                            <i class="fas fa-user-plus"></i>
                            Tambah Anggota
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