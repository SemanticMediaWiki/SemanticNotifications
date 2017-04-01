<?php

namespace SMW\Notifications\DataValues;

use SMW\DataValueFactory;
use SMW\ApplicationFactory;
use SMW\DIProperty;
use SMW\Message;
use SMW\RequestOptions;
use SMW\Notifications\PropertyRegistry;
use SMWStringValue as StringValue;
use SMWDIBlob as DIBlob;
use SMWDataValue as DataValue;
use Html;
use SpecialPage;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class NotificationGroupValue extends StringValue {

	/**
	 * DV identifier
	 */
	const TYPE_ID = '_notification_group';

	/**
	 * Special group name
	 */
	const SPECIAL_GROUP = 'smw-notifications-entity-specification-change-group';

	/**
	 * Get name in a possible localized representation
	 *
	 * @since 1.0
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public static function getSpecialGroupName( $language = Message::CONTENT_LANGUAGE ) {
		return Message::get( self::SPECIAL_GROUP, Message::TEXT, $language );
	}

	/**
	 * @see StringValue::parseUserValue
	 */
	protected function parseUserValue( $value ) {

		$inputValue = trim( $value );

		// Special group to watch property changes
		if ( mb_strtolower( $inputValue ) === self::getSpecialGroupName() ) {
			return parent::parseUserValue( $value );;
		}

		if ( !$this->isKnownNotificationsGroup( $inputValue ) ) {
			$this->addErrorMsg( array( 'smw-notifications-datavalue-invalid-group', $inputValue ), Message::PARSE );
			$this->m_dataitem = new DIBlob( 'ERROR' );
			return;
		}

		// If it has no contextPage it is most likely linked from a Special page
		// such as Special:SearchByProperty or Special:Ask
		if ( $this->getContextPage() !== null && !$this->hasUserContext() ) {
			$this->addErrorMsg( array( 'smw-notifications-datavalue-restricted-to-user', $inputValue ), Message::PARSE );
			$this->m_dataitem = new DIBlob( 'ERROR' );
			return;
		}

		parent::parseUserValue( $value );
	}

	/**
	 * @see StringValue::getShortWikiText
	 */
	public function getShortWikiText( $linker = null ) {

		if ( !$this->isValid() ) {
			return '';
		}

		if ( !$this->m_caption ) {
			$this->m_caption = $this->m_dataitem->getString();
		}

		if ( $linker === null ) {
			return $this->m_caption;
		}

	//	return Html::rawElement(
	//		'span',
	//		array(),
	//		'[' . $this->getTargetLink( urlencode( $this->m_caption ) ) . ' ' . $this->m_caption .']'
	//	);

		$text = SpecialPage::getTitleFor( 'SearchByProperty' )->getPrefixedText();

		// This is a bit heavy handed ...
		return '[[' . $text . '/' . SMW_NOTIFICATIONS_TO_GROUP . '/' . rawurlencode( $this->m_caption ) . '|' . $this->m_caption . ']]';
	}

	/**
	 * @see StringValue::getShortHTMLText
	 */
	public function getShortHTMLText( $linker = null ) {

		if ( !$this->isValid() ) {
			return '';
		}

		if ( !$this->m_caption ) {
			$this->m_caption = $this->m_dataitem->getString();
		}

		if ( $linker === null ) {
			return $this->m_caption;
		}

		$url = SpecialPage::getTitleFor( 'SearchByProperty' )->getLocalUrl(
			array(
				'property' => SMW_NOTIFICATIONS_TO_GROUP,
				'value' => $this->m_caption
			)
		);

		return Html::rawElement(
			'a',
			array(
				'href'   => $url,
			//	'target' => '_blank'
			),
			$this->m_caption
		);
	}

	/**
	 * @see StringValue::getLongWikiText
	 */
	public function getLongWikiText( $linked = null ) {
		return $this->getShortWikiText( $linked );
	}

	/**
	 * @see StringValue::getLongHTMLText
	 */
	public function getLongHTMLText( $linker = null ) {
		return $this->getShortHTMLText( $linker );
	}

	private function isKnownNotificationsGroup( $value ) {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_TO_GROUP
		);

		$requestOptions = new RequestOptions();
		$requestOptions->limit = 1;

		// Is a known group?
		$propertyValues = ApplicationFactory::getInstance()->getStore()->getPropertySubjects(
			$property,
			new DIBlob( $value ),
			$requestOptions
		);

		return $propertyValues !== array();
	}

	private function hasUserContext() {
		return $this->getContextPage() !== null && $this->getContextPage()->getNamespace() === NS_USER;
	}

}
