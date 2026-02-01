@extends('perusahaan.layouts.app')

@section('title', 'Tambah Rekening')
@section('page-title', 'Tambah Rekening')
@section('page-subtitle', 'Tambah rekening bank baru untuk project')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('perusahaan.keuangan.rekening.store') }}" method="POST" class="space-y-6">
        @csrf
        
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
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                           value="{{ old('nama_rekening') }}"
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
                           value="{{ old('nomor_rekening') }}"
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
                           value="{{ old('nama_bank') }}"
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
                           value="{{ old('nama_pemilik') }}"
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
                        <option value="operasional" {{ old('jenis_rekening') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="payroll" {{ old('jenis_rekening') == 'payroll' ? 'selected' : '' }}>Payroll</option>
                        <option value="investasi" {{ old('jenis_rekening') == 'investasi' ? 'selected' : '' }}>Investasi</option>
                        <option value="emergency" {{ old('jenis_rekening') == 'emergency' ? 'selected' : '' }}>Emergency Fund</option>
                        <option value="lainnya" {{ old('jenis_rekening') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                               value="{{ old('saldo_awal', 0) }}"
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
                        <option value="IDR" {{ old('mata_uang', 'IDR') == 'IDR' ? 'selected' : '' }}>IDR - Rupiah</option>
                        <option value="USD" {{ old('mata_uang') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="EUR" {{ old('mata_uang') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="SGD" {{ old('mata_uang') == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar</option>
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
                              style="focus:ring-color: #3B82C8;">{{ old('keterangan') }}</textarea>
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
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                                Rekening Aktif
                            </label>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                                   {{ old('is_primary') ? 'checked' : '' }}
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
                                       {{ old('warna_card', '#3B82C8') == $hex ? 'checked' : '' }}
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

        <!-- Preview Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-eye mr-3" style="color: #3B82C8;"></i>
                Preview Card
            </h3>
            
            <div class="max-w-sm mx-auto">
                <div id="preview-card" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden transform hover:scale-105 transition-transform duration-200">
                    <!-- Card Header with Color -->
                    <div id="preview-color" class="h-3" style="background-color: #3B82C8"></div>
                    
                    <!-- Card Content -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h4 id="preview-nama" class="text-lg font-bold text-gray-900">Nama Rekening</h4>
                                <p id="preview-project" class="text-sm text-gray-600">Project Name</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-university w-5 mr-3"></i>
                                <span id="preview-bank">Nama Bank</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-credit-card w-5 mr-3"></i>
                                <span id="preview-nomor">****-****-****</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span id="preview-jenis" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Jenis
                            </span>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Saldo</p>
                                <p id="preview-saldo" class="text-lg font-bold text-blue-600">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <button type="reset" 
                            class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl"
                            style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Rekening
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
    const previewColor = document.getElementById('preview-color');
    const previewJenis = document.getElementById('preview-jenis');
    const previewSaldo = document.getElementById('preview-saldo');

    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            const color = this.value;
            previewColor.style.backgroundColor = color;
            previewJenis.style.backgroundColor = color + '20';
            previewJenis.style.color = color;
            previewSaldo.style.color = color;
            
            // Update check icon
            colorInputs.forEach(inp => {
                const icon = inp.parentElement.querySelector('.fa-check');
                icon.style.opacity = inp.checked ? '1' : '0';
            });
        });
    });

    // Preview updates
    const namaRekeningInput = document.getElementById('nama_rekening');
    const projectSelect = document.getElementById('project_id');
    const bankInput = document.getElementById('nama_bank');
    const nomorInput = document.getElementById('nomor_rekening');
    const jenisSelect = document.getElementById('jenis_rekening');
    const saldoInput = document.getElementById('saldo_awal');

    const previewNama = document.getElementById('preview-nama');
    const previewProject = document.getElementById('preview-project');
    const previewBank = document.getElementById('preview-bank');
    const previewNomor = document.getElementById('preview-nomor');

    namaRekeningInput.addEventListener('input', function() {
        previewNama.textContent = this.value || 'Nama Rekening';
    });

    projectSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        previewProject.textContent = selectedOption.text === 'Pilih Project' ? 'Project Name' : selectedOption.text;
    });

    bankInput.addEventListener('input', function() {
        previewBank.textContent = this.value || 'Nama Bank';
    });

    nomorInput.addEventListener('input', function() {
        const value = this.value;
        if (value.length > 8) {
            const masked = value.substring(0, 4) + '*'.repeat(value.length - 8) + value.substring(value.length - 4);
            previewNomor.textContent = masked;
        } else {
            previewNomor.textContent = value || '****-****-****';
        }
    });

    jenisSelect.addEventListener('change', function() {
        const jenisLabels = {
            'operasional': 'Operasional',
            'payroll': 'Payroll',
            'investasi': 'Investasi',
            'emergency': 'Emergency Fund',
            'lainnya': 'Lainnya'
        };
        previewJenis.textContent = jenisLabels[this.value] || 'Jenis';
    });

    saldoInput.addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        previewSaldo.textContent = 'Rp ' + value.toLocaleString('id-ID');
    });

    // Initialize preview with default values
    const checkedColor = document.querySelector('input[name="warna_card"]:checked');
    if (checkedColor) {
        checkedColor.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection