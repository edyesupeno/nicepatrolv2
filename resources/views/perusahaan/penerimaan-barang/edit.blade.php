@extends('perusahaan.layouts.app')

@section('title', 'Edit Penerimaan Barang')
@section('page-title', 'Edit Penerimaan Barang')
@section('page-subtitle', 'Perbarui data penerimaan barang')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.penerimaan-barang.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-box mr-2"></i>
                    Penerimaan Barang
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('perusahaan.penerimaan-barang.show', $penerimaanBarang->hash_id) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                        {{ $penerimaanBarang->nomor_penerimaan }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Edit Penerimaan Barang</h2>
                        <p class="text-sm text-gray-600">{{ $penerimaanBarang->nomor_penerimaan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('perusahaan.penerimaan-barang.update', $penerimaanBarang->hash_id) }}" method="POST" enctype="multipart/form-data" id="formEditPenerimaanBarang">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-8">
                
                <!-- Detail Barang Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-900">Detail Barang</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Nama Barang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nama_barang" 
                                required
                                value="{{ old('nama_barang', $penerimaanBarang->nama_barang) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Contoh: Paket Dokumen A1"
                            >
                            @error('nama_barang')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori Barang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori Barang <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-wrap gap-3">
                                <label class="flex items-center">
                                    <input type="radio" name="kategori_barang" value="Dokumen" class="mr-2" {{ old('kategori_barang', $penerimaanBarang->kategori_barang) == 'Dokumen' ? 'checked' : '' }}>
                                    <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium cursor-pointer hover:bg-blue-200 transition">
                                        Dokumen
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kategori_barang" value="Material" class="mr-2" {{ old('kategori_barang', $penerimaanBarang->kategori_barang) == 'Material' ? 'checked' : '' }}>
                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium cursor-pointer hover:bg-green-200 transition">
                                        Material
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kategori_barang" value="Elektronik" class="mr-2" {{ old('kategori_barang', $penerimaanBarang->kategori_barang) == 'Elektronik' ? 'checked' : '' }}>
                                    <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-medium cursor-pointer hover:bg-purple-200 transition">
                                        Elektronik
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kategori_barang" value="Logistik" class="mr-2" {{ old('kategori_barang', $penerimaanBarang->kategori_barang) == 'Logistik' ? 'checked' : '' }}>
                                    <span class="px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-sm font-medium cursor-pointer hover:bg-orange-200 transition">
                                        Logistik
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kategori_barang" value="Lainnya" class="mr-2" {{ old('kategori_barang', $penerimaanBarang->kategori_barang) == 'Lainnya' ? 'checked' : '' }}>
                                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium cursor-pointer hover:bg-gray-200 transition">
                                        Lainnya
                                    </span>
                                </label>
                            </div>
                            @error('kategori_barang')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah Barang & Satuan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Barang <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="jumlah_barang" 
                                    required
                                    min="1"
                                    value="{{ old('jumlah_barang', $penerimaanBarang->jumlah_barang) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="0"
                                >
                                @error('jumlah_barang')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        name="satuan"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                    >
                                        <option value="">Unit</option>
                                        <option value="Pcs" {{ old('satuan', $penerimaanBarang->satuan) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="Box" {{ old('satuan', $penerimaanBarang->satuan) == 'Box' ? 'selected' : '' }}>Box</option>
                                        <option value="Kg" {{ old('satuan', $penerimaanBarang->satuan) == 'Kg' ? 'selected' : '' }}>Kg</option>
                                        <option value="Liter" {{ old('satuan', $penerimaanBarang->satuan) == 'Liter' ? 'selected' : '' }}>Liter</option>
                                        <option value="Meter" {{ old('satuan', $penerimaanBarang->satuan) == 'Meter' ? 'selected' : '' }}>Meter</option>
                                        <option value="Set" {{ old('satuan', $penerimaanBarang->satuan) == 'Set' ? 'selected' : '' }}>Set</option>
                                        <option value="Unit" {{ old('satuan', $penerimaanBarang->satuan) == 'Unit' ? 'selected' : '' }}>Unit</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                @error('satuan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kondisi & Asal Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-900">Kondisi & Asal</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Kondisi Barang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Kondisi Barang <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition kondisi-option {{ old('kondisi_barang', $penerimaanBarang->kondisi_barang) == 'Baik' ? 'border-blue-500 bg-blue-50' : '' }}">
                                    <input type="radio" name="kondisi_barang" value="Baik" class="hidden" {{ old('kondisi_barang', $penerimaanBarang->kondisi_barang) == 'Baik' ? 'checked' : '' }}>
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                                        <i class="fas fa-check text-green-600 text-xl"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Baik</span>
                                </label>
                                
                                <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition kondisi-option {{ old('kondisi_barang', $penerimaanBarang->kondisi_barang) == 'Rusak' ? 'border-blue-500 bg-blue-50' : '' }}">
                                    <input type="radio" name="kondisi_barang" value="Rusak" class="hidden" {{ old('kondisi_barang', $penerimaanBarang->kondisi_barang) == 'Rusak' ? 'checked' : '' }}>
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-2">
                                        <i class="fas fa-times text-red-600 text-xl"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Rusak</span>
                                </label>
                                
                                <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition kondisi-option {{ old('kondisi_barang', $penerimaanBarang->kondisi_barang) == 'Segel Terbuka' ? 'border-blue-500 bg-blue-50' : '' }}">
                                    <input type="radio" name="kondisi_barang" value="Segel Terbuka" class="hidden" {{ old('kondisi_barang', $penerimaanBarang->kondisi_barang) == 'Segel Terbuka' ? 'checked' : '' }}>
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-2">
                                        <i class="fas fa-lock-open text-yellow-600 text-xl"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Segel Terbuka</span>
                                </label>
                            </div>
                            @error('kondisi_barang')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Pengirim <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center px-6 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition pengirim-option {{ old('pengirim', $penerimaanBarang->pengirim) == 'Kurir' ? 'border-blue-500 bg-blue-50 selected' : '' }}">
                                    <input type="radio" name="pengirim" value="Kurir" class="mr-3" {{ old('pengirim', $penerimaanBarang->pengirim) == 'Kurir' ? 'checked' : '' }}>
                                    <span class="text-sm font-medium {{ old('pengirim', $penerimaanBarang->pengirim) == 'Kurir' ? 'text-blue-700' : 'text-gray-700' }}">Kurir</span>
                                </label>
                                
                                <label class="flex items-center px-6 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition pengirim-option {{ old('pengirim', $penerimaanBarang->pengirim) == 'Client' ? 'border-blue-500 bg-blue-50 selected' : '' }}">
                                    <input type="radio" name="pengirim" value="Client" class="mr-3" {{ old('pengirim', $penerimaanBarang->pengirim) == 'Client' ? 'checked' : '' }}>
                                    <span class="text-sm font-medium {{ old('pengirim', $penerimaanBarang->pengirim) == 'Client' ? 'text-blue-700' : 'text-gray-700' }}">Client</span>
                                </label>
                                
                                <label class="flex items-center px-6 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition pengirim-option {{ old('pengirim', $penerimaanBarang->pengirim) == 'Lainnya' ? 'border-blue-500 bg-blue-50 selected' : '' }}">
                                    <input type="radio" name="pengirim" value="Lainnya" class="mr-3" {{ old('pengirim', $penerimaanBarang->pengirim) == 'Lainnya' ? 'checked' : '' }}>
                                    <span class="text-sm font-medium {{ old('pengirim', $penerimaanBarang->pengirim) == 'Lainnya' ? 'text-blue-700' : 'text-gray-700' }}">Lainnya</span>
                                </label>
                            </div>
                            @error('pengirim')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tujuan Barang (Departemen) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan Barang (Departemen) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="tujuan_departemen" 
                                required
                                value="{{ old('tujuan_departemen', $penerimaanBarang->tujuan_departemen) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Cth: HRD, Finance, IT"
                            >
                            @error('tujuan_departemen')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Keterangan
                            </label>
                            <textarea 
                                name="keterangan" 
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Keterangan tambahan (opsional)"
                            >{{ old('keterangan', $penerimaanBarang->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bukti Fisik Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-900">Bukti Fisik</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Foto Barang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Barang
                            </label>
                            
                            @if($penerimaanBarang->foto_barang)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                                <img src="{{ Storage::url($penerimaanBarang->foto_barang) }}" alt="Foto Barang" class="w-32 h-24 object-cover rounded-lg border">
                            </div>
                            @endif
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        name="foto_barang" 
                                        id="foto_barang"
                                        accept="image/jpeg,image/png,image/jpg"
                                        class="hidden"
                                        onchange="previewFotoBarang(this)"
                                    >
                                    <label for="foto_barang" class="cursor-pointer flex items-center justify-center w-full px-4 py-8 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-center">
                                        <div class="text-center">
                                            <i class="fas fa-camera text-gray-400 text-3xl mb-3"></i>
                                            <p class="text-sm text-gray-600 font-medium" id="foto_barang_label">{{ $penerimaanBarang->foto_barang ? 'Ganti Foto' : 'Pilih File' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimal 5MB</p>
                                        </div>
                                    </label>
                                    <div class="mt-4 hidden" id="foto_barang_preview">
                                        <img class="w-32 h-24 object-cover rounded-lg border mx-auto" alt="Preview Barang">
                                        <p class="text-xs text-green-600 mt-2 text-center">Preview foto baru</p>
                                    </div>
                                </div>
                            </div>
                            @error('foto_barang')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Area Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-900">Project & Area</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Project -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Project
                            </label>
                            <div class="relative">
                                <select 
                                    name="project_id" 
                                    id="project_select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                    onchange="loadAreas()"
                                >
                                    <option value="">Pilih Project (Opsional)</option>
                                    @forelse($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', $penerimaanBarang->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->nama }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Belum ada project tersedia</option>
                                    @endforelse
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Project terkait dengan barang yang diterima</p>
                            @error('project_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Area
                            </label>
                            <div class="relative">
                                <select 
                                    name="area_id" 
                                    id="area_select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                >
                                    <option value="">Loading...</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Area tempat barang akan disimpan atau ditempatkan</p>
                            @error('area_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- POS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                POS JAGA
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    name="pos" 
                                    id="pos_input"
                                    value="{{ old('pos', $penerimaanBarang->pos) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ketik untuk mencari atau tambah POS baru..."
                                    autocomplete="off"
                                >
                                <!-- Dropdown suggestions -->
                                <div id="pos_suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-48 overflow-y-auto">
                                    <!-- Suggestions will be populated here -->
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Lokasi spesifik penyimpanan barang (opsional). Ketik untuk mencari atau tambah baru.</p>
                            @error('pos')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('perusahaan.penerimaan-barang.show', $penerimaanBarang->hash_id) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    
                    <button 
                        type="submit" 
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center"
                    >
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle kategori barang selection
    const kategoriInputs = document.querySelectorAll('input[name="kategori_barang"]');
    kategoriInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Remove selected class from all labels
            document.querySelectorAll('input[name="kategori_barang"]').forEach(radio => {
                radio.parentElement.classList.remove('selected');
                radio.parentElement.querySelector('span').classList.remove('ring-2', 'ring-blue-500');
            });
            
            // Add selected class to current label
            if (this.checked) {
                this.parentElement.classList.add('selected');
                this.parentElement.querySelector('span').classList.add('ring-2', 'ring-blue-500');
            }
        });
    });

    // Handle kondisi barang selection
    const kondisiOptions = document.querySelectorAll('.kondisi-option');
    kondisiOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            kondisiOptions.forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50');
                opt.querySelector('input').checked = false;
            });
            
            // Add selected class to current option
            this.classList.add('border-blue-500', 'bg-blue-50');
            this.querySelector('input').checked = true;
        });
    });

    // Handle pengirim selection
    const pengirimOptions = document.querySelectorAll('.pengirim-option');
    pengirimOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            pengirimOptions.forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50', 'selected');
                opt.querySelector('span').classList.remove('text-blue-700');
                opt.querySelector('span').classList.add('text-gray-700');
                opt.querySelector('input').checked = false;
            });
            
            // Add selected class to current option
            this.classList.add('border-blue-500', 'bg-blue-50', 'selected');
            this.querySelector('span').classList.remove('text-gray-700');
            this.querySelector('span').classList.add('text-blue-700');
            this.querySelector('input').checked = true;
        });
    });
});

function previewFotoBarang(input) {
    const preview = document.getElementById('foto_barang_preview');
    const label = document.getElementById('foto_barang_label');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
            label.textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
        label.textContent = 'Ganti Foto';
    }
}

// Form validation and submission
document.getElementById('formEditPenerimaanBarang').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    const requiredFields = this.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    // Check radio buttons
    const kategoriChecked = document.querySelector('input[name="kategori_barang"]:checked');
    const kondisiChecked = document.querySelector('input[name="kondisi_barang"]:checked');
    const pengirimChecked = document.querySelector('input[name="pengirim"]:checked');
    
    if (!kategoriChecked || !kondisiChecked || !pengirimChecked) {
        isValid = false;
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Silakan lengkapi semua field yang wajib diisi'
        });
        return;
    }
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Silakan lengkapi semua field yang wajib diisi'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan Perubahan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit form via AJAX
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
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
                window.location.href = '{{ route("perusahaan.penerimaan-barang.show", $penerimaanBarang->hash_id) }}';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan saat menyimpan data'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan data'
        });
    });
});

// Load areas based on selected project
function loadAreas() {
    const projectSelect = document.getElementById('project_select');
    const areaSelect = document.getElementById('area_select');
    const projectId = projectSelect.value;
    const currentAreaId = {{ $penerimaanBarang->area_id ?? 'null' }};
    
    // Reset area select
    areaSelect.innerHTML = '<option value="">Loading...</option>';
    areaSelect.disabled = true;
    
    if (!projectId) {
        areaSelect.innerHTML = '<option value="">Pilih project terlebih dahulu</option>';
        return;
    }
    
    // Fetch areas for selected project
    const baseUrl = '{{ url("/") }}';
    fetch(`${baseUrl}/perusahaan/penerimaan-barang-areas/${projectId}`)
        .then(response => response.json())
        .then(areas => {
            areaSelect.innerHTML = '<option value="">Pilih Area (Opsional)</option>';
            
            if (areas.length > 0) {
                areas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.nama + (area.alamat ? ' - ' + area.alamat : '');
                    
                    // Select current area if it matches
                    if (area.id == currentAreaId) {
                        option.selected = true;
                    }
                    
                    areaSelect.appendChild(option);
                });
                areaSelect.disabled = false;
            } else {
                areaSelect.innerHTML = '<option value="">Tidak ada area tersedia</option>';
            }
        })
        .catch(error => {
            console.error('Error loading areas:', error);
            areaSelect.innerHTML = '<option value="">Error loading areas</option>';
        });
}

// Load areas on page load if project is already selected
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_select');
    if (projectSelect.value) {
        loadAreas();
    }
    
    // Initialize POS search
    initializePosSearch();
});

// POS Search functionality
function initializePosSearch() {
    const posInput = document.getElementById('pos_input');
    const posSuggestions = document.getElementById('pos_suggestions');
    let searchTimeout;
    
    posInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            posSuggestions.classList.add('hidden');
            return;
        }
        
        // Debounce search
        searchTimeout = setTimeout(() => {
            searchPos(query);
        }, 300);
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!posInput.contains(e.target) && !posSuggestions.contains(e.target)) {
            posSuggestions.classList.add('hidden');
        }
    });
    
    // Show suggestions when focusing on input
    posInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            searchPos(this.value.trim());
        }
    });
}

function searchPos(query) {
    const posSuggestions = document.getElementById('pos_suggestions');
    const baseUrl = '{{ url("/") }}';
    
    fetch(`${baseUrl}/perusahaan/penerimaan-barang-search-pos?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(suggestions => {
            posSuggestions.innerHTML = '';
            
            if (suggestions.length > 0) {
                suggestions.forEach(pos => {
                    const item = document.createElement('div');
                    item.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                    item.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            <span class="text-sm text-gray-900">${pos}</span>
                        </div>
                    `;
                    
                    item.addEventListener('click', function() {
                        document.getElementById('pos_input').value = pos;
                        posSuggestions.classList.add('hidden');
                    });
                    
                    posSuggestions.appendChild(item);
                });
                
                // Add "Add new" option
                const addNewItem = document.createElement('div');
                addNewItem.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer border-t border-gray-200 bg-gray-50';
                addNewItem.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-plus text-blue-600 mr-2"></i>
                        <span class="text-sm text-blue-600 font-medium">Tambah "${query}" sebagai POS baru</span>
                    </div>
                `;
                
                addNewItem.addEventListener('click', function() {
                    document.getElementById('pos_input').value = query;
                    posSuggestions.classList.add('hidden');
                });
                
                posSuggestions.appendChild(addNewItem);
                
            } else {
                // No suggestions found, show "Add new" option
                const noResultsItem = document.createElement('div');
                noResultsItem.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer bg-gray-50';
                noResultsItem.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-plus text-blue-600 mr-2"></i>
                        <span class="text-sm text-blue-600 font-medium">Tambah "${query}" sebagai POS baru</span>
                    </div>
                `;
                
                noResultsItem.addEventListener('click', function() {
                    document.getElementById('pos_input').value = query;
                    posSuggestions.classList.add('hidden');
                });
                
                posSuggestions.appendChild(noResultsItem);
            }
            
            posSuggestions.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error searching POS:', error);
            posSuggestions.classList.add('hidden');
        });
}
</script>

<style>
/* Custom radio button styling */
input[type="radio"] {
    accent-color: #3B82F6;
}

/* Kategori barang styling */
input[name="kategori_barang"]:checked + span {
    transform: scale(1.05);
}

/* Kondisi barang styling */
.kondisi-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.kondisi-option.selected {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

/* Pengirim styling */
.pengirim-option {
    transition: all 0.2s ease;
}

.pengirim-option:hover {
    transform: translateY(-1px);
}

/* File input styling */
input[type="file"] + label {
    transition: all 0.2s ease;
}

input[type="file"] + label:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Form sections */
.bg-gray-50 {
    transition: all 0.2s ease;
}

.bg-gray-50:hover {
    background-color: #f8fafc;
}

/* POS Suggestions styling */
#pos_suggestions {
    z-index: 1000;
}

#pos_suggestions::-webkit-scrollbar {
    width: 6px;
}

#pos_suggestions::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#pos_suggestions::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#pos_suggestions::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush