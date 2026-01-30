@extends('perusahaan.layouts.app')

@section('title', 'Detail Peminjaman Aset')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('perusahaan.peminjaman-aset.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Peminjaman Aset</h1>
                <p class="text-gray-600 mt-1">{{ $peminjamanAset->kode_peminjaman }}</p>
            </div>
        </div>
        <div class="flex space-x-2">
            @if(in_array($peminjamanAset->status_peminjaman, ['approved', 'dipinjam', 'dikembalikan']))
                <a href="{{ route('perusahaan.peminjaman-aset.export-bukti', $peminjamanAset->hash_id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export Bukti
                </a>
            @endif
            @if(in_array($peminjamanAset->status_peminjaman, ['pending', 'approved']))
                <a href="{{ route('perusahaan.peminjaman-aset.edit', $peminjamanAset->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Status Peminjaman</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($peminjamanAset->status_color === 'yellow') bg-yellow-100 text-yellow-800
                    @elseif($peminjamanAset->status_color === 'blue') bg-blue-100 text-blue-800
                    @elseif($peminjamanAset->status_color === 'green') bg-green-100 text-green-800
                    @elseif($peminjamanAset->status_color === 'red') bg-red-100 text-red-800
                    @elseif($peminjamanAset->status_color === 'orange') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $peminjamanAset->status_label }}
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                @if($peminjamanAset->status_peminjaman === 'pending')
                    <button onclick="showApproveModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        <i class="fas fa-check mr-1"></i> Setujui
                    </button>
                    <button onclick="showRejectModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        <i class="fas fa-times mr-1"></i> Tolak
                    </button>
                @elseif($peminjamanAset->status_peminjaman === 'approved')
                    <form action="{{ route('perusahaan.peminjaman-aset.borrow', $peminjamanAset->hash_id) }}" method="POST" class="inline" id="borrowForm">
                        @csrf
                        <button type="button" id="confirmBorrowBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            <i class="fas fa-handshake mr-1"></i> Konfirmasi Dipinjam
                        </button>
                    </form>
                @elseif($peminjamanAset->status_peminjaman === 'dipinjam')
                    <a href="{{ route('perusahaan.peminjaman-aset.return-form', $peminjamanAset->hash_id) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        <i class="fas fa-undo mr-1"></i> Kembalikan Aset
                    </a>
                @endif
            </div>
        </div>

        <!-- Detail Peminjaman -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Peminjaman</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Kode Peminjaman:</span>
                    <span class="font-medium">{{ $peminjamanAset->kode_peminjaman }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Project:</span>
                    <span class="font-medium">{{ $peminjamanAset->project->nama ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal Peminjaman:</span>
                    <span class="font-medium">{{ $peminjamanAset->tanggal_peminjaman->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Rencana Kembali:</span>
                    <span class="font-medium">{{ $peminjamanAset->tanggal_rencana_kembali->format('d/m/Y') }}</span>
                </div>
                @if($peminjamanAset->tanggal_kembali_aktual)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Kembali:</span>
                        <span class="font-medium">{{ $peminjamanAset->tanggal_kembali_aktual->format('d/m/Y') }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Jumlah Dipinjam:</span>
                    <span class="font-medium">{{ $peminjamanAset->jumlah_dipinjam }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kondisi Saat Dipinjam:</span>
                    <span class="font-medium">{{ $peminjamanAset->kondisi_saat_dipinjam_label }}</span>
                </div>
                @if($peminjamanAset->kondisi_saat_dikembalikan)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kondisi Saat Dikembalikan:</span>
                        <span class="font-medium">{{ $peminjamanAset->kondisi_saat_dikembalikan_label }}</span>
                    </div>
                @endif
            </div>

            @if($peminjamanAset->keperluan)
                <div class="mt-4">
                    <span class="text-gray-600 block mb-2">Keperluan:</span>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $peminjamanAset->keperluan }}</p>
                </div>
            @endif

            @if($peminjamanAset->catatan_peminjaman)
                <div class="mt-4">
                    <span class="text-gray-600 block mb-2">Catatan Peminjaman:</span>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $peminjamanAset->catatan_peminjaman }}</p>
                </div>
            @endif

            @if($peminjamanAset->catatan_pengembalian)
                <div class="mt-4">
                    <span class="text-gray-600 block mb-2">Catatan Pengembalian:</span>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $peminjamanAset->catatan_pengembalian }}</p>
                </div>
            @endif
        </div>

        <!-- File Bukti -->
        @if($peminjamanAset->file_bukti_peminjaman || $peminjamanAset->file_bukti_pengembalian)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">File Bukti</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($peminjamanAset->file_bukti_peminjaman)
                        <div>
                            <span class="text-gray-600 block mb-2">Bukti Peminjaman:</span>
                            <a href="{{ $peminjamanAset->file_bukti_peminjaman_url }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-file mr-2"></i>
                                Lihat File
                            </a>
                        </div>
                    @endif
                    
                    @if($peminjamanAset->file_bukti_pengembalian)
                        <div>
                            <span class="text-gray-600 block mb-2">Bukti Pengembalian:</span>
                            <a href="{{ $peminjamanAset->file_bukti_pengembalian_url }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-file mr-2"></i>
                                Lihat File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Aset Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Aset</h3>
            
            @if($peminjamanAset->dataAset && $peminjamanAset->dataAset->foto_url)
                <div class="mb-4">
                    <img src="{{ $peminjamanAset->dataAset->foto_url }}" alt="Foto Aset" class="w-full h-32 object-cover rounded-lg">
                </div>
            @endif
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600 text-sm">Kode Aset:</span>
                    <p class="font-medium">{{ $peminjamanAset->dataAset->kode_aset ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Nama Aset:</span>
                    <p class="font-medium">{{ $peminjamanAset->dataAset->nama_aset ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Kategori:</span>
                    <p class="font-medium">{{ $peminjamanAset->dataAset->kategori ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">PIC:</span>
                    <p class="font-medium">{{ $peminjamanAset->dataAset->pic_penanggung_jawab ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Peminjam Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Peminjam</h3>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600 text-sm">Nama:</span>
                    <p class="font-medium">{{ $peminjamanAset->peminjam_nama }}</p>
                </div>
                <div>
                    <span class="text-gray-600 text-sm">Tipe:</span>
                    <p class="font-medium">{{ ucfirst($peminjamanAset->peminjam_tipe) }}</p>
                </div>
                @if($peminjamanAset->peminjamKaryawan)
                    <div>
                        <span class="text-gray-600 text-sm">NIK:</span>
                        <p class="font-medium">{{ $peminjamanAset->peminjamKaryawan->nik_karyawan }}</p>
                    </div>
                @elseif($peminjamanAset->peminjamUser)
                    <div>
                        <span class="text-gray-600 text-sm">Email:</span>
                        <p class="font-medium">{{ $peminjamanAset->peminjamUser->email }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
            
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    <div>
                        <p class="text-sm font-medium">Dibuat</p>
                        <p class="text-xs text-gray-500">{{ $peminjamanAset->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-gray-600">oleh {{ $peminjamanAset->createdBy->name }}</p>
                    </div>
                </div>
                
                @if($peminjamanAset->approved_at)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium">{{ $peminjamanAset->status_peminjaman === 'ditolak' ? 'Ditolak' : 'Disetujui' }}</p>
                            <p class="text-xs text-gray-500">{{ $peminjamanAset->approved_at->format('d/m/Y H:i') }}</p>
                            @if($peminjamanAset->approvedBy)
                                <p class="text-xs text-gray-600">oleh {{ $peminjamanAset->approvedBy->name }}</p>
                            @endif
                        </div>
                    </div>
                @endif
                
                @if($peminjamanAset->borrowed_at)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium">Dipinjam</p>
                            <p class="text-xs text-gray-500">{{ $peminjamanAset->borrowed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($peminjamanAset->returned_at)
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-gray-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium">Dikembalikan</p>
                            <p class="text-xs text-gray-500">{{ $peminjamanAset->returned_at->format('d/m/Y H:i') }}</p>
                            @if($peminjamanAset->returnedBy)
                                <p class="text-xs text-gray-600">oleh {{ $peminjamanAset->returnedBy->name }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Persetujuan</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeApproveModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </button>
            <form action="{{ route('perusahaan.peminjaman-aset.approve', $peminjamanAset->hash_id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Setujui
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Peminjaman</h3>
        <form action="{{ route('perusahaan.peminjaman-aset.reject', $peminjamanAset->hash_id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="catatan_penolakan" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan_penolakan" id="catatan_penolakan" rows="3" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').classList.add('flex');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('flex');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

// Handle confirm borrow button with SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    const confirmBorrowBtn = document.getElementById('confirmBorrowBtn');
    const borrowForm = document.getElementById('borrowForm');
    
    if (confirmBorrowBtn && borrowForm) {
        confirmBorrowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Peminjaman Aset',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Apakah Anda yakin ingin mengkonfirmasi bahwa aset sudah dipinjam?</p>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm">
                            <div class="grid grid-cols-2 gap-2">
                                <div><strong>Kode:</strong></div>
                                <div>{{ $peminjamanAset->kode_peminjaman }}</div>
                                <div><strong>Aset:</strong></div>
                                <div>{{ $peminjamanAset->aset_nama }}</div>
                                <div><strong>Peminjam:</strong></div>
                                <div>{{ $peminjamanAset->peminjam_nama }}</div>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Setelah dikonfirmasi, status akan berubah menjadi "Sedang Dipinjam".
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-handshake mr-2"></i>Ya, Konfirmasi Dipinjam',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'text-left'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit the form
                    borrowForm.submit();
                }
            });
        });
    }
});

// Close modals when clicking outside
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endpush