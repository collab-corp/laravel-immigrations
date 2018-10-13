<?php

namespace CollabCorp\LaravelImmigrations\Console\Commands;

use CollabCorp\LaravelImmigrations\Console;
use CollabCorp\LaravelImmigrations\LaravelImmigrations;
use CollabCorp\LaravelImmigrations\Registry;
use Illuminate\Console\Command;

class DbImmigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:immigrate {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Immigrate data from database to database';

    /**
     * Start immigrating the data
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $from = $this->hasOption('from') ? $this->option('from') : null;

        $immigrations = new LaravelImmigrations(
            $this->laravel->make(Registry::class),
            new Console($this->output)
        );

        $immigrations->run($from);
    }
}
