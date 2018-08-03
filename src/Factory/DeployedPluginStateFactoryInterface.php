<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
