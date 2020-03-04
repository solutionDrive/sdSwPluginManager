<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Provider;

use sd\SwPluginManager\Factory\S3ClientFactoryInterface;

class S3Provider implements ProviderInterface
{
    /** @var S3ClientFactoryInterface */
    private $s3ClientFactory;

    /** @var string */
    private $defaultReleasesBucket;

    /** @var string */
    private $defaultReleasesBucketBasePath;

    public function __construct(
        S3ClientFactoryInterface $s3ClientFactory,
        string $defaultReleasesBucket,
        string $defaultReleasesBucketBasePath
    ) {
        $this->s3ClientFactory = $s3ClientFactory;
        $this->defaultReleasesBucket = $defaultReleasesBucket;
        $this->defaultReleasesBucketBasePath = $defaultReleasesBucketBasePath;
    }

    /**
     * {@inheritdoc}
     */
    public function loadFile(array $parameters, bool $force = false): ?string
    {
        if (true === empty($parameters['src'])) {
            throw new \RuntimeException('src must not be empty for S3Provider.');
        }

        $region = null;
        if (false === empty($parameters['region'])) {
            $region = $parameters['region'];
        }

        $profile = null;
        if (false === empty($parameters['profile'])) {
            $profile = $parameters['profile'];
        }

        $bucket = $this->defaultReleasesBucket;
        if (false === empty($parameters['bucket'])) {
            $bucket = $parameters['bucket'];
        }

        $basePath = $this->defaultReleasesBucketBasePath;
        if (false === empty($parameters['basePath'])) {
            $basePath = $parameters['basePath'];
        }

        $client = $this->s3ClientFactory->createClient($region, $profile);
        $key = $basePath . '/' . $parameters['src'];

        $tmpName = \tempnam('/tmp', 'sw-plugin-');
        $client->getObject([
            'Bucket'                     => $bucket,
            'Key'                        => $key,
            'ResponseCacheControl'       => 'No-cache',
            'ResponseExpires'            => \gmdate(DATE_RFC2822, \time()),
            'SaveAs'                     => $tmpName,
        ]);

        return $tmpName;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $providerName): bool
    {
        return 's3' === $providerName;
    }
}
