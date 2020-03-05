<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Provider\ProviderInterface;

interface ProviderRepositoryInterface
{
    public function addProvider(ProviderInterface $provider): void;

    public function getProviderSupporting(string $type): ?ProviderInterface;
}
