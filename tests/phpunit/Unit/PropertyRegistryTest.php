<?php

namespace SMW\Notifications\Tests;

use SMW\Notifications\PropertyRegistry;
use SMW\DIProperty;

/**
 * @covers \SMW\Notifications\PropertyRegistry
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testRegistry() {

		$propertyRegistry = $this->getMockBuilder( \SMW\PropertyRegistry::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new PropertyRegistry();
		$instance->registerTo( $propertyRegistry );

		$this->assertSame(
			SMW_NOTIFICATIONS_ON,
			DIProperty::findPropertyLabel( PropertyRegistry::NOTIFICATIONS_ON )
		);

		$this->assertSame(
			SMW_NOTIFICATIONS_TO_GROUP,
			DIProperty::findPropertyLabel( PropertyRegistry::NOTIFICATIONS_TO_GROUP )
		);

		$this->assertSame(
			SMW_NOTIFICATIONS_GROUP_MEMBER_OF,
			DIProperty::findPropertyLabel( PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF )
		);

		$this->assertSame(
			SMW_NOTIFICATIONS_TO,
			DIProperty::findPropertyLabel( PropertyRegistry::NOTIFICATIONS_TO )
		);
	}

}
