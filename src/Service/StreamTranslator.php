<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
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
        return \json_decode($stream->__toString(), true);
    }
}
