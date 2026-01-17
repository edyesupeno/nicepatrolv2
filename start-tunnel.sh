#!/bin/bash

echo "🌐 Starting Named Cloudflare Tunnel..."
echo ""

# Kill any existing cloudflared processes
sudo pkill -f cloudflared 2>/dev/null

echo "🚀 Starting named tunnel: nice-patrol-dev"
echo "📱 Custom domains:"
echo "   • https://devapp.nicepatrol.id (Mobile App)"
echo "   • https://devapi.nicepatrol.id (API)"  
echo "   • https://devdash.nicepatrol.id (Dashboard)"
echo ""

# Start named tunnel
cloudflared tunnel --config tunnel-config.yml run nice-patrol-dev