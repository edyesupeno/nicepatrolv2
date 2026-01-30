@extends('perusahaan.layouts.app')

@section('page-title', 'Buat Jadwal Penyerahan')
@section('page-subtitle', 'Buat jadwal penyerahan perlengkapan baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Penyerahan
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Form Jadwal Penyerahan</h3>
        <p class="text-sm text-gray-500 mt-1">Buat jadwal penyerahan terlebih dahulu. Pemilihan karyawan dan item akan dilakukan setelah jadwal dibuat.</p>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('perusahaan.penyerahan-perlengkapan.store') }}">
            @csrf
            
            <!-- Basic Information -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_mulai')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+7 days'))) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_selesai')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan jadwal penyerahan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Langkah Selanjutnya</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Setelah jadwal dibuat, Anda dapat:</p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>Menambahkan karyawan yang akan menerima perlengkapan</li>
                                    <li>Memilih item perlengkapan yang akan diserahkan</li>
                                    <li>Mengatur jumlah dan detail penyerahan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 mt-8">
                <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Buat Jadwal</button>
            </div>
        </form>
    </div>
</div>

@endsection