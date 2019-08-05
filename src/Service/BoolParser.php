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
    public function parse(string $stringValue): bool
    {
        $normalizedValue = \trim(\strtolower($stringValue));
        return \in_array($normalizedValue, ['yes', 'on', 'true', '1']);
    }
}
