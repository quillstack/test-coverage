<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageInterface
{
    /**
     * Starts debugging to find code coverage.
     */
    public function start(): void;

    /**
     * Ends debugging.
     */
    public function end(): void;

    /**
     * Processes created array and generate a string depending on a coverage output (XML, HTML, console output, etc.).
     */
    public function process(string $dir = __DIR__, string $rootDir = ''): string;
}
