<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Entity\DeployedPluginState;
use sd\SwPluginManager\Factory\DeployedPluginStateFactoryInterface;

class DeployedPluginRepository implements DeployedPluginRepositoryInterface
{
    /** @var array|DeployedPluginState[] */
    private $plugins = [];

    /** @var DeployedPluginStateFactoryInterface */
    private $deployedPluginStateFactory;

    /**
     * @param DeployedPluginStateFactoryInterface $deployedPluginStateFactory
     */
    public function __construct(DeployedPluginStateFactoryInterface $deployedPluginStateFactory)
    {
        $this->deployedPluginStateFactory = $deployedPluginStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function readFromCLIOutputArray($stateAsArray)
    {
        foreach ($stateAsArray as $pluginConfig) {
            $this->plugins[] =
                $this->deployedPluginStateFactory->createFromShopwareCLIInfoOutput($pluginConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}
