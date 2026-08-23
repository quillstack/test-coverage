<?php

declare(strict_types=1);

$tests = [
    \Quillstack\TestCoverage\Tests\Unit\TestCoverageXml::class,
    \Quillstack\TestCoverage\Tests\Unit\TestDrivers::class,
    \Quillstack\TestCoverage\Tests\Unit\TestSummary::class,
];

// The driver can only be measured where the thing that measures is installed. Without it
// these do not run at all, rather than passing quietly.
if (shell_exec('command -v phpdbg') !== null) {
    $tests[] = \Quillstack\TestCoverage\Tests\Unit\TestPhpdbgDriver::class;
}

return $tests;
