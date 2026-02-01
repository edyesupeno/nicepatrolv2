@extends('perusahaan.layouts.app')

@section('title', 'Detail Cash Advance - ' . $cashAdvance->nomor_ca)
@section('page-title', 'Detail Cash Advance')
@section('page-subtitle', $cashAdvance->nomor_ca)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center">
        <a href="{{ route('perusahaan.keuangan.cash-advance.index') }}" 
           class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $cashAdvance->nomor_ca }}</h1>
                    <p class="text-gray-500">Cash Advance Details</p>
                </div>
            </div>
            <div>
                @php
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-blue-100 text-blue-800',
                        'active' => 'bg-green-100 text-green-800',
                        'need_report' => 'bg-red-100 text-red-800',
                        'completed' => 'bg-gray-100 text-gray-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Approval',
                        'approved' => 'Disetujui',
                        'active' => 'Aktif',
                        'need_report' => 'Perlu Laporan',
                        'completed' => 'Selesai',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClasses[$cashAdvance->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$cashAdvance->status] ?? $cashAdvance->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Karyawan:</span>
                    <span class="text-sm text-gray-900">{{ $cashAdvance->karyawan->nama_lengkap }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">NIK:</span>
                    <span class="text-sm text-gray-900">{{ $cashAdvance->karyawan->nik_karyawan }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Project:</span>
                    <span class="text-sm text-gray-900">{{ $cashAdvance->project->nama }}</span>
                </div>
                @if($cashAdvance->rekening)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Rekening Sumber:</span>
                        <span class="text-sm text-gray-900">{{ $cashAdvance->rekening->nama_rekening }} ({{ $cashAdvance->rekening->nomor_rekening }})</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Jumlah Pengajuan:</span>
                    <span class="text-sm font-bold text-blue-600">Rp {{ number_format($cashAdvance->jumlah_pengajuan, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Tanggal Pengajuan:</span>
                    <span class="text-sm text-gray-900">{{ $cashAdvance->tanggal_pengajuan->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Batas Pertanggungjawaban:</span>
                    <span class="text-sm text-gray-900">
                        {{ $cashAdvance->batas_pertanggungjawaban ? $cashAdvance->batas_pertanggungjawaban->format('d/m/Y') : '-' }}
                        @if($cashAdvance->batas_pertanggungjawaban && $cashAdvance->batas_pertanggungjawaban < now() && in_array($cashAdvance->status, ['active', 'need_report']))
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-2">Terlambat</span>
                        @endif
                    </span>
                </div>
                @if($cashAdvance->tanggal_approved)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Tanggal Approved:</span>
                        <span class="text-sm text-gray-900">{{ $cashAdvance->tanggal_approved->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Approved By:</span>
                        <span class="text-sm text-gray-900">{{ $cashAdvance->approvedBy->name ?? '-' }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Keperluan:</h3>
            <p class="text-gray-900">{{ $cashAdvance->keperluan }}</p>
        </div>

        @if($cashAdvance->catatan_approval)
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">Catatan Approval:</h4>
                <p class="text-blue-700">{{ $cashAdvance->catatan_approval }}</p>
            </div>
        @endif

        @if($cashAdvance->catatan_reject)
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="text-sm font-semibold text-red-800 mb-2">Alasan Penolakan:</h4>
                <p class="text-red-700">{{ $cashAdvance->catatan_reject }}</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mt-6">
            @if($cashAdvance->status === 'pending')
                <button type="button" onclick="approveCA('{{ $cashAdvance->hash_id }}')" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Approve & Aktifkan
                </button>
                <button type="button" onclick="rejectCA('{{ $cashAdvance->hash_id }}')" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Tolak
                </button>
            @endif

            @if(in_array($cashAdvance->status, ['approved', 'active']))
                @if($cashAdvance->sisa_saldo > 0)
                    <button type="button" onclick="toggleExpenseForm()" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pengeluaran
                    </button>
                @else
                    <button type="button" onclick="showBalanceEmptyAlert()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed" disabled>
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pengeluaran (Saldo Habis)
                    </button>
                @endif
                <button type="button" onclick="createReport('{{ $cashAdvance->hash_id }}')" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>
                    Buat Laporan Pertanggungjawaban
                </button>
            @endif

            @if(in_array($cashAdvance->status, ['active', 'completed', 'need_report']))
                <button type="button" onclick="printReport('{{ $cashAdvance->hash_id }}')" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Print Laporan
                </button>
            @endif

            @if(in_array($cashAdvance->status, ['active', 'need_report']) && $cashAdvance->sisa_saldo > 0)
                <button type="button" onclick="returnBalance('{{ $cashAdvance->hash_id }}')" 
                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-undo mr-2"></i>
                    Kembalikan Saldo
                </button>
            @endif
        </div>
    </div>

    <!-- Saldo Information (if active) -->
    @if(in_array($cashAdvance->status, ['active', 'need_report', 'completed']))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Saldo Tersedia</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($cashAdvance->saldo_tersedia, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-minus-circle text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Terpakai</p>
                        <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($cashAdvance->total_terpakai, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 {{ $cashAdvance->sisa_saldo > 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins {{ $cashAdvance->sisa_saldo > 0 ? 'text-green-600' : 'text-red-600' }} text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Sisa Saldo</p>
                        <p class="text-2xl font-bold {{ $cashAdvance->sisa_saldo > 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($cashAdvance->sisa_saldo, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Persentase Terpakai</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $cashAdvance->saldo_tersedia > 0 ? round(($cashAdvance->total_terpakai / $cashAdvance->saldo_tersedia) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Expense Form (if approved/active) -->
    @if(in_array($cashAdvance->status, ['approved', 'active', 'need_report']) && ($cashAdvance->sisa_saldo > 0 || $cashAdvance->status === 'approved'))
        <div id="expenseFormSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-receipt text-red-600"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Tambah Bukti Pengeluaran</h2>
                </div>
                <button type="button" onclick="toggleExpenseForm()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('perusahaan.keuangan.cash-advance.add-expense', $cashAdvance->hash_id) }}" method="POST" enctype="multipart/form-data" id="expenseForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-calendar text-blue-500 mr-2"></i>
                            Tanggal Transaksi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_transaksi" required
                               value="{{ old('tanggal_transaksi', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_transaksi') border-red-500 @enderror">
                        @error('tanggal_transaksi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-money-bill text-green-500 mr-2"></i>
                            Jumlah Pengeluaran <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="text" name="jumlah" id="jumlahPengeluaran" required
                                   value="{{ old('jumlah') }}"
                                   placeholder="0"
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jumlah') border-red-500 @enderror">
                        </div>
                        @error('jumlah')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Sisa saldo: <span class="font-semibold text-green-600">Rp {{ number_format($cashAdvance->sisa_saldo, 0, ',', '.') }}</span>
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-align-left text-purple-500 mr-2"></i>
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="keterangan" rows="3" required
                                  placeholder="Jelaskan untuk apa pengeluaran ini..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-camera text-indigo-500 mr-2"></i>
                            Bukti Transaksi (Foto) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="bukti_transaksi" accept="image/*" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bukti_transaksi') border-red-500 @enderror">
                        @error('bukti_transaksi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Upload foto struk, nota, atau bukti transaksi lainnya. Format: JPG, PNG. Maksimal 2MB.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Transactions History -->
    @if($cashAdvance->transactions->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-history mr-2"></i>
                    Riwayat Transaksi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Sesudah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cashAdvance->transactions as $index => $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-100 text-gray-600 text-xs font-medium rounded-full mr-2">
                                            {{ $index + 1 }}
                                        </span>
                                        {{ $transaction->nomor_transaksi }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $tipeClasses = [
                                            'pencairan' => 'bg-green-100 text-green-800',
                                            'pengeluaran' => 'bg-red-100 text-red-800',
                                            'pengembalian' => 'bg-blue-100 text-blue-800',
                                        ];
                                        $tipeLabels = [
                                            'pencairan' => 'Pencairan',
                                            'pengeluaran' => 'Pengeluaran',
                                            'pengembalian' => 'Pengembalian',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $tipeClasses[$transaction->tipe] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $tipeLabels[$transaction->tipe] ?? $transaction->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $transaction->tanggal_transaksi->format('d/m/Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i:s') }} WIB</span>
                                        <span class="text-xs text-gray-400">{{ $transaction->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->tipe === 'pengeluaran' ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $transaction->tipe === 'pengeluaran' ? '-' : '+' }}
                                    Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->keterangan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($transaction->saldo_sesudah, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($transaction->bukti_transaksi)
                                        <a href="{{ $transaction->bukti_transaksi_url }}" target="_blank" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-file-image"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Reports History -->
    @if($cashAdvance->reports->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-alt mr-2"></i>
                    Laporan Pertanggungjawaban
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Laporan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pengeluaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Saldo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cashAdvance->reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->nomor_laporan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->tanggal_laporan->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($report->total_pengeluaran, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($report->sisa_saldo, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $reportStatusClasses = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'submitted' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $reportStatusLabels = [
                                            'draft' => 'Draft',
                                            'submitted' => 'Menunggu Approval',
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $reportStatusClasses[$report->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $reportStatusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($report->file_laporan)
                                        <a href="{{ $report->file_laporan_url }}" target="_blank" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Cash Advance</h3>
            
            <!-- Account Transfer Information -->
            @if($cashAdvance->rekening)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <h4 class="text-sm font-semibold text-blue-800">Informasi Transfer Dana</h4>
                    </div>
                    <div class="space-y-2 text-sm text-blue-700">
                        <div class="flex justify-between">
                            <span class="font-medium">Rekening Sumber:</span>
                            <span>{{ $cashAdvance->rekening->nama_rekening }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Nomor Rekening:</span>
                            <span>{{ $cashAdvance->rekening->nomor_rekening }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Saldo Saat Ini:</span>
                            <span class="font-semibold">Rp {{ number_format($cashAdvance->rekening->saldo_saat_ini, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Jumlah Transfer:</span>
                            <span class="font-semibold text-red-600">Rp {{ number_format($cashAdvance->jumlah_pengajuan, 0, ',', '.') }}</span>
                        </div>
                        <hr class="border-blue-300">
                        <div class="flex justify-between">
                            <span class="font-medium">Saldo Setelah Transfer:</span>
                            <span class="font-semibold {{ ($cashAdvance->rekening->saldo_saat_ini - $cashAdvance->jumlah_pengajuan) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($cashAdvance->rekening->saldo_saat_ini - $cashAdvance->jumlah_pengajuan, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    
                    @if(($cashAdvance->rekening->saldo_saat_ini - $cashAdvance->jumlah_pengajuan) < 0)
                        <div class="mt-3 p-2 bg-red-100 border border-red-300 rounded text-red-700 text-sm">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Peringatan:</strong> Saldo rekening tidak mencukupi untuk transfer ini!
                        </div>
                    @endif
                </div>
            @endif
            
            <form id="approveForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Approval (Opsional)</label>
                    <textarea name="catatan_approval" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('approveModal')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
                            @if($cashAdvance->rekening && ($cashAdvance->rekening->saldo_saat_ini - $cashAdvance->jumlah_pengajuan) < 0) disabled @endif>
                        <i class="fas fa-check mr-1"></i>
                        Approve & Transfer Dana
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Cash Advance</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_reject" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('rejectModal')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Report Modal -->
<div id="createReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Laporan Pertanggungjawaban</h3>
            
            <!-- Summary Information -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center mb-3">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <h4 class="text-sm font-semibold text-blue-800">Ringkasan Cash Advance</h4>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm text-blue-700">
                    <div>
                        <span class="font-medium">Total Saldo:</span>
                        <span class="float-right">Rp {{ number_format($cashAdvance->saldo_tersedia, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Total Terpakai:</span>
                        <span class="float-right">Rp {{ number_format($cashAdvance->total_terpakai, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Sisa Saldo:</span>
                        <span class="float-right font-semibold">Rp {{ number_format($cashAdvance->sisa_saldo, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Jumlah Transaksi:</span>
                        <span class="float-right">{{ $cashAdvance->transactions->where('tipe', 'pengeluaran')->count() }} transaksi</span>
                    </div>
                </div>
            </div>
            
            <form id="createReportForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Laporan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_laporan" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ringkasan Penggunaan <span class="text-red-500">*</span></label>
                        <textarea name="ringkasan_penggunaan" rows="4" required
                                  placeholder="Jelaskan secara ringkas bagaimana dana Cash Advance digunakan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Laporan (PDF) <span class="text-red-500">*</span></label>
                        <input type="file" name="file_laporan" accept=".pdf" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Upload file laporan dalam format PDF. Maksimal 5MB.</p>
                    </div>
                    
                    @if($cashAdvance->sisa_saldo > 0)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                <h4 class="text-sm font-semibold text-yellow-800">Sisa Saldo</h4>
                            </div>
                            <p class="text-sm text-yellow-700 mb-3">
                                Anda masih memiliki sisa saldo sebesar <strong>Rp {{ number_format($cashAdvance->sisa_saldo, 0, ',', '.') }}</strong>. 
                                Pilih tindakan untuk sisa saldo ini:
                            </p>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="tindakan_sisa_saldo" value="kembalikan" class="mr-2" checked>
                                    <span class="text-sm">Kembalikan ke rekening perusahaan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="tindakan_sisa_saldo" value="lanjutkan" class="mr-2">
                                    <span class="text-sm">Lanjutkan penggunaan (Cash Advance tetap aktif)</span>
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('createReportModal')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-alt mr-1"></i>
                        Buat Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahInput = document.getElementById('jumlahPengeluaran');
    
    // Format input jumlah pengeluaran
    if (jumlahInput) {
        jumlahInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value) {
                e.target.value = new Intl.NumberFormat('id-ID').format(value);
            }
        });

        // Handle form submission
        document.getElementById('expenseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Convert formatted number back to plain number
            const jumlahFormatted = jumlahInput.value.replace(/[^\d]/g, '');
            jumlahInput.value = jumlahFormatted;
            
            Swal.fire({
                title: 'Konfirmasi Pengeluaran',
                text: 'Apakah data pengeluaran sudah benar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tambahkan!',
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
    }
});

function toggleExpenseForm() {
    const formSection = document.getElementById('expenseFormSection');
    if (formSection.classList.contains('hidden')) {
        formSection.classList.remove('hidden');
        formSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        formSection.classList.add('hidden');
    }
}

function approveCA(hashId) {
    const form = document.getElementById('approveForm');
    form.action = `/perusahaan/keuangan/cash-advance/${hashId}/approve`;
    
    document.getElementById('approveModal').classList.remove('hidden');
}

function rejectCA(hashId) {
    const form = document.getElementById('rejectForm');
    form.action = `/perusahaan/keuangan/cash-advance/${hashId}/reject`;
    
    document.getElementById('rejectModal').classList.remove('hidden');
}

function createReport(hashId) {
    const form = document.getElementById('createReportForm');
    form.action = `/perusahaan/keuangan/cash-advance/${hashId}/create-report`;
    
    document.getElementById('createReportModal').classList.remove('hidden');
}

function returnBalance(hashId) {
    Swal.fire({
        title: 'Kembalikan Sisa Saldo?',
        text: 'Sisa saldo akan dikembalikan ke rekening dan Cash Advance akan diselesaikan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kembalikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/keuangan/cash-advance/${hashId}/return-balance`;
            
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

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function showBalanceEmptyAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Saldo Habis!',
        text: 'Saldo Cash Advance sudah habis terpakai. Tidak dapat menambah pengeluaran lagi.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6'
    });
}

function printReport(hashId) {
    // Open print page in new window
    const printUrl = `/perusahaan/keuangan/cash-advance/${hashId}/print`;
    window.open(printUrl, '_blank');
}

// Handle form submissions with SweetAlert
document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('createReportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Konfirmasi Laporan',
        text: 'Apakah data laporan sudah benar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Buat Laporan!',
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
        }
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');
    const createReportModal = document.getElementById('createReportModal');
    
    if (event.target == approveModal) {
        approveModal.classList.add('hidden');
    }
    if (event.target == rejectModal) {
        rejectModal.classList.add('hidden');
    }
    if (event.target == createReportModal) {
        createReportModal.classList.add('hidden');
    }
}
</script>
@endpush