<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageDriverInterface
{
    /**
     * Starts debugging for a specific driver to find code coverage.
     */
    public function start(): void;

    /**
     * Ends debugging.
     */
    public function end(): void;

    /**
     * Processes all data to create an output array.
     */
    public function process(): array;
}
