<?php

namespace SMW\Notifications;

use SMW\PropertyRegistry as SemanticMediaWikiPropertyRegistry;
use SMW\Notifications\DataValues\NotificationGroupValue;

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
	 * @param SemanticMediaWikiPropertyRegistry $propertyRegistry
	 *
	 * @return boolean
	 */
	public function registerTo( SemanticMediaWikiPropertyRegistry $propertyRegistry ) {

		$propertyDefinitions = array(

			self::NOTIFICATIONS_ON => array(
				'label' => SMW_NOTIFICATIONS_ON,
				'type'  => '_txt',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-on' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-on',
				'visible' => true,
				'annotable' => true
			),

			self::NOTIFICATIONS_TO_GROUP => array(
				'label' => SMW_NOTIFICATIONS_TO_GROUP,
				'type'  => '_txt',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-to-group' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-to-group',
				'visible' => true,
				'annotable' => true
			),

			self::NOTIFICATIONS_GROUP_MEMBER_OF => array(
				'label' => SMW_NOTIFICATIONS_GROUP_MEMBER_OF,
				'type'  => NotificationGroupValue::TYPE_ID,
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-group-member-of' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-group',
				'visible' => true,
				'annotable' => true
			),

			self::NOTIFICATIONS_TO => array(
				'label' => SMW_NOTIFICATIONS_TO,
				'type'  => '_wpg',
				'alias' => array( wfMessage( 'smw-notifications-property-alias-notifications-to' )->text() ),
				'msgkey' => 'smw-notifications-property-alias-notifications-to',
				'visible' => true,
				'annotable' => true
			)
		);

		foreach ( $propertyDefinitions as $id => $definition ) {
			$this->addPropertyDefinition( $propertyRegistry, $id, $definition );
		}

		foreach ( $propertyDefinitions as $id => $definition ) {
			// 2.4+
			if ( method_exists( $propertyRegistry, 'registerPropertyAliasByMsgKey' ) ) {
				$propertyRegistry->registerPropertyAliasByMsgKey(
					$id,
					$definition['msgkey']
				);
			}
		}

		return true;
	}

	private function addPropertyDefinition( $propertyRegistry, $id, $definition ) {

		$propertyRegistry->registerProperty(
			$id,
			$definition['type'],
			$definition['label'],
			$definition['visible'],
			$definition['annotable']
		);

		foreach ( $definition['alias'] as $alias ) {
			$propertyRegistry->registerPropertyAlias(
				$id,
				$alias
			);
		}
	}

}
