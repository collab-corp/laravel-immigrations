<?php

namespace CollabCorp\LaravelImmigrations\Testing;


use CollabCorp\LaravelImmigrations\Contracts\Writer;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;

class WriterFake implements Writer
{
	protected $messages = [];

	/**
	 * @param string $value
	 */
	public function writeLine(string $value)
	{
		$this->write('default', $value);
	}

	public function write(string $level, string $value)
	{
		$this->messages[] = [
			'level' => $level,
			'value' => $value
		];
	}

	/**
	 *
	 */
	public function newLine()
	{
		$this->write(__FUNCTION__, PHP_EOL);
	}

	/**
	 * @param string $value
	 */
	public function warning(string $value)
	{
		$this->write(__FUNCTION__, $value);
	}

	/**
	 * @param string $value
	 */
	public function success(string $value)
	{
		$this->write(__FUNCTION__, $value);
	}

	/**
	 * @param string $value
	 */
	public function info(string $value)
	{
		$this->write(__FUNCTION__, $value);
	}

	/**
	 * Assert if a message was written based on a truth-test callback.
	 *
	 * @param  string $level
	 * @param  callable|int|null $callback
	 * @return void
	 */
	public function assertWritten(string $level, $callback = null)
	{
		Assert::assertTrue(
			$this->written($level, $callback)->count() > 0,
			"The expected message with level [{$level}] was not written."
		);
	}

	/**
	 * Get all of the messages matching a truth-test callback.
	 *
	 * @param  string $level
	 * @param  callable|null $callback
	 * @return Collection
	 */
	public function written(string $level, $callback = null)
	{
		$callback = $callback ?: function () {
			return true;
		};

		return Collection::make($this->messages)
			->where('level', $level)
			->filter(function (array $message) use ($callback) {
				return $callback($message['value'], $message['level']);
			});
	}
}