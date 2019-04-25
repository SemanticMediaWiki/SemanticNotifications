<?php

namespace SMW\Notifications\ChangeNotification;

use SMW\Store;
use SMW\ApplicationFactory;
use SMW\DataValueFactory;
use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\SQLStore\CompositePropertyTableDiffIterator;
use SMW\Notifications\PropertyRegistry;
use User;
use Hooks;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChangeNotificationFilter {

	const VALUE_CHANGE = 'smw-value-change';
	const SPECIFICATION_CHANGE = 'smw-specification-change';

	/**
	 * @var DIWikiPage
	 */
	private $subject;

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var DataValueFactory
	 */
	private $dataValueFactory;

	/**
	 * @var array
	 */
	private $detectedProperties = array();

	/**
	 * In case detection matrix was stored as subobject on a property then match
	 * its pairs of property => subobjectKey so that later the Locator can find
	 * out which subobject contains the group that needs to be notified.
	 *
	 * @var array
	 */
	private $subSemanticDataMatch = array();

	/**
	 * @var string|null
	 */
	private $type = null;

	/**
	 * @var User|null
	 */
	private $agent = null;

	/**
	 * @var boolean
	 */
	private $canNotify = false;

	/**
	 * @var array
	 */
	private $propertyExemptionList = array();

	/**
	 * @var boolean
	 */
	private $isCommandLineMode = false;

	/**
	 * @since 1.0
	 *
	 * @param DIWikiPage $subject
	 * @param Store $store
	 */
	public function __construct( DIWikiPage $subject, Store $store ) {
		$this->subject = $subject;
		$this->store = $store;
		$this->dataValueFactory = DataValueFactory::getInstance();
	}

	/**
	 * @since 1.0
	 *
	 * @param User $agent
	 */
	public function setAgent( User $agent ) {
		$this->agent = $agent;
	}

	/**
	 * @since 1.0
	 *
	 * @param array $propertyExemptionList
	 */
	public function setPropertyExemptionList( array $propertyExemptionList ) {
		$this->propertyExemptionList = array_flip(
			str_replace( ' ', '_', $propertyExemptionList )
		);
	}

	/**
	 * @since 1.0
	 *
	 * @param boolean $isCommandLineMode
	 */
	public function isCommandLineMode( $isCommandLineMode ) {
		$this->isCommandLineMode = $isCommandLineMode;
	}

	/**
	 * @since 1.0
	 *
	 * @param CompositePropertyTableDiffIterator $compositePropertyTableDiffIterator
	 *
	 * @return array
	 */
	public function findChangeEvent( CompositePropertyTableDiffIterator $compositePropertyTableDiffIterator ) {

		// Avoid notification when run from the commandLine
		if ( $this->isCommandLineMode ) {
			return array();
		}

		return $this->getEventRecord( $this->hasChangeToNotifyAbout( $compositePropertyTableDiffIterator ) );
	}

	/**
	 * @see EchoEvent::create
	 *
	 * @since 1.0
	 *
	 * @param boolean|null $hasChangeToNotifyAbout
	 *
	 * @return array
	 */
	public function getEventRecord( $hasChangeToNotifyAbout = false ) {

		if ( $this->subject === null || $this->type === null || !$hasChangeToNotifyAbout ) {
			wfDebugLog( 'smw', 'EchoEvent was not triggered' );
			return array();
		}

		return array(
			'agent' => $this->agent,
			'extra' => array(
				'notifyAgent' => false,
				'revid'       => $this->subject->getTitle()->getLatestRevID(),
				'properties'  => $this->detectedProperties,
				'subSemanticDataMatch' => $this->subSemanticDataMatch,
				'subject'     => $this->subject
			),
			'title' => $this->subject->getTitle(),
			'type'  => $this->type
		);
	}

	/**
	 * @since 1.0
	 *
	 * @param CompositePropertyTableDiffIterator $compositePropertyTableDiffIterator
	 *
	 * @return boolean|null
	 */
	public function hasChangeToNotifyAbout( CompositePropertyTableDiffIterator $compositePropertyTableDiffIterator ) {

		$start = microtime( true );
		$this->type = self::VALUE_CHANGE;

		$property = new DIProperty(
			PropertyRegistry::NOTIFICATIONS_ON
		);

		if (
			$this->subject->getNamespace() === SMW_NS_PROPERTY ||
			$this->subject->getNamespace() === NS_CATEGORY ||
			$this->subject->getNamespace() === SMW_NS_CONCEPT ) {
			$this->type = self::SPECIFICATION_CHANGE;
		}

		foreach ( $compositePropertyTableDiffIterator->getTableChangeOps() as $tableChangeOp ) {

			if ( isset( $this->propertyExemptionList[$this->getFixedPropertyValueBy( $tableChangeOp, 'key' )] ) ) {
				continue;
			}

			$this->doFilterOnFieldChangeOps(
				$property,
				$tableChangeOp,
				$tableChangeOp->getFieldChangeOps( 'insert' )
			);

			$this->doFilterOnFieldChangeOps(
				$property,
				$tableChangeOp,
				$tableChangeOp->getFieldChangeOps( 'delete' )
			);
		}

		wfDebugLog( 'smw', __METHOD__ . ' ' .  $this->subject->getHash() . ' in procTime (sec): ' . round( ( microtime( true ) - $start ), 7 ) );

		return $this->canNotify;
	}

	// 2.4 compat
	private function getFixedPropertyValueBy( $tableChangeOp, $key ) {
		return method_exists( $tableChangeOp, 'getFixedPropertyValueFor' ) ? $tableChangeOp->getFixedPropertyValueFor( $key ) : $tableChangeOp->getFixedPropertyValueBy( $key );
	}

	// 2.4 compat
	private function getDataItemById( $id ) {
		return method_exists( $this->store->getObjectIds(), 'getDataItemForId' ) ?  $this->store->getObjectIds()->getDataItemForId( $id ) : $this->store->getObjectIds()->getDataItemById( $id );
	}

	private function doFilterOnFieldChangeOps( $property, $tableChangeOp, $fieldChangeOps ) {

		foreach ( $fieldChangeOps as $fieldChangeOp ) {

			// _INST is special since the p_id doesn't play a role
			// in determining the category page involved
			if ( $tableChangeOp->isFixedPropertyOp() ) {
				if ( $this->getFixedPropertyValueBy( $tableChangeOp, 'key' ) === '_INST' ) {
					$fieldChangeOp->set( 'p_id', $fieldChangeOp->get( 'o_id' ) );
				} else {
					$fieldChangeOp->set( 'p_id', $this->getFixedPropertyValueBy( $tableChangeOp, 'p_id' ) );
				}
			}

			// Get DI representation to build a DataValues that allows
			// to match/compare values to its serialization form
			$dataItem = $this->getDataItemById(
				$fieldChangeOp->get( 'p_id' )
			);

			if ( $dataItem === null || $dataItem->getDBKey() === '' || isset( $this->propertyExemptionList[$dataItem->getDBKey()] ) ) {
				continue;
			}

			// Shortcut! we know changes occurred on a property itself
			if ( $this->type === self::SPECIFICATION_CHANGE ) {
				$this->detectedProperties[$dataItem->getHash()] = $dataItem;
				$this->canNotify = true;
				continue;
			}

			$this->doCompareNotificationsOnValuesWithOps(
				$property,
				$dataItem,
				$fieldChangeOp
			);
		}
	}

	private function doCompareNotificationsOnValuesWithOps( $property, $dataItem, $fieldChangeOp ) {

		$propertySpecificationLookup = ApplicationFactory::getInstance()->getPropertySpecificationLookup();

		// Don't mix !!
		// Either use the plain annotation style via [[Notifications on:: ...]] OR
		// in case one wants to have a detection matrix use:
		//
		// {{#subobject:
		//  |Notifications on=...
		//  |Notifications to group=...
		// }}
		if ( ( $pv = $propertySpecificationLookup->getSpecification( $dataItem, $property ) ) !== array() ) {
			return $this->doCompareOnPropertyValues( $dataItem, $pv, $fieldChangeOp );
		}

		// Get the whole property definitions and compare on subobjects that
		// contain `Notifications on:: ...` declarations
		$semanticData = $this->store->getSemanticData(
			$dataItem
		);

		// If matched then remember the subobjectName to later during the UserLocator
		// process to find out which groups on a particular SOBJ are to be
		// addressed
		foreach ( $semanticData->getSubSemanticData() as $subSemanticData ) {
			if ( $subSemanticData->hasProperty( $property ) ) {
				$this->doCompareOnPropertyValues(
					$dataItem,
					$subSemanticData->getPropertyValues( $property ),
					$fieldChangeOp,
					$subSemanticData->getSubject()->getSubobjectName()
				);
			}
		}
	}

	private function doCompareOnPropertyValues( $dataItem, $propertyValues, $fieldChangeOp, $subobjectName = null ) {
		foreach ( $propertyValues as $val ) {
			$this->doCompareFields( $val->getString(), $fieldChangeOp, $dataItem, $subobjectName );
		}
	}

	private function doCompareFields( $value, $fieldChangeOp, $dataItem, $subobjectName ) {

		$hash = $dataItem->getHash();

		// Any value
		if ( $value === '+' ) {
			$this->detectedProperties[$hash] = $dataItem;
			$this->subSemanticDataMatch[$hash][] = $subobjectName;
			$this->canNotify = true;
		} elseif ( $fieldChangeOp->has( 'o_serialized' ) || $fieldChangeOp->has( 'o_blob' ) ) {

			// Literal object entities
			if ( $fieldChangeOp->has( 'o_serialized' ) ) {
				$string = $fieldChangeOp->get( 'o_serialized' );
			} elseif ( $fieldChangeOp->get( 'o_blob' ) ) {
				$string = $fieldChangeOp->get( 'o_blob' );
			} else {
				$string = $fieldChangeOp->get( 'o_hash' );
			}

			$dataValue = $this->dataValueFactory->newDataValueByProperty(
				DIProperty::newFromUserLabel( $dataItem->getDBKey() ),
				$value
			);

/*
			if ( Hooks::run( 'SMW::Notifications::CanCreateNotificationEventOnDistinctValueChange', array( $this->subject, $this->agent, $value, $dataValue->getDataItem() ) ) === false ) {
				return;
			}
*/
			if ( $string === $dataValue->getDataItem()->getSerialization() ) {
				$this->detectedProperties[$hash] = $dataItem;
				$this->subSemanticDataMatch[$hash][] = $subobjectName;
				$this->canNotify = true;
			}
		} elseif ( $fieldChangeOp->has( 'o_id' ) ) {

			// Page object entities
			$oDataItem = $this->getDataItemById(
				$fieldChangeOp->get( 'o_id' )
			);
/*
			if ( Hooks::run( 'SMW::Notifications::CanCreateNotificationEventOnDistinctValueChange', array( $this->subject, $this->agent, $value, $oDataItem ) ) === false ) {
				return;
			}
*/
			if ( $value === str_replace( '_', ' ', $oDataItem->getDBKey() ) ) {
				$this->detectedProperties[$hash] = $dataItem;
				$this->subSemanticDataMatch[$hash][] = $subobjectName;
				$this->canNotify = true;
			}
		}
	}

}
