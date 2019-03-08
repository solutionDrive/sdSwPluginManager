<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Factory;

use Aws\S3\S3Client;

interface S3ClientFactoryInterface
{
    /**
     * @param null|string $region  the AWS region to use or `null` for default
     * @param null|string $profile the AWS profile to use if using ~/.aws/credentials file
     *
     * @return S3Client
     */
    public function createClient($region = null, $profile = null);
}
