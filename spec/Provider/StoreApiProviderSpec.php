<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;
use sd\SwPluginManager\Provider\ProviderInterface;
use sd\SwPluginManager\Provider\StoreApiProvider;
use sd\SwPluginManager\Service\StreamTranslatorInterface;

class StoreApiProviderSpec extends ObjectBehavior
{
    const BASE_URL = 'https://api.shopware.com';

    const SHOPWARE_ACCOUNT_USER = 'NotExistingShopwareAccount';
    const SHOPWARE_ACCOUNT_PASSWORD = 'SuperSecurePassword';

    public function it_is_initializable()
    {
        $this->shouldHaveType(StoreApiProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
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
    }

    public function it_can_load_a_plugin_with_correct_credentials(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $accessTokenResponse,
        StreamInterface $accessCodeStream,
        Response $partnerResponse,
        StreamInterface $partnerStream,
        Response $clientshopsResponse,
        StreamInterface $clientshopsStream,
        Response $shopsResponse,
        StreamInterface $shopsStream
    ) {
        putenv('SHOPWARE_ACCOUNT_USER=' . self::SHOPWARE_ACCOUNT_USER);
        putenv('SHOPWARE_ACCOUNT_PASSWORD=' . self::SHOPWARE_ACCOUNT_PASSWORD);

        // ACCESS TOKEN
        $guzzleClient->post(
            self::BASE_URL . '/accesstokens',
            [
                RequestOptions::JSON => [
                    'shopwareId'    => self::SHOPWARE_ACCOUNT_USER,
                    'password'      => self::SHOPWARE_ACCOUNT_PASSWORD
                ]
            ]
        )
        ->shouldBeCalled()
        ->willReturn($accessTokenResponse);

        $accessTokenResponse->getStatusCode()
            ->willReturn(200);

        $accessTokenResponse->getBody()
            ->willReturn($accessCodeStream);

        $accessCodeData = [
            'token'     => 'ABCDEF',
            'userId'    => '12345'
        ];

        $streamTranslator->translateToArray($accessCodeStream)
            ->willReturn($accessCodeData);

        // CHECK FOR PARTNER ACCOUNT
        $guzzleClient->get(
            self::BASE_URL . '/partners/12345',
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => 'ABCDEF',
                ]
            ]
        )
        ->shouldBeCalled()
        ->willReturn($partnerResponse);

        $partnerResponse->getStatusCode()
            ->willReturn(200);

        $partnerResponse->getBody()
            ->willReturn($partnerStream);

        $partnerData = [
            'partnerId' => '9876',
        ];

        $streamTranslator->translateToArray($partnerStream)
            ->willReturn($partnerData);

        // GET ALL AVAILABLE PARTNER CLIENTSHOPS
        $guzzleClient->get(
            self::BASE_URL . '/partners/12345/clientshops',
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => 'ABCDEF',
                ]
            ]
        )
        ->shouldBeCalled()
        ->willReturn($clientshopsResponse);

        $clientshopsResponse->getStatusCode()
            ->willReturn(200);

        $clientshopsResponse->getBody()
            ->willReturn($clientshopsStream);

        $clientshopData = [
            [
                'id' => 1,
                'domain' => 'example.com',
            ]
        ];

        $streamTranslator->translateToArray($clientshopsStream)
            ->willReturn($clientshopData);

        // GET ALL SHOPS DIRECTLY CONNECTED TO ACCOUNT
        $guzzleClient->get(
            self::BASE_URL . '/shops?userId=12345',
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => 'ABCDEF',
                ]
            ]
        )
        ->shouldBeCalled()
        ->willReturn($shopsResponse);

        $shopsResponse->getStatusCode()
            ->willReturn(200);

        $shopsResponse->getBody()
            ->willReturn($shopsStream);

        $shopsData = [
            [
                'id' => 5,
                'domain' => 'example.org',
            ],
        ];

        $streamTranslator->translateToArray($shopsStream)
            ->willReturn($shopsData);

        $this->shouldNotThrow(\RuntimeException::class)->during('loadFile', [[]]);
    }

    public function it_cannot_connect_to_store_api_without_credentials()
    {
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [[]]);
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
