#!/bin/bash

# Start Laravel Queue Worker
# This script will start the queue worker to process background jobs

echo "🚀 Starting Laravel Queue Worker..."
echo "📝 Processing jobs from 'default' queue"
echo "⚠️  Press Ctrl+C to stop"
echo ""

# Start queue worker with auto-restart on code changes
php artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600 --timeout=300

echo ""
echo "✅ Queue worker stopped"