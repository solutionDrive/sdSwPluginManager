<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Worker;

interface ShopwareConsoleCallerInterface
{
    /**
     * @param array|string[] $parameters For example [ '-v' => null, 'arg1' => 'value1' ]
     *
     * @return bool True if command was executed successfully
     */
    public function call(string $command, array $parameters = []): bool;

    /**
     * Returns the output (from `stdout`) of the executed command.
     */
    public function getOutput(): string;

    /**
     * Returns true if the command was executed and output was generated (on `stdout`).
     */
    public function hasOutput(): bool;

    /**
     * Clears the saved output and the saved return code.
     *
     * @return $this
     */
    public function resetOutput();

    /**
     * Returns the error output (from `stderr`) of the executed command.
     */
    public function getError(): string;

    /**
     * Returns true if the command wrote output to `stderr`.
     */
    public function hasError(): bool;
}
