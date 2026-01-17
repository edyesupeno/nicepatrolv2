#!/bin/bash

echo "🚀 Setting up Cloudflare Tunnel for Nice Patrol development..."
echo ""

# Check if cloudflared is installed
if ! command -v cloudflared &> /dev/null; then
    echo "📦 Installing cloudflared..."
    brew install cloudflared
    echo "✅ cloudflared installed"
else
    echo "✅ cloudflared already installed"
fi

echo ""
echo "🔧 Installing Cloudflare service with your token..."

# Install the service with the provided token
sudo cloudflared service install eyJhIjoiOTQ1YTllNGVlNWZkODBkYTBmZWU3MTg0NTQ5ZmZhNWMiLCJ0IjoiZWFiOGQ3ZmMtMDUyMS00ZmIwLWI4MjEtM2Q1ZjQxYTEyYzI2IiwicyI6Ik9ESXdPR1ZoTlRBdFpqUmtNQzAwTXprM0xUaGhNV1V0TUdOa09HSmpNakU4TnpnMyJ9

echo ""
echo "✅ Cloudflare Tunnel service installed!"
echo ""
echo "🌐 Starting tunnel for localhost:8000..."

# Start tunnel for localhost:8000
cloudflared tunnel --url http://localhost:8000 &

TUNNEL_PID=$!

echo ""
echo "🎉 Cloudflare Tunnel is running!"
echo ""
echo "📱 Look for the HTTPS URL above (something like: https://xxx.trycloudflare.com)"
echo "🔒 Use that HTTPS URL to access your app with camera and GPS permissions"
echo ""
echo "⚠️  Keep this terminal open to maintain the tunnel"
echo "🛑 Press Ctrl+C to stop the tunnel"
echo ""

# Wait for user to stop
wait $TUNNEL_PID