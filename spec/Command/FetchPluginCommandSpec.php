<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\FetchPluginCommand;
use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;
use Symfony\Component\Console\Command\Command;

class FetchPluginCommandSpec extends ObjectBehavior
{
    public function let(
        StateFileInterface $stateFile,
        PluginFetcherInterface $pluginFetcher
    ) {
        $this->beConstructedWith(
            $stateFile,
            $pluginFetcher
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FetchPluginCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
