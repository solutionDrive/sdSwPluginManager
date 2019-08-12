<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Entity;

class ConfiguredPluginState
{
    /** @var string */
    private $id;

    /** @var string */
    private $provider;

    /** @var string|null */
    private $version;

    /** @var array|mixed[] */
    private $providerParameters;

    /** @var bool */
    private $activated;

    /** @var bool */
    private $installed;

    /** @var array|string[] */
    private $environments;

    /** @var bool */
    private $alwaysReinstall;

    /** @var bool */
    private $removeDataOnReinstall;

    /**
     * @param array|mixed[]  $providerParameters
     * @param array|string[] $environments
     */
    public function __construct(
        string $id = '',
        string $provider = 'none',
        ?string $version = '',
        array $providerParameters = [],
        array $environments = [],
        bool $activated = false,
        bool $installed = false,
        bool $alwaysReinstall = true,
        bool $removeDataOnReinstall = false
    ) {
        if (true === isset($providerParameters['pluginId'])) {
            throw new \RuntimeException('The parameter "pluginId" is reserved and cannot be used. It will be filled automatically with the pluginId');
        }

        $providerParameters['pluginId'] = $id;

        $this->id = $id;
        $this->provider = $provider;
        $this->version = $version;
        $this->providerParameters = $providerParameters;
        $this->activated = $activated;
        $this->installed = $installed;
        $this->environments = $environments;
        $this->alwaysReinstall = $alwaysReinstall;
        $this->removeDataOnReinstall = $removeDataOnReinstall;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return array|mixed[]
     */
    public function getProviderParameters(): array
    {
        return $this->providerParameters;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function isInstalled(): bool
    {
        return $this->installed;
    }

    /**
     * @return array|string[]
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }

    public function getAlwaysReinstall(): bool
    {
        return $this->alwaysReinstall;
    }

    public function getRemoveDataOnReinstall(): bool
    {
        return $this->removeDataOnReinstall;
    }
}
