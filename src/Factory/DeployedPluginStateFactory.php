<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\DeployedPluginState;
use sd\SwPluginManager\Service\BoolParser;

class DeployedPluginStateFactory implements DeployedPluginStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromShopwareCLIInfoOutput($parsedPluginAsArray)
    {
        $boolParser = new BoolParser();
        return new DeployedPluginState(
            $parsedPluginAsArray[0],
            $parsedPluginAsArray[1],
            $parsedPluginAsArray[2],
            $parsedPluginAsArray[3],
            $boolParser->parse($parsedPluginAsArray[4]),
            $boolParser->parse($parsedPluginAsArray[5])
        );
    }
}
