<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Command;

use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeactivateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:deactivate')
            ->setDescription('Deactivates the given plugin.')
            ->addArgument('pluginId', InputArgument::REQUIRED, 'The plugin\'s identifier')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->setHelp(
                'Deactivates the given plugin. First it is tried to use the Shopware CLI. ' .
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

        // Try to deactivate using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $parameters = [
            $pluginId => null,
            '--env'   => $env,
        ];
        $callSuccess = $shopwareConsole->call('sw:plugin:deactivate', $parameters);

        // @TODO If it did not work, deactivate the plugin by setting the flag in the database and inform the user.
        if (false === $callSuccess) {
            $alreadyNeedle = 'is already deactivated';
            if ($shopwareConsole->hasOutput() && false !== \strpos($shopwareConsole->getOutput(), $alreadyNeedle)) {
                $output->writeln('<info>Plugin `' . $pluginId . '` was already deactivated.</info>');
                return 0;
            } else {
                $output->writeln(
                    '<error>Plugin `' . $pluginId . '` could not be deactivated by Shopware. ' .
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

        $output->writeln('<info>Plugin `' . $pluginId . '` deactivated successfully.</info>');
        return 0;
    }
}
