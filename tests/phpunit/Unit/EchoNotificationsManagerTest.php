<?php

namespace SMW\Notifications\Tests;

use SMW\Notifications\EchoNotificationsManager;
use SMW\Notifications\ValueChange\ChangeNotifications;
use SMW\DIWikiPage;

/**
 * @covers \SMW\Notifications\EchoNotificationsManager
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class EchoNotificationsManagerTest extends \PHPUnit_Framework_TestCase {

	public function testAddNotificationsDefinitions() {

		$instance = new EchoNotificationsManager();

		$notifications = array();
		$notificationCategories = array();
		$icons = array();

		$instance->addNotificationsDefinitions(
			$notifications,
			$notificationCategories,
			$icons
		);

		$this->assertArrayHasKey(
			ChangeNotifications::VALUE_CHANGE,
			$notifications
		);

		$this->assertArrayHasKey(
			ChangeNotifications::SPECIFICATION_CHANGE,
			$notifications
		);

		$this->assertArrayHasKey(
			ChangeNotifications::VALUE_CHANGE,
			$notificationCategories
		);

		$this->assertArrayHasKey(
			ChangeNotifications::SPECIFICATION_CHANGE,
			$notificationCategories
		);

		$this->assertArrayHasKey(
			ChangeNotifications::VALUE_CHANGE,
			$icons
		);

		$this->assertArrayHasKey(
			ChangeNotifications::SPECIFICATION_CHANGE . '-property',
			$icons
		);

		$this->assertArrayHasKey(
			ChangeNotifications::SPECIFICATION_CHANGE . '-category',
			$icons
		);
	}

	public function testAddDefaultOptions() {

		$instance = new EchoNotificationsManager();

		$defaultOptions = array();

		$instance->addDefaultOptions(
			$defaultOptions
		);

		$this->assertArrayHasKey(
			'echo-subscriptions-web-' . ChangeNotifications::VALUE_CHANGE,
			$defaultOptions
		);
	}

	public function testGetNotificationsBundle() {

		$extra = array(
			'subject' => DIWikiPage::newFromText( 'Foo' ),
			'revid'   => 1001
		);

		$echoEvent = $this->getMockBuilder( \EchoEvent::class )
			->disableOriginalConstructor()
			->getMock();

		$echoEvent->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( ChangeNotifications::VALUE_CHANGE ) );

		$echoEvent->expects( $this->once() )
			->method( 'getExtra' )
			->will( $this->returnValue( $extra ) );

		$instance = new EchoNotificationsManager();

		$bundleString = '';

		$instance->getNotificationsBundle(
			$echoEvent,
			$bundleString
		);

		$this->assertSame(
			'Foo#0#1001',
			$bundleString
		);
	}

	public function testCreateEvent() {

		$agent = $this->getMockBuilder( \User::class )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( \Title::class )
			->disableOriginalConstructor()
			->getMock();

		$event = array(
			'agent' => $agent,
			'title' => $title,
			'type'  => ChangeNotifications::VALUE_CHANGE
		);

		$instance = new EchoNotificationsManager();

		$this->assertInstanceOf(
			\EchoEvent::class,
			$instance->createEvent( $event )
		);
	}

}
