<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

use Psr\Http\Message\StreamInterface;

class StreamTranslator implements StreamTranslatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function translateToArray(StreamInterface $stream)
    {
        return json_decode($stream, true);
    }
}
