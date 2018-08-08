<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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

    /**
     * @param ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory
     */
    public function __construct(ConfiguredPluginStateFactoryInterface $configuredPluginStateFactory)
    {
        $this->configuredPluginStateFactory = $configuredPluginStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function readYamlStateFile($file)
    {
        $stateYaml = Yaml::parse(\file_get_contents($file));
        $this->readArray($stateYaml);
    }

    /**
     * {@inheritdoc}
     */
    public function readArray($stateAsArray)
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
    public function getPlugin($pluginId)
    {
        if (false === isset($this->plugins[$pluginId])) {
            return null;
        }

        return $this->plugins[$pluginId];
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}
