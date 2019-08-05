<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// @TODO change to ContainerAwareCommand and remove constructors here
class ListCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:list')
            ->setDescription('Lists all plugins (whether they are installed, activated, ...)')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->setHelp('Lists all plugins that exist in this installation and/or should exist.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        // @TODO Read plugins from database
        // @TODO Read plugins from file system
        // @TODO Read plugins from plugin deployment config file
        // @TODO Compare all these together

        return 1;
    }
}
