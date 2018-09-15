<?php

namespace SMW\Notifications\ChangeNotification;

use EchoEvent;
use EchoEventPresentationModel;
use Title;
use SMW\DIProperty;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChangeNotificationPresentationModel extends EchoEventPresentationModel {

	/**
	 * @see EchoEventPresentationModel::getIconType
	 */
	public function getIconType() {

		if ( $this->event->getType() !== ChangeNotificationFilter::SPECIFICATION_CHANGE ) {
			return $this->event->getType();
		}

		$namespace = $this->event->getTitle()->getNamespace();

		// @see EchoNotificationsManager::addNotificationsDefinitions for the icons
		if ( $namespace === NS_CATEGORY || $namespace === SMW_NS_CONCEPT ) {
			return $this->event->getType() . '-category';
		}

		return $this->event->getType() . '-property';
	}

	/**
	 * @see EchoEventPresentationModel::canRender
	 */
	public function canRender() {
		return (bool)$this->event->getTitle();
	}

	/**
	 * @param EchoEvent
	 * @return string
	 */
	public function callbackForBundleCount( EchoEvent $event ) {
		return $event->getTitle()->getPrefixedText();
	}

	/**
	 * @see EchoEventPresentationModel::getHeaderMessageKey
	 */
	public function getHeaderMessageKey() {

		if ( $this->getBundleCount( true, [ $this, 'callbackForBundleCount' ] ) > 1 ) {
			return "notification-bundle-header-{$this->type}";
		}

		return "notification-header-{$this->type}";
	}

	/**
	 * @see EchoEventPresentationModel::getHeaderMessage
	 */
	public function getHeaderMessage() {

		$extra = $this->event->getExtra();
		$msg = parent::getHeaderMessage();

		$msg->params(
			$this->getTruncatedTitleText( $this->event->getTitle(), true )
		);

		// Plural indicator
		$msg->params(
			( isset( $extra['properties'] ) ? count( $extra['properties'] ) : 0 )
		);

		$count = $this->getNotificationCountForOutput(
			false, // we need only other pages count
			[ $this, 'callbackForBundleCount' ]
		);

		if ( $count > 0 ) {
			$msg->numParams( $count );
		}

		return $msg;
	}

	/**
	 * @see EchoEventPresentationModel::getBodyMessage
	 */
	public function getBodyMessage() {

		$extra = $this->event->getExtra();
		$labels = array();

		if ( !isset( $extra['properties'] ) || $extra['properties'] === array() ) {
			return false;
		}

		foreach ( $extra['properties'] as $dataItem ) {

			$prefix = '';

			if ( $dataItem->getDBKey() === '' ) {
				continue;
			}

			if ( $dataItem->getNamespace() === NS_CATEGORY ) {
				$prefix = $this->language->getNsText( NS_CATEGORY ) . ':';
			}

			try {
				$labels[] = $prefix . DIProperty::newFromUserLabel( $dataItem->getDBKey() )->getLabel();
			} catch ( \SMW\Exception\PredefinedPropertyLabelMismatchException $e ) {
				continue;
			}
		}

		$msg = wfMessage( "notification-body-{$this->type}" );
		$msg->params( $this->language->listToText( $labels ) );
		$msg->params( count( $labels ) );

		return $msg;
	}

	/**
	 * @see EchoEventPresentationModel::getPrimaryLink
	 */
	public function getPrimaryLink() {
		$title = $this->event->getTitle();
		return array(
			'url' => $title->getFullURL(),
			'label' => $title->getFullText()
		);
	}

	/**
	 * @see EchoEventPresentationModel::getSecondaryLinks
	 */
	public function getSecondaryLinks() {

		$extra = $this->event->getExtra();
		$title = $this->event->getTitle();

		$queryParameters['oldid'] = 'prev';

		if ( $this->event->getExtraParam( 'revid' ) > 0 ) {
			$queryParameters['diff'] = $this->event->getExtraParam( 'revid' );
		}

		$viewChangesLink = array(
			'url'   => $title->getLocalURL( $queryParameters ),
			'label' => $this->msg( 'notification-link-text-view-changes', $this->getViewingUserForGender() )->text(),
			'description' => '',
			'icon' => 'changes',
			'prioritized' => true,
		);

		return array( $this->getAgentLink(), $viewChangesLink );
	}

}
