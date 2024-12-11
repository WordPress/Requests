#!/bin/bash

# Get the directory of this script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Detect PHPUnit version
PHPUNIT_VERSION=$("${PROJECT_ROOT}/vendor/bin/phpunit" --version | grep --only-matching --max-count=1 --extended-regexp '\b[0-9]+\.[0-9]+')

# Determine if we should include coverage based on argument
COVERAGE_ARG=""
if [ "$1" != "--no-coverage" ]; then
    COVERAGE_ARG="--coverage-html build/coverage"
fi

# Run the tests with the appropriate config
if printf '%s\n%s\n' "10.0" "$PHPUNIT_VERSION" | sort -V -C; then
    "${PROJECT_ROOT}/vendor/bin/phpunit" -c phpunit10.xml.dist $COVERAGE_ARG
else
    "${PROJECT_ROOT}/vendor/bin/phpunit" $COVERAGE_ARG
fi 