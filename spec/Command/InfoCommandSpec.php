<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\InfoCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InfoCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(InfoCommand::class);
    }
}
