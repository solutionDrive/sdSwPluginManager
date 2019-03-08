<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

class NoneProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadFile($parameters)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($providerName)
    {
        return 'none' === $providerName;
    }
}
