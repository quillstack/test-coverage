<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Drivers;

use Quillstack\TestCoverage\TestCoverageDriverInterface;

class PHPDbg implements TestCoverageDriverInterface
{
    private array $data = [];

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        \phpdbg_start_oplog();
    }

    /**
     * {@inheritDoc}
     */
    public function end(): void
    {
        $this->data = \phpdbg_end_oplog();
    }

    /**
     * {@inheritDoc}
     */
    public function process(string $dir = __DIR__): array
    {
        $results = $this->createResultsArray($dir);

        return $this->createOutputArray($dir, $results);
    }

    /**
     * Creates data based on results from phpdbg_end_oplog, contains only covered lines.
     */
    private function createResultsArray(string $dir): array
    {
        $results = [];

        foreach ($this->data as $file => $coverage) {
            if (!str_starts_with($file, $dir)) {
                continue;
            }

            foreach ($coverage as $line => $value) {
                $results[$file][$line] = $value <= 0 ? 0 : 1;
            }
        }

        return $results;
    }

    /**
     * Creates output data, contains all lines.
     */
    private function createOutputArray(string $dir, array $results): array
    {
        $output = [];
        $lines = phpdbg_get_executable();

        foreach ($lines as $file => $coverage) {
            if (!str_starts_with($file, $dir)) {
                continue;
            }

            foreach ($coverage as $line => $value) {
                if (isset($results[$file][$line])) {
                    $output[$file][$line] = $results[$file][$line];
                } else {
                    $output[$file][$line] = $value;
                }
            }
        }

        return $output;
    }
}
