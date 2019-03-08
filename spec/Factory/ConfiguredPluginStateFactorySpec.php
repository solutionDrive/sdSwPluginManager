<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Factory;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Factory\ConfiguredPluginStateFactory;
use sd\SwPluginManager\Factory\ConfiguredPluginStateFactoryInterface;

class ConfiguredPluginStateFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfiguredPluginStateFactory::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(ConfiguredPluginStateFactoryInterface::class);
    }

    public function it_can_create_a_configured_plugin_state_from_config()
    {
        $key = 'shopUltimatorPro';
        $config = [
            'provider' => 'fluxLoader',
            'version' => 'latest-what-else',
            'activated' => 'false',
            'installed' => 'true',
            'providerParameters' => [
                'src' => 'flux://ultimator.encoded.test/compensator.zip',
                'header' => [
                    'X-AUTH' => 'sudo you know me',
                ],
            ],
            'env' => ['unix', 'linux'],
            'alwaysReinstall' => 'false',
            'removeDataOnReinstall' => 'true',
        ];

        $plugin = $this->createFromConfigurationArray($key, $config);
        $plugin->shouldHaveType(ConfiguredPluginState::class);
        $plugin->getId()->shouldReturn($key);
        $plugin->getProvider()->shouldReturn('fluxLoader');
        $plugin->getVersion()->shouldReturn('latest-what-else');
        $plugin->getProviderParameters()->shouldReturn([
            'src' => 'flux://ultimator.encoded.test/compensator.zip',
            'header' => [
                'X-AUTH' => 'sudo you know me',
            ],
            'pluginId' => $key,
        ]);
        $plugin->isActivated()->shouldReturn(false);
        $plugin->isInstalled()->shouldReturn(true);
        $plugin->getEnvironments()->shouldReturn(['unix', 'linux']);
        $plugin->getAlwaysReinstall()->shouldReturn(false);
        $plugin->getRemoveDataOnReinstall()->shouldReturn(true);
    }
}
