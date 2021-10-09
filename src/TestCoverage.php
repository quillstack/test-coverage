<?php

declare(strict_types=1);

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

    public function process(): void
    {
        $output = $this->driver->process();
        $this->output->generate($output);
    }
}
