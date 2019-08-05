<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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

    public function __construct(
        string $workingDirectory = null,
        string $shopwareConsoleExecutable = 'bin/console',
        string $commandPrefix = '/usr/bin/env php '
    ) {
        $this->workingDirectory = $workingDirectory;
        $this->shopwareConsoleExecutable = $shopwareConsoleExecutable;
        $this->commandPrefix = $commandPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function call(string $command, array $parameters = []): bool
    {
        $fullCommand = $this->buildFullCommand($command, $parameters);

        $stdDescriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = \proc_open($fullCommand, $stdDescriptors, $pipes, $this->workingDirectory, null);
        if (false === $process || false === \is_resource($process)) {
            $this->error = \printf('Could not start command "%s" correctly. No valid process resource was returned', $command);
            return false;
        }

        \stream_set_blocking($pipes[0], false);

        $this->output = \stream_get_contents($pipes[1]);
        \fclose($pipes[1]);
        $this->error = \stream_get_contents($pipes[2]);
        \fclose($pipes[2]);

        $this->waitForExitCode($process);

        \proc_close($process);
        return 0 === $this->returnCode;
    }

    /**
     * @TODO Perhaps this can be outsourced to an external library or at least in an own service.
     *
     * @param array|string[] $parameters
     */
    private function buildParameterString(array $parameters = []): string
    {
        $flat = '';

        foreach ($parameters as $key => $value) {
            if (null === $value) {
                $flat .= $key . ' ';
            } elseif (\is_bool($value)) {
                $flat .= $key . '=' . ($value ? 'true' : 'false') . ' ';
            } else {
                $flat .= $key . '=' . $value . ' ';
            }
        }

        return $flat;
    }

    /**
     * @param array|string[] $parameters
     */
    private function buildFullCommand(string $command, array $parameters = []): string
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
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutput(): bool
    {
        return false === empty($this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function hasError(): bool
    {
        return false === empty($this->error);
    }

    /**
     * {@inheritdoc}
     */
    public function resetOutput()
    {
        $this->output = '';
        $this->returnCode = -1;
        return $this;
    }

    /**
     * @param resource $process     process which should be checked for exit code
     * @param int      $maxWaitTime max time to wait for a running process (in microseconds)
     */
    private function waitForExitCode($process, $maxWaitTime = 30000000): void
    {
        $waitTime = 0;
        do {
            $processStatus = \proc_get_status($process);
            if (false === $processStatus) {
                throw new \RuntimeException('Process of Shopware CLI exited abnormally.');
            }

            if (false === $processStatus['running']) {
                $this->returnCode = $processStatus['exitcode'];
            } else {
                \usleep(100);
                $waitTime += 100;
            }

            if ($waitTime >= $maxWaitTime) {
                $this->error = \printf('Process did not exit properly within %d micro seconds', $maxWaitTime);
                $this->returnCode = 1;
            }
        } while (true === $processStatus['running'] && false === $this->hasError());
    }
}
