<?php

namespace SMW\Notifications\Iterators;

use SMW\Store;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\Notifications\PropertyRegistry;
use RecursiveIterator;
use ArrayIterator;
use Iterator;
use Hooks;

/**
 * Lazy load members from a `Notifications group member of` or `Notifications to`
 * assignment.
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class RecursiveMembersIterator implements RecursiveIterator {

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var array $primaryKey The name of the primary key(s)
	 */
	protected $groups;

	/**
	 * @var array $current The current iterator value
	 */
	private $current = [];

	/**
	 * @var integer key 0-indexed number of pages fetched since self::reset()
	 */
	private $key;

	/**
	 * @var string
	 */
	private $agentName = '';

	/**
	 * @var boolean
	 */
	private $notifyAgent = false;

	/**
	 * @var DIWikiPage|null
	 */
	private $subject = null;

	/**
	 * @var false
	 */
	private $notificationsTo = false;

	/**
	 * @since 1.0
	 *
	 * @param Iterator|array $groups
	 * @param Store $store
	 */
	public function __construct( $groups, Store $store ) {
		$this->groups = $groups;
		$this->store = $store;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $agentName
	 */
	public function setAgentName( $agentName ) {
		$this->agentName = str_replace( ' ', '_', $agentName );
	}

	/**
	 * @since 1.0
	 *
	 * @param boolean $notifyAgent
	 */
	public function notifyAgent( $notifyAgent ) {
		$this->notifyAgent = (bool)$notifyAgent;
	}

	/**
	 * @since 1.0
	 *
	 * @param DIWikiPage|null $subject
	 */
	public function setSubject( DIWikiPage $subject = null ) {
		$this->subject = $subject;
	}

	/**
	 * @since 1.0
	 *
	 * @return array The most recently fetched set of rows from the database
	 */
	public function current() {
		return $this->current;
	}

	/**
	 * @since 1.0
	 *
	 * @return integer 0-indexed count of the page number fetched
	 */
	public function key() {
		return $this->key;
	}

	/**
	 * @since 1.0
	 *
	 * Reset the iterator to the beginning of the table.
	 */
	public function rewind() {
		$this->key = -1; // self::next() will turn this into 0
		$this->current = [];
		$this->next();
	}

	/**
	 * @since 1.0
	 *
	 * @return bool True when the iterator is in a valid state
	 */
	public function valid() {
		return (bool)$this->current;
	}

	/**
	 * @since 1.0
	 *
	 * @return bool True when this result set has rows
	 */
	public function hasChildren() {
		return $this->current && count( $this->current );
	}

	/**
	 * @since 1.0
	 *
	 * @return RecursiveIterator
	 */
	public function getChildren() {
		return new ChildlessRecursiveIterator( $this->current );
	}

	/**
	 * Fetch the next set of rows from the database.
	 *
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function next() {

		// Initial state, transform it to a workable state
		if ( $this->key === -1 ) {
			$this->initGroups();
		}

		$group = array_shift( $this->groups );

		$recipients = $this->doFilterMembersByNotificationsToAssignment(
			$this->subject
		);

		if ( $group === false || $group === array() || $group === null ) {
			$this->current = $recipients === array() ? array() : array_values( $recipients );
			return false;
		}

		foreach ( $group as $groupName ) {

			wfDebugLog( 'smw', __METHOD__ . ' groupName: ' . $groupName->getString() );

			$members = $this->store->getPropertySubjects(
				new DIProperty( PropertyRegistry::NOTIFICATIONS_GROUP_MEMBER_OF ),
				$groupName
			);

			$this->doFilterMembers( $members, $recipients, $groupName );
		}

		$this->current = array_values( $recipients );
		$this->key++;
	}

	private function doFilterMembersByNotificationsToAssignment( $subject ) {

		$recipients = array();

		if ( $subject === null || $this->notificationsTo ) {
			return $recipients;
		}

		$members = $this->store->getPropertyValues(
			$subject,
			new DIProperty( PropertyRegistry::NOTIFICATIONS_TO )
		);

		$this->doFilterMembers( $members, $recipients );
		$this->notificationsTo = true;

		return $recipients;
	}

	private function doFilterMembers( $members, &$recipients, $groupName = null ) {

		foreach ( $members as $member ) {

			if ( !$this->notifyAgent && $this->agentName === $member->getDBKey() ) {
				continue;
			}

			if ( Hooks::run( 'SMW::Notifications::UserCanReceiveNotification', array( $this->store, $this->subject, $this->agentName, $member, $groupName ) ) === false ) {
				continue;
			}

			// Avoids duplicate members in a group but this will not avoid
			// duplicates beyond the group because we don't track them otherwise
			// we would require an in-memory hash table
			$recipients[$member->getHash()] = $member->getDBKey();
		}
	}

	private function initGroups() {

		// It might be that we used an Array or CallbackIterator to avoid
		// an initial data load, resolve the iterator now to get a list
		// of groups
		if ( $this->groups instanceof Iterator ) {
			$this->groups = iterator_to_array( $this->groups, false );
		}

		// Remove any keys used earlier to filter duplicates
		$this->groups = array_values( $this->groups );
	}

}
