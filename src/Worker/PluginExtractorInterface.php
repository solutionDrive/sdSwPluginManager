<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

interface PluginExtractorInterface
{
    /**
     * Extracts a plugin into a shopware installation.
     *
     * @param string $sourceFile the zip file that contains the plugin that should be extracted
     */
    public function extract(string $sourceFile): string;
}
