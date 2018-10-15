<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\RefreshCommand;
use Symfony\Component\Console\Command\Command;

class RefreshCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RefreshCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
