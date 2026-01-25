@extends('perusahaan.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome Back!')

@section('content')
<!-- Tab Navigation with Date -->
<div class="flex items-center justify-between mb-6">
    <div class="flex space-x-2 bg-gray-100 p-2 rounded-lg">
        <button class="tab-btn active px-6 py-3 rounded-md text-base font-medium transition-colors" data-tab="hr">
            <i class="fas fa-users mr-2"></i>Human Resource
        </button>
        <button class="tab-btn px-6 py-3 rounded-md text-base font-medium transition-colors" data-tab="patrol">
            <i class="fas fa-shield-alt mr-2"></i>Patrol
        </button>
        <button class="tab-btn px-6 py-3 rounded-md text-base font-medium transition-colors" data-tab="payroll">
            <i class="fas fa-money-bill-wave mr-2"></i>Payroll
        </button>
    </div>
    
    <div class="text-right">
        <p class="text-sm text-gray-500 mb-2">{{ now()->format('l, d F Y') }}</p>
        
        <!-- Overall Dropdown -->
        <div class="relative">
            <button id="project-dropdown-btn" class="flex items-center justify-between w-64 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span id="selected-project">Overall</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 ml-2" id="dropdown-arrow"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="project-dropdown" class="hidden absolute top-full right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                <div class="py-2">
                    <button class="project-option w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:bg-gray-50" data-value="all">
                        Overall
                    </button>
                    @foreach($projects as $project)
                    <button class="project-option w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:bg-gray-50" data-value="{{ $project->id }}">
                        {{ $project->nama }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
    <!-- Total Employee -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-users text-lg mr-2"></i>
                    <span class="text-sm opacity-90">TOTAL KARYAWAN</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="total-karyawan">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-12 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Present -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-user-check text-lg mr-2"></i>
                    <span class="text-sm opacity-90">HADIR</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="kehadiran-hari-ini">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-12 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <i class="fas fa-user-check text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Absent -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-user-times text-lg mr-2"></i>
                    <span class="text-sm opacity-90">ALPHA</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="absent-count">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <i class="fas fa-user-times text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Early Tap Out -->
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-clock text-lg mr-2"></i>
                    <span class="text-sm opacity-90">PULANG CEPAT</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="early-tapout">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <i class="fas fa-clock text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Overtime -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-business-time text-lg mr-2"></i>
                    <span class="text-sm opacity-90">LEMBUR</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="overtime-count">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <i class="fas fa-business-time text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Leave -->
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-calendar-times text-lg mr-2"></i>
                    <span class="text-sm opacity-90">CUTI</span>
                </div>
                <p class="text-4xl font-bold mb-2" id="leave-count">
                    <span class="animate-pulse bg-white bg-opacity-30 rounded w-8 h-8 inline-block"></span>
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <i class="fas fa-calendar-times text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Employee Statistics Chart -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Karyawan Per-Divisi</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Laki-Laki</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-pink-400 rounded-full"></div>
                        <span class="text-sm text-gray-600">Perempuan</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div style="position: relative; height: 300px;">
                <canvas id="employeeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Employee Age Statistics -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Umur Karyawan</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Laki-Laki</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-pink-400 rounded-full"></div>
                        <span class="text-sm text-gray-600">Perempuan</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div style="position: relative; height: 300px;">
                <canvas id="ageChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Employee Statistics -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Jadwal Karyawan</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Duty</span>
                    <i class="fas fa-sync-alt text-gray-400 cursor-pointer"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="relative flex items-center justify-center min-h-[280px]">
                <!-- Chart Container -->
                <div class="relative w-48 h-48">
                    <canvas id="dutyChart"></canvas>
                    
                    <!-- Center Text -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Total Employee</div>
                            <div class="text-4xl font-bold text-gray-900" id="total-employee-duty">23</div>
                        </div>
                    </div>
                    
                    <!-- Off Duty Label with Line (Top Left) -->
                    <div class="absolute -top-8 -left-20">
                        <div class="text-left">
                            <div class="text-sm font-medium text-gray-700">Off Duty</div>
                            <div class="text-lg font-bold text-gray-900">
                                <span id="off-duty-count">10</span>
                                <span class="text-sm text-gray-500 ml-1" id="off-duty-percentage">43.48%</span>
                            </div>
                        </div>
                        <!-- Line pointing to chart - positioned to align with left edge of "Off Duty" text -->
                        <div class="absolute top-6 left-0 w-16 h-px bg-gray-400"></div>
                    </div>
                    
                    <!-- On Duty Label with Line (Bottom Right) -->
                    <div class="absolute -bottom-8 -right-20">
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-700">On Duty</div>
                            <div class="text-lg font-bold text-gray-900">
                                <span id="on-duty-count">13</span>
                                <span class="text-sm text-blue-500 ml-1" id="on-duty-percentage">56.52%</span>
                            </div>
                        </div>
                        <!-- Line pointing to chart - positioned to align with right edge of "On Duty" text -->
                        <div class="absolute top-6 right-0 w-16 h-px bg-blue-400"></div>
                    </div>
                </div>
                
                <!-- Legend (Right Side) -->
                <div class="absolute top-0 right-0 space-y-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">On Duty</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        <span class="text-sm text-gray-600">Off Duty</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- New Submission -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">New Submission</h3>
                <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
            </div>
        </div>
        <div class="p-6">
            <div id="new-submissions" class="space-y-4">
                <!-- Loading skeleton -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="animate-pulse bg-gray-200 w-10 h-10 rounded-full"></div>
                        <div>
                            <div class="animate-pulse bg-gray-200 h-4 rounded mb-2 w-32"></div>
                            <div class="animate-pulse bg-gray-200 h-3 rounded w-20"></div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Accept</button>
                        <button class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Decline</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Issue -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Attendance Issue</h3>
                <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">See Details</a>
            </div>
        </div>
        <div class="p-6">
            <div id="attendance-issues" class="space-y-4">
                <!-- Loading skeleton -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="animate-pulse bg-gray-200 w-10 h-10 rounded-full"></div>
                        <div>
                            <div class="animate-pulse bg-gray-200 h-4 rounded mb-2 w-32"></div>
                            <div class="animate-pulse bg-gray-200 h-3 rounded w-20"></div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Leave</span>
                        <span class="text-xs text-gray-500">11 Nov 2024, 8:47 AM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attention Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Attention</h3>
                <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Lengkap</a>
            </div>
        </div>
        <div class="p-6">
            <div id="attention-items" class="space-y-4">
                <!-- Loading skeleton -->
                <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <div class="animate-pulse bg-gray-200 w-10 h-10 rounded-full"></div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <div class="animate-pulse bg-gray-200 h-4 rounded w-24"></div>
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">High</span>
                            </div>
                            <div class="animate-pulse bg-gray-200 h-3 rounded mb-2"></div>
                            <div class="animate-pulse bg-gray-200 h-3 rounded w-32"></div>
                        </div>
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
    let employeeChart, ageChart, dutyChart;
    
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all tabs
            tabBtns.forEach(tab => tab.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Here you can add logic to show/hide different content based on tab
            const tabType = this.dataset.tab;
            console.log('Switched to tab:', tabType);
        });
    });
    
    // Project dropdown functionality
    const dropdownBtn = document.getElementById('project-dropdown-btn');
    const dropdown = document.getElementById('project-dropdown');
    const dropdownArrow = document.getElementById('dropdown-arrow');
    const selectedProject = document.getElementById('selected-project');
    const projectOptions = document.querySelectorAll('.project-option');
    
    // Toggle dropdown
    dropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
        dropdownArrow.classList.toggle('rotate-180');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && !dropdownBtn.contains(e.target)) {
            dropdown.classList.add('hidden');
            dropdownArrow.classList.remove('rotate-180');
        }
    });
    
    // Handle project selection
    projectOptions.forEach(option => {
        option.addEventListener('click', function() {
            const value = this.dataset.value;
            const text = this.textContent.trim();
            
            selectedProject.textContent = text;
            dropdown.classList.add('hidden');
            dropdownArrow.classList.remove('rotate-180');
            
            // Here you can add logic to filter data based on selected project
            console.log('Selected project:', value, text);
            
            // Reload dashboard data with selected project filter
            loadDashboardStats(value);
        });
    });
    
    // Load dashboard data
    loadDashboardStats();
    loadEmployeeChart();
    loadAgeChart();
    loadDutyChart();
    loadNewSubmissions();
    loadOvertimeSubmissions();
    loadAttendanceIssues();
    loadAttentionItems();
    
    // Refresh data every 5 minutes
    setInterval(() => {
        loadDashboardStats();
        loadNewSubmissions();
        loadOvertimeSubmissions();
        loadAttendanceIssues();
        loadAttentionItems();
    }, 300000);
    
    async function loadDashboardStats(projectFilter = 'all') {
        try {
            console.log('Loading dashboard stats...');
            const url = new URL('{{ route("perusahaan.dashboard.api.stats") }}');
            if (projectFilter !== 'all') {
                url.searchParams.append('project', projectFilter);
            }
            
            const response = await fetch(url);
            const stats = await response.json();
            console.log('Stats loaded:', stats);
            
            // Update stat cards with animation
            updateStatCard('total-karyawan', stats.total_karyawan);
            updateStatCard('kehadiran-hari-ini', stats.kehadiran_hari_ini);
            
            // Calculate absent count
            const absentCount = stats.total_karyawan - stats.kehadiran_hari_ini;
            updateStatCard('absent-count', absentCount);
            
            // Update leave count
            updateStatCard('leave-count', stats.cuti_pending || 0);
            
            // Mock data for other cards (you can implement these in the controller)
            updateStatCard('early-tapout', 2);
            updateStatCard('overtime-count', 4);
            
            // Update additional info
            document.getElementById('new-employees').textContent = '5';
            document.getElementById('present-employees').textContent = stats.kehadiran_hari_ini;
            document.getElementById('absent-employees').textContent = absentCount;
            document.getElementById('early-employees').textContent = '2';
            document.getElementById('overtime-employees').textContent = '4';
            document.getElementById('leave-employees').textContent = stats.cuti_pending || 0;
            
            // Also reload charts when project filter changes
            loadEmployeeChart(projectFilter);
            loadAgeChart(projectFilter);
            loadDutyChart(projectFilter);
            
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
    
    async function loadEmployeeChart(projectFilter = 'all') {
        try {
            console.log('Loading employee chart...');
            const url = new URL('{{ route("perusahaan.dashboard.api.employee-division-stats") }}');
            if (projectFilter !== 'all') {
                url.searchParams.append('project', projectFilter);
            }
            
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Employee chart data:', data);
            
            const labels = data.map(item => item.division);
            const menData = data.map(item => item.men);
            const womenData = data.map(item => item.women);
            
            // Destroy existing chart if it exists
            if (employeeChart) {
                employeeChart.destroy();
            }
            
            const ctx = document.getElementById('employeeChart').getContext('2d');
            employeeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Laki-Laki',
                        data: menData,
                        backgroundColor: '#3B82F6',
                        borderRadius: 4,
                        barThickness: 20
                    }, {
                        label: 'Perempuan',
                        data: womenData,
                        backgroundColor: '#F472B6',
                        borderRadius: 4,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    const label = context.dataset.label; // Sudah dalam bahasa Indonesia
                                    const value = context.parsed.y;
                                    return `${label}: ${value} karyawan`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 10
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
            console.error('Error loading employee chart:', error);
        }
    }
    
    async function loadAgeChart(projectFilter = 'all') {
        try {
            console.log('Loading age chart...');
            const url = new URL('{{ route("perusahaan.dashboard.api.employee-age-stats") }}');
            if (projectFilter !== 'all') {
                url.searchParams.append('project', projectFilter);
            }
            
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Age chart data:', data);
            
            const labels = data.map(item => item.group);
            const menData = data.map(item => item.men);
            const womenData = data.map(item => item.women);
            
            // Destroy existing chart if it exists
            if (ageChart) {
                ageChart.destroy();
            }
            
            const ctx = document.getElementById('ageChart').getContext('2d');
            ageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Laki-Laki',
                        data: menData,
                        backgroundColor: '#3B82F6',
                        borderRadius: 4,
                        barThickness: 15
                    }, {
                        label: 'Perempuan',
                        data: womenData,
                        backgroundColor: '#F472B6',
                        borderRadius: 4,
                        barThickness: 15
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return 'Umur ' + context[0].label;
                                },
                                label: function(context) {
                                    const label = context.dataset.label; // Sudah dalam bahasa Indonesia
                                    const value = context.parsed.x;
                                    return `${label}: ${value} karyawan`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading age chart:', error);
        }
    }
    
    async function loadDutyChart(projectFilter = 'all') {
        try {
            console.log('Loading duty chart...');
            
            // Fetch real duty data from API with project filter
            const url = new URL('{{ route("perusahaan.dashboard.api.duty-stats") }}');
            if (projectFilter !== 'all') {
                url.searchParams.append('project', projectFilter);
            }
            
            const response = await fetch(url);
            const dutyData = await response.json();
            
            const onDuty = dutyData.on_duty || 0;
            const offDuty = dutyData.off_duty || 0;
            const total = onDuty + offDuty;
            
            // Update display values
            document.getElementById('total-employee-duty').textContent = total;
            document.getElementById('on-duty-count').textContent = onDuty;
            document.getElementById('off-duty-count').textContent = offDuty;
            
            if (total > 0) {
                document.getElementById('on-duty-percentage').textContent = ((onDuty / total) * 100).toFixed(2) + '%';
                document.getElementById('off-duty-percentage').textContent = ((offDuty / total) * 100).toFixed(2) + '%';
            } else {
                document.getElementById('on-duty-percentage').textContent = '0%';
                document.getElementById('off-duty-percentage').textContent = '0%';
            }
            
            const ctx = document.getElementById('dutyChart').getContext('2d');
            dutyChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['On Duty', 'Off Duty'],
                    datasets: [{
                        data: [onDuty, offDuty],
                        backgroundColor: ['#3B82F6', '#E5E7EB'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label;
                                    const value = context.parsed;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading duty chart:', error);
            
            // Fallback to sample data if API fails
            const onDuty = 13;
            const offDuty = 10;
            const total = onDuty + offDuty;
            
            document.getElementById('total-employee-duty').textContent = total;
            document.getElementById('on-duty-count').textContent = onDuty;
            document.getElementById('off-duty-count').textContent = offDuty;
            document.getElementById('on-duty-percentage').textContent = ((onDuty / total) * 100).toFixed(2) + '%';
            document.getElementById('off-duty-percentage').textContent = ((offDuty / total) * 100).toFixed(2) + '%';
            
            const ctx = document.getElementById('dutyChart').getContext('2d');
            dutyChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['On Duty', 'Off Duty'],
                    datasets: [{
                        data: [onDuty, offDuty],
                        backgroundColor: ['#3B82F6', '#E5E7EB'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }
    
    async function loadNewSubmissions() {
        try {
            console.log('Loading new submissions...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.new-submissions") }}');
            const submissions = await response.json();
            console.log('New submissions:', submissions);
            
            const container = document.getElementById('new-submissions');
            
            if (submissions.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>Tidak ada submission baru</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = submissions.map(submission => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            ${submission.avatar}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">${submission.name}</div>
                            <div class="text-sm text-gray-500">${submission.description}</div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Accept</button>
                        <button class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Decline</button>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading new submissions:', error);
        }
    }
    
    async function loadOvertimeSubmissions() {
        try {
            // For now, use the same endpoint as new submissions but filter for overtime
            // You can create a separate endpoint later if needed
            const container = document.getElementById('overtime-submissions');
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-clock text-3xl mb-2"></i>
                    <p>Tidak ada overtime submission</p>
                </div>
            `;
        } catch (error) {
            console.error('Error loading overtime submissions:', error);
        }
    }
    
    async function loadAttendanceIssues() {
        try {
            console.log('Loading attendance issues...');
            const response = await fetch('{{ route("perusahaan.dashboard.api.attendance-issues") }}');
            const issues = await response.json();
            console.log('Attendance issues:', issues);
            
            const container = document.getElementById('attendance-issues');
            
            if (issues.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-3xl mb-2"></i>
                        <p>Tidak ada masalah kehadiran</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = issues.map(issue => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            ${issue.avatar}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">${issue.name}</div>
                            <div class="text-sm text-gray-500">
                                ${issue.status === 'terlambat' ? `Terlambat ${issue.late_duration} menit` : 'Alpha'}
                            </div>
                            <div class="text-xs text-gray-400">
                                ${issue.status === 'terlambat' ? `Masuk: ${issue.time}` : 'Tidak hadir'}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 ${issue.status === 'terlambat' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'} rounded-full text-xs font-medium">
                            ${issue.status === 'terlambat' ? 'Late' : 'Absent'}
                        </span>
                        <span class="text-xs text-gray-500">${issue.date}</span>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading attendance issues:', error);
        }
    }
    
    async function loadAttentionItems() {
        try {
            // Mock data - implement in controller
            const items = [
                {
                    name: 'Admin Rudi',
                    message: 'Pengumuman',
                    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    time: '11 Nov 2024, 9:24 AM',
                    priority: 'High',
                    avatar: 'AR'
                },
                {
                    name: 'Admin Rudi',
                    message: 'Pengumuman',
                    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    time: '11 Nov 2024, 8:00 AM',
                    priority: 'High',
                    avatar: 'AR'
                }
            ];
            
            const container = document.getElementById('attention-items');
            container.innerHTML = items.map(item => `
                <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded-lg mb-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            ${item.avatar}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">${item.name}</div>
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">${item.priority}</span>
                            </div>
                            <div class="font-semibold text-gray-900 mb-1">${item.message}</div>
                            <div class="text-sm text-gray-600 mb-2">${item.description}</div>
                            <div class="text-xs text-gray-500">${item.time}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading attention items:', error);
        }
    }
});
</script>

<style>
.tab-btn.active {
    background-color: white;
    color: #1f2937;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.tab-btn {
    color: #6b7280;
}

.tab-btn:hover {
    color: #374151;
}
</style>
@endpush
