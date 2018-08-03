<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\DeployedPluginState;

class DeployedPluginStateFactory implements DeployedPluginStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromShopwareCLIInfoOutput($parsedPluginAsArray)
    {
        return new DeployedPluginState(
            $parsedPluginAsArray[0],
            $parsedPluginAsArray[1],
            $parsedPluginAsArray[2],
            $parsedPluginAsArray[3],
            $this->parseBoolean($parsedPluginAsArray[4]),
            $this->parseBoolean($parsedPluginAsArray[5])
        );
    }

    /**
     * @param string $stringValue
     *
     * @return bool
     *
     * @TODO Move to own service/class!
     */
    private function parseBoolean($stringValue)
    {
        $normalizedValue = trim(strtolower($stringValue));
        return in_array($normalizedValue, ['yes', 'on', 'true', '1']);
    }
}
