@extends('perusahaan.layouts.app')

@section('title', 'Edit Kartu Tamu')
@section('page-title', 'Edit Kartu Tamu')
@section('page-subtitle', $project->nama . ' - ' . $area->nama)

@section('content')
<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('perusahaan.kartu-tamu.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-id-card mr-2"></i>
                    Kartu Tamu
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('perusahaan.kartu-tamu.detail', ['project_id' => $kartuTamu->project_id, 'area_id' => $kartuTamu->area_id]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">{{ $project->nama }} - {{ $area->nama }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit {{ $kartuTamu->no_kartu }}</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Form Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-edit mr-2 text-blue-600"></i>
            Edit Kartu Tamu
        </h3>
        <p class="text-sm text-gray-600 mt-1">Edit informasi kartu {{ $kartuTamu->no_kartu }} untuk {{ $project->nama }} - {{ $area->nama }}</p>
    </div>

    <form action="{{ route('perusahaan.kartu-tamu.update', $kartuTamu->hash_id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nomor Kartu -->
            <div>
                <label for="no_kartu" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Kartu <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="no_kartu" 
                    name="no_kartu" 
                    value="{{ old('no_kartu', $kartuTamu->no_kartu) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: GT-001"
                    required
                >
                @error('no_kartu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- NFC Kartu -->
            <div>
                <label for="nfc_kartu" class="block text-sm font-medium text-gray-700 mb-2">
                    NFC Kartu
                </label>
                <input 
                    type="text" 
                    id="nfc_kartu" 
                    name="nfc_kartu" 
                    value="{{ old('nfc_kartu', $kartuTamu->nfc_kartu) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: 04:A3:B2:C1:D4:E5:F6"
                >
                @error('nfc_kartu')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">Pilih Status</option>
                    <option value="aktif" {{ old('status', $kartuTamu->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="rusak" {{ old('status', $kartuTamu->status) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="hilang" {{ old('status', $kartuTamu->status) == 'hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @if($kartuTamu->is_assigned)
                    <p class="text-amber-600 text-sm mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Kartu sedang dipinjamkan. Jika status diubah ke rusak/hilang, kartu akan otomatis dikembalikan.
                    </p>
                @endif
            </div>

            <!-- Current Guest Info (if assigned) -->
            @if($kartuTamu->currentGuest)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tamu Saat Ini
                </label>
                <div class="p-3 bg-orange-50 border border-orange-200 rounded-lg">
                    <p class="text-sm font-medium text-orange-800">{{ $kartuTamu->currentGuest->nama_tamu }}</p>
                    <p class="text-xs text-orange-600">{{ $kartuTamu->currentGuest->perusahaan_tamu }}</p>
                    <p class="text-xs text-orange-600">Dipinjam: {{ $kartuTamu->assigned_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea 
                    id="keterangan" 
                    name="keterangan" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="Tambahkan keterangan jika diperlukan..."
                >{{ old('keterangan', $kartuTamu->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('perusahaan.kartu-tamu.detail', ['project_id' => $kartuTamu->project_id, 'area_id' => $kartuTamu->area_id]) }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button 
                type="submit" 
                class="px-6 py-3 text-white rounded-lg transition font-medium shadow-lg hover:shadow-xl"
                style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);"
            >
                <i class="fas fa-save mr-2"></i>Update Kartu
            </button>
        </div>
    </form>
</div>
@endsection