#!/usr/bin/env bash
# Sets up a cake core installation one folder level above the cloned plugin.
#
# Uses the CAKE_VERSION environment variable to determine the Cake
# version to clone.
#
# Expects to be called from the Plugin's directory. I.e.: `build/cakephp.sh`


# Get correct cake version
# Clone Cake
# cd into that dir
# Copy the plugin into the cake core.
# Copy the plugin's composer.json into core and execute it to load dependencies.
# Set up the core to use the plugin.


#
# Returns the latest reference (either a branch or tag) for any given
# MAJOR.MINOR semantic versioning.
#
latest_ref() {
	# Get version from master branch
	MASTER=$(curl --silent https://raw.github.com/cakephp/cakephp/master/lib/Cake/VERSION.txt)
	MASTER=$(echo "$MASTER" | tail -1 | grep -Ei "^$CAKE_VERSION\.")
	if [ -n "$MASTER" ]; then
		echo "master"
		exit 0
	fi

	# Check if any branch matches CAKE_VERSION
	BRANCH=$(curl --silent https://api.github.com/repos/cakephp/cakephp/git/refs/heads)
	BRANCH=$(echo "$BRANCH" | grep -Ei "\"refs/heads/$CAKE_VERSION\"" | grep -oEi "$CAKE_VERSION" | tail -1)
	if [ -n "$BRANCH" ]; then
		echo "$BRANCH"
		exit 0
	fi

	# Get the latest tag matching CAKE_VERSION.*
	TAG=$(curl --silent https://api.github.com/repos/cakephp/cakephp/git/refs/tags)
	TAG=$(echo "$TAG" | grep -Ei "\"refs/tags/$CAKE_VERSION\." | grep -oEi "$CAKE_VERSION\.[^\"]+" | tail -1)
	if [ -n "$TAG" ]; then
		echo "$TAG"
		exit 0
	fi
}

REPO_PATH=$(pwd)
SELF_PATH="$( cd -P "$( dirname "$0" )" >/dev/null 2>&1 && pwd )"

# Clone CakePHP repository
CAKE_REF=$(latest_ref)
if [ -z "$CAKE_REF" ]; then
	echo "Found no valid ref to match with version $CAKE_VERSION" >&2
	exit 1
fi

git clone git://github.com/cakephp/cakephp.git --branch $CAKE_REF --depth 1 ../cakephp

