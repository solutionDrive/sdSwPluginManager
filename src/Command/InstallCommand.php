<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
                '',
                true
            )
            ->addArgument('pluginName', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->setHelp(
                'Installs the given plugin. First it is tried to use the Shopware CLI. ' .
                'If this does not work, a more error tolerant approach is taken.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @TODO Try to install using the Shopware CLI. If this works, everything is fine.

        // @TODO If it did not work, install the plugin by setting the flag in the database and inform the user.

        return 1;
    }
}
