#!/bin/bash

echo "🚀 Starting Chrome with localhost permissions for development..."
echo ""
echo "This will allow camera and GPS access on localhost:8000"
echo ""

# Close existing Chrome instances
echo "Closing existing Chrome instances..."
pkill -f "Google Chrome" 2>/dev/null || true
sleep 2

# Check if Chrome is installed
if [ -d "/Applications/Google Chrome.app" ]; then
    CHROME_PATH="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
elif [ -d "/Applications/Chromium.app" ]; then
    CHROME_PATH="/Applications/Chromium.app/Contents/MacOS/Chromium"
else
    echo "❌ Chrome or Chromium not found in Applications folder"
    echo "Please install Google Chrome first"
    exit 1
fi

echo "✅ Found Chrome at: $CHROME_PATH"
echo ""

# Create temporary user data directory
TEMP_DIR="/tmp/chrome-dev-session-$(date +%s)"
mkdir -p "$TEMP_DIR"

echo "🔧 Starting Chrome with development flags..."
echo "📁 Using temp profile: $TEMP_DIR"
echo ""

# Start Chrome with development flags
"$CHROME_PATH" \
    --unsafely-treat-insecure-origin-as-secure=http://localhost:8000 \
    --user-data-dir="$TEMP_DIR" \
    --disable-web-security \
    --allow-running-insecure-content \
    --ignore-certificate-errors \
    --disable-features=VizDisplayCompositor \
    http://localhost:8000 &

echo "✅ Chrome started with development flags!"
echo ""
echo "📱 You can now access http://localhost:8000 with camera and GPS permissions."
echo "🔒 When prompted, click 'Allow' for camera and location access."
echo ""
echo "⚠️  Note: This is for development only. Don't use these flags for regular browsing."
echo ""
echo "To stop: Close Chrome and run 'pkill -f chrome-dev-session'"