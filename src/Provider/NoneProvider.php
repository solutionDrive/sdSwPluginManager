<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
