<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
    public function let(DeployedPluginStateFactoryInterface $deployedPluginStateFactory)
    {
        $this->beConstructedWith($deployedPluginStateFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DeployedPluginRepository::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(DeployedPluginRepositoryInterface::class);
    }

    public function it_can_read_from_array_and_get_plugins(
        DeployedPluginStateFactoryInterface $deployedPluginStateFactory,
        DeployedPluginState $state1,
        DeployedPluginState $state2
    ) {
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
            ->shouldReturn([$state1, $state2]);
    }

    // [1] See:  https://sylius.com/  or  https://github.com/sylius/sylius
}
