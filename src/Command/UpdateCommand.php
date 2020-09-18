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

class UpdateCommand extends Command
{
    /** @var ShopwareConsoleCaller */
    private $consoleCaller;

    public function __construct(ShopwareConsoleCaller $consoleCaller, string $name = null)
    {
        parent::__construct($name);
        $this->consoleCaller = $consoleCaller;
    }

    protected function configure(): void
    {
        $this->setName('sd:plugin:update')
            ->setDescription('Updates the given Plugin.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The plugin name'
            )
            ->addOption(
                'clear-cache',
                'c',
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
            ->setHelp('Updates the plugin information in the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $pluginName = (string) $input->getArgument('plugin');
        $environment = (string) $input->getOption('env');
        $clearCache = $input->getOption('clear-cache');

        $parameters = [
            $pluginName => null,
            '--env' => $environment,
        ];

        if ($clearCache) {
            $parameters['--clear-cache'] = null;
        }

        if ($this->consoleCaller->call('sw:plugin:update', $parameters)) {
            $output->writeln('<info>Plugin `' . $pluginName . '` was successfully updated.</info>');
            return 0;
        }

        $output->writeln('<info>Plugin `' . $pluginName . '` could not be updated.</info>');

        if ($this->consoleCaller->hasOutput()) {
            $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $this->consoleCaller->getOutput());
        }

        if ($this->consoleCaller->hasError()) {
            $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $this->consoleCaller->getError());
        }

        return 1;
    }
}
