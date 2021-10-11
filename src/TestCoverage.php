<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

class TestCoverage implements TestCoverageInterface
{
    public function __construct(
        private TestCoverageDriverInterface $driver,
        private TestCoverageOutputInterface $output
    ) {
        //
    }

    public function start(): void
    {
        $this->driver->start();
    }

    public function end(): void
    {
        $this->driver->end();
    }

    public function process(): string
    {
        $output = $this->driver->process();

        return $this->output->generate($output);
    }
}
