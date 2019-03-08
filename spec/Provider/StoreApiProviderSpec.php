<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Provider\ProviderInterface;
use sd\SwPluginManager\Provider\StoreApiProvider;
use sd\SwPluginManager\Service\StoreApiConnectorInterface;

class StoreApiProviderSpec extends ObjectBehavior
{
    const BASE_URL = 'https://api.shopware.com';

    const SHOPWARE_ACCOUNT_USER = 'NotExistingShopwareAccount';
    const SHOPWARE_ACCOUNT_PASSWORD = 'SuperSecurePassword';
    const SHOPWARE_SHOP_DOMAIN = 'example.org';

    public function it_is_initializable()
    {
        $this->shouldHaveType(StoreApiProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function let(
        StoreApiConnectorInterface $storeApiConnector
    ) {
        $this->beConstructedWith(
            $storeApiConnector
        );
    }

    public function it_can_load_a_plugin_with_correct_credentials(
        StoreApiConnectorInterface $storeApiConnector
    ) {
        $storeApiConnector->loadPlugin('awesomePlugin', '0.0.2')
            ->willReturn('/tmp/plugin');

        $this->loadFile(
            [
                'pluginId' => 'awesomePlugin',
                'version'  => '0.0.2',
            ]
        )
        ->shouldReturn('/tmp/plugin');
    }

    public function it_supports()
    {
        $this->supports('http')->shouldReturn(false);
        $this->supports('none')->shouldReturn(false);
        $this->supports('other')->shouldReturn(false);
        $this->supports('file')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
        $this->supports('s3')->shouldReturn(false);

        $this->supports('store_api')->shouldReturn(true);
    }
}
