<?php

namespace SMW\Notifications;

use EchoEvent;
use EchoAttributeManager;
use SMW\Notifications\ValueChange\ChangeNotifications;
use SMW\Notifications\ValueChange\ChangeNotificationFormatter;
use SMW\Notifications\ValueChange\ChangeNotificationPresentationModel;

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
	 *
	 */
	public function addNotificationsDefinitions( array &$notifications, array &$notificationCategories, array &$icons ) {

		$valueChangeNotificationType = ChangeNotifications::VALUE_CHANGE;
		$specificationChangeNotificationType = ChangeNotifications::SPECIFICATION_CHANGE;

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
				'\SMW\Notifications\ValueChange\UserLocator::doLocateEventSubscribers'
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
				'agent',
				'page-count'
			),
			'email-body-batch-message' => "notification-{$valueChangeNotificationType}-email-batch-body",
			'email-body-batch-params' => array(
				'title',
				'agent',
				'item'
			),
			'icon' => $valueChangeNotificationType,
		);

		$notifications[$specificationChangeNotificationType] = array(
			EchoAttributeManager::ATTR_LOCATORS => [
				'\SMW\Notifications\ValueChange\UserLocator::doLocateEventSubscribers'
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
				'agent',
				'page-count'
			),
			'email-body-batch-message' => "notification-{$valueChangeNotificationType}-email-batch-body",
			'email-body-batch-params' => array(
				'title',
				'agent',
				'item'
			)
		);

		$icons[$valueChangeNotificationType] = array(
			'path' => "SemanticNotifications/res/smw-value-change.png"
		);

		// Resolved in ChangeNotificationPresentationModel::getIconType
		$icons[$specificationChangeNotificationType . '-property'] = array(
			'path' => "SemanticNotifications/res/smw-property-change.png"
		);

		$icons[$specificationChangeNotificationType . '-category'] = array(
			'path' => "SemanticNotifications/res/smw-category-change.png"
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
		$defaultOptions['echo-subscriptions-web-' . ChangeNotifications::VALUE_CHANGE] = true;
		return true;
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Notifications/Developer_guide#Bundled_notification
	 *
	 * @since 1.0
	 *
	 * @param EchoEvent $event
	 * @param string &$bundleString
	 *
	 */
	public function getNotificationsBundle( EchoEvent $event, &$bundleString ) {

		$extra = $event->getExtra();

		if ( $event->getType() === ChangeNotifications::VALUE_CHANGE ) {
			$bundleString = $extra['subject']->getHash() . $extra['revid'];
		}
	}

	/**
	 * @since 1.0
	 *
	 * @param array &$event
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

		if ( $event->getType() === ChangeNotifications::VALUE_CHANGE ) {
			if ( isset( $extra['recipients'] ) ) {
				foreach ( $extra['recipients'] as $key => $recipient ) {
					$users[] = \User::newFromName( $recipient, false );
				}
			}
		}
	}

}
