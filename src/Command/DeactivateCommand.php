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

class DeactivateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sd:plugins:deactivate')
            ->setDescription('Deactivates the given plugin.')
            ->addArgument('pluginId', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->setHelp(
                'Deactivates the given plugin. First it is tried to use the Shopware CLI. ' .
                'If this does not work, a more error tolerant approach is taken.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginId = (string) $input->getArgument('pluginId');

        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $callSuccess = $shopwareConsole->call('sw:plugin:deactivate', [$pluginId => null]);

        // @TODO If it did not work, install the plugin by setting the flag in the database and inform the user.
        if (false === $callSuccess) {
            $alreadyNeedle = 'is already deactivated';
            if ($shopwareConsole->hasOutput() && false !== strpos($shopwareConsole->getOutput(), $alreadyNeedle)) {
                $output->writeln('<info>Plugin `' . $pluginId . '` was already deactivated.</info>');
                return 0;
            } else {
                $output->writeln(
                    '<error>Plugin `' . $pluginId . '` could not be deactivated by Shopware. ' .
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
        }

        $output->writeln('<info>Plugin `' . $pluginId . '` deactivated successfully.</info>');
        return 0;
    }
}
