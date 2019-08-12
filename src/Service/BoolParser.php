<?php
declare(strict_types=1);

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
    public function parse($value): bool
    {
        $normalizedValue = \trim(\strtolower($value));
        return \in_array($normalizedValue, ['yes', 'on', 'true', '1']);
    }
}
