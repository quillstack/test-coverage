<?php

declare(strict_types=1);

/**
 * One full coverage cycle, in a process of its own.
 *
 * phpdbg collects into one log at a time, so starting a second one inside the suite would
 * take the suite's own measurement away from it. Running it out here keeps both honest.
 */
require __DIR__ . '/../../vendor/autoload.php';

use Quillstack\TestCoverage\CoverageOutput\CoverageXml;
use Quillstack\TestCoverage\Drivers\PHPDbg;
use Quillstack\TestCoverage\TestCoverage;
use Quillstack\TestCoverage\Tests\Fixtures\Covered;

$fixtures = realpath(__DIR__ . '/../Fixtures');
$coverage = new TestCoverage(new PHPDbg(), new CoverageXml());

$coverage->start();
(new Covered())->run();
$coverage->end();

$xml = $coverage->process($fixtures, $fixtures);

echo json_encode([
    'available' => $coverage->isAvailable(),
    'summary' => $coverage->getSummary(),
    'xml' => $xml,
]);
