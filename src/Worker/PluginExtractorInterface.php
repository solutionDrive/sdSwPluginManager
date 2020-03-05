<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
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
