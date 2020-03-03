<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Worker;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Worker\ShopwareConsoleCaller;
use sd\SwPluginManager\Worker\ShopwareConsoleCallerInterface;

class ShopwareConsoleCallerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopwareConsoleCaller::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(ShopwareConsoleCallerInterface::class);
    }

    public function it_has_clean_state(): void
    {
        $this->hasOutput()->shouldReturn(false);
        $this->hasError()->shouldReturn(false);
        $this->getOutput()->shouldReturn('');
        $this->getError()->shouldReturn('');

        // After resetOutput() everything should be the same state
        $this->resetOutput()->shouldReturn($this);

        $this->hasOutput()->shouldReturn(false);
        $this->hasError()->shouldReturn(false);
        $this->getOutput()->shouldReturn('');
        $this->getError()->shouldReturn('');
    }

    // This won't be tested further as system calls like proc_open cannot be mocked.
}
