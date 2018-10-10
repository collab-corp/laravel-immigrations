<?php

namespace CollabCorp\LaravelImmigrations;

use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use InvalidArgumentException;

class Guards
{
    public static function guardAgainstInvalidDatabaseConnection($value)
    {
        throw_unless(is_string($value), new InvalidArgumentException('Given database connection is not a string.'));

        $connection = config("database.connections.{$value}");

        throw_unless(is_array($connection), new InvalidArgumentException("The [{$value}] database connection does not exist."));
    }

	public static function guardAgainstUnsupportedImmigration($immigration)
	{
		throw_unless(
			is_a($immigration, Immigration::class, true),
			new InvalidArgumentException('['.get_class($immigration).'] is not implementing the ['.Immigration::class.'] interface.')
		);
    }
}
