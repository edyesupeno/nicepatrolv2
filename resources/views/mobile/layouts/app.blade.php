<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    @php
        $themeColor = setting('app_primary_color', '#0071CE');
    @endphp
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- API Configuration -->
    <script>
        window.API_DOMAIN = '{{ config('app.api_domain') }}';
    </script>
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nice Patrol">
    
    <title>@yield('title', 'Nice Patrol')</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/mobile/manifest">
    
    <!-- Icons - Dynamic from settings -->
    @php
        $appIcon = setting('app_favicon') ? asset('storage/' . setting('app_favicon')) : asset('favicon.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $appIcon }}">
    <link rel="apple-touch-icon" href="{{ $appIcon }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ $appIcon }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $appIcon }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ $appIcon }}">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="/mobile/css/app.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    @yield('content')
    
    <!-- Scripts -->
    <script src="/mobile/js/app.js?v={{ time() }}"></script>
    <script>
        // Register Service Worker (disabled for development)
        @if(config('app.env') === 'production')
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/mobile/service-worker.js')
                .then(reg => console.log('Service Worker registered'))
                .catch(err => console.log('Service Worker registration failed'));
        }
        @endif
        
        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    
    @stack('scripts')
</body>
</html>
