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

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        $this->driver->start();
    }

    /**
     * {@inheritDoc}
     */
    public function end(): void
    {
        $this->driver->end();
    }

    /**
     * {@inheritDoc}
     */
    public function process(string $dir = __DIR__): string
    {
        $output = $this->driver->process($dir);

        return $this->output->generate($output);
    }
}
