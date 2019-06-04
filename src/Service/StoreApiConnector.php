<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class StoreApiConnector implements StoreApiConnectorInterface
{
    const BASE_URL = 'https://api.shopware.com';

    /** @var Client */
    private $guzzleClient;

    /** @var StreamTranslatorInterface */
    private $streamTranslator;

    /** @var string|null */
    private $accessToken;

    /** @var string|null */
    private $userId;

    /** @var bool */
    private $isPartnerAccount = false;

    public function __construct(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->streamTranslator = $streamTranslator;
    }

    public function loadPlugin($pluginId, $version)
    {
        $partnerShops = $this->getShopsFromPartnerAccount();
        $shops = $this->getGeneralShops();

        $shop = $this->filterShopsByDomain($shops, $partnerShops);

        $licenseUrl = self::BASE_URL;
        if (true === $this->isPartnerAccount) {
            $licenseUrl .= '/partners/' . $this->getUserId();
            $licenseUrl .= '/customers/' . $shop['companyId'];
        }
        $licenseUrl .=  '/shops/' . $shop['id'] . '/pluginlicenses';

        $licenseResponse = $this->guzzleClient->get(
            $licenseUrl,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $licenseResponse->getStatusCode()) {
            $licenses = $this->streamTranslator->translateToArray($licenseResponse->getBody());

            $plugin = $this->filterPluginFromLicenses($pluginId, $licenses);

            // Get plugin information
            $pluginOverallId = $plugin['id'];
            $pluginName = $plugin['plugin']['name'];
            $pluginSpecificId = $plugin['plugin']['id'];

            $pluginInfoUrl = self::BASE_URL;
            if (true === $this->isPartnerAccount) {
                $pluginInfoUrl .= '/partners/' . $this->getUserId();
                $pluginInfoUrl .= '/customers/' . $shop['companyId'];
            }
            $pluginInfoUrl .= '/shops/' . $shop['id'] . '/pluginlicenses/' . $pluginOverallId;

            $pluginInfoResponse = $this->guzzleClient->get(
                $pluginInfoUrl,
                [
                    RequestOptions::HEADERS => [
                        'X-Shopware-Token'  => $this->getAccessToken(),
                    ],
                ]
            );

            if (200 === $pluginInfoResponse->getStatusCode()) {
                $pluginInfo = $this->streamTranslator->translateToArray($pluginInfoResponse->getBody());

                $versions = \array_column($pluginInfo['plugin']['binaries'], 'version');
                if (!\in_array($version, $versions)) {
                    throw new \RuntimeException(\sprintf('Plugin with name "%s" doesnt have the version "%s", Available versions are %s', $pluginId, $version, \implode(', ', \array_reverse($versions))));
                }

                $binaryVersion = \array_values(\array_filter($pluginInfo['plugin']['binaries'], function ($binary) use ($version) {
                    return $binary['version'] === $version;
                }))[0];

                $tmpName = '/tmp/sw-plugin-' . $pluginName . $version;
                $downloadUrl = self::BASE_URL . '/plugins/' . $pluginSpecificId . '/binaries/' . $binaryVersion['id'] . '/file?shopId=' . $shop['id'];
                $this->guzzleClient->get(
                    $downloadUrl,
                    [
                        RequestOptions::HEADERS => [
                            'X-Shopware-Token' => $this->getAccessToken(),
                        ],
                        RequestOptions::SINK => $tmpName,
                    ]
                );

                return $tmpName;
            }
        }

        return '';
    }

    private function getAccessToken()
    {
        if (null !== $this->accessToken) {
            return $this->accessToken;
        }

        $this->loadAccessTokens();

        return $this->accessToken;
    }

    private function getUserId()
    {
        if (null !== $this->userId) {
            return $this->userId;
        }

        $this->loadAccessTokens();

        return $this->userId;
    }

    private function loadAccessTokens()
    {
        $user = \getenv('SHOPWARE_ACCOUNT_USER');
        if (false === $user || '' === \trim($user)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_USER" should be available');
        }

        $password = \getenv('SHOPWARE_ACCOUNT_PASSWORD');
        if (false === $password || '' === \trim($password)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_PASSWORD" should be available');
        }

        $accessTokenResponse = $this->guzzleClient->post(
            self::BASE_URL . '/accesstokens',
            [
                RequestOptions::JSON => [
                    'shopwareId' => $user,
                    'password' => $password,
                ],
            ]
        );
        if (200 === $accessTokenResponse->getStatusCode()) {
            $accessTokenData = $this->streamTranslator->translateToArray($accessTokenResponse->getBody());
            $this->accessToken = $accessTokenData['token'];
            $this->userId = $accessTokenData['userId'];
        }
    }

    /**
     * Returns all shops associated with a partner account
     *
     * First checks if it is a partner account otherwise only a empty array will be returned
     *
     * @return string[][]
     */
    private function getShopsFromPartnerAccount()
    {
        $shops = [];

        $partnerResponse = $this->guzzleClient->get(
            self::BASE_URL . '/partners/' . $this->getUserId(),
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $partnerResponse->getStatusCode()) {
            $partnerData = $this->streamTranslator->translateToArray($partnerResponse->getBody());
            if (false === empty($partnerData['partnerId'])) {
                $this->isPartnerAccount = true;
            }

            if (true === $this->isPartnerAccount) {
                $clientshopsResponse = $this->guzzleClient->get(
                    self::BASE_URL . '/partners/' . $this->getUserId() . '/clientshops',
                    [
                        RequestOptions::HEADERS => [
                            'X-Shopware-Token' => $this->getAccessToken(),
                        ],
                    ]
                );

                $shops = \array_merge($shops, $this->streamTranslator->translateToArray($clientshopsResponse->getBody()));
            }
        }

        return $shops;
    }

    /**
     * Returns all shops which are generally associated with the account
     *
     * @return string[][]
     */
    private function getGeneralShops()
    {
        $shops = [];

        $shopsResponse = $this->guzzleClient->get(
            self::BASE_URL . '/shops?userId=' . $this->getUserId(),
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $shopsResponse->getStatusCode()) {
            $shops = \array_merge($shops, $this->streamTranslator->translateToArray($shopsResponse->getBody()));
        }

        return $shops;
    }

    /**
     * Filters out the shop by domain and throws an exception if no shop is left afterwards
     *
     * @param string[][] $shops
     * @param string[][] $partnerShops
     *
     * @return string[]
     */
    private function filterShopsByDomain($shops, $partnerShops)
    {
        $shopDomain = \getenv('SHOPWARE_SHOP_DOMAIN');
        if (false === $shopDomain || '' === \trim($shopDomain)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_SHOP_DOMAIN" should be available');
        }

        $shops = \array_merge($shops, $partnerShops);

        $shops = \array_filter($shops, function ($shop) use ($shopDomain) {
            return $shop['domain'] === $shopDomain || ('.' === \substr($shop['domain'], 0, 1) && false !== \strpos($shop['domain'], $shopDomain));
        });

        if (0 === \count($shops)) {
            throw new \RuntimeException(\sprintf('Shop with given domain "%s" does not exist!', $shopDomain));
        }

        return \array_values($shops)[0];
    }

    /**
     * Filters out plugin from licenses and throws an exception if no plugin was found
     *
     * @param string     $pluginId
     * @param string[][] $licenses
     *
     * @return string[]
     */
    private function filterPluginFromLicenses($pluginId, $licenses)
    {
        $plugin = \array_filter($licenses, function ($license) use ($pluginId) {
            // Basic Plugins like SwagCore
            if (!isset($license['plugin'])) {
                return false;
            }

            return $license['plugin']['name'] === $pluginId || $license['plugin']['code'] === $pluginId;
        });

        if (empty($plugin)) {
            throw new \RuntimeException(\sprintf('Plugin with name "%s" is not available in your Account. Please buy the plugin first', $pluginId));
        }

        $plugin = \array_values($plugin)[0];
        return $plugin;
    }
}
