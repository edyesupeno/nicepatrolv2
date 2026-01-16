@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Kelola pengaturan aplikasi Nice Patrol')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-4">
                <h3 class="font-semibold text-gray-900 mb-4">Kategori</h3>
                <nav class="space-y-1">
                    <a href="#general" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-blue-600 bg-blue-50">
                        <i class="fas fa-cog mr-3"></i>
                        Umum
                    </a>
                    <a href="#appearance" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-palette mr-3"></i>
                        Tampilan
                    </a>
                    <a href="#seo" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-search mr-3"></i>
                        SEO & Metadata
                    </a>
                </nav>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="w-full px-4 py-3 text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General Settings -->
            <div id="general" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Pengaturan Umum</h3>
                        <p class="text-sm text-gray-500">Konfigurasi dasar aplikasi</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi</label>
                        <input 
                            type="text" 
                            name="app_name" 
                            value="{{ $settings['general']->firstWhere('key', 'app_name')->value ?? 'Nice Patrol' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nice Patrol"
                        >
                        <p class="text-xs text-gray-500 mt-1">Nama aplikasi yang ditampilkan di sidebar dan header</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title Aplikasi</label>
                        <input 
                            type="text" 
                            name="app_title" 
                            value="{{ $settings['general']->firstWhere('key', 'app_title')->value ?? '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nice Patrol - Sistem Manajemen Patroli"
                        >
                        <p class="text-xs text-gray-500 mt-1">Title yang ditampilkan di browser tab</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Footer Text</label>
                        <input 
                            type="text" 
                            name="footer_text" 
                            value="{{ $settings['general']->firstWhere('key', 'footer_text')->value ?? '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nice Patrol - Sistem Manajemen Patroli Keamanan"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Copyright Text</label>
                        <input 
                            type="text" 
                            name="copyright_text" 
                            value="{{ $settings['general']->firstWhere('key', 'copyright_text')->value ?? '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="© 2026 Nice Patrol. All rights reserved."
                        >
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div id="appearance" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-palette text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Tampilan</h3>
                        <p class="text-sm text-gray-500">Logo, icon, dan visual branding</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo Aplikasi</label>
                        @php
                            $logo = $settings['appearance']->firstWhere('key', 'app_logo')->value ?? null;
                        @endphp
                        @if($logo)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="h-16 object-contain border border-gray-200 rounded-lg p-2">
                        </div>
                        @endif
                        <input 
                            type="file" 
                            name="app_logo" 
                            accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, SVG. Maksimal 2MB</p>
                    </div>

                    <!-- Favicon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                        @php
                            $favicon = $settings['appearance']->firstWhere('key', 'app_favicon')->value ?? null;
                        @endphp
                        @if($favicon)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $favicon) }}" alt="Favicon" class="h-8 w-8 object-contain border border-gray-200 rounded">
                        </div>
                        @endif
                        <input 
                            type="file" 
                            name="app_favicon" 
                            accept="image/x-icon,image/png"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <p class="text-xs text-gray-500 mt-1">Format: ICO, PNG. Maksimal 1MB. Rekomendasi: 32x32px atau 16x16px</p>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div id="seo" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: linear-gradient(135deg, #3B82C8 0%, #2563A8 100%);">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">SEO & Metadata</h3>
                        <p class="text-sm text-gray-500">Optimasi mesin pencari</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea 
                            name="app_description" 
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Deskripsi singkat aplikasi untuk SEO"
                        >{{ $settings['seo']->firstWhere('key', 'app_description')->value ?? '' }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Deskripsi yang muncul di hasil pencarian Google (150-160 karakter)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input 
                            type="text" 
                            name="app_keywords" 
                            value="{{ $settings['seo']->firstWhere('key', 'app_keywords')->value ?? '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="patroli, keamanan, security, manajemen"
                        >
                        <p class="text-xs text-gray-500 mt-1">Kata kunci dipisahkan dengan koma</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Smooth scroll to section
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Update active state
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('text-blue-600', 'bg-blue-50');
                link.classList.add('text-gray-600');
            });
            this.classList.remove('text-gray-600');
            this.classList.add('text-blue-600', 'bg-blue-50');
        }
    });
});
</script>
@endpush
