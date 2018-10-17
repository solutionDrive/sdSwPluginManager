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
                    'shopwareId'    => $user,
                    'password'      => $password
                ],
            ]
        );
        if (200 === $accessTokenResponse->getStatusCode()) {
            $accessTokenData = $this->streamTranslator->translateToArray($accessTokenResponse->getBody());

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
                }
            }
        }
    }

    public function supports($providerName)
    {
        return 'store_api' === $providerName;
    }
}
