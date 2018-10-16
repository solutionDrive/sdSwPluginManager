<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

use GuzzleHttp\Client;

/**
 * This provider is heavily inspired by https://github.com/shyim/store-plugin-installer
 */
class StoreApiProvider implements ProviderInterface
{
    /** @var Client */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
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
    }

    public function supports($providerName)
    {
        return 'store_api' === $providerName;
    }
}
