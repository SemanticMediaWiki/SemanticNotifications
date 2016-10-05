<?php

namespace SMW\Notifications\Tests;

use RecursiveIterator;
use SMW\Notifications\IteratorFactory;
use SMW\Notifications\Iterators\MappingIterator;
use SMW\Notifications\Iterators\RecursiveMembersIterator;
use SMW\Notifications\Iterators\ChildlessRecursiveIterator;
use RecursiveIteratorIterator;
use SMW\Store;

/**
 * @covers \SMW\Notifications\IteratorFactory
 * @group semantic-notifications
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class IteratorFactoryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstructMappingIterator() {

		$instance = new IteratorFactory();

		$this->assertInstanceOf(
			MappingIterator::class,
			$instance->newMappingIterator( [], function() {} )
		);
	}

	public function testCanConstructRecursiveMembersIterator() {

		$store = $this->getMockBuilder( Store::class )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new IteratorFactory();

		$this->assertInstanceOf(
			RecursiveMembersIterator::class,
			$instance->newRecursiveMembersIterator( [], $store )
		);
	}

	public function testCanConstructRecursiveIteratorIterator() {

		$recursiveIterator = $this->getMockBuilder( RecursiveIterator::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new IteratorFactory();

		$this->assertInstanceOf(
			RecursiveIteratorIterator::class,
			$instance->newRecursiveIteratorIterator( $recursiveIterator )
		);
	}

	public function testCanConstructChildlessRecursiveIterator() {

		$instance = new IteratorFactory();

		$this->assertInstanceOf(
			ChildlessRecursiveIterator::class,
			$instance->newChildlessRecursiveIterator( [] )
		);
	}

}
