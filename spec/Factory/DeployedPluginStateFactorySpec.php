<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Factory;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Entity\DeployedPluginState;
use sd\SwPluginManager\Factory\DeployedPluginStateFactory;
use sd\SwPluginManager\Factory\DeployedPluginStateFactoryInterface;

class DeployedPluginStateFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(DeployedPluginStateFactory::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(DeployedPluginStateFactoryInterface::class);
    }

    public function it_can_create_a_configured_plugin_state_from_config()
    {
        $config = [
            'shopUltimatorPro',
            'Fanciest Shop Ultimator',
            'latest-no-doubt',
            'Monster Inc.',
            'false',
            'true',
        ];

        $plugin = $this->createFromShopwareCLIInfoOutput($config);
        $plugin->shouldHaveType(DeployedPluginState::class);
        $plugin->getName()->shouldReturn('Fanciest Shop Ultimator');
        $plugin->getAuthor()->shouldReturn('Monster Inc.');
        $plugin->getVersion()->shouldReturn('latest-no-doubt');
        $plugin->isActivated()->shouldReturn(false);
        $plugin->isInstalled()->shouldReturn(true);
    }
}
