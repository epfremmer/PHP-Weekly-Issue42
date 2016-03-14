<?php
/**
 * File Grid.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */

namespace PHPWeekly\Issue42\Entity;

use PHPWeekly\Issue42\Enum\CellEnum;
use PHPWeekly\Issue42\Finder\SiblingFinder;

/**
 * Class Grid
 *
 * @package Epfremme\Issue42\Entity
 */
class Grid
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var array|int[]
     */
    private $cells;

    /**
     * @var array|array[]
     */
    private $siblings;

    /**
     * @var SiblingFinder
     */
    private $finder;

    /**
     * @var \Closure
     */
    private $seed;

    /**
     * Grid constructor
     *
     * @param int $width
     * @param int $height
     * @param \Closure $seed
     */
    public function __construct(int $width, int $height, \Closure $seed = null)
    {
        $this->width = $width;
        $this->height = $height;

        $this->cells = [];
        $this->siblings = [];

        $this->finder = new SiblingFinder($this);

        $this->seed = $seed ?: function() {
            return mt_rand(CellEnum::DEAD, CellEnum::ALIVE);
        };

        $this->initCells();
        $this->initSiblings();
    }

    /**
     * Add cells to the grid
     *
     * @return void
     */
    private function initCells()
    {
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $value = call_user_func($this->seed, $x, $y);

                array_push($this->cells, $value);
            }
        }
    }

    /**
     * Initialize cell siblings array
     *
     * note:
     *  - values stored in sibling array by reference
     *
     * @return void
     */
    private function initSiblings()
    {
        foreach ($this->iterator() as $index => $cell) {
            $siblings = array_flip($this->finder->getSiblings($index));

            foreach ($siblings as $i => $sibling) {
                $siblings[$i] = &$this->cells[$i];
            }

            array_push($this->siblings, $siblings);
        }
    }

    /**
     * Return cell iterator
     *
     * @return \Generator
     */
    public function iterator() : \Generator
    {
        for ($i = 0; $i < $this->width * $this->height; $i++) {
            yield $i => $this->cells[$i];
        }
    }

    /**
     * Return grid width
     *
     * @return int
     */
    public function getWidth() : int
    {
        return $this->width;
    }

    /**
     * Return grid height
     *
     * @return int
     */
    public function getHeight() : int
    {
        return $this->height;
    }

    /**
     * Return grid cell value
     *
     * @param int $index
     * @return int
     */
    public function getCell(int $index) : int
    {
        return $this->cells[$index];
    }

    /**
     * Set grid cell value
     *
     * @param int $index
     * @param int $value
     */
    public function setCell(int $index, int $value)
    {
        $this->cells[$index] = $value;
    }

    /**
     * Return grid cell sibling array
     *
     * @param int $index
     * @return array
     */
    public function getSiblings(int $index) : array
    {
        return $this->siblings[$index];
    }
}
