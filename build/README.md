# CakePHP Plugin build system

A set of scripts designed to be used either locally or with Travis to test the plugin.

The scripts are broken into two groups:

* Convenience scripts that encapsulate the commands to run for executing unit tests, code sniffs, etc.
* Build scripts that are intended to be used with Travis and toggle their behavior based on a number of environment variables (for use with build matrices.)

# Convenience Scripts

These scripts assume that you've "installed" your plugin into a functional Cake application such that `./Plugin/$PLUGIN_NAME/` correctly points to your plugin under development.

* `./Plugin/$PLUGIN_NAME/build/unittests.sh` - Wraps up the call to `cake test`

# Env Vars

