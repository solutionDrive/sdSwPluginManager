<?php
declare(strict_types=1);

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
     */
    public function createClient(?string $region = null, ?string $profile = null): S3Client;
}
