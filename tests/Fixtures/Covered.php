<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Fixtures;

/**
 * A file with lines somebody runs.
 */
class Covered
{
    public function run(): int
    {
        $total = 0;

        foreach ([1, 2, 3] as $number) {
            $total += $number;
        }

        return $total;
    }

    /**
     * Nobody calls this, so its lines are executable and uncovered — which is the difference
     * the report has to show.
     */
    public function neverCalled(): string
    {
        $answer = 'nothing ran this';

        return $answer;
    }
}
