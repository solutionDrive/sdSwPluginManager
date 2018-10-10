<?php

namespace spec\sd\SwPluginManager\Command;

use sd\SwPluginManager\Command\DeployZipCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DeployZipCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(DeployZipCommand::class);
    }
}
