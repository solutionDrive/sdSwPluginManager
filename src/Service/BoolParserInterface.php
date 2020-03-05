<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Service;

interface BoolParserInterface
{
    /**
     * @param mixed $value value to parse
     */
    public function parse($value): bool;
}
