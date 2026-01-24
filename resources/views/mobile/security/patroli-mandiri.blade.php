<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patroli Mandiri - Nice Patrol</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('mobile/js/app.js') }}"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 sticky top-0 z-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <button onclick="history.back()" class="mr-3">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <h1 class="text-lg font-semibold">Patroli Mandiri</h1>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="loadMyReports()" class="p-2 rounded-full hover:bg-blue-700">
                    <i class="fas fa-list"></i>
                </button>
                <button onclick="getCurrentLocation()" class="p-2 rounded-full hover:bg-blue-700" id="locationBtn">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto p-4 max-w-md">
        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-4">
            <form id="patroliMandiriForm" enctype="multipart/form-data">
                <!-- Project Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select id="project_id" name="project_id" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Project</option>
                    </select>
                </div>

                <!-- Area Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Area Patrol
                    </label>
                    <select id="area_patrol_id" name="area_patrol_id" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Area (Opsional)</option>
                    </select>
                </div>

                <!-- Location Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_lokasi" name="nama_lokasi" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Masukkan nama lokasi" required>
                </div>

                <!-- GPS Coordinates -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Koordinat GPS <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" id="latitude" name="latitude" step="any"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Latitude" required readonly>
                        <input type="number" id="longitude" name="longitude" step="any"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Longitude" required readonly>
                    </div>
                    <button type="button" onclick="getCurrentLocation()" class="mt-2 w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-crosshairs mr-2"></i>Ambil Lokasi Saat Ini
                    </button>
                </div>

                <!-- Status Lokasi -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kondisi Lokasi <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="status_lokasi" value="aman" class="mr-3 text-blue-600" required>
                            <div>
                                <div class="font-medium text-green-600">
                                    <i class="fas fa-shield-alt mr-2"></i>Aman
                                </div>
                                <div class="text-xs text-gray-500">Kondisi normal</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="status_lokasi" value="tidak_aman" class="mr-3 text-blue-600" required>
                            <div>
                                <div class="font-medium text-red-600">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Tidak Aman
                                </div>
                                <div class="text-xs text-gray-500">Ada kendala</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Jenis Kendala (Hidden by default) -->
                <div id="jenisKendalaSection" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kendala <span class="text-red-500">*</span>
                    </label>
                    <select id="jenis_kendala" name="jenis_kendala" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Jenis Kendala</option>
                    </select>
                </div>

                <!-- Deskripsi Kendala -->
                <div id="deskripsiKendalaSection" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Kendala
                    </label>
                    <textarea id="deskripsi_kendala" name="deskripsi_kendala" rows="3"
                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Jelaskan detail kendala yang ditemukan..."></textarea>
                </div>

                <!-- Catatan Petugas -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Petugas
                    </label>
                    <textarea id="catatan_petugas" name="catatan_petugas" rows="3"
                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Catatan tambahan..."></textarea>
                </div>

                <!-- Tindakan yang Diambil -->
                <div id="tindakanSection" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tindakan yang Diambil
                    </label>
                    <textarea id="tindakan_yang_diambil" name="tindakan_yang_diambil" rows="3"
                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Jelaskan tindakan yang sudah diambil..."></textarea>
                </div>

                <!-- Foto Lokasi -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Lokasi <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <input type="file" id="foto_lokasi" name="foto_lokasi" accept="image/*" capture="environment" class="hidden" required>
                        <button type="button" onclick="document.getElementById('foto_lokasi').click()" class="w-full">
                            <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                            <div class="text-sm text-gray-600">Ambil Foto Lokasi</div>
                        </button>
                        <div id="fotoLokasiPreview" class="mt-3 hidden">
                            <img id="fotoLokasiImg" src="" class="w-full h-32 object-cover rounded-lg">
                            <button type="button" onclick="removeFotoLokasi()" class="mt-2 text-red-600 text-sm">
                                <i class="fas fa-trash mr-1"></i>Hapus Foto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Foto Kendala -->
                <div id="fotoKendalaSection" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Kendala
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <input type="file" id="foto_kendala" name="foto_kendala" accept="image/*" capture="environment" class="hidden">
                        <button type="button" onclick="document.getElementById('foto_kendala').click()" class="w-full">
                            <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                            <div class="text-sm text-gray-600">Ambil Foto Kendala</div>
                        </button>
                        <div id="fotoKendalaPreview" class="mt-3 hidden">
                            <img id="fotoKendalaImg" src="" class="w-full h-32 object-cover rounded-lg">
                            <button type="button" onclick="removeFotoKendala()" class="mt-2 text-red-600 text-sm">
                                <i class="fas fa-trash mr-1"></i>Hapus Foto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Laporan
                </button>
            </form>
        </div>

        <!-- My Reports Button -->
        <button onclick="loadMyReports()" class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-700 transition-colors mb-4">
            <i class="fas fa-list mr-2"></i>Lihat Laporan Saya
        </button>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg text-center">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
            <div class="text-gray-700">Mengirim laporan...</div>
        </div>
    </div>

    <script>
        let currentLocation = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadProjects();
            loadJenisKendala();
            setupEventListeners();
            getCurrentLocation();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Status lokasi change
            document.querySelectorAll('input[name="status_lokasi"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleKendalaFields(this.value === 'tidak_aman');
                });
            });

            // Project change
            document.getElementById('project_id').addEventListener('change', function() {
                loadAreasByProject(this.value);
            });

            // File inputs
            document.getElementById('foto_lokasi').addEventListener('change', function() {
                previewImage(this, 'fotoLokasiPreview', 'fotoLokasiImg');
            });

            document.getElementById('foto_kendala').addEventListener('change', function() {
                previewImage(this, 'fotoKendalaPreview', 'fotoKendalaImg');
            });

            // Form submit
            document.getElementById('patroliMandiriForm').addEventListener('submit', handleSubmit);
        }

        // Toggle kendala fields
        function toggleKendalaFields(show) {
            const sections = ['jenisKendalaSection', 'deskripsiKendalaSection', 'tindakanSection', 'fotoKendalaSection'];
            sections.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (show) {
                    section.classList.remove('hidden');
                    if (sectionId === 'jenisKendalaSection') {
                        document.getElementById('jenis_kendala').required = true;
                    }
                } else {
                    section.classList.add('hidden');
                    if (sectionId === 'jenisKendalaSection') {
                        document.getElementById('jenis_kendala').required = false;
                        document.getElementById('jenis_kendala').value = '';
                    }
                }
            });
        }

        // Load projects
        async function loadProjects() {
            try {
                const response = await API.get('/patroli-mandiri-projects');
                if (response.success) {
                    const select = document.getElementById('project_id');
                    select.innerHTML = '<option value="">Pilih Project</option>';
                    response.data.forEach(project => {
                        select.innerHTML += `<option value="${project.id}">${project.nama}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading projects:', error);
            }
        }

        // Load areas by project
        async function loadAreasByProject(projectId) {
            const select = document.getElementById('area_patrol_id');
            select.innerHTML = '<option value="">Pilih Area (Opsional)</option>';
            
            if (!projectId) return;

            try {
                const response = await API.get(`/patroli-mandiri-areas/${projectId}`);
                if (response.success) {
                    response.data.forEach(area => {
                        select.innerHTML += `<option value="${area.id}">${area.nama}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading areas:', error);
            }
        }

        // Load jenis kendala
        async function loadJenisKendala() {
            try {
                const response = await API.get('/patroli-mandiri-jenis-kendala');
                if (response.success) {
                    const select = document.getElementById('jenis_kendala');
                    select.innerHTML = '<option value="">Pilih Jenis Kendala</option>';
                    response.data.forEach(item => {
                        select.innerHTML += `<option value="${item.value}">${item.label}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading jenis kendala:', error);
            }
        }

        // Get current location
        function getCurrentLocation() {
            const btn = document.getElementById('locationBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        currentLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };
                        
                        document.getElementById('latitude').value = currentLocation.latitude;
                        document.getElementById('longitude').value = currentLocation.longitude;
                        
                        btn.innerHTML = '<i class="fas fa-map-marker-alt"></i>';
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi Berhasil Diambil!',
                            text: `Lat: ${currentLocation.latitude.toFixed(6)}, Lng: ${currentLocation.longitude.toFixed(6)}`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    function(error) {
                        btn.innerHTML = '<i class="fas fa-map-marker-alt"></i>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengambil Lokasi',
                            text: 'Pastikan GPS aktif dan berikan izin akses lokasi'
                        });
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                btn.innerHTML = '<i class="fas fa-map-marker-alt"></i>';
                Swal.fire({
                    icon: 'error',
                    title: 'GPS Tidak Didukung',
                    text: 'Browser tidak mendukung geolocation'
                });
            }
        }

        // Preview image
        function previewImage(input, previewId, imgId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(imgId).src = e.target.result;
                    document.getElementById(previewId).classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Remove foto lokasi
        function removeFotoLokasi() {
            document.getElementById('foto_lokasi').value = '';
            document.getElementById('fotoLokasiPreview').classList.add('hidden');
        }

        // Remove foto kendala
        function removeFotoKendala() {
            document.getElementById('foto_kendala').value = '';
            document.getElementById('fotoKendalaPreview').classList.add('hidden');
        }

        // Handle form submit
        async function handleSubmit(e) {
            e.preventDefault();
            
            // Validate required fields
            if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lokasi Diperlukan',
                    text: 'Silakan ambil lokasi GPS terlebih dahulu'
                });
                return;
            }

            const formData = new FormData(e.target);
            
            // Show loading
            document.getElementById('loadingOverlay').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = true;

            try {
                const response = await API.post('/patroli-mandiri', formData);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reset form
                        e.target.reset();
                        document.getElementById('fotoLokasiPreview').classList.add('hidden');
                        document.getElementById('fotoKendalaPreview').classList.add('hidden');
                        toggleKendalaFields(false);
                        
                        // Clear coordinates
                        document.getElementById('latitude').value = '';
                        document.getElementById('longitude').value = '';
                    });
                } else {
                    throw new Error(response.message || 'Gagal mengirim laporan');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim Laporan',
                    text: error.message || 'Terjadi kesalahan saat mengirim laporan'
                });
            } finally {
                // Hide loading
                document.getElementById('loadingOverlay').classList.add('hidden');
                document.getElementById('submitBtn').disabled = false;
            }
        }

        // Load my reports (redirect to list page)
        function loadMyReports() {
            // This would typically redirect to a reports list page
            Swal.fire({
                icon: 'info',
                title: 'Fitur Dalam Pengembangan',
                text: 'Fitur lihat laporan akan segera tersedia'
            });
        }
    </script>
</body>
</html>