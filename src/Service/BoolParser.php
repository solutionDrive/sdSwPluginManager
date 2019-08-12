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
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            $value = \trim(\strtolower($value));
        }
        return \in_array($value, ['yes', 'on', 'true', '1', 1]);
    }
}
