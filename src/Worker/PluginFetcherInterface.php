<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface PluginFetcherInterface
{
    public function fetch(ConfiguredPluginState $configuredPluginState): string;
}
