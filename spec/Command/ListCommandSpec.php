<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Command\ListCommand;
use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommandSpec extends ObjectBehavior
{
    public function let(ShopwareConsoleCaller $consoleCaller): void
    {
        $this->beConstructedWith($consoleCaller);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ListCommand::class);
        $this->shouldHaveType(Command::class);
    }

    public function it_should_call_the_shopware_plugin_list_command(
        InputInterface $input,
        OutputInterface $output,
        ShopwareConsoleCaller $consoleCaller
    ): void {
        $this->prepareMocks($input);

        $consoleCaller->call('sw:plugin:list', ['--env' => 'dev'])
            ->willReturn(true);

        $consoleCaller->getOutput()
            ->willReturn('plugin list');

        $output->write('plugin list')
            ->shouldBeCalled();

        $this->run($input, $output)
            ->shouldReturn(0);
    }

    public function it_should_call_the_command_with_optional_parameters(
        InputInterface $input,
        OutputInterface $output,
        ShopwareConsoleCaller $consoleCaller
    ): void {
        $this->prepareMocks($input, 'active', 'frontend,backend');

        $consoleCaller->call('sw:plugin:list', [
            '--env' => 'dev',
            '--filter' => 'active',
            '--namespace' => 'frontend,backend',
        ])->willReturn(true);

        $consoleCaller->getOutput()
            ->willReturn('plugin list');

        $output->writeln('plugin list');

        $this->run($input, $output)
            ->shouldReturn(0);
    }

    public function it_should_print_the_shopware_cli_output_if_the_command_fails(
        InputInterface $input,
        OutputInterface $output,
        ShopwareConsoleCaller $consoleCaller
    ): void {
        $this->prepareMocks($input);

        $consoleCaller->call('sw:plugin:list', ['--env' => 'dev'])
            ->willReturn(false);

        $output->writeln('<error>Error getting the plugin list from Shopware</error>')
            ->shouldBeCalled();

        $consoleCaller->hasOutput()
            ->willReturn(true);

        $consoleCaller->getOutput()
            ->willReturn('stdout');

        $output->writeln('Output (stdout) from Shopware CLI: ' . PHP_EOL . 'stdout')
            ->shouldBeCalled();

        $consoleCaller->hasError()
            ->willReturn(true);

        $consoleCaller->getError()
            ->willReturn('stderr');

        $output->writeln('Output (stderr) from Shopware CLI: ' . PHP_EOL . 'stderr')
            ->shouldBeCalled();

        $this->run($input, $output)
            ->shouldReturn(1);
    }

    private function prepareMocks(
        InputInterface $input,
        string $filterOption = null,
        string $namespaceOption = null
    ): void {
        $input->bind(Argument::any())
            ->shouldBeCalled();

        $input->isInteractive()
            ->willReturn(false);

        $input->hasArgument('command')
            ->willReturn(false);

        $input->validate()
            ->shouldBeCalled();

        $input->getOption('filter')
            ->willReturn($filterOption);

        $input->getOption('env')
            ->willReturn('dev');

        $input->getOption('namespace')
            ->willReturn($namespaceOption);
    }
}
