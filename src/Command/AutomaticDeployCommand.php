<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Worker\PluginExtractorInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AutomaticDeployCommand extends Command
{
    /** @var StateFileInterface */
    private $stateFile;

    /** @var PluginFetcherInterface */
    private $pluginFetcher;

    /** @var PluginExtractorInterface */
    private $pluginExtractor;

    public function __construct(
        StateFileInterface $stateFile,
        PluginFetcherInterface $pluginFetcher,
        PluginExtractorInterface $pluginExtractor,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->stateFile = $stateFile;
        $this->pluginFetcher = $pluginFetcher;
        $this->pluginExtractor = $pluginExtractor;
    }

    // @TODO Add a --force -f flag to force download and deployment of configured plugins
    //       (by now, we are always in force mode as plugins are always loaded and extracted)

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:deploy:auto')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for plugin filtering',
                'production'
            )
            ->addOption(
                'skip-download',
                '',
                InputOption::VALUE_NONE,
                'If set, skip download and extraction'
            )
            ->addOption(
                'skip-install',
                '',
                InputOption::VALUE_NONE,
                'If set, skip installation'
            )
            ->addArgument(
                'statefile',
                InputArgument::REQUIRED,
                'Path to the statefile where the plugins are listed.'
            )
            ->setDescription('Deploys all configured plugins into their given state.')
            ->setHelp('Deploys all configured plugins into their given state.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $skipDownload = (bool) $input->getOption('skip-download');
        $skipInstall = (bool) $input->getOption('skip-install');

        $environment = $input->getOption('env');
        $yamlStateFilePath = $input->getArgument('statefile');
        if (false === \is_readable($yamlStateFilePath)) {
            throw new \RuntimeException(
                "The given statefile  $yamlStateFilePath  was not found or is not readable."
            );
        }

        // Read the statefile (use Configuration)
        $this->stateFile->readYamlStateFile($yamlStateFilePath);

        // VERY FIRST very simple version: Extract each and install activate accordingly afterwards

        // Extract plugins
        if (false === $skipDownload) {
            foreach ($this->stateFile->getPlugins() as $configuredPluginState) {
                // Skip if plugin should not be installed in the current environment
                if ($this->skipPluginByEnviroment($environment, $configuredPluginState->getEnvironments())) {
                    continue;
                }

                // Download the plugin and get the path
                $output->write('Downloading plugin `' . $configuredPluginState->getId() . '`...');
                $downloadPath = $this->pluginFetcher->fetch($configuredPluginState);
                $output->writeln(' <info>done.</info>');

                // If no path was returned, it is assumed that the plugin is already in its destination path
                if (false === empty($downloadPath)) {
                    // @TODO Replace by own extract command
                    $output->write('Extracting plugin `' . $configuredPluginState->getId() . '`...');
                    $this->pluginExtractor->extract($downloadPath);
                    $output->writeln(' <info>done.</info>');
                }
            }
        } else {
            $output->writeln('Skipped download and extraction because the flag --skip-download is set.');
        }

        // Now refresh plugin list
        /** @var Application $app */
        $app = $this->getApplication();
        $app->setAutoExit(false);

        if (false === $skipInstall) {
            $input = new ArrayInput([
                'command' => 'sd:plugins:refresh',
                '--env' => $environment,
            ]);
            $app->run($input, $output);

            // And now install and activate all plugins (if configured)
            foreach ($this->stateFile->getPlugins() as $configuredPluginState) {
                // Skip if plugin should not be installed in the current environment
                if ($this->skipPluginByEnviroment($environment, $configuredPluginState->getEnvironments())) {
                    continue;
                }

                if ($configuredPluginState->isInstalled()) {
                    $parameters = [
                        'command' => 'sd:plugins:install',
                        'pluginId' => $configuredPluginState->getId(),
                        '--env' => $environment,
                    ];

                    if (false === $configuredPluginState->getAlwaysReinstall()) {
                        $parameters['--no-reinstall'] = null;
                    }

                    if ($configuredPluginState->getRemoveDataOnReinstall()) {
                        $parameters['--remove-data-on-reinstall'] = null;
                    }

                    $input = new ArrayInput($parameters);
                    $app->run($input, $output);
                } else {
                    // Try to uninstall
                    $input = new ArrayInput([
                        'command' => 'sd:plugins:uninstall',
                        '--secure' => true,
                        'pluginId' => $configuredPluginState->getId(),
                        '--env' => $environment,
                    ]);
                    $app->run($input, $output);
                }

                if ($configuredPluginState->isActivated()) {
                    $input = new ArrayInput([
                        'command' => 'sd:plugins:activate',
                        'pluginId' => $configuredPluginState->getId(),
                        '--env' => $environment,
                    ]);
                    $app->run($input, $output);
                } else {
                    // Try to unactivate
                    $input = new ArrayInput([
                        'command' => 'sd:plugins:deactivate',
                        'pluginId' => $configuredPluginState->getId(),
                        '--env' => $environment,
                    ]);
                    $app->run($input, $output);
                }
            }
        } else {
            $output->writeln('Skipped installation because the flag --skip-install is set.');
        }

        // @TODO For later versions:
        //  * Get all plugins from Shopware console
        //  * Compare with statefile (AND REMOVE uninstall AND deactivate STEPS HERE AS THEY ARE CRAP!)
        //  * Load and extract only plugins that are missing
        //    + provide a "clean mode": plugin files(!) will first be deleted and then newly extracted
        //  * Warn/Fail if there are too much plugins in shop (i.e. other than in statefile)
        //  * If an older version of plugin was already installed, do a 'local update' (only from frontend up to now)

        $output->writeln('<info>Done. Now you should clear all caches.</info>');
        return 0;
    }

    /**
     * @param string[] $targetEnvironments
     */
    private function skipPluginByEnviroment(
        string $currentEnvironment,
        array $targetEnvironments
    ): bool {
        return
            false === empty($targetEnvironments) &&
            false === \in_array($currentEnvironment, $targetEnvironments);
    }
}
