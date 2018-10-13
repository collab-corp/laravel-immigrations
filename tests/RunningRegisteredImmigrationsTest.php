<?php

namespace CollabCorp\LaravelImmigrations\Tests;

use CollabCorp\LaravelImmigrations\Contracts\Immigration;
use CollabCorp\LaravelImmigrations\Contracts\Writer;
use CollabCorp\LaravelImmigrations\Database;
use CollabCorp\LaravelImmigrations\Facades\Immigrations;
use CollabCorp\LaravelImmigrations\Queue;
use CollabCorp\LaravelImmigrations\Testing\ImmigrationFake;
use CollabCorp\LaravelImmigrations\Testing\WriterFake;
use PHPUnit\Framework\Assert as PHPUnit;

class RunningRegisteredImmigrationsTest extends TestCase
{
    /**
     * @var WriterFake
     */
    protected $writerFake;

    protected function setUp()
    {
        parent::setUp();

        $this->app->instance(Writer::class, $this->writerFake = new WriterFake);
    }

    /** @test */
    public function itRunsTheRegisteredImmigrations()
    {
        Immigrations::register([$one = new ImmigrationFake]);

        Immigrations::run();

        $this->assertTrue($one->executed);
    }

    /** @test */
    public function itWritesTheMigrationsItRan()
    {
        Immigrations::register(ImmigrationFake::class);

        Immigrations::run();

        $this->writerFake->assertWritten('info', function (string $message) {
            $class = ImmigrationFake::class;

            return $message === "running immigration [{$class}].";
        });
    }

    /** @test */
    public function itSkipsImmigrationsThatShouldntRun()
    {
        $shouldBeSkipped = new class implements Immigration {
            /**
             * @param Queue $immigrations
             * @return bool
             */
            public function shouldRun(Queue $immigrations): bool
            {
                return false;
            }

            /**
             * @param Database $database
             * @return mixed|void
             */
            public function run(Database $database)
            {
                PHPUnit::fail('An immigration that should not run was executed.');
            }
        };

        Immigrations::register($shouldBeSkipped);

        Immigrations::run();

        $this->assertCount(1, Immigrations::skipped());
        $this->assertCount(0, Immigrations::remaining());
    }

    /** @test */
    public function anImmigrationCanSkipItselfIfAnotherHaventRun()
    {
        $expectsTestImmigration = new class implements Immigration {
            /**
             * @param Queue $immigrations
             * @return bool
             */
            public function shouldRun(Queue $immigrations): bool
            {
                if (!$immigrations->executed(ImmigrationFake::class)) {
                    return false;
                }

                return true;
            }

            /**
             * @param Database $database
             * @return mixed|void
             */
            public function run(Database $database)
            {
                PHPUnit::fail('An immigration that should not run was executed.');
            }
        };

        Immigrations::register($expectsTestImmigration);

        Immigrations::run();

        $this->assertCount(1, Immigrations::skipped());
    }
}
