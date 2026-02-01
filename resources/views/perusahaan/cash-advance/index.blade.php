@extends('perusahaan.layouts.app')

@section('title', 'Cash Advance')
@section('page-title', 'Manajemen Cash Advance')
@section('page-subtitle', 'Kelola pengajuan dan proses Cash Advance karyawan')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 40px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 0 12px !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px !important;
    padding-left: 0 !important;
    color: #374151 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 12px !important;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
}

.select2-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

.select2-results__option {
    padding: 8px 12px !important;
}

.select2-results__option--highlighted {
    background-color: #3b82f6 !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db !important;
    border-radius: 0.375rem !important;
    padding: 6px 10px !important;
}
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-end">
        <a href="{{ route('perusahaan.keuangan.cash-advance.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i>
            Pengajuan Baru
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Cash Advance</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cashAdvances->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cashAdvances->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cashAdvances->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Perlu Laporan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cashAdvances->where('status', 'need_report')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-double text-gray-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cashAdvances->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('perusahaan.keuangan.cash-advance.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Project Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i>
                        Project
                    </label>
                    <select name="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Karyawan Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i>
                        Karyawan
                    </label>
                    <select name="karyawan_id" id="karyawan_filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Karyawan</option>
                        @if(request('karyawan_id'))
                            @php
                                $selectedKaryawan = \App\Models\Karyawan::find(request('karyawan_id'));
                            @endphp
                            @if($selectedKaryawan)
                                <option value="{{ $selectedKaryawan->id }}" selected>
                                    {{ $selectedKaryawan->nama_lengkap }} ({{ $selectedKaryawan->nik_karyawan }})
                                </option>
                            @endif
                        @endif
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-1"></i>
                        Status
                    </label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="need_report" {{ request('status') == 'need_report' ? 'selected' : '' }}>Perlu Laporan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('perusahaan.keuangan.cash-advance.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Cash Advance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Cash Advance</h3>
        </div>

        @if($cashAdvances->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. CA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Saldo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cashAdvances as $ca)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $ca->nomor_ca }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ca->karyawan->nama_lengkap }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ca->project->nama }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Rp {{ number_format($ca->jumlah_pengajuan, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ca->status === 'active' || $ca->status === 'need_report')
                                        <div class="text-sm font-medium {{ $ca->sisa_saldo > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Rp {{ number_format($ca->sisa_saldo, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$ca->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$ca->status] ?? $ca->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ca->tanggal_pengajuan->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('perusahaan.keuangan.cash-advance.show', $ca->hash_id) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($ca->status === 'pending')
                                            <button type="button" onclick="approveCA('{{ $ca->hash_id }}')" 
                                                    data-rekening-nama="{{ $ca->rekening->nama_rekening ?? '-' }}"
                                                    data-rekening-nomor="{{ $ca->rekening->nomor_rekening ?? '-' }}"
                                                    data-saldo-saat-ini="{{ $ca->rekening->saldo_saat_ini ?? 0 }}"
                                                    data-jumlah-pengajuan="{{ $ca->jumlah_pengajuan }}"
                                                    class="text-green-600 hover:text-green-900 transition-colors approve-btn"
                                                    title="Approve & Aktifkan">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" onclick="rejectCA('{{ $ca->hash_id }}')" 
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        @if(in_array($ca->status, ['approved', 'active']))
                                            @if($ca->sisa_saldo > 0)
                                                <button type="button" onclick="window.location.href='{{ route('perusahaan.keuangan.cash-advance.show', $ca->hash_id) }}'" 
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Tambah Pengeluaran">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @else
                                                <button type="button" onclick="showBalanceEmptyAlert()" 
                                                        class="text-gray-400 cursor-not-allowed"
                                                        title="Saldo Habis - Tidak dapat menambah pengeluaran" disabled>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                            <button type="button" onclick="window.location.href='{{ route('perusahaan.keuangan.cash-advance.show', $ca->hash_id) }}'" 
                                                    class="text-blue-600 hover:text-blue-900 transition-colors"
                                                    title="Buat Laporan">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        @endif

                                        @if(in_array($ca->status, ['active', 'completed', 'need_report']))
                                            <button type="button" onclick="window.open('{{ route('perusahaan.keuangan.cash-advance.print', $ca->hash_id) }}', '_blank')" 
                                                    class="text-green-600 hover:text-green-900 transition-colors"
                                                    title="Print Laporan">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($cashAdvances->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $cashAdvances->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                    <i class="fas fa-inbox text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada Cash Advance</h3>
                <p class="text-gray-500 mb-6">Mulai dengan membuat pengajuan Cash Advance pertama.</p>
                <a href="{{ route('perusahaan.keuangan.cash-advance.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Pengajuan Baru
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Cash Advance</h3>
            
            <!-- Account Transfer Information -->
            <div id="accountInfo" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <div class="flex items-center mb-3">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <h4 class="text-sm font-semibold text-blue-800">Informasi Transfer Dana</h4>
                </div>
                <div class="space-y-2 text-sm text-blue-700">
                    <div class="flex justify-between">
                        <span class="font-medium">Rekening Sumber:</span>
                        <span id="rekeningNama">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Nomor Rekening:</span>
                        <span id="rekeningNomor">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Saldo Saat Ini:</span>
                        <span class="font-semibold" id="saldoSaatIni">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Jumlah Transfer:</span>
                        <span class="font-semibold text-red-600" id="jumlahTransfer">-</span>
                    </div>
                    <hr class="border-blue-300">
                    <div class="flex justify-between">
                        <span class="font-medium">Saldo Setelah Transfer:</span>
                        <span class="font-semibold" id="saldoSetelah">-</span>
                    </div>
                </div>
                
                <div id="warningInsufficient" class="mt-3 p-2 bg-red-100 border border-red-300 rounded text-red-700 text-sm hidden">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Peringatan:</strong> Saldo rekening tidak mencukupi untuk transfer ini!
                </div>
            </div>
            
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
                    <button type="submit" id="approveButton"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for karyawan filter
    $('#karyawan_filter').select2({
        placeholder: 'Ketik nama atau NIK karyawan...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("perusahaan.keuangan.cash-advance.search-karyawan") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term
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
});

function approveCA(hashId) {
    const form = document.getElementById('approveForm');
    form.action = `/perusahaan/keuangan/cash-advance/${hashId}/approve`;
    
    // Find the button that was clicked to get the data attributes
    const button = event.target.closest('.approve-btn');
    if (button) {
        const rekeningNama = button.dataset.rekeningNama;
        const rekeningNomor = button.dataset.rekeningNomor;
        const saldoSaatIni = parseFloat(button.dataset.saldoSaatIni);
        const jumlahPengajuan = parseFloat(button.dataset.jumlahPengajuan);
        const saldoSetelah = saldoSaatIni - jumlahPengajuan;
        
        // Show account info
        document.getElementById('accountInfo').classList.remove('hidden');
        document.getElementById('rekeningNama').textContent = rekeningNama;
        document.getElementById('rekeningNomor').textContent = rekeningNomor;
        document.getElementById('saldoSaatIni').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldoSaatIni);
        document.getElementById('jumlahTransfer').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(jumlahPengajuan);
        
        // Update saldo setelah with color
        const saldoSetelahElement = document.getElementById('saldoSetelah');
        saldoSetelahElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(saldoSetelah);
        saldoSetelahElement.className = saldoSetelah >= 0 ? 'font-semibold text-green-600' : 'font-semibold text-red-600';
        
        // Show/hide warning and disable/enable button
        const warningElement = document.getElementById('warningInsufficient');
        const approveButton = document.getElementById('approveButton');
        
        if (saldoSetelah < 0) {
            warningElement.classList.remove('hidden');
            approveButton.disabled = true;
            approveButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            warningElement.classList.add('hidden');
            approveButton.disabled = false;
            approveButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    document.getElementById('approveModal').classList.remove('hidden');
}

function rejectCA(hashId) {
    const form = document.getElementById('rejectForm');
    form.action = `/perusahaan/keuangan/cash-advance/${hashId}/reject`;
    
    document.getElementById('rejectModal').classList.remove('hidden');
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

// Close modal when clicking outside
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');
    
    if (event.target == approveModal) {
        approveModal.classList.add('hidden');
    }
    if (event.target == rejectModal) {
        rejectModal.classList.add('hidden');
    }
}
</script>
@endpush