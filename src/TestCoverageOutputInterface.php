<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageOutputInterface
{
    /**
     * Transforms an input array to an output string (XML, HTML, console output, etc.).
     */
    public function generate(array $input, string $rootDir = ''): string;
}
