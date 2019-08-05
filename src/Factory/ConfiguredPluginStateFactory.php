<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Service\BoolParser;

class ConfiguredPluginStateFactory implements ConfiguredPluginStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromConfigurationArray(
        string $pluginKey,
        array $parsedPluginAsArray
    ): ConfiguredPluginState {
        $boolParser = new BoolParser();
        return new ConfiguredPluginState(
            $pluginKey,
            $parsedPluginAsArray['provider'],
            $parsedPluginAsArray['version'],
            $parsedPluginAsArray['providerParameters'],
            $parsedPluginAsArray['env'],
            $boolParser->parse($parsedPluginAsArray['activated']),
            $boolParser->parse($parsedPluginAsArray['installed']),
            $boolParser->parse($parsedPluginAsArray['alwaysReinstall']),
            $boolParser->parse($parsedPluginAsArray['removeDataOnReinstall'])
        );
    }
}
