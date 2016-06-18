<?php

namespace SMW\Notifications\Iterator;

use IteratorIterator;
use Iterator;
use Closure;
use ArrayIterator;
use RuntimeException;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class CallbackIterator extends IteratorIterator {

	/**
	 * @var Closure
	 */
	protected $callback;

	/**
	 * @since 1.0
	 *
	 * @param Iterator $iterator
	 * @param Closure  $callback
	 */
	public function __construct( $iterator, Closure $callback ) {

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
