#!/bin/bash

# Get the directory of this script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Source the environment setup
source "${SCRIPT_DIR}/start-test-environment.sh" || exit 1

# Run the tests
"${SCRIPT_DIR}/run-phpunit.sh" --no-coverage
TEST_EXIT_CODE=$?

# Stop the test environment
source "${SCRIPT_DIR}/stop-test-environment.sh"

exit $TEST_EXIT_CODE 