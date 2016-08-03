<?php

namespace SMW\Notifications\Tests\ChangeNotification;

use SMW\Notifications\ChangeNotification\ChangeNotificationFormatter;
use SMW\Notifications\ChangeNotification\ChangeNotificationFilter;
use SMW\DIWikiPage;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Notifications\ChangeNotification\ChangeNotificationFormatter
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChangeNotificationFormatterTest extends \PHPUnit_Framework_TestCase {

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

		$this->assertInstanceOf(
			ChangeNotificationFormatter::class,
			ChangeNotificationFormatter::factory( $type )
		);
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testFormatOnWebDistributionType( $type ) {

		$instance = ChangeNotificationFormatter::factory( $type );

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInternalType(
			'string',
			$instance->format( $echoEvent, $user, 'web' )
		);
	}

	/**
	 * @dataProvider typeProvider
	 */
	public function testFormatOnEmailDistributionType( $type ) {

		$instance = ChangeNotificationFormatter::factory( $type );

		$echoEvent = $this->getMockBuilder( '\EchoEvent' )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->any() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$user = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInternalType(
			'string',
			$instance->format( $echoEvent, $user, 'email' )
		);
	}

	public function typeProvider() {

		$provider['specification-prop'] = array(
			ChangeNotificationFilter::SPECIFICATION_CHANGE,
			DIWikiPage::newFromText( 'Foo', SMW_NS_PROPERTY )
		);

		$provider['specification-cat'] = array(
			ChangeNotificationFilter::SPECIFICATION_CHANGE,
			DIWikiPage::newFromText( 'Foo', NS_CATEGORY )
		);

		$provider['value'] = array(
			ChangeNotificationFilter::VALUE_CHANGE,
			DIWikiPage::newFromText( 'Foo' )
		);

		return $provider;
	}

}
