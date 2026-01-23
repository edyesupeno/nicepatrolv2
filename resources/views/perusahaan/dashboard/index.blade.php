@extends('perusahaan.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di panel admin ' . auth()->user()->perusahaan->nama)

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
                    <p class="text-lg opacity-90">{{ auth()->user()->perusahaan->nama }}</p>
                    <p class="text-sm opacity-75 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-32 h-32 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative elements -->
        <div class="absolute -top-4 -right-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white bg-opacity-5 rounded-full"></div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Patroli -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Patroli</p>
                <p class="text-4xl font-bold" id="total-patroli">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-16 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-shield-alt text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Patroli Hari Ini -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Patroli Hari Ini</p>
                <p class="text-4xl font-bold" id="patroli-hari-ini">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-12 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-calendar-day text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Patroli Berlangsung -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Sedang Berlangsung</p>
                <p class="text-4xl font-bold" id="patroli-berlangsung">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-running text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Karyawan -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Karyawan</p>
                <p class="text-4xl font-bold" id="total-karyawan">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-12 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-users text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Kehadiran Hari Ini -->
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Kehadiran Hari Ini</p>
                <p class="text-4xl font-bold" id="kehadiran-hari-ini">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-12 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-user-check text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Cuti Pending -->
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Cuti Pending</p>
                <p class="text-4xl font-bold" id="cuti-pending">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-calendar-times text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Projects -->
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Projects</p>
                <p class="text-4xl font-bold" id="total-projects">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-project-diagram text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Checkpoint -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">Total Checkpoint</p>
                <p class="text-4xl font-bold" id="total-checkpoint">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-12 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <i class="fas fa-map-marker-alt text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Patrol Trend Chart -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Tren Patroli (7 Hari Terakhir)</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Patroli</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div style="position: relative; height: 300px;">
                <canvas id="patrolChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Attendance Chart -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Kehadiran (7 Hari Terakhir)</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Kehadiran</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div style="position: relative; height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Today's Attendance Summary -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Ringkasan Kehadiran Hari Ini</h3>
        </div>
        <div class="p-6">
            <div id="attendance-summary">
                <!-- Loading skeleton -->
                <div class="space-y-4">
                    <div class="animate-pulse bg-gray-200 h-4 rounded"></div>
                    <div class="animate-pulse bg-gray-200 h-4 rounded w-3/4"></div>
                    <div class="animate-pulse bg-gray-200 h-4 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Distribution -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Distribusi Karyawan per Project</h3>
        </div>
        <div class="p-6">
            <div style="position: relative; height: 300px;">
                <canvas id="projectChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6">
            <div id="recent-activities" class="space-y-3">
                <!-- Loading skeleton -->
                <div class="flex items-center space-x-3">
                    <div class="animate-pulse bg-gray-200 w-10 h-10 rounded-full"></div>
                    <div class="flex-1">
                        <div class="animate-pulse bg-gray-200 h-4 rounded mb-2"></div>
                        <div class="animate-pulse bg-gray-200 h-3 rounded w-2/3"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="animate-pulse bg-gray-200 w-10 h-10 rounded-full"></div>
                    <div class="flex-1">
                        <div class="animate-pulse bg-gray-200 h-4 rounded mb-2"></div>
                        <div class="animate-pulse bg-gray-200 h-3 rounded w-2/3"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="animate-pulse bg-gray-200 w-10 h-10 rounded-full"></div>
                    <div class="flex-1">
                        <div class="animate-pulse bg-gray-200 h-4 rounded mb-2"></div>
                        <div class="animate-pulse bg-gray-200 h-3 rounded w-2/3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loading...');
    
    // Initialize charts
    let patrolChart, attendanceChart, projectChart;
    
    // Load dashboard data
    loadDashboardStats();
    loadPatrolChart();
    loadAttendanceChart();
    loadProjectChart();
    loadRecentActivities();
    loadTodayAttendanceSummary();
    
    // Refresh data every 5 minutes
    setInterval(() => {
        loadDashboardStats();
        loadRecentActivities();
        loadTodayAttendanceSummary();
    }, 300000);
    
    async function loadDashboardStats() {
        try {
            console.log('Loading dashboard stats...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.stats") }}');
            const stats = await response.json();
            console.log('Stats loaded:', stats);
            
            // Update stat cards with animation
            updateStatCard('total-patroli', stats.total_patroli);
            updateStatCard('patroli-hari-ini', stats.patroli_hari_ini);
            updateStatCard('patroli-berlangsung', stats.patroli_berlangsung);
            updateStatCard('total-karyawan', stats.total_karyawan);
            updateStatCard('kehadiran-hari-ini', stats.kehadiran_hari_ini);
            updateStatCard('cuti-pending', stats.cuti_pending);
            updateStatCard('total-projects', stats.total_projects);
            updateStatCard('total-checkpoint', stats.total_checkpoint);
            
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        }
    }
    
    function updateStatCard(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            // Add animation
            element.style.transform = 'scale(1.1)';
            element.innerHTML = value.toLocaleString();
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
        }
    }
    
    async function loadPatrolChart() {
        try {
            console.log('Loading patrol chart...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.patrol-chart") }}');
            const data = await response.json();
            console.log('Patrol chart data:', data);
            
            const ctx = document.getElementById('patrolChart').getContext('2d');
            patrolChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Patroli',
                        data: data.data,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading patrol chart:', error);
        }
    }
    
    async function loadAttendanceChart() {
        try {
            console.log('Loading attendance chart...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.attendance-chart") }}');
            const data = await response.json();
            console.log('Attendance chart data:', data);
            
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            attendanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Kehadiran',
                        data: data.data,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: '#22C55E',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading attendance chart:', error);
        }
    }
    
    async function loadProjectChart() {
        try {
            console.log('Loading project chart...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.project-chart") }}');
            const data = await response.json();
            console.log('Project chart data:', data);
            
            if (data.length === 0) {
                document.getElementById('projectChart').parentElement.innerHTML = 
                    '<div class="flex items-center justify-center h-full text-gray-500"><p>Belum ada data project</p></div>';
                return;
            }
            
            const ctx = document.getElementById('projectChart').getContext('2d');
            projectChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        data: data.map(item => item.value),
                        backgroundColor: [
                            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', 
                            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading project chart:', error);
        }
    }
    
    async function loadRecentActivities() {
        try {
            console.log('Loading recent activities...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.recent-activities") }}');
            const activities = await response.json();
            console.log('Recent activities:', activities);
            
            const container = document.getElementById('recent-activities');
            
            if (activities.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>Belum ada aktivitas</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = activities.map(activity => `
                <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-${activity.color}-100 rounded-full flex items-center justify-center">
                        <i class="${activity.icon} text-${activity.color}-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 text-sm">${activity.title}</p>
                        <p class="text-xs text-gray-500">${activity.user} • ${activity.time}</p>
                    </div>
                    <div class="text-xs">
                        <span class="px-2 py-1 bg-${activity.color}-100 text-${activity.color}-700 rounded-full">
                            ${activity.status}
                        </span>
                    </div>
                </div>
            `).join('');
            
        } catch (error) {
            console.error('Error loading recent activities:', error);
        }
    }
    
    async function loadTodayAttendanceSummary() {
        try {
            console.log('Loading attendance summary...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.today-attendance-summary") }}');
            const data = await response.json();
            console.log('Attendance summary:', data);
            
            const container = document.getElementById('attendance-summary');
            container.innerHTML = `
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">${data.percentage}%</div>
                        <div class="text-sm text-gray-600">Tingkat Kehadiran</div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Hadir</span>
                            <span class="font-semibold text-green-600">${data.hadir}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Terlambat</span>
                            <span class="font-semibold text-yellow-600">${data.terlambat}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Alpha</span>
                            <span class="font-semibold text-red-600">${data.alpha}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between items-center font-semibold">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">${data.total}</span>
                        </div>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-500" 
                             style="width: ${data.percentage}%"></div>
                    </div>
                </div>
            `;
            
        } catch (error) {
            console.error('Error loading attendance summary:', error);
        }
    }
});
</script>
@endpush
