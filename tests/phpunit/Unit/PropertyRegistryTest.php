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

	public function testCanConstruct() {

		$this->assertInstanceOf(
			PropertyRegistry::class,
			new PropertyRegistry()
		);
	}

	public function testRegister() {

		$propertyRegistry = $this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$propertyRegistry->expects( $this->atLeastOnce() )
			->method( 'registerProperty' )
			->withConsecutive(
				[ $this->equalTo( PropertyRegistry::NOTIFICATIONS_ON ) ],
				[ $this->equalTo( PropertyRegistry::NOTIFICATIONS_TO_GROUP ) ],
				[ $this->equalTo( PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF ) ],
				[ $this->equalTo( PropertyRegistry::NOTIFICATIONS_TO ) ]
			);

		$instance = new PropertyRegistry();
		$instance->registerTo( $propertyRegistry );
	}

}
