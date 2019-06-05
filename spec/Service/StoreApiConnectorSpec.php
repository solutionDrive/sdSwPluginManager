<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;
use sd\SwPluginManager\Service\StoreApiConnector;
use sd\SwPluginManager\Service\StoreApiConnectorInterface;
use sd\SwPluginManager\Service\StreamTranslatorInterface;

class StoreApiConnectorSpec extends ObjectBehavior
{
    const BASE_URL = 'https://api.shopware.com';

    const SHOPWARE_ACCOUNT_USER = 'NotExistingShopwareAccount';
    const SHOPWARE_ACCOUNT_PASSWORD = 'SuperSecurePassword';
    const SHOPWARE_SHOP_DOMAIN = 'example.org';

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
        \putenv('SHOPWARE_ACCOUNT_USER=');
        \putenv('SHOPWARE_ACCOUNT_PASSWORD=');
        \putenv('SHOPWARE_SHOP_DOMAIN=');
    }

    public function it_can_load_a_plugin_only_if_domain_exists_in_partner_account(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $accessTokenResponse,
        StreamInterface $accessCodeStream,
        Response $partnerResponse,
        StreamInterface $partnerStream,
        Response $clientshopsResponse,
        StreamInterface $clientshopsStream,
        Response $shopsResponse,
        StreamInterface $shopsStream,
        Response $licenseResponse,
        StreamInterface $licenseStream,
        Response $pluginInfoResponse,
        StreamInterface $pluginInfoStream,
        Response $pluginResponse
    ) {
        \putenv('SHOPWARE_ACCOUNT_USER=' . self::SHOPWARE_ACCOUNT_USER);
        \putenv('SHOPWARE_ACCOUNT_PASSWORD=' . self::SHOPWARE_ACCOUNT_PASSWORD);
        \putenv('SHOPWARE_SHOP_DOMAIN=' . self::SHOPWARE_SHOP_DOMAIN);

        // ACCESS TOKEN
        $this->prepareAccessToken($guzzleClient, $streamTranslator, $accessTokenResponse, $accessCodeStream);

        // CHECK FOR PARTNER ACCOUNT
        $partnerData = [
            'partnerId' => '9876',
        ];
        $this->preparePartnerAccountCheck($guzzleClient, $streamTranslator, $partnerResponse, $partnerStream, $partnerData);

        $clientshopData = [
            [
                'id' => 1,
                'companyId' => 27,
                'domain' => 'example.org',
            ],
        ];
        // GET ALL AVAILABLE PARTNER CLIENTSHOPS
        $this->preparePartnerAccount($guzzleClient, $streamTranslator, $clientshopsResponse, $clientshopsStream, $clientshopData);

        // GET ALL SHOPS DIRECTLY CONNECTED TO ACCOUNT
        $shopsData = [
            [
                'id' => 5,
                'companyId' => 87,
                'domain' => 'example.org',
            ],
        ];
        $this->prepareShops($guzzleClient, $streamTranslator, $shopsResponse, $shopsStream, $shopsData);

        // GET ALL LICENSES
        $licenseUrl = '/partners/12345/customers/27/shops/1/pluginlicenses';
        $this->prepareLicenseData($guzzleClient, $streamTranslator, $licenseResponse, $licenseStream, $licenseUrl);

        // GET ALL INFOS ABOUT PLUGIN
        $pluginInfoUrl = '/partners/12345/customers/27/shops/1/pluginlicenses/17';
        $this->preparePluginInfoData($guzzleClient, $streamTranslator, $pluginInfoResponse, $pluginInfoStream, $pluginInfoUrl);

        $downloadUrl = '/plugins/58/binaries/10/file?shopId=1';
        $guzzleClient->get(
            self::BASE_URL . $downloadUrl,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => 'ABCDEF',
                ],
                RequestOptions::SINK => '/tmp/sw-plugin-awesomePlugin0.0.2',
            ]
        )
            ->shouldBeCalled()
            ->willReturn($pluginResponse);

        $this->loadPlugin('awesomePlugin', '0.0.2');
    }

    public function it_can_load_a_plugin_only_if_domain_exists_in_normal_shop(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $accessTokenResponse,
        StreamInterface $accessCodeStream,
        Response $partnerResponse,
        StreamInterface $partnerStream,
        Response $clientshopsResponse,
        StreamInterface $clientshopsStream,
        Response $shopsResponse,
        StreamInterface $shopsStream,
        Response $licenseResponse,
        StreamInterface $licenseStream,
        Response $pluginInfoResponse,
        StreamInterface $pluginInfoStream,
        Response $pluginResponse
    ) {
        \putenv('SHOPWARE_ACCOUNT_USER=' . self::SHOPWARE_ACCOUNT_USER);
        \putenv('SHOPWARE_ACCOUNT_PASSWORD=' . self::SHOPWARE_ACCOUNT_PASSWORD);
        \putenv('SHOPWARE_SHOP_DOMAIN=' . self::SHOPWARE_SHOP_DOMAIN);

        // ACCESS TOKEN
        $this->prepareAccessToken($guzzleClient, $streamTranslator, $accessTokenResponse, $accessCodeStream);

        // CHECK FOR PARTNER ACCOUNT
        $partnerData = [
            'partnerId' => '9876',
        ];
        $this->preparePartnerAccountCheck($guzzleClient, $streamTranslator, $partnerResponse, $partnerStream, $partnerData);

        $clientshopData = [
            [
                'id' => 1,
                'companyId' => 27,
                'domain' => 'example.com',
            ],
        ];
        // GET ALL AVAILABLE PARTNER CLIENTSHOPS
        $this->preparePartnerAccount($guzzleClient, $streamTranslator, $clientshopsResponse, $clientshopsStream, $clientshopData);

        // GET ALL SHOPS DIRECTLY CONNECTED TO ACCOUNT
        $shopsData = [
            [
                'id' => 7,
                'domain' => 'example.org',
            ],
        ];
        $this->prepareShops($guzzleClient, $streamTranslator, $shopsResponse, $shopsStream, $shopsData);

        // GET ALL LICENSES
        $licenseUrl = '/shops/7/pluginlicenses';
        $this->prepareLicenseData($guzzleClient, $streamTranslator, $licenseResponse, $licenseStream, $licenseUrl);

        // GET ALL INFOS ABOUT PLUGIN
        $pluginInfoUrl = '/shops/7/pluginlicenses/17';
        $this->preparePluginInfoData($guzzleClient, $streamTranslator, $pluginInfoResponse, $pluginInfoStream, $pluginInfoUrl);

        $downloadUrl = '/plugins/58/binaries/10/file?shopId=7';
        $guzzleClient->get(
            self::BASE_URL . $downloadUrl,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => 'ABCDEF',
                ],
                RequestOptions::SINK => '/tmp/sw-plugin-awesomePlugin0.0.2',
            ]
        )
            ->shouldBeCalled()
            ->willReturn($pluginResponse);

        $this->loadPlugin('awesomePlugin', '0.0.2');
    }

    public function it_can_load_a_plugin_without_a_partner_account(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $accessTokenResponse,
        StreamInterface $accessCodeStream,
        Response $partnerResponse,
        StreamInterface $partnerStream,
        Response $shopsResponse,
        StreamInterface $shopsStream,
        Response $licenseResponse,
        StreamInterface $licenseStream,
        Response $pluginInfoResponse,
        StreamInterface $pluginInfoStream,
        Response $pluginResponse
    ) {
        \putenv('SHOPWARE_ACCOUNT_USER=' . self::SHOPWARE_ACCOUNT_USER);
        \putenv('SHOPWARE_ACCOUNT_PASSWORD=' . self::SHOPWARE_ACCOUNT_PASSWORD);
        \putenv('SHOPWARE_SHOP_DOMAIN=' . self::SHOPWARE_SHOP_DOMAIN);

        // ACCESS TOKEN
        $this->prepareAccessToken($guzzleClient, $streamTranslator, $accessTokenResponse, $accessCodeStream);

        // CHECK FOR PARTNER ACCOUNT
        $partnerData = [];
        $this->preparePartnerAccountCheck($guzzleClient, $streamTranslator, $partnerResponse, $partnerStream, $partnerData);

        // GET ALL SHOPS DIRECTLY CONNECTED TO ACCOUNT
        $shopsData = [
            [
                'id' => 7,
                'domain' => 'example.org',
            ],
        ];
        $this->prepareShops($guzzleClient, $streamTranslator, $shopsResponse, $shopsStream, $shopsData);

        // GET ALL LICENSES
        $licenseUrl = '/shops/7/pluginlicenses';
        $this->prepareLicenseData($guzzleClient, $streamTranslator, $licenseResponse, $licenseStream, $licenseUrl);

        // GET ALL INFOS ABOUT PLUGIN
        $pluginInfoUrl = '/shops/7/pluginlicenses/17';
        $this->preparePluginInfoData($guzzleClient, $streamTranslator, $pluginInfoResponse, $pluginInfoStream, $pluginInfoUrl);

        $downloadUrl = '/plugins/58/binaries/10/file?shopId=7';
        $guzzleClient->get(
            self::BASE_URL . $downloadUrl,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => 'ABCDEF',
                ],
                RequestOptions::SINK => '/tmp/sw-plugin-awesomePlugin0.0.2',
            ]
        )
            ->shouldBeCalled()
            ->willReturn($pluginResponse);

        $this->loadPlugin('awesomePlugin', '0.0.2');
    }

    public function it_cannot_connect_to_store_api_without_credentials()
    {
        $this->shouldThrow(\RuntimeException::class)->during('loadPlugin', ['awesomePlugin', '0.0.2']);
    }

    private function prepareAccessToken(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $accessTokenResponse,
        StreamInterface $accessCodeStream
    ) {
        $guzzleClient->post(
            self::BASE_URL . '/accesstokens',
            [
                RequestOptions::JSON => [
                    'shopwareId' => self::SHOPWARE_ACCOUNT_USER,
                    'password' => self::SHOPWARE_ACCOUNT_PASSWORD,
                ],
            ]
        )
            ->shouldBeCalled()
            ->willReturn($accessTokenResponse);

        $accessTokenResponse->getStatusCode()
            ->willReturn(200);

        $accessTokenResponse->getBody()
            ->willReturn($accessCodeStream);

        $accessCodeData = [
            'token' => 'ABCDEF',
            'userId' => '12345',
        ];

        $streamTranslator->translateToArray($accessCodeStream)
            ->willReturn($accessCodeData);
    }

    private function preparePartnerAccountCheck(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $partnerResponse,
        StreamInterface $partnerStream,
        array $partnerData
    ) {
        $guzzleClient->get(
            self::BASE_URL . '/partners/12345',
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => 'ABCDEF',
                ],
            ]
        )
            ->shouldBeCalled()
            ->willReturn($partnerResponse);

        $partnerResponse->getStatusCode()
            ->willReturn(200);

        $partnerResponse->getBody()
            ->willReturn($partnerStream);

        $streamTranslator->translateToArray($partnerStream)
            ->willReturn($partnerData);
    }

    private function preparePartnerAccount(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $clientshopsResponse,
        StreamInterface $clientshopsStream,
        array $clientshopData
    ) {
        $guzzleClient->get(
            self::BASE_URL . '/partners/12345/clientshops',
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => 'ABCDEF',
                ],
            ]
        )
            ->shouldBeCalled()
            ->willReturn($clientshopsResponse);

        $clientshopsResponse->getStatusCode()
            ->willReturn(200);

        $clientshopsResponse->getBody()
            ->willReturn($clientshopsStream);

        $streamTranslator->translateToArray($clientshopsStream)
            ->willReturn($clientshopData);
    }

    private function prepareShops(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $shopsResponse,
        StreamInterface $shopsStream,
        array $shopsData
    ) {
        $guzzleClient->get(
            self::BASE_URL . '/shops?userId=12345',
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => 'ABCDEF',
                ],
            ]
        )
            ->shouldBeCalled()
            ->willReturn($shopsResponse);

        $shopsResponse->getStatusCode()
            ->willReturn(200);

        $shopsResponse->getBody()
            ->willReturn($shopsStream);

        $streamTranslator->translateToArray($shopsStream)
            ->willReturn($shopsData);
    }

    private function prepareLicenseData(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $licenseResponse,
        StreamInterface $licenseStream,
        string $url
    ) {
        $guzzleClient->get(
            self::BASE_URL . $url,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => 'ABCDEF',
                ],
            ]
        )
            ->shouldBeCalled()
            ->willReturn($licenseResponse);

        $licenseResponse->getStatusCode()
            ->willReturn(200);

        $licenseResponse->getBody()
            ->willReturn($licenseStream);

        $licenseData = [
            [
                'id' => 17,
                'plugin' => [
                    'id' => 58,
                    'name' => 'awesomePlugin',
                ],
            ],
        ];

        $streamTranslator->translateToArray($licenseStream)
            ->willReturn($licenseData);
    }

    private function preparePluginInfoData(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        Response $pluginInfoResponse,
        StreamInterface $pluginInfoStream,
        string $url
    ) {
        $guzzleClient->get(
            self::BASE_URL . $url,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => 'ABCDEF',
                ],
            ]
        )
            ->shouldBeCalled()
            ->willReturn($pluginInfoResponse);

        $pluginInfoResponse->getStatusCode()
            ->willReturn(200);

        $pluginInfoResponse->getBody()
            ->willReturn($pluginInfoStream);

        $data = [
            'id' => 17,
            'plugin' => [
                'id' => 58,
                'name' => 'awesomePlugin',
                'binaries' => [
                    [
                        'id' => 3,
                        'version' => '0.0.1',
                        'filePath' => '/plugin0.0.1',
                    ],
                    [
                        'id' => 10,
                        'version' => '0.0.2',
                        'filePath' => '/plugin0.0.2',
                    ],
                ],
            ],
        ];

        $streamTranslator->translateToArray($pluginInfoStream)
            ->willReturn($data);
    }
}
