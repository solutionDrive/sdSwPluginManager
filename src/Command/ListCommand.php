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
class ListCommand extends Command
{
    /** @var ShopwareConsoleCaller */
    private $consoleCaller;

    public function __construct(ShopwareConsoleCaller $consoleCaller, string $name = null)
    {
        parent::__construct($name);
        $this->consoleCaller = $consoleCaller;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sd:plugins:list')
            ->setDescription('Lists all plugins (whether they are installed, activated, ...)')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'The current environment to use for calling shopware commands',
                'production'
            )
            ->addOption(
                'filter',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Filter Plugins (inactive, active, installed, uninstalled)'
            )
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filter Plugins by namespace (core, frontend, backend) (multiple values allowed)'
            )
            ->setHelp('Lists all plugins that exist in this installation and/or should exist.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        // @TODO Read plugins from database -> done
        // @TODO Read plugins from file system
        // @TODO Read plugins from plugin deployment config file
        // @TODO Compare all these together

        $env = (string) $input->getOption('env');
        $filter = $input->getOption('filter');
        $namespace = $input->getOption('namespace');
        $parameters = [
            '--env' => $env,
        ];

        if ($filter) {
            $parameters['--filter'] = $filter;
        }

        if ($namespace) {
            $parameters['--namespace'] = $namespace;
        }

        if ($this->consoleCaller->call('sw:plugin:list', $parameters)) {
            $output->write($this->consoleCaller->getOutput());
            return 0;
        } else {
            $output->writeln('<error>Error getting the plugin list from Shopware</error>');

            if ($this->consoleCaller->hasOutput()) {
                $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . $this->consoleCaller->getOutput());
            }

            if ($this->consoleCaller->hasError()) {
                $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . $this->consoleCaller->getError());
            }

            return 1;
        }
    }
}
