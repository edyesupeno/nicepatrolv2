@extends('perusahaan.layouts.app')

@section('title', 'Tambah Aset Kendaraan')

@section('content')
<div class="mb-6">
    <a href="{{ route('perusahaan.aset-kendaraan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Aset Kendaraan
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-xl font-semibold text-gray-900">Tambah Aset Kendaraan</h1>
        <p class="text-gray-600 mt-1">Isi form di bawah untuk menambah data kendaraan baru</p>
    </div>
    
    <form action="{{ route('perusahaan.aset-kendaraan.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button type="button" onclick="showTab('basic')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600" id="tab-basic">
                    Informasi Dasar
                </button>
                <button type="button" onclick="showTab('documents')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-documents">
                    Dokumen Kendaraan
                </button>
                <button type="button" onclick="showTab('operational')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-operational">
                    Operasional
                </button>
                <button type="button" onclick="showTab('files')" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-files">
                    File & Foto
                </button>
            </nav>
        </div>

        <!-- Tab Content: Informasi Dasar -->
        <div id="content-basic" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select name="project_id" id="project_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_kendaraan" id="jenis_kendaraan" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kendaraan') border-red-500 @enderror">
                            <option value="">Pilih Jenis</option>
                            @foreach($jenisOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('jenis_kendaraan') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_kendaraan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="merk" class="block text-sm font-medium text-gray-700 mb-2">
                                Merk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="merk" id="merk" value="{{ old('merk') }}" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('merk') border-red-500 @enderror"
                                   placeholder="Contoh: Toyota, Honda">
                            @error('merk')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                Model <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('model') border-red-500 @enderror"
                                   placeholder="Contoh: Avanza, Beat">
                            @error('model')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Pembuatan <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan') }}" required 
                                   min="1900" max="{{ date('Y') + 1 }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tahun_pembuatan') border-red-500 @enderror"
                                   placeholder="{{ date('Y') }}">
                            @error('tahun_pembuatan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="warna" class="block text-sm font-medium text-gray-700 mb-2">
                                Warna <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="warna" id="warna" value="{{ old('warna') }}" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('warna') border-red-500 @enderror"
                                   placeholder="Contoh: Putih, Hitam">
                            @error('warna')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label for="nomor_polisi" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Polisi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_polisi" id="nomor_polisi" value="{{ old('nomor_polisi') }}" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_polisi') border-red-500 @enderror"
                               placeholder="Contoh: B 1234 ABC"
                               style="text-transform: uppercase;">
                        @error('nomor_polisi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nomor_rangka" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Rangka <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_rangka" id="nomor_rangka" value="{{ old('nomor_rangka') }}" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_rangka') border-red-500 @enderror"
                               placeholder="Nomor rangka kendaraan">
                        @error('nomor_rangka')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nomor_mesin" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Mesin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_mesin" id="nomor_mesin" value="{{ old('nomor_mesin') }}" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_mesin') border-red-500 @enderror"
                               placeholder="Nomor mesin kendaraan">
                        @error('nomor_mesin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pembelian <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" value="{{ old('tanggal_pembelian') }}" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_pembelian') border-red-500 @enderror">
                            @error('tanggal_pembelian')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status_kendaraan" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status_kendaraan" id="status_kendaraan" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status_kendaraan') border-red-500 @enderror">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_kendaraan', 'aktif') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_kendaraan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="harga_pembelian" class="block text-sm font-medium text-gray-700 mb-2">
                                Harga Pembelian <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="number" name="harga_pembelian" id="harga_pembelian" value="{{ old('harga_pembelian') }}" required min="0" step="0.01"
                                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('harga_pembelian') border-red-500 @enderror"
                                       placeholder="0">
                            </div>
                            @error('harga_pembelian')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nilai_penyusutan" class="block text-sm font-medium text-gray-700 mb-2">
                                Nilai Penyusutan
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="number" name="nilai_penyusutan" id="nilai_penyusutan" value="{{ old('nilai_penyusutan', 0) }}" min="0" step="0.01"
                                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nilai_penyusutan') border-red-500 @enderror"
                                       placeholder="0">
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Kosongkan jika belum ada penyusutan</p>
                            @error('nilai_penyusutan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Dokumen Kendaraan -->
        <div id="content-documents" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: STNK & BPKB -->
                <div class="space-y-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900 mb-4">STNK (Surat Tanda Nomor Kendaraan)</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nomor_stnk" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor STNK <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nomor_stnk" id="nomor_stnk" value="{{ old('nomor_stnk') }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_stnk') border-red-500 @enderror">
                                @error('nomor_stnk')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_berlaku_stnk" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Berlaku STNK <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_berlaku_stnk" id="tanggal_berlaku_stnk" value="{{ old('tanggal_berlaku_stnk') }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_berlaku_stnk') border-red-500 @enderror">
                                @error('tanggal_berlaku_stnk')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-green-900 mb-4">BPKB (Buku Pemilik Kendaraan Bermotor)</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nomor_bpkb" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor BPKB <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nomor_bpkb" id="nomor_bpkb" value="{{ old('nomor_bpkb') }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_bpkb') border-red-500 @enderror">
                                @error('nomor_bpkb')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="atas_nama_bpkb" class="block text-sm font-medium text-gray-700 mb-2">
                                    Atas Nama BPKB <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="atas_nama_bpkb" id="atas_nama_bpkb" value="{{ old('atas_nama_bpkb') }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('atas_nama_bpkb') border-red-500 @enderror"
                                       placeholder="Nama pemilik di BPKB">
                                @error('atas_nama_bpkb')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Asuransi & Pajak -->
                <div class="space-y-6">
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-purple-900 mb-4">Asuransi Kendaraan</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="perusahaan_asuransi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Perusahaan Asuransi
                                </label>
                                <input type="text" name="perusahaan_asuransi" id="perusahaan_asuransi" value="{{ old('perusahaan_asuransi') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('perusahaan_asuransi') border-red-500 @enderror"
                                       placeholder="Contoh: Asuransi Sinar Mas">
                                @error('perusahaan_asuransi')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nomor_polis_asuransi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Polis Asuransi
                                </label>
                                <input type="text" name="nomor_polis_asuransi" id="nomor_polis_asuransi" value="{{ old('nomor_polis_asuransi') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_polis_asuransi') border-red-500 @enderror">
                                @error('nomor_polis_asuransi')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_berlaku_asuransi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Berlaku Asuransi
                                </label>
                                <input type="date" name="tanggal_berlaku_asuransi" id="tanggal_berlaku_asuransi" value="{{ old('tanggal_berlaku_asuransi') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_berlaku_asuransi') border-red-500 @enderror">
                                @error('tanggal_berlaku_asuransi')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-orange-900 mb-4">Pajak Kendaraan</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nilai_pajak_tahunan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nilai Pajak Tahunan
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                    <input type="number" name="nilai_pajak_tahunan" id="nilai_pajak_tahunan" value="{{ old('nilai_pajak_tahunan') }}" min="0" step="0.01"
                                           class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nilai_pajak_tahunan') border-red-500 @enderror"
                                           placeholder="0">
                                </div>
                                @error('nilai_pajak_tahunan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jatuh_tempo_pajak" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jatuh Tempo Pajak
                                </label>
                                <input type="date" name="jatuh_tempo_pajak" id="jatuh_tempo_pajak" value="{{ old('jatuh_tempo_pajak') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jatuh_tempo_pajak') border-red-500 @enderror">
                                @error('jatuh_tempo_pajak')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Operasional -->
        <div id="content-operational" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Operasional</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="driver_utama" class="block text-sm font-medium text-gray-700 mb-2">
                                    Driver Utama
                                </label>
                                <input type="text" name="driver_utama" id="driver_utama" value="{{ old('driver_utama') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('driver_utama') border-red-500 @enderror"
                                       placeholder="Nama driver utama">
                                @error('driver_utama')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="lokasi_parkir" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Parkir
                                </label>
                                <input type="text" name="lokasi_parkir" id="lokasi_parkir" value="{{ old('lokasi_parkir') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lokasi_parkir') border-red-500 @enderror"
                                       placeholder="Lokasi parkir kendaraan">
                                @error('lokasi_parkir')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="kilometer_terakhir" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kilometer Terakhir
                                </label>
                                <input type="number" name="kilometer_terakhir" id="kilometer_terakhir" value="{{ old('kilometer_terakhir', 0) }}" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kilometer_terakhir') border-red-500 @enderror"
                                       placeholder="0">
                                @error('kilometer_terakhir')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-yellow-900 mb-4">Maintenance</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="tanggal_service_terakhir" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Service Terakhir
                                </label>
                                <input type="date" name="tanggal_service_terakhir" id="tanggal_service_terakhir" value="{{ old('tanggal_service_terakhir') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_service_terakhir') border-red-500 @enderror">
                                @error('tanggal_service_terakhir')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_service_berikutnya" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Service Berikutnya
                                </label>
                                <input type="date" name="tanggal_service_berikutnya" id="tanggal_service_berikutnya" value="{{ old('tanggal_service_berikutnya') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_service_berikutnya') border-red-500 @enderror">
                                @error('tanggal_service_berikutnya')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="catatan" id="catatan" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan') border-red-500 @enderror"
                                  placeholder="Catatan tambahan tentang kendaraan">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: File & Foto -->
        <div id="content-files" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label for="foto_kendaraan" class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Kendaraan
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <input type="file" name="foto_kendaraan" id="foto_kendaraan" accept="image/*" class="hidden" onchange="previewImage(this, 'foto-preview')">
                            <div id="foto-upload-placeholder">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-2">Klik untuk upload foto kendaraan</p>
                                <p class="text-sm text-gray-500">Format: JPG, PNG (Max: 2MB)</p>
                                <button type="button" onclick="document.getElementById('foto_kendaraan').click()" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Pilih File
                                </button>
                            </div>
                            <div id="foto-preview" class="hidden">
                                <img id="foto-preview-img" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-lg mx-auto mb-3">
                                <p id="foto-file-name" class="text-sm text-gray-600 mb-2"></p>
                                <button type="button" onclick="removeImage('foto')" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Hapus Foto
                                </button>
                            </div>
                        </div>
                        @error('foto_kendaraan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="file_stnk" class="block text-sm font-medium text-gray-700 mb-2">
                            File STNK
                        </label>
                        <div class="border-2 border-dashed border-blue-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors">
                            <input type="file" name="file_stnk" id="file_stnk" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="previewFile(this, 'stnk-preview')">
                            <div id="stnk-upload-placeholder">
                                <i class="fas fa-file-alt text-2xl text-blue-400 mb-2"></i>
                                <p class="text-gray-600 text-sm mb-2">Upload file STNK</p>
                                <button type="button" onclick="document.getElementById('file_stnk').click()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Pilih File
                                </button>
                            </div>
                            <div id="stnk-preview" class="hidden">
                                <i class="fas fa-file-alt text-2xl text-blue-600 mb-2"></i>
                                <p id="stnk-file-name" class="text-sm text-gray-600 mb-2"></p>
                                <button type="button" onclick="removeFile('stnk')" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                        @error('file_stnk')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label for="file_bpkb" class="block text-sm font-medium text-gray-700 mb-2">
                            File BPKB
                        </label>
                        <div class="border-2 border-dashed border-green-300 rounded-lg p-4 text-center hover:border-green-400 transition-colors">
                            <input type="file" name="file_bpkb" id="file_bpkb" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="previewFile(this, 'bpkb-preview')">
                            <div id="bpkb-upload-placeholder">
                                <i class="fas fa-file-alt text-2xl text-green-400 mb-2"></i>
                                <p class="text-gray-600 text-sm mb-2">Upload file BPKB</p>
                                <button type="button" onclick="document.getElementById('file_bpkb').click()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Pilih File
                                </button>
                            </div>
                            <div id="bpkb-preview" class="hidden">
                                <i class="fas fa-file-alt text-2xl text-green-600 mb-2"></i>
                                <p id="bpkb-file-name" class="text-sm text-gray-600 mb-2"></p>
                                <button type="button" onclick="removeFile('bpkb')" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                        @error('file_bpkb')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="file_asuransi" class="block text-sm font-medium text-gray-700 mb-2">
                            File Asuransi
                        </label>
                        <div class="border-2 border-dashed border-purple-300 rounded-lg p-4 text-center hover:border-purple-400 transition-colors">
                            <input type="file" name="file_asuransi" id="file_asuransi" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="previewFile(this, 'asuransi-preview')">
                            <div id="asuransi-upload-placeholder">
                                <i class="fas fa-file-alt text-2xl text-purple-400 mb-2"></i>
                                <p class="text-gray-600 text-sm mb-2">Upload file asuransi</p>
                                <button type="button" onclick="document.getElementById('file_asuransi').click()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Pilih File
                                </button>
                            </div>
                            <div id="asuransi-preview" class="hidden">
                                <i class="fas fa-file-alt text-2xl text-purple-600 mb-2"></i>
                                <p id="asuransi-file-name" class="text-sm text-gray-600 mb-2"></p>
                                <button type="button" onclick="removeFile('asuransi')" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                        @error('file_asuransi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('perusahaan.aset-kendaraan.index') }}" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan Kendaraan
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-blue-500', 'text-blue-600');
}

// File preview functions
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById(previewId.replace('-preview', '-upload-placeholder')).classList.add('hidden');
            document.getElementById(previewId).classList.remove('hidden');
            document.getElementById(previewId + '-img').src = e.target.result;
            document.getElementById(previewId.replace('-preview', '-file-name')).textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function previewFile(input, previewId) {
    if (input.files && input.files[0]) {
        document.getElementById(previewId.replace('-preview', '-upload-placeholder')).classList.add('hidden');
        document.getElementById(previewId).classList.remove('hidden');
        document.getElementById(previewId.replace('-preview', '-file-name')).textContent = input.files[0].name;
    }
}

function removeImage(type) {
    document.getElementById(type + '_kendaraan').value = '';
    document.getElementById(type + '-upload-placeholder').classList.remove('hidden');
    document.getElementById(type + '-preview').classList.add('hidden');
}

function removeFile(type) {
    document.getElementById('file_' + type).value = '';
    document.getElementById(type + '-upload-placeholder').classList.remove('hidden');
    document.getElementById(type + '-preview').classList.add('hidden');
}

// Auto uppercase for nomor polisi
document.getElementById('nomor_polisi').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Auto-suggest merk
document.getElementById('merk').addEventListener('input', async function() {
    const query = this.value;
    if (query.length >= 2) {
        try {
            const response = await fetch(`/perusahaan/aset-kendaraan-merk-suggestions?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            
            const datalist = document.getElementById('merk-suggestions');
            datalist.innerHTML = '';
            
            suggestions.forEach(merk => {
                const option = document.createElement('option');
                option.value = merk;
                datalist.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching merk suggestions:', error);
        }
    }
});
</script>
@endpush