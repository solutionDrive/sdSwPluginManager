<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Provider\ProviderInterface;
use sd\SwPluginManager\Provider\StoreApiProvider;

class StoreApiProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(StoreApiProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_cannot_connect_to_store_api_without_credentials()
    {
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [[]]);
    }

    public function it_supports()
    {
        $this->supports('http')->shouldReturn(false);
        $this->supports('none')->shouldReturn(false);
        $this->supports('other')->shouldReturn(false);
        $this->supports('file')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
        $this->supports('s3')->shouldReturn(false);

        $this->supports('store_api')->shouldReturn(true);
    }
}
