<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

use sd\SwPluginManager\Exception\ZipFileCouldNotBeExtractedException;
use sd\SwPluginManager\Exception\ZipFileCouldNotBeOpenedException;
use ZipArchive;

class PluginExtractor implements PluginExtractorInterface
{
    /** @var string */
    private $targetShopwareRoot = '';

    /** @var string */
    private $pluginFolder = '';

    /** @var string */
    private $legacyPluginFolder = '';

    /** @var string[] Folders which will be used to decide if it is a legacy plugin */
    private $legacyPluginRootFolders = [
        'Backend',
        'Core',
        'Frontend',
    ];

    /**
     * @param string $targetShopwareRoot root path to shopware installation where the plugin should be installed
     * @param string $pluginFolder       path to plugins inside the shop directory
     * @param string $legacyPluginFolder path to legacy plugins inside the shop directory
     */
    public function __construct(
        $targetShopwareRoot = '.',
        $pluginFolder = 'custom/plugins',
        $legacyPluginFolder = 'engine/Shopware/Plugins/Community'
    ) {
        if ('.' === $targetShopwareRoot) {
            $targetShopwareRoot = getcwd();
        }

        $this->targetShopwareRoot = $targetShopwareRoot;
        $this->pluginFolder = $pluginFolder;
        $this->legacyPluginFolder = $legacyPluginFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($sourceFile)
    {
        $zipArchive = new ZipArchive();
        $openResult = $zipArchive->open($sourceFile);
        if (true !== $openResult) {
            throw new ZipFileCouldNotBeOpenedException($sourceFile);
        }

        // Get plugins key to return
        // @TODO Verify that this works with lots of plugins (but it should...)
        $stat = $zipArchive->statIndex(0);
        $folderName = trim($stat['name'], '/');
        $extractToPath = $this->getExtractToPath($folderName);

        $extractResult = $zipArchive->extractTo($extractToPath);
        if (false === $extractResult) {
            throw new ZipFileCouldNotBeExtractedException($sourceFile);
        }

        $zipArchive->close();
        return $folderName;
    }

    /**
     * @param string $folderName Name of the folder to decide if the should extract to legacy path
     *
     * @return string
     */
    private function getExtractToPath($folderName)
    {
        if (true === $this->isLegacyPlugin($folderName)) {
            return $this->targetShopwareRoot . DIRECTORY_SEPARATOR . $this->legacyPluginFolder;
        }

        return $this->targetShopwareRoot . DIRECTORY_SEPARATOR . $this->pluginFolder;
    }

    /**
     * @param string $folderName Name of the folder to decide if the should extract to legacy path
     *
     * @return bool
     */
    private function isLegacyPlugin($folderName)
    {
        return in_array($folderName, $this->legacyPluginRootFolders, false);
    }
}
