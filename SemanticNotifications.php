<?php

use SMW\Notifications\HookRegistry;

/**
 * @see https://github.com/SemanticMediaWiki/SemanticNotifications/
 *
 * @defgroup SemanticNotifications Semantic Notifications
 */
SemanticNotifications::load();

/**
 * @codeCoverageIgnore
 */
class SemanticNotifications {

	/**
	 * @since 1.0
	 *
	 * @note It is expected that this function is loaded before LocalSettings.php
	 * to ensure that settings and global functions are available by the time
	 * the extension is activated.
	 */
	public static function load() {

		if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
			include_once __DIR__ . '/vendor/autoload.php';
		}

		$GLOBALS['snogChangeNotificationDetectionPropertyExemptionList'] = array(
			'_MDAT',
			'_REDI'
		);
	}

	/**
	 * @since 1.0
	 */
	public static function initExtension( $credits = [] ) {

		$version = 'UNKNOWN' ;

		// See https://phabricator.wikimedia.org/T151136
		if ( isset( $credits['version'] ) ) {
			$version = $credits['version'];
		}

		define( 'SMW_NOTIFICATIONS_VERSION', $version );

		// Register message files
		$GLOBALS['wgMessagesDirs']['SemanticNotifications'] = __DIR__ . '/i18n';

		// Register the hook before the execution of ExtensionFunction
		$GLOBALS['wgHooks']['BeforeCreateEchoEvent'][] = "\SMW\Notifications\EchoNotificationsManager::initNotificationsDefinitions";
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {

		// Check requirements after LocalSetting.php has been processed

		if ( !defined( 'SMW_VERSION' ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die( "\nThe 'Semantic Notifications' extension requires the 'Semantic MediaWiki' extension to be installed and enabled.\n" );
			} else {
				die(
					'<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticNotifications/">Semantic Notifications</a> extension' .
					' requires the <a href="https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> extension to be installed and enabled.<br />'
				);
			}
		}

		// There is no good way to detect whether Echo is available or not without
		// making a class_exists, what should I say ...
		if ( !isset( $GLOBALS['wgMessagesDirs']['Echo'] ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die( "\nThe 'Semantic Notifications' extension requires the 'Echo' extension to be installed and enabled.\n" );
			} else {
				die(
					'<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticNotifications/">Semantic Notifications</a> extension' .
					' requires the <a href="https://www.mediawiki.org/wiki/Extension:Echo">Echo</a> extension to be installed and enabled.<br />'
				);
			}
		}

		$hookRegistry = new HookRegistry();
		$hookRegistry->register();
	}

}
