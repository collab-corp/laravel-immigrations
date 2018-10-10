<?php

namespace CollabCorp\LaravelImmigrations;

/**
 * Class Queue
 *
 * @purpose Hold immigrations and keep tabs of the state of em
 * @package CollabCorp\LaravelImmigrations
 */
class Queue
{
    /**
     * The executed immigrations
     *
     * @var array
     */
    protected $executed = [];

    /**
     * The skipped immigrations
     *
     * @var array
     */
    protected $skipped = [];

    /**
     * The remaining immigrations
     *
     * @var array
     */
    protected $remaining = [];

    /**
     * Queue constructor.
     *
     * @param array $remaining
     */
    public function __construct(array $remaining)
    {
        $this->remaining = $remaining;
    }

    /**
     * Iterate over the remaining immigrations
     *
     * @param \Closure $callback
     */
    public function run(\Closure $callback)
    {
        while ($immigration = array_shift($this->remaining)) {
            // To allow for partially seeding the database,
            // a immigration may declare itself as already executed.
            if (property_exists($immigration, 'hasBeenExecuted') && $immigration->hasBeenExecuted) {
                $this->executed[$this->key($immigration)] = $immigration;
                continue;
            }

            if (! $immigration->shouldRun($this)) {
                $this->skipped[$this->key($immigration)] = $immigration;
                continue;
            }

            if ($callback($immigration) !== false) {
                $this->executed[$this->key($immigration)] = $immigration;
            }
        }
    }

    /**
     * Whether given immigration has been skipped
     *
     * @param array|string|Immigration $immigration
     * @return bool
     */
    public function skipped($immigration): bool
    {
        return $this->immigrationExistsIn($this->skipped, $immigration);
    }

    /**
     * Whether given immigration has been executed
     *
     * @param array|string|Immigration $immigration
     * @return bool
     */
    public function executed($immigration): bool
    {
        return $this->immigrationExistsIn($this->executed, $immigration);
    }

    /**
     * Whether given immigration pending
     *
     * @param string|Immigration $immigration
     * @return bool
     */
    public function remaining($immigration): bool
    {
        return $this->immigrationExistsIn($this->remaining, $immigration);
    }

    /**
     * alias for remaining
     *
     * @param string|Immigration $immigration
     * @return bool
     */
    public function pending($immigration): bool
    {
        return $this->remaining($immigration);
    }

    /**
     * Get one or multiple executed immigration(s)
     *
     * @param string|Immigration|null $immigration
     * @return array|Immigration|null
     */
    public function getExecuted($immigration = null)
    {
        if ($immigration) {
            return $this->executed[$this->key($immigration)] ?? null;
        }

        return $this->executed;
    }

    /**
     * Get one or multiple skipped immigration(s)
     *
     * @param string|Immigration|null $immigration
     * @return array|Immigration|null
     */
    public function getSkipped($immigration = null)
    {
        if ($immigration) {
            return $this->skipped[$this->key($immigration)] ?? null;
        }

        return $this->skipped;
    }

    /**
     * Get one or multiple remaining immigration(s)
     *
     * @param string|Immigration|null $immigration
     * @return array|Immigration|null
     */
    public function getRemaining($immigration = null)
    {
        if ($immigration) {
            return $this->remaining[$this->key($immigration)] ?? null;
        }

        return $this->remaining;
    }

    /**
     * cast given immigration to a repository key
     *
     * @param string|Immigration $immigration
     * @return string
     */
    protected function key($immigration): string
    {
        if (is_string($immigration) && class_exists($immigration)) {
            return $immigration;
        }

        //return spl_object_hash($immigration);
        return get_class($immigration);
    }

    private function immigrationExistsIn(array $items, $immigration): bool
    {
        foreach (array_wrap($immigration) as $item) {
            if (! array_key_exists($this->key($item), $items)) {
                return false;
            }
        }

        return true;
    }
}
