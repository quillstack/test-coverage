<?php

declare(strict_types=1);

interface TestCoverageInterface
{
    public function start(): void;
    public function end(): void;
    public function process(): void;
}
