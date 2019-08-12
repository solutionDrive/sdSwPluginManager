<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Repository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Entity\DeployedPluginState;
use sd\SwPluginManager\Factory\DeployedPluginStateFactoryInterface;
use sd\SwPluginManager\Repository\DeployedPluginRepository;
use sd\SwPluginManager\Repository\DeployedPluginRepositoryInterface;

class DeployedPluginRepositorySpec extends ObjectBehavior
{
    public function let(
        DeployedPluginStateFactoryInterface $deployedPluginStateFactory,
        DeployedPluginState $state1,
        DeployedPluginState $state2
    ): void {
        $state1
            ->getId()
            ->willReturn('testPlugin1');
        $state2
            ->getId()
            ->willReturn('testPlugin2');

        $this->beConstructedWith($deployedPluginStateFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DeployedPluginRepository::class);
    }

    public function it_is_a_provider(): void
    {
        $this->shouldImplement(DeployedPluginRepositoryInterface::class);
    }

    public function it_can_read_from_array_and_get_plugins(
        DeployedPluginStateFactoryInterface $deployedPluginStateFactory,
        DeployedPluginState $state1,
        DeployedPluginState $state2
    ): void {
        $source = [
            'testPlugin1' => [
                0 => 'AdvancedPerformanceBooster',
                1 => 'Very fast making plugin you have to love',
            ],
            'testPlugin2' => [
                0 => 'SyliusMigrator',
                1 => 'Plugin that helps migrating easily to Sylius eCommerce Framework [1]',
            ],
        ];

        $deployedPluginStateFactory
            ->createFromShopwareCLIInfoOutput(
                Argument::exact([
                    0 => 'AdvancedPerformanceBooster',
                    1 => 'Very fast making plugin you have to love',
                ])
            )
            ->shouldBeCalled()
            ->willReturn($state1);

        $deployedPluginStateFactory
            ->createFromShopwareCLIInfoOutput(
                Argument::exact([
                    0 => 'SyliusMigrator',
                    1 => 'Plugin that helps migrating easily to Sylius eCommerce Framework [1]',
                ])
            )
            ->shouldBeCalled()
            ->willReturn($state2);

        $this->readFromCLIOutputArray($source);
        $this
            ->getPlugins()
            ->shouldReturn(['testPlugin1' => $state1, 'testPlugin2' => $state2]);
    }

    public function it_can_return_plugin_by_key(
        DeployedPluginStateFactoryInterface $deployedPluginStateFactory,
        DeployedPluginState $state1,
        DeployedPluginState $state2
    ): void {
        $source = [
            'testPlugin1' => [/* not important, mocked! */],
            'testPlugin2' => [/* not important, mocked! */],
        ];

        $deployedPluginStateFactory
            ->createFromShopwareCLIInfoOutput(Argument::any())
            ->willReturn($state1, $state2);

        $this->readFromCLIOutputArray($source);
        $this
            ->getPlugin('testPlugin1')
            ->shouldReturn($state1);
        $this
            ->getPlugin('testPlugin2')
            ->shouldReturn($state2);
    }

    // [1] See:  https://sylius.com/  or  https://github.com/sylius/sylius
}
