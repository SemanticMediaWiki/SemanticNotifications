<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL | E_STRICT );
date_default_timezone_set( 'UTC' );
ini_set( 'display_errors', 1 );

if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The Semantic MediaWiki test autoloader is not available' );
}

if ( !defined( 'SMW_NOTIFICATIONS_VERSION' ) ) {
	die( "\nSemantic Notifications is not available, please check your Composer or LocalSettings.\n\n" );
}

print sprintf( "\n%-20s%s\n", "Semantic Notifications: ", SMW_NOTIFICATIONS_VERSION );

$autoLoader = require $autoloaderClassPath;
$autoLoader->addPsr4( 'SMW\\Notifications\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoLoader->addPsr4( 'SMW\\Notifications\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
unset( $autoLoader );
