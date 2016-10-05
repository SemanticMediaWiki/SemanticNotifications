<?php

namespace SMW\Notifications;

use SMW\Store;
use SMW\Notifications\Iterators\MappingIterator;
use SMW\Notifications\Iterators\RecursiveMembersIterator;
use SMW\Notifications\Iterators\ChildlessRecursiveIterator;
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
	 * @param callable $callback
	 *
	 * @return MappingIterator
	 */
	public function newMappingIterator( $iterator, callable $callback ) {
		return new MappingIterator( $iterator, $callback );
	}

	/**
	 * @since 1.0
	 *
	 * @param array|Iterator $iterator
	 * @param Store $store
	 *
	 * @return RecursiveMembersIterator
	 */
	public function newRecursiveMembersIterator( $iterator, Store $store ) {
		return new RecursiveMembersIterator( $iterator, $store );
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
