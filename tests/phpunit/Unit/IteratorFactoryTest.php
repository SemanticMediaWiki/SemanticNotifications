<?php

namespace SMW\Notifications\Tests;

use SMW\Notifications\IteratorFactory;
use SMW\Notifications\Iterator\CallbackIterator;
use SMW\Notifications\Iterator\RecursiveGroupMembersIterator;
use SMW\Notifications\Iterator\ChildlessRecursiveIterator;
use RecursiveIteratorIterator;

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

	public function testCanConstruct() {

		$this->assertInstanceOf(
			IteratorFactory::class,
			new IteratorFactory()
		);
	}

	public function testCanConstructCallbackIterator() {

		$instance = new IteratorFactory();

		$this->assertInstanceOf(
			CallbackIterator::class,
			$instance->newCallbackIterator( array(), function(){} )
		);
	}

	public function testCanConstructRecursiveGroupMembersIterator() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new IteratorFactory();

		$this->assertInstanceOf(
			RecursiveGroupMembersIterator::class,
			$instance->newRecursiveGroupMembersIterator( array(), $store )
		);
	}

	public function testCanConstructRecursiveIteratorIterator() {

		$recursiveIterator = $this->getMockBuilder( '\RecursiveIterator' )
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
			$instance->newChildlessRecursiveIterator( array() )
		);
	}

}
