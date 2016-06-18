<?php

namespace SMW\Notifications\Tests;

use SMW\Notifications\ValueChange\UserLocator;
use SMW\Notifications\ValueChange\ChangeNotifications;
use SMW\DIWikiPage;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Notifications\ValueChange\UserLocator
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class UserLocatorTest extends \PHPUnit_Framework_TestCase {

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

	public function testCanConstruct() {

		$this->assertInstanceOf(
			UserLocator::class,
			new UserLocator()
		);
	}

	public function testDoLocateEventSubscribersOnEmptyExtra() {

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertEmpty(
			UserLocator::doLocateEventSubscribers( $echoEvent )
		);
	}

	/**
	 * @dataProvider changeTypeProvider
	 */
	public function testDoLocateEventSubscribersOnChange( $type ) {

		$extra = array(
			'subject'    => DIWikiPage::newFromText( __METHOD__ ),
			'properties' => array()
		);

		$agent = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->once() )
			->method( 'getExtra' )
			->will( $this->returnValue( $extra ) );

		$echoEvent->expects( $this->atLeastOnce() )
			->method( 'getAgent' )
			->will( $this->returnValue( $agent ) );

		$echoEvent->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$it =  UserLocator::doLocateEventSubscribers( $echoEvent );

		$this->assertInstanceOf(
			'\Iterator',
			$it
		);
	}

	public function testIterationOnPropertyChangeGroup() {

		$extra = array(
			'subject'    => DIWikiPage::newFromText( __METHOD__ ),
			'properties' => array()
		);

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->will( $this->returnValue( array( DIWikiPage::newFromText( 'UserBar', NS_USER ) ) ) );

		$agent = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->once() )
			->method( 'getExtra' )
			->will( $this->returnValue( $extra ) );

		$echoEvent->expects( $this->atLeastOnce() )
			->method( 'getAgent' )
			->will( $this->returnValue( $agent ) );

		$echoEvent->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( ChangeNotifications::SPECIFICATION_CHANGE ) );

		$it = UserLocator::doLocateEventSubscribers( $echoEvent );

		foreach ( $it as $user ) {
			$this->assertSame(
				'UserBar',
				$user->getName()
			);
		}
	}

	public function changeTypeProvider() {

		$provider[ChangeNotifications::SPECIFICATION_CHANGE] = array(
			ChangeNotifications::SPECIFICATION_CHANGE
		);

		$provider[ChangeNotifications::VALUE_CHANGE] = array(
			ChangeNotifications::VALUE_CHANGE
		);

		return $provider;
	}

}
