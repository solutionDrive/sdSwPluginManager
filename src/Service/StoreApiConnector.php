<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class StoreApiConnector implements StoreApiConnectorInterface
{
    const BASE_URL = 'https://api.shopware.com';

    /** @var Client */
    private $guzzleClient;

    /** @var StreamTranslatorInterface */
    private $streamTranslator;

    /** @var string */
    private $cacheDir;

    /** @var string|null */
    private $accessToken;

    /** @var string|null */
    private $userId;

    /** @var bool */
    private $isPartnerAccount = false;

    public function __construct(
        Client $guzzleClient,
        StreamTranslatorInterface $streamTranslator,
        string $cacheDir
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->streamTranslator = $streamTranslator;
        $this->cacheDir = $cacheDir;
    }

    public function loadPlugin(string $pluginId, string $version): string
    {
        $tmpName = $this->cacheDir . DIRECTORY_SEPARATOR . 'sw-plugin-' . $pluginId . $version;
        if (\file_exists($tmpName)) {
            return $tmpName;
        }

        $partnerShops = $this->getShopsFromPartnerAccount();
        $shops = $this->getGeneralShops();
        $shop = $this->filterShopsByDomain($shops, $partnerShops);

        $licenseUrl = self::BASE_URL;
        if (true === $this->isPartnerAccount) {
            $licenseUrl .= '/partners/' . $this->getUserId();
            $licenseUrl .= '/customers/' . $shop['companyId'];
        }
        $licenseUrl .=  '/shops/' . $shop['id'] . '/pluginlicenses';

        list($statusCode, $body) = $this->doRequest(
            $licenseUrl,
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token'  => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $statusCode) {
            $licenses = $this->streamTranslator->translateToArray($body);

            $plugin = $this->filterPluginFromLicenses($pluginId, $licenses);

            // Get plugin information
            $pluginOverallId = $plugin['id'];
            $pluginSpecificId = $plugin['plugin']['id'];

            $pluginInfoUrl = self::BASE_URL;
            if (true === $this->isPartnerAccount) {
                $pluginInfoUrl .= '/partners/' . $this->getUserId();
                $pluginInfoUrl .= '/customers/' . $shop['companyId'];
            }
            $pluginInfoUrl .= '/shops/' . $shop['id'] . '/pluginlicenses/' . $pluginOverallId;

            list($statusCode, $body) = $this->doRequest(
                $pluginInfoUrl,
                [
                    RequestOptions::HEADERS => [
                        'X-Shopware-Token'  => $this->getAccessToken(),
                    ],
                ]
            );

            if (200 === $statusCode) {
                $pluginInfo = $this->streamTranslator->translateToArray($body);

                $versions = \array_column($pluginInfo['plugin']['binaries'], 'version');
                if (!\in_array($version, $versions)) {
                    throw new \RuntimeException(\sprintf('Plugin with name "%s" doesnt have the version "%s", Available versions are %s', $pluginId, $version, \implode(', ', \array_reverse($versions))));
                }

                $binaryVersion = \array_values(\array_filter($pluginInfo['plugin']['binaries'], function ($binary) use ($version) {
                    return $binary['version'] === $version;
                }))[0];

                $downloadUrl = self::BASE_URL . '/plugins/' . $pluginSpecificId . '/binaries/' . $binaryVersion['id'] . '/file?shopId=' . $shop['id'];
                $this->doRequest(
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

    private function getAccessToken(): string
    {
        if (null !== $this->accessToken) {
            return $this->accessToken;
        }

        $this->loadAccessTokens();

        return $this->accessToken;
    }

    private function getUserId(): string
    {
        if (null !== $this->userId) {
            return $this->userId;
        }

        $this->loadAccessTokens();

        return $this->userId;
    }

    private function loadAccessTokens(): void
    {
        $user = \getenv('SHOPWARE_ACCOUNT_USER');
        if (false === $user || '' === \trim($user)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_USER" should be available');
        }

        $password = \getenv('SHOPWARE_ACCOUNT_PASSWORD');
        if (false === $password || '' === \trim($password)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_ACCOUNT_PASSWORD" should be available');
        }

        list($statusCode, $body) = $this->doRequest(
            self::BASE_URL . '/accesstokens',
            [
                RequestOptions::JSON => [
                    'shopwareId' => $user,
                    'password' => $password,
                ],
            ],
            'post'
        );

        if (200 === $statusCode) {
            $accessTokenData = $this->streamTranslator->translateToArray($body);
            $this->accessToken = (string) $accessTokenData['token'];
            $this->userId = (string) $accessTokenData['userId'];
        }
    }

    /**
     * Returns all shops associated with a partner account
     *
     * First checks if it is a partner account otherwise only a empty array will be returned
     *
     * @return string[][]
     */
    private function getShopsFromPartnerAccount(): array
    {
        $shops = [];
        list($statusCode, $body) = $this->doRequest(
            self::BASE_URL . '/partners/' . $this->getUserId(),
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $statusCode) {
            $partnerData = $this->streamTranslator->translateToArray($body);
            if (false === empty($partnerData['partnerId'])) {
                $this->isPartnerAccount = true;
            }

            if (true === $this->isPartnerAccount) {
                list($statusCode, $body) = $this->doRequest(
                    self::BASE_URL . '/partners/' . $this->getUserId() . '/clientshops',
                    [
                        RequestOptions::HEADERS => [
                            'X-Shopware-Token' => $this->getAccessToken(),
                        ],
                    ]
                );

                if (200 === $statusCode) {
                    $shops = \array_merge($shops, $this->streamTranslator->translateToArray($body));
                }
            }
        }

        return $shops;
    }

    /**
     * Returns all shops which are generally associated with the account
     *
     * @return string[][]
     */
    private function getGeneralShops(): array
    {
        $shops = [];
        list($statusCode, $body) = $this->doRequest(
            self::BASE_URL . '/shops?userId=' . $this->getUserId(),
            [
                RequestOptions::HEADERS => [
                    'X-Shopware-Token' => $this->getAccessToken(),
                ],
            ]
        );

        if (200 === $statusCode) {
            $shops = \array_merge($shops, $this->streamTranslator->translateToArray($body));
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
    private function filterShopsByDomain(array $shops, array $partnerShops): array
    {
        $shopDomain = \getenv('SHOPWARE_SHOP_DOMAIN');
        if (false === $shopDomain || '' === \trim($shopDomain)) {
            throw new \RuntimeException('Environment variable "SHOPWARE_SHOP_DOMAIN" should be available');
        }

        $shop = \array_filter($partnerShops, function ($partnerShops) use ($shopDomain) {
            return $partnerShops['domain'] === $shopDomain || ('.' === \substr($partnerShops['domain'], 0, 1) && false !== \strpos($partnerShops['domain'], $shopDomain));
        });

        if (empty($shop)) {
            $shop = \array_filter($shops, function ($shops) use ($shopDomain) {
                return $shops['domain'] === $shopDomain || ('.' === \substr($shops['domain'], 0, 1) && false !== \strpos($shops['domain'], $shopDomain));
            });
            $this->isPartnerAccount = false;
        }

        if (0 === \count($shop)) {
            throw new \RuntimeException(\sprintf('Shop with given domain "%s" does not exist!', $shopDomain));
        }

        return \array_values($shop)[0];
    }

    /**
     * Filters out plugin from licenses and throws an exception if no plugin was found
     *
     * @param string[][] $licenses
     *
     * @return string[]
     */
    private function filterPluginFromLicenses(string $pluginId, array $licenses): array
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

    /**
     * @param array|mixed[] $options
     *
     * @return array|mixed[]
     */
    private function doRequest(
        string $uri,
        array $options,
        string $type = 'get'
    ): array {
        try {
            /** @var ResponseInterface $response */
            $response = $this->guzzleClient->$type(
                $uri,
                $options
            );
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
        }

        return [$statusCode, $body];
    }
}
