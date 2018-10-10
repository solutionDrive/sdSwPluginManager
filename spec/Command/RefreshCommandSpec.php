<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\RefreshCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RefreshCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RefreshCommand::class);
    }
}
