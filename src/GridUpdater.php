<?php
/**
 * File GridUpdater.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace PHPWeekly\Issue42;

use PHPWeekly\Issue42\Entity\Grid;
use PHPWeekly\Issue42\Enum\CellEnum;

/**
 * Class GridUpdater
 *
 * @package PHPWeekly\Issue42
 */
class GridUpdater
{
    /**
     * @var array
     */
    private $dying = [];

    /**
     * @var array
     */
    private $born = [];

    /**
     * @var array
     */
    private $fertile = [];

    /**
     * @var Grid
     */
    private $grid;

    /**
     * GridUpdater constructor
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Update cell states
     *
     * Cells with a change in states are stored to resolve the
     * updated state after calculations are completed to not interfere
     * with cell updates as we traverse the grid
     *
     * @return void
     */
    public function update()
    {
        foreach ($this->grid->iterator() as $index => $cell) {
            if ($cell === CellEnum::DEAD) continue;

            $siblings = $this->grid->getSiblings($index);
            $count = array_sum($siblings);

            if ($this->willDie($count)) {
                array_push($this->dying, $index);
            }

            foreach ($siblings as $i => $sibling) {
                if ($sibling === CellEnum::DEAD) {
                    $this->fertile[$i] = true;
                }
            }
        }

        $this->fertile = array_keys($this->fertile);

        for ($i = 0; $i < count($this->fertile); $i++) {
            $sibling = $this->fertile[$i];
            $siblings = $this->grid->getSiblings($sibling);
            $count = array_sum($siblings);

            if ($this->willReproduce($count)) {
                array_push($this->born, $sibling);
            }
        }
    }

    /**
     * Resolve cell states
     *
     * @return void
     */
    public function resolve()
    {
        for ($i = 0; $i < count($this->dying); $i++) {
            $cell = $this->dying[$i];
            $this->grid->setCell($cell, CellEnum::DEAD);
        }

        for ($i = 0; $i < count($this->born); $i++) {
            $cell = $this->born[$i];
            $this->grid->setCell($cell, CellEnum::ALIVE);
        }

        $this->reset();
    }

    /**
     * Reset cell storage
     *
     * @reutrn void
     */
    private function reset()
    {
        $this->fertile = [];
        $this->dying = [];
        $this->born = [];
    }

    /**
     * Test if cell will die in next generation
     * 
     * 1. Any live cell with fewer than two live neighbours dies, as if caused by under-population.
     * 3. Any live cell with more than three live neighbours dies, as if by over-population.
     * 
     * @param int $count
     * @return bool
     */
    private function willDie(int $count) : bool
    {
        return $count < 2 || $count > 3;
        
        
    }

    /**
     * Test if cell will reproduce in next generation
     * 
     * 4. Any dead cell with exactly three live neighbours becomes a live cell, as if by reproduction.
     * 
     * @param int $count
     * @return bool
     */
    private function willReproduce(int $count) : bool
    {
        return $count === 3;
    }
}
