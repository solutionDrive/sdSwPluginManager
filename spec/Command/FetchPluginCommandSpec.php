<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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
    ): void {
        $this->beConstructedWith(
            $stateFile,
            $pluginFetcher
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FetchPluginCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
