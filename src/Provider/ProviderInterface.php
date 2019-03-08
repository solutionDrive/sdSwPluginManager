<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

interface ProviderInterface
{
    /**
     * @param array $parameters Parameters (including source, auth data, etc.)
     *
     * @return string Path to the downloaded ZIP file
     */
    public function loadFile($parameters);

    /**
     * @param string $providerName
     *
     * @return bool
     */
    public function supports($providerName);
}
