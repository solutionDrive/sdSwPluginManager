<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\DeactivateCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeactivateCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DeactivateCommand::class);
    }
}
