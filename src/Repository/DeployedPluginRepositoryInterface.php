<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
    public function readFromCLIOutputArray($stateAsArray);

    /**
     * Returns the DeployedPluginState if the plugin exists, or null if it is missing in shop.
     *
     * @param string $pluginId
     *
     * @return DeployedPluginState|null
     */
    public function getPlugin($pluginId);

    /**
     * @return array|DeployedPluginState[]
     */
    public function getPlugins();
}
