#!/bin/bash

# Nice Patrol - Stop Network Server

echo "🛑 Stopping Nice Patrol Server..."

# Stop Laravel server (port 8000)
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "   Stopping Laravel server (port 8000)..."
    kill -9 $(lsof -t -i:8000)
fi

# Stop Vite server (port 5173)
if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null ; then
    echo "   Stopping Vite server (port 5173)..."
    kill -9 $(lsof -t -i:5173)
fi

echo "✅ All servers stopped!"
