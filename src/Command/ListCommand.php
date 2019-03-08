<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// @TODO change to ContainerAwareCommand and remove constructors here
class ListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sd:plugins:list')
            ->setDescription('Lists all plugins (whether they are installed, activated, ...)')
            ->setHelp('Lists all plugins that exist in this installation and/or should exist.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @TODO Read plugins from database
        // @TODO Read plugins from file system
        // @TODO Read plugins from plugin deployment config file
        // @TODO Compare all these together

        return 1;
    }
}
