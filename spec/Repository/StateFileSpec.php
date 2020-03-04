<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
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
    public function let(
        ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory,
        ConfiguredPluginState $state1,
        ConfiguredPluginState $state2
    ): void {
        $state1
            ->getId()
            ->willReturn('testPlugin1');
        $state2
            ->getId()
            ->willReturn('testPlugin2');

        $this->beConstructedWith($configuredPluginStateFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StateFile::class);
    }

    public function it_is_a_provider(): void
    {
        $this->shouldImplement(StateFileInterface::class);
    }

    public function it_can_read_from_array_and_get_plugins(
        ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory,
        ConfiguredPluginState $state1,
        ConfiguredPluginState $state2
    ): void {
        $source = [
            'plugins' => [
                'testPlugin1' => [
                    'provider' => 'none',
                    'version' => '~',
                ],
                'testPlugin2' => [
                    'provider' => 'http',
                    'version' => '1',
                    'alwaysReinstall' => false,
                    'removeDataOnReinstall' => true,
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
                    'alwaysReinstall' => true,
                    'removeDataOnReinstall' => false,
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
                    'alwaysReinstall' => false,
                    'removeDataOnReinstall' => true,
                ])
            )
            ->shouldBeCalled()
            ->willReturn($state2);

        $this->readArray($source);
        $this
            ->getPlugins()
            ->shouldReturn(['testPlugin1' => $state1, 'testPlugin2' => $state2]);
    }

    public function it_can_return_plugin_by_key(
        ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory,
        ConfiguredPluginState $state1,
        ConfiguredPluginState $state2
    ): void {
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
            ->createFromConfigurationArray(Argument::any(), Argument::any())
            ->willReturn($state1, $state2);

        $this->readArray($source);
        $this
            ->getPlugin('testPlugin1')
            ->shouldReturn($state1);
        $this
            ->getPlugin('testPlugin2')
            ->shouldReturn($state2);
    }

    // Unfortunately we cannot test readYamlStateFile() as it uses \file_get_contents() to read a real file.
}
