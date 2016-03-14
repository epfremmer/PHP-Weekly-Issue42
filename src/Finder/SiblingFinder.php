<?php
/**
 * File SiblingFinder.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace PHPWeekly\Issue42\Finder;

use PHPWeekly\Issue42\Entity\Grid;

/**
 * Class SiblingFinder
 *
 * @package PHPWeekly\Issue42\Finder
 */
class SiblingFinder
{
    const TOP_LEFT     = 0b100000000;
    const TOP          = 0b010000000;
    const TOP_RIGHT    = 0b001000000;
    const LEFT         = 0b000100000;
    const RIGHT        = 0b000001000;
    const BOTTOM_LEFT  = 0b000000100;
    const BOTTOM       = 0b000000010;
    const BOTTOM_RIGHT = 0b000000001;

    /**
     * @var int
     */
    private $leftMask = 0b100100100;

    /**
     * @var int
     */
    private $rightMask = 0b001001001;

    /**
     * @var int
     */
    private $topMask = 0b111000000;

    /**
     * @var int
     */
    private $bottomMask = 0b000000111;

    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var array
     */
    private $offsets;

    /**
     * SiblingFinder constructor
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
        $this->offsets = $this->getOffsets($grid);
    }

    /**
     * Return array of sibling indicies from the target index
     *
     * @param int $index
     * @return array
     */
    public function getSiblings(int $index) : array
    {
        $isLeft = $this->isLeft($index);
        $isRight = $this->isRight($index);
        $isTop = $this->isTop($index);
        $isBottom = $this->isBottom($index);

        $siblings = [];

        foreach ($this->offsets as $position => $offset) {
            $siblingIndex = $index + $offset;

            if ($isLeft && $position & $this->leftMask
                || $isRight && $position & $this->rightMask
                || $isTop && $position & $this->topMask
                || $isBottom && $position & $this->bottomMask
            ) {
                continue;
            }

            array_push($siblings, $siblingIndex);
        }

        return $siblings;
    }

    /**
     * Calculate relative sibling offsets by the grid size
     *
     * @param Grid $grid
     * @return array
     */
    private function getOffsets(Grid $grid) : array
    {
        $width = $grid->getWidth();

        return [
            // position        => index offset
            self::TOP_LEFT     => -$width - 1,
            self::TOP          => -$width,
            self::TOP_RIGHT    => -$width + 1,
            self::LEFT         => -1,
            self::RIGHT        => +1,
            self::BOTTOM_LEFT  => $width - 1,
            self::BOTTOM       => $width,
            self::BOTTOM_RIGHT => $width + 1,
        ];
    }

    /**
     * Test if index is on the top bounds of the grid
     *
     * @param int $index
     * @return bool
     */
    private function isTop(int $index) : bool
    {
        return $index < $this->grid->getWidth();
    }

    /**
     * Test if index is on the bottom bounds of the grid
     *
     * @param int $index
     * @return bool
     */
    private function isBottom(int $index) : bool
    {
        return $index >= $this->grid->getWidth() * ($this->grid->getHeight() - 1);
    }

    /**
     * Test if index is on the left bounds of the grid
     *
     * @param int $index
     * @return bool
     */
    private function isLeft(int $index) : bool
    {
        return $index % $this->grid->getWidth() === 0;
    }

    /**
     * Test if index is on the right bounds of the grid
     *
     * @param int $index
     * @return bool
     */
    private function isRight(int $index) : bool
    {
        return ($index + 1) % $this->grid->getWidth() === 0;
    }
}
