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
     * @return array|DeployedPluginState[]
     */
    public function getPlugins();
}
