// Ultra-simple map implementation - no complexity
let map;

function initMap() {
    try {
        console.log('🗺️ Starting ultra-simple map initialization...');
        
        // Get map element
        const mapElement = document.getElementById('map');
        if (!mapElement) {
            console.error('❌ Map element not found');
            return;
        }
        
        console.log('✅ Map element found, dimensions:', mapElement.offsetWidth, 'x', mapElement.offsetHeight);
        
        // Update status
        const statusElement = document.getElementById('map-loading');
        if (statusElement) {
            statusElement.innerHTML = '<span class="text-blue-600">Initializing map...</span>';
        }
        
        // Initialize map with basic settings
        map = L.map('map', {
            center: [-6.2088, 106.8456],
            zoom: 15,
            zoomControl: true,
            attributionControl: true
        });
        
        console.log('✅ Leaflet map object created');
        
        // Add OpenStreetMap tiles - most basic approach
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18,
            minZoom: 1
        });
        
        tileLayer.addTo(map);
        console.log('✅ Tile layer added');
        
        // Add a simple test marker
        const testMarker = L.marker([-6.2088, 106.8456])
            .addTo(map)
            .bindPopup('Test marker - Map is working!')
            .openPopup();
        
        console.log('✅ Test marker added');
        
        // Update status
        if (statusElement) {
            statusElement.innerHTML = '<span class="text-green-600">✅ Map loaded successfully</span>';
        }
        
        console.log('✅ Ultra-simple map initialization complete');
        
        // Force map to resize after a delay
        setTimeout(function() {
            if (map) {
                map.invalidateSize();
                console.log('✅ Map size invalidated');
            }
        }, 500);
        
    } catch (error) {
        console.error('❌ Error in ultra-simple map initialization:', error);
        const statusElement = document.getElementById('map-loading');
        if (statusElement) {
            statusElement.innerHTML = '<span class="text-red-600">❌ Map failed to load: ' + error.message + '</span>';
        }
    }
}

// Initialize when location tab is clicked
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌐 DOM loaded');
    
    const locationTab = document.getElementById('tab-location');
    if (locationTab) {
        locationTab.addEventListener('click', function() {
            console.log('📍 Location tab clicked');
            
            // Wait for tab to be visible
            setTimeout(function() {
                const tabContent = document.getElementById('content-location');
                if (tabContent && !tabContent.classList.contains('hidden')) {
                    console.log('✅ Location tab is visible, initializing map...');
                    if (!map) {
                        initMap();
                    } else {
                        console.log('🔄 Map already exists, refreshing...');
                        map.invalidateSize();
                    }
                } else {
                    console.log('⚠️ Location tab not visible yet');
                }
            }, 100);
        });
    }
    
    // Also check if location tab is already active
    const locationContent = document.getElementById('content-location');
    if (locationContent && !locationContent.classList.contains('hidden')) {
        console.log('🚀 Location tab already active, initializing map...');
        setTimeout(initMap, 200);
    }
});