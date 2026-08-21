<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageOutputInterface
{
    /**
     * Transforms an input array to an output string (XML, HTML, console output, etc.).
     *
     * @param array<string, array<int, int>> $input every executable line of every file,
     *                                              1 when it was covered and 0 when not
     */
    public function generate(array $input, string $rootDir = ''): string;
}
