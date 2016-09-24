<?php

namespace SMW\Notifications\Tests;

use SMW\Notifications\DataValues\NotificationGroupValue;
use SMW\Notifications\PropertyRegistry;
use SMW\DIWikiPage;
use SMW\DIProperty;
use SMWDIBlob as DIBlob;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Notifications\DataValues\NotificationGroupValue
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class NotificationGroupValueTest extends \PHPUnit_Framework_TestCase {

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
			NotificationGroupValue::class,
			new NotificationGroupValue( '' )
		);
	}

	public function testSpecialGroup() {

		$this->assertSame(
			'entity specification change group',
			NotificationGroupValue::getSpecialGroupName( 'en' )
		);
	}

	public function testTryToSetUserValueOnMissingContext() {

		$instance = new NotificationGroupValue( '' );
		$instance->setUserValue( 'Foo' );

		$this->assertSame(
			'Foo',
			$instance->getWikiValue()
		);
	}

	public function testTryToSetUserValueOnMissingUserContext() {

		$instance = new NotificationGroupValue( '' );

		$instance->setContextPage(
			DIWikiPage::newFromText( __METHOD__ )
		);

		$instance->setUserValue( 'Foo' );

		$this->assertSame(
			'error',
			$instance->getWikiValue()
		);
	}

	public function testSetUserValueWithSpecialGroup() {

		$instance = new NotificationGroupValue( '' );
		$instance->setUserValue( NotificationGroupValue::getSpecialGroupName( 'en' ) );

		$this->assertSame(
			NotificationGroupValue::getSpecialGroupName( 'en' ),
			$instance->getWikiValue()
		);
	}

	public function testSetUserValueWithValidUserContext() {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_TO_GROUP
		);

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->anything() )
			->will( $this->returnValue( array( 'OnlyCompareWhetherItIsAvailableOrNot' ) ) );

		$instance = new NotificationGroupValue( '' );

		$instance->setContextPage(
			DIWikiPage::newFromText( __METHOD__, NS_USER )
		);

		$instance->setUserValue( 'Foo' );

		$this->assertSame(
			'Foo',
			$instance->getWikiValue()
		);
	}

	public function testTryToSetUserValueOnUnknownGroup() {

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_TO_GROUP
		);

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->anything() )
			->will( $this->returnValue( array() ) );

		$instance = new NotificationGroupValue( '' );

		$instance->setContextPage(
			DIWikiPage::newFromText( __METHOD__, NS_USER )
		);

		$instance->setUserValue( 'Foo' );

		$this->assertSame(
			'error',
			$instance->getWikiValue()
		);
	}

	public function testSetUserValueWithValidUserContextAndNotNullLinker() {

		$this->store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->will( $this->returnValue( array( 'WasMentionedAsGroup' ) ) );

		$instance = new NotificationGroupValue( '' );

		$instance->setContextPage(
			DIWikiPage::newFromText( __METHOD__, NS_USER )
		);

		$instance->setUserValue( 'Foo bar' );
		$instance->setCaption( false );

		$this->assertContains(
			SMW_NOTIFICATIONS_TO_GROUP . '/' . 'Foo%20bar',
			$instance->getShortWikiText( '' )
		);

		$this->assertContains(
			SMW_NOTIFICATIONS_TO_GROUP . '/' . 'Foo%20bar',
			$instance->getLongWikiText( '' )
		);

		$this->assertContains(
			'value=Foo+bar',
			$instance->getShortHTMLText( '' )
		);

		$this->assertContains(
			'value=Foo+bar',
			$instance->getLongHTMLText( '' )
		);
	}

	public function testSetDataItem() {

		$instance = new NotificationGroupValue( '' );

		$instance->setDataItem(
			new DIBlob( 'Foo' )
		);

		$this->assertContains(
			SMW_NOTIFICATIONS_TO_GROUP . '/' . 'Foo',
			$instance->getShortWikiText( '' )
		);

		$this->assertContains(
			SMW_NOTIFICATIONS_TO_GROUP . '/' . 'Foo',
			$instance->getLongWikiText( '' )
		);

		$this->assertContains(
			'value=Foo',
			$instance->getShortHTMLText( '' )
		);

		$this->assertContains(
			'value=Foo',
			$instance->getLongHTMLText( '' )
		);
	}

	public function testEmptyDataValue() {

		$instance = new NotificationGroupValue( '' );

		$this->assertEmpty(
			'',
			$instance->getShortWikiText()
		);

		$this->assertEmpty(
			'',
			$instance->getShortHTMLText()
		);
	}

}
