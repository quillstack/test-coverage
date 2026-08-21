<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage;

class TestCoverage implements TestCoverageInterface
{
    private array $lastOutput = [];

    public function __construct(
        private TestCoverageDriverInterface $driver,
        private TestCoverageOutputInterface $output
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable(): bool
    {
        return !$this->driver instanceof Drivers\NoCoverage;
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
    public function process(string $dir = __DIR__, string $rootDir = ''): string
    {
        $this->lastOutput = $this->driver->process($dir);

        return $this->output->generate($this->lastOutput, $rootDir);
    }

    /**
     * {@inheritDoc}
     */
    public function getSummary(): array
    {
        $covered = $total = 0;

        foreach ($this->lastOutput as $lines) {
            $total += count($lines);
            $covered += count(array_filter($lines));
        }

        return [
            'covered' => $covered,
            'total' => $total,
            'percent' => $total > 0 ? round(100 * $covered / $total, 1) : 0.0,
            'files' => count($this->lastOutput),
        ];
    }
}
