<?php

namespace CollabCorp\LaravelImmigrations\Database\Concerns;


use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Trait DelegatesToConnection
 *
 * @purpose Delegate dynamic calls to the underlying database connection
 * @package CollabCorp\DatabaseMigration\Database\Concerns
 */
trait DelegatesToConnection
{
    /**
     * Delegate dynamic calls to the database connection
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed|QueryBuilder|EloquentBuilder
     */
    public function __call(string $method, array $parameters)
    {
        $builder = $this->connection->$method(...$parameters);

        if ($builder instanceof Connection) {
            return $this;
        }

        return $builder;
    }

    /**
     * "Proxy" dynamic property calls to a QueryBuilder
     *
     * @param string $table
     * @return QueryBuilder
     */
    public function __get(string $table): QueryBuilder
    {
        return $this->connection->table($table);
    }
}
