<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Entity\DeployedPluginState;

interface DeployedPluginRepositoryInterface
{
    /**
     * @param array|string[] $stateAsArray
     *
     * @return mixed
     */
    public function readFromCLIOutputArray(array $stateAsArray);

    /**
     * Returns the DeployedPluginState if the plugin exists, or null if it is missing in shop.
     */
    public function getPlugin(string $pluginId): ?DeployedPluginState;

    /**
     * @return array|DeployedPluginState[]
     */
    public function getPlugins(): array;
}
