<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface StateFileInterface
{
    /**
     * @param string $file path to the yaml file to read
     */
    public function readYamlStateFile($file);

    /**
     * @param array $stateAsArray state of the plugins as array
     */
    public function readArray($stateAsArray);

    /**
     * @return array|ConfiguredPluginState[]
     */
    public function getPlugins();
}
