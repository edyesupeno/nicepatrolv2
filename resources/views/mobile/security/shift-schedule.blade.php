@extends('mobile.layouts.app')

@section('title', 'Jadwal Shift - Nice Patrol')

@section('content')
<div class="min-h-screen pb-20" style="background-color: #f5f5f5;">
    <!-- Header -->
    <div style="background-color: #0071CE;" class="px-4 pt-3 pb-4">
        <div class="flex items-center space-x-3 text-white">
            <a href="/profile" class="p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Jadwal Shift</h1>
        </div>
    </div>

    <!-- Month Navigation -->
    <div class="bg-white px-4 py-3 flex items-center justify-between shadow-sm">
        <button onclick="previousMonth()" class="p-2">
            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </button>
        <h2 id="currentMonth" class="text-base font-semibold text-gray-800">Loading...</h2>
        <button onclick="nextMonth()" class="p-2">
            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="px-4 mt-4">
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Shift</p>
                <p class="text-2xl font-bold" style="color: #0071CE;" id="totalShift">0</p>
                <p class="text-xs text-gray-500 mt-1">hari kerja</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Hari Libur</p>
                <p class="text-2xl font-bold text-gray-700" id="totalLibur">0</p>
                <p class="text-xs text-gray-500 mt-1">hari</p>
            </div>
        </div>
    </div>

    <!-- Schedule List -->
    <div class="px-4 mt-4 mb-4">
        <div id="scheduleList" class="space-y-2">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
@include('mobile.partials.bottom-nav-security')

@push('scripts')
<script>
let currentDate = new Date();
let monthSchedules = [];

// Load schedule for current month
async function loadMonthSchedule() {
    try {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const monthStr = `${year}-${month}`;
        
        // Update month display
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        document.getElementById('currentMonth').textContent = `${monthNames[currentDate.getMonth()]} ${year}`;
        
        // Get schedule from API
        const response = await API.get(`/shift/my-schedule?month=${monthStr}`);
        
        if (response.success && response.data.schedules) {
            monthSchedules = response.data.schedules;
        } else {
            monthSchedules = [];
        }
        
        // Generate all days in month
        const daysInMonth = new Date(year, currentDate.getMonth() + 1, 0).getDate();
        const allDays = [];
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, currentDate.getMonth(), day);
            const dateStr = date.toISOString().split('T')[0];
            
            // Find schedule for this day
            const schedule = monthSchedules.find(s => s.tanggal === dateStr);
            
            allDays.push({
                date: dateStr,
                day: day,
                dayName: getDayName(date.getDay()),
                dayShort: getDayShort(date.getDay()),
                shift: schedule ? schedule.shift : null
            });
        }
        
        // Calculate summary
        const totalShift = allDays.filter(d => d.shift !== null).length;
        const totalLibur = daysInMonth - totalShift;
        
        document.getElementById('totalShift').textContent = totalShift;
        document.getElementById('totalLibur').textContent = totalLibur;
        
        // Render schedule list
        renderScheduleList(allDays);
        
    } catch (error) {
        console.error('Failed to load schedule:', error);
        document.getElementById('scheduleList').innerHTML = `
            <div class="bg-white rounded-xl p-4 text-center text-gray-500">
                Gagal memuat jadwal shift
            </div>
        `;
    }
}

// Render schedule list
function renderScheduleList(days) {
    const container = document.getElementById('scheduleList');
    const today = new Date().toISOString().split('T')[0];
    
    container.innerHTML = days.map(day => {
        const isToday = day.date === today;
        
        if (day.shift) {
            // Has shift
            const jamMasuk = day.shift.jam_mulai.substring(0, 5);
            const jamKeluar = day.shift.jam_selesai.substring(0, 5);
            const shiftColor = day.shift.warna || '#0071CE'; // Use shift color or default blue
            
            return `
                <div class="bg-white rounded-xl shadow-sm overflow-hidden ${isToday ? 'ring-2 ring-blue-500' : ''}">
                    <div class="flex items-center">
                        <!-- Date Badge -->
                        <div class="w-16 flex-shrink-0 py-3 text-center text-white font-semibold" style="background-color: ${shiftColor};">
                            <div class="text-xs">${day.dayShort}</div>
                            <div class="text-lg">${day.day}</div>
                        </div>
                        
                        <!-- Shift Info -->
                        <div class="flex-1 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">${day.dayName}</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-1">${jamMasuk} - ${jamKeluar}</p>
                                </div>
                                <div class="px-3 py-1 rounded-full text-xs font-semibold text-white" style="background-color: ${shiftColor};">
                                    ${day.shift.kode_shift}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // No shift (Libur)
            return `
                <div class="bg-white rounded-xl shadow-sm overflow-hidden ${isToday ? 'ring-2 ring-blue-500' : ''}">
                    <div class="flex items-center">
                        <!-- Date Badge -->
                        <div class="w-16 flex-shrink-0 py-3 text-center text-white font-semibold bg-gray-400">
                            <div class="text-xs">${day.dayShort}</div>
                            <div class="text-lg">${day.day}</div>
                        </div>
                        
                        <!-- No Shift Info -->
                        <div class="flex-1 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">${day.dayName}</p>
                                    <p class="text-sm font-semibold text-gray-500 mt-1">Tidak ada shift</p>
                                </div>
                                <div class="px-3 py-1 bg-gray-100 rounded-full text-xs font-semibold text-gray-500">
                                    Libur
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }).join('');
}

// Helper functions
function getDayShort(dayOfWeek) {
    const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
    return days[dayOfWeek];
}

function getDayName(dayOfWeek) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    return days[dayOfWeek];
}

// Month navigation
function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    loadMonthSchedule();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    loadMonthSchedule();
}

// Check authentication and load
if (!API.isAuthenticated()) {
    window.location.href = '/login';
} else {
    loadMonthSchedule();
}
</script>
@endpush
@endsection
