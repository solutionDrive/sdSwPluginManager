<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sd\SwPluginManager\Service;

class BoolParser implements BoolParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($stringValue)
    {
        $normalizedValue = \trim(\strtolower($stringValue));
        return \in_array($normalizedValue, ['yes', 'on', 'true', '1']);
    }
}
