<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sd:plugins:refresh')
            ->setDescription('Reloads the plugin list.')
            ->setHelp(
                'Refreshes Shopware\'s internal plugin list using `bin/console sw:plugin:refresh`.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $callSuccess = $shopwareConsole->call('sw:plugin:refresh');

        if (false === $callSuccess) {
            $output->writeln('<error>Plugin list could not be refreshed by Shopware.</error>');
            if ($shopwareConsole->hasOutput()) {
                $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getOutput());
            }

            if ($shopwareConsole->hasError()) {
                $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getError());
            }

            return 1;
        }

        $output->writeln('<info>Refreshed plugin list.</info>');
        return 0;
    }
}
