<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface ConfiguredPluginStateFactoryInterface
{
    /**
     * @param string $pluginKey
     * @param array  $parsedPluginAsArray
     *
     * @return ConfiguredPluginState
     */
    public function createFromConfigurationArray($pluginKey, $parsedPluginAsArray);
}
