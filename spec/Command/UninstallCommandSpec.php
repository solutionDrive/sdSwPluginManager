<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\UninstallCommand;
use Symfony\Component\Console\Command\Command;

class UninstallCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(UninstallCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
