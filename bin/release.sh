#!/bin/bash

# TODO: it's maybe better to have two different scripts for releasing & deploying
# TODO: ask if the major or minor version should be increased

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ "$1" == "" ]
then
	echo "You must provide the argument 'client' or 'server'!"
	exit
fi;

# this compiles sass files and compresses css/js for deployment
# and creates a new tag
if [ "$1" == "client" ]
then
	cd "$SCRIPT_DIR"/..
	grunt build
	git add bower.json composer.json
	
	# we need to explicitly add and commit these files
	# because they're in .gitignore
	git add -f public/styles/*.min.css
	
	semver=$(cat "$SCRIPT_DIR"/../.semver)
	semver=$(php -f "$SCRIPT_DIR"/bump_version.php $semver)
	
	git commit bower.json composer.json public/styles/*.min.css -m "Created release $semver"
	git tag $semver
	
	echo "Run 'git push --tags' now"
fi

# this installs the bower/composer dependencies
if [ "$1" == "server" ]
then
	cd "$SCRIPT_DIR"/..
	
	# TODO: switch to the maintenance page
	
	php composer.phar self-update
	php composer.phar install
	npm install
	bower install
	grunt build
	
	# TODO: switch back to the live page
fi
