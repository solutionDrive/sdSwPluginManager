<?php
declare(strict_types=1);

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

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StoreApiProvider::class);
    }

    public function it_is_a_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function let(
        StoreApiConnectorInterface $storeApiConnector
    ): void {
        $this->beConstructedWith(
            $storeApiConnector
        );
    }

    public function it_can_load_a_plugin_with_correct_credentials(
        StoreApiConnectorInterface $storeApiConnector
    ): void {
        $storeApiConnector->loadPlugin('awesomePlugin', '0.0.2', false)
            ->willReturn('/tmp/plugin');

        $this->loadFile(
            [
                'pluginId' => 'awesomePlugin',
                'version'  => '0.0.2',
            ]
        )
        ->shouldReturn('/tmp/plugin');
    }

    public function it_can_force_load_a_plugin_with_correct_credentials(
        StoreApiConnectorInterface $storeApiConnector
    ): void {
        $storeApiConnector->loadPlugin('awesomePlugin', '0.0.2', true)
            ->willReturn('/tmp/plugin');

        $this->loadFile(
            [
                'pluginId' => 'awesomePlugin',
                'version'  => '0.0.2',
            ],
            true
        )
            ->shouldReturn('/tmp/plugin');
    }

    public function it_supports(): void
    {
        $this->supports('http')->shouldReturn(false);
        $this->supports('none')->shouldReturn(false);
        $this->supports('other')->shouldReturn(false);
        $this->supports('file')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
        $this->supports('s3')->shouldReturn(false);

        $this->supports('store_api')->shouldReturn(true);
    }

    public function it_can_parse_decimal_versions(
        StoreApiConnectorInterface $storeApiConnector
    ): void {
        $storeApiConnector->loadPlugin('awesomePlugin', '0.2', true)
            ->willReturn('/tmp/plugin');

        $this->loadFile(
            [
                'pluginId' => 'awesomePlugin',
                'version'  => 0.2,
            ],
            true
        )
            ->shouldReturn('/tmp/plugin');
    }

    public function it_can_parse_integer_versions(
        StoreApiConnectorInterface $storeApiConnector
    ): void {
        $storeApiConnector->loadPlugin('awesomePlugin', '2', true)
            ->willReturn('/tmp/plugin');

        $this->loadFile(
            [
                'pluginId' => 'awesomePlugin',
                'version'  => 2,
            ],
            true
        )
            ->shouldReturn('/tmp/plugin');
    }

    public function it_can_parse_string_versions(
        StoreApiConnectorInterface $storeApiConnector
    ): void {
        $storeApiConnector->loadPlugin('awesomePlugin', 'HYPER MASTER ULTRA VERSION MK.3', true)
            ->willReturn('/tmp/plugin');

        $this->loadFile(
            [
                'pluginId' => 'awesomePlugin',
                'version'  => 'HYPER MASTER ULTRA VERSION MK.3',
            ],
            true
        )
            ->shouldReturn('/tmp/plugin');
    }
}
