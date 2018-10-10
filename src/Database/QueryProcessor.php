<?php

namespace CollabCorp\LaravelImmigrations;

use CollabCorp\LaravelImmigrations\Console\ProgressBar;
use CollabCorp\LaravelImmigrations\Contracts\Writer;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Query\Builder;
use CollabCorp\LaravelImmigrations\Contracts\QueryProcessor as QueryProcessorContract;

/**
 * Class QueryProcessor
 *
 * @purpose writes database query progress and state to given output interface
 * @package CollabCorp\DatabaseMigration
 */
class QueryProcessor implements QueryProcessorContract
{
    /**
     * The configured status writer
     *
     * @var Writer
     */
    protected $writer;

	/**
	 * The console output
	 *
	 * @var OutputStyle
	 */
    protected $output;

	/**
	 * QueryProcessor constructor.
	 *
	 * @param Writer $writer
	 * @param OutputStyle $output
	 */
    public function __construct(Writer $writer, OutputStyle $output)
    {
        $this->writer = $writer;
        $this->output = $output;
    }

    /**
     * Run the queries while keeping tabs with a progress bar
     *
     * @param Builder $query
     * @param \Closure $callback
     * @param int $count
     * @return bool
     */
    public function chunk(Builder $query, \Closure $callback, int $count = 1000): bool
    {
        $progress = ProgressBar::create($query->count(), $this->output);

        $result = $query->chunk($count, function ($results) use ($callback, $progress) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }

                $progress->advance();
            }

            return true;
        });

        $this->writer->newLine();

        return $result;
    }
}
