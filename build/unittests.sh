#!/usr/bin/env bash
# Runs the PHPunit test suite on the plugin.
#
# Expects a PLUGIN_NAME environment variable to be set.
#
# When a COVERALLS env var is set to `1`, a clover.xml coverage report
# will also be generated.
#
# Expects to be a run from a Cake installation's app/ folder.
# The plugin being tested should be available in ./Plugin/$PLUGIN_NAME/

CLOVER_FILE=tmp/clover.xml
if [ -z "$PLUGIN_NAME" ]; then
	if [[ "$0" = *Plugin/* ]]; then
		PLUGIN_NAME="$0"
		# Strip everything before the plugin name.
		PLUGIN_NAME="${PLUGIN_NAME##*Plugin/}"
		# Strip everything after the first path component.
		PLUGIN_NAME="${PLUGIN_NAME%%/*}"
	else
		# Use the folder name one "up" from the build/ folder.
		PLUGIN_NAME="$( cd -P "$( dirname "$0" )"/.. >/dev/null 2>&1 && basename `pwd` )"
		cd ../../
	fi
fi

if [ "$COVERALLS" == 1 ]; then
    ./Console/cake test $PLUGIN_NAME All$PLUGIN_NAME --stderr --coverage-clover "$CLOVER_FILE"
else
    ./Console/cake test $PLUGIN_NAME All$PLUGIN_NAME --stderr
fi
