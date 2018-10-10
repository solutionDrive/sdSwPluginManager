<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\ListCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ListCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ListCommand::class);
    }
}
