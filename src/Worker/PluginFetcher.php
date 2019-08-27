<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Worker;

use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Exception\NoSuitableProviderException;
use sd\SwPluginManager\Repository\ProviderRepositoryInterface;

class PluginFetcher implements PluginFetcherInterface
{
    /** @var ProviderRepositoryInterface */
    private $providerRepository;

    public function __construct(
        ProviderRepositoryInterface $providerRepository
    ) {
        $this->providerRepository = $providerRepository;
    }

    public function fetch(ConfiguredPluginState $configuredPluginState, bool $force = false): ?string
    {
        $provider = $this->providerRepository->getProviderSupporting($configuredPluginState->getProvider());
        if (null === $provider) {
            throw new NoSuitableProviderException($configuredPluginState->getProvider());
        }

        return $provider->loadFile($configuredPluginState->getProviderParameters(), $force);
    }
}
