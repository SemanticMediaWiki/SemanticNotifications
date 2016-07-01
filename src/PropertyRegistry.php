<?php

namespace SMW\Notifications;

use SMW\PropertyRegistry as CorePropertyRegistry;

define( 'SMW_NOTIFICATIONS_ON', 'Notifications on' );
define( 'SMW_NOTIFICATIONS_TO_GROUP', 'Notifications to group' );
define( 'SMW_NOTIFICATIONS_GROUP_MEMBER_OF', 'Notifications group member of' );
define( 'SMW_NOTIFICATIONS_TO', 'Notifications to' );

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistry {

	/**
	 * Marking a property to be watched
	 */
	const NOTIFICATIONS_ON = "__notifications_on";

	/**
	 * Group assigned to the property to can point recipients
	 */
	const NOTIFICATIONS_TO_GROUP = "__notifications_to_group";

	/**
	 * Users assigned to a group
	 */
	const NOTIFICATIONS_GROUP_MEMBER_OF = "__notifications_group_member";

	/**
	 * List of individuals that can be listed directly
	 */
	const NOTIFICATIONS_TO = "__notifications_to";

	/**
	 * @since 1.0
	 *
	 * @param CorePropertyRegistry $propertyRegistry
	 *
	 * @return boolean
	 */
	public function register( CorePropertyRegistry $propertyRegistry ) {

		$propertyDefinitions = array(

			self::NOTIFICATIONS_ON => array(
				'label' => SMW_NOTIFICATIONS_ON,
				'type'  => '_txt',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-on' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-on',
				'visibility' => true,
				'annotableByUser'  => true
			),

			self::NOTIFICATIONS_TO_GROUP => array(
				'label' => SMW_NOTIFICATIONS_TO_GROUP,
				'type'  => '_txt',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-to-group' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-to-group',
				'visibility' => true,
				'annotableByUser'  => true
			),

			self::NOTIFICATIONS_GROUP_MEMBER_OF => array(
				'label' => SMW_NOTIFICATIONS_GROUP_MEMBER_OF,
				'type'  => '_notification_group',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-group-member-of' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-group',
				'visibility' => true,
				'annotableByUser'  => true
			),

			self::NOTIFICATIONS_TO => array(
				'label' => SMW_NOTIFICATIONS_TO,
				'type'  => '_txt',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-to' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-to',
				'visibility' => true,
				'annotableByUser'  => true
			)
		);

		foreach ( $propertyDefinitions as $propertyId => $definition ) {
			$this->addPropertyDefinitionFor( $propertyRegistry, $propertyId, $definition  );
		}

		foreach ( $propertyDefinitions as $propertyId => $definition ) {
			// 2.4+
			if ( method_exists( $propertyRegistry, 'registerPropertyAliasByMsgKey' ) ) {
				$propertyRegistry->registerPropertyAliasByMsgKey(
					$propertyId,
					$definition['msgkey']
				);
			}
		}

		return true;
	}

	private function addPropertyDefinitionFor( $propertyRegistry, $propertyId, $definition ) {

		$propertyRegistry->registerProperty(
			$propertyId,
			$definition['type'],
			$definition['label'],
			$definition['visibility'],
			$definition['annotableByUser']
		);

		foreach ( $definition['alias'] as $alias ) {
			$propertyRegistry->registerPropertyAlias(
				$propertyId,
				$alias
			);
		}
	}

}
