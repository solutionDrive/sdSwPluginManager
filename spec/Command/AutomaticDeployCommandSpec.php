<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\AutomaticDeployCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Worker\PluginExtractorInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;

class AutomaticDeployCommandSpec extends ObjectBehavior
{
    public function let(
        StateFileInterface $stateFile,
        PluginFetcherInterface $pluginFetcher,
        PluginExtractorInterface $pluginExtractor
    )
    {
        $this->beConstructedWith(
            $stateFile,
            $pluginFetcher,
            $pluginExtractor
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AutomaticDeployCommand::class);
    }
}
