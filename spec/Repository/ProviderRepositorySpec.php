<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Repository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Provider\ProviderInterface;
use sd\SwPluginManager\Repository\ProviderRepository;
use sd\SwPluginManager\Repository\ProviderRepositoryInterface;

class ProviderRepositorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ProviderRepository::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderRepositoryInterface::class);
    }

    public function it_can_register_and_return_provider(ProviderInterface $provider1, ProviderInterface $provider2)
    {
        $provider1->supports(Argument::exact('fancy'))->willReturn(true);
        $provider1->supports(Argument::exact('https'))->willReturn(false);
        $provider1->supports(Argument::exact('s3'))->willReturn(false);
        $provider2->supports(Argument::exact('fancy'))->willReturn(false);
        $provider2->supports(Argument::exact('https'))->willReturn(true);
        $provider2->supports(Argument::exact('s3'))->willReturn(false);

        $this->addProvider($provider1);
        $this->addProvider($provider2);
        $this->getProviderSupporting('fancy')->shouldReturn($provider1);
        $this->getProviderSupporting('https')->shouldReturn($provider2);
        $this->getProviderSupporting('s3')->shouldReturn(null);
    }
}
