<?php

namespace SMW\Notifications\ChangeNotification;

use SMW\Notifications\PropertyRegistry;
use SMW\ApplicationFactory;
use SMW\Notifications\DataValues\NotificationGroupValue;
use SMW\Store;
use SMW\DIProperty;
use SMWDIBlob as DIBlob;
use SMWDataItem as DataItem;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class NotificationGroupsLocator {

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @since 1.0
	 *
	 * @param  Store $store
	 */
	public function __construct( Store $store ) {
		$this->store = $store;
	}

	/**
	 * Special group
	 *
	 * @since 1.0
	 *
	 * @return []
	 */
	public function getSpecialGroupOnSpecificationChange() {
		return array(
			NotificationGroupValue::SPECIAL_GROUP => array( new DIBlob( NotificationGroupValue::getSpecialGroupName() ) )
		);
	}

	/**
	 * @since 1.0
	 *
	 * @param DataItem $dataItem
	 * @param array $subSemanticDataMatch
	 *
	 * @return DataItem[]
	 */
	public function findNotificationsToGroupList( DataItem $dataItem, array $subSemanticDataMatch ) {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_TO_GROUP
		);

		// Either match the property on a plain assignment or
		if ( ( $pv = $this->store->getPropertyValues( $dataItem, $property ) ) !== array() ) {
			return $pv;
		}

		// Find out whether a detection matrix was build using a subobject
		$semanticData = $this->store->getSemanticData(
			$dataItem
		);

		$propertyValues = array();

		// Get the group from the subobject that contained the matchable
		// condition from the preceding filterOnChange process
		foreach ( $subSemanticDataMatch as $hash => $value ) {
			foreach ( $value as $sobj ) {
				if (
					( $hash === $dataItem->getHash() ) &&
					( $subSemanticData = $semanticData->findSubSemanticData( $sobj ) ) !== array() ) {
					$propertyValues = array_merge( $propertyValues, $subSemanticData->getPropertyValues( $property ) );
				}
			}
		}

		return $propertyValues;
	}

	/**
	 * @since 1.0
	 *
	 * @param array $subSemanticDataMatch
	 *
	 * @return Closure
	 */
	public function getNotificationsToGroupListAsCallback( array $subSemanticDataMatch ) {
		return function( $dataItem ) use( $subSemanticDataMatch ) {
			return $this->findNotificationsToGroupList( $dataItem, $subSemanticDataMatch );
		};
	}

}
