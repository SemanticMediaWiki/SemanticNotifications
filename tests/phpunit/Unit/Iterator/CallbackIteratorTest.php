<?php

namespace SMW\Notifications\Iterator\Tests;

use SMW\Notifications\Iterator\CallbackIterator;
use ArrayIterator;

/**
 * @covers \SMW\Notifications\Iterator\CallbackIterator
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class CallbackIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			CallbackIterator::class,
			new CallbackIterator( array(), function() {} )
		);
	}

	public function testInvalidIteratorThrowsException() {

		$this->setExpectedException( 'RuntimeException' );
		$instance = new CallbackIterator( 2, function() {} );
	}

	public function testIterateOnArray() {

		$expected = array(
			1 , 42
		);

		$callbackIterator = new CallbackIterator( array( 1, 42 ), function( $counter ) {
			return $counter;
		} );

		foreach ( $callbackIterator as $key => $value ) {
			$this->assertEquals(
				$expected[$key],
				$value
			);
		}
	}

	public function testIterateOnArrayIterator() {

		$expected = array(
			1001 , 42
		);

		$callbackIterator = new CallbackIterator( new ArrayIterator( array( 1001, 42 ) ), function( $counter ) {
			return $counter;
		} );

		foreach ( $callbackIterator as $key => $value ) {
			$this->assertEquals(
				$expected[$key],
				$value
			);
		}
	}

}
