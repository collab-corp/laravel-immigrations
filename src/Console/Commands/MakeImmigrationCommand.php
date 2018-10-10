<?php

namespace CollabCorp\LaravelImmigrations\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeImmigrationCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:immigration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Immigration class.';

	/**
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/immigration.stub';
	}
}
