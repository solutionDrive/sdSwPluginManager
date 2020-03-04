<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
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

    /** @var string[] */
    private $legacyPluginFolders = [];

    /** @var string[] Folders which will be used to decide if it is a legacy plugin */
    private $legacyPluginRootFolders = [
        'Backend',
        'Core',
        'Frontend',
    ];

    /**
     * @param string   $targetShopwareRoot  root path to shopware installation where the plugin should be installed
     * @param string   $pluginFolder        path to plugins inside the shop directory
     * @param string[] $legacyPluginFolders paths to legacy plugins inside the shop directory
     */
    public function __construct(
        string $targetShopwareRoot = '.',
        string $pluginFolder = 'custom/plugins',
        array $legacyPluginFolders = [
            'Plugins/Community',
            'engine/Shopware/Plugins/Community',
            'custom/project',
        ]
    ) {
        if ('.' === $targetShopwareRoot) {
            $targetShopwareRoot = \getcwd();
        }

        $this->targetShopwareRoot = $targetShopwareRoot;
        $this->pluginFolder = $pluginFolder;
        $this->legacyPluginFolders = $legacyPluginFolders;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(string $sourceFile): string
    {
        $zipArchive = new ZipArchive();
        $openResult = $zipArchive->open($sourceFile);
        if (true !== $openResult) {
            throw new ZipFileCouldNotBeOpenedException($sourceFile);
        }

        // Get plugins key to return
        // @TODO Verify that this works with lots of plugins (but it should...)
        $stat = $zipArchive->statIndex(0);
        $folderName = \explode('/', $stat['name'])[0];
        $extractToPath = $this->getExtractToPath($folderName);

        $extractResult = $zipArchive->extractTo($extractToPath);
        if (false === $extractResult) {
            throw new ZipFileCouldNotBeExtractedException($sourceFile);
        }

        $zipArchive->close();
        return $folderName;
    }

    /**
     * @param string $folderName Name of the folder to decide if it should be extracted to a legacy path
     *
     * @throws \RuntimeException if there is no legacy path available
     */
    private function getExtractToPath(string $folderName): string
    {
        if (true === $this->isLegacyPlugin($folderName)) {
            $baseShopwarePath = $this->targetShopwareRoot . DIRECTORY_SEPARATOR;
            foreach ($this->legacyPluginFolders as $legacyPluginFolder) {
                if (\is_dir($baseShopwarePath . $legacyPluginFolder)) {
                    return $baseShopwarePath . $legacyPluginFolder;
                }
            }

            throw new \RuntimeException('Found a plugin with legacy structure, but no directory was available for installation. Checked directories: ' . \implode(', ', $this->legacyPluginFolders));
        }

        return $this->targetShopwareRoot . DIRECTORY_SEPARATOR . $this->pluginFolder;
    }

    /**
     * @param string $folderName Name of the folder to decide if it should be extracted to a legacy path
     */
    private function isLegacyPlugin(string $folderName): bool
    {
        return \in_array($folderName, $this->legacyPluginRootFolders, false);
    }
}
