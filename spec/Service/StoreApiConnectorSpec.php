<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Service\StoreApiConnector;
use sd\SwPluginManager\Service\StoreApiConnectorInterface;

class StoreApiConnectorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(StoreApiConnector::class);
    }

    public function it_implements_StoreApiConnector_interface()
    {
        $this->shouldImplement(StoreApiConnectorInterface::class);
    }
}
