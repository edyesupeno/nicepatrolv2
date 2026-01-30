@extends('perusahaan.layouts.app')

@section('page-title', 'Detail Penyerahan Perlengkapan')
@section('page-subtitle', 'Lihat detail penyerahan perlengkapan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('perusahaan.penyerahan-perlengkapan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Penyerahan
    </a>
    <div class="flex items-center space-x-3">
        {{-- Debug Info --}}
        <div class="bg-yellow-100 p-2 rounded text-xs mb-4">
            Status: {{ $penyerahan->status }}<br>
            Karyawan Count: {{ $penyerahan->karyawans()->count() }}<br>
            Items Count: {{ $penyerahan->items()->count() }}
        </div>
        
        @if($penyerahan->status === 'draft')
            <div class="flex items-center space-x-3">
                <!-- Test Button - Always Show -->
                <button onclick="alert('Test button works!')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-test mr-2"></i>TEST BUTTON
                </button>
                
                @if($penyerahan->karyawans()->count() == 0)
                    <button onclick="showPilihKaryawanModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                        <i class="fas fa-users mr-2"></i>1. Pilih Karyawan
                    </button>
                @elseif($penyerahan->items()->count() == 0)
                    <button onclick="showPilihItemModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                        <i class="fas fa-box mr-2"></i>2. Pilih Item
                    </button>
                    <button onclick="showPilihKaryawanModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>Edit Karyawan
                    </button>
                @else
                    <button onclick="showSerahkanItemModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                        <i class="fas fa-handshake mr-2"></i>3. Serahkan Item
                    </button>
                    <button onclick="showPilihItemModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>Edit Item
                    </button>
                    <button onclick="updateStatusToPending('{{ $penyerahan->hash_id }}')" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                        <i class="fas fa-arrow-right mr-2"></i>Set Pending
                    </button>
                @endif
                <a href="{{ route('perusahaan.penyerahan-perlengkapan.edit', $penyerahan->hash_id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit Jadwal
                </a>
                <button onclick="deletePenyerahan('{{ $penyerahan->hash_id }}', 'jadwal penyerahan')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
        @elseif($penyerahan->status === 'pending')
            <button onclick="showSerahkanItemModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                <i class="fas fa-handshake mr-2"></i>Serahkan Item
            </button>
            <a href="{{ route('perusahaan.penyerahan-perlengkapan.edit', $penyerahan->hash_id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <button onclick="deletePenyerahan('{{ $penyerahan->hash_id }}', 'jadwal penyerahan')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                <i class="fas fa-trash mr-2"></i>Hapus
            </button>
        @elseif($penyerahan->status === 'diserahkan')
            <button onclick="showKembalikanModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center">
                <i class="fas fa-undo mr-2"></i>Kembalikan
            </button>
        @endif
    </div>
</div>

<!-- Penyerahan Info -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Informasi Penyerahan</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Karyawan</label>
                @if($penyerahan->karyawans()->count() > 0)
                    <div class="space-y-1">
                        @foreach($penyerahan->karyawans as $penyerahanKaryawan)
                            <div class="flex items-center justify-between bg-gray-50 rounded px-2 py-1">
                                <span class="text-sm font-medium text-gray-900">{{ $penyerahanKaryawan->karyawan->nama_lengkap }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $penyerahanKaryawan->status_color }}-100 text-{{ $penyerahanKaryawan->status_color }}-800">
                                    {{ $penyerahanKaryawan->status_text }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Belum dipilih</p>
                    <div class="mt-2">
                        <button onclick="showPilihKaryawanModal()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i>Pilih Karyawan
                        </button>
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Project</label>
                <p class="text-gray-900">{{ $penyerahan->project->nama }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                @if($penyerahan->status === 'pending')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i>Pending
                    </span>
                @elseif($penyerahan->status === 'diserahkan')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Diserahkan
                    </span>
                @elseif($penyerahan->status === 'dikembalikan')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-undo mr-1"></i>Dikembalikan
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                <p class="text-gray-900">{{ $penyerahan->tanggal_mulai->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                <p class="text-gray-900">{{ $penyerahan->tanggal_selesai->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                <p class="text-gray-900">{{ $penyerahan->createdBy->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Pada</label>
                <p class="text-gray-900">{{ $penyerahan->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($penyerahan->keterangan)
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
                <p class="text-gray-900">{{ $penyerahan->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Item yang Diserahkan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    @if($penyerahan->status === 'dikembalikan')
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dikembalikan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($item->item->foto_item)
                                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $item->item->foto_url }}" alt="{{ $item->item->nama_item }}">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-toolbox text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->item->nama_item }}</div>
                                <div class="text-xs text-gray-400">{{ $item->item->kategori->nama_kategori ?? 'Kategori tidak ditemukan' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $item->karyawan->nama_lengkap ?? 'Tidak ada' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $item->jumlah_diserahkan }} {{ $item->item->satuan }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->is_diserahkan)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Sudah Diserahkan
                            </span>
                            @if($item->tanggal_diserahkan)
                                <div class="text-xs text-gray-500 mt-1">{{ $item->tanggal_diserahkan->format('d/m/Y H:i') }}</div>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-clock mr-1"></i>Belum Diserahkan
                            </span>
                        @endif
                    </td>
                    @if($penyerahan->status === 'dikembalikan')
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->jumlah_dikembalikan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->kondisi_saat_dikembalikan === 'baik')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Baik
                                </span>
                            @elseif($item->kondisi_saat_dikembalikan === 'rusak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Rusak
                                </span>
                            @elseif($item->kondisi_saat_dikembalikan === 'hilang')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i>Hilang
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $item->keterangan_item ?: '-' }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pilih Karyawan Modal -->
<div id="pilihKaryawanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pilih Karyawan</h3>
                <button onclick="closePilihKaryawanModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="pilihKaryawanForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <select id="jabatan_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Jabatan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                        <div id="karyawan_list" class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3">
                            <p class="text-gray-500 text-sm">Pilih jabatan terlebih dahulu</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closePilihKaryawanModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Pilih Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pilih Item Modal -->
<div id="pilihItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pilih Item Perlengkapan</h3>
                <button onclick="closePilihItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="pilihItemForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select id="kategori_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                        <div id="item_list" class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3">
                            <p class="text-gray-500 text-sm">Pilih kategori terlebih dahulu</p>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Item yang dipilih akan diberikan kepada <strong>semua karyawan</strong> yang terpilih dalam sesi ini.
                        </p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closePilihItemModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Pilih Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Serahkan Item Modal -->
<div id="serahkanItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Serahkan Item</h3>
                <button onclick="closeSerahkanItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="serahkanItemForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Karyawan</label>
                        <select id="karyawan_serahkan_select" name="karyawan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Karyawan</option>
                            @foreach($penyerahan->karyawans as $penyerahanKaryawan)
                                <option value="{{ $penyerahanKaryawan->karyawan_id }}">
                                    {{ $penyerahanKaryawan->karyawan->nama_lengkap }} 
                                    ({{ $penyerahanKaryawan->status_text }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item yang akan diserahkan</label>
                        <div id="item_serahkan_list" class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3">
                            <p class="text-gray-500 text-sm">Pilih karyawan terlebih dahulu</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeSerahkanItemModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">Serahkan Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kembalikan Modal -->
<div id="kembalikanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Kembalikan Perlengkapan</h3>
                <button onclick="closeKembalikanModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="kembalikanForm">
                @csrf
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 mb-4">Isi jumlah dan kondisi item yang dikembalikan:</p>
                    
                    @foreach($items as $index => $item)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                @if($item->item->foto_item)
                                    <img class="h-10 w-10 rounded object-cover" src="{{ $item->item->foto_url }}" alt="{{ $item->item->nama_item }}">
                                @else
                                    <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-toolbox text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->item->nama_item }}</div>
                                    <div class="text-xs text-gray-500">Diserahkan: {{ $item->jumlah_diserahkan }} {{ $item->item->satuan }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Dikembalikan</label>
                                <input type="number" name="items[{{ $index }}][jumlah_dikembalikan]" 
                                       min="0" max="{{ $item->jumlah_diserahkan }}" value="{{ $item->jumlah_diserahkan }}"
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kondisi</label>
                                <select name="items[{{ $index }}][kondisi_dikembalikan]" required
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                                <input type="text" name="items[{{ $index }}][keterangan_item]" 
                                       placeholder="Keterangan kondisi..."
                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeKembalikanModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">Proses Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deletePenyerahanForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
// Modal functions
function showKembalikanModal() {
    document.getElementById('kembalikanModal').classList.remove('hidden');
}

function closeKembalikanModal() {
    document.getElementById('kembalikanModal').classList.add('hidden');
}

function showPilihKaryawanModal() {
    console.log('showPilihKaryawanModal called');
    document.getElementById('pilihKaryawanModal').classList.remove('hidden');
    loadJabatans();
}

function closePilihKaryawanModal() {
    document.getElementById('pilihKaryawanModal').classList.add('hidden');
}

function showPilihItemModal() {
    document.getElementById('pilihItemModal').classList.remove('hidden');
    loadKategoris();
}

function closePilihItemModal() {
    document.getElementById('pilihItemModal').classList.add('hidden');
}

function showSerahkanItemModal() {
    document.getElementById('serahkanItemModal').classList.remove('hidden');
}

function closeSerahkanItemModal() {
    document.getElementById('serahkanItemModal').classList.add('hidden');
}

// Load jabatans for the project
async function loadJabatans() {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/jabatan-by-project?project_id={{ $penyerahan->project_id }}`);
        const result = await response.json();
        
        const jabatanSelect = document.getElementById('jabatan_select');
        jabatanSelect.innerHTML = '<option value="">Pilih Jabatan</option>';
        
        if (result.success) {
            result.data.forEach(jabatan => {
                jabatanSelect.innerHTML += `<option value="${jabatan.id}">${jabatan.nama}</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading jabatans:', error);
    }
}

// Load karyawans by jabatan (multiple selection)
document.getElementById('jabatan_select').addEventListener('change', async function() {
    const jabatanId = this.value;
    const karyawanList = document.getElementById('karyawan_list');
    
    if (!jabatanId) {
        karyawanList.innerHTML = '<p class="text-gray-500 text-sm">Pilih jabatan terlebih dahulu</p>';
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/karyawan-by-jabatan?jabatan_id=${jabatanId}&project_id={{ $penyerahan->project_id }}`);
        const result = await response.json();
        
        karyawanList.innerHTML = '';
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(karyawan => {
                karyawanList.innerHTML += `
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="karyawan_${karyawan.id}" name="karyawan_ids[]" value="${karyawan.id}" class="mr-2">
                        <label for="karyawan_${karyawan.id}" class="text-sm text-gray-900">${karyawan.nama_lengkap}</label>
                    </div>
                `;
            });
        } else {
            karyawanList.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada karyawan ditemukan</p>';
        }
    } catch (error) {
        console.error('Error loading karyawans:', error);
        karyawanList.innerHTML = '<p class="text-red-500 text-sm">Error loading karyawan</p>';
    }
});

// Load kategoris for item selection
async function loadKategoris() {
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/kategori-by-project?project_id={{ $penyerahan->project_id }}`);
        const result = await response.json();
        
        const kategoriSelect = document.getElementById('kategori_select');
        kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
        
        if (result.success) {
            result.data.forEach(kategori => {
                kategoriSelect.innerHTML += `<option value="${kategori.id}">${kategori.nama_kategori}</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading kategoris:', error);
    }
}

// Load items by kategori
document.getElementById('kategori_select').addEventListener('change', async function() {
    const kategoriId = this.value;
    const itemList = document.getElementById('item_list');
    
    if (!kategoriId) {
        itemList.innerHTML = '<p class="text-gray-500 text-sm">Pilih kategori terlebih dahulu</p>';
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/items-by-kategori?kategori_id=${kategoriId}`);
        const result = await response.json();
        
        itemList.innerHTML = '';
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(item => {
                itemList.innerHTML += `
                    <div class="flex items-center justify-between mb-3 p-2 border border-gray-200 rounded">
                        <div class="flex items-center">
                            <input type="checkbox" id="item_${item.id}" name="item_ids[]" value="${item.id}" class="mr-3">
                            <label for="item_${item.id}" class="text-sm text-gray-900">${item.nama_item}</label>
                            <span class="text-xs text-gray-500 ml-2">(Stok: ${item.stok_tersedia} ${item.satuan})</span>
                        </div>
                        <div class="flex items-center">
                            <label class="text-xs text-gray-700 mr-2">Jumlah:</label>
                            <input type="number" id="jumlah_${item.id}" min="1" max="${item.stok_tersedia}" value="1" class="w-16 px-2 py-1 text-xs border border-gray-300 rounded">
                        </div>
                    </div>
                `;
            });
        } else {
            itemList.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada item ditemukan</p>';
        }
    } catch (error) {
        console.error('Error loading items:', error);
        itemList.innerHTML = '<p class="text-red-500 text-sm">Error loading items</p>';
    }
});

// Load items for karyawan in serahkan modal
document.getElementById('karyawan_serahkan_select').addEventListener('change', async function() {
    const karyawanId = this.value;
    const itemList = document.getElementById('item_serahkan_list');
    
    if (!karyawanId) {
        itemList.innerHTML = '<p class="text-gray-500 text-sm">Pilih karyawan terlebih dahulu</p>';
        return;
    }
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/items-by-karyawan?karyawan_id=${karyawanId}`);
        const result = await response.json();
        
        itemList.innerHTML = '';
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(item => {
                const statusBadge = item.is_diserahkan ? 
                    '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Sudah Diserahkan</span>' :
                    '<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Belum Diserahkan</span>';
                
                itemList.innerHTML += `
                    <div class="flex items-center justify-between mb-2 p-2 border border-gray-200 rounded ${item.is_diserahkan ? 'bg-green-50' : ''}">
                        <div class="flex items-center">
                            <input type="checkbox" id="serahkan_item_${item.id}" name="item_ids[]" value="${item.id}" 
                                   ${item.is_diserahkan ? 'disabled' : ''} class="mr-3">
                            <label for="serahkan_item_${item.id}" class="text-sm text-gray-900">${item.item.nama_item}</label>
                            <span class="text-xs text-gray-500 ml-2">(${item.jumlah_diserahkan} ${item.item.satuan})</span>
                        </div>
                        ${statusBadge}
                    </div>
                `;
            });
        } else {
            itemList.innerHTML = '<p class="text-gray-500 text-sm">Tidak ada item untuk karyawan ini</p>';
        }
    } catch (error) {
        console.error('Error loading items:', error);
        itemList.innerHTML = '<p class="text-red-500 text-sm">Error loading items</p>';
    }
});

// Pilih karyawan form submission
document.getElementById('pilihKaryawanForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const checkedKaryawan = document.querySelectorAll('input[name="karyawan_ids[]"]:checked');
    if (checkedKaryawan.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu karyawan'
        });
        return;
    }
    
    const karyawanIds = Array.from(checkedKaryawan).map(cb => cb.value);
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/pilih-karyawan`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ karyawan_ids: karyawanIds })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closePilihKaryawanModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memilih karyawan'
        });
    }
});

// Pilih item form submission
document.getElementById('pilihItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const checkedItems = document.querySelectorAll('input[name="item_ids[]"]:checked');
    if (checkedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu item'
        });
        return;
    }
    
    const items = Array.from(checkedItems).map(cb => {
        const itemId = cb.value;
        const jumlah = document.getElementById(`jumlah_${itemId}`).value;
        return {
            item_id: itemId,
            jumlah: parseInt(jumlah)
        };
    });
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/pilih-item`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items: items })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closePilihItemModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memilih item'
        });
    }
});

// Serahkan item form submission
document.getElementById('serahkanItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const karyawanId = document.getElementById('karyawan_serahkan_select').value;
    const checkedItems = document.querySelectorAll('input[name="item_ids[]"]:checked:not(:disabled)');
    
    if (!karyawanId) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih karyawan terlebih dahulu'
        });
        return;
    }
    
    if (checkedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu item untuk diserahkan'
        });
        return;
    }
    
    const itemIds = Array.from(checkedItems).map(cb => cb.value);
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/serahkan-item`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                karyawan_id: karyawanId,
                item_ids: itemIds 
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeSerahkanItemModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyerahkan item'
        });
    }
});

// Action functions
async function updateStatusToPending(hashId) {
    const result = await Swal.fire({
        title: 'Set Status Pending?',
        text: 'Jadwal penyerahan akan diubah ke status pending dan siap untuk diserahkan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Set Pending!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/penyerahan-perlengkapan/${hashId}/set-pending`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengubah status'
            });
        }
    }
}

async function serahkanPenyerahan(hashId, karyawanName) {
    const result = await Swal.fire({
        title: 'Konfirmasi Penyerahan',
        text: `Apakah Anda yakin ingin mengkonfirmasi penyerahan kepada ${karyawanName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Serahkan!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/perusahaan/penyerahan-perlengkapan/${hashId}/serahkan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengkonfirmasi penyerahan'
            });
        }
    }
}

async function deletePenyerahan(hashId, itemName) {
    const result = await Swal.fire({
        title: 'Hapus Penyerahan?',
        text: `Apakah Anda yakin ingin menghapus ${itemName}? Data yang sudah dihapus tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        const form = document.getElementById('deletePenyerahanForm');
        form.action = `/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}`;
        form.submit();
    }
}

// Kembalikan form submission
@if($items->count() > 0)
document.getElementById('kembalikanForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/perusahaan/penyerahan-perlengkapan/{{ $penyerahan->hash_id }}/kembalikan`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeKembalikanModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses pengembalian'
        });
    }
});
@endif
</script>
@endpush
@endsection