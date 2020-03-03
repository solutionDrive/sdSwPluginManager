<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:install')
            ->setDescription('Installs the given plugin.')
            ->addOption(
                'no-reinstall',
                null,
                InputOption::VALUE_NONE,
                ''
            )
            ->addOption(
                'remove-data-on-reinstall',
                null,
                InputOption::VALUE_NONE,
                ''
            )
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->addArgument('pluginId', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->setHelp(
                'Installs the given plugin. First it is tried to use the Shopware CLI. ' .
                'If this does not work, a more error tolerant approach is taken.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $env = (string) $input->getOption('env');
        $pluginId = (string) $input->getArgument('pluginId');

        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $parameters = [
            $pluginId => null,
            '--env'   => $env,
        ];
        $callSuccess = $shopwareConsole->call('sw:plugin:install', $parameters);

        // @TODO If it did not work, install the plugin by setting the flag in the database and inform the user.
        if (false === $callSuccess) {
            $output->writeln(
                '<error>Plugin `' . $pluginId . '` could not be installed by Shopware. ' .
                'Fallback not yet implemented.</error>'
            );

            if ($shopwareConsole->hasOutput()) {
                $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getOutput());
            }

            if ($shopwareConsole->hasError()) {
                $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getError());
            }

            return 1;
        }

        $alreadyNeedle = 'is already installed';
        if ($shopwareConsole->hasOutput() && false !== \strpos($shopwareConsole->getOutput(), $alreadyNeedle)) {
            $output->writeln('<info>Plugin `' . $pluginId . '` was already installed.</info>');
            $this->reinstallIfRequested($input, $output);
            return 0;
        }

        $output->writeln('<info>Plugin `' . $pluginId . '` installed successfully.</info>');
        return 0;
    }

    private function reinstallIfRequested(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $pluginId = (string) $input->getArgument('pluginId');
        $skipReinstall = (bool) $input->getOption('no-reinstall');
        $removeData = (bool) $input->getOption('remove-data-on-reinstall');

        // Skip reinstall if requested
        if ($skipReinstall) {
            return 0;
        }

        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();

        $parameters = [$pluginId => null];
        if ($removeData) {
            $parameters['--removedata'] = null;
        }

        $callSuccess = $shopwareConsole->call('sw:plugin:reinstall', $parameters);

        if (false === $callSuccess) {
            $output->writeln(
                '<error>Plugin `' . $pluginId . '` could not be reinstalled by Shopware.</error>'
            );

            if ($shopwareConsole->hasOutput()) {
                $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getOutput());
            }

            if ($shopwareConsole->hasError()) {
                $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getError());
            }

            return 1;
        }

        $output->writeln('<info>Plugin `' . $pluginId . '` reinstalled successfully.</info>');
        return 0;
    }
}
