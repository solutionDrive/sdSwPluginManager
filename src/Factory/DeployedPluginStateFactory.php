<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\DeployedPluginState;
use sd\SwPluginManager\Service\BoolParser;

class DeployedPluginStateFactory implements DeployedPluginStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromShopwareCLIInfoOutput(
        array $parsedPluginAsArray
    ): DeployedPluginState {
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
