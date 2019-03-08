<?php

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
     *
     * @return bool
     */
    public function parse($stringValue);
}
