#!/usr/bin/env bash
# Main "test" execution during a Travis build.
#
# Expects a PLUGIN_NAME environment variable to be set.
#
# When a PHPCS env var is set to `1`, a code sniff report will be run
# and the exit code used as the return value.
#
# Otherwise PHPUnit tests will be run, respecting the COVERALLS=1 setting
# to control generation of a clover.xml coverage report.
#
# Expects to be a run from a Cake installation's app/ folder.
# The plugin being tested should be available in ./Plugin/$PLUGIN_NAME/

if [ "$PHPCS" == 1 ]; then
    "$PLUGIN_NAME/build/codesniffer.sh"
else
    "$PLUGIN_NAME/build/unittests.sh"
fi
