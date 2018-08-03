<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

class ConfiguredPluginStateFactory implements ConfiguredPluginStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromConfigurationArray($pluginKey, $parsedPluginAsArray)
    {
        return new ConfiguredPluginState(
            $pluginKey,
            $parsedPluginAsArray['provider'],
            $parsedPluginAsArray['version'],
            $parsedPluginAsArray['providerParameters'],
            $parsedPluginAsArray['env'],
            $this->parseBoolean($parsedPluginAsArray['activated']),
            $this->parseBoolean($parsedPluginAsArray['installed'])
        );
    }

    /**
     * @param string $stringValue
     *
     * @return bool
     */
    private function parseBoolean($stringValue)
    {
        $normalizedValue = trim(strtolower($stringValue));
        return in_array($normalizedValue, ['yes', 'on', 'true', '1']);
    }
}
