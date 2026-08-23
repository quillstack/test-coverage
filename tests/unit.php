<?php

declare(strict_types=1);

use Quillstack\TestCoverage\Drivers\PHPDbg;

$tests = [
    \Quillstack\TestCoverage\Tests\Unit\TestCoverageXml::class,
    \Quillstack\TestCoverage\Tests\Unit\TestDrivers::class,
    \Quillstack\TestCoverage\Tests\Unit\TestSummary::class,
];

// Running the driver out there needs the binary on the path.
if (shell_exec('command -v phpdbg') !== null) {
    $tests[] = \Quillstack\TestCoverage\Tests\Unit\TestPhpdbgDriver::class;
}

// Running it in here needs this process to be phpdbg, which is not the same thing: the
// functions it calls exist only inside it, and the plain PHP job has the binary anyway.
if (PHPDbg::isAvailable()) {
    $tests[] = \Quillstack\TestCoverage\Tests\Unit\TestPhpdbgInProcess::class;
}

return $tests;
