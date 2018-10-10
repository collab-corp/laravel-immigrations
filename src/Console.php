<?php

namespace CollabCorp\LaravelImmigrations;


use CollabCorp\LaravelImmigrations\Console\Concerns\DelegatesToOutput;
use CollabCorp\LaravelImmigrations\Contracts\Writer;
use Illuminate\Console\OutputStyle;

/**
 * Class Console
 *
 * @package CollabCorp\DatabaseMigration
 */
class Console implements Writer
{
    use DelegatesToOutput;

	/**
	 * The console output style
	 * 
	 * @var OutputStyle
	 */
    protected $output;

	/**
	 * Console constructor.
	 * 
	 * @param OutputStyle $output
	 */
	public function __construct(OutputStyle $output)
	{
		$this->output = $output;
	}

	public function newLine()
	{
		$this->output->newLine();
	}

	public function writeLine(string $value)
    {
        $this->output->writeln($value);
    }

    public function warning(string $value)
    {
        $this->output->warning($value);
    }

    public function success(string $value)
    {
        $this->output->success($value);
    }

    public function info(string $value)
    {
        $this->output->block($value, 'INFO', 'fg=blue;bg=default', ' ', true);
    }
}
