@extends('perusahaan.layouts.app')

@section('title', 'Edit Permintaan Lembur')
@section('page-title', 'Edit Permintaan Lembur')
@section('page-subtitle', 'Ubah data permintaan lembur karyawan')

@section('content')
<!-- Flash Messages -->
@if($errors->any())
<div id="errorAlert" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl">
    <div class="flex items-center gap-3 mb-2">
        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        <span class="font-medium">Terdapat kesalahan pada form:</span>
    </div>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('perusahaan.lembur.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Lembur
    </a>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Edit Permintaan Lembur</h3>
        <p class="text-sm text-gray-600">Ubah data permintaan lembur karyawan</p>
    </div>

    <form action="{{ route('perusahaan.lembur.update', $lembur->hash_id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Project Selection -->
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Project <span class="text-red-500">*</span>
                </label>
                <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('project_id') border-red-300 @enderror" required>
                    <option value="">Pilih Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ (old('project_id') ?? $lembur->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->nama }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Karyawan Selection -->
            <div>
                <label for="karyawan_search" class="block text-sm font-medium text-gray-700 mb-2">
                    Karyawan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="karyawan_search" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('karyawan_id') border-red-300 @enderror" 
                           placeholder="Ketik nama atau NIK karyawan..." 
                           autocomplete="off">
                    <input type="hidden" name="karyawan_id" id="karyawan_id" value="{{ old('karyawan_id') ?? $lembur->karyawan_id }}">
                    
                    <!-- Search Results Dropdown -->
                    <div id="karyawan_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                    
                    <!-- Selected Karyawan Display -->
                    <div id="selected_karyawan" class="mt-2 {{ $lembur->karyawan ? '' : 'hidden' }}">
                        <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div>
                                <div class="font-medium text-blue-900" id="selected_name">{{ $lembur->karyawan->nama_lengkap ?? '' }}</div>
                                <div class="text-sm text-blue-600" id="selected_nik">NIK: {{ $lembur->karyawan->nik_karyawan ?? '' }}</div>
                            </div>
                            <button type="button" onclick="clearKaryawan()" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @error('karyawan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Ketik minimal 2 karakter untuk mencari</p>
            </div>

            <!-- Tanggal Lembur -->
            <div>
                <label for="tanggal_lembur" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Lembur <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_lembur" id="tanggal_lembur" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_lembur') border-red-300 @enderror" 
                       value="{{ old('tanggal_lembur') ?? $lembur->tanggal_lembur->format('Y-m-d') }}" required>
                @error('tanggal_lembur')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Mulai -->
            <div>
                <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                    Jam Mulai <span class="text-red-500">*</span>
                </label>
                <input type="time" name="jam_mulai" id="jam_mulai" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jam_mulai') border-red-300 @enderror" 
                       value="{{ old('jam_mulai') ?? $lembur->jam_mulai->format('H:i') }}" required>
                @error('jam_mulai')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Selesai -->
            <div>
                <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                    Jam Selesai <span class="text-red-500">*</span>
                </label>
                <input type="time" name="jam_selesai" id="jam_selesai" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jam_selesai') border-red-300 @enderror" 
                       value="{{ old('jam_selesai') ?? $lembur->jam_selesai->format('H:i') }}" required>
                @error('jam_selesai')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Total Jam Display -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Total Jam Lembur</label>
            <input type="text" id="total_jam_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly placeholder="Akan dihitung otomatis">
            <p class="mt-1 text-sm text-gray-500">Dihitung otomatis berdasarkan jam mulai dan selesai</p>
        </div>

        <!-- Tarif Lembur (Auto-calculated) -->
        <div class="mt-6">
            <label for="tarif_lembur_per_jam" class="block text-sm font-medium text-gray-700 mb-2">
                Tarif Lembur per Jam (Rp) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="number" name="tarif_lembur_per_jam" id="tarif_lembur_per_jam" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tarif_lembur_per_jam') border-red-300 @enderror" 
                       value="{{ old('tarif_lembur_per_jam') ?? $lembur->tarif_lembur_per_jam }}" min="0" step="1000" readonly>
                <button type="button" id="edit_tarif_btn" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 hidden" onclick="enableTarifEdit()">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            @error('tarif_lembur_per_jam')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">
                <span id="tarif_info">Tarif akan dihitung otomatis berdasarkan tanggal dan pengaturan payroll</span>
            </p>
        </div>

        <!-- Total Jam Display -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Total Jam Lembur</label>
            <input type="text" id="total_jam_display" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly placeholder="Akan dihitung otomatis" value="{{ $lembur->total_jam }} jam">
            <p class="mt-1 text-sm text-gray-500">Dihitung otomatis berdasarkan jam mulai dan selesai</p>
        </div>

        <!-- Alasan Lembur -->
        <div class="mt-6">
            <label for="alasan_lembur" class="block text-sm font-medium text-gray-700 mb-2">
                Alasan Lembur <span class="text-red-500">*</span>
            </label>
            <textarea name="alasan_lembur" id="alasan_lembur" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alasan_lembur') border-red-300 @enderror" 
                      placeholder="Jelaskan alasan mengapa perlu lembur..." required>{{ old('alasan_lembur') ?? $lembur->alasan_lembur }}</textarea>
            @error('alasan_lembur')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Deskripsi Pekerjaan -->
        <div class="mt-6">
            <label for="deskripsi_pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi Pekerjaan <span class="text-red-500">*</span>
            </label>
            <textarea name="deskripsi_pekerjaan" id="deskripsi_pekerjaan" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deskripsi_pekerjaan') border-red-300 @enderror" 
                      placeholder="Jelaskan detail pekerjaan yang akan dilakukan saat lembur..." required>{{ old('deskripsi_pekerjaan') ?? $lembur->deskripsi_pekerjaan }}</textarea>
            @error('deskripsi_pekerjaan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Estimasi Upah -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-lg font-medium text-blue-900 mb-3">
                <i class="fas fa-calculator mr-2"></i>
                Estimasi Upah Lembur
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-blue-800">Total Jam:</span>
                    <span id="estimasi_jam" class="ml-2 font-bold text-blue-900">{{ $lembur->total_jam }} jam</span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Tarif per Jam:</span>
                    <span class="ml-2 font-bold text-blue-900">Rp <span id="estimasi_tarif">{{ number_format($lembur->tarif_lembur_per_jam, 0, ',', '.') }}</span></span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Total Upah:</span>
                    <span class="ml-2 font-bold text-blue-900">Rp <span id="estimasi_total">{{ number_format($lembur->total_upah_lembur ?? 0, 0, ',', '.') }}</span></span>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-8 flex items-center justify-end gap-3">
            <a href="{{ route('perusahaan.lembur.index') }}" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white rounded-lg shadow-sm transition-all duration-200 hover:shadow-md" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                <i class="fas fa-save mr-2"></i>
                Update Permintaan Lembur
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    let currentProjectId = document.getElementById('project_id').value;
    
    // Project selection handler
    document.getElementById('project_id').addEventListener('change', function() {
        currentProjectId = this.value;
        const karyawanSearch = document.getElementById('karyawan_search');
        
        if (currentProjectId) {
            karyawanSearch.disabled = false;
            karyawanSearch.placeholder = 'Ketik nama atau NIK karyawan...';
            // Don't clear if same project
            if (this.value !== '{{ $lembur->project_id }}') {
                clearKaryawan();
            }
        } else {
            karyawanSearch.disabled = true;
            karyawanSearch.placeholder = 'Pilih Project terlebih dahulu';
            clearKaryawan();
        }
    });

    // Karyawan search handler
    document.getElementById('karyawan_search').addEventListener('input', function() {
        const searchValue = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (searchValue.length < 2 || !currentProjectId) {
            hideDropdown();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchKaryawan(searchValue);
        }, 300);
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#karyawan_search') && !e.target.closest('#karyawan_dropdown')) {
            hideDropdown();
        }
    });

    function searchKaryawan(search) {
        const dropdown = document.getElementById('karyawan_dropdown');
        dropdown.innerHTML = `
            <div class="p-3 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Mencari karyawan...
            </div>
        `;
        dropdown.classList.remove('hidden');
        
        fetch(`/perusahaan/lembur-search-karyawan?project_id=${currentProjectId}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displaySearchResults(data.data);
                } else {
                    dropdown.innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            <i class="fas fa-exclamation-triangle mb-2"></i>
                            <div class="text-sm">${data.message || 'Error memuat data'}</div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                dropdown.innerHTML = `
                    <div class="p-4 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <div class="text-sm">Terjadi kesalahan saat mencari</div>
                        <div class="text-xs text-gray-400 mt-1">Silakan coba lagi</div>
                    </div>
                `;
            });
    }

    function displaySearchResults(karyawans) {
        const dropdown = document.getElementById('karyawan_dropdown');
        
        if (karyawans.length === 0) {
            dropdown.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search mb-2 text-gray-400"></i>
                    <div class="text-sm">Tidak ada karyawan ditemukan</div>
                    <div class="text-xs text-gray-400 mt-1">Pastikan project sudah dipilih dan nama/NIK benar</div>
                </div>
            `;
            return;
        }
        
        let html = '';
        karyawans.forEach(karyawan => {
            html += `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                     onclick="selectKaryawan(${karyawan.id}, '${karyawan.nama_lengkap}', '${karyawan.nik_karyawan}')">
                    <div class="font-medium text-gray-900">${karyawan.nama_lengkap}</div>
                    <div class="text-sm text-gray-500">NIK: ${karyawan.nik_karyawan}</div>
                </div>
            `;
        });
        
        dropdown.innerHTML = html;
    }

    window.selectKaryawan = function(id, nama, nik) {
        // Set hidden input value
        document.getElementById('karyawan_id').value = id;
        
        // Clear search input
        document.getElementById('karyawan_search').value = '';
        
        // Show selected karyawan
        document.getElementById('selected_name').textContent = nama;
        document.getElementById('selected_nik').textContent = `NIK: ${nik}`;
        document.getElementById('selected_karyawan').classList.remove('hidden');
        
        // Hide dropdown
        hideDropdown();
        
        // Calculate overtime rate
        calculateOvertimeRate();
    };

    window.clearKaryawan = function() {
        document.getElementById('karyawan_id').value = '';
        document.getElementById('karyawan_search').value = '';
        document.getElementById('selected_karyawan').classList.add('hidden');
        hideDropdown();
        
        // Reset overtime rate
        document.getElementById('tarif_lembur_per_jam').value = '';
        document.getElementById('tarif_info').textContent = 'Tarif akan dihitung otomatis berdasarkan tanggal dan pengaturan payroll';
        calculateHours();
    };

    function hideDropdown() {
        document.getElementById('karyawan_dropdown').classList.add('hidden');
    }

    // Enable manual tarif editing
    window.enableTarifEdit = function() {
        const tarifInput = document.getElementById('tarif_lembur_per_jam');
        const editBtn = document.getElementById('edit_tarif_btn');
        
        tarifInput.readOnly = false;
        tarifInput.classList.add('border-yellow-300', 'bg-yellow-50');
        tarifInput.focus();
        
        if (editBtn) {
            editBtn.classList.add('hidden');
        }
        
        // Add event listener for manual input
        tarifInput.addEventListener('input', function() {
            calculateHours();
            document.getElementById('tarif_info').innerHTML = `
                <span class="text-yellow-600">
                    <i class="fas fa-edit mr-1"></i>Tarif diinput manual
                </span><br>
                <span class="text-xs text-gray-500">Klik "Hitung Otomatis" untuk kembali ke perhitungan otomatis</span>
            `;
        });
        
        // Update info
        document.getElementById('tarif_info').innerHTML = `
            <span class="text-yellow-600">
                <i class="fas fa-edit mr-1"></i>Mode input manual aktif
            </span><br>
            <span class="text-xs text-blue-600 cursor-pointer" onclick="calculateOvertimeRate()">
                <i class="fas fa-calculator mr-1"></i>Klik untuk hitung otomatis
            </span>
        `;
    };

    // Calculate overtime rate based on date and employee
    function calculateOvertimeRate() {
        const tanggalLembur = document.getElementById('tanggal_lembur').value;
        const karyawanId = document.getElementById('karyawan_id').value;
        
        console.log('Calculating overtime rate:', { tanggalLembur, karyawanId });
        
        if (!tanggalLembur || !karyawanId) {
            console.log('Missing required data for calculation');
            return;
        }
        
        // Show loading state
        document.getElementById('tarif_info').innerHTML = '<span class="text-blue-500"><i class="fas fa-spinner fa-spin mr-1"></i>Menghitung tarif...</span>';
        
        fetch(`/perusahaan/lembur-overtime-rate?date=${tanggalLembur}&karyawan_id=${karyawanId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Overtime rate response:', data);
                if (data.success) {
                    const rateData = data.data;
                    const tarifInput = document.getElementById('tarif_lembur_per_jam');
                    
                    // Reset field styling and make readonly
                    tarifInput.readOnly = true;
                    tarifInput.classList.remove('border-yellow-300', 'bg-yellow-50');
                    tarifInput.value = rateData.overtime_rate;
                    
                    // Create allowances breakdown
                    let allowancesText = '';
                    if (rateData.allowances_breakdown && Object.keys(rateData.allowances_breakdown).length > 0) {
                        allowancesText = '<br><span class="text-xs text-gray-600">Tunjangan Tetap: ';
                        const allowancesList = [];
                        Object.values(rateData.allowances_breakdown).forEach(allowance => {
                            allowancesList.push(`${allowance.nama} (Rp ${allowance.nilai.toLocaleString('id-ID')})`);
                        });
                        allowancesText += allowancesList.join(', ') + '</span>';
                    }
                    
                    // Update info text with detailed breakdown
                    const infoText = `${rateData.day_type} - ${rateData.multiplier}x upah per jam (Rp ${rateData.hourly_rate.toLocaleString('id-ID')})`;
                    document.getElementById('tarif_info').innerHTML = `
                        <span class="text-blue-600">${infoText}</span><br>
                        <span class="text-xs text-gray-500">
                            Gaji Pokok: Rp ${rateData.gaji_pokok.toLocaleString('id-ID')} + 
                            Tunjangan: Rp ${rateData.allowances_total.toLocaleString('id-ID')} = 
                            <strong>Rp ${rateData.total_monthly_salary.toLocaleString('id-ID')}</strong>
                        </span>
                        ${allowancesText}
                        <br><span class="text-xs text-gray-400">Max ${rateData.max_hours} jam/hari | Formula: (Gaji Total ÷ 173) × ${rateData.multiplier}</span>
                    `;
                    
                    // Recalculate total
                    calculateHours();
                } else {
                    console.error('Calculation failed:', data.message);
                    document.getElementById('tarif_info').innerHTML = `
                        <span class="text-red-500">${data.message}</span><br>
                        <span class="text-xs text-blue-600 cursor-pointer" onclick="enableTarifEdit()">
                            <i class="fas fa-edit mr-1"></i>Klik untuk input manual
                        </span>
                    `;
                    // Show edit button and enable manual input
                    enableTarifEdit();
                }
            })
            .catch(error => {
                console.error('Error calculating overtime rate:', error);
                document.getElementById('tarif_info').innerHTML = `
                    <span class="text-red-500">Error menghitung tarif lembur: ${error.message}</span><br>
                    <span class="text-xs text-gray-400">Silakan refresh halaman atau hubungi admin</span>
                `;
            });
    }

    // Calculate total hours and estimate pay
    function calculateHours() {
        const jamMulai = document.getElementById('jam_mulai').value;
        const jamSelesai = document.getElementById('jam_selesai').value;
        const tarif = parseFloat(document.getElementById('tarif_lembur_per_jam').value) || 0;
        
        if (jamMulai && jamSelesai) {
            const mulai = new Date(`2000-01-01 ${jamMulai}`);
            const selesai = new Date(`2000-01-01 ${jamSelesai}`);
            
            // Handle overnight shift
            if (selesai < mulai) {
                selesai.setDate(selesai.getDate() + 1);
            }
            
            const diffMs = selesai - mulai;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            // Validate minimum duration (30 minutes = 0.5 hours)
            if (diffHours < 0.5) {
                document.getElementById('total_jam_display').value = Math.round(diffHours) + ' jam (Minimal 30 menit)';
                document.getElementById('total_jam_display').classList.add('border-red-300', 'bg-red-50');
                document.getElementById('estimasi_jam').textContent = Math.round(diffHours) + ' jam';
                document.getElementById('estimasi_jam').classList.add('text-red-600');
            } else {
                document.getElementById('total_jam_display').value = Math.round(diffHours) + ' jam';
                document.getElementById('total_jam_display').classList.remove('border-red-300', 'bg-red-50');
                document.getElementById('estimasi_jam').textContent = Math.round(diffHours) + ' jam';
                document.getElementById('estimasi_jam').classList.remove('text-red-600');
            }
            
            const totalUpah = Math.round(diffHours * tarif);
            document.getElementById('estimasi_total').textContent = totalUpah.toLocaleString('id-ID');
        } else {
            document.getElementById('total_jam_display').value = '';
            document.getElementById('total_jam_display').classList.remove('border-red-300', 'bg-red-50');
            document.getElementById('estimasi_jam').textContent = '0 jam';
            document.getElementById('estimasi_jam').classList.remove('text-red-600');
            document.getElementById('estimasi_total').textContent = '0';
        }
        
        updateTarifDisplay();
    }

    function updateTarifDisplay() {
        const tarif = parseFloat(document.getElementById('tarif_lembur_per_jam').value) || 0;
        document.getElementById('estimasi_tarif').textContent = tarif.toLocaleString('id-ID');
    }
        calculateHours();
    }

    // Bind events
    document.getElementById('jam_mulai').addEventListener('change', calculateHours);
    document.getElementById('jam_selesai').addEventListener('change', calculateHours);
    document.getElementById('tanggal_lembur').addEventListener('change', function() {
        calculateOvertimeRate();
    });
    
    // Initialize calculations on page load
    calculateOvertimeRate();
    calculateHours();
    
    // Handle form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const karyawanId = document.getElementById('karyawan_id').value;
        if (!karyawanId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Silakan pilih karyawan terlebih dahulu',
                confirmButtonText: 'OK'
            }).then(() => {
                document.getElementById('karyawan_search').focus();
            });
            return;
        }
        
        // Validate minimum time duration
        const jamMulai = document.getElementById('jam_mulai').value;
        const jamSelesai = document.getElementById('jam_selesai').value;
        
        if (jamMulai && jamSelesai) {
            const mulai = new Date(`2000-01-01 ${jamMulai}`);
            const selesai = new Date(`2000-01-01 ${jamSelesai}`);
            
            // Handle overnight shift
            if (selesai < mulai) {
                selesai.setDate(selesai.getDate() + 1);
            }
            
            const diffMs = selesai - mulai;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            if (diffHours < 0.5) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Durasi Tidak Valid!',
                    text: `Durasi lembur minimal adalah 30 menit. Durasi saat ini: ${Math.round(diffHours * 60)} menit.`,
                    confirmButtonText: 'OK'
                }).then(() => {
                    document.getElementById('jam_selesai').focus();
                });
                return;
            }
        }
    });
});
</script>
@endpush