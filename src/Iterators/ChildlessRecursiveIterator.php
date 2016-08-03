<?php

namespace SMW\Notifications\Iterators;

use Iterator;
use ArrayIterator;
use RecursiveIterator;
use RuntimeException;

/**
 * Pretends to be a RecursiveIterator without children
 *
 * @see EchoNotRecursiveIterator
 *
 * @license GNU GPL v2+
 * @since 1.0
 */
class ChildlessRecursiveIterator implements RecursiveIterator {

	/**
	 * @var Iterator
	 */
	protected $iterator;

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function __construct( $iterator ) {

		if ( is_array( $iterator ) ) {
			$iterator = new ArrayIterator( $iterator );
		}

		if ( !$iterator instanceof Iterator ) {
			throw new RuntimeException( "ChildlessRecursiveIterator expected an Iterator" );
		}

		$this->iterator = $iterator;
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function current() {
		return $this->iterator->current();
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function key() {
		return $this->iterator->key();
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function next() {
		return $this->iterator->next();
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function rewind() {
		return $this->iterator->rewind();
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function valid() {
		return $this->iterator->valid();
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function hasChildren() {
		return false;
	}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function getChildren() {
		return null;
	}

}
