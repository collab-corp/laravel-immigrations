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
	 * The immigrations queue
	 *
	 * @var Queue
	 */
	protected $queue;

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
     * @param Registry $registry
     * @param Writer $output
     */
	public function __construct(Registry $registry, Writer $output)
    {
        $this->registry = $registry;
        $this->output = $output;
	    $this->queue = new Queue;
    }

	/**
	 * @return Registry
	 */
	public function registry(): Registry
	{
		return $this->registry;
	}

	/**
	 * @return Writer
	 */
	public function output(): Writer
	{
		return $this->output;
	}

	/**
	 * @return Queue
	 */
	public function queue(): Queue
	{
		return $this->queue;
	}

    /**
     * Run the database data migrations
     *
     * @param string|null $from
     * @throws \Throwable
     */
	public function run(string $from = null): void
    {
	    $this->database = new Database($from ?? config('immigrations.immigrate_from', 'old_database'));

        $queue = $this->queue->push($this->instantiateImmigrations());

        $this->database
            ->connection()
            ->transaction(function () use ($queue) {
                $queue->run(function (Immigration $immigration) {
                    $this->setDatabaseOrderForImmigration($immigration);

                    if ($this->queue->skipped($immigration)) {
                        $this->output->warning('skipping immigration [' . get_class($immigration) . '].');
                        return;
                    }

                    $this->output->info('running immigration [' . get_class($immigration) . '].');
                    $immigration->run($this->database);
                });
            });
    }

	/**
	 * The instantiated immigrations
	 *
	 * @return array
	 */
	private function instantiateImmigrations(): array
	{
		return array_map(function ($immigration) {
			if (is_object($immigration)) {
				return $immigration;
			}

			return new $immigration($this->database);
		}, $this->registry->immigrations());
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
