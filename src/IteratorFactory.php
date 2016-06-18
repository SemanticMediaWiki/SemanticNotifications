<?php

namespace SMW\Notifications;

use SMW\Store;
use SMW\Notifications\Iterator\CallbackIterator;
use SMW\Notifications\Iterator\CallbackRecursiveIterator;
use SMW\Notifications\Iterator\RecursiveGroupMembersIterator;
use SMW\Notifications\Iterator\ChildlessRecursiveIterator;
use Closure;
use Iterator;
use RecursiveIterator;
use RecursiveIteratorIterator;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class IteratorFactory {

	/**
	 * @since 1.0
	 *
	 * @param array|Iterator $iterator
	 * @param Closure $callback
	 *
	 * @return CallbackIterator
	 */
	public function newCallbackIterator( $iterator, Closure $callback ) {
		return new CallbackIterator( $iterator, $callback );
	}

	/**
	 * @since 1.0
	 *
	 * @param array|Iterator $iterator
	 * @param Store $store
	 *
	 * @return RecursiveGroupMembersIterator
	 */
	public function newRecursiveGroupMembersIterator( $iterator, Store $store ) {
		return new RecursiveGroupMembersIterator( $iterator, $store );
	}

	/**
	 * @since 1.0
	 *
	 * @param RecursiveIterator $recursiveIterator
	 *
	 * @return RecursiveIteratorIterator
	 */
	public function newRecursiveIteratorIterator( RecursiveIterator $recursiveIterator ) {
		return new RecursiveIteratorIterator( $recursiveIterator );
	}

	/**
	 * @since 1.0
	 *
	 * @param Iterator|array $iterator
	 *
	 * @return ChildlessRecursiveIterator
	 */
	public function newChildlessRecursiveIterator( $iterator ) {
		return new ChildlessRecursiveIterator( $iterator );
	}

}
