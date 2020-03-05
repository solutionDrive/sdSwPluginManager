<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Worker;

use sd\SwPluginManager\Entity\ConfiguredPluginState;

interface PluginFetcherInterface
{
    public function fetch(ConfiguredPluginState $configuredPluginState, bool $force = false): ?string;
}
