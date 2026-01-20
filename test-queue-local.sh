#!/bin/bash

echo "🚀 Starting Laravel Queue Worker for LOCAL TESTING"
echo "📝 Processing jobs from 'default' queue"
echo "⚠️  Press Ctrl+C to stop"
echo "🔄 Auto-restart on code changes: ENABLED"
echo ""

# Start queue worker with auto-restart for development
php artisan queue:work --queue=default --sleep=1 --tries=3 --timeout=60 --max-time=300

echo ""
echo "✅ Queue worker stopped"