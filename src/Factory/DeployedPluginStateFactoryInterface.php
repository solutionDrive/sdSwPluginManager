<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\DeployedPluginState;

interface DeployedPluginStateFactoryInterface
{
    /**
     * @param array $parsedPluginAsArray
     *
     * @return DeployedPluginState
     */
    public function createFromShopwareCLIInfoOutput($parsedPluginAsArray);
}
