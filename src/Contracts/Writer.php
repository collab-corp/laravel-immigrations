<?php

namespace CollabCorp\LaravelImmigrations\Contracts;

/**
 * Class Console
 *
 * @package CollabCorp\DatabaseMigration
 */
interface Writer
{
    /**
     * Write a plain text line
     *
     * @param string $value
     * @return void
     */
    public function writeLine(string $value);

    /**
     * Write a line break
     *
     * @return void
     */
    public function newLine();

    /**
     * Write a formatted warning line or block
     *
     * @param string $value
     * @return void
     */
    public function warning(string $value);

    /**
     * Write a formatted success line or block
     *
     * @param string $value
     * @return void
     */
    public function success(string $value);

    /**
     * Write a formatted information line or block
     *
     * @param string $value
     * @return void
     */
    public function info(string $value);
}
