@extends('perusahaan.layouts.app')

@section('title', 'Detail Karyawan')
@section('page-title')
    <span id="pageTitle">Detail Data Karyawan</span>
@endsection
@section('page-subtitle')
    <span id="pageSubtitle">Kelola profil karyawan</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('perusahaan.karyawans.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Karyawan
        </a>
    </div>

    <!-- Header dengan Nama & NIK -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ $karyawan->nama_lengkap }}</h2>
        <p class="text-gray-500">No Badge: {{ $karyawan->nik_karyawan }}</p>
    </div>

    <!-- Layout 2 Kolom -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Sidebar Kiri - Menu Navigasi -->
        <div class="col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-6">
                <nav class="space-y-1">
                    <a href="#informasi" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition nav-link active" data-target="informasi">
                        <i class="fas fa-info-circle mr-3" style="color: #3B82C8;"></i>
                        Informasi
                    </a>
                    <a href="#pengalaman-kerja" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="pengalaman-kerja">
                        <i class="fas fa-briefcase mr-3 text-gray-400"></i>
                        Pengalaman Kerja
                    </a>
                    <a href="#pendidikan" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="pendidikan">
                        <i class="fas fa-graduation-cap mr-3 text-gray-400"></i>
                        Pendidikan
                    </a>
                    <a href="#sertifikasi" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="sertifikasi">
                        <i class="fas fa-certificate mr-3 text-gray-400"></i>
                        Sertifikasi
                    </a>
                    <a href="#rekening-bank" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="rekening-bank">
                        <i class="fas fa-university mr-3 text-gray-400"></i>
                        Rekening Bank
                    </a>
                    <a href="#bpjs" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="bpjs">
                        <i class="fas fa-id-card mr-3 text-gray-400"></i>
                        BPJS
                    </a>
                    <a href="#medical-checkup" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="medical-checkup">
                        <i class="fas fa-heartbeat mr-3 text-gray-400"></i>
                        Medical Checkup
                    </a>
                    <a href="#akun-pengguna" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="akun-pengguna">
                        <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                        Akun Pengguna
                    </a>
                    <a href="#kartu-akses" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition nav-link" data-target="kartu-akses">
                        <i class="fas fa-id-badge mr-3 text-gray-400"></i>
                        Kartu Akses
                    </a>
                </nav>
            </div>
        </div>

        <!-- Konten Kanan - Full Width -->
        <div class="col-span-9">
            <div class="space-y-6">
                <!-- Foto Karyawan -->
                <div id="foto-karyawan" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-image mr-2" style="color: #3B82C8;"></i>
                        Foto Karyawan
                    </h3>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <div id="fotoPreview" class="w-32 h-32 rounded-full overflow-hidden cursor-pointer" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);" onclick="document.getElementById('fotoInput').click()">
                            @if($karyawan->foto)
                                <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_lengkap }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-user text-6xl" style="color: #3B82C8;"></i>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="document.getElementById('fotoInput').click()" class="absolute bottom-0 right-0 w-10 h-10 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-xl font-bold text-gray-900">{{ $karyawan->nama_lengkap }}</h4>
                            <button onclick="openEditNamaModal()" class="text-gray-400 hover:text-blue-600 transition">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mb-3">No Badge: {{ $karyawan->nik_karyawan }}</p>
                        <form id="uploadFotoForm" action="{{ route('perusahaan.karyawans.upload-foto', $karyawan->hash_id) }}" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" id="fotoInput" name="foto" accept="image/*" onchange="previewAndUploadFoto(this)">
                        </form>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Klik foto untuk mengganti. Max 2MB (JPG, PNG)
                        </p>
                    </div>
                </div>
            </div>

    <!-- Informasi Pekerjaan -->
    <div id="informasi" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-briefcase mr-2" style="color: #3B82C8;"></i>
                        Informasi Pekerjaan
                    </h3>
                    <button onclick="openEditPekerjaanModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-edit mr-1"></i>Edit Data
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Project</p>
                        @if($karyawan->project)
                            <span class="px-2 py-1 rounded text-xs font-medium text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                {{ $karyawan->project->nama }}
                            </span>
                        @else
                            <p class="text-sm text-gray-900">-</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status Karyawan</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold" style="background: #D1FAE5; color: #065F46;">
                            {{ $karyawan->status_karyawan }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Jabatan</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->jabatan->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Masuk</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->tanggal_masuk->format('d F Y') }}</p>
                    </div>
                    @if(str_contains(strtolower($karyawan->status_karyawan), 'kontrak'))
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Berakhir Kontrak</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('d F Y') : '-' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

    <!-- Data Pribadi -->
    <div id="data-pribadi" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>
                        Data Pribadi
                    </h3>
                    <button onclick="openEditPribadiModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-edit mr-1"></i>Edit Data
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">NIK KTP</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->nik_ktp }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Jenis Kelamin</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->jenis_kelamin }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tempat Lahir</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->tempat_lahir }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Lahir</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->tanggal_lahir->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status Perkawinan</p>
                        <p class="text-sm font-medium text-gray-900">
                            @if($karyawan->status_perkawinan == 'TK')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user mr-1"></i>
                                    Tidak Kawin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-users mr-1"></i>
                                    Kawin
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Jumlah Tanggungan</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->jumlah_tanggungan }} orang</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status PTKP (Pajak)</p>
                        <p class="text-sm font-medium text-gray-900">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-file-invoice-dollar mr-1"></i>
                                {{ $karyawan->ptkp_status }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Telepon</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->telepon }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->user->email ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Alamat</p>
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->alamat }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $karyawan->kota }}, {{ $karyawan->provinsi }}</p>
                    </div>
                </div>
            </div>

    <!-- Pengalaman Kerja -->
    <div id="pengalaman-kerja" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-briefcase mr-2" style="color: #3B82C8;"></i>
                Pengalaman Kerja
            </h3>
            <button onclick="openTambahPengalamanModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-plus mr-1"></i>Tambah
            </button>
        </div>
        
        @if($karyawan->pengalamanKerjas->count() > 0)
            <div class="space-y-4">
                @foreach($karyawan->pengalamanKerjas as $pengalaman)
                    <div class="relative border-l-4 rounded-lg p-5 hover:shadow-lg transition-all duration-300" style="border-left-color: #3B82C8; background: linear-gradient(to right, #F0F9FF 0%, #FFFFFF 100%);">
                        <!-- Header Card -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $pengalaman->jabatan }}</h4>
                                    @if($pengalaman->masih_bekerja)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full" style="background: #D1FAE5; color: #065F46;">
                                            <i class="fas fa-circle text-[6px] mr-1"></i>Aktif
                                        </span>
                                    @endif
                                </div>
                                <p class="text-base font-semibold mb-2" style="color: #3B82C8;">
                                    <i class="fas fa-building mr-1"></i>{{ $pengalaman->nama_perusahaan }}
                                </p>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar-alt mr-2" style="color: #60A5FA;"></i>
                                    <span class="font-medium">
                                        {{ $pengalaman->tanggal_mulai->format('M Y') }} - 
                                        @if($pengalaman->masih_bekerja)
                                            <span class="font-semibold" style="color: #3B82C8;">Sekarang</span>
                                        @else
                                            {{ $pengalaman->tanggal_selesai ? $pengalaman->tanggal_selesai->format('M Y') : '-' }}
                                        @endif
                                    </span>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <span class="text-gray-500">
                                        @php
                                            $start = $pengalaman->tanggal_mulai;
                                            $end = $pengalaman->masih_bekerja ? now() : $pengalaman->tanggal_selesai;
                                            $diff = $start->diff($end);
                                            $years = $diff->y;
                                            $months = $diff->m;
                                            $duration = '';
                                            if ($years > 0) {
                                                $duration .= $years . ' tahun ';
                                            }
                                            if ($months > 0) {
                                                $duration .= $months . ' bulan';
                                            }
                                            if (empty($duration)) {
                                                $duration = '< 1 bulan';
                                            }
                                        @endphp
                                        {{ $duration }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button onclick="openEditPengalamanModal('{{ $pengalaman->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deletePengalaman('{{ $pengalaman->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #F87171 0%, #DC2626 100%);" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Deskripsi Pekerjaan -->
                        @if($pengalaman->deskripsi_pekerjaan)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);">
                                        <i class="fas fa-tasks text-sm" style="color: #3B82C8;"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-semibold text-gray-700 mb-1">Deskripsi Pekerjaan</p>
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ $pengalaman->deskripsi_pekerjaan }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Pencapaian -->
                        @if($pengalaman->pencapaian)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);">
                                        <i class="fas fa-trophy text-sm" style="color: #D97706;"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-semibold text-gray-700 mb-1">Pencapaian</p>
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ $pengalaman->pencapaian }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                    <i class="fas fa-briefcase text-3xl" style="color: #3B82C8;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada data pengalaman kerja</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah" untuk menambahkan pengalaman kerja</p>
            </div>
        @endif
    </div>

    <div id="pendidikan" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-graduation-cap mr-2" style="color: #3B82C8;"></i>
                Pendidikan
            </h3>
            <button onclick="openTambahPendidikanModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-plus mr-1"></i>Tambah
            </button>
        </div>
        
        @if($karyawan->pendidikans->count() > 0)
            <div class="space-y-4">
                @foreach($karyawan->pendidikans->sortByDesc('tahun_selesai') as $pendidikan)
                    <div class="relative border-l-4 rounded-lg p-5 hover:shadow-lg transition-all duration-300" style="border-left-color: #8B5CF6; background: linear-gradient(to right, #FAF5FF 0%, #FFFFFF 100%);">
                        <!-- Header Card -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full text-white" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                                        {{ $pendidikan->jenjang_pendidikan }}
                                    </span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 mt-2">{{ $pendidikan->nama_institusi }}</h4>
                                @if($pendidikan->jurusan)
                                    <p class="text-base font-semibold mt-1" style="color: #8B5CF6;">
                                        <i class="fas fa-book mr-1"></i>{{ $pendidikan->jurusan }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-4 mt-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar-alt mr-2" style="color: #A78BFA;"></i>
                                        <span class="font-medium">{{ $pendidikan->tahun_mulai }} - {{ $pendidikan->tahun_selesai }}</span>
                                    </div>
                                    @if($pendidikan->ipk)
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-star mr-1" style="color: #FBBF24;"></i>
                                            <span class="font-semibold text-gray-700">IPK: {{ $pendidikan->ipk }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button onclick="openEditPendidikanModal('{{ $pendidikan->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #A78BFA 0%, #8B5CF6 100%);" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deletePendidikan('{{ $pendidikan->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #F87171 0%, #DC2626 100%);" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #FAF5FF 0%, #EDE9FE 100%);">
                    <i class="fas fa-graduation-cap text-3xl" style="color: #8B5CF6;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada data pendidikan</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah" untuk menambahkan riwayat pendidikan</p>
            </div>
        @endif
    </div>

    <!-- Sertifikasi -->
    <div id="sertifikasi" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-certificate mr-2" style="color: #D97706;"></i>
                Sertifikasi
            </h3>
            <button onclick="openTambahSertifikasiModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                <i class="fas fa-plus mr-1"></i>Tambah
            </button>
        </div>
        
        @if($karyawan->sertifikasis->count() > 0)
            <div class="space-y-4">
                @foreach($karyawan->sertifikasis->sortByDesc('tanggal_terbit') as $sertifikasi)
                    <div class="relative border-l-4 rounded-lg p-5 hover:shadow-lg transition-all duration-300" style="border-left-color: #F59E0B; background: linear-gradient(to right, #FFFBEB 0%, #FFFFFF 100%);">
                        <!-- Header Card -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $sertifikasi->nama_sertifikasi }}</h4>
                                    @if($sertifikasi->tanggal_expired)
                                        @php
                                            $isExpired = $sertifikasi->tanggal_expired->isPast();
                                            $isExpiringSoon = !$isExpired && $sertifikasi->tanggal_expired->diffInDays(now()) <= 30;
                                        @endphp
                                        @if($isExpired)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle mr-1"></i>Expired
                                            </span>
                                        @elseif($isExpiringSoon)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Segera Expired
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Aktif
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                            <i class="fas fa-infinity mr-1"></i>Permanen
                                        </span>
                                    @endif
                                </div>
                                <p class="text-base font-semibold mb-2" style="color: #F59E0B;">
                                    <i class="fas fa-building mr-1"></i>{{ $sertifikasi->penerbit }}
                                </p>
                                <div class="flex items-center gap-4 flex-wrap">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar-check mr-2" style="color: #FBBF24;"></i>
                                        <span class="font-medium">Terbit: {{ $sertifikasi->tanggal_terbit->format('d M Y') }}</span>
                                    </div>
                                    @if($sertifikasi->tanggal_expired)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-calendar-times mr-2" style="color: #F59E0B;"></i>
                                            <span class="font-medium">Expired: {{ $sertifikasi->tanggal_expired->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if($sertifikasi->nomor_sertifikat)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-hashtag mr-1" style="color: #FBBF24;"></i>
                                            <span class="font-medium">{{ $sertifikasi->nomor_sertifikat }}</span>
                                        </div>
                                    @endif
                                </div>
                                @if($sertifikasi->url_sertifikat)
                                    <div class="mt-3">
                                        <a href="{{ $sertifikasi->url_sertifikat }}" target="_blank" class="inline-flex items-center text-sm font-medium hover:underline" style="color: #F59E0B;">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            Lihat Sertifikat
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button onclick="openEditSertifikasiModal('{{ $sertifikasi->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #FBBF24 0%, #F59E0B 100%);" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deleteSertifikasi('{{ $sertifikasi->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #F87171 0%, #DC2626 100%);" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);">
                    <i class="fas fa-certificate text-3xl" style="color: #F59E0B;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada data sertifikasi</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah" untuk menambahkan sertifikasi</p>
            </div>
        @endif
    </div>

    <!-- Rekening Bank -->
    <div id="rekening-bank" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-university mr-2" style="color: #10B981;"></i>
                Rekening Bank
            </h3>
            @if(!$karyawan->nama_bank)
                <button onclick="openTambahRekeningModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="fas fa-plus mr-1"></i>Tambah
                </button>
            @else
                <button onclick="openEditRekeningModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    <i class="fas fa-edit mr-1"></i>Edit Data
                </button>
            @endif
        </div>
        
        @if($karyawan->nama_bank)
            <div class="border-l-4 rounded-lg p-5" style="border-left-color: #10B981; background: linear-gradient(to right, #ECFDF5 0%, #FFFFFF 100%);">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Bank</p>
                        <p class="text-base font-bold text-gray-900">{{ $karyawan->nama_bank }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nomor Rekening</p>
                        <p class="text-base font-bold" style="color: #10B981;">{{ $karyawan->nomor_rekening }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Pemilik Rekening</p>
                        <p class="text-base font-semibold text-gray-900">{{ $karyawan->nama_pemilik_rekening }}</p>
                    </div>
                    @if($karyawan->cabang_bank)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Cabang Bank</p>
                            <p class="text-base font-medium text-gray-700">{{ $karyawan->cabang_bank }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);">
                    <i class="fas fa-university text-3xl" style="color: #10B981;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada data rekening bank</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah" untuk menambahkan rekening bank</p>
            </div>
        @endif
    </div>

    <!-- BPJS -->
    <div id="bpjs" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-id-card mr-2" style="color: #3B82C8;"></i>
                Data BPJS
            </h3>
            <button onclick="openEditBpjsModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-edit mr-1"></i>{{ $karyawan->bpjs_kesehatan_nomor || $karyawan->bpjs_jkm_nomor || $karyawan->bpjs_jkk_nomor || $karyawan->bpjs_jp_nomor || $karyawan->bpjs_jht_nomor ? 'Edit Data' : 'Tambah Data' }}
            </button>
        </div>

        @php
            $hasBpjsData = $karyawan->bpjs_kesehatan_nomor || $karyawan->bpjs_jkm_nomor || $karyawan->bpjs_jkk_nomor || $karyawan->bpjs_jp_nomor || $karyawan->bpjs_jht_nomor;
        @endphp

        @if($hasBpjsData)
            <div class="space-y-4">
                <!-- BPJS Kesehatan -->
                @if($karyawan->bpjs_kesehatan_nomor)
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #EF4444; background: linear-gradient(to right, #FEE2E2 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-hospital text-xl" style="color: #EF4444;"></i>
                        <h4 class="text-lg font-bold text-gray-900">BPJS Kesehatan</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor BPJS</p>
                            <p class="text-base font-bold" style="color: #EF4444;">{{ $karyawan->bpjs_kesehatan_nomor }}</p>
                        </div>
                        @if($karyawan->bpjs_kesehatan_tanggal_terdaftar)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Terdaftar</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_kesehatan_tanggal_terdaftar->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_kesehatan_status)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $karyawan->bpjs_kesehatan_status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $karyawan->bpjs_kesehatan_status }}
                            </span>
                        </div>
                        @endif
                        @if($karyawan->bpjs_kesehatan_catatan)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Catatan</p>
                            <p class="text-sm text-gray-700">{{ $karyawan->bpjs_kesehatan_catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- BPJS Ketenagakerjaan - JKM -->
                @if($karyawan->bpjs_jkm_nomor)
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #8B5CF6; background: linear-gradient(to right, #FAF5FF 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-shield-alt text-xl" style="color: #8B5CF6;"></i>
                        <h4 class="text-lg font-bold text-gray-900">Jaminan Kematian (JKM)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor BPJS</p>
                            <p class="text-base font-bold" style="color: #8B5CF6;">{{ $karyawan->bpjs_jkm_nomor }}</p>
                        </div>
                        @if($karyawan->bpjs_jkm_npp)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">NPP (Nomor Peserta Program)</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jkm_npp }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jkm_tanggal_terdaftar)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Terdaftar</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jkm_tanggal_terdaftar->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jkm_status)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $karyawan->bpjs_jkm_status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $karyawan->bpjs_jkm_status }}
                            </span>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jkm_catatan)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Catatan</p>
                            <p class="text-sm text-gray-700">{{ $karyawan->bpjs_jkm_catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- BPJS Ketenagakerjaan - JKK -->
                @if($karyawan->bpjs_jkk_nomor)
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #F59E0B; background: linear-gradient(to right, #FFFBEB 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-exclamation-triangle text-xl" style="color: #F59E0B;"></i>
                        <h4 class="text-lg font-bold text-gray-900">Jaminan Kecelakaan Kerja (JKK)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor BPJS</p>
                            <p class="text-base font-bold" style="color: #F59E0B;">{{ $karyawan->bpjs_jkk_nomor }}</p>
                        </div>
                        @if($karyawan->bpjs_jkk_npp)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">NPP (Nomor Peserta Program)</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jkk_npp }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jkk_tanggal_terdaftar)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Terdaftar</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jkk_tanggal_terdaftar->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jkk_status)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $karyawan->bpjs_jkk_status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $karyawan->bpjs_jkk_status }}
                            </span>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jkk_catatan)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Catatan</p>
                            <p class="text-sm text-gray-700">{{ $karyawan->bpjs_jkk_catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- BPJS Ketenagakerjaan - JP -->
                @if($karyawan->bpjs_jp_nomor)
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #10B981; background: linear-gradient(to right, #ECFDF5 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-landmark text-xl" style="color: #10B981;"></i>
                        <h4 class="text-lg font-bold text-gray-900">Jaminan Pensiun (JP)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor BPJS</p>
                            <p class="text-base font-bold" style="color: #10B981;">{{ $karyawan->bpjs_jp_nomor }}</p>
                        </div>
                        @if($karyawan->bpjs_jp_npp)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">NPP (Nomor Peserta Program)</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jp_npp }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jp_tanggal_terdaftar)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Terdaftar</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jp_tanggal_terdaftar->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jp_status)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $karyawan->bpjs_jp_status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $karyawan->bpjs_jp_status }}
                            </span>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jp_catatan)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Catatan</p>
                            <p class="text-sm text-gray-700">{{ $karyawan->bpjs_jp_catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- BPJS Ketenagakerjaan - JHT -->
                @if($karyawan->bpjs_jht_nomor)
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #3B82F6; background: linear-gradient(to right, #EFF6FF 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-coins text-xl" style="color: #3B82F6;"></i>
                        <h4 class="text-lg font-bold text-gray-900">Jaminan Hari Tua (JHT)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nomor BPJS</p>
                            <p class="text-base font-bold" style="color: #3B82F6;">{{ $karyawan->bpjs_jht_nomor }}</p>
                        </div>
                        @if($karyawan->bpjs_jht_npp)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">NPP (Nomor Peserta Program)</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jht_npp }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jht_tanggal_terdaftar)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Terdaftar</p>
                            <p class="text-base font-medium text-gray-900">{{ $karyawan->bpjs_jht_tanggal_terdaftar->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jht_status)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $karyawan->bpjs_jht_status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $karyawan->bpjs_jht_status }}
                            </span>
                        </div>
                        @endif
                        @if($karyawan->bpjs_jht_catatan)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Catatan</p>
                            <p class="text-sm text-gray-700">{{ $karyawan->bpjs_jht_catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                    <i class="fas fa-id-card text-3xl" style="color: #3B82C8;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada data BPJS</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Data" untuk menambahkan data BPJS</p>
            </div>
        @endif
    </div>

    <!-- Medical Checkup -->
    <div id="medical-checkup" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-heartbeat mr-2" style="color: #EF4444;"></i>
                Medical Checkup
            </h3>
            <button onclick="openTambahMedicalCheckupModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                <i class="fas fa-plus mr-1"></i>Tambah
            </button>
        </div>
        
        @if($karyawan->medicalCheckups->count() > 0)
            <div class="space-y-4">
                @foreach($karyawan->medicalCheckups->sortByDesc('tanggal_checkup') as $checkup)
                    <div class="relative border-l-4 rounded-lg p-5 hover:shadow-lg transition-all duration-300" style="border-left-color: #EF4444; background: linear-gradient(to right, #FEE2E2 0%, #FFFFFF 100%);">
                        <!-- Header Card -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $checkup->jenis_checkup }}</h4>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $checkup->status_kesehatan == 'Sehat' ? 'bg-green-100 text-green-700' : ($checkup->status_kesehatan == 'Perlu Perhatian' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $checkup->status_kesehatan }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mb-3">
                                    <i class="fas fa-calendar-alt mr-2" style="color: #EF4444;"></i>
                                    <span class="font-medium">{{ $checkup->tanggal_checkup->format('d F Y') }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button onclick="openEditMedicalCheckupModal('{{ $checkup->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #F87171 0%, #EF4444 100%);" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deleteMedicalCheckup('{{ $checkup->hash_id }}')" class="w-9 h-9 flex items-center justify-center rounded-lg text-white hover:shadow-md transition-all" style="background: linear-gradient(135deg, #F87171 0%, #DC2626 100%);" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Pengukuran Fisik -->
                        @if($checkup->tinggi_badan || $checkup->berat_badan || $checkup->golongan_darah || $checkup->tekanan_darah)
                        <div class="grid grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200">
                            @if($checkup->tinggi_badan)
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Tinggi Badan</p>
                                <p class="text-base font-bold" style="color: #EF4444;">{{ $checkup->tinggi_badan }} cm</p>
                            </div>
                            @endif
                            @if($checkup->berat_badan)
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Berat Badan</p>
                                <p class="text-base font-bold" style="color: #EF4444;">{{ $checkup->berat_badan }} kg</p>
                            </div>
                            @endif
                            @if($checkup->golongan_darah)
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Golongan Darah</p>
                                <p class="text-base font-bold" style="color: #EF4444;">{{ $checkup->golongan_darah }}</p>
                            </div>
                            @endif
                            @if($checkup->tekanan_darah)
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Tekanan Darah</p>
                                <p class="text-base font-bold" style="color: #EF4444;">{{ $checkup->tekanan_darah }}</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Hasil Lab -->
                        @if($checkup->gula_darah || $checkup->kolesterol)
                        <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-200">
                            @if($checkup->gula_darah)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Gula Darah</p>
                                <p class="text-base font-semibold text-gray-900">{{ $checkup->gula_darah }} mg/dL</p>
                            </div>
                            @endif
                            @if($checkup->kolesterol)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Kolesterol</p>
                                <p class="text-base font-semibold text-gray-900">{{ $checkup->kolesterol }} mg/dL</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Informasi Medis -->
                        @if($checkup->rumah_sakit || $checkup->nama_dokter)
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            @if($checkup->rumah_sakit)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Rumah Sakit</p>
                                <p class="text-sm font-medium text-gray-900">{{ $checkup->rumah_sakit }}</p>
                            </div>
                            @endif
                            @if($checkup->nama_dokter)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nama Dokter</p>
                                <p class="text-sm font-medium text-gray-900">{{ $checkup->nama_dokter }}</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Diagnosis -->
                        @if($checkup->diagnosis)
                        <div class="mb-3">
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);">
                                    <i class="fas fa-stethoscope text-sm" style="color: #EF4444;"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Diagnosis</p>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $checkup->diagnosis }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Catatan Tambahan -->
                        @if($checkup->catatan_tambahan)
                        <div>
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);">
                                    <i class="fas fa-clipboard text-sm" style="color: #D97706;"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan</p>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $checkup->catatan_tambahan }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);">
                    <i class="fas fa-heartbeat text-3xl" style="color: #EF4444;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada data medical checkup</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah" untuk menambahkan riwayat medical checkup</p>
            </div>
        @endif
    </div>

    <!-- Akun Pengguna -->
    <div id="akun-pengguna" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fas fa-user-circle mr-2" style="color: #3B82C8;"></i>
                Akun Pengguna
            </h3>
        </div>
        
        @if($karyawan->user)
            <div class="space-y-4">
                <!-- Email Section -->
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #3B82C8; background: linear-gradient(to right, #F0F9FF 0%, #FFFFFF 100%);">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="text-base font-bold" style="color: #3B82C8;">{{ $karyawan->user->email }}</p>
                        </div>
                        <button onclick="openGantiEmailModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-edit mr-1"></i>Ganti Email
                        </button>
                    </div>
                </div>

                <!-- Role Section -->
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #8B5CF6; background: linear-gradient(to right, #F5F3FF 0%, #FFFFFF 100%);">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Role / Hak Akses</p>
                            <p class="text-base font-bold text-purple-700">{{ $karyawan->user->getRoleDisplayName() }}</p>
                        </div>
                        <button onclick="openGantiRoleModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                            <i class="fas fa-user-shield mr-1"></i>Ganti Role
                        </button>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #F59E0B; background: linear-gradient(to right, #FFFBEB 0%, #FFFFFF 100%);">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Password</p>
                            <p class="text-base font-medium text-gray-900">••••••••</p>
                        </div>
                        <button onclick="openResetPasswordModal()" class="px-4 py-2 text-white rounded-lg hover:shadow-lg transition text-sm font-medium" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                            <i class="fas fa-key mr-1"></i>Reset Password
                        </button>
                    </div>
                </div>

                <!-- Status Akun Section -->
                <div class="border-l-4 rounded-lg p-5" style="border-left-color: #10B981; background: linear-gradient(to right, #ECFDF5 0%, #FFFFFF 100%);">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Status Akun</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $karyawan->user->is_active ? '#D1FAE5' : '#FEE2E2' }}; color: {{ $karyawan->user->is_active ? '#065F46' : '#991B1B' }};">
                                {{ $karyawan->user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 100%);">
                    <i class="fas fa-user-circle text-3xl" style="color: #3B82C8;"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada akun pengguna</p>
                <p class="text-sm text-gray-400 mt-1">Karyawan ini belum memiliki akun login</p>
            </div>
        @endif
    </div>

    <!-- Kartu Akses -->
    <div id="kartu-akses" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 flex items-center mb-4">
            <i class="fas fa-id-badge mr-2" style="color: #3B82C8;"></i>
            Kartu Akses
        </h3>
        <p class="text-gray-500 text-center py-8">Belum ada data kartu akses</p>
    </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Nama Karyawan -->
<div id="editNamaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Nama Karyawan</h3>
            <button onclick="closeEditNamaModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editNamaForm" action="{{ route('perusahaan.karyawans.update-nama', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ $karyawan->nama_lengkap }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeEditNamaModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Informasi Pekerjaan -->
<div id="editPekerjaanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Informasi Pekerjaan</h3>
            <button onclick="closeEditPekerjaanModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editPekerjaanForm" action="{{ route('perusahaan.karyawans.update-pekerjaan', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" id="projectSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="loadJabatansByProject()">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                    {{ $karyawan->jabatan && $karyawan->jabatan->projects->contains($project->id) ? 'selected' : '' }}>
                                {{ $project->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <select name="jabatan_id" id="jabatanSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih project terlebih dahulu</option>
                        @if($karyawan->jabatan)
                            <option value="{{ $karyawan->jabatan->id }}" selected>{{ $karyawan->jabatan->nama }}</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Karyawan</label>
                    <select name="status_karyawan" id="statusKaryawanSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="toggleTanggalKeluar()">
                        <option value="">Pilih Status</option>
                        @foreach($statusKaryawans as $status)
                            <option value="{{ $status->nama }}" {{ $karyawan->status_karyawan == $status->nama ? 'selected' : '' }}>
                                {{ $status->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="{{ $karyawan->tanggal_masuk->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div id="tanggalKeluarWrapper" class="{{ str_contains(strtolower($karyawan->status_karyawan), 'kontrak') ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Berakhir Kontrak
                        <span id="requiredStar" class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_keluar" id="tanggalKeluarInput" value="{{ $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Wajib diisi untuk karyawan kontrak</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Aktif</label>
                    <select name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="1" {{ $karyawan->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$karyawan->is_active ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditPekerjaanModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Data Pribadi -->
<div id="editPribadiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Data Pribadi</h3>
            <button onclick="closeEditPribadiModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editPribadiForm" action="{{ route('perusahaan.karyawans.update-pribadi', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIK KTP <span class="text-red-500">*</span></label>
                    <input type="text" name="nik_ktp" value="{{ $karyawan->nik_ktp }}" maxlength="16" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-500 mt-1">16 digit</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" name="tempat_lahir" value="{{ $karyawan->tempat_lahir }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ $karyawan->tanggal_lahir->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ $karyawan->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $karyawan->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" value="{{ $karyawan->telepon }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-ring mr-1" style="color: #3B82C8;"></i>
                        Status Perkawinan <span class="text-red-500">*</span>
                    </label>
                    <select name="status_perkawinan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="TK" {{ ($karyawan->status_perkawinan ?? 'TK') == 'TK' ? 'selected' : '' }}>Tidak Kawin (TK)</option>
                        <option value="K" {{ ($karyawan->status_perkawinan ?? 'TK') == 'K' ? 'selected' : '' }}>Kawin (K)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Untuk perhitungan PTKP pajak</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-1" style="color: #3B82C8;"></i>
                        Jumlah Tanggungan <span class="text-red-500">*</span>
                    </label>
                    <select name="jumlah_tanggungan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="0" {{ ($karyawan->jumlah_tanggungan ?? 0) == 0 ? 'selected' : '' }}>0 (Tidak ada tanggungan)</option>
                        <option value="1" {{ ($karyawan->jumlah_tanggungan ?? 0) == 1 ? 'selected' : '' }}>1 (Satu tanggungan)</option>
                        <option value="2" {{ ($karyawan->jumlah_tanggungan ?? 0) == 2 ? 'selected' : '' }}>2 (Dua tanggungan)</option>
                        <option value="3" {{ ($karyawan->jumlah_tanggungan ?? 0) == 3 ? 'selected' : '' }}>3 (Tiga tanggungan)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Maksimal 3 tanggungan untuk PTKP</p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ $karyawan->user->email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="alamat" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ $karyawan->alamat }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kota <span class="text-red-500">*</span></label>
                    <input type="text" name="kota" value="{{ $karyawan->kota }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                    <input type="text" name="provinsi" value="{{ $karyawan->provinsi }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditPribadiModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Pengalaman Kerja -->
<div id="tambahPengalamanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Pengalaman Kerja</h3>
            <button onclick="closeTambahPengalamanModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="tambahPengalamanForm" action="{{ route('perusahaan.karyawans.pengalaman-kerja.store', $karyawan->hash_id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_perusahaan" placeholder="Contoh: PT. ABC Indonesia" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="jabatan" placeholder="Contoh: Senior Developer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div id="tanggalSelesaiWrapper">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggalSelesaiInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="masih_bekerja" id="masihBekerjaCheckbox" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="toggleTanggalSelesai()">
                    <label for="masihBekerjaCheckbox" class="ml-2 text-sm text-gray-700">Saya masih bekerja di sini</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi_pekerjaan" rows="3" placeholder="Jelaskan tanggung jawab dan tugas Anda..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencapaian</label>
                    <textarea name="pencapaian" rows="3" placeholder="Jelaskan pencapaian atau prestasi Anda..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeTambahPengalamanModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengalaman Kerja -->
<div id="editPengalamanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Pengalaman Kerja</h3>
            <button onclick="closeEditPengalamanModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editPengalamanForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_perusahaan" id="edit_nama_perusahaan" placeholder="Contoh: PT. ABC Indonesia" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="jabatan" id="edit_jabatan" placeholder="Contoh: Senior Developer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div id="editTanggalSelesaiWrapper">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="masih_bekerja" id="edit_masih_bekerja" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="toggleEditTanggalSelesai()">
                    <label for="edit_masih_bekerja" class="ml-2 text-sm text-gray-700">Saya masih bekerja di sini</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi_pekerjaan" id="edit_deskripsi_pekerjaan" rows="3" placeholder="Jelaskan tanggung jawab dan tugas Anda..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencapaian</label>
                    <textarea name="pencapaian" id="edit_pencapaian" rows="3" placeholder="Jelaskan pencapaian atau prestasi Anda..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditPengalamanModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Pendidikan -->
<div id="tambahPendidikanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Pendidikan</h3>
            <button onclick="closeTambahPendidikanModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="tambahPendidikanForm" action="{{ route('perusahaan.karyawans.pendidikan.store', $karyawan->hash_id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                        <select name="jenjang_pendidikan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                            <option value="">Pilih Jenjang</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Institusi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_institusi" placeholder="Nama sekolah/universitas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan/Program Studi</label>
                        <input type="text" name="jurusan" placeholder="Jurusan atau program studi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">IPK/Nilai</label>
                        <input type="text" name="ipk" placeholder="3.50" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Mulai <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_mulai" placeholder="2020" min="1900" max="{{ date('Y') + 10 }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Selesai <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_selesai" placeholder="2024" min="1900" max="{{ date('Y') + 10 }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeTambahPendidikanModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pendidikan -->
<div id="editPendidikanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Pendidikan</h3>
            <button onclick="closeEditPendidikanModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editPendidikanForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                        <select name="jenjang_pendidikan" id="edit_jenjang_pendidikan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                            <option value="">Pilih Jenjang</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Institusi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_institusi" id="edit_nama_institusi" placeholder="Nama sekolah/universitas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan/Program Studi</label>
                        <input type="text" name="jurusan" id="edit_jurusan" placeholder="Jurusan atau program studi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">IPK/Nilai</label>
                        <input type="text" name="ipk" id="edit_ipk" placeholder="3.50" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Mulai <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_mulai" id="edit_tahun_mulai" placeholder="2020" min="1900" max="{{ date('Y') + 10 }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Selesai <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_selesai" id="edit_tahun_selesai" placeholder="2024" min="1900" max="{{ date('Y') + 10 }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditPendidikanModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Sertifikasi -->
<div id="tambahSertifikasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Sertifikasi</h3>
            <button onclick="closeTambahSertifikasiModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="tambahSertifikasiForm" action="{{ route('perusahaan.karyawans.sertifikasi.store', $karyawan->hash_id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sertifikasi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sertifikasi" placeholder="Nama sertifikasi atau pelatihan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="penerbit" placeholder="Institusi atau organisasi penerbit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terbit <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_terbit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Expired</label>
                        <input type="date" name="tanggal_expired" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika sertifikat permanen</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Sertifikat</label>
                    <input type="text" name="nomor_sertifikat" placeholder="Nomor sertifikat (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL Sertifikat</label>
                    <input type="url" name="url_sertifikat" placeholder="https://example.com/certificate.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <p class="text-xs text-gray-500 mt-1">Link ke file sertifikat digital (optional)</p>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeTambahSertifikasiModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Sertifikasi -->
<div id="editSertifikasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Sertifikasi</h3>
            <button onclick="closeEditSertifikasiModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editSertifikasiForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sertifikasi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sertifikasi" id="edit_nama_sertifikasi" placeholder="Nama sertifikasi atau pelatihan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit <span class="text-red-500">*</span></label>
                    <input type="text" name="penerbit" id="edit_penerbit" placeholder="Institusi atau organisasi penerbit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terbit <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_terbit" id="edit_tanggal_terbit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Expired</label>
                        <input type="date" name="tanggal_expired" id="edit_tanggal_expired" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika sertifikat permanen</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Sertifikat</label>
                    <input type="text" name="nomor_sertifikat" id="edit_nomor_sertifikat" placeholder="Nomor sertifikat (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL Sertifikat</label>
                    <input type="url" name="url_sertifikat" id="edit_url_sertifikat" placeholder="https://example.com/certificate.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <p class="text-xs text-gray-500 mt-1">Link ke file sertifikat digital (optional)</p>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditSertifikasiModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Rekening Bank -->
<div id="tambahRekeningModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Rekening Bank</h3>
            <button onclick="closeTambahRekeningModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="tambahRekeningForm" action="{{ route('perusahaan.karyawans.update-rekening-bank', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_bank" placeholder="Contoh: Bank BCA" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_rekening" placeholder="Contoh: 1234567890" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pemilik_rekening" placeholder="Sesuai dengan buku tabungan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cabang Bank</label>
                    <input type="text" name="cabang_bank" placeholder="Contoh: KCP Jakarta Pusat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeTambahRekeningModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Rekening Bank -->
<div id="editRekeningModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Rekening Bank</h3>
            <button onclick="closeEditRekeningModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editRekeningForm" action="{{ route('perusahaan.karyawans.update-rekening-bank', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_bank" value="{{ $karyawan->nama_bank }}" placeholder="Contoh: Bank BCA" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_rekening" value="{{ $karyawan->nomor_rekening }}" placeholder="Contoh: 1234567890" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pemilik_rekening" value="{{ $karyawan->nama_pemilik_rekening }}" placeholder="Sesuai dengan buku tabungan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cabang Bank</label>
                    <input type="text" name="cabang_bank" value="{{ $karyawan->cabang_bank }}" placeholder="Contoh: KCP Jakarta Pusat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditRekeningModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit BPJS -->
<div id="editBpjsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-5 mx-auto p-5 border w-[800px] shadow-lg rounded-xl bg-white mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Data BPJS</h3>
            <button onclick="closeEditBpjsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editBpjsForm" action="{{ route('perusahaan.karyawans.update-bpjs', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6 max-h-[70vh] overflow-y-auto pr-2">
                
                <!-- BPJS Kesehatan -->
                <div class="border-2 border-red-200 rounded-lg p-4" style="background: linear-gradient(to right, #FEE2E2 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-hospital text-xl" style="color: #EF4444;"></i>
                        <h4 class="text-base font-bold text-gray-900">BPJS Kesehatan</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor BPJS</label>
                            <input type="text" name="bpjs_kesehatan_nomor" value="{{ $karyawan->bpjs_kesehatan_nomor }}" placeholder="Nomor BPJS (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terdaftar</label>
                            <input type="date" name="bpjs_kesehatan_tanggal_terdaftar" value="{{ $karyawan->bpjs_kesehatan_tanggal_terdaftar ? $karyawan->bpjs_kesehatan_tanggal_terdaftar->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="bpjs_kesehatan_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Pilih Status</option>
                                <option value="Aktif" {{ $karyawan->bpjs_kesehatan_status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ $karyawan->bpjs_kesehatan_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="bpjs_kesehatan_catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">{{ $karyawan->bpjs_kesehatan_catatan }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BPJS Ketenagakerjaan - JKM -->
                <div class="border-2 border-purple-200 rounded-lg p-4" style="background: linear-gradient(to right, #FAF5FF 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-shield-alt text-xl" style="color: #8B5CF6;"></i>
                        <h4 class="text-base font-bold text-gray-900">Jaminan Kematian (JKM)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor BPJS</label>
                            <input type="text" name="bpjs_jkm_nomor" value="{{ $karyawan->bpjs_jkm_nomor }}" placeholder="Nomor BPJS (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NPP (Nomor Peserta Program)</label>
                            <input type="text" name="bpjs_jkm_npp" value="{{ $karyawan->bpjs_jkm_npp }}" placeholder="NPP (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terdaftar</label>
                            <input type="date" name="bpjs_jkm_tanggal_terdaftar" value="{{ $karyawan->bpjs_jkm_tanggal_terdaftar ? $karyawan->bpjs_jkm_tanggal_terdaftar->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="bpjs_jkm_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Pilih Status</option>
                                <option value="Aktif" {{ $karyawan->bpjs_jkm_status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ $karyawan->bpjs_jkm_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="bpjs_jkm_catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ $karyawan->bpjs_jkm_catatan }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BPJS Ketenagakerjaan - JKK -->
                <div class="border-2 border-yellow-200 rounded-lg p-4" style="background: linear-gradient(to right, #FFFBEB 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-exclamation-triangle text-xl" style="color: #F59E0B;"></i>
                        <h4 class="text-base font-bold text-gray-900">Jaminan Kecelakaan Kerja (JKK)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor BPJS</label>
                            <input type="text" name="bpjs_jkk_nomor" value="{{ $karyawan->bpjs_jkk_nomor }}" placeholder="Nomor BPJS (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NPP (Nomor Peserta Program)</label>
                            <input type="text" name="bpjs_jkk_npp" value="{{ $karyawan->bpjs_jkk_npp }}" placeholder="NPP (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terdaftar</label>
                            <input type="date" name="bpjs_jkk_tanggal_terdaftar" value="{{ $karyawan->bpjs_jkk_tanggal_terdaftar ? $karyawan->bpjs_jkk_tanggal_terdaftar->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="bpjs_jkk_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Pilih Status</option>
                                <option value="Aktif" {{ $karyawan->bpjs_jkk_status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ $karyawan->bpjs_jkk_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="bpjs_jkk_catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">{{ $karyawan->bpjs_jkk_catatan }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BPJS Ketenagakerjaan - JP -->
                <div class="border-2 border-green-200 rounded-lg p-4" style="background: linear-gradient(to right, #ECFDF5 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-landmark text-xl" style="color: #10B981;"></i>
                        <h4 class="text-base font-bold text-gray-900">Jaminan Pensiun (JP)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor BPJS</label>
                            <input type="text" name="bpjs_jp_nomor" value="{{ $karyawan->bpjs_jp_nomor }}" placeholder="Nomor BPJS (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NPP (Nomor Peserta Program)</label>
                            <input type="text" name="bpjs_jp_npp" value="{{ $karyawan->bpjs_jp_npp }}" placeholder="NPP (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terdaftar</label>
                            <input type="date" name="bpjs_jp_tanggal_terdaftar" value="{{ $karyawan->bpjs_jp_tanggal_terdaftar ? $karyawan->bpjs_jp_tanggal_terdaftar->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="bpjs_jp_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Pilih Status</option>
                                <option value="Aktif" {{ $karyawan->bpjs_jp_status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ $karyawan->bpjs_jp_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="bpjs_jp_catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">{{ $karyawan->bpjs_jp_catatan }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BPJS Ketenagakerjaan - JHT -->
                <div class="border-2 border-blue-200 rounded-lg p-4" style="background: linear-gradient(to right, #EFF6FF 0%, #FFFFFF 100%);">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-coins text-xl" style="color: #3B82F6;"></i>
                        <h4 class="text-base font-bold text-gray-900">Jaminan Hari Tua (JHT)</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor BPJS</label>
                            <input type="text" name="bpjs_jht_nomor" value="{{ $karyawan->bpjs_jht_nomor }}" placeholder="Nomor BPJS (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NPP (Nomor Peserta Program)</label>
                            <input type="text" name="bpjs_jht_npp" value="{{ $karyawan->bpjs_jht_npp }}" placeholder="NPP (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terdaftar</label>
                            <input type="date" name="bpjs_jht_tanggal_terdaftar" value="{{ $karyawan->bpjs_jht_tanggal_terdaftar ? $karyawan->bpjs_jht_tanggal_terdaftar->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="bpjs_jht_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Status</option>
                                <option value="Aktif" {{ $karyawan->bpjs_jht_status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ $karyawan->bpjs_jht_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="bpjs_jht_catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $karyawan->bpjs_jht_catatan }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditBpjsModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Medical Checkup -->
<div id="tambahMedicalCheckupModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-5 mx-auto p-5 border w-[700px] shadow-lg rounded-xl bg-white mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Medical Checkup</h3>
            <button onclick="closeTambahMedicalCheckupModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="tambahMedicalCheckupForm" action="{{ route('perusahaan.karyawans.medical-checkup.store', $karyawan->hash_id) }}" method="POST">
            @csrf
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                
                <!-- Informasi Dasar -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Informasi Dasar</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Checkup <span class="text-red-500">*</span></label>
                            <select name="jenis_checkup" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                                <option value="">Pilih jenis checkup</option>
                                <option value="Medical Checkup Rutin">Medical Checkup Rutin</option>
                                <option value="Medical Checkup Tahunan">Medical Checkup Tahunan</option>
                                <option value="Pemeriksaan Khusus">Pemeriksaan Khusus</option>
                                <option value="Pre-Employment">Pre-Employment</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Checkup <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_checkup" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Kesehatan <span class="text-red-500">*</span></label>
                            <select name="status_kesehatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                                <option value="">Pilih status</option>
                                <option value="Sehat">Sehat</option>
                                <option value="Perlu Perhatian">Perlu Perhatian</option>
                                <option value="Tidak Sehat">Tidak Sehat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pengukuran Fisik -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Pengukuran Fisik</h4>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tinggi Badan (cm)</label>
                            <input type="number" step="0.01" name="tinggi_badan" placeholder="170" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="berat_badan" placeholder="70" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Golongan Darah</label>
                            <select name="golongan_darah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Pilih golongan darah</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tekanan Darah</label>
                            <input type="text" name="tekanan_darah" placeholder="120/80" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Hasil Lab -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Hasil Lab</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.01" name="gula_darah" placeholder="90" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.01" name="kolesterol" placeholder="200" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Informasi Medis -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Informasi Medis</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rumah Sakit</label>
                            <input type="text" name="rumah_sakit" placeholder="RS Siloam" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokter</label>
                            <input type="text" name="nama_dokter" placeholder="Dr. John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                        <textarea name="diagnosis" rows="3" placeholder="Diagnosis atau temuan medis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                        <textarea name="catatan_tambahan" rows="3" placeholder="Catatan atau rekomendasi tambahan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                </div>

            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeTambahMedicalCheckupModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Medical Checkup -->
<div id="editMedicalCheckupModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-5 mx-auto p-5 border w-[700px] shadow-lg rounded-xl bg-white mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Medical Checkup</h3>
            <button onclick="closeEditMedicalCheckupModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editMedicalCheckupForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                
                <!-- Informasi Dasar -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Informasi Dasar</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Checkup <span class="text-red-500">*</span></label>
                            <select name="jenis_checkup" id="edit_jenis_checkup" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                                <option value="">Pilih jenis checkup</option>
                                <option value="Medical Checkup Rutin">Medical Checkup Rutin</option>
                                <option value="Medical Checkup Tahunan">Medical Checkup Tahunan</option>
                                <option value="Pemeriksaan Khusus">Pemeriksaan Khusus</option>
                                <option value="Pre-Employment">Pre-Employment</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Checkup <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_checkup" id="edit_tanggal_checkup" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Kesehatan <span class="text-red-500">*</span></label>
                            <select name="status_kesehatan" id="edit_status_kesehatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                                <option value="">Pilih status</option>
                                <option value="Sehat">Sehat</option>
                                <option value="Perlu Perhatian">Perlu Perhatian</option>
                                <option value="Tidak Sehat">Tidak Sehat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pengukuran Fisik -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Pengukuran Fisik</h4>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tinggi Badan (cm)</label>
                            <input type="number" step="0.01" name="tinggi_badan" id="edit_tinggi_badan" placeholder="170" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="berat_badan" id="edit_berat_badan" placeholder="70" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Golongan Darah</label>
                            <select name="golongan_darah" id="edit_golongan_darah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Pilih golongan darah</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tekanan Darah</label>
                            <input type="text" name="tekanan_darah" id="edit_tekanan_darah" placeholder="120/80" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Hasil Lab -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Hasil Lab</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.01" name="gula_darah" id="edit_gula_darah" placeholder="90" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.01" name="kolesterol" id="edit_kolesterol" placeholder="200" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Informasi Medis -->
                <div class="border-b pb-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Informasi Medis</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rumah Sakit</label>
                            <input type="text" name="rumah_sakit" id="edit_rumah_sakit" placeholder="RS Siloam" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokter</label>
                            <input type="text" name="nama_dokter" id="edit_nama_dokter" placeholder="Dr. John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                        <textarea name="diagnosis" id="edit_diagnosis" rows="3" placeholder="Diagnosis atau temuan medis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                        <textarea name="catatan_tambahan" id="edit_catatan_tambahan" rows="3" placeholder="Catatan atau rekomendasi tambahan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                </div>

            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeEditMedicalCheckupModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ganti Email -->
<div id="gantiEmailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[450px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Ganti Email</h3>
            <button onclick="closeGantiEmailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="gantiEmailForm" action="{{ route('perusahaan.karyawans.update-email', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Saat Ini</label>
                    <input type="text" value="{{ $karyawan->user->email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Baru <span class="text-red-500">*</span></label>
                    <input type="email" name="email" placeholder="email@example.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-500 mt-1">Pastikan email valid dan dapat diakses</p>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeGantiEmailModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ganti Role -->
<div id="gantiRoleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[450px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Ganti Role / Hak Akses</h3>
            <button onclick="closeGantiRoleModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="gantiRoleForm" action="{{ route('perusahaan.karyawans.update-role', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Saat Ini</label>
                    <input type="text" value="{{ $karyawan->user->getRoleDisplayName() ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Baru <span class="text-red-500">*</span></label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="security_officer" {{ $karyawan->user->role == 'security_officer' ? 'selected' : '' }}>Security Officer</option>
                        <option value="office_employee" {{ $karyawan->user->role == 'office_employee' ? 'selected' : '' }}>Office Employee</option>
                        <option value="manager_project" {{ $karyawan->user->role == 'manager_project' ? 'selected' : '' }}>Manager Project</option>
                        <option value="admin_project" {{ $karyawan->user->role == 'admin_project' ? 'selected' : '' }}>Admin Project</option>
                        <option value="admin_branch" {{ $karyawan->user->role == 'admin_branch' ? 'selected' : '' }}>Admin Branch</option>
                        <option value="finance_branch" {{ $karyawan->user->role == 'finance_branch' ? 'selected' : '' }}>Finance Branch</option>
                        <option value="admin_hsse" {{ $karyawan->user->role == 'admin_hsse' ? 'selected' : '' }}>Admin HSSE</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih role sesuai dengan tanggung jawab karyawan</p>
                </div>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Perubahan role akan mempengaruhi hak akses karyawan di sistem.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeGantiRoleModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[450px] shadow-lg rounded-xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Reset Password</h3>
            <button onclick="closeResetPasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="resetPasswordForm" action="{{ route('perusahaan.karyawans.reset-password', $karyawan->hash_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Password akan direset dan pengguna harus menggunakan password baru untuk login.
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required minlength="8">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required minlength="8">
                </div>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="closeResetPasswordModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 text-white rounded-lg hover:shadow-lg transition" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show success/error messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#3B82C8'
    });
@endif

// Active tab from session
const activeTabFromSession = '{{ session('active_tab', 'informasi') }}';

// Menu titles dan subtitles
const menuTitles = {
    'informasi': {
        title: 'Informasi Karyawan',
        subtitle: 'Data lengkap karyawan'
    },
    'pengalaman-kerja': {
        title: 'Pengalaman Kerja',
        subtitle: 'Riwayat pengalaman kerja karyawan'
    },
    'pendidikan': {
        title: 'Pendidikan',
        subtitle: 'Riwayat pendidikan formal karyawan'
    },
    'sertifikasi': {
        title: 'Sertifikasi',
        subtitle: 'Sertifikat dan pelatihan yang dimiliki'
    },
    'rekening-bank': {
        title: 'Rekening Bank',
        subtitle: 'Informasi rekening bank untuk transfer gaji'
    },
    'bpjs': {
        title: 'BPJS',
        subtitle: 'Data kepesertaan BPJS Kesehatan dan Ketenagakerjaan'
    },
    'medical-checkup': {
        title: 'Medical Checkup',
        subtitle: 'Riwayat pemeriksaan kesehatan karyawan'
    },
    'akun-pengguna': {
        title: 'Akun Pengguna',
        subtitle: 'Informasi akun login dan akses sistem'
    },
    'kartu-akses': {
        title: 'Kartu Akses',
        subtitle: 'Kartu akses dan identitas karyawan'
    }
};

// Update page title
function updatePageTitle(menuId) {
    const menu = menuTitles[menuId];
    if (menu) {
        document.getElementById('pageTitle').textContent = menu.title;
        document.getElementById('pageSubtitle').textContent = menu.subtitle;
    }
}

// Show/hide sections based on active tab
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('[id="foto-karyawan"], [id="informasi"], [id="data-pribadi"], [id="pengalaman-kerja"], [id="pendidikan"], [id="sertifikasi"], [id="rekening-bank"], [id="bpjs"], [id="medical-checkup"], [id="akun-pengguna"], [id="kartu-akses"]').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section(s)
    if (sectionId === 'informasi') {
        // Show foto, informasi pekerjaan, and data pribadi for informasi tab
        const fotoSection = document.getElementById('foto-karyawan');
        const infoSection = document.getElementById('informasi');
        const pribadiSection = document.getElementById('data-pribadi');
        
        if (fotoSection) fotoSection.style.display = 'block';
        if (infoSection) infoSection.style.display = 'block';
        if (pribadiSection) pribadiSection.style.display = 'block';
    } else {
        const section = document.getElementById(sectionId);
        if (section) section.style.display = 'block';
    }
}

// Handle menu click
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all links
        document.querySelectorAll('.nav-link').forEach(l => {
            l.classList.remove('active');
            l.classList.add('text-gray-600', 'hover:bg-gray-50');
            l.classList.remove('text-white');
            l.style.background = '';
        });
        
        // Add active class to clicked link
        this.classList.add('active');
        this.classList.remove('text-gray-600', 'hover:bg-gray-50');
        this.classList.add('text-white');
        this.style.background = 'linear-gradient(135deg, #3B82C8 0%, #2563A8 100%)';
        
        // Update page title
        const targetId = this.getAttribute('data-target');
        updatePageTitle(targetId);
        
        // Show/hide sections
        showSection(targetId);
    });
});

// Set initial active state
document.addEventListener('DOMContentLoaded', function() {
    // Set active tab from session or default to informasi
    const activeLink = document.querySelector(`.nav-link[data-target="${activeTabFromSession}"]`) || 
                      document.querySelector('.nav-link[data-target="informasi"]');
    
    if (activeLink) {
        activeLink.style.background = 'linear-gradient(135deg, #3B82C8 0%, #2563A8 100%)';
        activeLink.classList.add('text-white', 'active');
        activeLink.classList.remove('text-gray-600');
        
        // Update title and show section
        updatePageTitle(activeTabFromSession);
        showSection(activeTabFromSession);
    }
});

// Modal functions
function openEditNamaModal() {
    document.getElementById('editNamaModal').classList.remove('hidden');
}

function closeEditNamaModal() {
    document.getElementById('editNamaModal').classList.add('hidden');
}

function openEditPekerjaanModal() {
    document.getElementById('editPekerjaanModal').classList.remove('hidden');
}

function closeEditPekerjaanModal() {
    document.getElementById('editPekerjaanModal').classList.add('hidden');
}

function openEditPribadiModal() {
    document.getElementById('editPribadiModal').classList.remove('hidden');
}

function closeEditPribadiModal() {
    document.getElementById('editPribadiModal').classList.add('hidden');
}

// Pengalaman Kerja Modal Functions
function openTambahPengalamanModal() {
    document.getElementById('tambahPengalamanModal').classList.remove('hidden');
}

function closeTambahPengalamanModal() {
    document.getElementById('tambahPengalamanModal').classList.add('hidden');
    document.getElementById('tambahPengalamanForm').reset();
}

function openEditPengalamanModal(hashId) {
    // Fetch data pengalaman kerja
    const pengalaman = @json($karyawan->pengalamanKerjas);
    const data = pengalaman.find(p => p.hash_id === hashId);
    
    if (data) {
        document.getElementById('edit_nama_perusahaan').value = data.nama_perusahaan;
        document.getElementById('edit_jabatan').value = data.jabatan;
        
        // Format tanggal ke Y-m-d untuk input date
        const tanggalMulai = new Date(data.tanggal_mulai);
        const formattedMulai = tanggalMulai.toISOString().split('T')[0];
        document.getElementById('edit_tanggal_mulai').value = formattedMulai;
        
        if (data.tanggal_selesai) {
            const tanggalSelesai = new Date(data.tanggal_selesai);
            const formattedSelesai = tanggalSelesai.toISOString().split('T')[0];
            document.getElementById('edit_tanggal_selesai').value = formattedSelesai;
        } else {
            document.getElementById('edit_tanggal_selesai').value = '';
        }
        
        document.getElementById('edit_masih_bekerja').checked = data.masih_bekerja;
        document.getElementById('edit_deskripsi_pekerjaan').value = data.deskripsi_pekerjaan || '';
        document.getElementById('edit_pencapaian').value = data.pencapaian || '';
        
        // Set form action
        document.getElementById('editPengalamanForm').action = 
            `/perusahaan/karyawans/{{ $karyawan->hash_id }}/pengalaman-kerja/${hashId}`;
        
        // Toggle tanggal selesai
        toggleEditTanggalSelesai();
        
        document.getElementById('editPengalamanModal').classList.remove('hidden');
    }
}

function closeEditPengalamanModal() {
    document.getElementById('editPengalamanModal').classList.add('hidden');
}

function toggleTanggalSelesai() {
    const checkbox = document.getElementById('masihBekerjaCheckbox');
    const wrapper = document.getElementById('tanggalSelesaiWrapper');
    const input = document.getElementById('tanggalSelesaiInput');
    
    if (checkbox.checked) {
        wrapper.classList.add('hidden');
        input.value = '';
        input.removeAttribute('required');
    } else {
        wrapper.classList.remove('hidden');
    }
}

function toggleEditTanggalSelesai() {
    const checkbox = document.getElementById('edit_masih_bekerja');
    const wrapper = document.getElementById('editTanggalSelesaiWrapper');
    const input = document.getElementById('edit_tanggal_selesai');
    
    if (checkbox.checked) {
        wrapper.classList.add('hidden');
        input.value = '';
        input.removeAttribute('required');
    } else {
        wrapper.classList.remove('hidden');
    }
}

function deletePengalaman(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data pengalaman kerja tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/karyawans/{{ $karyawan->hash_id }}/pengalaman-kerja/${hashId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}

// Pendidikan Modal Functions
function openTambahPendidikanModal() {
    document.getElementById('tambahPendidikanModal').classList.remove('hidden');
}

function closeTambahPendidikanModal() {
    document.getElementById('tambahPendidikanModal').classList.add('hidden');
    document.getElementById('tambahPendidikanForm').reset();
}

function openEditPendidikanModal(hashId) {
    // Fetch data pendidikan
    const pendidikan = @json($karyawan->pendidikans);
    const data = pendidikan.find(p => p.hash_id === hashId);
    
    if (data) {
        document.getElementById('edit_jenjang_pendidikan').value = data.jenjang_pendidikan;
        document.getElementById('edit_nama_institusi').value = data.nama_institusi;
        document.getElementById('edit_jurusan').value = data.jurusan || '';
        document.getElementById('edit_ipk').value = data.ipk || '';
        document.getElementById('edit_tahun_mulai').value = data.tahun_mulai;
        document.getElementById('edit_tahun_selesai').value = data.tahun_selesai;
        
        // Set form action
        document.getElementById('editPendidikanForm').action = 
            `/perusahaan/karyawans/{{ $karyawan->hash_id }}/pendidikan/${hashId}`;
        
        document.getElementById('editPendidikanModal').classList.remove('hidden');
    }
}

function closeEditPendidikanModal() {
    document.getElementById('editPendidikanModal').classList.add('hidden');
}

function deletePendidikan(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data pendidikan tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/karyawans/{{ $karyawan->hash_id }}/pendidikan/${hashId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}

// Sertifikasi Modal Functions
function openTambahSertifikasiModal() {
    document.getElementById('tambahSertifikasiModal').classList.remove('hidden');
}

function closeTambahSertifikasiModal() {
    document.getElementById('tambahSertifikasiModal').classList.add('hidden');
    document.getElementById('tambahSertifikasiForm').reset();
}

// Rekening Bank Modal Functions
function openTambahRekeningModal() {
    document.getElementById('tambahRekeningModal').classList.remove('hidden');
}

function closeTambahRekeningModal() {
    document.getElementById('tambahRekeningModal').classList.add('hidden');
    document.getElementById('tambahRekeningForm').reset();
}

function openEditRekeningModal() {
    document.getElementById('editRekeningModal').classList.remove('hidden');
}

function closeEditRekeningModal() {
    document.getElementById('editRekeningModal').classList.add('hidden');
}

// BPJS Modal Functions
function openEditBpjsModal() {
    document.getElementById('editBpjsModal').classList.remove('hidden');
}

function closeEditBpjsModal() {
    document.getElementById('editBpjsModal').classList.add('hidden');
}

// Medical Checkup Modal Functions
function openTambahMedicalCheckupModal() {
    document.getElementById('tambahMedicalCheckupModal').classList.remove('hidden');
}

function closeTambahMedicalCheckupModal() {
    document.getElementById('tambahMedicalCheckupModal').classList.add('hidden');
    document.getElementById('tambahMedicalCheckupForm').reset();
}

function openEditMedicalCheckupModal(hashId) {
    const checkups = @json($karyawan->medicalCheckups);
    const data = checkups.find(c => c.hash_id === hashId);
    
    if (data) {
        document.getElementById('edit_jenis_checkup').value = data.jenis_checkup;
        
        const tanggalCheckup = new Date(data.tanggal_checkup);
        document.getElementById('edit_tanggal_checkup').value = tanggalCheckup.toISOString().split('T')[0];
        
        document.getElementById('edit_status_kesehatan').value = data.status_kesehatan;
        document.getElementById('edit_tinggi_badan').value = data.tinggi_badan || '';
        document.getElementById('edit_berat_badan').value = data.berat_badan || '';
        document.getElementById('edit_golongan_darah').value = data.golongan_darah || '';
        document.getElementById('edit_tekanan_darah').value = data.tekanan_darah || '';
        document.getElementById('edit_gula_darah').value = data.gula_darah || '';
        document.getElementById('edit_kolesterol').value = data.kolesterol || '';
        document.getElementById('edit_rumah_sakit').value = data.rumah_sakit || '';
        document.getElementById('edit_nama_dokter').value = data.nama_dokter || '';
        document.getElementById('edit_diagnosis').value = data.diagnosis || '';
        document.getElementById('edit_catatan_tambahan').value = data.catatan_tambahan || '';
        
        document.getElementById('editMedicalCheckupForm').action = 
            `/perusahaan/karyawans/{{ $karyawan->hash_id }}/medical-checkup/${hashId}`;
        
        document.getElementById('editMedicalCheckupModal').classList.remove('hidden');
    }
}

function closeEditMedicalCheckupModal() {
    document.getElementById('editMedicalCheckupModal').classList.add('hidden');
}

function deleteMedicalCheckup(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data medical checkup tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/karyawans/{{ $karyawan->hash_id }}/medical-checkup/${hashId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}

// Akun Pengguna Modal Functions
function openGantiEmailModal() {
    document.getElementById('gantiEmailModal').classList.remove('hidden');
}

function closeGantiEmailModal() {
    document.getElementById('gantiEmailModal').classList.add('hidden');
    document.getElementById('gantiEmailForm').reset();
}

function openGantiRoleModal() {
    document.getElementById('gantiRoleModal').classList.remove('hidden');
}

function closeGantiRoleModal() {
    document.getElementById('gantiRoleModal').classList.add('hidden');
    document.getElementById('gantiRoleForm').reset();
}

function openResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    document.getElementById('resetPasswordForm').reset();
}

function openEditSertifikasiModal(hashId) {
    // Fetch data sertifikasi
    const sertifikasi = @json($karyawan->sertifikasis);
    const data = sertifikasi.find(s => s.hash_id === hashId);
    
    if (data) {
        document.getElementById('edit_nama_sertifikasi').value = data.nama_sertifikasi;
        document.getElementById('edit_penerbit').value = data.penerbit;
        
        // Format tanggal ke Y-m-d
        const tanggalTerbit = new Date(data.tanggal_terbit);
        document.getElementById('edit_tanggal_terbit').value = tanggalTerbit.toISOString().split('T')[0];
        
        if (data.tanggal_expired) {
            const tanggalExpired = new Date(data.tanggal_expired);
            document.getElementById('edit_tanggal_expired').value = tanggalExpired.toISOString().split('T')[0];
        } else {
            document.getElementById('edit_tanggal_expired').value = '';
        }
        
        document.getElementById('edit_nomor_sertifikat').value = data.nomor_sertifikat || '';
        document.getElementById('edit_url_sertifikat').value = data.url_sertifikat || '';
        
        // Set form action
        document.getElementById('editSertifikasiForm').action = 
            `/perusahaan/karyawans/{{ $karyawan->hash_id }}/sertifikasi/${hashId}`;
        
        document.getElementById('editSertifikasiModal').classList.remove('hidden');
    }
}

function closeEditSertifikasiModal() {
    document.getElementById('editSertifikasiModal').classList.add('hidden');
}

function deleteSertifikasi(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data sertifikasi tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/perusahaan/karyawans/{{ $karyawan->hash_id }}/sertifikasi/${hashId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}

// Load jabatan based on selected project
function loadJabatansByProject() {
    const projectSelect = document.getElementById('projectSelect');
    const jabatanSelect = document.getElementById('jabatanSelect');
    const projectId = projectSelect.value;
    
    if (!projectId) {
        jabatanSelect.innerHTML = '<option value="">Pilih project terlebih dahulu</option>';
        jabatanSelect.disabled = true;
        return;
    }
    
    // Show loading
    jabatanSelect.innerHTML = '<option value="">Loading...</option>';
    jabatanSelect.disabled = true;
    
    // Fetch jabatan by project
    fetch(`/perusahaan/projects/${projectId}/jabatans`)
        .then(response => response.json())
        .then(data => {
            jabatanSelect.innerHTML = '<option value="">Pilih Jabatan</option>';
            data.forEach(jabatan => {
                const option = document.createElement('option');
                option.value = jabatan.id;
                option.textContent = jabatan.nama;
                jabatanSelect.appendChild(option);
            });
            jabatanSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading jabatan:', error);
            jabatanSelect.innerHTML = '<option value="">Error loading jabatan</option>';
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal memuat data jabatan',
                confirmButtonColor: '#3B82C8'
            });
        });
}

// Toggle tanggal keluar based on status karyawan
function toggleTanggalKeluar() {
    const statusSelect = document.getElementById('statusKaryawanSelect');
    const tanggalKeluarWrapper = document.getElementById('tanggalKeluarWrapper');
    const tanggalKeluarInput = document.getElementById('tanggalKeluarInput');
    
    const selectedStatus = statusSelect.value.toLowerCase();
    const isKontrak = selectedStatus.includes('kontrak');
    
    if (isKontrak) {
        tanggalKeluarWrapper.classList.remove('hidden');
        tanggalKeluarInput.setAttribute('required', 'required');
    } else {
        tanggalKeluarWrapper.classList.add('hidden');
        tanggalKeluarInput.removeAttribute('required');
        tanggalKeluarInput.value = '';
    }
}

// Handle form submissions
document.getElementById('editNamaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editPekerjaanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editPribadiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('tambahPengalamanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editPengalamanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('tambahPendidikanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editPendidikanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('tambahSertifikasiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editSertifikasiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('tambahRekeningForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editRekeningForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editBpjsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('tambahMedicalCheckupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('editMedicalCheckupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('gantiEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Mereset password...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    this.submit();
});

function openEditModal(section) {
    // Redirect to edit page with section parameter
    window.location.href = '{{ route("perusahaan.karyawans.edit", $karyawan->hash_id) }}?section=' + section;
}

function previewAndUploadFoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'File harus berupa gambar (JPG, PNG)',
                confirmButtonColor: '#3B82C8'
            });
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Ukuran file maksimal 2MB',
                confirmButtonColor: '#3B82C8'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Mengupload foto...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('fotoPreview').innerHTML = 
                '<img src="' + e.target.result + '" alt="Preview" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
        
        // Submit form
        document.getElementById('uploadFotoForm').submit();
    }
}
</script>
@endpush
