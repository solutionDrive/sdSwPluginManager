<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\ActivateCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActivateCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ActivateCommand::class);
    }
}
