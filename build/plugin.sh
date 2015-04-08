#!/usr/bin/env bash
# Copies the plugin and necessary files into a cake Core for testing.
#
# Expects a PLUGIN_NAME environment variable to be set matching the
# plugin being tested.
#
# Expects to be called from the Plugin's directory. I.e.: `build/cakephp.sh`


# cd into that dir
# Copy the plugin into the cake core.
# Copy the plugin's composer.json into core and execute it to load dependencies.
# Set up the core to use the plugin.


REPO_PATH=$(pwd)
SELF_PATH="$( cd -P "$( dirname "$0" )" >/dev/null 2>&1 && pwd )"


# Prepare plugin
cd ../cakephp/app

chmod -R 777 tmp


cp -R $REPO_PATH Plugin/$PLUGIN_NAME

#@TODO: Set up database.

COMPOSER_JSON="$(pwd)/Plugin/$PLUGIN_NAME/composer.json"
if [ -f "$COMPOSER_JSON" ]; then
    cp $COMPOSER_JSON ./composer.json
    composer install --dev --no-interaction --prefer-source
fi

ln -s ./vendor/phpunit/phpunit/PHPUnit ./Vendor/PHPUnit

phpenv rehash

set +H

echo "CakePlugin::loadAll(array(array('bootstrap' => true, 'routes' => true, 'ignoreMissing' => true)));" >> Config/bootstrap.php

PHPUNIT_XML="$(pwd)/Plugin/$PLUGIN_NAME/build/phpunit.xml.dist"
if [ -f "$PHPUNIT_XML" ]; then
    cp $PHPUNIT_XML ./phpunit.xml.dist
fi

COVERALL_YML="$(pwd)/Plugin/$PLUGIN_NAME/build/coveralls.yml"
if [ -f "$COVERALL_YML" ]; then
    cp $PHPUNIT_XML ./coveralls.yml
fi
