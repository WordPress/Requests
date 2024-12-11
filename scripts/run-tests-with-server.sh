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

# Source the environment setup
if ! . "${SCRIPT_DIR}/start-test-environment.sh"; then
    exit 1
fi

# Run the tests
"${SCRIPT_DIR}/run-phpunit.sh" --no-coverage
TEST_EXIT_CODE=$?

# Stop the test environment
. "${SCRIPT_DIR}/stop-test-environment.sh"

exit $TEST_EXIT_CODE
