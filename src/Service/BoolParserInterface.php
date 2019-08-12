<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

interface BoolParserInterface
{
    /**
     * @param string $stringValue value to parse
     */
    public function parse(string $stringValue): bool;
}
