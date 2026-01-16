#!/bin/bash

# Nice Patrol - Network Server Starter
# Jalankan server agar bisa diakses di 1 network

echo "🚀 Starting Nice Patrol Server..."
echo "📡 IP Address: 10.79.202.42"
echo "🌐 Access from other devices: http://10.79.202.42:8000"
echo ""

# Check if port 8000 is already in use
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Port 8000 is already in use. Stopping existing process..."
    kill -9 $(lsof -t -i:8000)
    sleep 2
fi

# Check if port 5173 is already in use (Vite)
if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Port 5173 is already in use. Stopping existing process..."
    kill -9 $(lsof -t -i:5173)
    sleep 2
fi

echo "🔧 Starting Laravel server on 0.0.0.0:8000..."
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

echo "⚡ Starting Vite dev server..."
npm run dev -- --host &
VITE_PID=$!

echo ""
echo "✅ Servers started successfully!"
echo ""
echo "📱 Access from this device:"
echo "   http://localhost:8000"
echo "   http://10.79.202.42:8000"
echo ""
echo "📱 Access from other devices in same network:"
echo "   http://10.79.202.42:8000"
echo ""
echo "Press Ctrl+C to stop all servers"
echo ""

# Wait for Ctrl+C
trap "echo ''; echo '🛑 Stopping servers...'; kill $LARAVEL_PID $VITE_PID; exit" INT
wait
