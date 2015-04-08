#!/usr/bin/env bash
# Expects to be a run from a Cake installation's app/ folder.
# The plugin being tested should be available in ./Plugin/$PLUGIN_NAME/

PLUGIN_NAME=StatelessAuth
PLUGIN_PATH="Plugin/$PLUGIN_NAME"

"$PLUGIN_PATH/vendor/bin/phpcs" \
 --config "$PLUGIN_PATH/build/phpdoc.xml"
