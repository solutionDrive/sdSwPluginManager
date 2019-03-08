<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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
    ) {
        $this->beConstructedWith($providerRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PluginFetcher::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(PluginFetcherInterface::class);
    }

    public function it_can_fetch(
        ProviderRepositoryInterface $providerRepository,
        ProviderInterface $provider,
        ConfiguredPluginState $configuredPluginState
    ) {
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
            ->loadFile($providerParameters)
            ->shouldBeCalled();

        $this->fetch($configuredPluginState);
    }

    public function it_can_throw_no_suitable_provider_exception(
        ProviderRepositoryInterface $providerRepository,
        ConfiguredPluginState $configuredPluginState
    ) {
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
