<?php

namespace SMW\Notifications\Tests\ChangeNotification;

use SMW\Notifications\ChangeNotification\NotificationGroupsLocator;
use SMW\Notifications\DataValues\NotificationGroupValue;
use SMW\DIWikiPage;

/**
 * @covers \SMW\Notifications\ChangeNotification\NotificationGroupsLocator
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class NotificationGroupsLocatorTest extends \PHPUnit_Framework_TestCase {

	private $store;

	protected function setUp() {

		$this->store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			NotificationGroupsLocator::class,
			new NotificationGroupsLocator( $this->store )
		);
	}

	public function testGetSpecialGroupOnSpecificationChange() {

		$instance = new NotificationGroupsLocator(
			$this->store
		);

		$this->assertArrayHasKey(
			NotificationGroupValue::SPECIAL_GROUP,
			$instance->getSpecialGroupOnSpecificationChange()
		);
	}

	public function testGetNotificationsToGroupListAsCallback() {

		$instance = new NotificationGroupsLocator(
			$this->store
		);

		$subSemanticDataMatch = array();

		$this->assertInstanceOf(
			'\Closure',
			$instance->getNotificationsToGroupListAsCallback( $subSemanticDataMatch )
		);
	}

	public function testFindNotificationsToGroupListOnEmptyValues() {

		$this->store->expects( $this->once() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array() ) );

		$dataItem = $this->getMockBuilder( '\SMW\DIWikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new NotificationGroupsLocator(
			$this->store
		);

		$subSemanticDataMatch = array();

		$this->assertEmpty(
			$instance->findNotificationsToGroupList( $dataItem, $subSemanticDataMatch )
		);
	}

	public function testFindNotificationsToGroupListOnSubSemanticDataReference() {

		$subSemanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$subSemanticData->expects( $this->once() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array( 'Bar' ) ) );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'findSubSemanticData' )
			->with(	$this->equalTo( 'Foo' ) )
			->will( $this->returnValue( $subSemanticData ) );

		$this->store->expects( $this->once() )
			->method( 'getSemanticData' )
			->will( $this->returnValue( $semanticData ) );

		$this->store->expects( $this->once() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array() ) );

		$dataItem = DIWikiPage::newFromText( __METHOD__ );

		$instance = new NotificationGroupsLocator(
			$this->store
		);

		$subSemanticDataMatch = array(
			$dataItem->getHash() => array( 'Foo' )
		);

		$this->assertEquals(
			array( 'Bar' ),
			$instance->findNotificationsToGroupList( $dataItem, $subSemanticDataMatch )
		);
	}

}
