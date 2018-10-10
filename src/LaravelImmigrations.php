<?php

namespace CollabCorp\LaravelImmigrations;

use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use CollabCorp\LaravelImmigrations\Contracts\Writer;

/**
 * Class LaravelImmigrations
 *
 * @purpose Migrate the data from the old database to the current
 * @strategy Copy and use the same ID where possible, otherwise use migration_parity column
 */
class LaravelImmigrations
{
    /**
     * The connection to the old database
     *
     * @var Database
     */
    protected $database;

	/**
	 * The registry holding the registered immigrations
	 *
	 * @var Registry
	 */
    protected $registry;

	/**
	 * The output we'll write status messages to
	 *
	 * @var Writer
	 */
    protected $output;

    /**
     * The default column to order the database records by
     *
     * @var string
     */
    public $defaultOrderColumn = 'id';

    /**
     * The default column direction to order the records by
     *
     * @var string
     */
    public $defaultOrderDirection = 'desc';

    /**
     * LaravelImmigrations constructor.
     *
     * @param Database $database
     * @param Registry $registry
     * @param Writer $output
     */
    public function __construct(Database $database, Registry $registry, Writer $output)
    {
        $this->database = $database;
        $this->registry = $registry;
        $this->output = $output;
    }

    /**
     * The instantiated immigrations
     *
     * @return array
     */
    public function instantiateImmigrations(): array
    {
    	return array_map(function (string $immigration) {
    		return new $immigration($this->database);
	    }, $this->registry->immigrations());
    }

    /**
     * Run the database data migrations
     *
     * @throws \Throwable
     */
    public function run(): void
    {
        $this->database
            ->connection()
            ->transaction(function () {
                $queue = new Queue($this->instantiateImmigrations());

                $queue->run(function (Immigration $immigration) use ($queue) {
                    $this->setDatabaseOrderForImmigration($immigration);

                    if ($queue->skipped($immigration)) {
                        $this->output->warning('skipping immigration ['.get_class($immigration).'].');
                        return;
                    }

                    $this->output->info('running immigration ['.get_class($immigration).'].');
                    $immigration->run($this->database);
                });
            });
    }

    /**
     * Set the order by for the given immigration
     *
     * @param Immigration $immigration
     */
    protected function setDatabaseOrderForImmigration(Immigration $immigration)
    {
        $this->database->orderColumn = $immigration->orderBy ?? $this->defaultOrderColumn;
        $this->database->orderDirection = $immigration->orderDirection ?? $this->defaultOrderDirection;
    }
}
