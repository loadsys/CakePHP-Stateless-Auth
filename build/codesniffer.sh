#!/usr/bin/env bash
# Executes the php_codesniffer on the plugin.
#
# Expects a PLUGIN_NAME environment variable to be set matching the
# plugin being tested.
#
# Expects to be a run from a Cake installation's app/ folder.
# The plugin being tested should be available in ./Plugin/$PLUGIN_NAME/

if [ -z "$PLUGIN_NAME" ]; then
	if [[ "$0" = *Plugin/* ]]; then
		PLUGIN_NAME="$0"
		# Strip everything before the plugin name.
		PLUGIN_NAME="${PLUGIN_NAME##*Plugin/}"
		# Strip everything after the first path component.
		PLUGIN_NAME="${PLUGIN_NAME%%/*}"

		PLUGIN_PATH="./Plugin/$PLUGIN_NAME"
	else
		# Use the folder name one "up" from the build/ folder.
		PLUGIN_NAME="$( cd -P "$( dirname "$0" )"/.. >/dev/null 2>&1 && basename `pwd` )"

		PLUGIN_PATH="."
	fi
fi

set -x
"$PLUGIN_PATH/vendor/bin/phpcs" \
 -p \
 --extensions=php \
 --standard="vendor/loadsys/loadsys_codesniffer/Loadsys" \
 "$PLUGIN_PATH"
