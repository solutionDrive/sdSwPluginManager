<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Provider;

class NoneProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadFile(array $parameters, bool $force = false): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $providerName): bool
    {
        return 'none' === $providerName;
    }
}
