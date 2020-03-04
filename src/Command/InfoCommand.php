<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class InfoCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:info')
            ->setDescription('Outputs some information about the plugin manager.')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->setHelp('Outputs some information about the plugin manager.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $versionData = Yaml::parseFile(__DIR__ . '/../../.version.yml');
        $output->writeln('solutionDrive\'s plugin manager for Shopware.', OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln($versionData['tag']);
        $output->writeln($versionData['commit'], OutputInterface::VERBOSITY_VERBOSE);
        return 0;
    }
}
