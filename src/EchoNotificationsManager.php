<?php

namespace SMW\Notifications;

use EchoEvent;
use EchoAttributeManager;
use SMW\Notifications\ChangeNotification\ChangeNotificationFilter;
use SMW\Notifications\ChangeNotification\ChangeNotificationFormatter;
use SMW\Notifications\ChangeNotification\ChangeNotificationPresentationModel;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class EchoNotificationsManager {

	/**
	 * @see Echo::BeforeCreateEchoEvent
	 *
	 * @since 1.0
	 *
	 * @param array &$notifications
	 * @param array &$notificationCategories
	 * @param array &$icons
	 */
	public static function initNotificationsDefinitions( array &$notifications, array &$notificationCategories, array &$icons ) {

		$valueChangeNotificationType = ChangeNotificationFilter::VALUE_CHANGE;
		$specificationChangeNotificationType = ChangeNotificationFilter::SPECIFICATION_CHANGE;

		$notificationCategories[$valueChangeNotificationType] = array(
			'priority' => 5,
			'tooltip' => 'echo-pref-tooltip-smw-value-change',
		);

		$notificationCategories[$specificationChangeNotificationType] = array(
			'priority' => 5,
			'tooltip' => 'echo-pref-tooltip-smw-specification-change',
		);

		$notifications[$valueChangeNotificationType] = array(
			EchoAttributeManager::ATTR_LOCATORS => [
				'\SMW\Notifications\ChangeNotification\UserLocator::doLocateEventSubscribers'
			],
			'category' => 'smw-value-change',
			'group' => 'neutral',
			'section' => 'alert',
			'presentation-model' => ChangeNotificationPresentationModel::class,
			'formatter-class' => ChangeNotificationFormatter::class,
			'title-message' => "notification-{$valueChangeNotificationType}",
			'title-params' => array(
				'agent', 'difflink', 'title'
			),
			'flyout-message' => "notification-{$valueChangeNotificationType}-flyout",
			'flyout-params' => array(
				'agent', 'difflink', 'title'
			),
			'payload' => array( 'extra' ),
			'bundle' => array(
				'web' => true,
				'email' => false
			),
			'email-subject-message' => "notification-{$valueChangeNotificationType}-email-subject",
			'email-subject-params' => array(
				'user',
				'agent'
			),
			'email-body-batch-message' => "notification-{$valueChangeNotificationType}-email-batch-body",
			'email-body-batch-params' => array(
				'title',
				'agent'
			),
			'icon' => $valueChangeNotificationType,
		);

		$notifications[$specificationChangeNotificationType] = array(
			EchoAttributeManager::ATTR_LOCATORS => [
				'\SMW\Notifications\ChangeNotification\UserLocator::doLocateEventSubscribers'
			],
			'category' => 'smw-specification-change',
			'group' => 'neutral',
			'section' => 'alert',
			'presentation-model' => ChangeNotificationPresentationModel::class,
			'formatter-class' => ChangeNotificationFormatter::class,
			'title-message' => "notification-{$valueChangeNotificationType}",
			'title-params' => array(
				'agent', 'difflink', 'title'
			),
			'flyout-message' => "notification-{$valueChangeNotificationType}-flyout",
			'flyout-params' => array(
				'agent', 'difflink', 'title'
			),
			'payload' => array( 'extra' ),
			'bundle' => array(
				'web' => true,
				'email' => false
			),
			'email-subject-message' => "notification-{$valueChangeNotificationType}-email-subject",
			'email-subject-params' => array(
				'user',
				'agent'
			),
			'email-body-batch-message' => "notification-{$valueChangeNotificationType}-email-batch-body",
			'email-body-batch-params' => array(
				'title',
				'agent',
				'item'
			)
		);

		$icons[$valueChangeNotificationType] = array(
			'path' => "SemanticNotifications/res/smw-entity-change-yellow.png"
		);

		// Resolved in ChangeNotificationPresentationModel::getIconType
		$icons[$specificationChangeNotificationType . '-property'] = array(
			'path' => "SemanticNotifications/res/smw-entity-change-blue.png"
		);

		$icons[$specificationChangeNotificationType . '-category'] = array(
			'path' => "SemanticNotifications/res/smw-entity-change-green.png"
		);
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide#Conventions
	 *
	 * @since 1.0
	 *
	 * @param array &$defaultOptions
	 *
	 */
	public function addDefaultOptions( array &$defaultOptions ) {
		$defaultOptions['echo-subscriptions-web-' . ChangeNotificationFilter::VALUE_CHANGE] = true;
		return true;
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide#Bundled_notification
	 *
	 * @since 1.0
	 *
	 * @param EchoEvent $event
	 * @param string &$bundleString
	 */
	public function getNotificationsBundle( EchoEvent $event, &$bundleString ) {

		$extra = $event->getExtra();

		if (
			$event->getType() === ChangeNotificationFilter::VALUE_CHANGE ||
			$event->getType() === ChangeNotificationFilter::SPECIFICATION_CHANGE ) {
			$bundleString = $extra['subject']->getHash() . $extra['revid'];
		}
	}

	/**
	 * @since 1.0
	 *
	 * @param array $event
	 *
	 * @return EchoEvent
	 */
	public function createEvent( array $event ) {

		if ( $event === array() ) {
			return;
		}

		wfDebugLog( 'smw', 'EchoEvent triggered' );

		return EchoEvent::create( $event );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide#Create_the_notification_via_the_Echo_extension
	 *
	 * @since 1.0
	 *
	 * @param EchoEvent $event
	 * @param array &$users
	 */
	public function getDefaultRecipientsByType( EchoEvent $event, &$users ) {

		$extra = $event->getExtra();

		if ( $event->getType() === ChangeNotificationFilter::VALUE_CHANGE ) {
			if ( isset( $extra['recipients'] ) ) {
				foreach ( $extra['recipients'] as $key => $recipient ) {
					$users[] = \User::newFromName( $recipient, false );
				}
			}
		}
	}

}
