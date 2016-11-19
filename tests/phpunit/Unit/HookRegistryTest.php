<?php

namespace SMW\Notifications\Tests;

use SMW\DataTypeRegistry;
use SMW\DIWikiPage;
use SMW\Notifications\HookRegistry;
use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\SQLStore\CompositePropertyTableDiffIterator;
use SMWSQLStore3;

/**
 * @covers \SMW\Notifications\HookRegistry
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testRegister() {

		$instance = new HookRegistry();
		$instance->register();

		$this->doTestRegisteredSMWPropertyInitProperties( $instance );
		$this->doTestRegisteredSMWDataTypeInitTypes( $instance );
		$this->doTestRegisteredUserGetDefaultOptions( $instance );
		$this->doTestRegisteredBeforeCreateEchoEvent( $instance );
		$this->doTestRegisteredEchoGetBundleRules( $instance );
		$this->doTestRegisteredEchoGetDefaultNotifiedUsers( $instance );
		$this->doTestRegisteredSMWSQLStoreAfterDataUpdateComplete( $instance );
	}

	public function doTestRegisteredSMWPropertyInitProperties( $instance ) {

		$handler = 'SMW::Property::initProperties';

		$propertyRegistry = $this->getMockBuilder( PropertyRegistry::class )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $propertyRegistry )
		);
	}

	public function doTestRegisteredSMWDataTypeInitTypes( $instance ) {

		$handler = 'SMW::DataType::initTypes';

		$dataTypeRegistry = $this->getMockBuilder( DataTypeRegistry::class )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $dataTypeRegistry )
		);
	}

	public function doTestRegisteredUserGetDefaultOptions( $instance ) {

		$handler = 'UserGetDefaultOptions';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$defaultOptions = array();

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$defaultOptions )
		);

		$this->assertNotEmpty(
			$defaultOptions
		);
	}

	public function doTestRegisteredBeforeCreateEchoEvent( $instance ) {

		$handler = 'BeforeCreateEchoEvent';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$notifications = array();
		$notificationCategories = array();
		$icons = array();

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( &$notifications, &$notificationCategories, &$icons )
		);

		$this->assertNotEmpty(
			$notifications
		);

		$this->assertNotEmpty(
			$notificationCategories
		);

		$this->assertNotEmpty(
			$icons
		);
	}

	public function doTestRegisteredEchoGetBundleRules( $instance ) {

		$handler = 'EchoGetBundleRules';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$echoEvent = $this->getMockBuilder( \EchoEvent::class )
			->disableOriginalConstructor()
			->getMock();

		$bundleString = '';

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $echoEvent, &$bundleString )
		);
	}

	public function doTestRegisteredEchoGetDefaultNotifiedUsers( $instance ) {

		$handler = 'EchoGetDefaultNotifiedUsers';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$echoEvent = $this->getMockBuilder( \EchoEvent::class )
			->disableOriginalConstructor()
			->getMock();

		$users = array();

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $echoEvent, &$users )
		);
	}

	public function doTestRegisteredSMWSQLStoreAfterDataUpdateComplete( $instance ) {

		$handler = 'SMW::SQLStore::AfterDataUpdateComplete';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$subject = $this->getMockBuilder( DIWikiPage::class )
			->disableOriginalConstructor()
			->getMock();

		$store = $this->getMockBuilder( SMWSQLStore3::class )
			->disableOriginalConstructor()
			->getMock();

		$semanticData = $this->getMockBuilder( SemanticData::class )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getSubject' )
			->will( $this->returnValue( $subject ) );

		$compositePropertyTableDiffIterator = $this->getMockBuilder( CompositePropertyTableDiffIterator::class )
			->disableOriginalConstructor()
			->getMock();

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			array( $store, $semanticData, $compositePropertyTableDiffIterator )
		);
	}

	private function assertThatHookIsExcutable( \Closure $handler, $arguments ) {
		$this->assertInternalType(
			'boolean',
			call_user_func_array( $handler, $arguments )
		);
	}

}
