<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Provider\NoneProvider;
use sd\SwPluginManager\Provider\ProviderInterface;

class NoneProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NoneProvider::class);
    }

    public function it_is_a_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_can_load_file(): void
    {
        $this->loadFile([])->shouldReturn(null);
    }

    public function it_supports(): void
    {
        $this->supports('none')->shouldReturn(true);
        $this->supports('other')->shouldReturn(false);
        $this->supports('http')->shouldReturn(false);
        $this->supports('file')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
    }
}
