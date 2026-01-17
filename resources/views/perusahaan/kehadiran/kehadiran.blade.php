@extends('perusahaan.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        
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

        @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    html: '{!! session('warning') !!}',
                    confirmButtonText: 'OK'
                });
            });
        </script>
        @endif
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        Kehadiran Karyawan
                    </h1>
                    <p class="text-gray-600 mt-1">Kelola dan monitor kehadiran karyawan</p>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="openImportExcelModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-file-excel mr-2"></i>Import Excel
                    </button>
                    <a href="{{ route('perusahaan.kehadiran.rekap-kehadiran') }}" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                        <i class="fas fa-chart-bar mr-2"></i>Rekap
                    </a>
                    <button onclick="openTambahModal()" class="px-6 py-3 rounded-xl font-medium transition inline-flex items-center justify-center shadow-lg text-white" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-plus mr-2"></i>Tambah Kehadiran
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" class="space-y-4">
                <!-- Warning: Project Required -->
                @if(!$projectId)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-yellow-900 mb-1">Project Wajib Dipilih</p>
                            <p class="text-xs text-yellow-700">Untuk performa optimal, silakan pilih project terlebih dahulu sebelum menampilkan data kehadiran.</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Project -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-project-diagram mr-1" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                        </label>
                        <select name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Karyawan Search -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-1" style="color: #3B82C8;"></i>Karyawan
                        </label>
                        <input type="text" name="karyawan_search" value="{{ $karyawanSearch }}" placeholder="Nama atau NIK..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1" style="color: #3B82C8;"></i>Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Tanggal Akhir -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1" style="color: #3B82C8;"></i>Tanggal Akhir
                        </label>
                        <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir->format('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-filter mr-1" style="color: #3B82C8;"></i>Status
                        </label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ $statusFilter == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ $statusFilter == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="pulang_cepat" {{ $statusFilter == 'pulang_cepat' ? 'selected' : '' }}>Pulang Cepat</option>
                            <option value="terlambat_pulang_cepat" {{ $statusFilter == 'terlambat_pulang_cepat' ? 'selected' : '' }}>Terlambat & Pulang Cepat</option>
                            <option value="alpa" {{ $statusFilter == 'alpa' ? 'selected' : '' }}>Alpa</option>
                            <option value="izin" {{ $statusFilter == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ $statusFilter == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="cuti" {{ $statusFilter == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-6 py-2.5 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <a href="{{ route('perusahaan.kehadiran.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-6">
            <!-- Hadir -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-green-700">{{ $summary['hadir'] }}</span>
                </div>
                <p class="text-sm font-semibold text-green-700">Hadir</p>
            </div>
            
            <!-- Terlambat -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-orange-700">{{ $summary['terlambat'] }}</span>
                </div>
                <p class="text-sm font-semibold text-orange-700">Terlambat</p>
            </div>
            
            <!-- Pulang Cepat -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-running text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-yellow-700">{{ $summary['pulang_cepat'] }}</span>
                </div>
                <p class="text-sm font-semibold text-yellow-700">Pulang Cepat</p>
            </div>
            
            <!-- Terlambat & Pulang Cepat -->
            <div class="bg-gradient-to-br from-rose-50 to-rose-100 border border-rose-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-rose-700">{{ $summary['terlambat_pulang_cepat'] ?? 0 }}</span>
                </div>
                <p class="text-sm font-semibold text-rose-700">Terlambat & Pulang Cepat</p>
            </div>
            
            <!-- Alpa -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-times text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-red-700">{{ $summary['alpa'] }}</span>
                </div>
                <p class="text-sm font-semibold text-red-700">Alpa</p>
            </div>
            
            <!-- On Radius -->
            <div class="bg-gradient-to-br from-teal-50 to-teal-100 border border-teal-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-teal-700">{{ $summary['on_radius'] }}</span>
                </div>
                <p class="text-sm font-semibold text-teal-700">On Radius</p>
            </div>
            
            <!-- Off Radius -->
            <div class="bg-gradient-to-br from-pink-50 to-pink-100 border border-pink-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-pink-700">{{ $summary['off_radius'] }}</span>
                </div>
                <p class="text-sm font-semibold text-pink-700">Off Radius</p>
            </div>
            
            <!-- Tingkat Kehadiran -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <span class="text-3xl font-bold text-blue-700">{{ $tingkatKehadiran }}%</span>
                </div>
                <p class="text-sm font-semibold text-blue-700">Tingkat Kehadiran</p>
            </div>
        </div>

        <!-- Table Card -->
        @if(!$projectId)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-filter text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Pilih Project untuk Melihat Data</h3>
            <p class="text-gray-500">Silakan pilih project dari filter di atas untuk menampilkan data kehadiran</p>
        </div>
        @elseif($kehadirans->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data Kehadiran</h3>
            <p class="text-gray-500">Belum ada data kehadiran untuk periode yang dipilih</p>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Info Bar -->
            <div class="px-6 py-3 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-700 font-medium">
                        <i class="fas fa-database mr-2"></i>
                        Total: <strong>{{ $kehadirans->total() }}</strong> data
                    </span>
                    <span class="text-blue-600">
                        <i class="fas fa-file-alt mr-2"></i>
                        Halaman: <strong>{{ $kehadirans->currentPage() }}</strong> dari <strong>{{ $kehadirans->lastPage() }}</strong>
                    </span>
                    <span class="text-blue-600">
                        <i class="fas fa-list mr-2"></i>
                        Menampilkan: <strong>{{ $kehadirans->firstItem() }}</strong> - <strong>{{ $kehadirans->lastItem() }}</strong>
                    </span>
                </div>
                <span class="text-xs text-blue-600">
                    <i class="fas fa-clock mr-1"></i>
                    50 data per halaman
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Karyawan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Project</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Shift</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Jam Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Jam Keluar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Lokasi</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($kehadirans as $index => $kehadiran)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $kehadirans->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $kehadiran->tanggal->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $kehadiran->tanggal->format('l') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($kehadiran->karyawan->foto)
                                    <img 
                                        src="{{ asset('storage/' . $kehadiran->karyawan->foto) }}" 
                                        alt="{{ $kehadiran->karyawan->nama_lengkap }}"
                                        loading="lazy"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-blue-200"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%233B82F6%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2240%22 fill=%22white%22%3E{{ substr($kehadiran->karyawan->nama_lengkap, 0, 1) }}%3C/text%3E%3C/svg%3E';"
                                    >
                                    @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($kehadiran->karyawan->nama_lengkap, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $kehadiran->karyawan->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">{{ $kehadiran->karyawan->nik_karyawan }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $kehadiran->project->nama }}</td>
                            <td class="px-6 py-4">
                                @if($kehadiran->shift)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium text-white" style="background-color: {{ $kehadiran->shift->warna }};">
                                    {{ $kehadiran->shift->kode_shift }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $kehadiran->jam_masuk ? \Carbon\Carbon::parse($kehadiran->jam_masuk)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $kehadiran->jam_keluar ? \Carbon\Carbon::parse($kehadiran->jam_keluar)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check', 'label' => 'Hadir'],
                                        'terlambat' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'fa-clock', 'label' => 'Terlambat'],
                                        'pulang_cepat' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-running', 'label' => 'Pulang Cepat'],
                                        'terlambat_pulang_cepat' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'icon' => 'fa-exclamation-triangle', 'label' => 'Terlambat & Pulang Cepat'],
                                        'alpa' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times', 'label' => 'Alpa'],
                                        'izin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-file-alt', 'label' => 'Izin'],
                                        'sakit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'fa-notes-medical', 'label' => 'Sakit'],
                                        'cuti' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'icon' => 'fa-umbrella-beach', 'label' => 'Cuti'],
                                    ];
                                    $config = $statusConfig[$kehadiran->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-question', 'label' => ucfirst($kehadiran->status)];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                    <i class="fas {{ $config['icon'] }}"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($kehadiran->on_radius)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-teal-100 text-teal-700">
                                    <i class="fas fa-check-circle"></i>
                                    On Radius
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-pink-100 text-pink-700">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Off Radius
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="showDetail('{{ $kehadiran->hash_id }}')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button onclick="editKehadiran('{{ $kehadiran->hash_id }}')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteKehadiran('{{ $kehadiran->hash_id }}', '{{ $kehadiran->karyawan->nama_lengkap }}', '{{ $kehadiran->tanggal->format('d M Y') }}')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($kehadirans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $kehadirans->links() }}
            </div>
            @endif
        </div>
        @endif

    </div>
</div>

<!-- Modal: Import Excel -->
<div id="modalImportExcel" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex-shrink-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Import Kehadiran dari Excel</h3>
                        <p class="text-purple-100 text-sm">Upload file Excel (.xlsx)</p>
                    </div>
                </div>
                <button onclick="closeImportExcelModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formImportExcel" action="{{ route('perusahaan.kehadiran.import-excel') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-4">
            @csrf
            
            <!-- Project -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-project-diagram mr-2" style="color: #667eea;"></i>Project <span class="text-red-500">*</span>
                </label>
                <select id="importProjectId" name="project_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <option value="">Pilih Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>{{ $project->nama }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih project yang sesuai dengan template Excel</p>
            </div>
            
            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2" style="color: #667eea;"></i>Tanggal Mulai <span class="text-red-500">*</span>
                </label>
                <input type="date" id="importTanggalMulai" name="tanggal_mulai" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Tanggal awal periode kehadiran</p>
            </div>
            
            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2" style="color: #667eea;"></i>Tanggal Akhir <span class="text-red-500">*</span>
                </label>
                <input type="date" id="importTanggalAkhir" name="tanggal_akhir" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Tanggal akhir periode (max 31 hari)</p>
            </div>
            
            <!-- Info: Skip Existing Data -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-900 mb-1">Data dari Aplikasi Tidak Akan Di-Replace</p>
                        <p class="text-xs text-yellow-700">Jika karyawan sudah absen dari aplikasi mobile, data dari Excel akan di-skip (tidak di-replace).</p>
                    </div>
                </div>
            </div>
            
            <!-- Download Template -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-download text-blue-600 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Belum punya template?</p>
                        <p class="text-xs text-blue-700 mb-3">Download template Excel untuk periode yang dipilih (format horizontal dengan kolom tanggal), isi data kehadiran, lalu upload kembali.</p>
                        <button type="button" onclick="downloadTemplate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </button>
                        <p id="templateInfo" class="text-xs text-blue-600 mt-2 hidden"></p>
                    </div>
                </div>
            </div>
            
            <!-- File Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-upload mr-2" style="color: #667eea;"></i>Upload File Excel <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-500 transition">
                    <input type="file" name="file" id="excelFile" accept=".xlsx,.xls" required class="hidden" onchange="updateFileName(this)">
                    <label for="excelFile" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Klik untuk pilih file</p>
                        <p class="text-xs text-gray-500 mt-1">atau drag & drop file di sini</p>
                        <p class="text-xs text-gray-400 mt-2">Format: .xlsx atau .xls (Max: 2MB)</p>
                    </label>
                </div>
                <p id="fileName" class="text-sm text-purple-600 font-medium mt-2 hidden"></p>
            </div>
            </div>
        </form>
        
        <div class="p-6 border-t border-gray-100 flex-shrink-0 bg-gray-50">
            <div class="flex gap-3">
                <button type="submit" form="formImportExcel" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-upload mr-2"></i>Import
                </button>
                <button type="button" onclick="closeImportExcelModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Kehadiran -->
<div id="modalTambahKehadiran" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex-shrink-0" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Tambah Kehadiran Manual</h3>
                        <p class="text-blue-100 text-sm">Input kehadiran untuk 1 karyawan</p>
                    </div>
                </div>
                <button onclick="closeTambahModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formTambahKehadiran" action="{{ route('perusahaan.kehadiran.store') }}" method="POST" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Project -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-project-diagram mr-2" style="color: #3B82C8;"></i>Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="tambahProjectId" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" onchange="loadKaryawanByProject(this.value)">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->nama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2" style="color: #3B82C8;"></i>Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Karyawan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2" style="color: #3B82C8;"></i>Karyawan <span class="text-red-500">*</span>
                </label>
                <select name="karyawan_id" id="tambahKaryawanId" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih project terlebih dahulu</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Jam Masuk -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2" style="color: #3B82C8;"></i>Jam Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="jam_masuk" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Jam Keluar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2" style="color: #3B82C8;"></i>Jam Keluar
                    </label>
                    <input type="time" name="jam_keluar" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2" style="color: #3B82C8;"></i>Status <span class="text-red-500">*</span>
                </label>
                <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="pulang_cepat">Pulang Cepat</option>
                    <option value="terlambat_pulang_cepat">Terlambat & Pulang Cepat</option>
                    <option value="alpa">Alpa</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="cuti">Cuti</option>
                </select>
            </div>
            
            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment mr-2" style="color: #3B82C8;"></i>Keterangan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Input Manual - Tambahkan keterangan jika diperlukan..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">Input Manual</textarea>
            </div>
            
            </div>
        </form>
        
        <div class="p-6 border-t border-gray-100 flex-shrink-0 bg-gray-50">
            <div class="flex gap-3">
                <button type="submit" form="formTambahKehadiran" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="closeTambahModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Kehadiran -->
<div id="modalEditKehadiran" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex-shrink-0" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Edit Kehadiran</h3>
                        <p class="text-orange-100 text-sm">Ubah data kehadiran karyawan</p>
                    </div>
                </div>
                <button onclick="closeEditModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formEditKehadiran" method="POST" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Karyawan Info (Read Only) -->
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div id="editKaryawanAvatar" class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        K
                    </div>
                    <div class="flex-1">
                        <h3 id="editKaryawanNama" class="font-bold text-gray-900">-</h3>
                        <p id="editKaryawanInfo" class="text-sm text-gray-600">-</p>
                    </div>
                    <div>
                        <p id="editTanggal" class="text-sm font-semibold text-blue-700">-</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Jam Masuk -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2" style="color: #F59E0B;"></i>Jam Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="jam_masuk" id="editJamMasuk" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                </div>
                
                <!-- Jam Keluar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2" style="color: #F59E0B;"></i>Jam Keluar
                    </label>
                    <input type="time" name="jam_keluar" id="editJamKeluar" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Jam Istirahat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-coffee mr-2" style="color: #F59E0B;"></i>Jam Istirahat
                    </label>
                    <input type="time" name="jam_istirahat" id="editJamIstirahat" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                </div>
                
                <!-- Jam Kembali -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-undo mr-2" style="color: #F59E0B;"></i>Jam Kembali
                    </label>
                    <input type="time" name="jam_kembali" id="editJamKembali" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            
            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2" style="color: #F59E0B;"></i>Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="editStatus" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="pulang_cepat">Pulang Cepat</option>
                    <option value="terlambat_pulang_cepat">Terlambat & Pulang Cepat</option>
                    <option value="alpa">Alpa</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="cuti">Cuti</option>
                </select>
            </div>
            
            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment mr-2" style="color: #F59E0B;"></i>Keterangan
                </label>
                <textarea name="keterangan" id="editKeterangan" rows="3" placeholder="Tambahkan keterangan jika diperlukan..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            
            </div>
        </form>
        
        <div class="p-6 border-t border-gray-100 flex-shrink-0 bg-gray-50">
            <div class="flex gap-3">
                <button type="submit" form="formEditKehadiran" class="flex-1 px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
                <button type="button" onclick="closeEditModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form submit handler for debugging
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formImportExcel');
    if (form) {
        form.addEventListener('submit', function(e) {
            const projectId = document.getElementById('importProjectId').value;
            const tanggalMulai = document.getElementById('importTanggalMulai').value;
            const tanggalAkhir = document.getElementById('importTanggalAkhir').value;
            const fileInput = document.getElementById('excelFile');
            
            if (!projectId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Pilih project terlebih dahulu',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (!tanggalMulai || !tanggalAkhir) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Pilih tanggal mulai dan akhir terlebih dahulu',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Pilih file Excel terlebih dahulu',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            // Show loading
            Swal.fire({
                title: 'Mengimport...',
                text: 'Mohon tunggu, sedang memproses file Excel',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});

function openImportExcelModal() {
    document.getElementById('modalImportExcel').classList.remove('hidden');
}

function closeImportExcelModal() {
    document.getElementById('modalImportExcel').classList.add('hidden');
    document.getElementById('formImportExcel').reset();
    document.getElementById('fileName').classList.add('hidden');
    document.getElementById('templateInfo').classList.add('hidden');
}

function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = '✓ File dipilih: ' + input.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
}

function downloadTemplate() {
    const projectId = document.getElementById('importProjectId').value;
    const tanggalMulai = document.getElementById('importTanggalMulai').value;
    const tanggalAkhir = document.getElementById('importTanggalAkhir').value;
    
    if (!projectId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih project terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (!tanggalMulai || !tanggalAkhir) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih tanggal mulai dan akhir terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Validate date range
    const startDate = new Date(tanggalMulai);
    const endDate = new Date(tanggalAkhir);
    
    if (endDate < startDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Tanggal akhir harus sama atau setelah tanggal mulai',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Check max 31 days
    const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    if (daysDiff > 31) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Periode maksimal 31 hari',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show info
    const templateInfo = document.getElementById('templateInfo');
    templateInfo.textContent = '✓ Template akan didownload untuk periode ' + tanggalMulai + ' s/d ' + tanggalAkhir + ' (' + daysDiff + ' hari)';
    templateInfo.classList.remove('hidden');
    
    window.location.href = '{{ route("perusahaan.kehadiran.download-template") }}?project_id=' + projectId + '&tanggal_mulai=' + tanggalMulai + '&tanggal_akhir=' + tanggalAkhir;
}

// Close modal when clicking outside
document.getElementById('modalImportExcel').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportExcelModal();
    }
});

// Show detail kehadiran
async function showDetail(hashId) {
    try {
        const response = await fetch(`{{ url('perusahaan/kehadiran') }}/${hashId}/show`);
        const data = await response.json();
        
        // Build modal content
        const sumberBadge = data.sumber_data === 'excel' 
            ? '<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium"><i class="fas fa-file-excel mr-1"></i>Import Excel</span>'
            : '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium"><i class="fas fa-mobile-alt mr-1"></i>Mobile App</span>';
        
        const statusConfig = {
            'hadir': { bg: 'bg-green-100', text: 'text-green-700', icon: 'fa-check', label: 'Hadir' },
            'terlambat': { bg: 'bg-orange-100', text: 'text-orange-700', icon: 'fa-clock', label: 'Terlambat' },
            'pulang_cepat': { bg: 'bg-yellow-100', text: 'text-yellow-700', icon: 'fa-running', label: 'Pulang Cepat' },
            'terlambat_pulang_cepat': { bg: 'bg-rose-100', text: 'text-rose-700', icon: 'fa-exclamation-triangle', label: 'Terlambat & Pulang Cepat' },
            'alpa': { bg: 'bg-red-100', text: 'text-red-700', icon: 'fa-times', label: 'Alpa' },
            'izin': { bg: 'bg-blue-100', text: 'text-blue-700', icon: 'fa-file-alt', label: 'Izin' },
            'sakit': { bg: 'bg-purple-100', text: 'text-purple-700', icon: 'fa-notes-medical', label: 'Sakit' },
            'cuti': { bg: 'bg-indigo-100', text: 'text-indigo-700', icon: 'fa-umbrella-beach', label: 'Cuti' },
        };
        const config = statusConfig[data.status] || { bg: 'bg-gray-100', text: 'text-gray-700', icon: 'fa-question', label: data.status };
        
        const fotoMasuk = data.foto_masuk 
            ? `<img src="/storage/${data.foto_masuk}" alt="Foto Masuk" loading="lazy" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">`
            : '<div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400 text-4xl"></i></div>';
        
        const fotoKeluar = data.foto_keluar 
            ? `<img src="/storage/${data.foto_keluar}" alt="Foto Keluar" loading="lazy" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">`
            : '<div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400 text-4xl"></i></div>';
        
        Swal.fire({
            title: 'Detail Kehadiran',
            html: `
                <div class="text-left space-y-4">
                    <div class="bg-blue-50 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                ${data.karyawan.nama_lengkap.charAt(0)}
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">${data.karyawan.nama_lengkap}</h3>
                                <p class="text-sm text-gray-600">${data.karyawan.nik_karyawan} • ${data.karyawan.jabatan?.nama || '-'}</p>
                            </div>
                            ${sumberBadge}
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600">Tanggal</p>
                                <p class="font-semibold">${new Date(data.tanggal).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Status</p>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium ${config.bg} ${config.text}">
                                    <i class="fas ${config.icon}"></i> ${config.label}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-xl p-4">
                            <p class="text-xs text-green-700 font-semibold mb-2"><i class="fas fa-sign-in-alt mr-1"></i>MASUK</p>
                            <p class="text-2xl font-bold text-green-700 mb-1">${data.jam_masuk || '-'}</p>
                            <p class="text-xs text-green-600">${data.lokasi_masuk ? 'Lokasi: ' + data.lokasi_masuk : 'Tidak ada lokasi'}</p>
                            ${data.jarak_masuk !== null && data.jarak_masuk !== undefined ? `<p class="text-xs text-green-600"><i class="fas fa-ruler mr-1"></i>Jarak: ${data.jarak_masuk < 1000 ? Math.round(data.jarak_masuk) + ' meter' : (data.jarak_masuk / 1000).toFixed(1) + ' km'}</p>` : ''}
                            ${data.map_absen_masuk ? `<a href="${data.map_absen_masuk}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 mt-1"><i class="fas fa-map-marker-alt"></i> Lihat di Google Maps</a>` : ''}
                            <div class="mt-3">${fotoMasuk}</div>
                        </div>
                        <div class="bg-red-50 rounded-xl p-4">
                            <p class="text-xs text-red-700 font-semibold mb-2"><i class="fas fa-sign-out-alt mr-1"></i>KELUAR</p>
                            <p class="text-2xl font-bold text-red-700 mb-1">${data.jam_keluar || '-'}</p>
                            <p class="text-xs text-red-600">${data.lokasi_keluar ? 'Lokasi: ' + data.lokasi_keluar : 'Tidak ada lokasi'}</p>
                            ${data.jarak_keluar !== null && data.jarak_keluar !== undefined ? `<p class="text-xs text-red-600"><i class="fas fa-ruler mr-1"></i>Jarak: ${data.jarak_keluar < 1000 ? Math.round(data.jarak_keluar) + ' meter' : (data.jarak_keluar / 1000).toFixed(1) + ' km'}</p>` : ''}
                            ${data.map_absen_keluar ? `<a href="${data.map_absen_keluar}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 mt-1"><i class="fas fa-map-marker-alt"></i> Lihat di Google Maps</a>` : ''}
                            <div class="mt-3">${fotoKeluar}</div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Shift</p>
                            <p class="font-semibold">${data.shift ? data.shift.nama_shift : '-'}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Durasi Kerja</p>
                            <p class="font-semibold">${data.durasi_kerja ? Math.floor(data.durasi_kerja / 60) + ' jam ' + (data.durasi_kerja % 60) + ' menit' : '-'}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status Radius</p>
                            <div class="space-y-1">
                                ${data.on_radius_masuk !== null && data.on_radius_masuk !== undefined ? `<p class="text-xs ${data.on_radius_masuk ? 'text-green-600' : 'text-red-600'}">${data.on_radius_masuk ? '✓ Masuk: On Radius' : '✗ Masuk: Off Radius'}</p>` : ''}
                                ${data.on_radius_keluar !== null && data.on_radius_keluar !== undefined ? `<p class="text-xs ${data.on_radius_keluar ? 'text-green-600' : 'text-red-600'}">${data.on_radius_keluar ? '✓ Keluar: On Radius' : '✗ Keluar: Off Radius'}</p>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `,
            width: '800px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'text-left'
            }
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal memuat detail kehadiran',
            confirmButtonText: 'OK'
        });
    }
}

// Tambah Kehadiran Modal
function openTambahModal() {
    document.getElementById('modalTambahKehadiran').classList.remove('hidden');
}

function closeTambahModal() {
    document.getElementById('modalTambahKehadiran').classList.add('hidden');
    document.getElementById('formTambahKehadiran').reset();
    document.getElementById('tambahKaryawanId').innerHTML = '<option value="">Pilih project terlebih dahulu</option>';
}

// Load karyawan by project
async function loadKaryawanByProject(projectId) {
    const select = document.getElementById('tambahKaryawanId');
    
    if (!projectId) {
        select.innerHTML = '<option value="">Pilih project terlebih dahulu</option>';
        return;
    }
    
    select.innerHTML = '<option value="">Loading...</option>';
    
    try {
        const response = await fetch(`{{ url('perusahaan/karyawan/by-project') }}/${projectId}`);
        const karyawans = await response.json();
        
        if (karyawans.length === 0) {
            select.innerHTML = '<option value="">Tidak ada karyawan aktif</option>';
            return;
        }
        
        select.innerHTML = '<option value="">Pilih Karyawan</option>';
        karyawans.forEach(k => {
            const option = document.createElement('option');
            option.value = k.id;
            option.textContent = `${k.nama_lengkap} (${k.nik_karyawan})`;
            select.appendChild(option);
        });
    } catch (error) {
        select.innerHTML = '<option value="">Error loading karyawan</option>';
    }
}

// Close modal when clicking outside
document.getElementById('modalTambahKehadiran').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTambahModal();
    }
});

// Edit Kehadiran Modal
function openEditModal() {
    document.getElementById('modalEditKehadiran').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('modalEditKehadiran').classList.add('hidden');
    document.getElementById('formEditKehadiran').reset();
}

// Edit kehadiran function
async function editKehadiran(hashId) {
    try {
        // Fetch kehadiran data
        const response = await fetch(`{{ url('perusahaan/kehadiran') }}/${hashId}/show`);
        const data = await response.json();
        
        // Populate form
        document.getElementById('editKaryawanAvatar').textContent = data.karyawan.nama_lengkap.charAt(0);
        document.getElementById('editKaryawanNama').textContent = data.karyawan.nama_lengkap;
        document.getElementById('editKaryawanInfo').textContent = `${data.karyawan.nik_karyawan} • ${data.karyawan.jabatan?.nama || '-'}`;
        document.getElementById('editTanggal').textContent = new Date(data.tanggal).toLocaleDateString('id-ID', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        // Set form values
        document.getElementById('editJamMasuk').value = data.jam_masuk ? data.jam_masuk.substring(0, 5) : '';
        document.getElementById('editJamKeluar').value = data.jam_keluar ? data.jam_keluar.substring(0, 5) : '';
        document.getElementById('editJamIstirahat').value = data.jam_istirahat ? data.jam_istirahat.substring(0, 5) : '';
        document.getElementById('editJamKembali').value = data.jam_kembali ? data.jam_kembali.substring(0, 5) : '';
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editKeterangan').value = data.keterangan || '';
        
        // Set form action
        document.getElementById('formEditKehadiran').action = `{{ url('perusahaan/kehadiran') }}/${hashId}`;
        
        // Open modal
        openEditModal();
        
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal memuat data kehadiran',
            confirmButtonText: 'OK'
        });
    }
}

// Delete kehadiran function
function deleteKehadiran(hashId, namaKaryawan, tanggal) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        html: `
            <div class="text-left">
                <p class="mb-2">Data kehadiran yang akan dihapus:</p>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="font-semibold text-red-800">${namaKaryawan}</p>
                    <p class="text-sm text-red-600">${tanggal}</p>
                </div>
                <p class="mt-3 text-sm text-gray-600">Data tidak dapat dikembalikan setelah dihapus!</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('perusahaan/kehadiran') }}/${hashId}`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method override
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Close edit modal when clicking outside
document.getElementById('modalEditKehadiran').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush

@endsection
