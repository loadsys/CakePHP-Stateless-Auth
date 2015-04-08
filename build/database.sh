#!/usr/bin/env bash
#
# Creates a test database when the DB env var is set,
# and moves the database config file into place for use.
#
# Expects to be a run from a Cake installation's app/ folder.
# The plugin being tested should be available in ./Plugin/$PLUGIN_NAME/

PLUGIN_NAME=StatelessAuth
PLUGIN_PATH="Plugin/$PLUGIN_NAME"

if [ "$DB" = "mysql" ]; then
	mysql -e 'CREATE DATABASE cakephp_test;';
fi
if [ "$DB" = "pgsql" ]; then
	psql -c 'CREATE DATABASE cakephp_test;' -U postgres;
fi

mv "$PLUGIN_PATH/build/database.php" Config/database.php
