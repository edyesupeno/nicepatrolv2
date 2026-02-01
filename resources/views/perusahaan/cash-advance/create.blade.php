@extends('perusahaan.layouts.app')

@section('title', 'Pengajuan Cash Advance')
@section('page-title', 'Pengajuan Cash Advance Baru')
@section('page-subtitle', 'Isi form di bawah untuk membuat pengajuan Cash Advance baru')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 56px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.75rem !important;
    padding: 0 16px !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 56px !important;
    padding-left: 0 !important;
    color: #374151 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 54px !important;
    right: 16px !important;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
}

.select2-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 0.75rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

.select2-results__option {
    padding: 12px 16px !important;
}

.select2-results__option--highlighted {
    background-color: #3b82f6 !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 8px 12px !important;
}
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center">
        <a href="{{ route('perusahaan.keuangan.cash-advance.index') }}" 
           class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <form action="{{ route('perusahaan.keuangan.cash-advance.store') }}" method="POST" id="cashAdvanceForm" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Informasi Dasar</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-project-diagram text-blue-500 mr-2"></i>
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" required
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('project_id') border-red-500 @enderror">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Karyawan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-user text-green-500 mr-2"></i>
                        Karyawan Pemegang <span class="text-red-500">*</span>
                    </label>
                    <select name="karyawan_id" id="karyawan_id" required disabled
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('karyawan_id') border-red-500 @enderror">
                        <option value="">Pilih Project terlebih dahulu</option>
                    </select>
                    @error('karyawan_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-search mr-1"></i>
                        Ketik minimal 2 karakter untuk mencari karyawan berdasarkan nama atau NIK. Hanya karyawan yang belum memiliki Cash Advance aktif yang akan ditampilkan.
                    </p>
                </div>

                <!-- Rekening -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-university text-indigo-500 mr-2"></i>
                        Rekening Sumber Dana <span class="text-red-500">*</span>
                    </label>
                    <select name="rekening_id" id="rekening_id" required
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('rekening_id') border-red-500 @enderror">
                        <option value="">Pilih Rekening</option>
                        @foreach($rekenings as $rekening)
                            <option value="{{ $rekening->id }}" data-saldo="{{ $rekening->saldo_saat_ini }}" {{ old('rekening_id') == $rekening->id ? 'selected' : '' }}>
                                {{ $rekening->nama_rekening }} ({{ $rekening->nomor_rekening }}) - Saldo: Rp {{ number_format($rekening->saldo_saat_ini, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('rekening_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Dana Cash Advance akan dipindahkan dari rekening ini
                    </p>
                </div>
            </div>
        </div>

        <!-- Financial Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Detail Keuangan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jumlah Pengajuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-dollar-sign text-green-500 mr-2"></i>
                        Jumlah Pengajuan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-base">Rp</span>
                        </div>
                        <input type="text" name="jumlah_pengajuan" id="jumlah_pengajuan" required
                               value="{{ old('jumlah_pengajuan') }}"
                               placeholder="0"
                               class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('jumlah_pengajuan') border-red-500 @enderror">
                    </div>
                    @error('jumlah_pengajuan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Pengajuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                        Tanggal Pengajuan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pengajuan" id="tanggal_pengajuan" required
                           value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('tanggal_pengajuan') border-red-500 @enderror">
                    @error('tanggal_pengajuan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Pertanggungjawaban -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                        Batas Pertanggungjawaban <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="batas_pertanggungjawaban" id="batas_pertanggungjawaban" required
                           value="{{ old('batas_pertanggungjawaban') }}"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('batas_pertanggungjawaban') border-red-500 @enderror">
                    @error('batas_pertanggungjawaban')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Karyawan harus melaporkan penggunaan pada atau sebelum tanggal ini
                    </p>
                </div>
            </div>
        </div>

        <!-- Purpose Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clipboard-list text-purple-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Detail Keperluan</h2>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-align-left text-purple-500 mr-2"></i>
                    Keperluan <span class="text-red-500">*</span>
                </label>
                <textarea name="keperluan" id="keperluan" rows="4" required
                          placeholder="Jelaskan keperluan Cash Advance ini..."
                          class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base @error('keperluan') border-red-500 @enderror">{{ old('keperluan') }}</textarea>
                @error('keperluan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Information Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Informasi Penting:</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Cash Advance menggunakan sistem saldo virtual</li>
                        <li>• Karyawan harus melaporkan semua penggunaan dengan bukti</li>
                        <li>• Sisa saldo harus dikembalikan atau dilaporkan sebelum batas waktu</li>
                        <li>• Karyawan tidak dapat mengajukan Cash Advance baru sebelum yang lama selesai</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('perusahaan.keuangan.cash-advance.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-save mr-2"></i>
                Simpan Pengajuan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const karyawanSelect = document.getElementById('karyawan_id');
    const rekeningSelect = document.getElementById('rekening_id');
    const jumlahInput = document.getElementById('jumlah_pengajuan');
    const tanggalPengajuan = document.getElementById('tanggal_pengajuan');
    const batasPertanggungjawaban = document.getElementById('batas_pertanggungjawaban');

    // Initialize Select2 for karyawan
    let karyawanSelect2 = null;

    function initializeKaryawanSelect2(projectId) {
        // Destroy existing Select2 if exists
        if (karyawanSelect2) {
            $(karyawanSelect).select2('destroy');
        }

        // Initialize Select2 with AJAX
        karyawanSelect2 = $(karyawanSelect).select2({
            placeholder: 'Ketik nama atau NIK karyawan...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("perusahaan.keuangan.cash-advance.search-karyawan") }}',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        search: params.term,
                        project_id: projectId
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            templateResult: function(karyawan) {
                if (karyawan.loading) {
                    return karyawan.text;
                }
                
                return $('<div>' +
                    '<div style="font-weight: 600;">' + karyawan.nama_lengkap + '</div>' +
                    '<div style="font-size: 0.875rem; color: #6b7280;">NIK: ' + karyawan.nik_karyawan + '</div>' +
                    '</div>');
            },
            templateSelection: function(karyawan) {
                return karyawan.nama_lengkap || karyawan.text;
            }
        });

        // Enable the select
        karyawanSelect.disabled = false;
    }

    // Validasi saldo rekening vs jumlah pengajuan
    function validateSaldoRekening() {
        const selectedRekening = rekeningSelect.options[rekeningSelect.selectedIndex];
        const saldoRekening = selectedRekening ? parseFloat(selectedRekening.dataset.saldo) : 0;
        const jumlahPengajuan = parseFloat(jumlahInput.value.replace(/[^\d]/g, '')) || 0;
        
        if (jumlahPengajuan > saldoRekening && rekeningSelect.value) {
            jumlahInput.setCustomValidity('Jumlah pengajuan melebihi saldo rekening');
            jumlahInput.reportValidity();
            return false;
        } else {
            jumlahInput.setCustomValidity('');
            return true;
        }
    }

    // Validasi saat input jumlah berubah
    jumlahInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d]/g, '');
        if (value) {
            e.target.value = new Intl.NumberFormat('id-ID').format(value);
        }
        validateSaldoRekening();
    });

    // Validasi saat rekening berubah
    rekeningSelect.addEventListener('change', validateSaldoRekening);

    // Set minimum date for batas pertanggungjawaban
    tanggalPengajuan.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        // Allow same date or later
        batasPertanggungjawaban.min = selectedDate.toISOString().split('T')[0];
        
        // Auto set to 30 days later if empty
        if (!batasPertanggungjawaban.value) {
            const defaultDate = new Date(this.value);
            defaultDate.setDate(defaultDate.getDate() + 30);
            batasPertanggungjawaban.value = defaultDate.toISOString().split('T')[0];
        }
    });

    // Load karyawan when project changes
    projectSelect.addEventListener('change', function() {
        const projectId = this.value;
        
        if (projectId) {
            // Initialize Select2 for karyawan search
            initializeKaryawanSelect2(projectId);
        } else {
            // Destroy Select2 and disable
            if (karyawanSelect2) {
                $(karyawanSelect).select2('destroy');
                karyawanSelect2 = null;
            }
            karyawanSelect.innerHTML = '<option value="">Pilih Project terlebih dahulu</option>';
            karyawanSelect.disabled = true;
        }
    });

    // Form submission with SweetAlert
    document.getElementById('cashAdvanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi saldo rekening
        if (!validateSaldoRekening()) {
            return;
        }
        
        // Convert formatted number back to plain number
        const jumlahFormatted = jumlahInput.value.replace(/[^\d]/g, '');
        jumlahInput.value = jumlahFormatted;
        
        Swal.fire({
            title: 'Konfirmasi Pengajuan',
            text: 'Apakah data yang dimasukkan sudah benar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Periksa Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                this.submit();
            } else {
                // Restore formatted value if user cancels
                if (jumlahFormatted) {
                    jumlahInput.value = new Intl.NumberFormat('id-ID').format(jumlahFormatted);
                }
            }
        });
    });

    // Trigger change event if project is already selected (for edit mode)
    if (projectSelect.value) {
        projectSelect.dispatchEvent(new Event('change'));
    }

    // Set minimum date for tanggal_pengajuan to today
    tanggalPengajuan.min = new Date().toISOString().split('T')[0];
});
</script>
@endpush