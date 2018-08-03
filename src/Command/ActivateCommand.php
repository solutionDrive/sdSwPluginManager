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
use Symfony\Component\Console\Output\OutputInterface;

class ActivateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sd:plugins:activate')
            ->setDescription('Activates the given plugin.')
            ->addArgument('pluginId', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->setHelp(
                'Activates the given plugin. First it is tried to use the Shopware CLI. ' .
                'If this does not work, a more error tolerant approach is taken.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginId = (string) $input->getArgument('pluginId');

        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $statusCode = $shopwareConsole->call('sw:plugin:activate', [$pluginId => null]);

        // @TODO If it did not work, install the plugin by setting the flag in the database and inform the user.
        if (0 !== $statusCode) {
            $output->writeln(
                '<error>Plugin `' . $pluginId . '` could not be activated by Shopware. ' .
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

        return 0;
    }
}
