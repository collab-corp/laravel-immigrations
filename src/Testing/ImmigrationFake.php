<?php

namespace CollabCorp\LaravelImmigrations\Testing;

use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use CollabCorp\LaravelImmigrations\Database;
use CollabCorp\LaravelImmigrations\Queue;
use PHPUnit\Framework\Assert;

class ImmigrationFake implements Immigration
{
    public $executed = false;

    /**
     * @param Queue $immigrations
     * @return bool
     */
    public function shouldRun(Queue $immigrations): bool
    {
        return true;
    }

    /**
     * @param Database $database
     * @return mixed
     */
    public function run(Database $database)
    {
        $this->executed = true;

        return true;
    }

    public function assertExecuted()
    {
        Assert::assertTrue($this->executed, "the Immigration did not run.");
    }
}
