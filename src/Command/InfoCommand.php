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

class InfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sd:plugins:info')
            ->setDescription('Outputs some information about the plugin manager.')
            ->setHelp('Outputs some information about the plugin manager.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('solutionDrive\'s plugin manager for Shopware.', OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln('__TAG__');
        $output->writeln('__COMMIT__', OutputInterface::VERBOSITY_VERBOSE);
        return 0;
    }
}
