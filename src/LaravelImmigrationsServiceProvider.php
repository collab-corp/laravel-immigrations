<?php

namespace CollabCorp\LaravelImmigrations;

use CollabCorp\LaravelImmigrations\Console\Commands\DbImmigrateCommand;
use CollabCorp\LaravelImmigrations\Contracts\TerminalOutput;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class LaravelImmigrationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DbImmigrateCommand::class
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->singleton(Registry::class);

    	$this->app->singleton('console output', function() {
    		return new OutputStyle(new ArgvInput, new ConsoleOutput);
	    });

	    $this->app->bind(Contracts\Writer::class, function ($app) {
		    return new Console(
			    $app['console output']
		    );
	    });

    	$this->app->bind(Contracts\QueryProcessor::class, function ($app) {
    		return new QueryProcessor(
			    $app[Contracts\Writer::class],
			    $app['console output']
		    );
	    });

		$this->app->bind(Database::class, function ($app) {
			return new Database(
				$app['config']->get('immigrations.immigrate_from', 'old_database'),
				$app[Contracts\QueryProcessor::class]
			);
		});
    }
}
