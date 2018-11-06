<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
        $shopDomain = getenv('SHOPWARE_SHOP_DOMAIN');
        if (false === $shopDomain || '' === trim($shopDomain)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_SHOP_DOMAIN" should be available');
        }

        $shops = [];

        $partnerResponse = $this->guzzleClient->get(
            self::BASE_URL . '/partners/' . $this->getUserId(),
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => $this->getAccessToken(),
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
                            'X-Shopware-Token'  => $this->getAccessToken(),
                        ],
                    ]
                );

                $shops = array_merge($shops, $this->streamTranslator->translateToArray($clientshopsResponse->getBody()));
            }
        }

        $shopsResponse = $this->guzzleClient->get(
            self::BASE_URL . '/shops?userId=' . $this->getUserId(),
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $shopsResponse->getStatusCode()) {
            $shops = array_merge($shops, $this->streamTranslator->translateToArray($shopsResponse->getBody()));
        }

        $shops = array_filter($shops, function ($shop) use ($shopDomain) {
            return $shop['domain'] === $shopDomain || ('.' === substr($shop['domain'], 0, 1) && false !== strpos($shop['domain'], $shopDomain));
        });

        if (0 === count($shops)) {
            throw new \RuntimeException(sprintf('Shop with given domain "%s" does not exist!', $shopDomain));
        }

        $shop = array_values($shops)[0];

        $licenseUrl = self::BASE_URL . '/licenses?shopId=' . $shop['id'];
        if (true === $this->isPartnerAccount) {
            $licenseUrl .= '&partnerId=' . $this->getUserId();
        }

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

            $plugin = array_filter($licenses, function ($license) use ($pluginId) {
                // Basic Plugins like SwagCore
                if (!isset($license['plugin'])) {
                    return false;
                }

                return $license['plugin']['name'] === $pluginId || $license['plugin']['code'] === $pluginId;
            });

            if (empty($plugin)) {
                throw new \RuntimeException(sprintf('Plugin with name "%s" is not available in your Account. Please buy the plugin first', $pluginId));
            }

            $plugin = array_values($plugin)[0];
            // Fix plugin name
            $pluginId = $plugin['plugin']['name'];
            $versions = array_column($plugin['plugin']['binaries'], 'version');
            if (!in_array($version, $versions)) {
                throw new \RuntimeException(sprintf('Plugin with name "%s" doesnt have the version "%s", Available versions are %s', $pluginId, $version, implode(', ', array_reverse($versions))));
            }

            $binaryVersion = array_values(array_filter($plugin['plugin']['binaries'], function ($binary) use ($version) {
                return $binary['version'] === $version;
            }))[0];

            $tmpName = '/tmp/sw-plugin-' . $pluginId . $version;
            $this->guzzleClient->get(
                self::BASE_URL . $binaryVersion['filePath'] . '?shopId=' . $shop['id'],
                [
                    RequestOptions::HEADERS => [
                        'X-Shopware-Token'  => $this->getAccessToken(),
                    ],
                    RequestOptions::SINK => $tmpName,
                ]
            );

            return $tmpName;
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
        $user = getenv('SHOPWARE_ACCOUNT_USER');
        if (false === $user || '' === trim($user)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_USER" should be available');
        }

        $password = getenv('SHOPWARE_ACCOUNT_PASSWORD');
        if (false === $password || '' === trim($password)) {
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
}
