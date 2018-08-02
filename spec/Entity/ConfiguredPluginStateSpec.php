<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Entity;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Entity\ConfiguredPluginState;

class ConfiguredPluginStateSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfiguredPluginState::class);
    }

    public function it_can_be_constructed()
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
        $this->getProviderParameters()->shouldReturn(['p1' => 'v1']);
        $this->isActivated()->shouldReturn(true);
        $this->isInstalled()->shouldReturn(true);
        $this->getEnvironments()->shouldReturn(['dev', 'prod', 'crude']);
    }

    public function it_can_be_constructed_with_other_values()
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
        $this->getProviderParameters()->shouldReturn([]);
        $this->isActivated()->shouldReturn(false);
        $this->isInstalled()->shouldReturn(false);
        $this->getEnvironments()->shouldReturn([]);
    }
}
