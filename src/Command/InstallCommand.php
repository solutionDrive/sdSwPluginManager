<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// @TODO change to ContainerAwareCommand and remove constructors here
class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sd:plugins:install')
            ->setDescription('Installs the given plugin.')
            ->addOption(
                'activate',
                'a',
                InputOption::VALUE_NONE,
                ''
            )
            ->addArgument('pluginId', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->setHelp(
                'Installs the given plugin. First it is tried to use the Shopware CLI. ' .
                'If this does not work, a more error tolerant approach is taken.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginId = (string) $input->getArgument('pluginId');

        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $callSuccess = $shopwareConsole->call('sw:plugin:install', [$pluginId => null]);

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

        $output->writeln('<info>Plugin `' . $pluginId . '` installed successfully.</info>');
        return 0;
    }
}
