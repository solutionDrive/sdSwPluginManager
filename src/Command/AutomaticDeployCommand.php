<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AutomaticDeployCommand extends Command
{
    // @TODO Add a --force -f flag to force download and deployment of configured plugins
    protected function configure()
    {
        $this
            ->setName('sd:plugins:deploy:auto')
            ->addOption(
                'statefile',
                's',
                InputOption::VALUE_REQUIRED,
                'Path to the statefile where the plugins are listed.'
            )
            ->setDescription('Deploys all configured plugins into their given state.')
            ->setHelp('Deploys all configured plugins into their given state.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @TODO Read the statefile (use Configuration)

        // @TODO For first version:
        //  * Get all plugins from Shopware console
        //  * Compare with statefile
        //  * Load and extract plugins that are missing
        //  * Activate and Install the plugins as configured

        return 1;
    }
}
