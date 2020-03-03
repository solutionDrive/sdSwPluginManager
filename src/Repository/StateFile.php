<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Configuration\ConfiguredPluginConfiguration;
use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Factory\ConfiguredPluginStateFactoryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class StateFile implements StateFileInterface
{
    /** @var ConfiguredPluginStateFactoryInterface */
    private $configuredPluginStateFactory;

    /** @var array|ConfiguredPluginState[] */
    private $plugins = [];

    public function __construct(ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory)
    {
        $this->configuredPluginStateFactory = $configuredPluginStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function readYamlStateFile(string $file): void
    {
        $stateYaml = Yaml::parse(\file_get_contents($file));
        $this->readArray($stateYaml);
    }

    /**
     * {@inheritdoc}
     */
    public function readArray(array $stateAsArray): void
    {
        $processor = new Processor();
        $configuration = new ConfiguredPluginConfiguration();
        $globalState = $processor->processConfiguration($configuration, $stateAsArray);
        foreach ($globalState as $pluginId => $pluginConfig) {
            $configuredPluginState =
                $this->configuredPluginStateFactory->createFromConfigurationArray($pluginId, $pluginConfig);
            $this->plugins[$configuredPluginState->getId()] = $configuredPluginState;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugin(string $pluginId): ?ConfiguredPluginState
    {
        if (false === isset($this->plugins[$pluginId])) {
            return null;
        }

        return $this->plugins[$pluginId];
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
