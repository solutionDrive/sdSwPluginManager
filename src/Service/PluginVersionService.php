<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Service;

use BadMethodCallException;
use sd\SwPluginManager\Entity\ConfiguredPluginState;

class PluginVersionService implements PluginVersionServiceInterface
{
    /** @var string[] */
    private $pluginVersions = [];

    /** @var TableParser */
    private $tableParser;

    /** @var bool */
    private $initialized = false;

    public function __construct(TableParser $tableParser)
    {
        $this->tableParser = $tableParser;
    }

    public function pluginNeedsUpdate(ConfiguredPluginState $plugin): bool
    {
        if (false === $this->initialized) {
            throw new BadMethodCallException(
                'Initialize the plugin version list by calling the parsePluginVersionsFromPluginList first'
            );
        }

        $installedVersion = $this->getPluginVersion($plugin->getId());
        $configuredVersion = $plugin->getVersion() ?? $plugin->getProviderParameters()['version'] ?? null;

        if (null !== $configuredVersion && null !== $installedVersion) {
            return \version_compare($installedVersion, $configuredVersion, '<');
        }

        return false;
    }

    public function parsePluginVersionsFromPluginList(string $pluginList): void
    {
        $pluginInfo = $this->tableParser->parse($pluginList);
        foreach ($pluginInfo as $pluginLine) {
            $name = $pluginLine[0];
            $version = $pluginLine[2];

            $this->pluginVersions[$name] = $version;
        }

        $this->initialized = true;
    }

    public function getPluginVersion(string $pluginName): ?string
    {
        return $this->pluginVersions[$pluginName] ?? null;
    }
}
