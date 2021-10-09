<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageOutputInterface
{
    public function generate(array $input): string;
}
