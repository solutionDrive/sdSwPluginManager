<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sd\SwPluginManager\Service;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Service\TableParser;

class TableParserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(TableParser::class);
    }

    public function it_can_parse_a_very_simple_table()
    {
        $this->beConstructedWith(
            ',',
            "\n",
            3
        );

        $inputTable = 'firstColumn,secondColumn,third';

        $parsedTable = $this->parse($inputTable);
        $parsedTable->shouldEqual(
            [
                ['firstColumn', 'secondColumn', 'third'],
            ]
        );
    }

    public function it_can_parse_a_symfony_style_table()
    {
        $this->beConstructedWith(
            '|',
            "\n",
            3,
            true,
            true
        );

        $inputTable = <<<EOT
+-------------------+-------------------------+---------+
| firstColumn       | secondColumn            | third   |
+-------------------+-------------------------+---------+
| line1 col1        | line1 col2              | 1.3     |
| line2 col1        | line2 col2              | 2.3     |
| line3 col1        | line3 col2              | 3.3     |
+-------------------+-------------------------+---------+
EOT;

        $parsedTable = $this->parse($inputTable);
        $parsedTable->shouldEqual(
            [
                ['firstColumn', 'secondColumn', 'third'],
                ['line1 col1', 'line1 col2', '1.3'],
                ['line2 col1', 'line2 col2', '2.3'],
                ['line3 col1', 'line3 col2', '3.3'],
            ]
        );
    }

    public function it_can_parse_another_table()
    {
        $this->beConstructedWith(
            '/',
            "\n",
            2,
            false,
            false
        );

        $inputTable = <<<EOT
 firstColumn       / secondColumn            / third 
-------------------+-------------------------+-------
 line1 col1        / line1 col2              / 1.3   
 line2 col1        / line2 col2              / 2.3   
 line3 col1        / line3 col2              / 3.3   
EOT;

        $parsedTable = $this->parse($inputTable);
        $parsedTable->shouldEqual(
            [
                ['firstColumn', 'secondColumn', 'third'],
                ['line1 col1', 'line1 col2', '1.3'],
                ['line2 col1', 'line2 col2', '2.3'],
                ['line3 col1', 'line3 col2', '3.3'],
            ]
        );
    }
}
