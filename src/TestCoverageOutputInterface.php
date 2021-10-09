<?php

declare(strict_types=1);

interface TestCoverageOutputInterface
{
    public function generate(array $input): string;
}
