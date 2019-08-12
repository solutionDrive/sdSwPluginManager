<?php
declare(strict_types=1);

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
     * @param array|mixed[] $parsedPluginAsArray
     */
    public function createFromShopwareCLIInfoOutput(
        array $parsedPluginAsArray
    ): DeployedPluginState;
}
