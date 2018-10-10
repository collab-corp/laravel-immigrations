<?php

namespace CollabCorp\LaravelImmigrations\Tests;


use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use CollabCorp\LaravelImmigrations\Database;
use CollabCorp\LaravelImmigrations\Facades\Immigrations;
use CollabCorp\LaravelImmigrations\Queue;
use Orchestra\Testbench\TestCase;

class RunningRegisteredImmigrationsTest extends TestCase
{
	/** @test */
	public function itRunsTheRegisteredImmigrations()
	{
		Immigrations::register([$one = $this->makeImmigration()]);

		Immigrations::run();

		$this->assertTrue($one->executed);
	}

	private function makeImmigration()
	{
		return new class implements Immigration {
			public $executed = false;

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
				$this->executed = true;

				return true;
			}
		};
	}
}