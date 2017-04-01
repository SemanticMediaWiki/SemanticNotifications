
## Iterators

To avoid a possible large list of recipients being resolved ad-hoc during the
change detection process, two iterators are provided to solve the list of groups and
subsequently its members on request.

The `UserLocator` is responsible for locating recipients of a notification event and
is being resolved using a `MappingIterator` which is then added to the `RecursiveGroupMembersIterator`
to iteratively resolve a single group and its members during a recursive processing.

```
	// Find the groups related to changed properties
	$groups = $iteratorFactory->newMappingIterator(
		$extra['properties'],
		$notificationGroupsLocator->getNotificationsToGroupListByCallback( $subSemanticDataMatch )
	);
```
```
	// Find the members that belong to each group
	$recursiveGroupMemberIterator = $iteratorFactory->newRecursiveGroupMembersIterator(
		$groups,
		$store
	);
```
```
	// Recursively resolve the previous steps
	$recursiveIteratorIterator = $iteratorFactory->newRecursiveIteratorIterator(
		$recursiveGroupMemberIterator
	);

	// Create a "real" User object only during a request
	$mappingIterator = $iteratorFactory->newMappingIterator( $recursiveIteratorIterator, function( $recipient ) {
		return User::newFromName( $recipient, false );
	} );
```

## Hooks

### SMW::Notifications::CanCreateNotificationEventOnDistinctValueChange

```
/**
 * Occurs before matching the expected against the actual value to decide whether
 * a change happened within the parameters (`Notifications on`) given or not.
 *
 * @param DIWikiPage $subject
 * @param User $agent
 * @param string $value
 * @param DataItem $dataItem
 *
 * @return boolean
 */
Hooks::register( 'SMW::Notifications::CanCreateNotificationEventOnDistinctValueChange', function ( $subject, $agent, $value, $dataItem ) {

	/**
	 * true to allow creating a notification event in case $value matches against
	 * the serialized $dataItem
	 *
	 * false to suppress an event even though the normal process would have
	 * matched it
	 */
	return true;
} );

```
### SMW::Notifications::UserCanReceiveNotification

```
/**
 * Occurs when a list of recipients are created for a notification event
 *
 * @param DIWikiPage $subject where the change occurred
 * @param User $agent who made the change
 * @param DataItem  $groupName
 * @param DataItem $member represents the User as DIWikiPage object to where
 *        the notification should be send
 *
 * @return boolean
 */
Hooks::register( 'SMW::Notifications::UserCanReceiveNotification', function ( $subject, $agent, $groupName, $member ) {

	/**
	 * true to allow $dataItem as recipient
	 * false to suppress $dataItem as recipient
	 */
	return true;
} );

```