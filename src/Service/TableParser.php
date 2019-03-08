<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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

    /**
     * @param string $cellSeparator  Character that separates columns from each other (usually "|")
     * @param string $lineSeparator  Character that separates lines from each other (usually "\n")
     * @param int    $minCellsPerRow rows with less than this number of cells are ignored in output
     * @param bool   $stripFirstCell if set to true, the first cell will be removed from each line
     * @param bool   $stripLastCell  if set to true, the last cell will be removed from each line
     */
    public function __construct(
        $cellSeparator = '|',
        $lineSeparator = "\n",
        $minCellsPerRow = 6,
        $stripFirstCell = false,
        $stripLastCell = false
    ) {
        $this->cellSeparator = $cellSeparator;
        $this->lineSeparator = $lineSeparator;
        $this->minCellsPerRow = $minCellsPerRow;
        $this->stripFirstCell = $stripFirstCell;
        $this->stripLastCell = $stripLastCell;
    }

    /**
     * @param string $input
     *
     * @return array
     */
    public function parse($input)
    {
        $lines = \explode($this->lineSeparator, $input);
        $items = [];
        foreach ($lines as $line) {
            $splittedLine = $this->parseLine($line);
            if (\count($splittedLine) >= $this->minCellsPerRow) {
                $items[] = $splittedLine;
            }
        }

        return $items;
    }

    private function parseLine($line)
    {
        $line = \trim($line);
        $cells = \explode($this->cellSeparator, $line);
        \array_walk($cells, function (&$value, $key) {
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
