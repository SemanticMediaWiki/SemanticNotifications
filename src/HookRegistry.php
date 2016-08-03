<?php

namespace SMW\Notifications;

use SMW\Notifications\EchoPresentationModel;
use SMW\Notifications\EchoFormatter;
use SMW\Notifications\ChangeNotification\ChangeNotificationFilter;
use SMWDataItem as DataItem;
use Hooks;
use EchoEvent;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistry {

	/**
	 * @var array
	 */
	private $handlers = array();

	/**
	 * @since 1.0
	 *
	 * @param array $options
	 */
	public function __construct() {
		$this->addCallbackHandlers();
	}

	/**
	 * @since  1.0
	 */
	public function register() {
		foreach ( $this->handlers as $name => $callback ) {
			Hooks::register( $name, $callback );
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isRegistered( $name ) {
		return Hooks::isRegistered( $name );
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return Callable|false
	 */
	public function getHandlerFor( $name ) {
		return isset( $this->handlers[$name] ) ? $this->handlers[$name] : false;
	}

	private function addCallbackHandlers() {

		$echoNotificationsManager = new EchoNotificationsManager();

		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$this->handlers['SMW::Property::initProperties'] = function ( $baseRegistry ) {

			$propertyRegistry = new PropertyRegistry();

			$propertyRegistry->register(
				$baseRegistry
			);

			return true;
		};

		/**
		 * @see https://www.semantic-mediawiki.org/wiki/Hooks/SMW::DataType::initTypes
		 */
		$this->handlers['SMW::DataType::initTypes'] = function ( $dataTypeRegistry ) {

			$dataTypeRegistry->registerDatatype(
				'_notification_group',
				'\SMW\Notifications\DataValues\NotificationGroupValue',
				DataItem::TYPE_BLOB
			);

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide
		 * @see https://www.mediawiki.org/wiki/Extension:Echo/BeforeCreateEchoEvent
		 *
		 * @param array &$notifications
		 * @param array &$notificationCategories
		 * @param array &$icons
		 */
		$this->handlers['BeforeCreateEchoEvent'] = function( array &$notifications, array &$notificationCategories, array &$icons ) use ( $echoNotificationsManager ) {

			$echoNotificationsManager->addNotificationsDefinitions(
				$notifications,
				$notificationCategories,
				$icons
			);

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UserGetDefaultOptions
		 *
		 * @param array[] &$defaultOptions
		 */
		$this->handlers['UserGetDefaultOptions'] = function( array &$defaultOptions ) use ( $echoNotificationsManager ) {
			$echoNotificationsManager->addDefaultOptions( $defaultOptions );
			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide#Bundled_notifications
		 *
		 * @param EchoEvent $event
		 * @param string &$bundleString
		 */
		$this->handlers['EchoGetBundleRules'] = function( EchoEvent $event, &$bundleString ) use ( $echoNotificationsManager ) {
			$echoNotificationsManager->getNotificationsBundle( $event, $bundleString );
			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide#Bundled_notifications
		 *
		 * @param EchoEvent $event
		 * @param array &$users
		 */
		$this->handlers['EchoGetDefaultNotifiedUsers'] = function( EchoEvent $event, &$users ) use ( $echoNotificationsManager ) {
			$echoNotificationsManager->getDefaultRecipientsByType( $event, $users );
			return true;
		};

		$this->handlers['SMW::SQLStore::AfterDataUpdateComplete'] = function ( $store, $semanticData, $compositePropertyTableDiffIterator ) use ( $echoNotificationsManager ) {

			$changeNotificationFilter = new ChangeNotificationFilter(
				$semanticData->getSubject(),
				$store
			);

			$changeNotificationFilter->setAgent(
				$GLOBALS['wgUser']
			);

			$hasChangeToNotifAbout = $changeNotificationFilter->hasChangeToNotifAbout(
				$compositePropertyTableDiffIterator
			);

			$echoNotificationsManager->createEvent(
				$changeNotificationFilter->getEventRecord( $hasChangeToNotifAbout )
			);

			return true;
		};

	}

}
