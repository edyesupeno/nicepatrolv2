@extends('perusahaan.layouts.app')

@section('title', 'Edit Rekening')
@section('page-title', 'Edit Rekening')
@section('page-subtitle', 'Perbarui informasi rekening bank')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('perusahaan.keuangan.rekening.update', $rekening->hash_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Project -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" required
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $rekening->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Pilih project untuk rekening ini</p>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Rekening -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-university mr-2" style="color: #3B82C8;"></i>Nama Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_rekening" id="nama_rekening" required
                           value="{{ old('nama_rekening', $rekening->nama_rekening) }}"
                           placeholder="Contoh: Rekening Operasional Project A"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                           style="focus:ring-color: #3B82C8;">
                    <p class="mt-2 text-xs text-gray-500">Nama yang mudah diidentifikasi</p>
                    @error('nama_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rekening -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-credit-card mr-2" style="color: #3B82C8;"></i>Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomor_rekening" id="nomor_rekening" required
                           value="{{ old('nomor_rekening', $rekening->nomor_rekening) }}"
                           placeholder="Contoh: 1234567890"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition font-mono"
                           style="focus:ring-color: #3B82C8;">
                    <p class="mt-2 text-xs text-gray-500">Nomor rekening bank yang valid</p>
                    @error('nomor_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Bank -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-building mr-2" style="color: #3B82C8;"></i>Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_bank" id="nama_bank" required
                           value="{{ old('nama_bank', $rekening->nama_bank) }}"
                           placeholder="Contoh: Bank Mandiri"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                           style="focus:ring-color: #3B82C8;">
                    <p class="mt-2 text-xs text-gray-500">Nama bank penerbit rekening</p>
                    @error('nama_bank')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Pemilik -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Nama Pemilik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pemilik" id="nama_pemilik" required
                           value="{{ old('nama_pemilik', $rekening->nama_pemilik) }}"
                           placeholder="Contoh: PT. Nice Patrol Indonesia"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                           style="focus:ring-color: #3B82C8;">
                    <p class="mt-2 text-xs text-gray-500">Nama pemilik rekening sesuai bank</p>
                    @error('nama_pemilik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Rekening -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-tag mr-2" style="color: #3B82C8;"></i>Jenis Rekening <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_rekening" id="jenis_rekening" required
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;">
                        <option value="">Pilih Jenis Rekening</option>
                        <option value="operasional" {{ old('jenis_rekening', $rekening->jenis_rekening) == 'operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="payroll" {{ old('jenis_rekening', $rekening->jenis_rekening) == 'payroll' ? 'selected' : '' }}>Payroll</option>
                        <option value="investasi" {{ old('jenis_rekening', $rekening->jenis_rekening) == 'investasi' ? 'selected' : '' }}>Investasi</option>
                        <option value="emergency" {{ old('jenis_rekening', $rekening->jenis_rekening) == 'emergency' ? 'selected' : '' }}>Emergency Fund</option>
                        <option value="lainnya" {{ old('jenis_rekening', $rekening->jenis_rekening) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Kategori penggunaan rekening</p>
                    @error('jenis_rekening')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Saldo Awal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-wallet mr-2" style="color: #3B82C8;"></i>Saldo Awal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" name="saldo_awal" id="saldo_awal" required min="0" step="0.01"
                               value="{{ old('saldo_awal', $rekening->saldo_awal) }}"
                               placeholder="0"
                               class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                               style="focus:ring-color: #3B82C8;">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Saldo awal saat membuka rekening</p>
                    @error('saldo_awal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mata Uang -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-coins mr-2" style="color: #3B82C8;"></i>Mata Uang <span class="text-red-500">*</span>
                    </label>
                    <select name="mata_uang" id="mata_uang" required
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition"
                            style="focus:ring-color: #3B82C8;">
                        <option value="IDR" {{ old('mata_uang', $rekening->mata_uang) == 'IDR' ? 'selected' : '' }}>IDR - Rupiah</option>
                        <option value="USD" {{ old('mata_uang', $rekening->mata_uang) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="EUR" {{ old('mata_uang', $rekening->mata_uang) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="SGD" {{ old('mata_uang', $rekening->mata_uang) == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar</option>
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Mata uang yang digunakan</p>
                    @error('mata_uang')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-align-left mr-2" style="color: #3B82C8;"></i>Keterangan
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="4"
                              placeholder="Keterangan tambahan tentang rekening ini..."
                              class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition resize-none"
                              style="focus:ring-color: #3B82C8;">{{ old('keterangan', $rekening->keterangan) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Informasi tambahan tentang rekening (opsional)</p>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Settings & Color -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-cog mr-3" style="color: #3B82C8;"></i>
                Pengaturan & Tampilan
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Status Settings -->
                <div class="space-y-6">
                    <h4 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Status Rekening</h4>
                    
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $rekening->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                                Rekening Aktif
                            </label>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                                   {{ old('is_primary', $rekening->is_primary) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_primary" class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-star mr-2 text-yellow-600"></i>
                                Jadikan Rekening Utama
                            </label>
                            <div class="ml-2" data-tippy-content="Hanya satu rekening utama per project. Rekening utama akan digunakan sebagai default untuk transaksi.">
                                <i class="fas fa-info-circle text-gray-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Selection -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-4">
                        Warna Card <span class="text-red-500">*</span>
                    </h4>
                    <div class="grid grid-cols-5 gap-4">
                        @foreach($colors as $hex => $name)
                            <label class="cursor-pointer group">
                                <input type="radio" name="warna_card" value="{{ $hex }}" 
                                       {{ old('warna_card', $rekening->warna_card) == $hex ? 'checked' : '' }}
                                       class="sr-only">
                                <div class="w-16 h-16 rounded-xl border-3 border-gray-200 hover:border-gray-300 transition-all duration-200 flex items-center justify-center group-hover:scale-105 transform"
                                     style="background-color: {{ $hex }}"
                                     data-tippy-content="{{ $name }}">
                                    <i class="fas fa-check text-white text-xl opacity-0 transition-opacity duration-200"></i>
                                </div>
                                <p class="text-xs text-center mt-2 text-gray-600 font-medium">{{ $name }}</p>
                            </label>
                        @endforeach
                    </div>
                    @error('warna_card')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Current Balance Info -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-info-circle text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-blue-900">Informasi Saldo</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <p class="text-sm text-blue-700 mb-2 font-medium">Saldo Awal</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $rekening->formatted_saldo_awal }}</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <p class="text-sm text-blue-700 mb-2 font-medium">Saldo Saat Ini</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $rekening->formatted_saldo_saat_ini }}</p>
                </div>
            </div>
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <p class="text-sm text-yellow-800 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Perhatian:</strong> Perubahan saldo awal tidak akan mempengaruhi saldo saat ini. Saldo saat ini dikelola melalui transaksi.
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <a href="{{ route('perusahaan.keuangan.rekening.index') }}" 
                   class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>

                <div class="flex space-x-4">
                    <a href="{{ route('perusahaan.keuangan.rekening.show', $rekening->hash_id) }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-blue-300 text-blue-700 rounded-xl font-semibold hover:bg-blue-50 transition">
                        <i class="fas fa-eye mr-2"></i>
                        Lihat Detail
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl"
                            style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-save mr-2"></i>
                        Perbarui Rekening
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tippy.js
    tippy('[data-tippy-content]', {
        theme: 'light-border',
        placement: 'top'
    });

    // Color selection
    const colorInputs = document.querySelectorAll('input[name="warna_card"]');

    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Update check icon
            colorInputs.forEach(inp => {
                const icon = inp.parentElement.querySelector('.fa-check');
                icon.style.opacity = inp.checked ? '1' : '0';
            });
        });
    });

    // Initialize checked color
    const checkedColor = document.querySelector('input[name="warna_card"]:checked');
    if (checkedColor) {
        checkedColor.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection