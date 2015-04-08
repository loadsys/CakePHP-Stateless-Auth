#!/usr/bin/env bash
# Executes after a successful Travis build.
#
# If the COVERALLS env var is set to `1`, coverage will be published
# to coveralls.io.
#
# Expects to be a run from a Cake installation's app/ folder.
# The plugin being tested should be available in ./Plugin/$PLUGIN_NAME/

if [ "$COVERALLS" = '1' ]; then
    php vendor/bin/coveralls -c coveralls.yml -v;
fi
