#!/bin/sh

# Try to determine script location
if [ -n "${BASH_SOURCE:-}" ]; then
    # Bash-specific path resolution
    SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
else
    # POSIX fallback - assume we're in the repository root
    PROJECT_ROOT="$(pwd)"
    SCRIPT_DIR="$PROJECT_ROOT/scripts"
fi

PID_DIR="${PROJECT_ROOT}/.test-pids"

# Function to safely kill a process
kill_process() {
    pid_file="$1"
    if [ -f "$pid_file" ]; then
        pid=$(cat "$pid_file")
        if kill -0 "$pid" 2>/dev/null; then
            echo "Stopping process $pid"
            kill "$pid"
            rm "$pid_file"
        else
            echo "Process $pid not running"
            rm "$pid_file"
        fi
    fi
}

# Stop all servers
echo "Stopping test environment..."

# Stop test server
kill_process "${PID_DIR}/test-server.pid"
"${PROJECT_ROOT}/vendor/bin/stop.sh"

# Stop proxy servers
PORT=9002 "${PROJECT_ROOT}/tests/utils/proxy/stop.sh"
kill_process "${PID_DIR}/proxy-server.pid"

PORT=9003 "${PROJECT_ROOT}/tests/utils/proxy/stop.sh"
kill_process "${PID_DIR}/proxy-auth-server.pid"

# Clean up PID directory if empty
rmdir "${PID_DIR}" 2>/dev/null || true

echo "Test environment stopped"

return 0 2>/dev/null || exit 0
