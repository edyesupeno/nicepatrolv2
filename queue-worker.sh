#!/bin/bash

# Laravel Queue Worker untuk aaPanel
# File: /www/wwwroot/stagdash.nicepatrol.id/queue-worker.sh

PROJECT_PATH="/www/wwwroot/stagdash.nicepatrol.id"
LOCK_FILE="$PROJECT_PATH/storage/queue-worker.lock"
LOG_FILE="$PROJECT_PATH/storage/logs/queue-worker.log"
PHP_PATH="/www/server/php/84/bin/php"  # PHP 8.4

# Function to log with timestamp
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Check if queue worker is running
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        # Worker is running, check if it's been running too long (max 1 hour)
        START_TIME=$(stat -c %Y "$LOCK_FILE" 2>/dev/null || stat -f %m "$LOCK_FILE" 2>/dev/null)
        CURRENT_TIME=$(date +%s)
        RUNNING_TIME=$((CURRENT_TIME - START_TIME))
        
        if [ $RUNNING_TIME -gt 3600 ]; then
            log_message "Queue worker running too long (${RUNNING_TIME}s), restarting..."
            kill $PID 2>/dev/null
            rm -f "$LOCK_FILE"
        else
            log_message "Queue worker is running (PID: $PID, ${RUNNING_TIME}s)"
            exit 0
        fi
    else
        log_message "Queue worker PID file exists but process is dead, removing lock file"
        rm -f "$LOCK_FILE"
    fi
fi

# Change to project directory
cd "$PROJECT_PATH" || {
    log_message "ERROR: Cannot change to project directory: $PROJECT_PATH"
    exit 1
}

# Check if there are jobs to process
JOB_COUNT=$($PHP_PATH artisan tinker --execute="echo DB::table('jobs')->count();" 2>/dev/null | tail -1)

if [ "$JOB_COUNT" = "0" ]; then
    log_message "No jobs in queue, skipping worker start"
    exit 0
fi

# Start queue worker
log_message "Starting queue worker... (Jobs in queue: $JOB_COUNT)"

# Start queue worker in background and save PID
nohup $PHP_PATH artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=300 --stop-when-empty > "$PROJECT_PATH/storage/logs/queue-output.log" 2>&1 &
WORKER_PID=$!

# Save PID to lock file
echo $WORKER_PID > "$LOCK_FILE"
log_message "Queue worker started with PID: $WORKER_PID"

# Wait a moment to check if worker started successfully
sleep 2
if ps -p $WORKER_PID > /dev/null 2>&1; then
    log_message "Queue worker confirmed running (PID: $WORKER_PID)"
else
    log_message "ERROR: Queue worker failed to start"
    rm -f "$LOCK_FILE"
    exit 1
fi