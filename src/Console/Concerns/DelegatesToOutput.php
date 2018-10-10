<?php

namespace CollabCorp\LaravelImmigrations\Console\Concerns;

/**
 * Trait DelegatesToConsole
 *
 * Dynamically delegate calls to the console
 */
trait DelegatesToOutput
{
    /**
     * Dynamically delegate calls to the console
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->output->$method(...$parameters);
    }
}
