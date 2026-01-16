// Mobile PWA JavaScript

// API Configuration - Auto-detect environment
const API_BASE_URL = (() => {
    const hostname = window.location.hostname;
    const port = window.location.port;
    const protocol = window.location.protocol;
    
    // Local development (.test domain, localhost, or IP address)
    if (hostname.includes('.test') || 
        hostname === 'localhost' || 
        hostname === '127.0.0.1' ||
        /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(hostname)) { // IP address pattern
        // Use same host and port, just add /api/v1 prefix
        return `${protocol}//${hostname}${port ? ':' + port : ''}/api/v1`;
    }
    
    // Production
    return 'https://apiv1.nicepatrol.id/api/v1';
})();

console.log('API Base URL:', API_BASE_URL);

// API Helper Functions
const API = {
    // Get auth token from localStorage
    getToken() {
        return localStorage.getItem('auth_token');
    },
    
    // Get user data from localStorage
    getUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },
    
    // Check if user is logged in
    isAuthenticated() {
        return !!this.getToken();
    },
    
    // Logout
    logout() {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    },
    
    // Make API call with token
    async call(endpoint, options = {}) {
        const token = this.getToken();
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(token && { 'Authorization': `Bearer ${token}` }),
            },
        };
        
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        });
        
        // Handle 401 Unauthorized
        if (response.status === 401) {
            this.logout();
            return;
        }
        
        return response.json();
    },
    
    // GET request
    async get(endpoint) {
        return this.call(endpoint, { method: 'GET' });
    },
    
    // POST request
    async post(endpoint, data) {
        return this.call(endpoint, {
            method: 'POST',
            body: JSON.stringify(data),
        });
    },
    
    // PUT request
    async put(endpoint, data) {
        return this.call(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    },
    
    // DELETE request
    async delete(endpoint) {
        return this.call(endpoint, { method: 'DELETE' });
    },
};

// Check authentication on page load
window.addEventListener('DOMContentLoaded', () => {
    const currentPath = window.location.pathname;
    
    // Skip auth check for login page
    if (currentPath === '/login') {
        // If already logged in, redirect to home
        if (API.isAuthenticated()) {
            const user = API.getUser();
            if (user.role === 'security_officer') {
                window.location.href = '/security/home';
            } else if (user.role === 'office_employee') {
                window.location.href = '/employee/home';
            }
        }
        return;
    }
    
    // Check if user is authenticated
    if (!API.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }
    
    // Verify token is still valid
    API.get('/user').then(response => {
        if (!response.success) {
            API.logout();
        }
    }).catch(() => {
        API.logout();
    });
});

// Check if running as PWA
function isPWA() {
    return window.matchMedia('(display-mode: standalone)').matches || 
           window.navigator.standalone === true;
}

// Show install prompt
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    console.log('beforeinstallprompt event fired');
    
    // Show install button if not already installed
    if (!isPWA()) {
        // Check if user dismissed before
        const dismissed = localStorage.getItem('installPromptDismissed');
        if (!dismissed) {
            showInstallPrompt();
        }
    }
});

function showInstallPrompt() {
    // Show install banner
    const installBanner = document.createElement('div');
    installBanner.id = 'installBanner';
    installBanner.style.cssText = `
        position: fixed;
        bottom: 80px;
        left: 16px;
        right: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideUp 0.3s ease-out;
    `;
    
    installBanner.innerHTML = `
        <div style="flex: 1;">
            <div style="font-weight: 600; margin-bottom: 4px;">Install Nice Patrol</div>
            <div style="font-size: 13px; opacity: 0.9;">Akses lebih cepat seperti aplikasi native</div>
        </div>
        <button id="installBtn" style="
            background: white;
            color: #667eea;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin-left: 12px;
            cursor: pointer;
        ">Install</button>
        <button id="dismissBtn" style="
            background: transparent;
            color: white;
            border: none;
            padding: 8px;
            margin-left: 8px;
            font-size: 20px;
            cursor: pointer;
        ">×</button>
    `;
    
    document.body.appendChild(installBanner);
    
    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Install button click
    document.getElementById('installBtn').addEventListener('click', installPWA);
    
    // Dismiss button click
    document.getElementById('dismissBtn').addEventListener('click', () => {
        installBanner.remove();
        localStorage.setItem('installPromptDismissed', 'true');
    });
    
    console.log('PWA can be installed');
}

// Handle install
async function installPWA() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response: ${outcome}`);
        
        if (outcome === 'accepted') {
            // Remove install banner
            const banner = document.getElementById('installBanner');
            if (banner) banner.remove();
        }
        
        deferredPrompt = null;
    }
}

// Geolocation helper
function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation not supported'));
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            position => resolve(position),
            error => reject(error),
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
}

// Camera helper
async function capturePhoto() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        return stream;
    } catch (error) {
        console.error('Camera access denied:', error);
        throw error;
    }
}

// Network status
window.addEventListener('online', () => {
    console.log('Back online');
    // Sync offline data if any
});

window.addEventListener('offline', () => {
    console.log('Gone offline');
    // Show offline indicator
});

// Prevent zoom on double tap (iOS)
let lastTouchEnd = 0;
document.addEventListener('touchend', (event) => {
    const now = Date.now();
    if (now - lastTouchEnd <= 300) {
        event.preventDefault();
    }
    lastTouchEnd = now;
}, false);

// Pull to refresh (simple implementation)
let startY = 0;
let isPulling = false;

document.addEventListener('touchstart', (e) => {
    if (window.scrollY === 0) {
        startY = e.touches[0].pageY;
        isPulling = true;
    }
});

document.addEventListener('touchmove', (e) => {
    if (!isPulling) return;
    
    const currentY = e.touches[0].pageY;
    const distance = currentY - startY;
    
    if (distance > 80) {
        // Trigger refresh
        console.log('Pull to refresh triggered');
    }
});

document.addEventListener('touchend', () => {
    isPulling = false;
    startY = 0;
});

console.log('Nice Patrol Mobile App loaded');
