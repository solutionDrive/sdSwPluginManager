<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchPluginCommand extends Command
{
    /** @var PluginFetcherInterface */
    private $pluginFetcher;

    /** @var StateFileInterface */
    private $stateFile;

    public function __construct(StateFileInterface $stateFile, PluginFetcherInterface $pluginFetcher, $name = null)
    {
        parent::__construct($name);
        $this->pluginFetcher = $pluginFetcher;
        $this->stateFile = $stateFile;
    }

    protected function configure()
    {
        $this
            ->setName('sd:plugins:fetch')
            ->addOption(
                'statefile',
                's',
                InputOption::VALUE_REQUIRED,
                'Path to the statefile where the plugins are listed.'
            )
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The plugin that should be fetched'
            )
            ->setDescription('Fetches a plugin from its source.')
            ->setHelp(
                'Fetches a plugin from its source configured in the given statefile.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stateFile = $input->getOption('statefile');
        if (false === is_readable($stateFile)) {
            throw new \RuntimeException('Given statefile does not exist or is not readable.');
        }

        $pluginId = $input->getArgument('plugin');
        $this->stateFile->readYamlStateFile($stateFile);
        $configuredPluginState = $this->stateFile->getPlugin($pluginId);
        if (null === $configuredPluginState) {
            throw new \RuntimeException('The given plugin was not found in the statefile.');
        }

        $file = $this->pluginFetcher->fetch($configuredPluginState);

        $output->writeln("Downloaded plugin '$pluginId' to: ", OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln($file);

        return 0;
    }
}
