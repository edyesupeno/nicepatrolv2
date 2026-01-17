@extends('mobile.layouts.app')

@section('title', 'Kehadiran - Employee')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm px-4 py-3 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button onclick="goBack()" class="w-8 h-8 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">Kehadiran</h1>
            </div>
            <button onclick="showMonthPicker()" class="flex items-center space-x-1 px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-sm">
                <span id="currentMonth">Des 2024</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="px-4 py-4">
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-green-600" id="hadirCount">10</span>
                </div>
                <div class="text-sm text-gray-600">Hadir</div>
                <div class="text-xs text-gray-500" id="hadirHours">80 Jam 10 Menit</div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-red-600" id="terlambatCount">0</span>
                </div>
                <div class="text-sm text-gray-600">Terlambat</div>
                <div class="text-xs text-gray-500" id="terlambatHours">0 Jam 0 Menit</div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-yellow-600" id="pulangCepatCount">0</span>
                </div>
                <div class="text-sm text-gray-600">Pulang Cepat</div>
                <div class="text-xs text-gray-500" id="pulangCepatHours">0 Jam 0 Menit</div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-600" id="alpaCount">1</span>
                </div>
                <div class="text-sm text-gray-600">Tidak Hadir</div>
                <div class="text-xs text-gray-500">Alpa/Izin/Sakit</div>
            </div>
        </div>
    </div>
    
    <!-- Calendar View -->
    <div class="px-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Calendar Header -->
            <div class="bg-gray-50 px-4 py-3 border-b">
                <div class="grid grid-cols-7 gap-1">
                    <div class="text-center text-xs font-medium text-gray-500 py-2">MIN</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">SEN</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">SEL</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">RAB</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">KAM</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">JUM</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">SAB</div>
                </div>
            </div>
            
            <!-- Calendar Body -->
            <div class="p-4">
                <div class="grid grid-cols-7 gap-1" id="calendarGrid">
                    <!-- Calendar dates will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Legend -->
    <div class="px-4 py-4">
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h3 class="text-sm font-medium text-gray-800 mb-3">Keterangan</h3>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-gray-600">Hadir</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-900 rounded-full"></div>
                    <span class="text-gray-600">Terlambat</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span class="text-gray-600">Pulang Cepat</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-gray-900 rounded-full"></div>
                    <span class="text-gray-600">Terlambat & PC</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span class="text-gray-600">Tidak Hadir</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                    <span class="text-gray-600">Libur</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom spacing -->
    <div class="h-20"></div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-end justify-center min-h-screen">
        <div class="bg-white rounded-t-2xl w-full max-w-md transform transition-transform duration-300 translate-y-full" id="modalContent">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Detail Kehadiran</h3>
                    <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4" id="modalBody">
                <!-- Modal content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let scheduleData = [];
let summaryData = null;

// Load data when page loads
document.addEventListener('DOMContentLoaded', async function() {
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    await loadMonthData();
});

// Load month data
async function loadMonthData() {
    const monthStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`;
    
    try {
        // Load schedule data
        const scheduleResponse = await API.get(`/absensi/my-schedule?month=${monthStr}`);
        if (scheduleResponse.success) {
            scheduleData = scheduleResponse.data.schedules;
        }
        
        // Load summary data
        const summaryResponse = await API.get(`/absensi/summary?month=${monthStr}`);
        if (summaryResponse.success) {
            summaryData = summaryResponse.data;
            updateSummaryCards();
        }
        
        updateMonthDisplay();
        renderCalendar();
    } catch (error) {
        console.error('Error loading month data:', error);
        Swal.fire({
            title: 'Error',
            text: 'Gagal memuat data kehadiran',
            icon: 'error'
        });
    }
}

// Update summary cards
function updateSummaryCards() {
    if (!summaryData) return;
    
    const summary = summaryData.summary;
    const jamKerja = summaryData.total_jam_kerja;
    
    document.getElementById('hadirCount').textContent = summary.H || 0;
    document.getElementById('terlambatCount').textContent = summary.T || 0;
    document.getElementById('pulangCepatCount').textContent = summary.PC || 0;
    document.getElementById('alpaCount').textContent = summary.A || 0;
    
    document.getElementById('hadirHours').textContent = jamKerja.H || '0 Jam 0 Menit';
    document.getElementById('terlambatHours').textContent = jamKerja.T || '0 Jam 0 Menit';
    document.getElementById('pulangCepatHours').textContent = jamKerja.PC || '0 Jam 0 Menit';
}

// Update month display
function updateMonthDisplay() {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                   'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    document.getElementById('currentMonth').textContent = `${months[currentMonth]} ${currentYear}`;
}

// Render calendar
function renderCalendar() {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';
    
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay()); // Start from Sunday
    
    // Generate 42 days (6 weeks)
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dateStr = date.toISOString().split('T')[0];
        const isCurrentMonth = date.getMonth() === currentMonth;
        const isToday = dateStr === new Date().toISOString().split('T')[0];
        
        // Find schedule for this date
        const schedule = scheduleData.find(s => s.tanggal === dateStr);
        
        const dayElement = document.createElement('div');
        dayElement.className = `relative p-2 text-center cursor-pointer rounded-lg transition-colors ${
            isCurrentMonth ? 'text-gray-800' : 'text-gray-300'
        } ${isToday ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-50'}`;
        
        // Day number
        const dayNumber = document.createElement('div');
        dayNumber.className = 'text-sm';
        dayNumber.textContent = date.getDate();
        dayElement.appendChild(dayNumber);
        
        // Status indicator
        if (schedule && schedule.absensi && isCurrentMonth) {
            const indicator = document.createElement('div');
            indicator.className = `w-2 h-2 rounded-full mx-auto mt-1`;
            indicator.style.backgroundColor = schedule.absensi.warna;
            dayElement.appendChild(indicator);
            
            // Add click handler for detail
            dayElement.addEventListener('click', () => showDetail(schedule));
        }
        
        grid.appendChild(dayElement);
    }
}

// Show detail modal
function showDetail(schedule) {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    modalTitle.textContent = schedule.tanggal_formatted;
    
    if (schedule.absensi) {
        const absensi = schedule.absensi;
        modalBody.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" style="background-color: ${absensi.warna}20; color: ${absensi.warna}">
                        ${absensi.status_display}
                    </span>
                </div>
                ${absensi.jam_masuk ? `
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Jam Masuk</span>
                    <span class="font-medium">${absensi.jam_masuk}</span>
                </div>
                ` : ''}
                ${absensi.jam_keluar ? `
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Jam Keluar</span>
                    <span class="font-medium">${absensi.jam_keluar}</span>
                </div>
                ` : ''}
                ${absensi.keterangan ? `
                <div>
                    <div class="text-gray-600 mb-2">Keterangan</div>
                    <div class="bg-gray-50 p-3 rounded-lg text-sm">${absensi.keterangan}</div>
                </div>
                ` : ''}
            </div>
        `;
    } else {
        modalBody.innerHTML = `
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7a2 2 0 012-2h4a2 2 0 012 2v0M8 7v8a2 2 0 002 2h4a2 2 0 002-2V7M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                    </svg>
                </div>
                <div class="text-gray-500">Tidak ada data kehadiran</div>
            </div>
        `;
    }
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('translate-y-full');
    }, 10);
}

// Close modal
function closeModal() {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.classList.add('translate-y-full');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Show month picker
function showMonthPicker() {
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const currentDate = new Date();
    const options = [];
    
    // Generate last 12 months
    for (let i = 11; i >= 0; i--) {
        const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
        const value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        const label = `${months[date.getMonth()]} ${date.getFullYear()}`;
        options.push(`<option value="${value}" ${date.getMonth() === currentMonth && date.getFullYear() === currentYear ? 'selected' : ''}>${label}</option>`);
    }
    
    Swal.fire({
        title: 'Pilih Bulan',
        html: `<select id="monthSelect" class="w-full p-3 border rounded-lg">${options.join('')}</select>`,
        showCancelButton: true,
        confirmButtonText: 'Pilih',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const selected = document.getElementById('monthSelect').value;
            const [year, month] = selected.split('-');
            return { year: parseInt(year), month: parseInt(month) - 1 };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            currentYear = result.value.year;
            currentMonth = result.value.month;
            loadMonthData();
        }
    });
}

// Go back
function goBack() {
    window.location.href = '/employee/home';
}

// Close modal when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection