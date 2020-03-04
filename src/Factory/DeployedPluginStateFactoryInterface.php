<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
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
