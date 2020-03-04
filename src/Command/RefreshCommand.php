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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// @TODO change to ContainerAwareCommand and remove constructors here
class RefreshCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:refresh')
            ->setDescription('Reloads the plugin list.')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->setHelp(
                'Refreshes Shopware\'s internal plugin list using `bin/console sw:plugin:refresh`.'
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

        // Try to install using the Shopware CLI. If this works, everything is fine.
        $shopwareConsole = new ShopwareConsoleCaller();
        $parameters = [
            '--env' => $env,
        ];
        $callSuccess = $shopwareConsole->call('sw:plugin:refresh', $parameters);

        if (false === $callSuccess) {
            $output->writeln('<error>Plugin list could not be refreshed by Shopware.</error>');
            if ($shopwareConsole->hasOutput()) {
                $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getOutput());
            }

            if ($shopwareConsole->hasError()) {
                $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $shopwareConsole->getError());
            }

            return 1;
        }

        $output->writeln('<info>Refreshed plugin list.</info>');
        return 0;
    }
}
