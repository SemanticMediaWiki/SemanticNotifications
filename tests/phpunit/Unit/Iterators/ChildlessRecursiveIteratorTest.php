<?php

namespace SMW\Notifications\Iterators\Tests;

use SMW\Notifications\Iterators\ChildlessRecursiveIterator;
use RecursiveIteratorIterator;
use SMW\Tests\PHPUnitCompat;

/**
 * @covers \SMW\Notifications\Iterators\ChildlessRecursiveIterator
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ChildlessRecursiveIteratorTest extends \PHPUnit_Framework_TestCase {

	use PHPUnitCompat;

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ChildlessRecursiveIterator::class,
			new ChildlessRecursiveIterator( array() )
		);
	}

	public function testInvalidIteratorThrowsException() {

		$this->setExpectedException( 'RuntimeException' );
		$instance = new ChildlessRecursiveIterator( 2 );
	}

	public function testStandardaIteration() {

		$instance = new ChildlessRecursiveIterator( array( 'Foo' ) );

		foreach ( $instance as $value ) {
			$this->assertSame(
				'Foo',
				$value
			);
		}
	}

	public function testRecursiveIteration() {

		$instance = new RecursiveIteratorIterator(
			new ChildlessRecursiveIterator( array( 'Foo' ) )
		);

		foreach ( $instance as $value ) {
			$this->assertSame(
				'Foo',
				$value
			);
		}
	}

}
