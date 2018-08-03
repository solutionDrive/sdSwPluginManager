<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
        ]);
        $plugin->isActivated()->shouldReturn(false);
        $plugin->isInstalled()->shouldReturn(true);
        $plugin->getEnvironments()->shouldReturn(['unix', 'linux']);
    }

    public function it_can_handle_other_boolean_representations()
    {
        $key = 'shopUltimatorPro2';

        $config = [
            'activated' => 'off',
            'installed' => '0',
            'provider' => '',
            'version' => '',
            'providerParameters' => [],
            'env' => [],
        ];

        $plugin = $this->createFromConfigurationArray($key, $config);
        $plugin->isActivated()->shouldReturn(false);
        $plugin->isInstalled()->shouldReturn(false);

        $config = [
            'activated' => 'no',
            'installed' => 'none',
            'provider' => '',
            'version' => '',
            'providerParameters' => [],
            'env' => [],
        ];

        $plugin = $this->createFromConfigurationArray($key, $config);
        $plugin->isActivated()->shouldReturn(false);
        $plugin->isInstalled()->shouldReturn(false);

        $config = [
            'activated' => 'oN',
            'installed' => 'Yes',
            'provider' => '',
            'version' => '',
            'providerParameters' => [],
            'env' => [],
        ];

        $plugin = $this->createFromConfigurationArray($key, $config);
        $plugin->isActivated()->shouldReturn(true);
        $plugin->isInstalled()->shouldReturn(true);

        $config = [
            'activated' => 'TrUe',
            'installed' => '1',
            'provider' => '',
            'version' => '',
            'providerParameters' => [],
            'env' => [],
        ];

        $plugin = $this->createFromConfigurationArray($key, $config);
        $plugin->isActivated()->shouldReturn(true);
        $plugin->isInstalled()->shouldReturn(true);
    }
}
