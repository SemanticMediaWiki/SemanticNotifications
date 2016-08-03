<?php

namespace SMW\Notifications\Iterators;

use IteratorIterator;
use Iterator;
use ArrayIterator;
use RuntimeException;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class MappingIterator extends IteratorIterator {

	/**
	 * @var callable
	 */
	private $callback;

	/**
	 * @since 1.0
	 *
	 * @param Iterator|array $iterator
	 * @param callable  $callback
	 */
	public function __construct( $iterator, callable $callback ) {

		if ( is_array( $iterator ) ) {
			$iterator = new ArrayIterator( $iterator );
		}

		if ( !$iterator instanceof Iterator ) {
			throw new RuntimeException( "CallbackIterator expected an Iterator" );
		}

		parent::__construct( $iterator );
		$this->callback = $callback;
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function current() {
		return call_user_func( $this->callback, parent::current() );
	}

}
