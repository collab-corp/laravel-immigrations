<?php

namespace CollabCorp\LaravelImmigrations;


use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use Illuminate\Support\Collection;

class Registry
{
	/**
	 * The registered immigrations
	 *
	 * @var array
	 */
	protected $registered = [];

	/**
	 * Register one or multiple Immigrations
	 *
	 * @param array|Immigration $immigrations
	 * @return Registry
	 */
	public function register($immigrations): Registry
	{
		foreach (array_wrap($immigrations) as $immigration) {
			Guards::guardAgainstUnsupportedImmigration($immigration);
			$this->registered[] = $immigration;
		}

		return $this;
	}

	/**
	 * Alias to getRegistered
	 *
	 * @return array
	 */
	public function immigrations(): array
	{
		return $this->registered();
	}

	/**
	 * Get the registered immigrations
	 *
	 * @return array
	 */
	public function registered(): array
	{
		return $this->registered;
	}
}