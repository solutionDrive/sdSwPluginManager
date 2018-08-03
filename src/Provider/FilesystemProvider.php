<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Provider;

class FilesystemProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadFile($parameters)
    {
        if (true === empty($parameters['src'])) {
            throw new \RuntimeException('src must not be empty for FilesystemProvider.');
        }

        return $parameters['src'];
    }

    /**
     * {@inheritdoc}
     */
    public function supports($providerName)
    {
        return 'filesystem' === $providerName;
    }
}
