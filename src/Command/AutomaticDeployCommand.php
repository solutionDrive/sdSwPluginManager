<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Worker\PluginDeployerInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AutomaticDeployCommand extends Command
{
    /** @var StateFileInterface */
    private $stateFile;

    /** @var PluginFetcherInterface */
    private $pluginFetcher;

    /** @var PluginDeployerInterface */
    private $pluginDeployer;

    /**
     * @param null|string $name
     */
    public function __construct(
        StateFileInterface $stateFile,
        PluginFetcherInterface $pluginFetcher,
        PluginDeployerInterface $pluginDeployer,
        $name = null
    ) {
        parent::__construct($name);
        $this->stateFile = $stateFile;
        $this->pluginFetcher = $pluginFetcher;
        $this->pluginDeployer = $pluginDeployer;
    }

    // @TODO Add a --force -f flag to force download and deployment of configured plugins
    //       (by now, we are always in force mode as plugins are always loaded and extracted)
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
        $yamlStateFilePath = $input->getOption('statefile');
        if (false === is_readable($yamlStateFilePath)) {
            throw new \RuntimeException(
                "The given statefile  $yamlStateFilePath  was not found or is not readable."
            );
        }

        // Read the statefile (use Configuration)
        $this->stateFile->readYamlStateFile($yamlStateFilePath);

        // VERY FIRST very simple version: Extract each and install activate accordingly afterwards

        // Extract plugins
        foreach ($this->stateFile->getPlugins() as $configuredPluginState) {
            // Download the plugin and get the path
            $downloadPath = $this->pluginFetcher->fetch($configuredPluginState);

            // If no path was returned, it is assumed that the plugin is already in its destination path
            if (false === empty($downloadPath)) {
                // @TODO Replace by own extract command
                $this->pluginDeployer->deploy($downloadPath);
            }
        }

        // Now refresh plugin list
        /** @var Application $app */
        $app = $this->getApplication();
        $app->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'sd:plugins:refresh',
        ]);
        $app->run($input, $output);

        // And now install and activate all plugins (if configured)
        foreach ($this->stateFile->getPlugins() as $configuredPluginState) {
            if ($configuredPluginState->isInstalled()) {
                $input = new ArrayInput([
                    'command' => 'sd:plugins:install',
                    'pluginId' => $configuredPluginState->getId(),
                ]);
                $app->run($input, $output);
            }

            if ($configuredPluginState->isActivated()) {
                $input = new ArrayInput([
                    'command' => 'sd:plugins:activate',
                    'pluginId' => $configuredPluginState->getId(),
                ]);
                $app->run($input, $output);
            }
        }

        // @TODO For later versions:
        //  * Get all plugins from Shopware console
        //  * Compare with statefile
        //  * Load and extract only plugins that are missing
        //  * Activate and Install the plugins as configured
        //  * Warn/Fail if there are too much plugins in shop (i.e. other than in statefile)

        $output->writeln('<info>Done. Now you should clear all caches.</info>');
        return 0;
    }
}
