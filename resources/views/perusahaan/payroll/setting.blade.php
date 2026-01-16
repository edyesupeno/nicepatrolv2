@extends('perusahaan.layouts.app')

@section('content')
<div class="p-6">
    
    <!-- Flash Messages -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        });
    </script>
    @endif
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-cog text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengaturan Payroll</h1>
                <p class="text-sm text-gray-600">Konfigurasi BPJS, Pajak, Lembur, dan Periode Payroll</p>
            </div>
        </div>
    </div>

    <form action="{{ route('perusahaan.setting-payroll.update') }}" method="POST">
        @csrf
        
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex">
                        <button type="button" onclick="switchTab('bpjs')" id="tab-bpjs" class="tab-button flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                            <i class="fas fa-hospital"></i>
                            BPJS
                        </button>
                        <button type="button" onclick="switchTab('pph21')" id="tab-pph21" class="tab-button flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-chart-bar"></i>
                            Pajak PPh 21
                        </button>
                        <button type="button" onclick="switchTab('lembur')" id="tab-lembur" class="tab-button flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-clock"></i>
                            Lembur
                        </button>
                        <button type="button" onclick="switchTab('periode')" id="tab-periode" class="tab-button flex items-center gap-2 px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-calendar-alt"></i>
                            Periode
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    
                    <!-- Tab BPJS -->
                    <div id="content-bpjs" class="tab-content">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-blue-900 mb-1">Info</p>
                                    <p class="text-xs text-blue-700">Persentase BPJS dihitung dari gaji pokok karyawan</p>
                                </div>
                            </div>
                        </div>

                        <!-- BPJS Kesehatan -->
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-heartbeat text-red-600"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800">BPJS Kesehatan</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kontribusi Perusahaan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="bpjs_kesehatan_perusahaan" value="{{ old('bpjs_kesehatan_perusahaan', $setting->bpjs_kesehatan_perusahaan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kontribusi Karyawan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="bpjs_kesehatan_karyawan" value="{{ old('bpjs_kesehatan_karyawan', $setting->bpjs_kesehatan_karyawan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BPJS Ketenagakerjaan - JHT -->
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-briefcase text-blue-600"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800">BPJS Ketenagakerjaan - JHT (Jaminan Hari Tua)</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kontribusi Perusahaan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="bpjs_jht_perusahaan" value="{{ old('bpjs_jht_perusahaan', $setting->bpjs_jht_perusahaan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kontribusi Karyawan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="bpjs_jht_karyawan" value="{{ old('bpjs_jht_karyawan', $setting->bpjs_jht_karyawan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BPJS Ketenagakerjaan - JP -->
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-shield text-green-600"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800">BPJS Ketenagakerjaan - JP (Jaminan Pensiun)</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kontribusi Perusahaan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="bpjs_jp_perusahaan" value="{{ old('bpjs_jp_perusahaan', $setting->bpjs_jp_perusahaan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kontribusi Karyawan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="bpjs_jp_karyawan" value="{{ old('bpjs_jp_karyawan', $setting->bpjs_jp_karyawan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BPJS Ketenagakerjaan - Lainnya -->
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-purple-600"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800">BPJS Ketenagakerjaan - Lainnya</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">JKK - Jaminan Kecelakaan Kerja (%)</label>
                                    <p class="text-xs text-gray-500 mb-2">Ditanggung perusahaan</p>
                                    <div class="relative">
                                        <input type="number" name="bpjs_jkk_perusahaan" value="{{ old('bpjs_jkk_perusahaan', $setting->bpjs_jkk_perusahaan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">JKM - Jaminan Kematian (%)</label>
                                    <p class="text-xs text-gray-500 mb-2">Ditanggung perusahaan</p>
                                    <div class="relative">
                                        <input type="number" name="bpjs_jkm_perusahaan" value="{{ old('bpjs_jkm_perusahaan', $setting->bpjs_jkm_perusahaan) }}" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab PPh 21 -->
                    <div id="content-pph21" class="tab-content hidden">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-green-600 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-green-900 mb-1">Info</p>
                                    <p class="text-xs text-green-700">PTKP adalah Penghasilan Tidak Kena Pajak per tahun</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tarif Pajak PPh 21 Progresif -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-blue-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Tarif Pajak PPh 21 Progresif</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-48 text-sm font-medium text-gray-700">Rp 0 - 60 juta</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="pph21_bracket1_rate" value="{{ old('pph21_bracket1_rate', $setting->pph21_bracket1_rate) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-48 text-sm font-medium text-gray-700">Rp 60 - 250 juta</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="pph21_bracket2_rate" value="{{ old('pph21_bracket2_rate', $setting->pph21_bracket2_rate) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-48 text-sm font-medium text-gray-700">Rp 250 - 500 juta</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="pph21_bracket3_rate" value="{{ old('pph21_bracket3_rate', $setting->pph21_bracket3_rate) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-48 text-sm font-medium text-gray-700">Rp 500 juta - 5 miliar</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="pph21_bracket4_rate" value="{{ old('pph21_bracket4_rate', $setting->pph21_bracket4_rate) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-48 text-sm font-medium text-gray-700">> Rp 5 miliar</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="pph21_bracket5_rate" value="{{ old('pph21_bracket5_rate', $setting->pph21_bracket5_rate) }}" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-12" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PTKP - Tidak Kawin -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">PTKP - Tidak Kawin (TK)</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">TK/0 - Tidak Kawin, 0 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_tk0" value="{{ old('ptkp_tk0', $setting->ptkp_tk0) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">TK/1 - Tidak Kawin, 1 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_tk1" value="{{ old('ptkp_tk1', $setting->ptkp_tk1) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">TK/2 - Tidak Kawin, 2 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_tk2" value="{{ old('ptkp_tk2', $setting->ptkp_tk2) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">TK/3 - Tidak Kawin, 3 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_tk3" value="{{ old('ptkp_tk3', $setting->ptkp_tk3) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PTKP - Kawin -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-pink-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">PTKP - Kawin (K)</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">K/0 - Kawin, 0 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_k0" value="{{ old('ptkp_k0', $setting->ptkp_k0) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">K/1 - Kawin, 1 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_k1" value="{{ old('ptkp_k1', $setting->ptkp_k1) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">K/2 - Kawin, 2 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_k2" value="{{ old('ptkp_k2', $setting->ptkp_k2) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">K/3 - Kawin, 3 Tanggungan</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="ptkp_k3" value="{{ old('ptkp_k3', $setting->ptkp_k3) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pl-12" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Lembur -->
                    <div id="content-lembur" class="tab-content hidden">
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-orange-600 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-orange-900 mb-1">Info</p>
                                    <p class="text-xs text-orange-700">Multiplier dikalikan dengan upah per jam karyawan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tarif Lembur -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Tarif Lembur</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-64 text-sm font-medium text-gray-700">Hari Kerja (Senin - Jumat)</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="lembur_hari_kerja" value="{{ old('lembur_hari_kerja', $setting->lembur_hari_kerja) }}" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-32" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">x upah per jam</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 ml-64">Contoh: 1,5 = 1.5x gaji per jam</p>
                                
                                <div class="flex items-center gap-4">
                                    <div class="w-64 text-sm font-medium text-gray-700">Akhir Pekan (Sabtu - Minggu)</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="lembur_akhir_pekan" value="{{ old('lembur_akhir_pekan', $setting->lembur_akhir_pekan) }}" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-32" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">x upah per jam</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 ml-64">Contoh: 2 = 2x gaji per jam</p>
                                
                                <div class="flex items-center gap-4">
                                    <div class="w-64 text-sm font-medium text-gray-700">Hari Libur Nasional</div>
                                    <div class="flex-1 relative">
                                        <input type="number" name="lembur_hari_libur" value="{{ old('lembur_hari_libur', $setting->lembur_hari_libur) }}" step="0.1" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-32" required>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">x upah per jam</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 ml-64">Contoh: 3 = 3x gaji per jam</p>
                            </div>
                        </div>

                        <!-- Maksimal Jam Lembur -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-stopwatch text-red-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Maksimal Jam Lembur per Hari</h3>
                            </div>
                            <div class="max-w-md">
                                <div class="relative">
                                    <input type="number" name="lembur_max_jam_per_hari" value="{{ old('lembur_max_jam_per_hari', $setting->lembur_max_jam_per_hari) }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 pr-16" required>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">jam</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Sesuai peraturan ketenagakerjaan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Periode -->
                    <div id="content-periode" class="tab-content hidden">
                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-purple-600 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-purple-900 mb-1">Info</p>
                                    <p class="text-xs text-purple-700">Tentukan periode perhitungan dan pembayaran gaji</p>
                                </div>
                            </div>
                        </div>

                        <!-- Periode Payroll -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-purple-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Periode Payroll</h3>
                            </div>
                            
                            <!-- Warning -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-yellow-900 mb-1">Perhatian!</p>
                                        <p class="text-xs text-yellow-800 mb-2">Pengaturan ini akan berlaku untuk <strong>SEMUA PROJECT</strong>. Tidak semua perusahaan memiliki tanggal gajian yang sama di setiap project.</p>
                                        <p class="text-xs text-yellow-800">Jika pengaturan ini <strong>tidak diaktifkan</strong>, periode payroll masing-masing project akan diambil dari periode waktu saat generate payroll (input manual).</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-6">
                                <!-- Checkbox Enable -->
                                <div>
                                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-purple-300 transition">
                                        <input type="checkbox" name="periode_auto_generate" value="1" 
                                            {{ old('periode_auto_generate', $setting->periode_auto_generate) ? 'checked' : '' }} 
                                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                            onchange="togglePeriodeInputs(this)">
                                        <div>
                                            <span class="text-sm font-semibold text-gray-700">Aktifkan Pengaturan Periode Otomatis</span>
                                            <p class="text-xs text-gray-500">Centang untuk mengatur tanggal cutoff dan pembayaran yang berlaku untuk semua project</p>
                                        </div>
                                    </label>
                                </div>
                                
                                <div id="periode-inputs" class="{{ old('periode_auto_generate', $setting->periode_auto_generate) ? '' : 'opacity-50 pointer-events-none' }}">
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Cutoff</label>
                                            <div class="flex items-center gap-4">
                                                <div class="relative max-w-xs">
                                                    <input type="number" name="periode_cutoff_tanggal" 
                                                        value="{{ old('periode_cutoff_tanggal', $setting->periode_cutoff_tanggal) }}" 
                                                        min="1" max="31" 
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 pr-32" 
                                                        {{ old('periode_auto_generate', $setting->periode_auto_generate) ? 'required' : 'disabled' }}
                                                        id="periode_cutoff_tanggal">
                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">setiap bulan</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">Tanggal akhir perhitungan gaji (1-31)</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pembayaran</label>
                                            <div class="flex items-center gap-4">
                                                <div class="relative max-w-xs">
                                                    <input type="number" name="periode_pembayaran_tanggal" 
                                                        value="{{ old('periode_pembayaran_tanggal', $setting->periode_pembayaran_tanggal) }}" 
                                                        min="1" max="31" 
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 pr-32" 
                                                        {{ old('periode_auto_generate', $setting->periode_auto_generate) ? 'required' : 'disabled' }}
                                                        id="periode_pembayaran_tanggal">
                                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">setiap bulan</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">Tanggal pembayaran gaji ke karyawan (1-31)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contoh Periode -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6" id="contoh-periode" style="{{ old('periode_auto_generate', $setting->periode_auto_generate) ? '' : 'display: none;' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-blue-900 mb-2">Contoh Periode</h4>
                                    <p class="text-xs text-blue-700 mb-2">Dengan cutoff tanggal <strong id="cutoff-display">{{ $setting->periode_cutoff_tanggal }}</strong> dan pembayaran tanggal <strong id="pembayaran-display">{{ $setting->periode_pembayaran_tanggal }}</strong>:</p>
                                    <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                                        <li>Periode: <span id="periode-range">26 bulan lalu - 25 bulan ini</span></li>
                                        <li>Gaji dibayarkan: Tanggal <span id="pembayaran-tanggal">1</span> bulan depan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('perusahaan.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-white rounded-lg font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-save"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active state to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-blue-500', 'text-blue-600');
}

function togglePeriodeInputs(checkbox) {
    const periodeInputs = document.getElementById('periode-inputs');
    const contohPeriode = document.getElementById('contoh-periode');
    const cutoffInput = document.getElementById('periode_cutoff_tanggal');
    const pembayaranInput = document.getElementById('periode_pembayaran_tanggal');
    
    if (checkbox.checked) {
        // Enable inputs
        periodeInputs.classList.remove('opacity-50', 'pointer-events-none');
        cutoffInput.disabled = false;
        pembayaranInput.disabled = false;
        cutoffInput.required = true;
        pembayaranInput.required = true;
        contohPeriode.style.display = 'block';
    } else {
        // Disable inputs
        periodeInputs.classList.add('opacity-50', 'pointer-events-none');
        cutoffInput.disabled = true;
        pembayaranInput.disabled = true;
        cutoffInput.required = false;
        pembayaranInput.required = false;
        contohPeriode.style.display = 'none';
    }
}

// Update contoh periode when inputs change
document.addEventListener('DOMContentLoaded', function() {
    const cutoffInput = document.getElementById('periode_cutoff_tanggal');
    const pembayaranInput = document.getElementById('periode_pembayaran_tanggal');
    
    if (cutoffInput && pembayaranInput) {
        cutoffInput.addEventListener('input', updateContohPeriode);
        pembayaranInput.addEventListener('input', updateContohPeriode);
    }
});

function updateContohPeriode() {
    const cutoff = parseInt(document.getElementById('periode_cutoff_tanggal').value) || 25;
    const pembayaran = parseInt(document.getElementById('periode_pembayaran_tanggal').value) || 1;
    
    // Update display
    document.getElementById('cutoff-display').textContent = cutoff;
    document.getElementById('pembayaran-display').textContent = pembayaran;
    document.getElementById('pembayaran-tanggal').textContent = pembayaran;
    
    // Calculate periode range
    const startDay = cutoff + 1;
    const endDay = cutoff;
    document.getElementById('periode-range').textContent = `${startDay} bulan lalu - ${endDay} bulan ini`;
}
</script>
@endpush

@endsection
