<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ setting('app_name', 'Nice Patrol') }}</title>
    
    <!-- Favicon -->
    @if(setting('app_favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . setting('app_favicon')) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @endif
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-blue: #3B82C8;
            --primary-blue-dark: #2563A8;
            --primary-blue-light: #60A5FA;
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #3B82C8 0%, #2563A8 100%);
        }
        
        .menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .menu-item:hover {
            transform: translateX(4px);
        }
        
        .menu-item.active {
            background: white;
            color: #3B82C8;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .submenu-item {
            transition: all 0.2s ease;
        }
        
        .submenu-item:hover {
            transform: translateX(4px);
        }
        
        .logo-shield {
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #60A5FA 0%, #3B82C8 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .section-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .badge-new {
            background: #EF4444;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .7; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 sidebar-gradient text-white flex flex-col shadow-2xl">
            <!-- Logo -->
            <div class="p-6 border-b border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 logo-shield rounded-xl flex items-center justify-center">
                        @if(setting('app_logo'))
                            <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="h-8 object-contain">
                        @else
                            <i class="fas fa-shield-halved text-2xl" style="color: #3B82C8;"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight">{{ auth()->user()->perusahaan->nama }}</h1>
                        <p class="text-xs text-blue-200 font-medium">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('perusahaan.dashboard') }}" class="menu-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.dashboard') ? 'active' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                    <i class="fas fa-home w-5 text-center mr-3"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Kehadiran Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('kehadiran')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.kehadiran.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check w-5 text-center mr-3"></i>
                            <span class="font-medium">Kehadiran</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-kehadiran"></i>
                    </button>
                    <div id="submenu-kehadiran" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.kehadiran.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.kehadiran.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.kehadiran.index') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-calendar-check w-5 text-center mr-3 text-xs"></i>
                            <span>Kehadiran</span>
                        </a>
                        <a href="{{ route('perusahaan.kehadiran.schedule') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.kehadiran.schedule') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-calendar-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Schedule</span>
                        </a>
                        <a href="{{ route('perusahaan.kehadiran.lokasi-absensi') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.kehadiran.lokasi-absensi') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Lokasi Absensi</span>
                        </a>
                        <a href="{{ route('perusahaan.kehadiran.manajemen-shift') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.kehadiran.manajemen-shift') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-clock w-5 text-center mr-3 text-xs"></i>
                            <span>Manajemen Shift</span>
                        </a>
                    </div>
                </div>

                <!-- Payroll Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('payroll')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.payroll.*') || request()->routeIs('perusahaan.daftar-payroll.*') || request()->routeIs('perusahaan.manajemen-gaji.*') || request()->routeIs('perusahaan.setting-payroll.*') || request()->routeIs('perusahaan.komponen-payroll.*') || request()->routeIs('perusahaan.template-komponen.*') || request()->routeIs('perusahaan.template-karyawan.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave w-5 text-center mr-3"></i>
                            <span class="font-medium">Payroll</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-payroll"></i>
                    </button>
                    <div id="submenu-payroll" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.payroll.*') || request()->routeIs('perusahaan.daftar-payroll.*') || request()->routeIs('perusahaan.manajemen-gaji.*') || request()->routeIs('perusahaan.setting-payroll.*') || request()->routeIs('perusahaan.komponen-payroll.*') || request()->routeIs('perusahaan.template-komponen.*') || request()->routeIs('perusahaan.template-karyawan.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.payroll.generate') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.payroll.generate') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-rocket w-5 text-center mr-3 text-xs"></i>
                            <span>Generate Payroll</span>
                        </a>
                        <a href="{{ route('perusahaan.daftar-payroll.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.daftar-payroll.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-money-check-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Daftar Payroll</span>
                        </a>
                        <a href="{{ route('perusahaan.manajemen-gaji.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.manajemen-gaji.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-money-bill-wave w-5 text-center mr-3 text-xs"></i>
                            <span>Manajemen Gaji</span>
                        </a>
                        <a href="{{ route('perusahaan.setting-payroll.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.setting-payroll.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-cog w-5 text-center mr-3 text-xs"></i>
                            <span>Setting Payroll</span>
                        </a>
                        <a href="{{ route('perusahaan.komponen-payroll.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.komponen-payroll.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-puzzle-piece w-5 text-center mr-3 text-xs"></i>
                            <span>Komponen Payroll</span>
                        </a>
                        <a href="{{ route('perusahaan.template-komponen.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.template-komponen.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-file-contract w-5 text-center mr-3 text-xs"></i>
                            <span>Template Komponen</span>
                        </a>
                        <a href="{{ route('perusahaan.template-karyawan.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.template-karyawan.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-users-cog w-5 text-center mr-3 text-xs"></i>
                            <span>Template Karyawan</span>
                        </a>
                    </div>
                </div>

                <!-- Karyawan Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('karyawan')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.karyawans.*') || request()->routeIs('perusahaan.jabatans.*') || request()->routeIs('perusahaan.status-karyawan.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-users w-5 text-center mr-3"></i>
                            <span class="font-medium">Karyawan</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-karyawan"></i>
                    </button>
                    <div id="submenu-karyawan" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.karyawans.*') || request()->routeIs('perusahaan.jabatans.*') || request()->routeIs('perusahaan.status-karyawan.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.karyawans.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.karyawans.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-user w-5 text-center mr-3 text-xs"></i>
                            <span>Karyawan</span>
                        </a>
                        <a href="{{ route('perusahaan.status-karyawan.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.status-karyawan.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-tag w-5 text-center mr-3 text-xs"></i>
                            <span>Status</span>
                        </a>
                        <a href="{{ route('perusahaan.jabatans.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.jabatans.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-user-tie w-5 text-center mr-3 text-xs"></i>
                            <span>Jabatan</span>
                        </a>
                    </div>
                </div>

                <!-- Perusahaan Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('perusahaan')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.profil.*') || request()->routeIs('perusahaan.kantors.*') || request()->routeIs('perusahaan.projects.*') || request()->routeIs('perusahaan.areas.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-building w-5 text-center mr-3"></i>
                            <span class="font-medium">Perusahaan</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-perusahaan"></i>
                    </button>
                    <div id="submenu-perusahaan" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.profil.*') || request()->routeIs('perusahaan.kantors.*') || request()->routeIs('perusahaan.projects.*') || request()->routeIs('perusahaan.areas.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.profil.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.profil.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-id-card w-5 text-center mr-3 text-xs"></i>
                            <span>Profil Perusahaan</span>
                        </a>
                        <a href="{{ route('perusahaan.kantors.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.kantors.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-building w-5 text-center mr-3 text-xs"></i>
                            <span>Kantor</span>
                        </a>
                        <a href="{{ route('perusahaan.projects.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.projects.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-project-diagram w-5 text-center mr-3 text-xs"></i>
                            <span>Project</span>
                        </a>
                        <a href="{{ route('perusahaan.areas.index') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.areas.*') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-map-marked-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Area</span>
                        </a>
                    </div>
                </div>

                <!-- Section Divider -->
                <div class="section-divider my-4"></div>
                <div class="flex items-center px-4 py-2">
                    <div class="text-xs font-bold text-blue-200 uppercase tracking-wider">
                        Patrol Management
                    </div>
                </div>

                <!-- Patrol Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('patrol')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.patrol.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-book w-5 text-center mr-3"></i>
                            <span class="font-medium">Patrol</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-patrol"></i>
                    </button>
                    <div id="submenu-patrol" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.patrol.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.patrol.kategori-insiden') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.patrol.kategori-insiden') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-exclamation-triangle w-5 text-center mr-3 text-xs"></i>
                            <span>Kategori Insiden</span>
                        </a>
                        <a href="{{ route('perusahaan.patrol.area') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.patrol.area') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-map w-5 text-center mr-3 text-xs"></i>
                            <span>Area</span>
                        </a>
                        <a href="{{ route('perusahaan.patrol.rute-patrol') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.patrol.rute-patrol') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-route w-5 text-center mr-3 text-xs"></i>
                            <span>Rute Patrol</span>
                        </a>
                        <a href="{{ route('perusahaan.patrol.checkpoint') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.patrol.checkpoint') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Checkpoint</span>
                        </a>
                        <a href="{{ route('perusahaan.patrol.aset-kawasan') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.patrol.aset-kawasan') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-building w-5 text-center mr-3 text-xs"></i>
                            <span>Aset Kawasan</span>
                        </a>
                    </div>
                </div>

                <!-- Tim Patroli Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('tim-patroli')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.tim-patroli.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-users w-5 text-center mr-3"></i>
                            <span class="font-medium">Tim Patroli</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-tim-patroli"></i>
                    </button>
                    <div id="submenu-tim-patroli" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.tim-patroli.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.tim-patroli.master') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.tim-patroli.master') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-users-cog w-5 text-center mr-3 text-xs"></i>
                            <span>Master Tim Patroli</span>
                        </a>
                        <a href="{{ route('perusahaan.tim-patroli.inventaris') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.tim-patroli.inventaris') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-clipboard-list w-5 text-center mr-3 text-xs"></i>
                            <span>Inventaris Patroli</span>
                        </a>
                    </div>
                </div>

                <!-- Laporan Patroli Menu (Collapsible) -->
                <div class="mb-1">
                    <button onclick="toggleSubmenu('laporan-patroli')" class="menu-item w-full flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('perusahaan.laporan-patroli.*') ? 'bg-white bg-opacity-10' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt w-5 text-center mr-3"></i>
                            <span class="font-medium">Laporan Patroli</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="icon-laporan-patroli"></i>
                    </button>
                    <div id="submenu-laporan-patroli" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('perusahaan.laporan-patroli.*') ? '' : 'hidden' }}">
                        <a href="{{ route('perusahaan.laporan-patroli.insiden') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.laporan-patroli.insiden') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-exclamation-circle w-5 text-center mr-3 text-xs"></i>
                            <span>Laporan Insiden</span>
                        </a>
                        <a href="{{ route('perusahaan.laporan-patroli.kawasan') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.laporan-patroli.kawasan') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-map-marked-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Patroli Kawasan</span>
                        </a>
                        <a href="{{ route('perusahaan.laporan-patroli.inventaris') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.laporan-patroli.inventaris') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-boxes w-5 text-center mr-3 text-xs"></i>
                            <span>Inventaris Patroli</span>
                        </a>
                        <a href="{{ route('perusahaan.laporan-patroli.kru-change') }}" class="submenu-item flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('perusahaan.laporan-patroli.kru-change') ? 'bg-white text-blue-600 font-semibold' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' }} text-sm">
                            <i class="fas fa-exchange-alt w-5 text-center mr-3 text-xs"></i>
                            <span>Kru Change</span>
                        </a>
                    </div>
                </div>

                <!-- Section Divider -->
                <div class="section-divider my-4"></div>

                <!-- Pengaturan -->
                <a href="#" class="menu-item flex items-center px-4 py-3 rounded-xl text-white hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-cog w-5 text-center mr-3"></i>
                    <span class="font-medium">Pengaturan</span>
                </a>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-white border-opacity-20">
                <div class="flex items-center mb-3">
                    <div class="w-11 h-11 user-avatar rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-200 font-medium">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="w-full flex items-center justify-center bg-white bg-opacity-10 hover:bg-white hover:bg-opacity-20 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-sm text-gray-500 mt-1">@yield('page-subtitle', 'Kelola data patroli perusahaan Anda')</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Notification Bell -->
                            <button class="relative p-2 text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Show success/error messages from session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#3B82C8'
            });
        @endif

        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: "Anda akan keluar dari sistem",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3B82C8',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Toggle submenu
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById('submenu-' + menuId);
            const icon = document.getElementById('icon-' + menuId);
            
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Auto-open submenu if active
        document.addEventListener('DOMContentLoaded', function() {
            const activeSubmenu = document.querySelector('[id^="submenu-"]:not(.hidden)');
            if (activeSubmenu) {
                const menuId = activeSubmenu.id.replace('submenu-', '');
                const icon = document.getElementById('icon-' + menuId);
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
