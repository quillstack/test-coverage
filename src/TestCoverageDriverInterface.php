<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageDriverInterface
{
    /**
     * Tells whether this driver can be used in the current runtime.
     */
    public static function isAvailable(): bool;

    /**
     * Starts debugging for a specific driver to find code coverage.
     */
    public function start(): void;

    /**
     * Ends debugging.
     */
    public function end(): void;

    /**
     * Processes all data to create an output array, mapping every executable line of every
     * file under the given directory to 1 when it was covered and 0 when it was not.
     *
     * @return array<string, array<int, int>>
     */
    public function process(string $dir = __DIR__): array;
}
