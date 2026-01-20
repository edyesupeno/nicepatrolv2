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
                        <h1 class="text-lg font-bold tracking-tight">{{ setting('app_name', 'NicePatrol') }}</h1>
                        <p class="text-xs text-blue-200 font-medium">{{ auth()->user()->perusahaan->nama ?? 'Superadmin Panel' }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="menu-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                    <i class="fas fa-home w-5 text-center mr-3"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                @if(auth()->user()->isSuperAdmin())
                <!-- Section Divider -->
                <div class="section-divider my-4"></div>
                <div class="flex items-center px-4 py-2">
                    <div class="text-xs font-bold text-blue-200 uppercase tracking-wider">
                        Management
                    </div>
                </div>

                <!-- Perusahaan Mitra -->
                <a href="{{ route('admin.perusahaans.index') }}" class="menu-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.perusahaans.*') ? 'active' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                    <i class="fas fa-building w-5 text-center mr-3"></i>
                    <span class="font-medium">Perusahaan Mitra</span>
                </a>
                @endif

                <!-- Paket Langganan -->
                <a href="{{ route('admin.users.index') }}" class="menu-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.users.*') ? 'active' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                    <i class="fas fa-box w-5 text-center mr-3"></i>
                    <span class="font-medium">Paket Langganan</span>
                </a>

                <!-- Tagihan & Pembayaran -->
                <a href="{{ route('admin.lokasis.index') }}" class="menu-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.lokasis.*') ? 'active' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
                    <i class="fas fa-credit-card w-5 text-center mr-3"></i>
                    <span class="font-medium">Tagihan & Pembayaran</span>
                </a>

                <!-- Section Divider -->
                <div class="section-divider my-4"></div>

                <!-- Pengaturan -->
                <a href="{{ route('admin.settings.index') }}" class="menu-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.settings.*') ? 'active' : 'text-white hover:bg-white hover:bg-opacity-10' }}">
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
                            <p class="text-sm text-gray-500 mt-1">@yield('page-subtitle', 'Selamat datang di panel manajemen NicePatrol SaaS')</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Notification Bell -->
                            <button class="relative p-2 text-gray-400 hover:text-gray-600 transition">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            <!-- Quick Actions -->
                            <button class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 hover:shadow-lg" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                                <i class="fas fa-plus mr-2"></i>Tambah Data
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
    </script>

    @stack('scripts')
</body>
</html>
