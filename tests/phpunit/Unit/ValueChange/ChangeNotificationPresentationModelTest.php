<?php

namespace SMW\Notifications\Tests;

use SMW\Notifications\ValueChange\ChangeNotificationPresentationModel;
use SMW\Notifications\ValueChange\ChangeNotifications;
use SMW\DIWikiPage;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Notifications\ValueChange\ChangeNotificationPresentationModel
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChangeNotificationPresentationModelTest extends \PHPUnit_Framework_TestCase {

	private $store;
	private $testEnvironment;

	protected function setUp() {

		$this->store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->testEnvironment = new TestEnvironment();
		$this->testEnvironment->registerObject( 'Store', $this->store );
	}

	protected function tearDown() {
		$this->testEnvironment->tearDown();
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testCanConstruct( $type ) {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			ChangeNotificationPresentationModel::class,
			ChangeNotificationPresentationModel::factory( $echoEvent, 'en', $user )
		);
	}

	/**
	 * @dataProvider iconTypeProvider
	 */
	public function testGetIconType( $type, $subject, $expected ) {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$echoEvent->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $subject->getTitle() ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertSame(
			$expected,
			$instance->getIconType()
		);
	}

	public function testCanRender() {

		$subject = DIWikiPage::newFromText( 'Foo' );

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( ChangeNotifications::VALUE_CHANGE ) );

		$echoEvent->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $subject->getTitle() ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertInternalType(
			'boolean',
			$instance->canRender()
		);
	}

	public function testGetHeaderMessageKey() {

		$subject = DIWikiPage::newFromText( 'Foo' );

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( ChangeNotifications::VALUE_CHANGE ) );

		$echoEvent->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $subject->getTitle() ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertInternalType(
			'string',
			$instance->getHeaderMessageKey()
		);
	}

	public function testGetHeaderMessage() {

		$subject = DIWikiPage::newFromText( 'Foo' );

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( ChangeNotifications::VALUE_CHANGE ) );

		$echoEvent->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $subject->getTitle() ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertInstanceOf(
			'\Message',
			$instance->getHeaderMessage()
		);
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testGetBodyMessageOnNoProperties( $type , $subject ) {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertFalse(
			$instance->getBodyMessage()
		);
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testGetBodyMessageOnAvailableProperties( $type, $subject ) {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getExtra' )
			->will( $this->returnValue( array( 'properties' => array( $subject ) ) ) );

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertInstanceOf(
			'\Message',
			$instance->getBodyMessage()
		);
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testGetPrimaryLink( $type, $subject ) {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$echoEvent->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $subject->getTitle() ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertInternalType(
			'array',
			$instance->getPrimaryLink()
		);
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testGetSecondaryLinks( $type, $subject ) {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getExtraParam' )
			->with( $this->equalTo( 'revid' ) )
			->will( $this->returnValue( 42 ) );

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$echoEvent->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $subject->getTitle() ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = ChangeNotificationPresentationModel::factory(
			$echoEvent,
			'en',
			$user
		);

		$this->assertInternalType(
			'array',
			$instance->getSecondaryLinks()
		);
	}

	public function iconTypeProvider() {

		$provider['specification-prop'] = array(
			ChangeNotifications::SPECIFICATION_CHANGE,
			DIWikiPage::newFromText( 'Foo', SMW_NS_PROPERTY ),
			'smw-specification-change-property'
		);

		$provider['specification-cat'] = array(
			ChangeNotifications::SPECIFICATION_CHANGE,
			DIWikiPage::newFromText( 'Foo', NS_CATEGORY ),
			'smw-specification-change-category'
		);

		$provider['value'] = array(
			ChangeNotifications::VALUE_CHANGE,
			DIWikiPage::newFromText( 'Foo' ),
			'smw-value-change'
		);

		return $provider;
	}

	public function typeProvider() {

		$provider['specification-prop'] = array(
			ChangeNotifications::SPECIFICATION_CHANGE,
			DIWikiPage::newFromText( 'Foo', SMW_NS_PROPERTY )
		);

		$provider['specification-cat'] = array(
			ChangeNotifications::SPECIFICATION_CHANGE,
			DIWikiPage::newFromText( 'Foo', NS_CATEGORY )
		);

		$provider['value'] = array(
			ChangeNotifications::VALUE_CHANGE,
			DIWikiPage::newFromText( 'Foo' )
		);

		return $provider;
	}

}
