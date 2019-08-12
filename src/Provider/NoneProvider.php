<?php
declare(strict_types=1);

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
    public function loadFile(array $parameters): ?string
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
