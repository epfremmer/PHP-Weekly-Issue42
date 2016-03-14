#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * File index.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */

use PHPWeekly\Issue42\Entity\Grid;
use PHPWeekly\Issue42\GridRenderer;
use PHPWeekly\Issue42\GridUpdater;

require_once './vendor/autoload.php';

define('HEADLESS_ENV', 'HEADLESS');
define('HEADLESS_FLAG', 'true');

if ($argc < 3 || array_search('--help', $argv) !== false) {
    echo <<<END
Usage:
  php index.php [options] [--] <width> <height>

Arguments:
  width  Board width
  height Board height

Options:
  --help Display this help message

END;

    die(1);
}

$width = (int) $argv[1];
$height = (int) $argv[2];

$grid = new Grid($width, $height);

$gridUpdater = new GridUpdater($grid);
$gridRenderer = new GridRenderer($grid);

$running = true;
$start = microtime(true);
$count = 0;
$max = isset($argv[3]) ? (int) $argv[3] : INF;

while ($running) {
    $gridUpdater->update();
    $gridRenderer->render();
    $gridUpdater->resolve();

    $count++;

    if ($count >= $max) {
        $running = false;
    }
}

echo 'fps: ' . $count / ($time = microtime(true) - $start) . PHP_EOL;
echo 'time: ' . $time . PHP_EOL;
