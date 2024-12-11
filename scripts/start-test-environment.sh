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

# Detect if we're being sourced (POSIX-compatible way)
sourced=0
if [ -n "$ZSH_EVAL_CONTEXT" ]; then 
    case $ZSH_EVAL_CONTEXT in *:file:*) sourced=1;; esac
elif [ -n "$KSH_VERSION" ]; then
    [ "$(cd $(dirname -- $0) && pwd -P)/$(basename -- $0)" != "$(cd $(dirname -- ${.sh.file}) && pwd -P)/$(basename -- ${.sh.file})" ] && sourced=1
elif [ -n "$BASH_VERSION" ]; then
    (return 0 2>/dev/null) && sourced=1
else
    # POSIX fallback - check if we can modify our environment
    # Try to modify a test variable
    TEST_VAR="test"
    if [ -z "${TEST_VAR:-}" ]; then
        sourced=0
    else
        sourced=1
    fi
fi

if [ $sourced -eq 0 ]; then
    echo "Warning: This script should be sourced to set environment variables"
    echo "Please either:"
    echo "1. Source this script:  source scripts/start-test-environment.sh"
    echo "2. Or manually set these environment variables:"
    echo "   export REQUESTS_TEST_HOST_HTTP=localhost:8080"
    echo "   export REQUESTS_HTTP_PROXY=localhost:9002"
    echo "   export REQUESTS_HTTP_PROXY_AUTH=localhost:9003"
    echo "   export REQUESTS_HTTP_PROXY_AUTH_USER=test"
    echo "   export REQUESTS_HTTP_PROXY_AUTH_PASS=pass"
    if [ -n "${BASH_VERSION:-}" ]; then
        exit 1
    fi
fi

PID_DIR="${PROJECT_ROOT}/tests/utils/pids"

# Check if mitmproxy is installed
if ! command -v mitmdump >/dev/null 2>&1; then
    echo "Error: mitmproxy is not installed. Please install it with: pip3 install mitmproxy"
    return 1 2>/dev/null || exit 1
fi

# Get mitmproxy version and compare with minimum required
MITM_VERSION=$(mitmdump --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -n1)
MINIMUM_VERSION="11.0.2"

# POSIX-compatible version comparison
if ! printf '%s\n%s\n' "$MINIMUM_VERSION" "$MITM_VERSION" | sort -V -C 2>/dev/null; then
    echo "Error: mitmproxy version $MITM_VERSION is too old"
    echo "Please upgrade to version $MINIMUM_VERSION or newer with: pip3 install --upgrade mitmproxy"
    return 1 2>/dev/null || exit 1
fi

echo "Found mitmproxy version $MITM_VERSION"

# Create directory for PID files if it doesn't exist
mkdir -p "$PID_DIR"

# Start the test server
echo "Starting test server..."
PORT=8080 "${PROJECT_ROOT}/vendor/bin/start.sh"
echo $! > "${PID_DIR}/test-server.pid"
REQUESTS_TEST_HOST_HTTP=localhost:8080
export REQUESTS_TEST_HOST_HTTP

# Start proxy servers
echo "Starting proxy servers..."
PORT=9002 "${PROJECT_ROOT}/tests/utils/proxy/start.sh"
echo $! > "${PID_DIR}/proxy-server.pid"

PORT=9003 AUTH="test:pass" "${PROJECT_ROOT}/tests/utils/proxy/start.sh"
echo $! > "${PID_DIR}/proxy-auth-server.pid"

# Set environment variables
REQUESTS_HTTP_PROXY=localhost:9002
REQUESTS_HTTP_PROXY_AUTH=localhost:9003
REQUESTS_HTTP_PROXY_AUTH_USER=test
REQUESTS_HTTP_PROXY_AUTH_PASS=pass

export REQUESTS_HTTP_PROXY
export REQUESTS_HTTP_PROXY_AUTH
export REQUESTS_HTTP_PROXY_AUTH_USER
export REQUESTS_HTTP_PROXY_AUTH_PASS

# Wait for servers to be ready
echo "Waiting for servers to be ready..."
sleep 2

# Test server connections
echo "Testing server connections..."
if ! curl -s -I http://localhost:8080 >/dev/null 2>&1; then
    echo "Test server not responding"
    return 1 2>/dev/null || exit 1
fi

if ! curl -s -I http://localhost:9002 >/dev/null 2>&1; then
    echo "Proxy server not responding"
    return 1 2>/dev/null || exit 1
fi

echo "Test environment is ready!"
echo "Environment variables set:"
echo "REQUESTS_TEST_HOST_HTTP=localhost:8080"
echo "REQUESTS_HTTP_PROXY=localhost:9002"
echo "REQUESTS_HTTP_PROXY_AUTH=localhost:9003"

return 0 2>/dev/null || exit 0
