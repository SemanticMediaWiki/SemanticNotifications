<?php

use SMW\Notifications\HookRegistry;

/**
 * @see https://github.com/SemanticMediaWiki/SemanticNotifications/
 *
 * @defgroup SemanticNotifications Semantic Notifications
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of the Semantic Notifications extension, it is not a valid entry point.' );
}

if ( defined( 'SMW_NOTIFICATIONS_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

SemanticNotifications::initExtension();

$GLOBALS['wgExtensionFunctions'][] = function() {
	SemanticNotifications::onExtensionFunction();
};

/**
 * @codeCoverageIgnore
 */
class SemanticNotifications {

	/**
	 * @since 1.0
	 */
	public static function initExtension() {

		define( 'SMW_NOTIFICATIONS_VERSION', '1.0.0-alpha' );

		// Register extension info
		$GLOBALS['wgExtensionCredits']['semantic'][] = array(
			'path'           => __FILE__,
			'name'           => 'Semantic Notifications',
			'author'         => array( 'James Hong Kong' ),
			'url'            => 'https://github.com/SemanticMediaWiki/SemanticNotifications/',
			'descriptionmsg' => 'smw-notifications-desc',
			'version'        => SMW_NOTIFICATIONS_VERSION,
			'license-name'   => 'GPL-2.0+',
		);

		// Register message files
		$GLOBALS['wgMessagesDirs']['SemanticNotifications'] = __DIR__ . '/i18n';

		$GLOBALS['snogChangeNotificationDetectionPropertyExemptionList'] = array(
			'_MDAT',
			'_REDI'
		);
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {

		if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.26', 'lt' ) ) {
			die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticNotifications/">Semantic Notifications</a> is only compatible with MediaWiki 1.26 or above. You need to upgrade MediaWiki first.' );
		}

		// There is no good way to detect whether Echo is available or not without
		// making a class_exists, what should I say ...
		if ( !isset( $GLOBALS['wgMessagesDirs']['Echo'] ) ) {
			die( '<b>Error:</b> <a href="https://github.com/SemanticMediaWiki/SemanticNotifications/">Semantic Notifications</a> requires the Echo extension, please enable or install <a href="https://www.mediawiki.org/wiki/Extension:Echo">Echo(Notifications)</a> first.' );
		}

		if ( !defined( 'SMW_VERSION' ) ) {
			die( '<b>Error:</b> <a href="https://github.com/SemanticMediaWiki/SemanticNotifications/">Semantic Notifications</a> requires the Semantic MediaWiki extension, please enable or install <a href="https://github.com/SemanticMediaWiki/SemanticMediaWiki/">Semantic MediaWiki</a> first.' );
		}

		$hookRegistry = new HookRegistry();
		$hookRegistry->register();
	}

	/**
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		return SMW_NOTIFICATIONS_VERSION;
	}

}
