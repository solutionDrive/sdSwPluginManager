<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use sd\SwPluginManager\Service\StreamTranslatorInterface;

/**
 * This provider is heavily inspired by https://github.com/shyim/store-plugin-installer
 */
class StoreApiProvider implements ProviderInterface
{
    const BASE_URL = 'https://api.shopware.com';

    /** @var Client */
    private $guzzleClient;

    /** @var StreamTranslatorInterface */
    private $streamTranslator;

    public function __construct(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->streamTranslator = $streamTranslator;
    }

    public function loadFile($parameters)
    {
        // TODO: Clean up this messy code (Refactor it to own classes etc.)
        $user = getenv('SHOPWARE_ACCOUNT_USER');
        if (false === $user || '' === trim($user)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_USER" should be available');
        }

        $password = getenv('SHOPWARE_ACCOUNT_PASSWORD');
        if (false === $password || '' === trim($password)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_PASSWORD" should be available');
        }

        $shopDomain = getenv('SHOPWARE_SHOP_DOMAIN');
        if (false === $shopDomain || '' === trim($shopDomain)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_SHOP_DOMAIN" should be available');
        }

        $name = $parameters['pluginId'];
        $version = $parameters['version'];

        $accessTokenResponse = $this->guzzleClient->post(
            self::BASE_URL . '/accesstokens',
            [
                RequestOptions::JSON => [
                    'shopwareId'    => $user,
                    'password'      => $password,
                ],
            ]
        );
        if (200 === $accessTokenResponse->getStatusCode()) {
            $accessTokenData = $this->streamTranslator->translateToArray($accessTokenResponse->getBody());
            $shops = [];

            $partnerResponse = $this->guzzleClient->get(
                self::BASE_URL . '/partners/' . $accessTokenData['userId'],
                [
                    RequestOptions::HEADERS => [
                        'X-Shopware-Token'  => $accessTokenData['token'],
                    ],
                ]
            );

            if (200 === $partnerResponse->getStatusCode()) {
                $partnerData = $this->streamTranslator->translateToArray($partnerResponse->getBody());
                $partnerId = $partnerData['partnerId'];
                if (false === empty($partnerId)) {
                    $clientshopsResponse = $this->guzzleClient->get(
                        self::BASE_URL . '/partners/' . $accessTokenData['userId'] . '/clientshops',
                        [
                            RequestOptions::HEADERS => [
                                'X-Shopware-Token'  => $accessTokenData['token'],
                            ],
                        ]
                    );

                    $shops = array_merge($shops, $this->streamTranslator->translateToArray($clientshopsResponse->getBody()));
                }
            }

            $shopsResponse = $this->guzzleClient->get(
                self::BASE_URL . '/shops?userId=' . $accessTokenData['userId'],
                [
                    RequestOptions::HEADERS => [
                        'X-Shopware-Token'  => $accessTokenData['token'],
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

            $licenseResponse = $this->guzzleClient->get(
                self::BASE_URL . '/licenses?shopId=' . $shop['id'] . '&partnerId=' . $accessTokenData['userId'],
                [
                    RequestOptions::HEADERS => [
                        'X-Shopware-Token'  => $accessTokenData['token'],
                    ],
                ]
            );

            if (200 === $licenseResponse->getStatusCode()) {
                $licenses = $this->streamTranslator->translateToArray($licenseResponse->getBody());

                $plugin = array_filter($licenses, function ($license) use ($name) {
                    // Basic Plugins like SwagCore
                    if (!isset($license['plugin'])) {
                        return false;
                    }

                    return $license['plugin']['name'] === $name || $license['plugin']['code'] === $name;
                });

                if (empty($plugin)) {
                    throw new \RuntimeException(sprintf('Plugin with name "%s" is not available in your Account. Please buy the plugin first', $name));
                }

                $plugin = array_values($plugin)[0];
                // Fix plugin name
                $name = $plugin['plugin']['name'];
                $versions = array_column($plugin['plugin']['binaries'], 'version');
                if (!in_array($version, $versions)) {
                    throw new \RuntimeException(sprintf('Plugin with name "%s" doesnt have the version "%s", Available versions are %s', $name, $version, implode(', ', array_reverse($versions))));
                }

                $binaryVersion = array_values(array_filter($plugin['plugin']['binaries'], function ($binary) use ($version) {
                    return $binary['version'] === $version;
                }))[0];

                $tmpName = '/tmp/sw-plugin-' . $name . $version;
                $this->guzzleClient->get(
                    self::BASE_URL . $binaryVersion['filePath'] . '?shopId=' . $shop['id'],
                    [
                        RequestOptions::HEADERS => [
                            'X-Shopware-Token'  => $accessTokenData['token'],
                        ],
                        RequestOptions::SINK => $tmpName,
                    ]
                );

                return $tmpName;
            }
        }
    }

    public function supports($providerName)
    {
        return 'store_api' === $providerName;
    }
}
