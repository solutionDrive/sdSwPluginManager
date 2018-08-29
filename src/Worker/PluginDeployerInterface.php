<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

// @TODO Rename to PluginExtractor as it does not deploy but only extract a plugin
interface PluginDeployerInterface
{
    /**
     * Deploys a plugin to a shopware installation.
     *
     * @param string $sourceFile the zip file that contains the plugin that should be deployed
     */
    public function deploy($sourceFile);
}
