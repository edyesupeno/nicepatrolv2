<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <div class="flex justify-around items-center h-16 px-2">
        <!-- Home -->
        <a href="/security/home" class="flex flex-col items-center justify-center flex-1 {{ request()->is('security/home') ? 'active-nav' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            <span class="text-xs mt-1 {{ request()->is('security/home') ? 'font-semibold' : '' }}">Beranda</span>
        </a>

        <!-- Activity -->
        <a href="/security/activity" class="flex flex-col items-center justify-center flex-1 {{ request()->is('security/activity') ? 'active-nav' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
            </svg>
            <span class="text-xs mt-1">Aktivitas</span>
        </a>

        <!-- Scan (Center) -->
        <a href="/security/scan-qr" class="flex flex-col items-center justify-center -mt-6">
            <div class="w-14 h-14 rounded-full flex items-center justify-center shadow-lg" style="background-color: #0071CE;">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <span class="text-xs mt-1 text-gray-600">Scan</span>
        </a>

        <!-- Laporan -->
        <a href="/security/laporan" class="flex flex-col items-center justify-center flex-1 {{ request()->is('security/laporan') ? 'active-nav' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
            <span class="text-xs mt-1">Laporan</span>
        </a>

        <!-- Profile -->
        <a href="/profile" class="flex flex-col items-center justify-center flex-1 {{ request()->is('profile') ? 'active-nav' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
            <span class="text-xs mt-1 {{ request()->is('profile') ? 'font-semibold' : '' }}">Profil</span>
        </a>
    </div>
</div>

<style>
.active-nav {
    color: #0071CE !important;
}
</style>
