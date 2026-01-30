@extends('perusahaan.layouts.app')

@section('title', 'Tambah Jadwal Maintenance')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Jadwal Maintenance</h1>
            <p class="text-gray-600 mt-1">Buat jadwal maintenance baru untuk aset</p>
        </div>
        <a href="{{ route('perusahaan.maintenance-aset.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="{{ route('perusahaan.maintenance-aset.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Informasi Dasar
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Aset <span class="text-red-500">*</span></label>
                        <select name="asset_type" id="asset_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Tipe Aset</option>
                            <option value="data_aset" {{ old('asset_type') == 'data_aset' ? 'selected' : '' }}>Data Aset</option>
                            <option value="aset_kendaraan" {{ old('asset_type') == 'aset_kendaraan' ? 'selected' : '' }}>Aset Kendaraan</option>
                        </select>
                        @error('asset_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aset <span class="text-red-500">*</span></label>
                        <select name="asset_id" id="asset_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required disabled>
                            <option value="">Pilih aset terlebih dahulu</option>
                        </select>
                        @error('asset_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Maintenance <span class="text-red-500">*</span></label>
                        <select name="jenis_maintenance" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Jenis</option>
                            <option value="preventive" {{ old('jenis_maintenance') == 'preventive' ? 'selected' : '' }}>Preventive (Pencegahan)</option>
                            <option value="corrective" {{ old('jenis_maintenance') == 'corrective' ? 'selected' : '' }}>Corrective (Perbaikan)</option>
                            <option value="predictive" {{ old('jenis_maintenance') == 'predictive' ? 'selected' : '' }}>Predictive (Prediktif)</option>
                        </select>
                        @error('jenis_maintenance')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas <span class="text-red-500">*</span></label>
                        <select name="prioritas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Prioritas</option>
                            <option value="low" {{ old('prioritas') == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="medium" {{ old('prioritas') == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="high" {{ old('prioritas') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="urgent" {{ old('prioritas') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                        </select>
                        @error('prioritas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar text-blue-600"></i>
                    Jadwal Maintenance
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Maintenance <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_maintenance" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('tanggal_maintenance') }}" 
                               min="{{ date('Y-m-d') }}" required>
                        @error('tanggal_maintenance')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('waktu_mulai') }}">
                        @error('waktu_mulai')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Durasi (menit)</label>
                        <input type="number" name="estimasi_durasi" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('estimasi_durasi') }}" 
                               min="1" placeholder="60">
                        @error('estimasi_durasi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Work Description -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    Deskripsi Pekerjaan
                </h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi_pekerjaan" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Jelaskan pekerjaan maintenance yang akan dilakukan..." required>{{ old('deskripsi_pekerjaan') }}</textarea>
                        @error('deskripsi_pekerjaan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Sebelum Maintenance</label>
                        <textarea name="catatan_sebelum" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Catatan kondisi aset sebelum maintenance...">{{ old('catatan_sebelum') }}</textarea>
                        @error('catatan_sebelum')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Personnel Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-blue-600"></i>
                    Petugas Maintenance
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teknisi Internal</label>
                        <input type="text" name="teknisi_internal" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('teknisi_internal') }}" 
                               placeholder="Nama teknisi internal">
                        @error('teknisi_internal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Eksternal</label>
                        <input type="text" name="vendor_eksternal" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('vendor_eksternal') }}" 
                               placeholder="Nama vendor/perusahaan">
                        @error('vendor_eksternal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kontak Vendor</label>
                        <input type="text" name="kontak_vendor" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('kontak_vendor') }}" 
                               placeholder="Nomor telepon/email">
                        @error('kontak_vendor')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Cost Information -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-money-bill text-blue-600"></i>
                    Estimasi Biaya (Opsional)
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Biaya estimasi ini bersifat opsional. Biaya real akan diinput setelah maintenance selesai dikerjakan.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Biaya Sparepart (Rp)</label>
                        <input type="number" name="biaya_sparepart" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('biaya_sparepart', 0) }}" 
                               min="0" step="1000" placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Perkiraan biaya sparepart yang dibutuhkan</p>
                        @error('biaya_sparepart')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Biaya Jasa (Rp)</label>
                        <input type="number" name="biaya_jasa" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('biaya_jasa', 0) }}" 
                               min="0" step="1000" placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Perkiraan biaya jasa teknisi/vendor</p>
                        @error('biaya_jasa')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Biaya Lainnya (Rp)</label>
                        <input type="number" name="biaya_lainnya" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('biaya_lainnya', 0) }}" 
                               min="0" step="1000" placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Perkiraan biaya lain-lain (transport, dll)</p>
                        @error('biaya_lainnya')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Reminder Settings -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-bell text-blue-600"></i>
                    Pengaturan Reminder
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="reminder_aktif" id="reminder_aktif" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                   {{ old('reminder_aktif') ? 'checked' : '' }}>
                            <label for="reminder_aktif" class="ml-2 block text-sm text-gray-700">
                                Aktifkan reminder maintenance
                            </label>
                        </div>
                        
                        <div id="reminder_settings" class="{{ old('reminder_aktif') ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reminder (hari sebelum)</label>
                            <input type="number" name="reminder_hari" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old('reminder_hari', 7) }}" 
                                   min="1" max="365">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Interval Maintenance Berikutnya (hari)</label>
                        <input type="number" name="interval_maintenance" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ old('interval_maintenance') }}" 
                               min="1" placeholder="90">
                        <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ada jadwal berulang</p>
                        @error('interval_maintenance')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- File Uploads -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-paperclip text-blue-600"></i>
                    Lampiran
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Kondisi Sebelum</label>
                        <input type="file" name="foto_sebelum" accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                        @error('foto_sebelum')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Pendukung</label>
                        <input type="file" name="dokumen_pendukung" accept=".pdf,.doc,.docx" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX. Maksimal 5MB</p>
                        @error('dokumen_pendukung')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('perusahaan.maintenance-aset.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const assetTypeSelect = document.getElementById('asset_type');
    const assetSelect = document.getElementById('asset_id');
    const reminderCheckbox = document.getElementById('reminder_aktif');
    const reminderSettings = document.getElementById('reminder_settings');

    // Handle reminder checkbox
    reminderCheckbox.addEventListener('change', function() {
        if (this.checked) {
            reminderSettings.classList.remove('hidden');
        } else {
            reminderSettings.classList.add('hidden');
        }
    });

    // Handle asset loading
    function loadAssets() {
        const projectId = projectSelect.value;
        const assetType = assetTypeSelect.value;
        
        if (!projectId || !assetType) {
            assetSelect.innerHTML = '<option value="">Pilih project dan tipe aset terlebih dahulu</option>';
            assetSelect.disabled = true;
            return;
        }

        assetSelect.innerHTML = '<option value="">Loading...</option>';
        assetSelect.disabled = true;

        fetch(`/perusahaan/maintenance-aset/get-assets?project_id=${projectId}&asset_type=${assetType}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Asset loading response:', data); // Debug log
                
                assetSelect.innerHTML = '<option value="">Pilih Aset</option>';
                
                if (data.success && data.data && data.data.length > 0) {
                    data.data.forEach(asset => {
                        const option = document.createElement('option');
                        option.value = asset.id;
                        option.textContent = asset.text;
                        assetSelect.appendChild(option);
                    });
                    assetSelect.disabled = false;
                } else {
                    assetSelect.innerHTML = '<option value="">Tidak ada aset tersedia untuk project ini</option>';
                    console.log('No assets found:', data);
                }
            })
            .catch(error => {
                console.error('Error loading assets:', error);
                assetSelect.innerHTML = '<option value="">Error loading assets</option>';
                
                // Show user-friendly error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error Loading Assets',
                    text: 'Gagal memuat daftar aset. Silakan refresh halaman dan coba lagi.',
                    footer: 'Error: ' + error.message
                });
            });
    }

    projectSelect.addEventListener('change', loadAssets);
    assetTypeSelect.addEventListener('change', loadAssets);

    // Load assets if values are already selected (for old input)
    if (projectSelect.value && assetTypeSelect.value) {
        loadAssets();
    }
});
</script>
@endpush