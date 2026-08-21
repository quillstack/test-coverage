<?php

declare(strict_types=1);

namespace Quillstack\TestCoverage\Drivers;

use Quillstack\TestCoverage\TestCoverageDriverInterface;

/**
 * Used when no coverage driver is available, so tests still run without one.
 */
class NoCoverage implements TestCoverageDriverInterface
{
    /**
     * {@inheritDoc}
     */
    public static function isAvailable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function start(): void
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function end(): void
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function process(string $dir = __DIR__): array
    {
        return [];
    }
}
