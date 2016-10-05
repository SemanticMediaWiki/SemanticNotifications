<?php

namespace SMW\Notifications\Iterators\Tests;

use SMW\Notifications\Iterators\RecursiveMembersIterator;
use SMW\Tests\TestEnvironment;
use RecursiveIteratorIterator;
use SMW\Notifications\PropertyRegistry;
use SMW\DIWikiPage;
use SMW\DIProperty;
use SMWDIBlob as DIBlob;
use ArrayIterator;

/**
 * @covers \SMW\Notifications\Iterators\RecursiveMembersIterator
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class RecursiveMembersIteratorTest extends \PHPUnit_Framework_TestCase {

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
			RecursiveMembersIterator::class,
			new RecursiveMembersIterator( array(), $this->store )
		);
	}

	public function testEmptyGroup() {

		$expected = array();

		$instance = new RecursiveMembersIterator(
			array(),
			$this->store
		);

		$this->assertEmpty(
			$instance->next()
		);
	}

	/**
	 * @dataProvider singleGroupProvider
	 */
	public function testFetchUsersByGroupIteration( $group ) {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF
		);

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->anything() )
			->will( $this->returnValue( array( DIWikiPage::newFromText( 'Bar', NS_USER ) ) ) );

		$instance = new RecursiveMembersIterator(
			$group,
			$this->store
		);

		$this->assertEmpty(
			$instance->next()
		);

		$this->assertEquals(
			array( 'Bar' ),
			$instance->current()
		);
	}

	/**
	 * @dataProvider singleGroupProvider
	 */
	public function testFetchUsersByGroupIterationButExcludeAgent( $group ) {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF
		);

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->anything() )
			->will( $this->returnValue( array(
				DIWikiPage::newFromText( 'Bar', NS_USER ),
				DIWikiPage::newFromText( 'Tanaka Hiro', NS_USER ) ) ) );

		$instance = new RecursiveMembersIterator(
			$group,
			$this->store
		);

		$instance->setAgentName(
			'Tanaka Hiro'
		);

		$this->assertEmpty(
			$instance->next()
		);

		$this->assertEquals(
			array( 'Bar' ),
			$instance->current()
		);
	}

	/**
	 * @dataProvider singleGroupProvider
	 */
	public function testAllowToNotifAgent( $group ) {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF
		);

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->anything() )
			->will( $this->returnValue( array(
				DIWikiPage::newFromText( 'Bar', NS_USER ),
				DIWikiPage::newFromText( 'Foo', NS_USER ) ) ) );

		$instance = new RecursiveMembersIterator(
			$group,
			$this->store
		);

		$instance->notifyAgent(
			true
		);

		$instance->setAgentName(
			'Foo'
		);

		$this->assertEmpty(
			$instance->next()
		);

		$this->assertEquals(
			array( 'Bar', 'Foo' ),
			$instance->current()
		);
	}

	/**
	 * @dataProvider multiGroupProvider
	 */
	public function testFetchUsersByMultiGroupIteration( $group, $count ) {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF
		);

		$this->store->expects( $this->exactly( $count ) )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->anything() )
			->will( $this->returnValue( array( DIWikiPage::newFromText( 'Bar', NS_USER ) ) ) );

		$this->store->expects( $this->any() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array() ) );

		$instance = new RecursiveMembersIterator(
			$group,
			$this->store
		);

		$instance->setSubject(
			DIWikiPage::newFromText( __METHOD__ )
		);

		foreach ( $instance as $value ) {
			$this->assertEquals(
				array( 'Bar' ),
				$value
			);
		}
	}

	public function testFetchUsersByNotificationsTo() {

		$group = array();

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_TO
		);

		$subject = DIWikiPage::newFromText( __METHOD__ );

		$this->store->expects( $this->exactly( 1 ) )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( $subject ),
				$this->equalTo( $property ) )
			->will( $this->returnValue( array( DIWikiPage::newFromText( 'Bar', NS_USER ) ) ) );

		$this->store->expects( $this->any() )
			->method( 'getPropertySubjects' )
			->will( $this->returnValue( array() ) );

		$instance = new RecursiveMembersIterator(
			$group,
			$this->store
		);

		$instance->setSubject(
			$subject
		);

		foreach ( $instance as $value ) {
			$this->assertEquals(
				array( 'Bar' ),
				$value
			);
		}
	}

	public function singleGroupProvider() {

		$provider[] = array(
			array( 'Bar' => array( new DIBlob( 'Foo' ) ) )
		);

		$provider[] = array(
			array( new ArrayIterator( array( new DIBlob( 'Foo' ) ) ) )
		);

		return $provider;
	}

	public function multiGroupProvider() {

		$provider[] = array(
			new ArrayIterator( array( array( new DIBlob( 'Foo' ) ) ) ),
			1
		);

		$provider[] = array(
			array( 'Bar' => array(
				new DIBlob( 'Foo' ),
				new DIBlob( 'Foo-2' )
			) ),
			2
		);

		$provider[] = array(
			array(
				array(
					new DIBlob( 'Foo' ),
					new DIBlob( 'Foo-1' ),
					new DIBlob( 'Foo-2' ),
				)
			),
			3
		);

		return $provider;
	}

}
