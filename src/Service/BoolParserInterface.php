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
     * @param mixed $value value to parse
     */
    public function parse($value): bool;
}
