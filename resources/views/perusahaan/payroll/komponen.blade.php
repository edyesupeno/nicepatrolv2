@extends('perusahaan.layouts.app')

@section('content')
<div class="p-6">
    
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-puzzle-piece text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Komponen Payroll</h1>
                <p class="text-sm text-gray-600">Kelola komponen gaji (tunjangan & potongan)</p>
            </div>
        </div>
        <button onclick="openModal('create')" class="inline-flex items-center gap-2 px-5 py-2.5 text-white rounded-lg font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <i class="fas fa-plus"></i>
            Tambah Komponen
        </button>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-blue-900 mb-2">Jenis Komponen</h3>
                <div class="space-y-1 text-xs text-blue-800">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-hand-pointer text-blue-600 mt-0.5"></i>
                        <span><strong>Manual:</strong> Dapat ditambahkan ke template dan diatur nilainya secara manual</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-cog text-blue-600 mt-0.5"></i>
                        <span><strong>System:</strong> Dihitung otomatis oleh sistem berdasarkan pengaturan (BPJS, PPh21, Lembur)</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-blue-300">
                    <div class="flex items-start gap-2 text-xs text-blue-800">
                        <i class="fas fa-lightbulb text-yellow-600 mt-0.5"></i>
                        <span><strong>Catatan:</strong> BPJS dan Pajak tidak perlu dimasukkan di komponen ini karena sudah di-handle otomatis oleh sistem melalui menu <strong>Setting Payroll</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-puzzle-piece text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Komponen</p>
                    <p class="text-xl font-bold text-gray-800">{{ $komponens->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tunjangan</p>
                    <p class="text-xl font-bold text-gray-800">{{ $komponens->where('jenis', 'Tunjangan')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Potongan</p>
                    <p class="text-xl font-bold text-gray-800">{{ $komponens->where('jenis', 'Potongan')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Aktif</p>
                    <p class="text-xl font-bold text-gray-800">{{ $komponens->where('aktif', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
        <form method="GET" action="{{ route('perusahaan.komponen-payroll.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <!-- Search Box -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama komponen, kode, atau deskripsi..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onkeyup="searchKomponen()">
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex gap-2">
                <select name="project_id" id="projectFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Project</option>
                    <option value="global" {{ request('project_id') === 'global' ? 'selected' : '' }}>Global Only</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
                    @endforeach
                </select>
                
                <select name="status" id="statusFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                
                <select name="kategori" id="kategoriFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    <option value="Fixed" {{ request('kategori') === 'Fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="Variable" {{ request('kategori') === 'Variable' ? 'selected' : '' }}>Variable</option>
                </select>
                
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-search mr-1"></i>Cari
                </button>
                
                <a href="{{ route('perusahaan.komponen-payroll.index') }}" class="px-3 py-2 text-gray-600 hover:text-gray-800 text-sm font-medium">
                    <i class="fas fa-times mr-1"></i>Reset
                </a>
            </div>
        </form>
        
        <!-- Search Results Info -->
        <div id="searchInfo" class="mt-3 text-sm text-gray-600 {{ request()->hasAny(['search', 'status', 'kategori', 'project_id']) ? '' : 'hidden' }}">
            <i class="fas fa-info-circle mr-1"></i>
            <span id="searchResultText">
                @if(request()->hasAny(['search', 'status', 'kategori', 'project_id']))
                    Menampilkan {{ $komponens->count() }} komponen dari pencarian
                    @if(request('search'))
                        : "{{ request('search') }}"
                    @endif
                    @if(request('project_id'))
                        , Project: {{ request('project_id') === 'global' ? 'Global Only' : ($projects->where('id', request('project_id'))->first()->nama ?? 'Unknown') }}
                    @endif
                    @if(request('status'))
                        , Status: {{ request('status') }}
                    @endif
                    @if(request('kategori'))
                        , Kategori: {{ request('kategori') }}
                    @endif
                @endif
            </span>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button type="button" onclick="filterJenis('Semua')" id="tab-semua" class="tab-filter px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                        Semua (<span id="count-semua">{{ $komponens->count() }}</span>)
                    </button>
                    <button type="button" onclick="filterJenis('Tunjangan')" id="tab-tunjangan" class="tab-filter px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                        Tunjangan (<span id="count-tunjangan">{{ $komponens->where('jenis', 'Tunjangan')->count() }}</span>)
                    </button>
                    <button type="button" onclick="filterJenis('Potongan')" id="tab-potongan" class="tab-filter px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                        Potongan (<span id="count-potongan">{{ $komponens->where('jenis', 'Potongan')->count() }}</span>)
                    </button>
                </nav>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Komponen</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Project</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nilai</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kena Pajak</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kelola</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($komponens as $komponen)
                        <tr class="hover:bg-gray-50 komponen-row" 
                            data-jenis="{{ $komponen->jenis }}"
                            data-nama="{{ strtolower($komponen->nama_komponen) }}"
                            data-kode="{{ strtolower($komponen->kode) }}"
                            data-deskripsi="{{ strtolower($komponen->deskripsi ?? '') }}"
                            data-kategori="{{ $komponen->kategori }}"
                            data-status="{{ $komponen->aktif ? 'aktif' : 'nonaktif' }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-sm text-gray-800">{{ $komponen->nama_komponen }}</div>
                                @if($komponen->deskripsi)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($komponen->deskripsi, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono">{{ $komponen->kode }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($komponen->project_id)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                                        <i class="fas fa-building mr-1"></i>{{ $komponen->project->nama }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-semibold">
                                        <i class="fas fa-globe mr-1"></i>Global
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($komponen->jenis == 'Tunjangan')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-arrow-up mr-1"></i>{{ $komponen->jenis }}
                                </span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-arrow-down mr-1"></i>{{ $komponen->jenis }}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-700">{{ $komponen->kategori }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-800">
                                    @if($komponen->tipe_perhitungan == 'Persentase')
                                    {{ number_format($komponen->nilai, 2, ',', '.') }}%
                                    @else
                                    Rp {{ number_format($komponen->nilai, 0, ',', '.') }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $komponen->tipe_perhitungan }}</div>
                                @if($komponen->nilai_maksimal && in_array($komponen->tipe_perhitungan, ['Per Hari Masuk', 'Lembur Per Hari']))
                                <div class="text-xs text-orange-600 mt-1">
                                    <i class="fas fa-limit mr-1"></i>
                                    Max: Rp {{ number_format($komponen->nilai_maksimal, 0, ',', '.') }}
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($komponen->kena_pajak)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Ya</span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Tidak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($komponen->aktif)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($komponen->boleh_edit)
                                <span class="text-xs text-blue-600">Manual</span>
                                @else
                                <span class="text-xs text-gray-500">Otomatis</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button onclick="openModal('edit', {{ $komponen->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                        Edit
                                    </button>
                                    <button onclick="deleteKomponen('{{ $komponen->hash_id }}')" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyState">
                            <td colspan="10" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-puzzle-piece text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-semibold">Belum ada komponen payroll</p>
                                    <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Komponen" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        
                        <!-- No Search Results State -->
                        <tr id="noSearchResults" class="hidden">
                            <td colspan="10" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-semibold">Tidak ada komponen yang ditemukan</p>
                                    <p class="text-gray-400 text-sm mt-1">Coba ubah kata kunci pencarian atau filter</p>
                                    <button onclick="clearFilters()" class="mt-3 px-4 py-2 text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                        <i class="fas fa-times mr-1"></i>Reset Filter
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Form -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form id="komponenForm" method="POST">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="">
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-lg">
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Komponen Baru</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                
                <!-- Nama Komponen -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Komponen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_komponen" id="nama_komponen" placeholder="Contoh: Tunjangan Transport" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Kode -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode" id="kode" placeholder="Contoh: TRANSPORT" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 uppercase" required>
                    <p class="text-xs text-gray-500 mt-1">Kode unik untuk komponen ini</p>
                </div>

                <!-- Jenis & Kategori -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" id="jenis" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            <option value="Tunjangan">Tunjangan</option>
                            <option value="Potongan">Potongan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori" id="kategori" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            <option value="Fixed">Fixed</option>
                            <option value="Variable">Variable</option>
                        </select>
                    </div>
                </div>

                <!-- Project Scope -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-3">
                        <i class="fas fa-building mr-2"></i>Cakupan Project
                    </h4>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="project_scope" value="global" checked 
                                   onchange="toggleProjectSelect()" class="text-blue-600">
                            <div>
                                <span class="text-sm font-semibold text-blue-900">Semua Project (Global)</span>
                                <p class="text-xs text-blue-700">Komponen ini berlaku untuk semua project di perusahaan</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="project_scope" value="specific" 
                                   onchange="toggleProjectSelect()" class="text-blue-600">
                            <div>
                                <span class="text-sm font-semibold text-blue-900">Project Spesifik</span>
                                <p class="text-xs text-blue-700">Komponen ini hanya berlaku untuk project tertentu</p>
                            </div>
                        </label>
                    </div>
                    <select name="project_id" id="project_select" disabled 
                            class="mt-3 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipe Perhitungan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipe Perhitungan <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_perhitungan" id="tipe_perhitungan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="updateNilaiLabel()">
                        <option value="Tetap">💰 Tetap - Nilai tetap setiap bulan</option>
                        <option value="Persentase">📊 Persentase - Persentase dari gaji pokok</option>
                        <option value="Per Hari Masuk">📅 Per Hari Masuk - Dikalikan jumlah hari masuk kerja</option>
                        <option value="Lembur Per Hari">⏰ Lembur Per Hari - Dikalikan jumlah hari lembur</option>
                    </select>
                    <p class="text-xs text-blue-600 mt-2" id="tipePerhitunganInfo">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span id="contohText">Contoh: Tunjangan jabatan Rp 1.000.000 setiap bulan</span>
                    </p>
                </div>

                <!-- Jumlah Tetap -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span id="nilaiLabel">Jumlah Tetap (Rp)</span> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="nilai" id="nilai" placeholder="1000000" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Nilai Maksimal (hanya untuk Per Hari) -->
                <div id="nilai_maksimal_section" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nilai Maksimal Per Bulan (Rp)
                    </label>
                    <input type="number" name="nilai_maksimal" id="nilai_maksimal" placeholder="300000" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-blue-600 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="maxValueExample">Contoh: Uang makan Rp 10.000/hari, maksimal Rp 300.000/bulan (30 hari)</span>
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Deskripsi komponen..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Checkboxes -->
                <div class="space-y-3 bg-gray-50 rounded-lg p-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="kena_pajak" id="kena_pajak" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Kena Pajak</span>
                            <p class="text-xs text-gray-500">Komponen ini akan dikenakan pajak PPh 21</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="boleh_edit" id="boleh_edit" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Boleh di Edit</span>
                            <p class="text-xs text-gray-500">"Boleh di Edit" memastikan apakah nilai komponen ini bisa diubah saat payroll dijalankan</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="aktif" id="aktif" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Aktif</span>
                            <p class="text-xs text-gray-500">Komponen aktif akan otomatis muncul saat generate payroll</p>
                        </div>
                    </label>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3 rounded-b-lg">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-white rounded-lg font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Data komponen untuk edit
const komponenData = @json($komponens);

function openModal(mode, id = null) {
    const modal = document.getElementById('modalForm');
    const form = document.getElementById('komponenForm');
    const title = document.getElementById('modalTitle');
    const methodInput = document.getElementById('formMethod');
    
    if (mode === 'create') {
        title.textContent = 'Tambah Komponen Baru';
        form.action = '{{ route("perusahaan.komponen-payroll.store") }}';
        methodInput.value = '';
        form.reset();
        document.getElementById('boleh_edit').checked = true;
        document.getElementById('aktif').checked = true;
        
        // Reset project scope
        document.querySelector('input[name="project_scope"][value="global"]').checked = true;
        toggleProjectSelect();
    } else {
        const komponen = komponenData.find(k => k.id === id);
        if (komponen) {
            title.textContent = 'Edit Komponen';
            form.action = `/perusahaan/komponen-payroll/${komponen.hash_id}`;
            methodInput.value = 'PUT';
            
            document.getElementById('nama_komponen').value = komponen.nama_komponen;
            document.getElementById('kode').value = komponen.kode;
            document.getElementById('jenis').value = komponen.jenis;
            document.getElementById('kategori').value = komponen.kategori;
            document.getElementById('tipe_perhitungan').value = komponen.tipe_perhitungan;
            document.getElementById('nilai').value = komponen.nilai;
            document.getElementById('nilai_maksimal').value = komponen.nilai_maksimal || '';
            document.getElementById('deskripsi').value = komponen.deskripsi || '';
            document.getElementById('kena_pajak').checked = komponen.kena_pajak;
            document.getElementById('boleh_edit').checked = komponen.boleh_edit;
            document.getElementById('aktif').checked = komponen.aktif;
            
            // Set project scope
            if (komponen.project_id) {
                document.querySelector('input[name="project_scope"][value="specific"]').checked = true;
                document.getElementById('project_select').value = komponen.project_id;
            } else {
                document.querySelector('input[name="project_scope"][value="global"]').checked = true;
            }
            toggleProjectSelect();
            
            updateNilaiLabel();
        }
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function toggleProjectSelect() {
    const projectScope = document.querySelector('input[name="project_scope"]:checked').value;
    const projectSelect = document.getElementById('project_select');
    
    if (projectScope === 'specific') {
        projectSelect.disabled = false;
        projectSelect.classList.remove('disabled:bg-gray-100');
        projectSelect.required = true;
    } else {
        projectSelect.disabled = true;
        projectSelect.classList.add('disabled:bg-gray-100');
        projectSelect.value = '';
        projectSelect.required = false;
    }
}

function closeModal() {
    const modal = document.getElementById('modalForm');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updateNilaiLabel() {
    const tipe = document.getElementById('tipe_perhitungan').value;
    const label = document.getElementById('nilaiLabel');
    const contohText = document.getElementById('contohText');
    const maxSection = document.getElementById('nilai_maksimal_section');
    const maxExample = document.getElementById('maxValueExample');
    
    if (tipe === 'Tetap') {
        label.textContent = 'Jumlah Tetap (Rp)';
        contohText.textContent = 'Contoh: Tunjangan jabatan Rp 1.000.000 setiap bulan';
        maxSection.classList.add('hidden');
    } else if (tipe === 'Persentase') {
        label.textContent = 'Persentase (%)';
        contohText.textContent = 'Contoh: Tunjangan performa 10% × Gaji pokok Rp 5.000.000 = Rp 500.000';
        maxSection.classList.add('hidden');
    } else if (tipe === 'Per Hari Masuk') {
        label.textContent = 'Nilai Per Hari (Rp)';
        contohText.textContent = 'Contoh: Uang makan Rp 10.000/hari × 22 hari masuk = Rp 220.000';
        maxSection.classList.remove('hidden');
        maxExample.textContent = 'Contoh: Uang makan Rp 10.000/hari, maksimal Rp 300.000/bulan (30 hari)';
    } else if (tipe === 'Lembur Per Hari') {
        label.textContent = 'Nilai Per Hari Lembur (Rp)';
        contohText.textContent = 'Contoh: Uang makan lembur Rp 15.000/hari × 5 hari lembur = Rp 75.000';
        maxSection.classList.remove('hidden');
        maxExample.textContent = 'Contoh: Uang makan lembur Rp 15.000/hari, maksimal Rp 450.000/bulan (30 hari)';
    }
}

function filterJenis(jenis) {
    // Update tab active state
    document.querySelectorAll('.tab-filter').forEach(tab => {
        tab.classList.remove('border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeTab = document.getElementById('tab-' + jenis.toLowerCase());
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-blue-500', 'text-blue-600');
    
    // Store current jenis filter
    window.currentJenisFilter = jenis;
    
    // Apply all filters
    applyFilters();
}

function searchKomponen() {
    applyFilters();
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const kategoriFilter = document.getElementById('kategoriFilter').value;
    const jenisFilter = window.currentJenisFilter || 'Semua';
    
    let visibleCount = 0;
    let jenisCount = { 'Semua': 0, 'Tunjangan': 0, 'Potongan': 0 };
    let totalRows = 0;
    
    document.querySelectorAll('.komponen-row').forEach(row => {
        totalRows++;
        const nama = row.dataset.nama || '';
        const kode = row.dataset.kode || '';
        const deskripsi = row.dataset.deskripsi || '';
        const jenis = row.dataset.jenis || '';
        const kategori = row.dataset.kategori || '';
        const status = row.dataset.status || '';
        
        // Check search term
        const matchesSearch = !searchTerm || 
            nama.includes(searchTerm) || 
            kode.includes(searchTerm) || 
            deskripsi.includes(searchTerm);
        
        // Check jenis filter
        const matchesJenis = jenisFilter === 'Semua' || jenis === jenisFilter;
        
        // Check status filter
        const matchesStatus = !statusFilter || status === statusFilter;
        
        // Check kategori filter
        const matchesKategori = !kategoriFilter || kategori === kategoriFilter;
        
        // Show/hide row
        if (matchesSearch && matchesJenis && matchesStatus && matchesKategori) {
            row.style.display = '';
            visibleCount++;
            jenisCount['Semua']++;
            jenisCount[jenis]++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Handle empty states
    const emptyState = document.getElementById('emptyState');
    const noSearchResults = document.getElementById('noSearchResults');
    
    if (totalRows === 0) {
        // No data at all
        if (emptyState) emptyState.style.display = '';
        if (noSearchResults) noSearchResults.style.display = 'none';
    } else if (visibleCount === 0) {
        // No search results
        if (emptyState) emptyState.style.display = 'none';
        if (noSearchResults) noSearchResults.style.display = '';
    } else {
        // Has results
        if (emptyState) emptyState.style.display = 'none';
        if (noSearchResults) noSearchResults.style.display = 'none';
    }
    
    // Update counts in tabs
    document.getElementById('count-semua').textContent = jenisCount['Semua'];
    document.getElementById('count-tunjangan').textContent = jenisCount['Tunjangan'];
    document.getElementById('count-potongan').textContent = jenisCount['Potongan'];
    
    // Show search info
    updateSearchInfo(visibleCount, searchTerm, statusFilter, kategoriFilter);
}

function updateSearchInfo(visibleCount, searchTerm, statusFilter, kategoriFilter) {
    const searchInfo = document.getElementById('searchInfo');
    const searchResultText = document.getElementById('searchResultText');
    
    if (searchTerm || statusFilter || kategoriFilter) {
        let filterText = [];
        if (searchTerm) filterText.push(`"${searchTerm}"`);
        if (statusFilter) filterText.push(`Status: ${statusFilter}`);
        if (kategoriFilter) filterText.push(`Kategori: ${kategoriFilter}`);
        
        searchResultText.textContent = `Menampilkan ${visibleCount} komponen dari pencarian: ${filterText.join(', ')}`;
        searchInfo.classList.remove('hidden');
    } else {
        searchInfo.classList.add('hidden');
    }
}

function clearFilters() {
    // Redirect to clean URL without parameters
    window.location.href = '{{ route("perusahaan.komponen-payroll.index") }}';
}

// Auto-submit form when filters change
function autoSubmitForm() {
    const form = document.querySelector('form[method="GET"]');
    if (form) {
        form.submit();
    }
}

// Update filter functions to work with server-side
function applyFiltersServerSide() {
    autoSubmitForm();
}

// Initialize filters
window.currentJenisFilter = 'Semua';

function deleteKomponen(hashId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Komponen ini akan dihapus permanen!",
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
            form.action = `/perusahaan/komponen-payroll/${hashId}`;
            
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
            form.submit();
        }
    });
}

// Success/Error messages
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: false
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '{{ session('error') }}',
    confirmButtonText: 'OK'
});
@endif

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Auto uppercase kode
document.getElementById('kode').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Real-time search with debounce (client-side for better UX)
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Use client-side search for immediate feedback
        applyFilters();
    }, 300);
});

// Server-side search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        autoSubmitForm();
    }
});

// Auto-submit when filters change
document.getElementById('statusFilter').addEventListener('change', function() {
    // Use both client-side for immediate feedback and server-side for persistence
    applyFilters();
    setTimeout(() => {
        autoSubmitForm();
    }, 100);
});

document.getElementById('kategoriFilter').addEventListener('change', function() {
    // Use both client-side for immediate feedback and server-side for persistence
    applyFilters();
    setTimeout(() => {
        autoSubmitForm();
    }, 100);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // ESC to close modal
    if (e.key === 'Escape') {
        closeModal();
    }
    
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    // Ctrl/Cmd + N to add new komponen
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        openModal('create');
    }
});

// Focus search on page load if no komponen exists
document.addEventListener('DOMContentLoaded', function() {
    const komponenRows = document.querySelectorAll('.komponen-row');
    if (komponenRows.length === 0) {
        // No komponen exists, focus on add button instead
        return;
    }
    
    // Add search hint
    const searchInput = document.getElementById('searchInput');
    searchInput.setAttribute('title', 'Tekan Ctrl+K untuk fokus pencarian');
});
</script>
@endpush

@endsection
