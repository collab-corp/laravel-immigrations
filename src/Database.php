<?php

namespace CollabCorp\LaravelImmigrations;

use CollabCorp\LaravelImmigrations\Contracts\QueryProcessor;
use CollabCorp\LaravelImmigrations\Database\Concerns\DelegatesToConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Class Database
 *
 * @purpose provides a convenient wrapper around a database connection
 * @package CollabCorp\DatabaseMigration
 */
class Database
{
    use DelegatesToConnection;

    /**
     * The database connection
     *
     * @var Connection
     */
    protected $connection;

    /**
     * @var QueryProcessor
     */
    protected $queryProcessor;


    /**
     * The column to order the database records by
     *
     * @var string
     */
    public $orderColumn = 'id';

    /**
     * The column direction to order the records by
     *
     * @var string
     */
    public $orderDirection = 'desc';

    /**
     * Database constructor.
     * @param string $connection
     * @param QueryProcessor $processor
     */
    public function __construct(string $connection, QueryProcessor $processor = null)
    {
        $this->connection = DB::connection($connection);
        $this->queryProcessor = $processor ?? resolve(QueryProcessor::class);
    }

    /**
     * Get the database connection
     *
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get the number of records in given table
     *
     * @param string $table
     * @param string $columns
     * @return int
     */
    public function count(string $table, string $columns = 'id'): int
    {
        return $this->connection->table($table)->count($columns);
    }

    /**
     * Get a query builder for given table
     *
     * @see Connection
     * @param string $table
     * @return QueryBuilder
     */
    public function table(string $table): QueryBuilder
    {
        return $this->connection->table($table);
    }

    /**
     * Chunk the results of the query while outputting progress to console.
     *
     * @see QueryProcessor
     * @see Console
     * @param string|QueryBuilder $tableOrQuery
     * @param \Closure $callback
     * @return bool
     */
    public function each($tableOrQuery, \Closure $callback)
    {
        if (is_string($tableOrQuery)) {
            $tableOrQuery = $this->connection->table($tableOrQuery);
        }

        return $this->queryProcessor->chunk(
            $tableOrQuery->orderBy($this->orderColumn, $this->orderDirection),
            $callback,
            $tableOrQuery->limit ?? 1000
        );
    }
}
