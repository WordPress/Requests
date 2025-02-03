#!/bin/sh


# Set up PHPUnit command
# We're using composer exec to ensure we use the version of PHP that Composer
# is locked into, instead of the version of PHP that the system provides.
PHPUNIT="composer exec phpunit --"

# Detect PHPUnit version
PHPUNIT_VERSION=$($PHPUNIT --version | grep -oE '[0-9]+\.[0-9]+' | head -n 1)

# Determine config file based on version
if printf '%s\n%s\n' "10.0" "$PHPUNIT_VERSION" | sort -V -C 2>/dev/null; then
    CONFIG_FILE="phpunit10.xml.dist"
else
    CONFIG_FILE="phpunit.xml.dist"
fi

# Run the tests
$PHPUNIT -c "$CONFIG_FILE" "$@"
