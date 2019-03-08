<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Repository;

use sd\SwPluginManager\Provider\ProviderInterface;

interface ProviderRepositoryInterface
{
    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider);

    /**
     * @param string $type
     *
     * @return null|ProviderInterface
     */
    public function getProviderSupporting($type);
}
