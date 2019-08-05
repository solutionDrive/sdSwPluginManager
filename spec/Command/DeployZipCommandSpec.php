<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\DeployZipCommand;
use Symfony\Component\Console\Command\Command;

class DeployZipCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DeployZipCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
