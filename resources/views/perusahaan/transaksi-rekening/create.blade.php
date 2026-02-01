@extends('perusahaan.layouts.app')

@section('title', 'Tambah Transaksi Rekening')
@section('page-title', 'Tambah Transaksi Rekening')
@section('page-subtitle', 'Catat transaksi debit atau kredit pada rekening')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    Transaksi Rekening
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Tambah Transaksi</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="flex justify-end">
        <a href="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-plus-circle mr-2 text-blue-500"></i>
                    Form Transaksi Baru
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('perusahaan.keuangan.transaksi-rekening.store') }}" method="POST" id="transactionForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rekening -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-university text-blue-500 mr-2"></i>
                                Rekening <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rekening_id') border-red-300 @enderror" 
                                    name="rekening_id" id="rekening_id" required>
                                <option value="">Pilih Rekening</option>
                                @foreach($rekenings as $rekening)
                                    <option value="{{ $rekening->id }}" 
                                            data-saldo="{{ $rekening->saldo_saat_ini }}"
                                            data-mata-uang="{{ $rekening->mata_uang }}"
                                            data-warna="{{ $rekening->warna_card }}"
                                            {{ old('rekening_id') == $rekening->id ? 'selected' : '' }}>
                                        {{ $rekening->nama_rekening }} - {{ $rekening->project->nama ?? 'N/A' }}
                                        (Saldo: {{ $rekening->mata_uang }} {{ number_format($rekening->saldo_saat_ini, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('rekening_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Saldo Info -->
                            <div id="saldoInfo" class="mt-2 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-sm text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Saldo saat ini: <span id="currentSaldo" class="font-semibold"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Transaksi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                Tanggal Transaksi <span class="text-red-500">*</span>
                            </label>
                            <input type="date" class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_transaksi') border-red-300 @enderror" 
                                   name="tanggal_transaksi" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required>
                            @error('tanggal_transaksi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Jenis Transaksi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-exchange-alt text-blue-500 mr-2"></i>
                                Jenis Transaksi <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_transaksi') border-red-300 @enderror" 
                                    name="jenis_transaksi" id="jenis_transaksi" required>
                                <option value="">Pilih Jenis</option>
                                <option value="debit" {{ old('jenis_transaksi') === 'debit' ? 'selected' : '' }}>
                                    Debit (Uang Masuk)
                                </option>
                                <option value="kredit" {{ old('jenis_transaksi') === 'kredit' ? 'selected' : '' }}>
                                    Kredit (Uang Keluar)
                                </option>
                            </select>
                            @error('jenis_transaksi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-money-bill-wave text-blue-500 mr-2"></i>
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="currency-symbol">Rp</span>
                                </div>
                                <input type="text" class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jumlah') border-red-300 @enderror" 
                                       name="jumlah_display" id="jumlah_display" value="{{ old('jumlah') ? number_format(old('jumlah'), 0, ',', '.') : '' }}" 
                                       placeholder="0" required>
                                <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah') }}">
                            </div>
                            @error('jumlah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Saldo Warning -->
                            <div id="saldoWarning" class="mt-2 hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <p class="text-sm text-yellow-700">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <span id="warningText"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori Transaksi -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags text-blue-500 mr-2"></i>
                            Kategori Transaksi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   class="w-full px-4 py-4 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kategori_transaksi') border-red-300 @enderror" 
                                   name="kategori_transaksi_display" 
                                   id="kategori_transaksi"
                                   value="{{ old('kategori_transaksi') }}"
                                   placeholder="Ketik untuk mencari atau tambah kategori baru..."
                                   autocomplete="off"
                                   required>
                            <input type="hidden" name="kategori_transaksi" id="kategori_transaksi_hidden" value="{{ old('kategori_transaksi') }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        @error('kategori_transaksi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Dropdown suggestions -->
                        <div id="kategori_suggestions" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                            <!-- Suggestions will be populated by JavaScript -->
                        </div>
                        
                        <!-- Info text -->
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Ketik untuk mencari kategori yang ada atau masukkan kategori baru
                        </p>
                    </div>

                    <!-- Keterangan -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment-alt text-blue-500 mr-2"></i>
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-300 @enderror" 
                                  name="keterangan" rows="4" placeholder="Masukkan keterangan transaksi..." required>{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Referensi -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                            Nomor Referensi
                        </label>
                        <input type="text" class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('referensi') border-red-300 @enderror" 
                               name="referensi" value="{{ old('referensi') }}" 
                               placeholder="Nomor cek, transfer, invoice, dll (opsional)">
                        @error('referensi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Verifikasi -->
                    <div class="mt-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                                       name="is_verified" id="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_verified" class="font-medium text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Verifikasi transaksi ini sekarang
                                </label>
                                <p class="text-gray-500">
                                    Jika dicentang, transaksi akan langsung terverifikasi. 
                                    Jika tidak, transaksi akan berstatus pending dan perlu verifikasi manual.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 mt-8">
                        <a href="{{ route('perusahaan.keuangan.transaksi-rekening.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rekeningSelect = document.getElementById('rekening_id');
    const jenisSelect = document.getElementById('jenis_transaksi');
    const jumlahDisplay = document.getElementById('jumlah_display');
    const jumlahHidden = document.getElementById('jumlah');
    const saldoInfo = document.getElementById('saldoInfo');
    const currentSaldo = document.getElementById('currentSaldo');
    const saldoWarning = document.getElementById('saldoWarning');
    const warningText = document.getElementById('warningText');
    const currencySymbol = document.getElementById('currency-symbol');

    // Format number with thousand separator
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Remove formatting and get raw number
    function unformatNumber(str) {
        return str.replace(/\./g, '');
    }

    // Kategori search functionality
    const kategoriInput = document.getElementById('kategori_transaksi');
    const kategoriHidden = document.getElementById('kategori_transaksi_hidden');
    const kategoriSuggestions = document.getElementById('kategori_suggestions');
    
    // Get all available categories - hardcoded for better control
    const availableKategori = [
        { key: 'transfer_masuk', label: 'Transfer Masuk' },
        { key: 'transfer_keluar', label: 'Transfer Keluar' },
        { key: 'pembayaran_vendor', label: 'Pembayaran Vendor' },
        { key: 'pembayaran_gaji', label: 'Pembayaran Gaji' },
        { key: 'penerimaan_client', label: 'Penerimaan dari Client' },
        { key: 'biaya_operasional', label: 'Biaya Operasional' },
        { key: 'investasi', label: 'Investasi' },
        { key: 'pinjaman', label: 'Pinjaman' },
        { key: 'bunga_bank', label: 'Bunga Bank' },
        { key: 'biaya_admin', label: 'Biaya Administrasi' },
        { key: 'lainnya', label: 'Lainnya' }
    ];

    // Initialize kategori input with proper label
    function initializeKategoriInput() {
        const currentValue = kategoriHidden.value;
        if (currentValue) {
            const kategori = availableKategori.find(item => item.key === currentValue);
            if (kategori) {
                kategoriInput.value = kategori.label;
            }
        }
    }

    function showSuggestions(searchTerm) {
        const filtered = availableKategori.filter(item => 
            item.label.toLowerCase().includes(searchTerm.toLowerCase())
        );

        if (filtered.length === 0 && searchTerm.trim() !== '') {
            // Show option to add new category
            kategoriSuggestions.innerHTML = `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b" onclick="selectNewKategori('${searchTerm}')">
                    <div class="flex items-center">
                        <i class="fas fa-plus text-green-500 mr-2"></i>
                        <div>
                            <div class="font-medium text-gray-900">Tambah kategori baru</div>
                            <div class="text-sm text-gray-500">"${searchTerm}"</div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            kategoriSuggestions.innerHTML = filtered.map(item => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b" onclick="selectKategori('${item.key}', '${item.label}')">
                    <div class="flex items-center">
                        <i class="fas fa-tag text-blue-500 mr-2"></i>
                        <div class="font-medium text-gray-900">${item.label}</div>
                    </div>
                </div>
            `).join('');
            
            // Add option to create new if search term doesn't match exactly
            if (searchTerm.trim() !== '' && !filtered.some(item => 
                item.label.toLowerCase() === searchTerm.toLowerCase()
            )) {
                kategoriSuggestions.innerHTML += `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-t" onclick="selectNewKategori('${searchTerm}')">
                        <div class="flex items-center">
                            <i class="fas fa-plus text-green-500 mr-2"></i>
                            <div>
                                <div class="font-medium text-gray-900">Tambah kategori baru</div>
                                <div class="text-sm text-gray-500">"${searchTerm}"</div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        kategoriSuggestions.classList.remove('hidden');
    }

    function hideSuggestions() {
        setTimeout(() => {
            kategoriSuggestions.classList.add('hidden');
        }, 200);
    }

    function selectKategori(key, label) {
        kategoriInput.value = label;
        kategoriHidden.value = key;
        kategoriSuggestions.classList.add('hidden');
    }

    function selectNewKategori(newKategori) {
        // Convert to snake_case for consistency
        const key = newKategori.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_');
        
        kategoriInput.value = newKategori;
        kategoriHidden.value = key;
        kategoriSuggestions.classList.add('hidden');
        
        // Add to available categories for future use
        availableKategori.push({
            key: key,
            label: newKategori
        });
    }

    // Event listeners for kategori input
    kategoriInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value;
        if (searchTerm.length > 0) {
            showSuggestions(searchTerm);
        } else {
            hideSuggestions();
        }
    });

    kategoriInput.addEventListener('focus', function(e) {
        if (e.target.value.length > 0) {
            showSuggestions(e.target.value);
        }
    });

    kategoriInput.addEventListener('blur', hideSuggestions);

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!kategoriInput.contains(e.target) && !kategoriSuggestions.contains(e.target)) {
            kategoriSuggestions.classList.add('hidden');
        }
    });

    // Make functions global so they can be called from onclick
    window.selectKategori = selectKategori;
    window.selectNewKategori = selectNewKategori;

    // Handle input formatting
    jumlahDisplay.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove all non-digit characters
        value = value.replace(/[^\d]/g, '');
        
        // Format with thousand separator
        if (value) {
            const formatted = formatNumber(value);
            e.target.value = formatted;
            jumlahHidden.value = value;
        } else {
            e.target.value = '';
            jumlahHidden.value = '';
        }
        
        checkSaldoWarning();
    });

    // Prevent non-numeric input
    jumlahDisplay.addEventListener('keypress', function(e) {
        // Allow backspace, delete, tab, escape, enter
        if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
            // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    function updateSaldoInfo() {
        const selectedOption = rekeningSelect.options[rekeningSelect.selectedIndex];
        if (selectedOption.value) {
            const saldo = parseFloat(selectedOption.dataset.saldo);
            const mataUang = selectedOption.dataset.mataUang;
            
            currentSaldo.textContent = mataUang + ' ' + formatNumber(saldo.toString());
            currencySymbol.textContent = mataUang;
            saldoInfo.classList.remove('hidden');
            
            checkSaldoWarning();
        } else {
            saldoInfo.classList.add('hidden');
            saldoWarning.classList.add('hidden');
            currencySymbol.textContent = 'Rp';
        }
    }

    function checkSaldoWarning() {
        const selectedOption = rekeningSelect.options[rekeningSelect.selectedIndex];
        const jenis = jenisSelect.value;
        const jumlah = parseFloat(jumlahHidden.value) || 0;
        
        if (selectedOption.value && jenis === 'kredit' && jumlah > 0) {
            const saldo = parseFloat(selectedOption.dataset.saldo);
            const mataUang = selectedOption.dataset.mataUang;
            
            if (jumlah > saldo) {
                warningText.textContent = `Jumlah melebihi saldo tersedia (${mataUang} ${formatNumber(saldo.toString())})`;
                saldoWarning.classList.remove('hidden');
            } else {
                const sisaSaldo = saldo - jumlah;
                warningText.textContent = `Saldo setelah transaksi: ${mataUang} ${formatNumber(sisaSaldo.toString())}`;
                saldoWarning.classList.remove('hidden');
                saldoWarning.querySelector('.bg-yellow-50').className = 'bg-blue-50 border border-blue-200 rounded-lg p-3';
                saldoWarning.querySelector('.text-yellow-700').className = 'text-sm text-blue-700';
            }
        } else {
            saldoWarning.classList.add('hidden');
        }
    }

    rekeningSelect.addEventListener('change', updateSaldoInfo);
    jenisSelect.addEventListener('change', checkSaldoWarning);

    // Initialize
    updateSaldoInfo();
    initializeKategoriInput();
});

// Form validation
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const rekeningSelect = document.getElementById('rekening_id');
    const jenisSelect = document.getElementById('jenis_transaksi');
    const jumlahHidden = document.getElementById('jumlah');
    
    // Ensure hidden field has value
    if (!jumlahHidden.value || parseFloat(jumlahHidden.value) <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Jumlah Tidak Valid',
            text: 'Silakan masukkan jumlah transaksi yang valid.',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    if (rekeningSelect.value && jenisSelect.value === 'kredit' && jumlahHidden.value) {
        const selectedOption = rekeningSelect.options[rekeningSelect.selectedIndex];
        const saldo = parseFloat(selectedOption.dataset.saldo);
        const jumlah = parseFloat(jumlahHidden.value);
        
        if (jumlah > saldo) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Saldo Tidak Mencukupi',
                text: 'Jumlah transaksi melebihi saldo yang tersedia di rekening.',
                confirmButtonText: 'OK'
            });
            return false;
        }
    }
});
</script>
@endsection