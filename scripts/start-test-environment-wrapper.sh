#!/bin/bash

# Get the directory of this script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Source the main script
if source "${SCRIPT_DIR}/start-test-environment.sh"; then
    exit 0
else
    exit 1
fi 