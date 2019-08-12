<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

interface StoreApiConnectorInterface
{
    public function loadPlugin(string $pluginId, string $version): string;
}
