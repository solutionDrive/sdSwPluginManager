<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface PluginFetcherInterface
{
    public function fetch(ConfiguredPluginState $configuredPluginState);
}
