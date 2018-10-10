<?php

namespace CollabCorp\LaravelImmigrations\Facades;


use CollabCorp\LaravelImmigrations\LaravelImmigrations;
use CollabCorp\LaravelImmigrations\Registry;
use Illuminate\Support\Facades\Facade;

/**
 * Class Immigrations
 *
 * @method static Registry register(...$immigrations)
 * @method static array registered()
 * @package CollabCorp\LaravelImmigrations\Facades
 */
class Immigrations extends Facade
{
	protected static function getFacadeAccessor()
	{
		return Registry::class;
	}

	/**
	 * Run the registered immigrations
	 *
	 * @throws \Throwable
	 */
	public static function run(): void
	{
		static::$app->make(LaravelImmigrations::class)->run();
	}
}