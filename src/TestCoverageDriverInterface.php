<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

interface TestCoverageDriverInterface
{
    public function start(): void;
    public function end(): void;
    public function process(): array;
}
