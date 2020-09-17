<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sd\SwPluginManager\Service;

use PhpSpec\ObjectBehavior;
use sd\SwPluginManager\Service\TableParser;

class TableParserSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TableParser::class);
    }

    public function it_can_parse_a_very_simple_table(): void
    {
        $this->beConstructedWith(
            ',',
            "\n",
            3,
            false,
            false,
            false
        );

        $inputTable = 'firstColumn,secondColumn,third';

        $parsedTable = $this->parse($inputTable);
        $parsedTable->shouldEqual(
            [
                ['firstColumn', 'secondColumn', 'third'],
            ]
        );
    }

    public function it_can_parse_a_symfony_style_table(): void
    {
        $this->beConstructedWith(
            '|',
            "\n",
            3,
            true,
            true,
            false
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

    public function it_can_parse_another_table(): void
    {
        $this->beConstructedWith(
            '/',
            "\n",
            2,
            false,
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

    public function it_can_strip_the_first_line(): void
    {
        $this->beConstructedWith(
            '|',
            "\n",
            3,
            true,
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
                ['line1 col1', 'line1 col2', '1.3'],
                ['line2 col1', 'line2 col2', '2.3'],
                ['line3 col1', 'line3 col2', '3.3'],
            ]
        );
    }
}
