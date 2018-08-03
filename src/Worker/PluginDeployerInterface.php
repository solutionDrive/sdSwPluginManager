<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

interface PluginDeployerInterface
{
    /**
     * Deploys a plugin to a shopware installation.
     *
     * @param string $sourceFile the zip file that contains the plugin that should be deployed
     */
    public function deploy($sourceFile);
}
