<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

use Psr\Http\Message\StreamInterface;

interface StreamTranslatorInterface
{
    /**
     * @return object|mixed
     */
    public function translateToArray(StreamInterface $stream);
}
