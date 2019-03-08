<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

use sd\SwPluginManager\Service\StoreApiConnectorInterface;

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
