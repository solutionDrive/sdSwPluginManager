<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

use Psr\Http\Message\StreamInterface;

interface StreamTranslatorInterface
{
    public function translateToArray(StreamInterface $stream);
}
