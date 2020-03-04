<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:uninstall')
            ->setDescription('Uninstalls the given plugin.')
            ->addOption(
                'secure',
                'S',
                InputOption::VALUE_NONE,
                ''
            )
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->addArgument('pluginId', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->setHelp(
                'Uninstalls the given plugin. First it is tried to use the Shopware CLI. ' .
                'If this does not work, a more error tolerant approach is taken.'
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
        $pluginId = (string) $input->getArgument('pluginId');

        // Try to uninstall using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $parameters = [
            $pluginId => null,
            '--env'   => $env,
        ];
        $callSuccess = $shopwareConsole->call('sw:plugin:uninstall', $parameters);

        // @TODO If it did not work, install the plugin by setting the flag in the database and inform the user.
        if (false === $callSuccess) {
            $alreadyNeedle = 'is already uninstalled';
            if ($shopwareConsole->hasOutput() && false !== \strpos($shopwareConsole->getOutput(), $alreadyNeedle)) {
                $output->writeln('<info>Plugin `' . $pluginId . '` was already uninstalled.</info>');
                return 0;
            } else {
                $output->writeln(
                    '<error>Plugin `' . $pluginId . '` could not be uninstalled by Shopware. ' .
                    'Fallback not yet implemented.</error>'
                );

                if ($shopwareConsole->hasOutput()) {
                    $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getOutput());
                }

                if ($shopwareConsole->hasError()) {
                    $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getError());
                }

                return 1;
            }
        }

        $output->writeln('<info>Plugin `' . $pluginId . '` uninstalled successfully.</info>');
        return 0;
    }
}
