<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Drivers;

use Quillstack\TestCoverage\TestCoverageDriverInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;

class PHPDbg implements TestCoverageDriverInterface
{
    /**
     * @var array<string, array<int, int>>
     */
    private array $data = [];

    /**
     * {@inheritDoc}
     */
    public static function isAvailable(): bool
    {
        return function_exists('phpdbg_start_oplog');
    }

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
        // phpdbg answers with null when there was nothing to collect.
        $this->data = \phpdbg_end_oplog() ?? [];
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, array<int, int>>
     */
    public function process(string $dir = __DIR__): array
    {
        $this->compileFilesNeverLoaded($dir);
        $results = $this->createResultsArray($dir);

        return $this->createOutputArray($dir, $results);
    }

    /**
     * A file which no test ever loaded is never compiled, so phpdbg knows nothing about it
     * and it would silently drop out of the report, making the coverage look better than it
     * is. Compiling those files first puts their lines back into the total, uncovered.
     */
    private function compileFilesNeverLoaded(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $included = array_flip(get_included_files());
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            $path = $file instanceof SplFileInfo ? $file->getRealPath() : false;

            if (!$path || !str_ends_with($path, '.php') || isset($included[$path])) {
                continue;
            }

            try {
                require_once $path;
            } catch (Throwable) {
                // A file which cannot be loaded on its own is left out of the report.
            }
        }
    }

    /**
     * Creates data based on results from phpdbg_end_oplog, contains only covered lines.
     *
     * @return array<string, array<int, int>>
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
     *
     * @param array<string, array<int, int>> $results
     *
     * @return array<string, array<int, int>>
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
                $output[$file][$line] = $results[$file][$line] ?? 0;
            }
        }

        return $output;
    }
}
