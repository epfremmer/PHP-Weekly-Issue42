<?php
/**
 * File GridRenderer.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace PHPWeekly\Issue42;

use PHPWeekly\Issue42\Entity\Grid;
use PHPWeekly\Issue42\Enum\CellEnum;

/**
 * Class GridRenderer
 *
 * @package PHPWeekly\Issue42
 */
class GridRenderer
{
    /**
     * @var int
     */
    private $lines = 0;

    /**
     * @var Grid
     */
    private $grid;

    /**
     * GridRenderer constructor
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render the grid
     *
     * @return void
     */
    public function render()
    {
        if (getenv(HEADLESS_ENV) === HEADLESS_FLAG) {
            return;
        }

        $this->clear();

        $width = $this->grid->getWidth();

        $out = '';
        $count = 0;

        foreach ($this->grid->iterator() as $index => $cell) {
            $out .= $cell === CellEnum::ALIVE ? '#' : '.';
            $count++;

            if ($count === $width) {
                $out .= PHP_EOL;
                $this->lines++;

                $count = 0;
            }
        }

        echo $out;
    }

    /**
     * Clear current grid output
     *
     * @return void
     */
    private function clear()
    {
        if (!$this->lines) {
            return;
        }

        echo sprintf("\033[%sA", $this->lines);

        $this->lines = 0;
    }
}
