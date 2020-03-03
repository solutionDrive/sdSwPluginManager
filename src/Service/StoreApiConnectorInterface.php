<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Service;

interface StoreApiConnectorInterface
{
    public function loadPlugin(string $pluginId, string $version, bool $force = false): string;
}
