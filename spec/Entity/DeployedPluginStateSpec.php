<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Entity;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Entity\DeployedPluginState;

class DeployedPluginStateSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(DeployedPluginState::class);
    }

    public function it_can_be_constructed()
    {
        $this->beConstructedWith(
            'pluginId',
            'dummy',
            '1.2.3',
            'Monster Inc.',
            true,
            true
        );

        $this->getId()->shouldReturn('pluginId');
        $this->getName()->shouldReturn('dummy');
        $this->getVersion()->shouldReturn('1.2.3');
        $this->getAuthor()->shouldReturn('Monster Inc.');
        $this->isActivated()->shouldReturn(true);
        $this->isInstalled()->shouldReturn(true);
    }

    public function it_can_be_constructed_with_other_values()
    {
        $this->beConstructedWith(
            'pluginId2',
            'dummy2',
            '7',
            'ACME',
            false,
            false
        );

        $this->getId()->shouldReturn('pluginId2');
        $this->getName()->shouldReturn('dummy2');
        $this->getVersion()->shouldReturn('7');
        $this->getAuthor()->shouldReturn('ACME');
        $this->isActivated()->shouldReturn(false);
        $this->isInstalled()->shouldReturn(false);
    }
}
