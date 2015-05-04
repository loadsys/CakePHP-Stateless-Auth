#!/bin/bash
#
# The default FriendsOfCake/travis before_script.sh doesn't exclude
# the Plugin/$PLUGIN_NAME/vendor directory when it write out its
# phpunit.xml file. This script overwrites it.


echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<phpunit>
<filter>
    <whitelist>
        <directory suffix=\".php\">Plugin/$PLUGIN_NAME</directory>
        <exclude>
            <directory suffix=\".php\">Plugin/$PLUGIN_NAME/Test</directory>
            <directory suffix=\".php\">Plugin/$PLUGIN_NAME/vendor</directory>
        </exclude>
    </whitelist>
</filter>
</phpunit>" > ../cakephp/app/phpunit.xml
