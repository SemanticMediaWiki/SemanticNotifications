<?php

namespace SMW\Notifications\Iterator\Tests;

use SMW\Notifications\Iterator\MappingIterator;
use ArrayIterator;

/**
 * @covers \SMW\Notifications\Iterator\MappingIterator
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class MappingIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			MappingIterator::class,
			new MappingIterator( array(), function() {} )
		);
	}

	public function testInvalidConstructorArgumentThrowsException() {

		$this->setExpectedException( 'RuntimeException' );
		$instance = new MappingIterator( 2, function() {} );
	}

	public function testdoIterateOnArray() {

		$expected = array(
			1 , 42
		);

		$mappingIterator = new MappingIterator( $expected, function( $counter ) {
			return $counter;
		} );

		foreach ( $mappingIterator as $key => $value ) {
			$this->assertEquals(
				$expected[$key],
				$value
			);
		}
	}

	public function testdoIterateOnArrayIterator() {

		$expected = array(
			1001 , 42
		);

		$mappingIterator = new MappingIterator( new ArrayIterator( $expected ), function( $counter ) {
			return $counter;
		} );

		foreach ( $mappingIterator as $key => $value ) {
			$this->assertEquals(
				$expected[$key],
				$value
			);
		}
	}

}
