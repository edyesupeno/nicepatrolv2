@extends('perusahaan.layouts.app')

@section('page-title', 'Tambah Kategori Perlengkapan')
@section('page-subtitle', 'Tambahkan kategori perlengkapan baru')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Form Tambah Kategori</h3>
                <p class="text-sm text-gray-600 mt-1">Isi form di bawah untuk menambahkan kategori perlengkapan baru</p>
            </div>
            <a href="{{ route('perusahaan.perlengkapan.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
    </div>

    <form action="{{ route('perusahaan.perlengkapan.store') }}" method="POST" id="kategoriForm">
        @csrf
        <div class="p-6 space-y-6">
            <!-- Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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

            <!-- Nama Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Perlengkapan Karyawan, Alat Safety, dll" required>
                @error('nama_kategori')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Kategori akan berisi berbagai item perlengkapan, seperti seragam, topi, kacamata, dll</p>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi kategori perlengkapan...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Kategori Aktif
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('perusahaan.perlengkapan.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-save mr-2"></i>Simpan Kategori
            </button>
        </div>
    </form>
</div>

<!-- Info Box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Tentang Kategori Perlengkapan</h3>
            <div class="mt-2 text-sm text-blue-700">
                <p>Setelah membuat kategori, Anda dapat menambahkan item-item perlengkapan ke dalam kategori tersebut. Contoh:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><strong>Perlengkapan Karyawan:</strong> Seragam, Topi, Kacamata, Sarung Tangan</li>
                    <li><strong>Alat Safety:</strong> Helm, Sepatu Safety, Rompi, Masker</li>
                    <li><strong>Peralatan Kantor:</strong> Laptop, Mouse, Keyboard, Monitor</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection