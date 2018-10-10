<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\InstallCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InstallCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InstallCommand::class);
    }
}
