<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\InstallCommand;
use Symfony\Component\Console\Command\Command;

class InstallCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(InstallCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
