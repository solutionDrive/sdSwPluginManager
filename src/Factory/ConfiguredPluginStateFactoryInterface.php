<?php
declare(strict_types=1);

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
     * @param array|mixed[] $parsedPluginAsArray
     */
    public function createFromConfigurationArray(
        string $pluginKey,
        array $parsedPluginAsArray
    ): ConfiguredPluginState;
}
