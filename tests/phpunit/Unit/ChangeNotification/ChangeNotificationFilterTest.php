<?php

namespace SMW\Notifications\Tests\ChangeNotification;

use SMW\Notifications\ChangeNotification\ChangeNotificationFilter;
use SMW\DIWikiPage;
use SMWDIBlob as DIBlob;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Notifications\ChangeNotification\ChangeNotificationFilter
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChangeNotificationFilterTest extends \PHPUnit_Framework_TestCase {

	private $store;
	private $propertySpecificationLookup;
	private $testEnvironment;

	protected function setUp() {

		$this->store = $this->getMockBuilder( '\SMW\SQLStore\SQLStore' )
			->disableOriginalConstructor()
			->getMock();

		$this->propertySpecificationLookup = $this->getMockBuilder( '\SMW\PropertySpecificationLookup' )
			->disableOriginalConstructor()
			->getMock();

		$this->testEnvironment = new TestEnvironment();

		$this->testEnvironment->registerObject( 'Store', $this->store );
		$this->testEnvironment->registerObject( 'PropertySpecificationLookup', $this->propertySpecificationLookup );
	}

	protected function tearDown() {
		$this->testEnvironment->tearDown();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ChangeNotificationFilter::class,
			new ChangeNotificationFilter( DIWikiPage::newFromText( __METHOD__ ), $this->store )
		);
	}

	public function testTryToGetEventRecordOnNullType() {

		$instance = new ChangeNotificationFilter(
			DIWikiPage::newFromText( __METHOD__ ),
			$this->store
		);

		$this->assertEmpty(
			$instance->getEventRecord( true )
		);
	}

	public function testGetEventRecordOnPropertyChange() {

		$subject = DIWikiPage::newFromText( __METHOD__, SMW_NS_PROPERTY );
		$dataItem = DIWikiPage::newFromText( 'FOO', SMW_NS_PROPERTY );

		$orderedDiffByTable = array(
			'fpt_foo' => array(
				'property' => array(
					'key'  => '_FOO',
					'p_id' => 29
				),
				'insert' => array(
					array(
						's_id' => 201,
						'o_serialized' => '1/2016/6/1/11/1/48/0',
						'o_sortkey' => '2457540.9595833'
					)
				),
				'delete' => array(
					array(
						's_id' => 202,
						'o_serialized' => '1/2016/6/1/11/1/59/0',
						'o_sortkey' => '2457540.9582292'
					)
				)
			)
		);

		$idTable = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'getDataItemById' ) )
			->getMock();

		$idTable->expects( $this->any() )
			->method( 'getDataItemById' )
			->will( $this->returnValue( $dataItem ) );

		$this->store->expects( $this->any() )
			->method( 'getObjectIds' )
			->will( $this->returnValue( $idTable ) );

		$compositePropertyTableDiffIterator = $this->getMockBuilder( '\SMW\SQLStore\CompositePropertyTableDiffIterator' )
			->disableOriginalConstructor()
			->setMethods( array( 'getOrderedDiffByTable' ) )
			->getMock();

		$compositePropertyTableDiffIterator->expects( $this->any() )
			->method( 'getOrderedDiffByTable' )
			->will( $this->returnValue( $orderedDiffByTable ) );

		$instance = new ChangeNotificationFilter(
			$subject,
			$this->store
		);

		$instance->hasChangeToNotifyAbout(
			$compositePropertyTableDiffIterator
		);

		$this->assertEquals(
			array(
				'agent' => null,
				'extra' => array(
					'notifyAgent' => false,
					'revid' => 0,
					'properties' => array(
						'FOO#102##' => $dataItem
					),
					'subSemanticDataMatch' => array(),
					'subject' => $subject
				),
				'title' => $subject->getTitle(),
				'type' => 'smw-specification-change'
			),
			$instance->getEventRecord( true )
		);
	}

	public function testCannotNotifyOnChangeWhen_MDAT_REDI() {

		$subject = DIWikiPage::newFromText( __METHOD__ );

		$orderedDiffByTable = array(
			'fpt_mdat' => array(
				'property' => array(
					'key'  => '_MDAT',
					'p_id' => 29
				)
			),
			'fpt_redi' => array(
				'property' => array(
					'key'  => '_REDI',
					'p_id' => 290
				)
			)
		);

		$compositePropertyTableDiffIterator = $this->getMockBuilder( '\SMW\SQLStore\CompositePropertyTableDiffIterator' )
			->disableOriginalConstructor()
			->setMethods( array( 'getOrderedDiffByTable' ) )
			->getMock();

		$compositePropertyTableDiffIterator->expects( $this->any() )
			->method( 'getOrderedDiffByTable' )
			->will( $this->returnValue( $orderedDiffByTable ) );

		$instance = new ChangeNotificationFilter(
			$subject,
			$this->store
		);

		$instance->setPropertyExemptionList( array(
			'_MDAT',
			'_REDI'
		) );

		$result = $instance->hasChangeToNotifyAbout(
			$compositePropertyTableDiffIterator
		);

		$this->assertFalse(
			$result
		);
	}

	public function testhasChangeToNotifyAbout_ChangeNotificationForAnyValue() {

		$subject = DIWikiPage::newFromText( __METHOD__ );

		$orderedDiffByTable = array(
			'fpt_foo' => array(
				'property' => array(
					'key'  => '_FOO',
					'p_id' => 29
				),
				'insert' => array(
					array(
						's_id' => 201,
						'o_serialized' => '1/2016/6/1/11/1/48/0',
						'o_sortkey' => '2457540.9595833'
					)
				)
			)
		);

		$idTable = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'getDataItemById' ) )
			->getMock();

		$idTable->expects( $this->any() )
			->method( 'getDataItemById' )
			->will( $this->returnValue( DIWikiPage::newFromText( 'FOO', SMW_NS_PROPERTY ) ) );

		$this->store->expects( $this->any() )
			->method( 'getObjectIds' )
			->will( $this->returnValue( $idTable ) );

		$this->propertySpecificationLookup->expects( $this->any() )
			->method( 'getSpecification' )
			->will( $this->returnValue( array( new DIBlob( '+' ) ) ) ); //Any value

		$compositePropertyTableDiffIterator = $this->getMockBuilder( '\SMW\SQLStore\CompositePropertyTableDiffIterator' )
			->disableOriginalConstructor()
			->setMethods( array( 'getOrderedDiffByTable' ) )
			->getMock();

		$compositePropertyTableDiffIterator->expects( $this->any() )
			->method( 'getOrderedDiffByTable' )
			->will( $this->returnValue( $orderedDiffByTable ) );

		$agent = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ChangeNotificationFilter(
			$subject,
			$this->store
		);

		$instance->setAgent( $agent );

		$result = $instance->hasChangeToNotifyAbout(
			$compositePropertyTableDiffIterator
		);

		$this->assertTrue(
			$result
		);
	}

	public function testhasChangeToNotifyAbout_ChangeNotificationForDistinctValue() {

		$subject = DIWikiPage::newFromText( __METHOD__ );

		$orderedDiffByTable = array(
			'fpt_date' => array(
				'property' => array(
					'key'  => '_TEXT',
					'p_id' => 29
				),
				'insert' => array(
					array(
						's_id' => 201,
						'o_blob' => 'DistinctText',
						'o_hash' => ''
					)
				),
				'delete' => array(
					array(
						's_id' => 201,
						'o_blob' => 'Text',
						'o_hash' => ''
					)
				)
			)
		);

		$idTable = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'getDataItemById' ) )
			->getMock();

		$idTable->expects( $this->any() )
			->method( 'getDataItemById' )
			->will( $this->returnValue( DIWikiPage::newFromText( '_TEXT', SMW_NS_PROPERTY ) ) );

		$this->store->expects( $this->any() )
			->method( 'getObjectIds' )
			->will( $this->returnValue( $idTable ) );

		$this->propertySpecificationLookup->expects( $this->any() )
			->method( 'getSpecification' )
			->will( $this->returnValue( array( new DIBlob( 'DistinctText' ) ) ) ); //Distinct value

		$compositePropertyTableDiffIterator = $this->getMockBuilder( '\SMW\SQLStore\CompositePropertyTableDiffIterator' )
			->disableOriginalConstructor()
			->setMethods( array( 'getOrderedDiffByTable' ) )
			->getMock();

		$compositePropertyTableDiffIterator->expects( $this->any() )
			->method( 'getOrderedDiffByTable' )
			->will( $this->returnValue( $orderedDiffByTable ) );

		$agent = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ChangeNotificationFilter(
			$subject,
			$this->store
		);

		$instance->setAgent( $agent );

		$result = $instance->hasChangeToNotifyAbout(
			$compositePropertyTableDiffIterator
		);

		$this->assertTrue(
			$result
		);
	}

	public function testhasChangeToNotifyAbout_ChangeNotificationForPageValue() {

		$subject = DIWikiPage::newFromText( __METHOD__ );

		$orderedDiffByTable = array(
			'tab_id_page' => array(
				'insert' => array(
					array(
						'p_id' => 29,
						's_id' => 201,
						'o_id' => 1001
					)
				),
				'delete' => array(
					array(
						'p_id' => 29,
						's_id' => 201,
						'o_id' => 42
					)
				)
			)
		);

		$idTable = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'getDataItemById' ) )
			->getMock();

		$idTable->expects( $this->any() )
			->method( 'getDataItemById' )
			->will( $this->returnValue( DIWikiPage::newFromText( 'BAR', SMW_NS_PROPERTY ) ) );

		$this->store->expects( $this->any() )
			->method( 'getObjectIds' )
			->will( $this->returnValue( $idTable ) );

		$this->propertySpecificationLookup->expects( $this->any() )
			->method( 'getSpecification' )
			->will( $this->returnValue( array( new DIBlob( 'BAR' ) ) ) ); //Distinct value

		$compositePropertyTableDiffIterator = $this->getMockBuilder( '\SMW\SQLStore\CompositePropertyTableDiffIterator' )
			->disableOriginalConstructor()
			->setMethods( array( 'getOrderedDiffByTable' ) )
			->getMock();

		$compositePropertyTableDiffIterator->expects( $this->any() )
			->method( 'getOrderedDiffByTable' )
			->will( $this->returnValue( $orderedDiffByTable ) );

		$agent = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ChangeNotificationFilter(
			$subject,
			$this->store
		);

		$instance->setAgent( $agent );

		$result = $instance->hasChangeToNotifyAbout(
			$compositePropertyTableDiffIterator
		);

		$this->assertTrue(
			$result
		);
	}

	public function testhasChangeToNotifyAbout_ChangeNotificationForSubobjectRelatedValue() {

		$subject = DIWikiPage::newFromText( __METHOD__ );
		$dataItem = DIWikiPage::newFromText( 'BAR', SMW_NS_PROPERTY );

		$orderedDiffByTable = array(
			'tab_id_page' => array(
				'insert' => array(
					array(
						'p_id' => 29,
						's_id' => 201,
						'o_id' => 1001
					)
				),
				'delete' => array(
					array(
						'p_id' => 29,
						's_id' => 201,
						'o_id' => 42
					)
				)
			)
		);

		$subSemanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$subSemanticData->expects( $this->any() )
			->method( 'hasProperty' )
			->will( $this->returnValue( true ) );

		$subSemanticData->expects( $this->any() )
			->method( 'getSubject' )
			->will( $this->returnValue( new DIWikiPage( 'BAR', SMW_NS_PROPERTY, '', 'ooooooo' ) ) );

		$subSemanticData->expects( $this->any() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array( new DIBlob( 'BAR' ) ) ) ); //Distinct value

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->any() )
			->method( 'getSubSemanticData' )
			->will( $this->returnValue( array( $subSemanticData ) ) );

		$idTable = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'getDataItemById' ) )
			->getMock();

		$idTable->expects( $this->any() )
			->method( 'getDataItemById' )
			->will( $this->returnValue( $dataItem ) );

		$this->store->expects( $this->any() )
			->method( 'getObjectIds' )
			->will( $this->returnValue( $idTable ) );

		$this->store->expects( $this->any() )
			->method( 'getSemanticData' )
			->with( $this->equalTo( $dataItem ) )
			->will( $this->returnValue( $semanticData ) );

		$this->propertySpecificationLookup->expects( $this->any() )
			->method( 'getSpecification' )
			->will( $this->returnValue( array() ) );

		$compositePropertyTableDiffIterator = $this->getMockBuilder( '\SMW\SQLStore\CompositePropertyTableDiffIterator' )
			->disableOriginalConstructor()
			->setMethods( array( 'getOrderedDiffByTable' ) )
			->getMock();

		$compositePropertyTableDiffIterator->expects( $this->any() )
			->method( 'getOrderedDiffByTable' )
			->will( $this->returnValue( $orderedDiffByTable ) );

		$agent = $this->getMockBuilder( '\User' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ChangeNotificationFilter(
			$subject,
			$this->store
		);

		$instance->setAgent( $agent );

		$result = $instance->hasChangeToNotifyAbout(
			$compositePropertyTableDiffIterator
		);

		$this->assertTrue(
			$result
		);
	}

}
