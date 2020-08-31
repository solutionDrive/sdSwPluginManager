<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Factory;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Factory\S3ClientFactory;
use sd\SwPluginManager\Factory\S3ClientFactoryInterface;

class S3ClientFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(S3ClientFactory::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(S3ClientFactoryInterface::class);
    }
}
