#!/bin/bash

# Nice Patrol - Auto Update IP Configuration
# Script untuk otomatis detect dan update IP address

echo "🔍 Detecting current IP address..."

# Detect IP
NEW_IP=$(ipconfig getifaddr en0 || ipconfig getifaddr en1 || ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)

if [ -z "$NEW_IP" ]; then
    echo "❌ Error: Could not detect IP address"
    exit 1
fi

echo "✅ Detected IP: $NEW_IP"
echo ""

# Get old IP from .env
OLD_IP=$(grep "APP_URL=" .env | cut -d'/' -f3 | cut -d':' -f1)

if [ "$OLD_IP" == "$NEW_IP" ]; then
    echo "ℹ️  IP address unchanged ($NEW_IP)"
    echo "   No update needed."
    exit 0
fi

echo "🔄 Updating configuration..."
echo "   Old IP: $OLD_IP"
echo "   New IP: $NEW_IP"
echo ""

# Update .env
echo "📝 Updating .env..."
sed -i '' "s|APP_URL=http://.*:8000|APP_URL=http://$NEW_IP:8000|g" .env

# Update vite.config.js
echo "📝 Updating vite.config.js..."
sed -i '' "s|host: '.*'|host: '$NEW_IP'|g" vite.config.js

# Update start-server.sh
echo "📝 Updating start-server.sh..."
sed -i '' "s|IP Address: .*|IP Address: $NEW_IP\"|g" start-server.sh
sed -i '' "s|http://.*:8000|http://$NEW_IP:8000|g" start-server.sh

# Update SERVER-INFO.txt
echo "📝 Updating SERVER-INFO.txt..."
sed -i '' "s|IP Address: .*|IP Address: $NEW_IP (Auto-detected)|g" SERVER-INFO.txt
sed -i '' "s|http://.*:8000|http://$NEW_IP:8000|g" SERVER-INFO.txt
sed -i '' "s|Last Updated: .*|Last Updated: $(date '+%Y-%m-%d %H:%M')|g" SERVER-INFO.txt

echo ""
echo "✅ Configuration updated successfully!"
echo ""
echo "📡 New IP Address: $NEW_IP"
echo "🌐 Access URL: http://$NEW_IP:8000"
echo ""
echo "⚠️  Please restart the server:"
echo "   1. Stop: ./stop-server.sh"
echo "   2. Start: ./start-server.sh"
echo ""
