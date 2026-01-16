@extends('mobile.layouts.app')

@section('title', 'Rekap Absensi - Nice Patrol')

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
            <h1 class="text-lg font-semibold">Rekap Absensi</h1>
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

    <!-- Summary Cards - Horizontal Scrollable -->
    <div class="px-4 mt-4">
        <div class="flex space-x-2 overflow-x-auto pb-2">
            <!-- Hadir -->
            <div class="flex-shrink-0 w-20 bg-green-50 rounded-xl p-3 shadow-sm text-center">
                <p class="text-xs text-green-700 mb-1 font-semibold">H</p>
                <p class="text-2xl font-bold text-green-800" id="totalHadir">0</p>
            </div>
            
            <!-- Terlambat -->
            <div class="flex-shrink-0 w-20 bg-red-900 rounded-xl p-3 shadow-sm text-center">
                <p class="text-xs text-white mb-1 font-semibold">T</p>
                <p class="text-2xl font-bold text-white" id="totalTerlambat">0</p>
            </div>
            
            <!-- Pulang Cepat -->
            <div class="flex-shrink-0 w-20 bg-yellow-50 rounded-xl p-3 shadow-sm text-center">
                <p class="text-xs text-yellow-700 mb-1 font-semibold">PC</p>
                <p class="text-2xl font-bold text-yellow-800" id="totalPC">0</p>
            </div>
            
            <!-- TPC -->
            <div class="flex-shrink-0 w-20 bg-gray-900 rounded-xl p-3 shadow-sm text-center">
                <p class="text-xs text-white mb-1 font-semibold">TPC</p>
                <p class="text-2xl font-bold text-white" id="totalTPC">0</p>
            </div>
            
            <!-- Alpa -->
            <div class="flex-shrink-0 w-20 bg-red-50 rounded-xl p-3 shadow-sm text-center">
                <p class="text-xs text-red-700 mb-1 font-semibold">A</p>
                <p class="text-2xl font-bold text-red-800" id="totalAlpa">0</p>
            </div>
        </div>
    </div>

    <!-- Absensi List -->
    <div class="px-4 mt-4 mb-4">
        <div id="absensiList" class="space-y-2">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
@include('mobile.partials.bottom-nav-security')

@push('scripts')
<script>
let currentDate = new Date();
let monthAbsensis = [];

// Load absensi for current month
async function loadMonthAbsensi() {
    try {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const monthStr = `${year}-${month}`;
        
        // Update month display
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        document.getElementById('currentMonth').textContent = `${monthNames[currentDate.getMonth()]} ${year}`;
        
        // Get absensi from API
        const response = await API.get(`/absensi/my-schedule?month=${monthStr}`);
        
        if (response.success && response.data.schedules) {
            monthAbsensis = response.data.schedules;
        } else {
            monthAbsensis = [];
        }
        
        // Calculate summary
        let summary = {
            H: 0,
            T: 0,
            PC: 0,
            TPC: 0,
            A: 0
        };
        
        monthAbsensis.forEach(day => {
            if (day.absensi) {
                const status = day.absensi.status;
                if (summary.hasOwnProperty(status)) {
                    summary[status]++;
                }
            }
        });
        
        // Update summary cards
        document.getElementById('totalHadir').textContent = summary.H;
        document.getElementById('totalAlpa').textContent = summary.A;
        document.getElementById('totalPC').textContent = summary.PC;
        document.getElementById('totalTerlambat').textContent = summary.T;
        document.getElementById('totalTPC').textContent = summary.TPC;
        
        // Render absensi list
        renderAbsensiList(monthAbsensis);
        
    } catch (error) {
        console.error('Failed to load absensi:', error);
        document.getElementById('absensiList').innerHTML = `
            <div class="bg-white rounded-xl p-4 text-center text-gray-500">
                Gagal memuat rekap absensi
            </div>
        `;
    }
}

// Render absensi list
function renderAbsensiList(days) {
    const container = document.getElementById('absensiList');
    const today = new Date().toISOString().split('T')[0];
    
    container.innerHTML = days.map(day => {
        const isToday = day.tanggal === today;
        
        if (day.absensi) {
            // Has absensi record
            const absensi = day.absensi;
            const jamMasuk = absensi.jam_masuk ? absensi.jam_masuk.substring(0, 5) : '-';
            const jamKeluar = absensi.jam_keluar ? absensi.jam_keluar.substring(0, 5) : '-';
            const statusColor = absensi.warna;
            
            return `
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden ${isToday ? 'ring-2 ring-blue-500' : ''}">
                    <div class="flex items-center">
                        <!-- Date Badge -->
                        <div class="w-24 flex-shrink-0 py-4 text-center text-white font-semibold" style="background-color: ${statusColor};">
                            <div class="text-xs font-bold mb-1">${day.day_short}</div>
                            <div class="text-3xl font-bold">${day.day_number}</div>
                        </div>
                        
                        <!-- Absensi Info -->
                        <div class="flex-1 px-4 py-3">
                            <p class="text-sm text-gray-600 mb-1">${day.day_name}</p>
                            <p class="text-base font-semibold text-gray-800">${jamMasuk} - ${jamKeluar}</p>
                            ${absensi.keterangan ? `<p class="text-xs text-gray-500 mt-1">${absensi.keterangan}</p>` : ''}
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="px-4">
                            <div class="px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: ${statusColor};">
                                ${absensi.status}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // No absensi record (belum absen atau libur)
            return `
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden ${isToday ? 'ring-2 ring-blue-500' : ''}">
                    <div class="flex items-center">
                        <!-- Date Badge -->
                        <div class="w-24 flex-shrink-0 py-4 text-center text-white font-semibold bg-gray-400">
                            <div class="text-xs font-bold mb-1">${day.day_short}</div>
                            <div class="text-3xl font-bold">${day.day_number}</div>
                        </div>
                        
                        <!-- No Absensi Info -->
                        <div class="flex-1 px-4 py-3">
                            <p class="text-sm text-gray-600 mb-1">${day.day_name}</p>
                            <p class="text-base font-semibold text-gray-500">Belum ada data</p>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="px-4">
                            <div class="px-4 py-2 bg-gray-100 rounded-full text-sm font-bold text-gray-500">
                                -
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }).join('');
}

// Month navigation
function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    loadMonthAbsensi();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    loadMonthAbsensi();
}

// Check authentication and load
if (!API.isAuthenticated()) {
    window.location.href = '/login';
} else {
    loadMonthAbsensi();
}
</script>
@endpush
@endsection
