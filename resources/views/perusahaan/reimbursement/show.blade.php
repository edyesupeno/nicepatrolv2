@extends('perusahaan.layouts.app')

@section('title', 'Detail Reimbursement - ' . $reimbursement->nomor_reimbursement)
@section('page-title', $reimbursement->nomor_reimbursement)
@section('page-subtitle', $reimbursement->judul_pengajuan)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center">
        <a href="{{ route('perusahaan.keuangan.reimbursement.index') }}" 
           class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm border border-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

        <!-- Status & Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $reimbursement->getStatusBadgeClass() }}">
                        {{ $reimbursement->status_label }}
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $reimbursement->getPrioritasBadgeClass() }}">
                        {{ $reimbursement->prioritas_label }}
                    </span>
                    @if($reimbursement->is_urgent)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Urgent
                        </span>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    @if($reimbursement->canBeEdited())
                        <a href="{{ route('perusahaan.keuangan.reimbursement.edit', $reimbursement->hash_id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                    @endif
                    @if($reimbursement->canBeSubmitted())
                        <form action="{{ route('perusahaan.keuangan.reimbursement.submit', $reimbursement->hash_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin ingin submit reimbursement ini?')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit
                            </button>
                        </form>
                    @endif
                    @if($reimbursement->canBeCancelled())
                        <form action="{{ route('perusahaan.keuangan.reimbursement.cancel', $reimbursement->hash_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin ingin batalkan reimbursement ini?')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Pengajuan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Project</label>
                            <p class="text-gray-900 font-medium">{{ $reimbursement->project->nama ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Karyawan</label>
                            <p class="text-gray-900 font-medium">{{ $reimbursement->karyawan->nama_lengkap ?? 'N/A' }}</p>
                            @if($reimbursement->karyawan)
                                <p class="text-sm text-gray-500">{{ $reimbursement->karyawan->nik_karyawan ?? '' }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                            <p class="text-gray-900 font-medium">{{ $reimbursement->kategori_label }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Pengajuan</label>
                            <p class="text-gray-900 font-medium">{{ $reimbursement->tanggal_pengajuan->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Kejadian</label>
                            <p class="text-gray-900 font-medium">{{ $reimbursement->tanggal_kejadian->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Diajukan Oleh</label>
                            <p class="text-gray-900 font-medium">{{ $reimbursement->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $reimbursement->deskripsi }}</p>
                    </div>
                </div>

                <!-- Notes -->
                @if($reimbursement->catatan_pengaju || $reimbursement->catatan_reviewer || $reimbursement->catatan_approver || $reimbursement->alasan_penolakan)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Catatan</h2>
                    <div class="space-y-4">
                        @if($reimbursement->catatan_pengaju)
                            <div class="border-l-4 border-blue-400 pl-4">
                                <h3 class="font-medium text-gray-900">Catatan Pengaju</h3>
                                <p class="text-gray-700 mt-1">{{ $reimbursement->catatan_pengaju }}</p>
                            </div>
                        @endif
                        @if($reimbursement->catatan_reviewer)
                            <div class="border-l-4 border-yellow-400 pl-4">
                                <h3 class="font-medium text-gray-900">Catatan Reviewer</h3>
                                <p class="text-gray-700 mt-1">{{ $reimbursement->catatan_reviewer }}</p>
                                @if($reimbursement->reviewedBy)
                                    <p class="text-sm text-gray-500 mt-1">
                                        oleh {{ $reimbursement->reviewedBy->name }} - {{ $reimbursement->reviewed_at->format('d F Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                        @if($reimbursement->catatan_approver)
                            <div class="border-l-4 border-green-400 pl-4">
                                <h3 class="font-medium text-gray-900">Catatan Approver</h3>
                                <p class="text-gray-700 mt-1">{{ $reimbursement->catatan_approver }}</p>
                                @if($reimbursement->approvedBy)
                                    <p class="text-sm text-gray-500 mt-1">
                                        oleh {{ $reimbursement->approvedBy->name }} - {{ $reimbursement->approved_at->format('d F Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                        @if($reimbursement->alasan_penolakan)
                            <div class="border-l-4 border-red-400 pl-4">
                                <h3 class="font-medium text-gray-900">Alasan Penolakan</h3>
                                <p class="text-gray-700 mt-1">{{ $reimbursement->alasan_penolakan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Documents -->
                @if($reimbursement->bukti_dokumen && count($reimbursement->bukti_dokumen) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Bukti Dokumen</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($reimbursement->bukti_dokumen as $index => $dokumen)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            @if(in_array(pathinfo($dokumen['filename'], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                <i class="fas fa-image text-blue-600"></i>
                                            @elseif(pathinfo($dokumen['filename'], PATHINFO_EXTENSION) === 'pdf')
                                                <i class="fas fa-file-pdf text-red-600"></i>
                                            @else
                                                <i class="fas fa-file text-gray-600"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $dokumen['filename'] }}</p>
                                            <p class="text-sm text-gray-500">{{ number_format($dokumen['size'] / 1024, 1) }} KB</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('perusahaan.keuangan.reimbursement.download-file', [$reimbursement->hash_id, $index]) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Financial Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Keuangan</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Jumlah Pengajuan</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($reimbursement->jumlah_pengajuan, 0, ',', '.') }}</span>
                        </div>
                        @if($reimbursement->jumlah_disetujui)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jumlah Disetujui</span>
                                <span class="font-semibold text-green-600">Rp {{ number_format($reimbursement->jumlah_disetujui, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($reimbursement->rekening)
                            <div class="pt-4 border-t border-gray-200">
                                <span class="text-gray-600 block mb-2">Rekening Pembayaran</span>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">{{ $reimbursement->rekening->nama_rekening }}</p>
                                    <p class="text-gray-500">{{ $reimbursement->rekening->nomor_rekening }}</p>
                                </div>
                            </div>
                        @endif
                        @if($reimbursement->nomor_transaksi_pembayaran)
                            <div class="pt-4 border-t border-gray-200">
                                <span class="text-gray-600 block mb-2">No. Transaksi</span>
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $reimbursement->nomor_transaksi_pembayaran }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                            <div>
                                <p class="font-medium text-gray-900">Dibuat</p>
                                <p class="text-sm text-gray-500">{{ $reimbursement->created_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                        @if($reimbursement->reviewed_at)
                            <div class="flex items-start">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 mr-3"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Direview</p>
                                    <p class="text-sm text-gray-500">{{ $reimbursement->reviewed_at->format('d F Y H:i') }}</p>
                                    @if($reimbursement->reviewedBy)
                                        <p class="text-sm text-gray-500">oleh {{ $reimbursement->reviewedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($reimbursement->approved_at)
                            <div class="flex items-start">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Disetujui</p>
                                    <p class="text-sm text-gray-500">{{ $reimbursement->approved_at->format('d F Y H:i') }}</p>
                                    @if($reimbursement->approvedBy)
                                        <p class="text-sm text-gray-500">oleh {{ $reimbursement->approvedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($reimbursement->paid_at)
                            <div class="flex items-start">
                                <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Dibayar</p>
                                    <p class="text-sm text-gray-500">{{ $reimbursement->paid_at->format('d F Y H:i') }}</p>
                                    @if($reimbursement->paidBy)
                                        <p class="text-sm text-gray-500">oleh {{ $reimbursement->paidBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection