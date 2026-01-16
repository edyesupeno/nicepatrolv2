@extends('mobile.layouts.app')

@section('title', 'Profile - Nice Patrol')

@section('content')
<div class="min-h-screen pb-20" style="background-color: #f5f5f5;">
    <!-- Header with Blue Background -->
    <div style="background-color: #0071CE; padding-bottom: 30px;">
        <!-- Top Bar -->
        <div class="flex items-center justify-between px-4 pt-3 pb-2 text-white">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
                <span class="text-lg font-semibold">Profil</span>
            </div>
            <div class="px-3 py-1 bg-green-500 rounded-full text-xs font-semibold flex items-center space-x-1">
                <span class="w-2 h-2 bg-white rounded-full"></span>
                <span id="statusBadge">Active</span>
            </div>
        </div>
    </div>

    <!-- Profile Card (Overlapping) -->
    <div class="px-4" style="margin-top: -20px;">
        <div class="bg-white rounded-3xl shadow-lg p-5 text-center">
            <!-- Profile Photo -->
            <div class="flex justify-center mb-2">
                <div class="relative">
                    <div class="w-24 h-24 bg-gray-200 rounded-full overflow-hidden border-4 border-white shadow-md">
                        <img id="profilePhoto" src="https://ui-avatars.com/api/?name=User&background=0071CE&color=fff&size=96" alt="Profile" class="w-full h-full object-cover">
                    </div>
                    <!-- Camera Icon Button -->
                    <button onclick="changeProfilePhoto()" class="absolute bottom-0 right-0 w-8 h-8 rounded-full flex items-center justify-center shadow-lg" style="background-color: #0071CE;">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2 0-1.77-1.43-3.2-3.2-3.2-1.77 0-3.2 1.43-3.2 3.2 0 1.77 1.43 3.2 3.2 3.2zm0-5.2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/>
                            <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                        </svg>
                    </button>
                    <!-- Hidden File Input -->
                    <input type="file" id="photoInput" accept="image/*" class="hidden" onchange="handlePhotoUpload(event)">
                </div>
            </div>
            
            <!-- Name -->
            <h2 id="profileName" class="text-xl font-bold text-gray-800 mb-1">Jacob Jones</h2>
            
            <!-- Role & Location -->
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-600 mb-1">
                <span id="profileRole">Project Manager</span>
                <span>|</span>
                <span id="profileLocation">Pekanbaru, Riau</span>
            </div>
            
            <!-- Rating -->
            <div class="flex items-center justify-center space-x-1 text-sm">
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <span class="font-semibold text-gray-800" id="profileRating">4.8</span>
                <span class="text-gray-500" id="profileReviews">Lihat Ulasan</span>
            </div>
        </div>
    </div>

    <!-- Shift Section -->
    <div class="px-4 mt-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-800">Shift</h3>
            <a href="/security/shift-schedule" class="text-sm font-semibold" style="color: #0071CE;">Lihat Semua</a>
        </div>
        
        <!-- Days -->
        <div id="shiftDays" class="flex space-x-2 mb-3 overflow-x-auto pb-2">
            <!-- Will be populated by JavaScript -->
        </div>
        
        <!-- Date -->
        <p class="text-sm text-gray-600 mb-2" id="shiftDate">Loading...</p>
        
        <!-- Shift Times -->
        <div id="shiftTimes" class="flex space-x-2">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>

    <!-- Absensi Section -->
    <div class="px-4 mt-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-800">Absensi</h3>
            <a href="/security/absensi-schedule" class="text-sm font-semibold" style="color: #0071CE;">Lihat Semua</a>
        </div>
        
        <div class="flex space-x-3 overflow-x-auto pb-2">
            <!-- Hadir -->
            <div onclick="showAbsensiDetail('Hadir', 'H', document.getElementById('absenHadir').textContent, '10 Jam 10 Menit')" class="flex-shrink-0 w-32 bg-green-50 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-green-800 mb-1" id="absenHadir">0</p>
                <p class="text-xs text-green-800 font-bold">H</p>
            </div>
            
            <!-- Terlambat -->
            <div onclick="showAbsensiDetail('Terlambat', 'T', document.getElementById('absenTerlambat').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-red-900 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                <div class="w-10 h-10 bg-red-700 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-white mb-1" id="absenTerlambat">0</p>
                <p class="text-xs text-white font-bold">T</p>
            </div>
            
            <!-- Pulang Cepat -->
            <div onclick="showAbsensiDetail('Pulang Cepat', 'PC', document.getElementById('absenPulangCepat').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-yellow-50 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.9l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-yellow-800 mb-1" id="absenPulangCepat">0</p>
                <p class="text-xs text-yellow-800 font-bold">PC</p>
            </div>
            
            <!-- Terlambat & Pulang Cepat -->
            <div onclick="showAbsensiDetail('Terlambat & Pulang Cepat', 'TPC', document.getElementById('absenTPC').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-gray-900 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-white mb-1" id="absenTPC">0</p>
                <p class="text-xs text-white font-bold">TPC</p>
            </div>
            
            <!-- Alpa -->
            <div onclick="showAbsensiDetail('Alpa', 'A', document.getElementById('absenAlpa').textContent, '0 Jam 0 Menit')" class="flex-shrink-0 w-32 bg-red-50 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition">
                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-red-800 mb-1" id="absenAlpa">0</p>
                <p class="text-xs text-red-800 font-bold">A</p>
            </div>
        </div>
    </div>

    <!-- Biodata Section -->
    <div class="px-4 mt-6">
        <h3 class="text-base font-bold text-gray-800 mb-3">Biodata</h3>
        
        <div class="space-y-2">
            <!-- Work Experience -->
            <div class="bg-white rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Work Experience</p>
                <div class="flex items-center space-x-2 text-gray-800">
                    <svg class="w-5 h-5" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                    </svg>
                    <span class="font-semibold" id="workExperience">2 Year</span>
                </div>
            </div>
            
            <!-- Area -->
            <div class="bg-white rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Area</p>
                <div class="flex items-center space-x-2 text-gray-800">
                    <svg class="w-5 h-5" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="font-semibold" id="area">Fatmawati, Jakarta Selatan</span>
                </div>
            </div>
            
            <!-- Phone Number -->
            <div class="bg-white rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Phone Number</p>
                <div class="flex items-center space-x-2 text-gray-800">
                    <svg class="w-5 h-5" style="color: #0071CE;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                    </svg>
                    <span class="font-semibold" id="phoneNumber">+6285776511193</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="px-4 mt-6 mb-6">
        <button onclick="handleLogout()" class="w-full py-4 bg-red-50 text-red-600 rounded-xl font-semibold flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
            </svg>
            <span>Keluar Akun</span>
        </button>
    </div>
</div>

<!-- Bottom Navigation -->
@include('mobile.partials.bottom-nav-security')

@push('scripts')
<script>
// Load user profile data
async function loadProfileData() {
    try {
        const response = await API.get('/user');
        
        if (response.success) {
            const user = response.data;
            
            // Update profile info
            document.getElementById('profileName').textContent = user.name;
            document.getElementById('profileRole').textContent = user.role_display || 'Security Officer';
            
            // Set foto
            const profilePhoto = document.getElementById('profilePhoto');
            if (user.foto) {
                profilePhoto.src = user.foto;
                profilePhoto.onerror = function() {
                    this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0071CE&color=fff&size=96`;
                };
            } else {
                profilePhoto.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0071CE&color=fff&size=96`;
            }
        }
    } catch (error) {
        console.error('Failed to load profile data:', error);
    }
}

// Load absensi summary
async function loadAbsensiSummary() {
    try {
        const response = await API.get('/absensi/summary');
        
        if (response.success) {
            const summary = response.data.summary;
            const totalJamKerja = response.data.total_jam_kerja;
            
            // Update absensi cards
            document.getElementById('absenHadir').textContent = summary.H || 0;
            document.getElementById('absenTerlambat').textContent = summary.T || 0;
            document.getElementById('absenPulangCepat').textContent = summary.PC || 0;
            document.getElementById('absenTPC').textContent = summary.TPC || 0;
            document.getElementById('absenAlpa').textContent = summary.A || 0;
            
            // Store total jam kerja for popup
            window.absensiJamKerja = totalJamKerja;
        }
    } catch (error) {
        console.error('Failed to load absensi summary:', error);
    }
}

// Load shift schedule
let selectedDate = new Date();
let weekSchedules = [];

async function loadShiftSchedule() {
    try {
        // Calculate date range: 3 days before today, today, 3 days after today
        const today = new Date();
        const startDate = new Date(today);
        startDate.setDate(today.getDate() - 3);
        
        // Create 7 days array first (always show 7 days)
        weekSchedules = [];
        for (let i = 0; i < 7; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            weekSchedules.push({
                tanggal: date.toISOString().split('T')[0],
                tanggal_formatted: formatDateIndonesia(date),
                day_short: getDayShort(date.getDay()),
                day_number: date.getDate(),
                shift: null
            });
        }
        
        // Try to get schedule from API
        try {
            const response = await API.get(`/shift/my-schedule?date=${startDate.toISOString().split('T')[0]}&week=true`);
            
            if (response.success && response.data.schedules && response.data.schedules.length > 0) {
                // Merge API data with our 7 days array
                response.data.schedules.forEach(apiSchedule => {
                    const index = weekSchedules.findIndex(s => s.tanggal === apiSchedule.tanggal);
                    if (index !== -1) {
                        weekSchedules[index] = apiSchedule;
                    }
                });
            }
        } catch (apiError) {
            console.log('API error, showing empty schedule:', apiError);
        }
        
        // Always render days (even if no shift data)
        renderShiftDays();
        
        // Select today by default
        const todayStr = today.toISOString().split('T')[0];
        selectDate(todayStr);
        
        // Scroll to today
        scrollToToday();
        
    } catch (error) {
        console.error('Failed to load shift schedule:', error);
        document.getElementById('shiftDate').textContent = 'Gagal memuat jadwal shift';
    }
}

// Helper function to get day short name
function getDayShort(dayOfWeek) {
    const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
    return days[dayOfWeek];
}

// Helper function to format date in Indonesian
function formatDateIndonesia(date) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[date.getDay()];
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    
    return `${dayName}, ${day} ${month} ${year}`;
}

// Scroll to today's date
function scrollToToday() {
    setTimeout(() => {
        const container = document.getElementById('shiftDays');
        const todayButton = container.querySelector('[data-today="true"]');
        if (todayButton) {
            // Scroll to center the today button
            const containerWidth = container.offsetWidth;
            const buttonLeft = todayButton.offsetLeft;
            const buttonWidth = todayButton.offsetWidth;
            const scrollPosition = buttonLeft - (containerWidth / 2) + (buttonWidth / 2);
            container.scrollTo({ left: scrollPosition, behavior: 'smooth' });
        }
    }, 100);
}

// Render shift days
function renderShiftDays() {
    const container = document.getElementById('shiftDays');
    const today = new Date().toISOString().split('T')[0];
    
    container.innerHTML = weekSchedules.map(schedule => {
        const isToday = schedule.tanggal === today;
        
        let classes = 'flex-shrink-0 flex flex-col items-center justify-center w-14 h-16 rounded-xl text-xs cursor-pointer transition-all';
        let style = '';
        let dataAttr = isToday ? 'data-today="true"' : '';
        
        if (isToday) {
            // Today - blue background
            classes += ' border-2';
            style = 'background-color: #0071CE; border-color: #0071CE; color: white;';
        } else {
            // Other days - white background
            classes += ' bg-white hover:bg-gray-50';
        }
        
        return `
            <button class="${classes}" style="${style}" ${dataAttr} onclick="selectDate('${schedule.tanggal}')">
                <span class="${isToday ? '' : 'text-gray-500'} mb-1">${schedule.day_short}</span>
                <span class="font-semibold ${isToday ? '' : 'text-gray-800'}">${schedule.day_number}</span>
            </button>
        `;
    }).join('');
}

// Select date and show shift times
function selectDate(date) {
    selectedDate = new Date(date);
    let schedule = weekSchedules.find(s => s.tanggal === date);
    
    // If schedule not found, create empty one
    if (!schedule) {
        const dateObj = new Date(date);
        schedule = {
            tanggal: date,
            tanggal_formatted: formatDateIndonesia(dateObj),
            shift: null
        };
    }
    
    // Update date text
    document.getElementById('shiftDate').textContent = schedule.tanggal_formatted;
    
    // Render shift times
    renderShiftTimes(schedule);
}

// Render shift times
function renderShiftTimes(schedule) {
    const container = document.getElementById('shiftTimes');
    
    if (!schedule.shift) {
        // No shift - show full blue card with white text
        container.innerHTML = `
            <div class="w-full py-4 rounded-xl text-sm font-semibold text-white text-center" style="background-color: #0071CE;">
                Tidak ada shift pada hari ini
            </div>
        `;
        return;
    }
    
    // Format time to HH:MM (remove seconds)
    const jamMasuk = schedule.shift.jam_mulai.substring(0, 5); // Get HH:MM only
    const jamKeluar = schedule.shift.jam_selesai.substring(0, 5); // Get HH:MM only
    
    // Show 2 cards: jam masuk (left) and jam keluar (right)
    container.innerHTML = `
        <button class="flex-1 py-3 bg-white rounded-xl text-sm font-semibold border border-gray-200 text-gray-700">
            ${jamMasuk} - ${jamKeluar}
        </button>
        <button class="flex-1 py-3 rounded-xl text-sm font-semibold border-2" style="background-color: #0071CE; border-color: #0071CE; color: white;">
            ${jamMasuk} - ${jamKeluar}
        </button>
    `;
}

// Handle logout
async function handleLogout() {
    const result = await Swal.fire({
        title: 'Keluar Akun?',
        text: 'Anda akan keluar dari aplikasi',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal'
    });
    
    if (result.isConfirmed) {
        // Show loading
        Swal.fire({
            title: 'Logging out...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        try {
            // Call logout API
            const response = await API.post('/logout');
            console.log('Logout response:', response);
            
            if (response.success) {
                // Clear local storage and redirect
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Logout',
                    text: 'Anda akan diarahkan ke halaman login',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/login';
                });
            } else {
                throw new Error(response.message || 'Logout gagal');
            }
        } catch (error) {
            console.error('Logout error:', error);
            
            // Even if API fails, still logout locally
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            
            Swal.fire({
                icon: 'info',
                title: 'Logout',
                text: 'Anda akan diarahkan ke halaman login',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/login';
            });
        }
    }
}

// Show absensi detail popup
function showAbsensiDetail(nama, singkatan, jumlah, totalJam) {
    // Get total jam kerja from API data if available
    if (window.absensiJamKerja && window.absensiJamKerja[singkatan]) {
        totalJam = window.absensiJamKerja[singkatan];
    }
    
    Swal.fire({
        title: `<strong>${nama}</strong>`,
        html: `
            <div class="text-left">
                <p class="text-gray-600 mb-2">Singkatan: <strong>${singkatan}</strong></p>
                <p class="text-gray-600 mb-2">Jumlah: <strong>${jumlah} hari</strong></p>
                <p class="text-gray-600">Total Jam Kerja: <strong>${totalJam}</strong></p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#0071CE'
    });
}

// Change profile photo
function changeProfilePhoto() {
    document.getElementById('photoInput').click();
}

// Handle photo upload
async function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        Swal.fire({
            icon: 'error',
            title: 'File Tidak Valid',
            text: 'Harap pilih file gambar (JPG, PNG, dll)',
            confirmButtonColor: '#0071CE'
        });
        return;
    }
    
    // Validate file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 10MB',
            confirmButtonColor: '#0071CE'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Mengupload foto...',
        text: 'Mohon tunggu, foto sedang dikompres',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        // Create FormData
        const formData = new FormData();
        formData.append('foto', file);
        
        // Upload to API
        const response = await fetch(`${API_BASE_URL}/user/upload-photo`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${API.getToken()}`,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update photo preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePhoto').src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            // Update localStorage
            const user = API.getUser();
            if (user) {
                user.foto = result.data.foto_url;
                localStorage.setItem('user', JSON.stringify(user));
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Foto profil berhasil diupdate dan dikompres',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            throw new Error(result.message || 'Upload gagal');
        }
    } catch (error) {
        console.error('Upload error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Upload Gagal',
            text: error.message || 'Terjadi kesalahan saat mengupload foto',
            confirmButtonColor: '#0071CE'
        });
    }
}

// Check authentication
if (!API.isAuthenticated()) {
    window.location.href = '/login';
} else {
    loadProfileData();
    loadAbsensiSummary();
    loadShiftSchedule();
}
</script>
@endpush
@endsection
