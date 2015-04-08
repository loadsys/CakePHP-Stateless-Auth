#!/usr/bin/env bash
# Executes before Travis build. Sets up a Cake core to use as a host to
# the plugin being tested.
#
# Uses the CAKE_VERSION environment variable to determine the Cake
# version to clone.
#
# Expects a PLUGIN_NAME environment variable to be set matching the
# plugin being tested.
#
# Expects to be a run from the cloned Plugin's root folder.

composer self-update

build/cakephp.sh


REPO_PATH=$(pwd)
SELF_PATH=$(cd "$(dirname "$0")"; pwd)

# Clone CakePHP repository
CAKE_REF=$(latest_ref)
if [ -z "$CAKE_REF" ]; then
	echo "Found no valid ref to match with version $CAKE_VERSION" >&2
	exit 1
fi

git clone git://github.com/cakephp/cakephp.git --branch $CAKE_REF --depth 1 ../cakephp

# Prepare plugin
cd ../cakephp/app

chmod -R 777 tmp









cp -R $REPO_PATH Plugin/$PLUGIN_NAME

mv $SELF_PATH/database.php Config/database.php

COMPOSER_JSON="$(pwd)/Plugin/$PLUGIN_NAME/composer.json"
if [ -f "$COMPOSER_JSON" ]; then
    cp $COMPOSER_JSON ./composer.json;
    composer install --dev --no-interaction --prefer-source
fi

for dep in $REQUIRE; do
    composer require --dev --no-interaction --prefer-source $dep;
done

if [ "$COVERALLS" = '1' ]; then
	composer require --dev satooshi/php-coveralls:dev-master
fi

if [ "$PHPCS" != '1' ]; then
	composer global require 'phpunit/phpunit=3.7.33'
	ln -s ~/.composer/vendor/phpunit/phpunit/PHPUnit ./Vendor/PHPUnit
fi

phpenv rehash

set +H

echo "CakePlugin::loadAll(array(array('bootstrap' => true, 'routes' => true, 'ignoreMissing' => true)));" >> Config/bootstrap.php

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<phpunit>
<filter>
    <whitelist>
        <directory suffix=\".php\">Plugin/$PLUGIN_NAME</directory>
        <exclude>
            <directory suffix=\".php\">Plugin/$PLUGIN_NAME/Test</directory>
        </exclude>
    </whitelist>
</filter>
</phpunit>" > phpunit.xml

echo "# for php-coveralls
src_dir: Plugin/$PLUGIN_NAME
coverage_clover: build/logs/clover.xml
json_path: build/logs/coveralls-upload.json" > .coveralls.yml
