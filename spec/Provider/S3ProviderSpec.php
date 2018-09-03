<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Provider;

use Aws\S3\S3Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sd\SwPluginManager\Factory\S3ClientFactoryInterface;
use sd\SwPluginManager\Provider\ProviderInterface;
use sd\SwPluginManager\Provider\S3Provider;

class S3ProviderSpec extends ObjectBehavior
{
    const BUCKET = 'TESTbucket-not-existing';
    const BASEPATH = 'TESTpath/not/existing';

    public function let(
        S3ClientFactoryInterface $s3ClientFactory
    ) {
        $this->beConstructedWith($s3ClientFactory, self::BUCKET, self::BASEPATH);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(S3Provider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_can_load_simple(
        S3ClientFactoryInterface $s3ClientFactory,
        S3Client $client
    ) {
        $src = 'file.zip';

        $s3ClientFactory->createClient(Argument::any(), Argument::any())
            ->shouldBeCalled()
            ->willReturn($client);

        $client->getObject(
            Argument::allOf(
                Argument::withEntry('Bucket', self::BUCKET),
                Argument::withEntry('Key', self::BASEPATH . '/' . $src)
            )
        );

        $this->loadFile([
            'src' => $src,
        ]);
    }

    public function it_can_load_from_custom_region_and_profile(
        S3ClientFactoryInterface $s3ClientFactory,
        S3Client $client
    ) {
        $src     = 'file.zip';
        $region  = 'my-region';
        $profile = 'my-profile';

        $s3ClientFactory->createClient(Argument::exact($region), Argument::exact($profile))
            ->shouldBeCalled()
            ->willReturn($client);

        $client->getObject(
            Argument::allOf(
                Argument::withEntry('Bucket', self::BUCKET),
                Argument::withEntry('Key', self::BASEPATH . '/' . $src)
            )
        );

        $this->loadFile([
            'src'     => $src,
            'region'  => $region,
            'profile' => $profile,
        ]);
    }

    public function it_can_load_from_custom_bucket_and_path(
        S3ClientFactoryInterface $s3ClientFactory,
        S3Client $client
    ) {
        $bucket   = 'my-bucket';
        $basePath = 'base/path';
        $src      = 'file.zip';

        $s3ClientFactory->createClient(Argument::any(), Argument::any())
            ->shouldBeCalled()
            ->willReturn($client);

        $client->getObject(
            Argument::allOf(
                Argument::withEntry('Bucket', $bucket),
                Argument::withEntry('Key', $basePath . '/' . $src)
            )
        );

        $this->loadFile([
            'src'      => $src,
            'region'   => 'my-region',
            'bucket'   => $bucket,
            'basePath' => $basePath,
        ]);
    }

    public function it_cannot_load_with_empty_url()
    {
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [[]]);
        $this->shouldThrow(\RuntimeException::class)->during('loadFile', [['src' => '']]);
    }

    public function it_supports()
    {
        $this->supports('http')->shouldReturn(false);
        $this->supports('none')->shouldReturn(false);
        $this->supports('other')->shouldReturn(false);
        $this->supports('file')->shouldReturn(false);
        $this->supports('tmp')->shouldReturn(false);
        $this->supports('s3')->shouldReturn(true);
    }
}
