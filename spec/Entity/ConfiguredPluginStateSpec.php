<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Entity;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Entity\ConfiguredPluginState;

class ConfiguredPluginStateSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ConfiguredPluginState::class);
    }

    public function it_can_be_constructed(): void
    {
        $this->beConstructedWith(
            'pluginId',
            'dummy',
            '1.2.3',
            ['p1' => 'v1'],
            ['dev', 'prod', 'crude'],
            true,
            true
        );

        $this->getId()->shouldReturn('pluginId');
        $this->getProvider()->shouldReturn('dummy');
        $this->getVersion()->shouldReturn('1.2.3');
        $this->getProviderParameters()->shouldReturn(
            [
                'p1' => 'v1',
                'pluginId' => 'pluginId',
            ]
        );
        $this->isActivated()->shouldReturn(true);
        $this->isInstalled()->shouldReturn(true);
        $this->getEnvironments()->shouldReturn(['dev', 'prod', 'crude']);
    }

    public function it_can_be_constructed_with_other_values(): void
    {
        $this->beConstructedWith(
            'pluginId2',
            'dummy2',
            '4',
            [],
            [],
            false,
            false
        );

        $this->getId()->shouldReturn('pluginId2');
        $this->getProvider()->shouldReturn('dummy2');
        $this->getVersion()->shouldReturn('4');
        $this->getProviderParameters()->shouldReturn(
            [
                'pluginId' => 'pluginId2',
            ]
        );
        $this->isActivated()->shouldReturn(false);
        $this->isInstalled()->shouldReturn(false);
        $this->getEnvironments()->shouldReturn([]);
    }

    public function it_cannot_be_constructed_with_pluginId_as_parameter(): void
    {
        $this->shouldThrow(\RuntimeException::class)
            ->during(
                '__construct',
                [
                    'pluginId3',
                    'dummy3',
                    '1.33.7',
                    ['pluginId' => 'something'],
                    [],
                    true,
                    true,
                ]
            );
    }
}
