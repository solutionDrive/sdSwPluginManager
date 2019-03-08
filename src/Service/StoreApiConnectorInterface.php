<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

interface StoreApiConnectorInterface
{
    public function loadPlugin($pluginId, $version);
}
