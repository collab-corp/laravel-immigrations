<?php
namespace CollabCorp\LaravelImmigrations\Contracts;

use Illuminate\Database\Query\Builder;

/**
 * Class QueryProcessor
 *
 * @purpose Provides a swappable contract for overriding the QueryProcessor implementation
 * @package CollabCorp\DatabaseMigration
 */
interface QueryProcessor
{
    /**
     * Run the queries.
     *
     * @param Builder $query
     * @param \Closure $callback
     * @param int $count
     * @return bool
     */
    public function chunk(Builder $query, \Closure $callback, int $count = 1000): bool;
}
