<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\FetchPluginCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;

class FetchPluginCommandSpec extends ObjectBehavior
{
    public function let(
        StateFileInterface $stateFile,
        PluginFetcherInterface $pluginFetcher
    )
    {
        $this->beConstructedWith(
            $stateFile,
            $pluginFetcher
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FetchPluginCommand::class);
    }
}
