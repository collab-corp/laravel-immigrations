<?php

namespace CollabCorp\LaravelImmigrations\Facades;


use CollabCorp\LaravelImmigrations\LaravelImmigrations;
use Illuminate\Support\Facades\Facade;

/**
 * Class Immigrations
 *
 * @method static void run()
 * @purpose to provide an easy interface to interact with the underlying system(s).
 * @package CollabCorp\LaravelImmigrations\Facades
 */
class Immigrations extends Facade
{
	protected static function getFacadeAccessor()
	{
		return LaravelImmigrations::class;
	}

	public static function register($immigrations)
	{
		static::getFacadeRoot()->registry()->register($immigrations);
	}

	public static function registered(): array
	{
		return static::getFacadeRoot()->registry()->registered();
	}

	public static function skipped(): array
	{
		return static::getFacadeRoot()->queue()->getSkipped();
	}

	public static function executed(): array
	{
		return static::getFacadeRoot()->queue()->getExecuted();
	}

	public static function remaining(): array
	{
		return static::getFacadeRoot()->queue()->getRemaining();
	}
}