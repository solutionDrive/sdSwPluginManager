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
use sd\SwPluginManager\Command\UpdateCommand;
use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommandSpec extends ObjectBehavior
{
    public function let(ShopwareConsoleCaller $consoleCaller): void
    {
        $this->beConstructedWith($consoleCaller);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateCommand::class);
        $this->shouldHaveType(Command::class);
    }

    public function it_should_call_the_shopware_plugin_update_command(
        InputInterface $input,
        OutputInterface $output,
        ShopwareConsoleCaller $consoleCaller
    ): void {
        $this->prepareMocks($input);

        $consoleCaller->call('sw:plugin:update', [
            'nlxTest' => null,
            '--env' => 'dev',
        ])->willReturn(true);

        $output->writeln('<info>Plugin `nlxTest` was successfully updated.</info>')
            ->shouldBeCalled();

        $this->run($input, $output)
            ->shouldReturn(0);
    }

    public function it_should_call_the_command_with_optional_parameters(
        InputInterface $input,
        OutputInterface $output,
        ShopwareConsoleCaller $consoleCaller
    ): void {
        $this->prepareMocks($input, true);

        $consoleCaller->call('sw:plugin:update', [
            'nlxTest' => null,
            '--env' => 'dev',
            '--clear-cache' => null,
        ])->willReturn(true);

        $output->writeln('<info>Plugin `nlxTest` was successfully updated.</info>')
            ->shouldBeCalled();

        $this->run($input, $output)
            ->shouldReturn(0);
    }

    public function it_should_print_the_shopware_cli_output_of_the_command_fails(
        InputInterface $input,
        OutputInterface $output,
        ShopwareConsoleCaller $consoleCaller
    ): void {
        $this->prepareMocks($input);

        $consoleCaller->call('sw:plugin:update', [
            'nlxTest' => null,
            '--env' => 'dev',
        ])->willReturn(false);

        $output->writeln('<info>Plugin `nlxTest` could not be updated.</info>')
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

    private function prepareMocks(InputInterface $input, bool $hasClearCacheOption = null): void
    {
        $input->bind(Argument::any())
            ->shouldBeCalled();

        $input->isInteractive()
            ->willReturn(false);

        $input->hasArgument('command')
            ->willReturn(false);

        $input->validate()
            ->shouldBeCalled();

        $input->getArgument('plugin')
            ->willReturn('nlxTest');

        $input->getOption('env')
            ->willReturn('dev');

        $input->getOption('clear-cache')
            ->willReturn($hasClearCacheOption);
    }
}
