#!/bin/bash
set -ex

BASE_PATH=$(pwd)
MW_INSTALL_PATH=$BASE_PATH/../mw

# Run Composer installation from the MW root directory
function installToMediaWikiRoot {
	echo -e "Running MW root composer install build on $TRAVIS_BRANCH \n"

	cd $MW_INSTALL_PATH

	if [ "$PHPUNIT" != "" ]
	then
		composer require 'phpunit/phpunit='$PHPUNIT --update-with-dependencies
	else
		composer require 'phpunit/phpunit=4.8.*' --update-with-dependencies
	fi

	# Missing composer component
	if [ "$ECHO" != "" ]
	then
		cd extensions

		wget https://github.com/wikimedia/mediawiki-extensions-Echo/archive/$ECHO.tar.gz -O $ECHO.tar.gz

		tar -zxf $ECHO.tar.gz
		mv mediawiki-* Echo

		cd ..
	fi

	if [ "$SNO" != "" ]
	then
		composer require 'mediawiki/semantic-notifications='$SNO --update-with-dependencies
	else
		composer init --stability dev
		composer require mediawiki/semantic-notifications "dev-master" --dev --update-with-dependencies

		cd extensions
		cd SemanticNotifications

		# Pull request number, "false" if it's not a pull request
		# After the install via composer an additional get fetch is carried out to
		# update th repository to make sure that the latests code changes are
		# deployed for testing
		if [ "$TRAVIS_PULL_REQUEST" != "false" ]
		then
			git fetch origin +refs/pull/"$TRAVIS_PULL_REQUEST"/merge:
			git checkout -qf FETCH_HEAD
		else
			git fetch origin "$TRAVIS_BRANCH"
			git checkout -qf FETCH_HEAD
		fi

		cd ../..
	fi

	# Rebuild the class map for added classes during git fetch
	composer dump-autoload
}

function updateConfiguration {

	cd $MW_INSTALL_PATH

	# Site language
	if [ "$SITELANG" != "" ]
	then
		echo '$wgLanguageCode = "'$SITELANG'";' >> LocalSettings.php
	fi

	if [ "$ECHO" == "REL1_31" ]
	then
		echo 'wfLoadExtension( "Echo" );' >> LocalSettings.php
	else
		echo 'require_once "$IP/extensions/Echo/Echo.php";' >> LocalSettings.php
	fi

	echo 'wfLoadExtension( "SemanticNotifications" );' >> LocalSettings.php

	echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
	echo 'ini_set("display_errors", 1);' >> LocalSettings.php
	echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
	echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php
	echo "putenv( 'MW_INSTALL_PATH=$(pwd)' );" >> LocalSettings.php

	# SMW#1732
	echo 'wfLoadExtension( "SemanticMediaWiki" );' >> LocalSettings.php

	php maintenance/update.php --quick
}

installToMediaWikiRoot
updateConfiguration
