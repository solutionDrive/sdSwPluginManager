<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface StateFileInterface
{
    /**
     * @param string $file path to the yaml file to read
     */
    public function readYamlStateFile(string $file): void;

    /**
     * @param array|mixed[] $stateAsArray state of the plugins as array
     */
    public function readArray(array $stateAsArray): void;

    public function getPlugin(string $pluginId): ?ConfiguredPluginState;

    /**
     * @return array|ConfiguredPluginState[]
     */
    public function getPlugins(): array;
}
