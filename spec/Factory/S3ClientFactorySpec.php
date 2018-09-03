<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Factory;

use Aws\S3\S3Client;
use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Factory\S3ClientFactory;
use sd\SwPluginManager\Factory\S3ClientFactoryInterface;

class S3ClientFactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(S3ClientFactory::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(S3ClientFactoryInterface::class);
    }

    public function it_can_construct_s3_client()
    {
        $this->createClient()
            ->shouldHaveType(S3Client::class);
    }
}
