<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Command;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Command\UninstallCommand;
use Symfony\Component\Console\Command\Command;

class UninstallCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UninstallCommand::class);
        $this->shouldHaveType(Command::class);
    }
}
