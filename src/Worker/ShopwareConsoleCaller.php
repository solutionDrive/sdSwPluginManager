<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

class ShopwareConsoleCaller implements ShopwareConsoleCallerInterface
{
    /** @var string */
    private $output = '';

    /** @var string */
    private $error = '';

    /** @var int */
    private $returnCode = -1;

    /** @var null|string */
    private $workingDirectory = null;

    /** @var string */
    private $shopwareConsoleExecutable = '';

    /** @var string */
    private $commandPrefix = '';

    /**
     * @param string $workingDirectory
     * @param string $shopwareConsoleExecutable
     * @param string $commandPrefix
     */
    public function __construct(
        $workingDirectory = null,
        $shopwareConsoleExecutable = 'bin/console',
        $commandPrefix = '/usr/bin/env php '
    ) {
        $this->workingDirectory = $workingDirectory;
        $this->shopwareConsoleExecutable = $shopwareConsoleExecutable;
        $this->commandPrefix = $commandPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function call($command, $parameters = [])
    {
        $fullCommand = $this->buildFullCommand($command, $parameters);

        $stdDescriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = \proc_open($fullCommand, $stdDescriptors, $pipes, $this->workingDirectory, null);

        $this->output = \stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $this->error = \stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $processStatus = \proc_get_status($process);
        if (false === $processStatus) {
            throw new \RuntimeException('Process of Shopware CLI exited abnormally.');
        } else {
            $this->returnCode = $processStatus['exitcode'];
        }

        proc_close($process);
        return 0 === $this->returnCode;
    }

    /**
     * @TODO Perhaps this can be outsourced to an external library or at least in an own service.
     *
     * @param array $parameters
     *
     * @return string
     */
    private function buildParameterString($parameters = [])
    {
        $flat = '';

        foreach ($parameters as $key => $value) {
            if (null === $value) {
                $flat .= $key . ' ';
            } elseif (is_bool($value)) {
                $flat .= $key . '=' . ($value ? 'true' : 'false') . ' ';
            } else {
                $flat .= $key . '=' . $value . ' ';
            }
        }

        return $flat;
    }

    /**
     * @param string $command
     * @param array  $parameters
     *
     * @return string
     */
    private function buildFullCommand($command, $parameters = [])
    {
        return
            $this->commandPrefix .
            $this->shopwareConsoleExecutable . ' ' .
            $command . ' ' .
            $this->buildParameterString($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutput()
    {
        return false === empty($this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function hasError()
    {
        return false === empty($this->error);
    }

    /**
     * {@inheritdoc}
     */
    public function resetOutput()
    {
        $this->output = '';
        $this->returnCode = '';
        return $this;
    }
}
