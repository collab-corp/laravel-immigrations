<?php

namespace CollabCorp\LaravelImmigrations\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeImmigrationCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:immigration {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Immigration class.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Immigration';


	/**
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/immigration.stub';
	}

	/**
	 * Get the root namespace for the class.
	 *
	 * @return string
	 */
	protected function rootNamespace()
	{
		return 'Database';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace.'\Immigrations';
	}

	/**
	 * Get the destination class path.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getPath($name)
	{
		$name = Str::replaceFirst($this->rootNamespace(), '', $name);

		return $this->rootNamespace().'/'.str_replace('\\', '/', $name).'.php';
	}
}
