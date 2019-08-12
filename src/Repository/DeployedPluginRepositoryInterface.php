<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Entity\DeployedPluginState;

interface DeployedPluginRepositoryInterface
{
    /**
     * @param array|array[] $stateAsArray
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
