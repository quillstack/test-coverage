<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Tests\Mocks;

use Quillstack\TestCoverage\TestCoverageOutputInterface;

class FakeOutput implements TestCoverageOutputInterface
{
    /**
     * @var array<string, array<int, int>>
     */
    public array $received = [];

    public string $rootDir = '';

    /**
     * {@inheritDoc}
     */
    public function generate(array $input, string $rootDir = ''): string
    {
        $this->received = $input;
        $this->rootDir = $rootDir;

        return 'generated';
    }
}
