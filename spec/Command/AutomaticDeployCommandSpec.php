<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\AutomaticDeployCommand;
use sd\SwPluginManager\Repository\StateFileInterface;
use sd\SwPluginManager\Service\PluginVersionServiceInterface;
use sd\SwPluginManager\Worker\PluginExtractorInterface;
use sd\SwPluginManager\Worker\PluginFetcherInterface;
use Symfony\Component\Console\Command\Command;

class AutomaticDeployCommandSpec extends ObjectBehavior
{
    public function let(
        StateFileInterface $stateFile,
        PluginFetcherInterface $pluginFetcher,
        PluginExtractorInterface $pluginExtractor,
        PluginVersionServiceInterface $pluginVersionService
    ): void {
        $this->beConstructedWith(
            $stateFile,
            $pluginFetcher,
            $pluginExtractor,
            $pluginVersionService
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AutomaticDeployCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
