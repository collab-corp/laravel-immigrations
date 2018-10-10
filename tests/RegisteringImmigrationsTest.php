<?php

namespace CollabCorp\LaravelImmigrations\Tests;

use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use CollabCorp\LaravelImmigrations\Database;
use CollabCorp\LaravelImmigrations\Facades\Immigrations;
use CollabCorp\LaravelImmigrations\Queue;
use Orchestra\Testbench\TestCase;

class RegisteringImmigrationsTest extends TestCase
{
	/**
	 * @test
	 */
	public function ItRegistersTheImmigrations()
	{
		Immigrations::register($this->makeImmigration());

		$this->assertCount(1, Immigrations::registered());
	}

	/** @test */
	public function canRegisterMultipleImmigrationsAtOnce()
	{
		$one = $this->makeImmigration('one');
		$two = $this->makeImmigration('two');

		Immigrations::register([$one, $two]);

		$registered = Immigrations::registered();

		$this->assertEquals('one', $registered[0]->name);
		$this->assertEquals('two', $registered[1]->name);
	}

	/** @test */
	public function itRetainsALogicalOrder()
	{
		$one = $this->makeImmigration('one');
		$two = $this->makeImmigration('two');

		Immigrations::register([$one, $two]);

		$three = $this->makeImmigration('three');
		$four = $this->makeImmigration('four');

		Immigrations::register([$three, $four]);

		$registered = Immigrations::registered();

		$this->assertEquals('one', $registered[0]->name);
		$this->assertEquals('two', $registered[1]->name);
		$this->assertEquals('three', $registered[2]->name);
		$this->assertEquals('four', $registered[3]->name);
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function cannotRegisterAClassThatDoesntImplementTheContract()
	{
		Immigrations::register(new class {});
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function cannotRegisterAClosure()
	{
		Immigrations::register(function () {});
	}

	private function makeImmigration(string $name = null)
	{
		return new class($name) implements Immigration {
			public $name;

			public function __construct($name)
			{
				$this->name = $name;
			}

			/**
			 * @param Queue $immigrations
			 * @return bool
			 */
			public function shouldRun(Queue $immigrations): bool
			{
				return true;
			}

			/**
			 * @param Database $database
			 * @return mixed
			 */
			public function run(Database $database)
			{
				return true;
			}
		};
	}
}