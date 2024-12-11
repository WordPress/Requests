#!/bin/bash

# Ensure the script is being sourced
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    echo "Error: This script must be sourced to set environment variables"
    echo "Usage: source scripts/start-test-environment.sh"
    exit 1
fi

# Store script dir for relative paths
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
PID_DIR="${PROJECT_ROOT}/.test-pids"

# Check if mitmproxy is installed
if ! command -v mitmdump &> /dev/null; then
    echo "Error: mitmproxy is not installed. Please install it with: pip3 install mitmproxy"
    return 1
fi

# Get mitmproxy version and compare with minimum required (using 9.0.0 as minimum)
MITM_VERSION=$(mitmdump --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -n1)
MINIMUM_VERSION="11.0.2"

if ! printf '%s\n%s\n' "$MINIMUM_VERSION" "$MITM_VERSION" | sort -V -C; then
    echo "Error: mitmproxy version $MITM_VERSION is too old"
    echo "Please upgrade to version $MINIMUM_VERSION or newer with: pip3 install --upgrade mitmproxy"
    return 1
fi

echo "Found mitmproxy version $MITM_VERSION"

# Create directory for PID files if it doesn't exist
mkdir -p "$PID_DIR"

# Start the test server
echo "Starting test server..."
PORT=8080 "${PROJECT_ROOT}/vendor/bin/start.sh"
echo $! > "${PID_DIR}/test-server.pid"
export REQUESTS_TEST_HOST_HTTP=localhost:8080

# Start proxy servers
echo "Starting proxy servers..."
PORT=9002 "${PROJECT_ROOT}/tests/utils/proxy/start.sh"
echo $! > "${PID_DIR}/proxy-server.pid"

PORT=9003 AUTH="test:pass" "${PROJECT_ROOT}/tests/utils/proxy/start.sh"
echo $! > "${PID_DIR}/proxy-auth-server.pid"

# Set environment variables
export REQUESTS_HTTP_PROXY=localhost:9002
export REQUESTS_HTTP_PROXY_AUTH=localhost:9003
export REQUESTS_HTTP_PROXY_AUTH_USER=test
export REQUESTS_HTTP_PROXY_AUTH_PASS=pass

# Wait for servers to be ready
echo "Waiting for servers to be ready..."
sleep 2

# Test server connections
echo "Testing server connections..."
curl -s -I http://localhost:8080 > /dev/null || { echo "Test server not responding"; return 1; }
curl -s -I http://localhost:9002 > /dev/null || { echo "Proxy server not responding"; return 1; }

echo "Test environment is ready!"
echo "Environment variables set:"
echo "REQUESTS_TEST_HOST_HTTP=localhost:8080"
echo "REQUESTS_HTTP_PROXY=localhost:9002"
echo "REQUESTS_HTTP_PROXY_AUTH=localhost:9003"

return 0 