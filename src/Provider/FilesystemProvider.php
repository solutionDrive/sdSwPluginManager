<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Provider;

class FilesystemProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadFile(array $parameters, bool $force = false): ?string
    {
        if (true === empty($parameters['src'])) {
            throw new \RuntimeException('src must not be empty for FilesystemProvider.');
        }

        return $parameters['src'];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $providerName): bool
    {
        return 'filesystem' === $providerName;
    }
}
