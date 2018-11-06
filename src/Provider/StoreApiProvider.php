<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use sd\SwPluginManager\Service\StoreApiConnectorInterface;
use sd\SwPluginManager\Service\StreamTranslatorInterface;

/**
 * This provider is heavily inspired by https://github.com/shyim/store-plugin-installer
 */
class StoreApiProvider implements ProviderInterface
{
    const BASE_URL = 'https://api.shopware.com';

    /** @var StoreApiConnectorInterface */
    private $storeApiConnector;

    public function __construct(
        StoreApiConnectorInterface $storeApiConnector
    ) {
        $this->storeApiConnector = $storeApiConnector;
    }

    public function loadFile($parameters)
    {
        $name = $parameters['pluginId'];
        $version = $parameters['version'];

        return $this->storeApiConnector->loadPlugin($name, $version);
    }

    public function supports($providerName)
    {
        return 'store_api' === $providerName;
    }
}
