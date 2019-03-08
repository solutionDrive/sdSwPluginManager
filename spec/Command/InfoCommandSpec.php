<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\InfoCommand;
use Symfony\Component\Console\Command\Command;

class InfoCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(InfoCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
