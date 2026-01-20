#!/bin/bash

# Cron job untuk memastikan queue worker selalu berjalan
# Tambahkan ke crontab: * * * * * /path/to/your/project/queue-cron.sh

PROJECT_PATH="/path/to/your/project"
LOCK_FILE="$PROJECT_PATH/storage/queue-worker.lock"
LOG_FILE="$PROJECT_PATH/storage/logs/queue-cron.log"

# Function to log with timestamp
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Check if queue worker is running
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        log_message "Queue worker is running (PID: $PID)"
        exit 0
    else
        log_message "Queue worker PID file exists but process is dead, removing lock file"
        rm -f "$LOCK_FILE"
    fi
fi

# Start queue worker
log_message "Starting queue worker..."
cd "$PROJECT_PATH"

# Start queue worker in background and save PID
nohup php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=300 > "$PROJECT_PATH/storage/logs/queue-worker.log" 2>&1 &
WORKER_PID=$!

# Save PID to lock file
echo $WORKER_PID > "$LOCK_FILE"
log_message "Queue worker started with PID: $WORKER_PID"