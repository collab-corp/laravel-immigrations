<?php

namespace CollabCorp\LaravelImmigrations;

use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use InvalidArgumentException;

class Guards
{
    public static function guardAgainstUnsupportedImmigration($immigration)
    {
        if (is_object($immigration)) {
            throw_unless(
                $immigration instanceof Immigration,
                new InvalidArgumentException('[' . get_class($immigration) . '] is not implementing the [' . Immigration::class . '] interface.')
            );
        } else {
            throw_unless(
                class_implements($immigration, true),
                new InvalidArgumentException('[' . ($immigration) . '] is not implementing the [' . Immigration::class . '] interface.')
            );
        }
    }
}
