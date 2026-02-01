@extends('perusahaan.layouts.app')

@section('title', 'Detail Arus Kas - ' . $rekening->nama_rekening)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('perusahaan.keuangan.laporan-arus-kas.index') }}">Laporan Arus Kas</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $rekening->nama_rekening }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-university text-primary me-2"></i>
                Detail Arus Kas - {{ $rekening->nama_rekening }}
            </h1>
            <p class="text-muted mb-0">{{ $rekening->project->nama ?? 'N/A' }} • {{ $rekening->nama_bank }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('perusahaan.keuangan.laporan-arus-kas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Rekening Info Card -->
    <div class="card shadow-sm mb-4" style="border-left: 4px solid {{ $rekening->warna_card }} !important;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title mb-3">{{ $rekening->nama_rekening }}</h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Nomor Rekening:</strong> {{ $rekening->nomor_rekening }}</p>
                            <p class="mb-1"><strong>Bank:</strong> {{ $rekening->nama_bank }}</p>
                            <p class="mb-1"><strong>Pemilik:</strong> {{ $rekening->nama_pemilik }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Jenis:</strong> {{ ucwords(str_replace('_', ' ', $rekening->jenis_rekening)) }}</p>
                            <p class="mb-1"><strong>Mata Uang:</strong> {{ $rekening->mata_uang }}</p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-{{ $rekening->is_active ? 'success' : 'danger' }}">
                                    {{ $rekening->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        <small class="text-muted">Saldo Saat Ini</small>
                    </div>
                    <div class="h3 mb-0 font-weight-bold text-{{ $rekening->saldo_saat_ini >= 0 ? 'success' : 'danger' }}">
                        {{ $rekening->mata_uang }} {{ number_format($rekening->saldo_saat_ini, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('perusahaan.keuangan.laporan-arus-kas.show', $rekening->hash_id) }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            Tanggal Mulai
                        </label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            Tanggal Selesai
                        </label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('perusahaan.keuangan.laporan-arus-kas.show', $rekening->hash_id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Debit (Masuk)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['total_debit'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Kredit (Keluar)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $stats['net_cash_flow'] >= 0 ? 'success' : 'danger' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $stats['net_cash_flow'] >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">
                                Net Cash Flow
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['net_cash_flow'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $stats['net_cash_flow'] >= 0 ? 'chart-line' : 'chart-line-down' }} fa-2x text-{{ $stats['net_cash_flow'] >= 0 ? 'success' : 'danger' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Saldo Awal Periode
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                Riwayat Transaksi
            </h6>
            <div class="text-muted">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>
        <div class="card-body">
            @if($transaksis->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Transaksi</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                            <th class="text-end">Saldo</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksis as $transaksi)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $transaksi->tanggal_transaksi ? $transaksi->tanggal_transaksi->format('d/m/Y') : '-' }}</div>
                                <small class="text-muted">{{ $transaksi->created_at ? $transaksi->created_at->format('H:i') : '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $transaksi->nomor_transaksi }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $transaksi->kategori_transaksi_label }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $transaksi->keterangan }}">
                                    {{ $transaksi->keterangan }}
                                </div>
                                @if($transaksi->referensi)
                                <small class="text-muted">Ref: {{ $transaksi->referensi }}</small>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($transaksi->jenis_transaksi === 'debit')
                                <span class="text-success fw-bold">
                                    +{{ $transaksi->formatted_jumlah }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($transaksi->jenis_transaksi === 'kredit')
                                <span class="text-danger fw-bold">
                                    -{{ $transaksi->formatted_jumlah }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-{{ $transaksi->saldo_sesudah >= 0 ? 'success' : 'danger' }}">
                                    {{ $transaksi->formatted_saldo_sesudah }}
                                </span>
                            </td>
                            <td>
                                @if($transaksi->is_verified)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Verified
                                </span>
                                @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Pending
                                </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $transaksi->user->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('perusahaan.keuangan.transaksi-rekening.show', $transaksi->hash_id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $transaksis->firstItem() ?? 0 }} - {{ $transaksis->lastItem() ?? 0 }} 
                    dari {{ $transaksis->total() }} transaksi
                </div>
                {{ $transaksis->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada transaksi</h5>
                <p class="text-muted">Belum ada transaksi pada periode yang dipilih untuk rekening ini</p>
                <a href="{{ route('perusahaan.keuangan.transaksi-rekening.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Transaksi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection