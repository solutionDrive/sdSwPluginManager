<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

interface PluginExtractorInterface
{
    /**
     * Extracts a plugin into a shopware installation.
     *
     * @param string $sourceFile the zip file that contains the plugin that should be extracted
     */
    public function extract($sourceFile);
}
