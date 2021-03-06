<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Worker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Entity\ConfiguredPluginState;
use sd\SwPluginManager\Exception\NoSuitableProviderException;
use sd\SwPluginManager\Provider\ProviderInterface;
use sd\SwPluginManager\Repository\ProviderRepositoryInterface;
use sd\SwPluginManager\Worker\PluginFetcher;
use sd\SwPluginManager\Worker\PluginFetcherInterface;

class PluginFetcherSpec extends ObjectBehavior
{
    public function let(
        ProviderRepositoryInterface $providerRepository
    ): void {
        $this->beConstructedWith($providerRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PluginFetcher::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(PluginFetcherInterface::class);
    }

    public function it_can_fetch(
        ProviderRepositoryInterface $providerRepository,
        ProviderInterface $provider,
        ConfiguredPluginState $configuredPluginState
    ): void {
        $providerParameters = [];

        $configuredPluginState
            ->getProviderParameters()
            ->willReturn($providerParameters);

        $configuredPluginState
            ->getProvider()
            ->willReturn('testType');

        $providerRepository
            ->getProviderSupporting(Argument::exact('testType'))
            ->shouldBeCalled()
            ->willReturn($provider);

        $provider
            ->loadFile($providerParameters, false)
            ->shouldBeCalled();

        $this->fetch($configuredPluginState);
    }

    public function it_can_force_fetch(
        ProviderRepositoryInterface $providerRepository,
        ProviderInterface $provider,
        ConfiguredPluginState $configuredPluginState
    ): void {
        $providerParameters = [];

        $configuredPluginState
            ->getProviderParameters()
            ->willReturn($providerParameters);

        $configuredPluginState
            ->getProvider()
            ->willReturn('testType');

        $providerRepository
            ->getProviderSupporting(Argument::exact('testType'))
            ->shouldBeCalled()
            ->willReturn($provider);

        $provider
            ->loadFile($providerParameters, true)
            ->shouldBeCalled();

        $this->fetch($configuredPluginState, true);
    }

    public function it_can_throw_no_suitable_provider_exception(
        ProviderRepositoryInterface $providerRepository,
        ConfiguredPluginState $configuredPluginState
    ): void {
        $providerParameters = [];

        $configuredPluginState
            ->getProviderParameters()
            ->willReturn($providerParameters);

        $configuredPluginState
            ->getProvider()
            ->willReturn('otherType');

        $providerRepository
            ->getProviderSupporting(Argument::exact('otherType'))
            ->shouldBeCalled()
            ->willReturn(null);

        $this
            ->shouldThrow(NoSuitableProviderException::class)
            ->during('fetch', [$configuredPluginState]);
    }
}
