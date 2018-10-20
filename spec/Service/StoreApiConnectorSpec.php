<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Service;

use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Service\StoreApiConnector;
use sd\SwPluginManager\Service\StoreApiConnectorInterface;
use sd\SwPluginManager\Service\StreamTranslatorInterface;

class StoreApiConnectorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(StoreApiConnector::class);
    }

    public function it_implements_StoreApiConnector_interface()
    {
        $this->shouldImplement(StoreApiConnectorInterface::class);
    }

    public function let(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator
    ) {
        $this->beConstructedWith(
            $guzzleClient,
            $streamTranslator
        );

        // Resets environment variables on every run
        putenv('SHOPWARE_ACCOUNT_USER=');
        putenv('SHOPWARE_ACCOUNT_PASSWORD=');
        putenv('SHOPWARE_SHOP_DOMAIN=');
    }
}
