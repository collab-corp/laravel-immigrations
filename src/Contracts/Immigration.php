<?php

namespace CollabCorp\LaravelImmigrations\Contracts;

use CollabCorp\LaravelImmigrations\Database;
use CollabCorp\LaravelImmigrations\Queue;

/**
 * Interface Immigration
 *
 * @property bool $hasBeenExecuted
 * @property mixed $results
 * @property string $orderBy
 * @property string $orderDirection
 * @purpose define a database immigration to be registered in LaravelImmigrations@immigrations
 * @package CollabCorp\LaravelImmigrations
 */
interface Immigration
{
    /**
     * Whether the immigration should run
     *
     * @param Queue $immigrations
     * @return bool
     */
    public function shouldRun(Queue $immigrations): bool;

    /**
     * Run the immigration.
     * Executing the database queries, migrating the data from the old database to the new.
     *
     * @param Database $database
     *
     * @return void|mixed
     */
    public function run(Database $database);
}
