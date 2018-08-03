<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

interface ShopwareConsoleCallerInterface
{
    /**
     * @param string $command
     * @param array  $parameters For example [ '-v' => null, 'arg1' => 'value1' ]
     *
     * @return bool True if command was executed successfully
     */
    public function call($command, $parameters = []);

    /**
     * Returns the output (from `stdout`) of the executed command.
     *
     * @return string
     */
    public function getOutput();

    /**
     * Returns true if the command was executed and output was generated (on `stdout`).
     *
     * @return bool
     */
    public function hasOutput();

    /**
     * Clears the saved output and the saved return code.
     *
     * @return $this
     */
    public function resetOutput();

    /**
     * Returns the error output (from `stderr`) of the executed command.
     *
     * @return string
     */
    public function getError();

    /**
     * Returns true if the command wrote output to `stderr`.
     *
     * @return bool
     */
    public function hasError();
}
