@extends('perusahaan.layouts.app')

@section('title', 'Detail Payroll')
@section('page-title', 'Detail Payroll')
@section('page-subtitle', 'Rincian Slip Gaji Karyawan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Detail Payroll -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Karyawan Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $payroll->karyawan->nama_lengkap }}</h2>
                        <p class="text-sm text-gray-600">{{ $payroll->karyawan->nik_karyawan }}</p>
                        <p class="text-sm text-gray-600">{{ $payroll->karyawan->jabatan->nama ?? '-' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    @if($payroll->status == 'draft')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-edit mr-1"></i>
                            Draft
                        </span>
                    @elseif($payroll->status == 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Approved
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-money-check-alt mr-1"></i>
                            Paid
                        </span>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($payroll->periode)->format('F Y') }}</p>
                    @if($payroll->periode_start && $payroll->periode_end)
                        <p class="text-xs text-blue-600 font-medium mt-0.5">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $payroll->periode_start->format('d M Y') }} - {{ $payroll->periode_end->format('d M Y') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">Project</p>
                    <p class="text-sm font-medium text-gray-900">{{ $payroll->project->nama }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">Tanggal Generate</p>
                    <p class="text-sm font-medium text-gray-900">{{ $payroll->tanggal_generate->format('d M Y') }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <p class="text-xs text-purple-600 mb-1">
                        <i class="fas fa-file-invoice-dollar mr-1"></i>
                        Status PTKP (Pajak)
                    </p>
                    <p class="text-sm font-bold text-purple-900">{{ $payroll->ptkp_status ?? 'TK/0' }}</p>
                    <p class="text-xs text-purple-600 mt-1">Rp {{ number_format($payroll->ptkp_value ?? 0, 0, ',', '.') }}/tahun</p>
                </div>
            </div>
        </div>

        <!-- Kehadiran -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-check text-blue-600"></i>
                Data Kehadiran
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $payroll->hari_kerja }}</p>
                    <p class="text-xs text-gray-600 mt-1">Hari Kerja</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $payroll->hari_masuk }}</p>
                    <p class="text-xs text-gray-600 mt-1">Hari Masuk</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $payroll->hari_alpha }}</p>
                    <p class="text-xs text-gray-600 mt-1">Alpha</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $payroll->hari_lembur }}</p>
                    <p class="text-xs text-gray-600 mt-1">Lembur</p>
                </div>
            </div>
            @if($payroll->hari_sakit > 0 || $payroll->hari_izin > 0 || $payroll->hari_cuti > 0)
                <div class="grid grid-cols-3 gap-4 mt-4">
                    @if($payroll->hari_sakit > 0)
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <p class="text-xl font-bold text-orange-600">{{ $payroll->hari_sakit }}</p>
                            <p class="text-xs text-gray-600 mt-1">Sakit</p>
                        </div>
                    @endif
                    @if($payroll->hari_izin > 0)
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <p class="text-xl font-bold text-yellow-600">{{ $payroll->hari_izin }}</p>
                            <p class="text-xs text-gray-600 mt-1">Izin</p>
                        </div>
                    @endif
                    @if($payroll->hari_cuti > 0)
                        <div class="bg-indigo-50 rounded-lg p-4 text-center">
                            <p class="text-xl font-bold text-indigo-600">{{ $payroll->hari_cuti }}</p>
                            <p class="text-xs text-gray-600 mt-1">Cuti</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Rincian Gaji -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-green-600"></i>
                Rincian Gaji
            </h3>

            @if($payroll->status == 'draft')
                <!-- Info Box for Editable Components -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Komponen yang Bisa Diedit</p>
                            <p class="text-xs">Komponen dengan icon <i class="fas fa-edit text-blue-500"></i> bisa diedit langsung dengan klik pada nilainya. Perubahan akan otomatis menghitung ulang total gaji.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Gaji Pokok -->
            <div class="space-y-3">
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Gaji Pokok</span>
                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</span>
                </div>

                <!-- Tunjangan -->
                @if($payroll->tunjangan_detail && count($payroll->tunjangan_detail) > 0)
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm font-semibold text-green-900 mb-2">Tunjangan</p>
                        @foreach($payroll->tunjangan_detail as $index => $tunjangan)
                            @php
                                // For existing payrolls without kode, use nama as fallback
                                $componentCode = $tunjangan['kode'] ?? $tunjangan['nama'];
                                $isEditable = isset($komponenPayrolls[$componentCode]) && $komponenPayrolls[$componentCode]->boleh_edit && $payroll->status == 'draft';
                            @endphp
                            <div class="flex items-center justify-between py-2 text-xs">
                                <span class="text-gray-700">
                                    {{ $tunjangan['nama'] }}
                                    @if($tunjangan['tipe'] == 'Persentase')
                                        ({{ number_format($tunjangan['nilai_dasar'], 0) }}%)
                                    @elseif($tunjangan['tipe'] == 'Per Hari Masuk')
                                        ({{ number_format($tunjangan['nilai_dasar'], 0) }} x {{ $payroll->hari_masuk }} hari)
                                    @elseif($tunjangan['tipe'] == 'Lembur Per Hari')
                                        ({{ number_format($tunjangan['nilai_dasar'], 0) }} x {{ $payroll->hari_lembur }} hari)
                                    @endif
                                    @if($isEditable)
                                        <i class="fas fa-edit text-blue-500 ml-1" title="Bisa diedit"></i>
                                    @endif
                                </span>
                                <div class="flex items-center gap-2">
                                    @if($isEditable)
                                        <div class="inline-edit-container" data-component-type="tunjangan" data-component-code="{{ $componentCode }}" data-component-index="{{ $index }}">
                                            <div class="view-mode">
                                                <span class="component-value font-medium text-green-700 cursor-pointer hover:bg-green-100 px-2 py-1 rounded" onclick="enableEdit(this)">
                                                    + Rp {{ number_format($tunjangan['nilai_hitung'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="edit-mode hidden">
                                                <input type="number" 
                                                       class="component-input w-24 px-2 py-1 text-xs border border-green-500 rounded focus:ring-1 focus:ring-green-500" 
                                                       value="{{ $tunjangan['nilai_hitung'] }}"
                                                       data-original="{{ $tunjangan['nilai_hitung'] }}"
                                                       onblur="saveEdit(this)"
                                                       onkeypress="handleKeyPress(event, this)">
                                            </div>
                                        </div>
                                    @else
                                        <span class="font-medium text-green-700">+ Rp {{ number_format($tunjangan['nilai_hitung'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-2 mt-2 border-t border-green-200">
                            <span class="text-sm font-semibold text-green-900">Total Tunjangan</span>
                            <span class="text-sm font-bold text-green-700" id="total-tunjangan">Rp {{ number_format($payroll->total_tunjangan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                <!-- BPJS -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm font-semibold text-blue-900 mb-2">BPJS (Ditanggung Perusahaan)</p>
                    <div class="flex items-center justify-between py-2 text-xs">
                        <span class="text-gray-700">BPJS Kesehatan</span>
                        <span class="font-medium text-blue-700">+ Rp {{ number_format($payroll->bpjs_kesehatan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 text-xs">
                        <span class="text-gray-700">BPJS Ketenagakerjaan (JHT + JP + JKK + JKM)</span>
                        <span class="font-medium text-blue-700">+ Rp {{ number_format($payroll->bpjs_ketenagakerjaan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 mt-2 border-t border-blue-200">
                        <span class="text-sm font-semibold text-blue-900">Total BPJS Perusahaan</span>
                        <span class="text-sm font-bold text-blue-700">+ Rp {{ number_format($payroll->bpjs_kesehatan + $payroll->bpjs_ketenagakerjaan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Gaji Bruto -->
                <div class="flex items-center justify-between py-3 bg-gray-100 rounded-lg px-4">
                    <span class="text-sm font-bold text-gray-900">Gaji Bruto</span>
                    <span class="text-lg font-bold text-gray-900" id="gaji-bruto">Rp {{ number_format($payroll->gaji_bruto, 0, ',', '.') }}</span>
                </div>

                <!-- Potongan -->
                @if($payroll->potongan_detail && count($payroll->potongan_detail) > 0)
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-sm font-semibold text-red-900 mb-2">Potongan</p>
                        @foreach($payroll->potongan_detail as $index => $potongan)
                            @php
                                // For existing payrolls without kode, use nama as fallback
                                $componentCode = $potongan['kode'] ?? $potongan['nama'];
                                $isEditable = isset($komponenPayrolls[$componentCode]) && $komponenPayrolls[$componentCode]->boleh_edit && $payroll->status == 'draft';
                            @endphp
                            <div class="flex items-center justify-between py-2 text-xs">
                                <span class="text-gray-700">
                                    {{ $potongan['nama'] }}
                                    @if($potongan['tipe'] == 'Persentase')
                                        ({{ number_format($potongan['nilai_dasar'], 0) }}%)
                                    @endif
                                    @if($isEditable)
                                        <i class="fas fa-edit text-blue-500 ml-1" title="Bisa diedit"></i>
                                    @endif
                                </span>
                                <div class="flex items-center gap-2">
                                    @if($isEditable)
                                        <div class="inline-edit-container" data-component-type="potongan" data-component-code="{{ $componentCode }}" data-component-index="{{ $index }}">
                                            <div class="view-mode">
                                                <span class="component-value font-medium text-red-700 cursor-pointer hover:bg-red-100 px-2 py-1 rounded" onclick="enableEdit(this)">
                                                    - Rp {{ number_format($potongan['nilai_hitung'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="edit-mode hidden">
                                                <input type="number" 
                                                       class="component-input w-24 px-2 py-1 text-xs border border-red-500 rounded focus:ring-1 focus:ring-red-500" 
                                                       value="{{ $potongan['nilai_hitung'] }}"
                                                       data-original="{{ $potongan['nilai_hitung'] }}"
                                                       onblur="saveEdit(this)"
                                                       onkeypress="handleKeyPress(event, this)">
                                            </div>
                                        </div>
                                    @else
                                        <span class="font-medium text-red-700">- Rp {{ number_format($potongan['nilai_hitung'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-2 mt-2 border-t border-red-200">
                            <span class="text-sm font-semibold text-red-900">Total Potongan</span>
                            <span class="text-sm font-bold text-red-700" id="total-potongan">Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Pajak -->
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Pajak PPh 21</span>
                    <span class="text-sm font-bold text-red-600">- Rp {{ number_format($payroll->pajak_pph21, 0, ',', '.') }}</span>
                </div>

                <!-- Gaji Netto -->
                <div class="flex items-center justify-between py-4 bg-gradient-to-r from-green-500 to-green-600 rounded-lg px-4">
                    <span class="text-base font-bold text-white">Gaji Netto (Take Home Pay)</span>
                    <span class="text-2xl font-bold text-white" id="gaji-netto">Rp {{ number_format($payroll->gaji_netto, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Actions & Info -->
    <div class="space-y-6">
        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
            <div class="space-y-3">
                @if($payroll->status == 'draft')
                    <button onclick="approvePayroll()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>
                        Approve Payroll
                    </button>
                    <button onclick="deletePayroll()" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Payroll
                    </button>
                @endif
                <button onclick="window.print()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-print mr-2"></i>
                    Print Slip Gaji
                </button>
                <a href="{{ route('perusahaan.daftar-payroll.index', ['periode' => $payroll->periode]) }}" class="block w-full px-4 py-3 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition text-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Approval Info -->
        @if($payroll->approved_at)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-green-900 mb-2">
                    <i class="fas fa-check-circle mr-1"></i>
                    Approved
                </h3>
                <div class="text-xs text-green-800 space-y-1">
                    <p><strong>Oleh:</strong> {{ $payroll->approvedBy->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $payroll->approved_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        @endif

        <!-- Payment Info -->
        @if($payroll->paid_at)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-blue-900 mb-2">
                    <i class="fas fa-money-check-alt mr-1"></i>
                    Paid
                </h3>
                <div class="text-xs text-blue-800 space-y-1">
                    <p><strong>Oleh:</strong> {{ $payroll->paidBy->name ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ $payroll->paid_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        @endif

        <!-- Catatan -->
        @if($payroll->catatan)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-yellow-900 mb-2">
                    <i class="fas fa-sticky-note mr-1"></i>
                    Catatan
                </h3>
                <p class="text-xs text-yellow-800">{{ $payroll->catatan }}</p>
            </div>
        @endif

        <!-- Rekap Absensi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-calendar-check mr-2" style="color: #3B82C8;"></i>
                Rekap Absensi
            </h3>
            @if($payroll->periode_start && $payroll->periode_end)
                <p class="text-xs text-gray-500 mb-4">
                    Periode: {{ $payroll->periode_start->format('d M Y') }} - {{ $payroll->periode_end->format('d M Y') }}
                </p>
            @else
                <p class="text-xs text-gray-500 mb-4">
                    Periode: {{ \Carbon\Carbon::parse($payroll->periode)->format('F Y') }}
                </p>
            @endif
            
            <!-- Legend -->
            <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-100 text-green-800 rounded flex items-center justify-center font-bold">H</span>
                    <span class="text-gray-600">Hadir</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-yellow-100 text-yellow-800 rounded flex items-center justify-center font-bold">T</span>
                    <span class="text-gray-600">Terlambat</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-orange-100 text-orange-800 rounded flex items-center justify-center font-bold">P</span>
                    <span class="text-gray-600">Pulang Cepat</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-100 text-blue-800 rounded flex items-center justify-center font-bold">I</span>
                    <span class="text-gray-600">Izin</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-purple-100 text-purple-800 rounded flex items-center justify-center font-bold">S</span>
                    <span class="text-gray-600">Sakit</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-red-100 text-red-800 rounded flex items-center justify-center font-bold">A</span>
                    <span class="text-gray-600">Alpha</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-100 text-indigo-800 rounded flex items-center justify-center font-bold">C</span>
                    <span class="text-gray-600">Cuti</span>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <!-- Days Header -->
                <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                    @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                        <div class="text-center py-2 text-xs font-semibold text-gray-600 border-r border-gray-200 last:border-r-0">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Calendar Days -->
                @php
                    // Use periode_start and periode_end if available, otherwise use periode month
                    if ($payroll->periode_start && $payroll->periode_end) {
                        $startOfPeriod = $payroll->periode_start->copy();
                        $endOfPeriod = $payroll->periode_end->copy();
                    } else {
                        $periodeDate = \Carbon\Carbon::parse($payroll->periode);
                        $startOfPeriod = $periodeDate->copy()->startOfMonth();
                        $endOfPeriod = $periodeDate->copy()->endOfMonth();
                    }
                    
                    // For calendar display, we need to show from start of week to end of week
                    $calendarStart = $startOfPeriod->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $calendarEnd = $endOfPeriod->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    
                    // Create kehadiran map by date
                    $kehadiranMap = [];
                    foreach($kehadirans as $k) {
                        $kehadiranMap[$k->tanggal->format('Y-m-d')] = $k;
                    }
                    
                    // Function to get status code
                    function getStatusCode($kehadiran) {
                        if (!$kehadiran) return '-';
                        
                        $status = $kehadiran->status;
                        
                        switch($status) {
                            case 'hadir': return 'H';
                            case 'terlambat': return 'T';
                            case 'pulang_cepat': return 'P';
                            case 'izin': return 'I';
                            case 'sakit': return 'S';
                            case 'alpa': return 'A';
                            case 'cuti': return 'C';
                            default: return '-';
                        }
                    }
                    
                    // Function to get status color
                    function getStatusColor($code) {
                        switch($code) {
                            case 'H': return 'bg-green-100 text-green-800 border-green-200';
                            case 'T': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
                            case 'P': return 'bg-orange-100 text-orange-800 border-orange-200';
                            case 'I': return 'bg-blue-100 text-blue-800 border-blue-200';
                            case 'S': return 'bg-purple-100 text-purple-800 border-purple-200';
                            case 'A': return 'bg-red-100 text-red-800 border-red-200';
                            case 'C': return 'bg-indigo-100 text-indigo-800 border-indigo-200';
                            default: return 'bg-gray-50 text-gray-400 border-gray-200';
                        }
                    }
                    
                    $currentDate = $calendarStart->copy();
                    $totalWeeks = $calendarStart->diffInWeeks($calendarEnd) + 1;
                @endphp

                @for($week = 0; $week < $totalWeeks; $week++)
                    <div class="grid grid-cols-7 border-b border-gray-200 last:border-b-0">
                        @for($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++)
                            @php
                                $isInPeriod = $currentDate->between($startOfPeriod, $endOfPeriod);
                                $dateKey = $currentDate->format('Y-m-d');
                                $kehadiran = $kehadiranMap[$dateKey] ?? null;
                                $statusCode = getStatusCode($kehadiran);
                                $statusColor = getStatusColor($statusCode);
                            @endphp
                            
                            <div class="border-r border-gray-200 last:border-r-0 p-2 min-h-[60px] {{ $isInPeriod ? 'bg-white' : 'bg-gray-50' }}">
                                <div class="text-xs {{ $isInPeriod ? 'text-gray-700 font-medium' : 'text-gray-400' }} mb-1">
                                    {{ $currentDate->day }}
                                </div>
                                @if($isInPeriod)
                                    <div class="flex items-center justify-center">
                                        <span class="w-8 h-8 {{ $statusColor }} rounded border flex items-center justify-center font-bold text-sm">
                                            {{ $statusCode }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            @php $currentDate->addDay(); @endphp
                        @endfor
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function approvePayroll() {
    Swal.fire({
        title: 'Konfirmasi Approve',
        text: 'Approve payroll ini? Payroll yang sudah di-approve tidak bisa diubah lagi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.daftar-payroll.approve", $payroll->hash_id) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function deletePayroll() {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Payroll ini akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perusahaan.daftar-payroll.destroy", $payroll->hash_id) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Inline editing functions
function enableEdit(element) {
    const container = element.closest('.inline-edit-container');
    const viewMode = container.querySelector('.view-mode');
    const editMode = container.querySelector('.edit-mode');
    const input = container.querySelector('.component-input');
    
    viewMode.classList.add('hidden');
    editMode.classList.remove('hidden');
    
    setTimeout(() => {
        input.focus();
        input.select();
    }, 100);
}

function handleKeyPress(event, input) {
    if (event.key === 'Enter') {
        input.blur(); // This will trigger saveEdit
    } else if (event.key === 'Escape') {
        cancelEdit(input);
    }
}

function cancelEdit(input) {
    const container = input.closest('.inline-edit-container');
    const viewMode = container.querySelector('.view-mode');
    const editMode = container.querySelector('.edit-mode');
    
    // Reset to original value
    input.value = input.dataset.original;
    
    viewMode.classList.remove('hidden');
    editMode.classList.add('hidden');
}

function saveEdit(input) {
    const container = input.closest('.inline-edit-container');
    const componentType = container.dataset.componentType;
    const componentCode = container.dataset.componentCode;
    const componentIndex = container.dataset.componentIndex;
    const newValue = parseFloat(input.value) || 0;
    const originalValue = parseFloat(input.dataset.original) || 0;
    
    // If no change, just cancel edit
    if (newValue === originalValue) {
        cancelEdit(input);
        return;
    }
    
    // Show loading
    const viewMode = container.querySelector('.view-mode');
    const editMode = container.querySelector('.edit-mode');
    
    editMode.innerHTML = '<span class="text-xs text-gray-500">Menyimpan...</span>';
    
    // Prepare request body
    const requestBody = {
        component_type: componentType,
        component_code: componentCode,
        new_value: newValue
    };
    
    // Add component index if available
    if (componentIndex !== undefined) {
        requestBody.component_index = parseInt(componentIndex);
    }
    
    // Send AJAX request
    fetch('{{ route("perusahaan.daftar-payroll.update-component", $payroll->hash_id) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the display value
            const valueSpan = container.querySelector('.component-value');
            const prefix = componentType === 'tunjangan' ? '+ ' : '- ';
            valueSpan.textContent = prefix + data.data.new_value_formatted;
            
            // Update original value for future edits
            input.dataset.original = data.data.new_value;
            input.value = data.data.new_value;
            
            // Update totals
            const totalElement = document.getElementById('total-' + componentType);
            if (totalElement) {
                totalElement.textContent = data.data.new_total_formatted;
            }
            
            // Update gaji bruto and netto
            const gajiBrutoElement = document.getElementById('gaji-bruto');
            const gajiNettoElement = document.getElementById('gaji-netto');
            
            if (gajiBrutoElement) {
                gajiBrutoElement.textContent = data.data.new_gaji_bruto_formatted;
            }
            
            if (gajiNettoElement) {
                gajiNettoElement.textContent = data.data.new_gaji_netto_formatted;
            }
            
            // Reset edit mode
            editMode.innerHTML = `
                <input type="number" 
                       class="component-input w-24 px-2 py-1 text-xs border border-${componentType === 'tunjangan' ? 'green' : 'red'}-500 rounded focus:ring-1 focus:ring-${componentType === 'tunjangan' ? 'green' : 'red'}-500" 
                       value="${data.data.new_value}"
                       data-original="${data.data.new_value}"
                       onblur="saveEdit(this)"
                       onkeypress="handleKeyPress(event, this)">
            `;
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Switch back to view mode
            viewMode.classList.remove('hidden');
            editMode.classList.add('hidden');
            
        } else {
            // Show error and revert
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message
            });
            
            // Reset edit mode
            editMode.innerHTML = `
                <input type="number" 
                       class="component-input w-24 px-2 py-1 text-xs border border-${componentType === 'tunjangan' ? 'green' : 'red'}-500 rounded focus:ring-1 focus:ring-${componentType === 'tunjangan' ? 'green' : 'red'}-500" 
                       value="${originalValue}"
                       data-original="${originalValue}"
                       onblur="saveEdit(this)"
                       onkeypress="handleKeyPress(event, this)">
            `;
            
            cancelEdit(editMode.querySelector('.component-input'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan'
        });
        
        // Reset edit mode
        editMode.innerHTML = `
            <input type="number" 
                   class="component-input w-24 px-2 py-1 text-xs border border-${componentType === 'tunjangan' ? 'green' : 'red'}-500 rounded focus:ring-1 focus:ring-${componentType === 'tunjangan' ? 'green' : 'red'}-500" 
                   value="${originalValue}"
                   data-original="${originalValue}"
                   onblur="saveEdit(this)"
                   onkeypress="handleKeyPress(event, this)">
        `;
        
        cancelEdit(editMode.querySelector('.component-input'));
    });
}

// Add click outside to cancel edit
document.addEventListener('click', function(event) {
    const editContainers = document.querySelectorAll('.inline-edit-container .edit-mode:not(.hidden)');
    
    editContainers.forEach(editMode => {
        if (!editMode.contains(event.target) && !editMode.closest('.inline-edit-container').querySelector('.view-mode').contains(event.target)) {
            const input = editMode.querySelector('.component-input');
            if (input) {
                cancelEdit(input);
            }
        }
    });
});
</script>
@endpush
