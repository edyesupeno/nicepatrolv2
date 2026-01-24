@extends('perusahaan.layouts.app')

@section('title', 'Edit Kru Change')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Edit Kru Change</h3>
            <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('perusahaan.kru-change.update', $kruChange->hash_id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror" required>
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $kruChange->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Area Selection -->
                <div>
                    <label for="area_patrol_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Area Patroli <span class="text-red-500">*</span>
                    </label>
                    <select name="area_patrol_id" id="area_patrol_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('area_patrol_id') border-red-500 @enderror" required>
                        <option value="{{ $kruChange->area_patrol_id }}" selected>{{ $kruChange->areaPatrol->nama }}</option>
                    </select>
                    @error('area_patrol_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tim Keluar -->
                <div>
                    <label for="tim_keluar_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tim Keluar <span class="text-red-500">*</span>
                    </label>
                    <select name="tim_keluar_id" id="tim_keluar_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('tim_keluar_id') border-red-500 @enderror" required>
                        <option value="{{ $kruChange->tim_keluar_id }}" selected>{{ $kruChange->timKeluar->nama_tim }} ({{ $kruChange->timKeluar->jenis_regu }})</option>
                    </select>
                    @error('tim_keluar_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tim Masuk -->
                <div>
                    <label for="tim_masuk_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tim Masuk <span class="text-red-500">*</span>
                    </label>
                    <select name="tim_masuk_id" id="tim_masuk_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('tim_masuk_id') border-red-500 @enderror" required>
                        <option value="{{ $kruChange->tim_masuk_id }}" selected>{{ $kruChange->timMasuk->nama_tim }} ({{ $kruChange->timMasuk->jenis_regu }})</option>
                    </select>
                    @error('tim_masuk_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Handover -->
                <div>
                    <label for="waktu_mulai_handover" class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Mulai Handover <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="waktu_mulai_handover" id="waktu_mulai_handover" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('waktu_mulai_handover') border-red-500 @enderror" 
                           value="{{ old('waktu_mulai_handover', $kruChange->waktu_mulai_handover->format('Y-m-d\TH:i')) }}" required>
                    @error('waktu_mulai_handover')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supervisor -->
                <div>
                    <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supervisor
                    </label>
                    <select name="supervisor_id" id="supervisor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('supervisor_id') border-red-500 @enderror">
                        @if($kruChange->supervisor_id)
                            <option value="{{ $kruChange->supervisor_id }}" selected>{{ $kruChange->supervisor->name }}</option>
                        @else
                            <option value="">Pilih Supervisor</option>
                        @endif
                    </select>
                    @error('supervisor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Petugas Keluar -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Petugas Keluar <span class="text-red-500">*</span>
                </label>
                <div id="petugas_keluar_container" class="border border-gray-300 rounded-lg p-3 bg-gray-50 min-h-[100px]">
                    @if($kruChange->petugas_keluar_ids && count($kruChange->petugas_keluar_ids) > 0)
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700 mb-2">Petugas Keluar Saat Ini:</p>
                            @foreach($kruChange->petugasKeluarWithRoles() as $anggota)
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" 
                                           name="petugas_keluar_ids[]" 
                                           value="{{ $anggota->user->id }}" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           checked>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $anggota->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $anggota->user->email }}</div>
                                        <div class="mt-1">{!! $anggota->role_badge !!}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Pilih tim keluar untuk memuat petugas</p>
                    @endif
                </div>
                @error('petugas_keluar_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Petugas Masuk -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Petugas Masuk
                </label>
                <div id="petugas_masuk_container" class="border border-gray-300 rounded-lg p-3 bg-gray-50 min-h-[100px]">
                    @if($kruChange->petugas_masuk_ids && count($kruChange->petugas_masuk_ids) > 0)
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700 mb-2">Petugas Masuk Saat Ini:</p>
                            @foreach($kruChange->petugasMasukWithRoles() as $anggota)
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded cursor-pointer">
                                    <input type="checkbox" 
                                           name="petugas_masuk_ids[]" 
                                           value="{{ $anggota->user->id }}" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           checked>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $anggota->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $anggota->user->email }}</div>
                                        <div class="mt-1">{!! $anggota->role_badge !!}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Pilih tim masuk untuk memuat petugas</p>
                    @endif
                </div>
                @error('petugas_masuk_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Keluar -->
            <div class="mt-6">
                <label for="catatan_keluar" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Tim Keluar
                </label>
                <textarea name="catatan_keluar" id="catatan_keluar" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('catatan_keluar') border-red-500 @enderror" 
                          placeholder="Catatan atau instruksi khusus dari tim keluar untuk handover...">{{ old('catatan_keluar', $kruChange->catatan_keluar) }}</textarea>
                @error('catatan_keluar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex space-x-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
                <a href="{{ route('perusahaan.kru-change.show', $kruChange->hash_id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection