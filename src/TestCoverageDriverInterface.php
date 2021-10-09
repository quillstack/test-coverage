<?php

declare(strict_types=1);

interface TestCoverageDriverInterface
{
    public function start(): void;
    public function end(): void;
    public function process(): array;
}
