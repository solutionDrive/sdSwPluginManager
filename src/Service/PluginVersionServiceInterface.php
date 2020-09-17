<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Service;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface PluginVersionServiceInterface
{
    public function parsePluginVersionsFromPluginList(string $pluginInformation): void;

    public function pluginNeedsUpdate(ConfiguredPluginState $plugin): bool;
}
