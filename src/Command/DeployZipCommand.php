<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Worker\PluginExtractor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// @TODO Add command to only extract plugin zip

// @TODO change to ContainerAwareCommand and remove constructors here
class DeployZipCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:deploy:zip')
            ->addOption(
                'install',
                'i',
                InputOption::VALUE_NONE,
                'Also install the plugin'
            )
            ->addOption(
                'activate',
                'a',
                InputOption::VALUE_NONE,
                'Also activate the plugin'
            )
            ->addOption(
                'root',
                'r',
                InputOption::VALUE_REQUIRED,
                'Path to shop root (defaults to working directory)',
                '.'
            )
            ->addOption(
                'pluginfolder',
                'p',
                InputOption::VALUE_REQUIRED,
                'Path to the plugin folder under the shop directory',
                'custom/plugins'
            )
            ->addOption(
                'no-refresh',
                null,
                InputOption::VALUE_NONE,
                'Skip the plugin refresh step'
            )
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->setDescription('Deploys the given plugin.')
            ->setHelp(
                'Deploys the given plugin (packed) in a zip file. Optionally installs and activates the plugin.'
            )
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The zip file that should be deployed'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $env = (string) $input->getOption('env');
        $sourceFile = $input->getArgument('file');
        $shouldInstall = (bool) $input->getOption('install');
        $shouldActivate = (bool) $input->getOption('activate');
        $shopRoot = (string) $input->getOption('root');
        $pluginFolder = (string) $input->getOption('pluginfolder');
        $skipRefresh = (bool) $input->getOption('no-refresh');

        $pluginExtractor = new PluginExtractor($shopRoot, $pluginFolder);

        if (false === $shouldInstall && true === $shouldActivate) {
            throw new \RuntimeException('A plugin cannot be activated without being installed.');
        }

        $fileInfo = new \SplFileInfo($sourceFile);
        if (false === $fileInfo->isFile()) {
            throw new \RuntimeException('No file could be found by the given file name: ' . $sourceFile);
        }

        $pluginId = $pluginExtractor->extract($sourceFile);

        $output->writeln('<info>Plugin extracted successfully.</info>');

        // Install the plugin if requested
        if (true === $shouldInstall) {
            /** @var Application $app */
            $app = $this->getApplication();
            $app->setAutoExit(false);

            // First refresh plugin list
            if (false === $skipRefresh) {
                $input = new ArrayInput([
                    'command' => 'sd:plugins:refresh',
                    '--env'   => $env,
                ]);
                $app->run($input, $output);
            }

            // Then install the plugin
            $input = new ArrayInput([
                'command' => 'sd:plugins:install',
                'pluginId' => $pluginId,
                '--env'    => $env,
            ]);
            $app->run($input, $output);
        }

        // Activate the plugin if requested
        if (true === $shouldActivate) {
            /** @var Application $app */
            $app = $this->getApplication();
            $app->setAutoExit(false);
            $input = new ArrayInput([
                'command' => 'sd:plugins:activate',
                'pluginId' => $pluginId,
                '--env'    => $env,
            ]);
            $app->run($input, $output);
        }

        return 0;
    }
}
