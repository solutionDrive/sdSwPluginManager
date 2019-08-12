<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\ListCommand;
use Symfony\Component\Console\Command\Command;

class ListCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ListCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
