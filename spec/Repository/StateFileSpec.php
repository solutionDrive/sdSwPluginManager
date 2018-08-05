<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Repository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Factory\ConfiguredPluginStateFactoryInterface;
use sd\SwPluginManager\Repository\StateFile;
use sd\SwPluginManager\Repository\StateFileInterface;

class StateFileSpec extends ObjectBehavior
{
    public function let(ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory)
    {
        $this->beConstructedWith($configuredPluginStateFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(StateFile::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(StateFileInterface::class);
    }

    public function it_can_read_from_array_and_get_plugins(
        ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory,
        ConfiguredPluginState $state1,
        ConfiguredPluginState $state2
    ) {
        $source = [
            'plugins' => [
                'testPlugin1' => [
                    'provider' => 'none',
                    'version' => '~',
                ],
                'testPlugin2' => [
                    'provider' => 'http',
                    'version' => '1',
                ],
            ],
        ];

        $configuredPluginStateFactory
            ->createFromConfigurationArray(
                Argument::exact('testPlugin1'),
                Argument::exact([
                    'provider' => 'none',
                    'version' => '~',
                    'installed' => true,
                    'activated' => true,
                    'env' => [],
                ])
            )
            ->shouldBeCalled()
            ->willReturn($state1);

        $configuredPluginStateFactory
            ->createFromConfigurationArray(
                Argument::exact('testPlugin2'),
                Argument::exact([
                    'provider' => 'http',
                    'version' => '1',
                    'installed' => true,
                    'activated' => true,
                    'env' => [],
                ])
            )
            ->shouldBeCalled()
            ->willReturn($state2);

        $this->readArray($source);
        $this
            ->getPlugins()
            ->shouldReturn([$state1, $state2]);
    }

    // Unfortunately we cannot test readYamlStateFile() as it uses \file_get_contents() to read a real file.
}
