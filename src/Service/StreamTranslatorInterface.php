<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
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
