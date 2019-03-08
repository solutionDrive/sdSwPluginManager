<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class S3ClientFactory implements S3ClientFactoryInterface
{
    /** @var string */
    private $defaultRegion;

    /**
     * @param string $defaultRegion
     */
    public function __construct(
        $defaultRegion = 'eu-central-1'
    ) {
        $this->defaultRegion = $defaultRegion;
    }

    /**
     * {@inheritdoc}
     */
    public function createClient($region = null, $profile = null)
    {
        // Use the default credential provider
        $provider = CredentialProvider::defaultProvider();

        $parameters = [
            'version' => '2006-03-01',
            'region'  => null !== $region ? $region : $this->defaultRegion,
            'credentials' => $provider,
        ];

        if (null !== $profile) {
            $parameters['profile'] = $profile;
        }

        return new S3Client($parameters);
    }
}
