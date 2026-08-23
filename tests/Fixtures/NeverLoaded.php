<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Fixtures;

/**
 * Nothing ever loads this file. Without compiling it first, phpdbg knows nothing about it and
 * it drops out of the report entirely — which makes the coverage look better than it is.
 */
class NeverLoaded
{
    public function run(): string
    {
        $answer = 'nobody loaded this';

        return $answer;
    }
}
