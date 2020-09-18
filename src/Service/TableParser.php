<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sd\SwPluginManager\Service;

class TableParser
{
    /** @var string */
    private $cellSeparator;

    /** @var string */
    private $lineSeparator;

    /** @var int */
    private $minCellsPerRow;

    /** @var bool */
    private $stripFirstCell;

    /** @var bool */
    private $stripLastCell;

    /** @var bool */
    private $stripFirstRow;

    /**
     * @param string $cellSeparator  Character that separates columns from each other (usually "|")
     * @param string $lineSeparator  Character that separates lines from each other (usually "\n")
     * @param int    $minCellsPerRow rows with less than this number of cells are ignored in output
     * @param bool   $stripFirstCell if set to true, the first cell will be removed from each line
     * @param bool   $stripLastCell  if set to true, the last cell will be removed from each line
     * @param bool   $stripFirstRow  if set to true, the first line will be removed from the output
     */
    public function __construct(
        string $cellSeparator = '|',
        string $lineSeparator = "\n",
        int $minCellsPerRow = 6,
        bool $stripFirstCell = false,
        bool $stripLastCell = false,
        bool $stripFirstRow = true
    ) {
        $this->cellSeparator = $cellSeparator;
        $this->lineSeparator = $lineSeparator;
        $this->minCellsPerRow = $minCellsPerRow;
        $this->stripFirstCell = $stripFirstCell;
        $this->stripLastCell = $stripLastCell;
        $this->stripFirstRow = $stripFirstRow;
    }

    /**
     * @return array|mixed[]
     */
    public function parse(string $input): array
    {
        $lines = \explode($this->lineSeparator, $input);
        $items = [];

        foreach ($lines as $line) {
            $splittedLine = $this->parseLine($line);
            if (\count($splittedLine) >= $this->minCellsPerRow) {
                $items[] = $splittedLine;
            }
        }

        if ($this->stripFirstRow) {
            \array_shift($items);
        }

        return $items;
    }

    /**
     * @return array|mixed[]
     */
    private function parseLine(string $line): array
    {
        $line = \trim($line);
        $cells = \explode($this->cellSeparator, $line);
        \array_walk($cells, function (&$value, $key): void {
            $value = \trim($value);
        });

        if ($this->stripFirstCell) {
            \array_shift($cells);
        }

        if ($this->stripLastCell) {
            \array_pop($cells);
        }

        return $cells;
    }
}
