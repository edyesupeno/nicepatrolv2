#!/bin/bash

# Nice Patrol - Complete Server Starter
# Jalankan Laravel server + Cloudflare tunnel sekaligus

echo "🚀 Starting Nice Patrol Complete Server..."
echo "📡 Local IP: $(ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1' | head -1)"
echo ""

# Function to kill process on port
kill_port() {
    local port=$1
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        echo "⚠️  Port $port is already in use. Stopping existing process..."
        kill -9 $(lsof -t -i:$port) 2>/dev/null
        sleep 2
    fi
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Kill existing processes
echo "🧹 Cleaning up existing processes..."
kill_port 8000
kill_port 5173

# Kill existing cloudflared processes
pkill -f cloudflared 2>/dev/null
sleep 2

echo ""
echo "🔧 Starting Laravel server on 0.0.0.0:8000..."
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!
echo "   ✅ Laravel PID: $LARAVEL_PID"

# Wait a moment for Laravel to start
sleep 3

# Check if Laravel started successfully
if ! curl -s http://localhost:8000 >/dev/null; then
    echo "❌ Laravel server failed to start!"
    kill $LARAVEL_PID 2>/dev/null
    exit 1
fi

echo "⚡ Starting Vite dev server..."
npm run dev -- --host &
VITE_PID=$!
echo "   ✅ Vite PID: $VITE_PID"

# Wait a moment for Vite to start
sleep 3

echo "🌐 Starting Cloudflare tunnel..."
if command_exists cloudflared; then
    # Check if tunnel config exists
    if [ -f "tunnel-config.yml" ]; then
        echo "   📋 Using existing tunnel configuration..."
        cloudflared tunnel --config tunnel-config.yml run &
        CF_PID=$!
        echo "   ✅ Cloudflare tunnel PID: $CF_PID"
        
        # Wait for tunnel to establish
        sleep 5
        
        echo "   🔗 Tunnel URLs:"
        echo "      https://devdash.nicepatrol.id"
        echo "      https://devapp.nicepatrol.id"
        echo "      https://devapi.nicepatrol.id"
    else
        echo "   ⚠️  tunnel-config.yml not found, using quick tunnel..."
        cloudflared tunnel --url http://localhost:8000 &
        CF_PID=$!
        echo "   ✅ Cloudflare quick tunnel PID: $CF_PID"
        sleep 5
    fi
else
    echo "   ⚠️  Cloudflared not found. Install with: brew install cloudflared"
    CF_PID=""
fi

echo ""
echo "✅ All servers started successfully!"
echo ""
echo "📱 Local Access:"
echo "   http://localhost:8000"
echo "   http://$(ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1' | head -1):8000"
echo ""
echo "🌐 Public Access:"
if [ -f "tunnel-config.yml" ]; then
    echo "   https://devdash.nicepatrol.id"
    echo "   https://devapp.nicepatrol.id"
    echo "   https://devapi.nicepatrol.id"
else
    echo "   Check terminal output above for Cloudflare tunnel URL"
fi
echo ""
echo "🔧 Development Tools:"
echo "   Vite: http://localhost:5173"
echo ""
echo "💡 Tips:"
echo "   - Use Ctrl+C to stop all servers"
echo "   - Laravel logs: tail -f storage/logs/laravel.log"
echo "   - Check tunnel status: cloudflared tunnel info"
echo ""

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Stopping all servers..."
    
    if [ ! -z "$LARAVEL_PID" ]; then
        echo "   Stopping Laravel server (PID: $LARAVEL_PID)..."
        kill $LARAVEL_PID 2>/dev/null
    fi
    
    if [ ! -z "$VITE_PID" ]; then
        echo "   Stopping Vite server (PID: $VITE_PID)..."
        kill $VITE_PID 2>/dev/null
    fi
    
    if [ ! -z "$CF_PID" ]; then
        echo "   Stopping Cloudflare tunnel (PID: $CF_PID)..."
        kill $CF_PID 2>/dev/null
    fi
    
    # Kill any remaining processes
    pkill -f "php artisan serve" 2>/dev/null
    pkill -f "vite" 2>/dev/null
    pkill -f "cloudflared" 2>/dev/null
    
    echo "✅ All servers stopped!"
    exit 0
}

# Set trap for cleanup
trap cleanup INT TERM

# Keep script running and show status
echo "🔄 Monitoring servers... (Press Ctrl+C to stop)"
while true; do
    sleep 30
    
    # Check if Laravel is still running
    if ! kill -0 $LARAVEL_PID 2>/dev/null; then
        echo "❌ Laravel server stopped unexpectedly!"
        cleanup
    fi
    
    # Check if Vite is still running
    if ! kill -0 $VITE_PID 2>/dev/null; then
        echo "❌ Vite server stopped unexpectedly!"
        cleanup
    fi
    
    # Optional: Check if Cloudflare tunnel is still running
    if [ ! -z "$CF_PID" ] && ! kill -0 $CF_PID 2>/dev/null; then
        echo "⚠️  Cloudflare tunnel stopped, attempting restart..."
        if [ -f "tunnel-config.yml" ]; then
            cloudflared tunnel --config tunnel-config.yml run &
        else
            cloudflared tunnel --url http://localhost:8000 &
        fi
        CF_PID=$!
        echo "   ✅ Cloudflare tunnel restarted (PID: $CF_PID)"
    fi
    
    # Show a heartbeat every 5 minutes
    current_time=$(date +"%H:%M:%S")
    echo "💓 $current_time - All servers running..."
done
