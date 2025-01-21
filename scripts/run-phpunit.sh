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

# Detect PHPUnit version
PHPUNIT_VERSION=$("${PROJECT_ROOT}/vendor/bin/phpunit" --version | grep -oE '[0-9]+\.[0-9]+' | head -n1)

# Run the tests with the appropriate config
if printf '%s\n%s\n' "10.0" "$PHPUNIT_VERSION" | sort -V -C 2>/dev/null; then
    "${PROJECT_ROOT}/vendor/bin/phpunit" -c phpunit10.xml.dist "$1"
else
    "${PROJECT_ROOT}/vendor/bin/phpunit" "$1"
fi
